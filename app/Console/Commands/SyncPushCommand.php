<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPushCommand extends Command
{
    protected $signature = 'zbiz:sync-push
                            {--tenant= : ID do Tenant específico para sincronizar}
                            {--limit=50 : Quantidade máxima de registos por lote}
                            {--dry-run : Apenas simula o envio sem contactar o servidor ou alterar registos}';

    protected $description = 'Sincroniza vendas e movimentações de stock locais para o servidor central na nuvem de forma assíncrona e idempotente';

    public function handle(): int
    {
        $this->info("==============================================================================");
        $this->info("   ZBIZ+ — SINCRONIZAÇÃO ASSÍNCRONA LOCAL -> NUVEM");
        $this->info("==============================================================================");

        $cloudUrl = config('services.sync.cloud_url', 'https://cloud.zbizplus.com/api/sync/ingest');
        $syncToken = config('services.sync.token', env('SYNC_TOKEN', 'zbiz_sync_default_token'));
        $limit = (int)$this->option('limit');
        $dryRun = (bool)$this->option('dry-run');

        // Determinar Tenants a sincronizar
        $tenantIdOption = $this->option('tenant');
        if ($tenantIdOption) {
            $tenants = Tenant::withoutGlobalScopes()->where('id', $tenantIdOption)->get();
        } else {
            $tenants = Tenant::withoutGlobalScopes()->where('status', 'active')->get();
            $tenants = Tenant::withoutGlobalScopes()->whereIn('status', ['active', 'trial'])->get();
        }

        if ($tenants->isEmpty()) {
            $this->warn("Nenhum tenant ativo encontrado para sincronização.");
            return Command::SUCCESS;
        }

        $totalSyncedSales = 0;
        $totalSyncedMovements = 0;

        foreach ($tenants as $tenant) {
            $this->line("A verificar registos pendentes para: <comment>{$tenant->name}</comment> (ID: {$tenant->id})...");

            // 1. Buscar Vendas Pendentes
            $pendingSales = Sale::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereNull('synced_at')
                ->with(['items.product', 'branch', 'user'])
                ->limit($limit)
                ->get();

            // 2. Buscar Movimentos de Stock Pendentes
            $pendingMovements = StockMovement::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereNull('synced_at')
                ->with(['product', 'branch', 'user'])
                ->limit($limit)
                ->get();

            if ($pendingSales->isEmpty() && $pendingMovements->isEmpty()) {
                $this->line("   -> Sem registos comerciais pendentes. Enviando pacote de verificação (heartbeat)...");
            } else {
                $this->line("   -> Pendentes: <info>{$pendingSales->count()} vendas</info>, <info>{$pendingMovements->count()} movimentos de stock</info>.");
            }

            // 3. Montar Carga Útil Idempotente
            $salesPayload = [];
            foreach ($pendingSales as $sale) {
                $offlineId = $sale->offline_id ?: ('OFF-' . $sale->id . '-' . $sale->created_at->timestamp);
                
                // Garantir que a venda local tenha offline_id persistido
                if (!$sale->offline_id) {
                    $sale->updateQuietly(['offline_id' => $offlineId]);
                }

                $salesPayload[] = [
                    'offline_id'           => $offlineId,
                    'branch_id'            => $sale->branch_id,
                    'branch_name'          => $sale->branch?->name,
                    'user_id'              => $sale->user_id,
                    'user_name'            => $sale->user?->name,
                    'customer_id'          => $sale->customer_id,
                    'customer_name'        => $sale->customer_name,
                    'customer_phone'       => $sale->customer_phone,
                    'customer_nuit'        => $sale->customer_nuit,
                    'customer_address'     => $sale->customer_address,
                    'subtotal'             => (float)$sale->subtotal,
                    'discount_amount'      => (float)$sale->discount_amount,
                    'discount_percentage'  => (float)($sale->discount_percentage ?? 0),
                    'discount_type'        => $sale->discount_type,
                    'discount_reason'      => $sale->discount_reason,
                    'total_amount'         => (float)$sale->total_amount,
                    'amount_paid'          => (float)$sale->amount_paid,
                    'change_amount'        => (float)$sale->change_amount,
                    'tax_regime'           => $sale->tax_regime,
                    'tax_rate'             => (float)$sale->tax_rate,
                    'tax_amount'           => (float)$sale->tax_amount,
                    'tax_exemption_reason' => $sale->tax_exemption_reason,
                    'prices_include_tax'   => (bool)$sale->prices_include_tax,
                    'invoice_type'         => $sale->invoice_type,
                    'invoice_number'       => $sale->invoice_number,
                    'payment_method'       => $sale->payment_method,
                    'notes'                => $sale->notes,
                    'sale_date'            => $sale->sale_date ? $sale->sale_date->toDateTimeString() : $sale->created_at->toDateTimeString(),
                    'items'                => $sale->items->map(fn($item) => [
                        'product_id'          => $item->product_id,
                        'product_name'        => $item->product?->name,
                        'product_barcode'     => $item->product?->barcode,
                        'product_sku'         => $item->product?->sku,
                        'quantity'            => $item->quantity,
                        'original_unit_price' => (float)($item->original_unit_price ?? $item->unit_price),
                        'unit_price'          => (float)$item->unit_price,
                        'total_price'         => (float)($item->total_price ?? ($item->quantity * $item->unit_price)),
                        'discount_amount'     => (float)($item->discount_amount ?? 0),
                        'discount_percentage' => (float)($item->discount_percentage ?? 0),
                        'discount_type'       => $item->discount_type,
                        'discount_reason'     => $item->discount_reason,
                    ])->toArray(),
                ];
            }

            $movementsPayload = [];
            foreach ($pendingMovements as $mov) {
                $offlineId = $mov->offline_id ?: ('SM-' . $mov->id . '-' . $mov->created_at->timestamp);

                if (!$mov->offline_id) {
                    $mov->updateQuietly(['offline_id' => $offlineId]);
                }

                $movementsPayload[] = [
                    'offline_id'      => $offlineId,
                    'branch_id'       => $mov->branch_id,
                    'branch_name'     => $mov->branch?->name,
                    'product_id'      => $mov->product_id,
                    'product_name'    => $mov->product?->name,
                    'product_barcode' => $mov->product?->barcode,
                    'product_sku'     => $mov->product?->sku,
                    'user_id'         => $mov->user_id,
                    'user_name'       => $mov->user?->name,
                    'movement_type'   => $mov->movement_type,
                    'quantity'        => (int)$mov->quantity,
                    'reason'          => $mov->reason,
                    'reference_id'    => $mov->reference_id,
                    'movement_date'   => $mov->movement_date ? $mov->movement_date->toDateString() : $mov->created_at->toDateString(),
                ];
            }

            $requestData = [
                'tenant_id'       => $tenant->id,
                'tenant_slug'     => $tenant->slug,
                'source_instance' => gethostname() ?: 'local-machine',
                'sales'           => $salesPayload,
                'stock_movements' => $movementsPayload,
            ];

            // Permitir override por definições do próprio Tenant
            $tenantCloudUrl = \App\Models\Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_url')->value('value');
            $tenantSyncToken = \App\Models\Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_token')->value('value');
            $effectiveCloudUrl = (!empty($tenantCloudUrl) && filter_var($tenantCloudUrl, FILTER_VALIDATE_URL)) ? $tenantCloudUrl : $cloudUrl;
            $effectiveSyncToken = !empty($tenantSyncToken) ? $tenantSyncToken : $syncToken;

            if ($dryRun) {
                $this->warn("   [SIMULAÇÃO / DRY-RUN] Lote preparado com sucesso. Nada foi enviado.");
                $this->warn("   [SIMULAÇÃO / DRY-RUN] Lote preparado com sucesso para {$effectiveCloudUrl}. Nada foi enviado.");
                continue;
            }

            // 4. Envio HTTP para o Servidor Central
            $this->line("   A enviar pacote para a nuvem: <comment>{$effectiveCloudUrl}</comment>...");

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'X-Sync-Token' => $effectiveSyncToken,
                        'Accept'       => 'application/json',
                    ])
                    ->post($effectiveCloudUrl, $requestData);

                if ($response->successful()) {
                    $resJson = $response->json();
                    $syncedAt = now();

                    // Marcar vendas locais como sincronizadas
                    $syncedSaleIds = $pendingSales->pluck('id');
                    Sale::withoutGlobalScopes()
                        ->whereIn('id', $syncedSaleIds)
                        ->update(['synced_at' => $syncedAt]);

                    // Marcar movimentos locais como sincronizados
                    $syncedMovIds = $pendingMovements->pluck('id');
                    StockMovement::withoutGlobalScopes()
                        ->whereIn('id', $syncedMovIds)
                        ->update(['synced_at' => $syncedAt]);

                    $totalSyncedSales += $pendingSales->count();
                    $totalSyncedMovements += $pendingMovements->count();

                    $this->info("   ✓ Sincronização concluída com sucesso!");
                    $this->line("     - Vendas sincronizadas: <info>{$pendingSales->count()}</info>");
                    $this->line("     - Movimentos sincronizados: <info>{$pendingMovements->count()}</info>");

                    // Processar metadados e atualizações remotas de licença da nuvem
                    $licenseMeta = $resJson['license_meta'] ?? null;
                    if ($licenseMeta) {
                        $remoteLicStatus = $licenseMeta['remote_license_status'] ?? null;
                        $remoteLicExpires = $licenseMeta['remote_license_expires_at'] ?? null;
                        $remoteTenantStatus = $licenseMeta['remote_tenant_status'] ?? null;

                        if ($remoteLicStatus === 'revoked' || $remoteTenantStatus === 'suspended') {
                            $tenant->updateQuietly([
                                'license_status' => $remoteLicStatus ?? 'revoked',
                                'status'         => $remoteTenantStatus ?? $tenant->status,
                            ]);
                            \App\Models\LicenseKey::withoutGlobalScopes()
                                ->where('tenant_id', $tenant->id)
                                ->where('status', 'active')
                                ->update(['status' => 'revoked', 'revoked_at' => now()]);

                            $this->warn("     ! Alerta: Licença foi revogada/suspensa remotamente pelo servidor central.");
                        } elseif ($remoteLicExpires) {
                            $newExpiry = \Carbon\Carbon::parse($remoteLicExpires);
                            if (!$tenant->license_expires_at || $newExpiry->greaterThan($tenant->license_expires_at)) {
                                $tenant->updateQuietly([
                                    'license_expires_at'   => $newExpiry,
                                    'subscription_ends_at' => $newExpiry,
                                    'license_status'       => 'active',
                                ]);
                                $this->info("     ✓ Licença renovada pela nuvem até: {$newExpiry->format('d/m/Y')}.");
                            }
                        }
                    }

                    \Illuminate\Support\Facades\Cache::put('tenant_' . $tenant->id . '_last_sync_push', now()->format('d/m/Y H:i:s'), now()->addDays(30));

                    Log::info("[SyncPush] Lote sincronizado com sucesso.", [
                        'tenant_id' => $tenant->id,
                        'sales'     => $pendingSales->count(),
                        'movements' => $pendingMovements->count(),
                    ]);
                } else {
                    $this->error("   ✗ O servidor central rejeitou o lote (HTTP {$response->status()}).");
                    $this->line("     Resposta: " . $response->body());

                    Log::warning("[SyncPush] Servidor rejeitou sincronização.", [
                        'tenant_id' => $tenant->id,
                        'status'    => $response->status(),
                        'body'      => $response->body(),
                    ]);
                }
            } catch (\Exception $e) {
                $this->error("   ✗ Falha de conectividade ao comunicar com a nuvem: " . $e->getMessage());
                Log::warning("[SyncPush] Falha ao sincronizar com a nuvem: " . $e->getMessage(), [
                    'tenant_id' => $tenant->id,
                ]);
            }
        }

        $this->info("==============================================================================");
        $this->info("   FIM DO PROCESSO: {$totalSyncedSales} vendas e {$totalSyncedMovements} movimentos sincronizados.");
        $this->info("==============================================================================");

        return Command::SUCCESS;
    }
}
