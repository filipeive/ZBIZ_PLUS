<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncIngestController extends Controller
{
    /**
     * Endpoint central para ingestão de dados sincronizados da máquina local para a nuvem.
     * Estritamente idempotente via offline_id.
     */
    public function ingest(Request $request): JsonResponse
    {
        // 1. Autenticação por Token de Sincronização
        $providedToken = $request->header('X-Sync-Token')
            ?? $request->bearerToken()
            ?? $request->input('sync_token');

        $expectedToken = config('services.sync.token', env('SYNC_TOKEN', 'zbiz_sync_default_token'));

        if (!$providedToken || !hash_equals((string)$expectedToken, (string)$providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Token de sincronização inválido ou ausente.',
            ], 401);
        }

        // 2. Validação da Carga Útil
        $request->validate([
            'tenant_id'          => 'required|integer',
            'source_instance'    => 'nullable|string|max:100',
            'sales'              => 'nullable|array',
            'stock_movements'    => 'nullable|array',
        ]);

        $tenantId = (int)$request->tenant_id;
        $tenant = Tenant::withoutGlobalScopes()->find($tenantId);

        if (!$tenant || !in_array($tenant->status, ['active', 'trial'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant de destino não encontrado ou inativo na nuvem.',
            ], 422);
        }

        $salesResults = [];
        $createdSalesCount = 0;
        $skippedSalesCount = 0;

        $movementsResults = [];
        $createdMovementsCount = 0;
        $skippedMovementsCount = 0;

        // 3. Processamento Idempotente de Vendas
        $incomingSales = $request->input('sales', []);
        foreach ($incomingSales as $sData) {
            $offlineId = $sData['offline_id'] ?? null;

            // Se a venda com este offline_id já existe neste tenant, ignora para não duplicar
            if ($offlineId) {
                $existingSale = Sale::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('offline_id', $offlineId)
                    ->first();

                if ($existingSale) {
                    $skippedSalesCount++;
                    $salesResults[] = [
                        'offline_id' => $offlineId,
                        'status'     => 'already_exists',
                        'sale_id'    => $existingSale->id,
                    ];
                    continue;
                }
            }

            // Criar venda
            try {
                DB::transaction(function () use (&$salesResults, &$createdSalesCount, $tenantId, $sData, $offlineId) {
                    $sale = Sale::create([
                        'tenant_id'          => $tenantId,
                        'branch_id'          => $sData['branch_id'] ?? null,
                        'cash_shift_id'      => $sData['cash_shift_id'] ?? null,
                        'user_id'            => $sData['user_id'] ?? null,
                        'customer_id'        => $sData['customer_id'] ?? null,
                        'customer_name'      => $sData['customer_name'] ?? 'Cliente Avulso',
                        'customer_phone'     => $sData['customer_phone'] ?? null,
                        'customer_nuit'      => $sData['customer_nuit'] ?? null,
                        'subtotal'           => (float)($sData['subtotal'] ?? $sData['total_amount'] ?? 0),
                        'discount_amount'    => (float)($sData['discount_amount'] ?? 0),
                        'total_amount'       => (float)($sData['total_amount'] ?? 0),
                        'amount_paid'        => (float)($sData['amount_paid'] ?? $sData['total_amount'] ?? 0),
                        'change_amount'      => (float)($sData['change_amount'] ?? 0),
                        'tax_regime'         => $sData['tax_regime'] ?? 'exempt',
                        'tax_rate'           => (float)($sData['tax_rate'] ?? 0),
                        'tax_amount'         => (float)($sData['tax_amount'] ?? 0),
                        'payment_method'     => $sData['payment_method'] ?? 'cash',
                        'sale_date'          => $sData['sale_date'] ?? now(),
                        'offline_id'         => $offlineId,
                        'synced_at'          => now(),
                    ]);

                    // Itens da venda
                    $items = $sData['items'] ?? [];
                    foreach ($items as $item) {
                        SaleItem::create([
                            'tenant_id'   => $tenantId,
                            'sale_id'     => $sale->id,
                            'product_id'  => $item['product_id'] ?? null,
                            'quantity'    => $item['quantity'] ?? 1,
                            'unit_price'  => $item['unit_price'] ?? 0,
                            'subtotal'    => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                            'total'       => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                        ]);
                    }

                    $createdSalesCount++;
                    $salesResults[] = [
                        'offline_id' => $offlineId,
                        'status'     => 'created',
                        'sale_id'    => $sale->id,
                    ];
                });
            } catch (\Exception $e) {
                Log::error("[SyncIngest] Erro ao ingerir venda: " . $e->getMessage(), [
                    'tenant_id'  => $tenantId,
                    'offline_id' => $offlineId,
                ]);

                $salesResults[] = [
                    'offline_id' => $offlineId,
                    'status'     => 'error',
                    'error'      => $e->getMessage(),
                ];
            }
        }

        // 4. Processamento Idempotente de Movimentos de Stock
        $incomingMovements = $request->input('stock_movements', []);
        foreach ($incomingMovements as $smData) {
            $offlineId = $smData['offline_id'] ?? null;

            if ($offlineId) {
                $existingSm = StockMovement::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('offline_id', $offlineId)
                    ->first();

                if ($existingSm) {
                    $skippedMovementsCount++;
                    $movementsResults[] = [
                        'offline_id' => $offlineId,
                        'status'     => 'already_exists',
                        'id'         => $existingSm->id,
                    ];
                    continue;
                }
            }

            try {
                $sm = StockMovement::create([
                    'tenant_id'     => $tenantId,
                    'branch_id'     => $smData['branch_id'] ?? null,
                    'product_id'    => $smData['product_id'] ?? null,
                    'user_id'       => $smData['user_id'] ?? null,
                    'movement_type' => $smData['movement_type'] ?? 'adjustment',
                    'quantity'      => (int)($smData['quantity'] ?? 0),
                    'reason'        => $smData['reason'] ?? 'Sincronização da máquina local',
                    'reference_id'  => $smData['reference_id'] ?? $offlineId,
                    'movement_date' => $smData['movement_date'] ?? now()->toDateString(),
                    'offline_id'    => $offlineId,
                    'synced_at'     => now(),
                ]);

                $createdMovementsCount++;
                $movementsResults[] = [
                    'offline_id' => $offlineId,
                    'status'     => 'created',
                    'id'         => $sm->id,
                ];
            } catch (\Exception $e) {
                Log::error("[SyncIngest] Erro ao ingerir movimento de stock: " . $e->getMessage(), [
                    'tenant_id'  => $tenantId,
                    'offline_id' => $offlineId,
                ]);

                $movementsResults[] = [
                    'offline_id' => $offlineId,
                    'status'     => 'error',
                    'error'      => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Lote de sincronização processado com sucesso.',
            'timestamp' => now()->toIso8601String(),
            'summary'   => [
                'sales' => [
                    'received' => count($incomingSales),
                    'created'  => $createdSalesCount,
                    'skipped'  => $skippedSalesCount,
                ],
                'stock_movements' => [
                    'received' => count($incomingMovements),
                    'created'  => $createdMovementsCount,
                    'skipped'  => $skippedMovementsCount,
                ],
            ],
            'sales_results'     => $salesResults,
            'movements_results' => $movementsResults,
        ]);
    }
}
