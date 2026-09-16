<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncIngestController extends Controller
{
    /**
     * Valida uma Chave de Licença ZBIZ+ na Nuvem e devolve os metadados do Tenant, Plano e URL de Sync.
     */
    public function verifyLicense(Request $request): JsonResponse
    {
        $licenseKey = trim((string)($request->input('license_key') ?? $request->header('X-License-Key')));

        if (empty($licenseKey)) {
            return response()->json([
                'valid'   => false,
                'message' => 'Por favor forneça a chave de licença.',
            ], 422);
        }

        $license = \App\Models\LicenseKey::withoutGlobalScopes()
            ->with(['tenant', 'plan'])
            ->where(function($q) use ($licenseKey) {
                $q->where('key_code', $licenseKey)
                  ->orWhere('key_hash', hash('sha256', $licenseKey));
            })
            ->first();

        if (!$license) {
            return response()->json([
                'valid'   => false,
                'message' => 'Chave de licença não encontrada no servidor central da nuvem.',
            ], 404);
        }

        if ($license->status === 'revoked') {
            return response()->json([
                'valid'   => false,
                'message' => 'Esta chave de licença foi revogada pelo administrador.',
            ], 403);
        }

        if ($license->isExpired()) {
            return response()->json([
                'valid'   => false,
                'message' => 'Esta chave de licença expirou em ' . ($license->expires_at ? $license->expires_at->format('d/m/Y') : 'data desconhecida') . '.',
            ], 403);
        }

        $tenant = $license->tenant;
        $plan = $license->plan;

        return response()->json([
            'valid'         => true,
            'message'       => 'Licença válida e autorizada no servidor central.',
            'license'       => [
                'id'         => $license->id,
                'key_code'   => $license->key_code,
                'mode'       => $license->mode ?? 'local_online',
                'status'     => $license->status,
                'starts_at'  => $license->starts_at?->toIso8601String(),
                'expires_at' => $license->expires_at?->toIso8601String(),
                'payload'    => $license->payload,
                'signature'  => $license->signature,
            ],
            'tenant'        => $tenant ? [
                'id'            => $tenant->id,
                'slug'          => $tenant->slug,
                'name'          => $tenant->name,
                'email'         => $tenant->email,
                'phone'         => $tenant->phone,
                'nuit'          => $tenant->nuit,
                'business_type' => $tenant->business_type,
            ] : null,
            'plan'          => $plan ? [
                'id'           => $plan->id,
                'slug'         => $plan->slug,
                'name'         => $plan->name,
                'features'     => $plan->features ?? [],
                'max_branches' => $plan->max_branches ?? 1,
                'max_users'    => $plan->max_users ?? 2,
                'max_products' => $plan->max_products ?? 0,
            ] : null,
            'sync_config'   => [
                'ingest_url' => url('/api/sync/ingest'),
                'sync_token' => $license->key_code,
            ],
        ]);
    }
    /**
     * Endpoint central para ingestão de dados sincronizados da máquina local para a nuvem.
     * Estritamente idempotente via offline_id, com mapeamento automático de Tenant (slug), Filiais e Produtos.
     */
    public function ingest(Request $request): JsonResponse
    {
        // 1. Autenticação por Token de Sincronização (Master Token ou Chave de Licença do Tenant)
        $providedToken = $request->header('X-Sync-Token')
            ?? $request->bearerToken()
            ?? $request->input('sync_token');

        $expectedToken = config('services.sync.token', env('SYNC_TOKEN', 'zbiz_sync_default_token'));

        $isMasterToken = $providedToken && hash_equals((string)$expectedToken, (string)$providedToken);
        $licenseTenantId = null;

        if (!$isMasterToken && $providedToken) {
            $license = \App\Models\LicenseKey::withoutGlobalScopes()
                ->where('key_code', trim($providedToken))
                ->whereIn('status', ['active', 'issued'])
                ->first();
            if ($license) {
                $licenseTenantId = $license->tenant_id;
            }
        }

        if (!$isMasterToken && !$licenseTenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Token de sincronização inválido ou ausente. Forneça o Sync Token da infraestrutura ou a Chave de Licença ZBIZ+.',
            ], 401);
        }

        // 2. Validação da Carga Útil
        $request->validate([
            'tenant_id'       => 'nullable|integer',
            'tenant_slug'     => 'nullable|string|max:100',
            'source_instance' => 'nullable|string|max:100',
            'categories'      => 'nullable|array',
            'products'        => 'nullable|array',
            'sales'           => 'nullable|array',
            'stock_movements' => 'nullable|array',
        ]);

        // Resolução inteligente de Tenant por Licença, Slug ou ID
        $tenantSlug = $request->input('tenant_slug');
        $tenantId = (int)$request->input('tenant_id');

        $tenant = null;
        if ($licenseTenantId) {
            $tenant = Tenant::withoutGlobalScopes()->find($licenseTenantId);
        }
        if (!$tenant && $tenantSlug) {
            $tenant = Tenant::withoutGlobalScopes()->where('slug', $tenantSlug)->first();
        }
        if (!$tenant && $tenantId) {
            $tenant = Tenant::withoutGlobalScopes()->find($tenantId);
        }

        if (!$tenant || !in_array($tenant->status, ['active', 'trial'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant de destino não encontrado ou inativo na nuvem.',
            ], 422);
        }

        $resolvedTenantId = $tenant->id;

        // Pré-carregar Filiais e Utilizadores do Tenant na nuvem
        $tenantBranches = Branch::withoutGlobalScopes()->where('tenant_id', $resolvedTenantId)->get();
        $defaultBranch = $tenantBranches->firstWhere('is_main', true) ?? $tenantBranches->first();

        $tenantUsers = User::withoutGlobalScopes()->where('tenant_id', $resolvedTenantId)->get();
        $defaultUser = $tenantUsers->first();

        // 3. Processamento de Categorias (Auto-Upsert)
        $incomingCategories = $request->input('categories', []);
        $createdCategoriesCount = 0;
        foreach ($incomingCategories as $catData) {
            $catName = trim($catData['name'] ?? '');
            if (empty($catName)) continue;

            Category::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $resolvedTenantId, 'name' => $catName],
                [
                    'branch_id'   => $defaultBranch?->id,
                    'description' => $catData['description'] ?? null,
                    'type'        => $catData['type'] ?? 'product',
                    'color'       => $catData['color'] ?? '#0D9488',
                    'icon'        => $catData['icon'] ?? 'box',
                    'is_active'   => (bool)($catData['is_active'] ?? true),
                ]
            );
            $createdCategoriesCount++;
        }

        // 4. Processamento de Produtos (Auto-Upsert)
        $incomingProducts = $request->input('products', []);
        $createdProductsCount = 0;
        foreach ($incomingProducts as $pData) {
            $barcode = !empty($pData['barcode']) ? trim($pData['barcode']) : null;
            $sku = !empty($pData['sku']) ? trim($pData['sku']) : null;
            $name = trim($pData['name'] ?? '');
            if (empty($name) && empty($barcode) && empty($sku)) continue;

            $catId = null;
            if (!empty($pData['category_name'])) {
                $c = Category::withoutGlobalScopes()->firstOrCreate(
                    ['tenant_id' => $resolvedTenantId, 'name' => trim($pData['category_name'])],
                    ['is_active' => true]
                );
                $catId = $c->id;
            }

            $query = Product::withoutGlobalScopes()->where('tenant_id', $resolvedTenantId);
            if ($barcode) {
                $query->where('barcode', $barcode);
            } elseif ($sku) {
                $query->where('sku', $sku);
            } else {
                $query->where('name', $name);
            }

            $product = $query->first();

            $pAttributes = [
                'tenant_id'      => $resolvedTenantId,
                'category_id'    => $catId ?? ($product?->category_id),
                'name'           => $name ?: ($product?->name ?? 'Artigo Sincronizado'),
                'barcode'        => $barcode ?: ($product?->barcode),
                'sku'            => $sku ?: ($product?->sku ?? ('SYNC-' . strtoupper(Str::random(8)))),
                'type'           => $pData['type'] ?? 'product',
                'purchase_price' => (float)($pData['purchase_price'] ?? $pData['cost_price'] ?? $product?->purchase_price ?? 0),
                'selling_price'  => (float)($pData['selling_price'] ?? $product?->selling_price ?? 0),
                'promotional_price' => isset($pData['promotional_price']) ? (float)$pData['promotional_price'] : ($product?->promotional_price),
                'stock_quantity' => isset($pData['stock_quantity']) ? (int)$pData['stock_quantity'] : ($product?->stock_quantity ?? 0),
                'min_stock_level'=> (int)($pData['min_stock_level'] ?? $pData['min_stock_alert'] ?? $product?->min_stock_level ?? 5),
                'unit'           => $pData['unit'] ?? ($product?->unit ?? 'unidade'),
                'is_active'      => (bool)($pData['is_active'] ?? true),
                'is_tax_exempt'  => (bool)($pData['is_tax_exempt'] ?? false),
                'tax_rate'       => (float)($pData['tax_rate'] ?? 16),
            ];

            if ($product) {
                $product->update($pAttributes);
            } else {
                Product::create($pAttributes);
            }
            $createdProductsCount++;
        }

        // Helpers de mapeamento resiliente
        $resolveBranchId = function(?string $name, ?int $id) use ($tenantBranches, $defaultBranch) {
            if ($name) {
                $cleanName = mb_strtolower(trim($name));
                foreach ($tenantBranches as $b) {
                    $bName = mb_strtolower(trim($b->name));
                    if (str_contains($bName, $cleanName) || str_contains($cleanName, $bName)) {
                        return $b->id;
                    }
                }
            }
            if ($id && $tenantBranches->contains('id', $id)) {
                return $id;
            }
            return $defaultBranch?->id;
        };

        $resolveUserId = function(?string $name, ?int $id) use ($tenantUsers, $defaultUser) {
            if ($id && $tenantUsers->contains('id', $id)) {
                return $id;
            }
            if ($name) {
                $matched = $tenantUsers->first(fn($u) => mb_strtolower(trim($u->name)) === mb_strtolower(trim($name)));
                if ($matched) return $matched->id;
            }
            return $defaultUser?->id;
        };

        $resolveProduct = function(array $itemData) use ($resolvedTenantId) {
            $barcode = !empty($itemData['product_barcode']) ? trim($itemData['product_barcode']) : null;
            $sku     = !empty($itemData['product_sku']) ? trim($itemData['product_sku']) : null;
            $name    = !empty($itemData['product_name']) ? trim($itemData['product_name']) : null;
            $prodId  = !empty($itemData['product_id']) ? (int)$itemData['product_id'] : null;

            if ($barcode) {
                $p = Product::withoutGlobalScopes()
                    ->where('tenant_id', $resolvedTenantId)
                    ->where('barcode', $barcode)
                    ->first();
                if ($p) return $p;
            }

            if ($sku) {
                $p = Product::withoutGlobalScopes()
                    ->where('tenant_id', $resolvedTenantId)
                    ->where('sku', $sku)
                    ->first();
                if ($p) return $p;
            }

            if ($name) {
                $p = Product::withoutGlobalScopes()
                    ->where('tenant_id', $resolvedTenantId)
                    ->where('name', $name)
                    ->first();
                if ($p) return $p;
            }

            if ($prodId) {
                $p = Product::withoutGlobalScopes()
                    ->where('tenant_id', $resolvedTenantId)
                    ->where('id', $prodId)
                    ->first();
                if ($p) return $p;
            }

            // Se o produto ainda não existe na nuvem, cria automaticamente para preservar histórico e evitar erros de FK
            $effectiveName = $name ?: ('Produto Sincronizado ' . ($sku ?: '#' . ($prodId ?: uniqid())));
            $unitPrice = (float)($itemData['unit_price'] ?? 0);

            return Product::create([
                'tenant_id'      => $resolvedTenantId,
                'name'           => $effectiveName,
                'barcode'        => $barcode,
                'sku'            => $sku ?: ('SYNC-' . strtoupper(Str::random(8))),
                'type'           => 'product',
                'purchase_price' => 0,
                'selling_price'  => $unitPrice,
                'stock_quantity' => 0,
                'min_stock_level'=> 5,
                'unit'           => 'unidade',
                'is_active'      => true,
            ]);
        };

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
                    ->where('tenant_id', $resolvedTenantId)
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

            $branchId = $resolveBranchId($sData['branch_name'] ?? null, $sData['branch_id'] ?? null);
            $userId   = $resolveUserId($sData['user_name'] ?? null, $sData['user_id'] ?? null);

            // Criar venda
            try {
                DB::transaction(function () use (
                    &$salesResults,
                    &$createdSalesCount,
                    $resolvedTenantId,
                    $branchId,
                    $userId,
                    $sData,
                    $offlineId,
                    $resolveProduct
                ) {
                    $sale = Sale::create([
                        'tenant_id'            => $resolvedTenantId,
                        'branch_id'            => $branchId,
                        'cash_shift_id'        => null,
                        'user_id'              => $userId,
                        'customer_id'          => null,
                        'customer_name'        => $sData['customer_name'] ?? 'Cliente Avulso',
                        'customer_phone'       => $sData['customer_phone'] ?? null,
                        'customer_nuit'        => $sData['customer_nuit'] ?? null,
                        'customer_address'     => $sData['customer_address'] ?? null,
                        'subtotal'             => (float)($sData['subtotal'] ?? $sData['total_amount'] ?? 0),
                        'discount_amount'      => (float)($sData['discount_amount'] ?? 0),
                        'discount_percentage'  => (float)($sData['discount_percentage'] ?? 0),
                        'discount_type'        => $sData['discount_type'] ?? null,
                        'discount_reason'      => $sData['discount_reason'] ?? null,
                        'total_amount'         => (float)($sData['total_amount'] ?? 0),
                        'amount_paid'          => (float)($sData['amount_paid'] ?? $sData['total_amount'] ?? 0),
                        'change_amount'        => (float)($sData['change_amount'] ?? 0),
                        'tax_regime'           => $sData['tax_regime'] ?? 'exempt',
                        'tax_rate'             => (float)($sData['tax_rate'] ?? 0),
                        'tax_amount'           => (float)($sData['tax_amount'] ?? 0),
                        'tax_exemption_reason' => $sData['tax_exemption_reason'] ?? null,
                        'prices_include_tax'   => (bool)($sData['prices_include_tax'] ?? true),
                        'invoice_type'         => $sData['invoice_type'] ?? 'cash_invoice',
                        'invoice_number'       => $sData['invoice_number'] ?? null,
                        'payment_method'       => $sData['payment_method'] ?? 'cash',
                        'notes'                => $sData['notes'] ?? null,
                        'sale_date'            => $sData['sale_date'] ?? now(),
                        'offline_id'           => $offlineId,
                        'synced_at'            => now(),
                    ]);

                    // Itens da venda
                    $items = $sData['items'] ?? [];
                    foreach ($items as $item) {
                        $targetProduct = $resolveProduct($item);

                        SaleItem::create([
                            'tenant_id'           => $resolvedTenantId,
                            'branch_id'           => $branchId,
                            'sale_id'             => $sale->id,
                            'product_id'          => $targetProduct->id,
                            'quantity'            => $item['quantity'] ?? 1,
                            'original_unit_price' => (float)($item['original_unit_price'] ?? $item['unit_price'] ?? 0),
                            'unit_price'          => (float)($item['unit_price'] ?? 0),
                            'total_price'         => (float)($item['total_price'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0))),
                            'discount_amount'     => (float)($item['discount_amount'] ?? 0),
                            'discount_percentage' => (float)($item['discount_percentage'] ?? 0),
                            'discount_type'       => $item['discount_type'] ?? null,
                            'discount_reason'     => $item['discount_reason'] ?? null,
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
                    'tenant_id'  => $resolvedTenantId,
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
                    ->where('tenant_id', $resolvedTenantId)
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

            $branchId = $resolveBranchId($smData['branch_name'] ?? null, $smData['branch_id'] ?? null);
            $userId   = $resolveUserId($smData['user_name'] ?? null, $smData['user_id'] ?? null);
            $targetProduct = $resolveProduct($smData);

            try {
                $sm = StockMovement::create([
                    'tenant_id'     => $resolvedTenantId,
                    'branch_id'     => $branchId,
                    'product_id'    => $targetProduct->id,
                    'user_id'       => $userId,
                    'movement_type' => $smData['movement_type'] ?? 'adjustment',
                    'quantity'      => (int)($smData['quantity'] ?? 0),
                    'reason'        => $smData['reason'] ?? 'Sincronização da máquina local',
                    'reference_id'  => $smData['reference_id'] ?? null,
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
                    'tenant_id'  => $resolvedTenantId,
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
                'categories'      => [
                    'received' => count($incomingCategories),
                    'upserted' => $createdCategoriesCount,
                ],
                'products'        => [
                    'received' => count($incomingProducts),
                    'upserted' => $createdProductsCount,
                ],
                'sales'           => [
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
            'license_meta' => [
                'remote_license_status'     => $tenant->license_status ?? 'active',
                'remote_license_expires_at' => $tenant->license_expires_at?->toIso8601String(),
                'remote_tenant_status'      => $tenant->status,
                'server_time'               => now()->toIso8601String(),
            ],
            'sales_results'     => $salesResults,
            'movements_results' => $movementsResults,
        ]);
    }
}
