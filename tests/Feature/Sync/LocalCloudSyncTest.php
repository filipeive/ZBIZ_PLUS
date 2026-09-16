<?php

namespace Tests\Feature\Sync;

use App\Models\Branch;
use App\Models\Category;
use App\Models\LicenseKey;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocalCloudSyncTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $user;
    protected Product $product;
    protected string $syncToken = 'test_secret_sync_token_123';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.sync.token' => $this->syncToken]);
        config(['services.sync.cloud_url' => 'https://mock-cloud.zbizplus.com/api/sync/ingest']);

        $this->seed(PlanSeeder::class);

        $this->tenant = Tenant::create([
            'name'          => 'Farmácia Central Sync',
            'slug'          => 'farmacia-central-sync',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->tenant, Plan::where('slug', 'pro')->firstOrFail());

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Balcão 1',
            'code'      => 'B1',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Operador Farmácia',
            'email'     => 'operador@sync.test',
            'password'  => bcrypt('password'),
            'role_id'   => $role->id,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Medicamentos Gerais',
        ]);

        $this->product = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Paracetamol 500mg',
            'barcode'        => '5601234567890',
            'type'           => 'product',
            'purchase_price' => 20.00,
            'selling_price'  => 50.00,
            'stock_quantity' => 100,
        ]);
    }

    public function test_sync_ingest_rejects_requests_without_valid_token(): void
    {
        $response = $this->postJson('/api/sync/ingest', [
            'tenant_id' => $this->tenant->id,
            'sales'     => [],
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    public function test_sync_ingest_creates_sales_and_stock_movements_successfully(): void
    {
        $payload = [
            'tenant_id'          => $this->tenant->id,
            'source_instance'    => 'terminal-caixa-01',
            'sales'              => [
                [
                    'offline_id'      => 'OFF-SALE-001',
                    'customer_name'   => 'João Paciente',
                    'total_amount'    => 100.00,
                    'amount_paid'     => 100.00,
                    'payment_method'  => 'cash',
                    'items'           => [
                        [
                            'product_id' => $this->product->id,
                            'quantity'   => 2,
                            'unit_price' => 50.00,
                        ],
                    ],
                ],
            ],
            'stock_movements'    => [
                [
                    'offline_id'    => 'OFF-MOV-001',
                    'product_id'    => $this->product->id,
                    'movement_type' => 'in',
                    'quantity'      => 50,
                    'reason'        => 'Entrada de mercadoria local',
                ],
            ],
        ];

        $response = $this->postJson('/api/sync/ingest', $payload, [
            'X-Sync-Token' => $this->syncToken,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'summary' => [
                'sales' => [
                    'received' => 1,
                    'created'  => 1,
                    'skipped'  => 0,
                ],
                'stock_movements' => [
                    'received' => 1,
                    'created'  => 1,
                    'skipped'  => 0,
                ],
            ],
        ]);

        // Verificar persistência no banco
        $this->assertDatabaseHas('sales', [
            'tenant_id'  => $this->tenant->id,
            'offline_id' => 'OFF-SALE-001',
            'total_amount' => 100.00,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'tenant_id'  => $this->tenant->id,
            'offline_id' => 'OFF-MOV-001',
            'quantity'   => 50,
        ]);
    }

    public function test_sync_ingest_is_strictly_idempotent(): void
    {
        $payload = [
            'tenant_id'       => $this->tenant->id,
            'source_instance' => 'terminal-caixa-01',
            'sales'           => [
                [
                    'offline_id'     => 'OFF-DUPLICATE-CHECK-001',
                    'customer_name'  => 'Maria Teste',
                    'total_amount'   => 150.00,
                    'amount_paid'    => 150.00,
                    'payment_method' => 'cash',
                    'items'          => [
                        [
                            'product_id' => $this->product->id,
                            'quantity'   => 3,
                            'unit_price' => 50.00,
                        ],
                    ],
                ],
            ],
            'stock_movements' => [
                [
                    'offline_id'    => 'OFF-MOV-DUPLICATE-001',
                    'product_id'    => $this->product->id,
                    'movement_type' => 'out',
                    'quantity'      => 3,
                    'reason'        => 'Saída de teste',
                ],
            ],
        ];

        // 1ª execução: cria
        $resp1 = $this->postJson('/api/sync/ingest', $payload, ['X-Sync-Token' => $this->syncToken]);
        $resp1->assertOk();
        $this->assertEquals(1, $resp1->json('summary.sales.created'));
        $this->assertEquals(0, $resp1->json('summary.sales.skipped'));

        $initialSalesCount = Sale::where('offline_id', 'OFF-DUPLICATE-CHECK-001')->count();
        $this->assertEquals(1, $initialSalesCount);

        // 2ª execução idêntica: DEVE IGNORAR / NÃO DUPLICAR
        $resp2 = $this->postJson('/api/sync/ingest', $payload, ['X-Sync-Token' => $this->syncToken]);
        $resp2->assertOk();
        $this->assertEquals(0, $resp2->json('summary.sales.created'));
        $this->assertEquals(1, $resp2->json('summary.sales.skipped'));
        $this->assertEquals(0, $resp2->json('summary.stock_movements.created'));
        $this->assertEquals(1, $resp2->json('summary.stock_movements.skipped'));

        // Contagem no banco não deve aumentar
        $this->assertEquals(1, Sale::where('offline_id', 'OFF-DUPLICATE-CHECK-001')->count());
        $this->assertEquals(1, StockMovement::where('offline_id', 'OFF-MOV-DUPLICATE-001')->count());
    }

    public function test_artisan_sync_push_command_pushes_pending_records_and_updates_synced_at(): void
    {
        // 1. Criar venda e movimento locais pendentes
        $sale = Sale::create([
            'tenant_id'      => $this->tenant->id,
            'branch_id'      => $this->branch->id,
            'customer_name'  => 'Cliente Balcão Local',
            'total_amount'   => 200.00,
            'amount_paid'    => 200.00,
            'payment_method' => 'cash',
            'offline_id'     => 'OFF-LOCAL-PUSH-100',
            'synced_at'      => null, // Pendente
        ]);

        $mov = StockMovement::create([
            'tenant_id'     => $this->tenant->id,
            'branch_id'     => $this->branch->id,
            'product_id'    => $this->product->id,
            'user_id'       => $this->user->id,
            'movement_type' => 'in',
            'quantity'      => 40,
            'reason'        => 'Entrada local pendente',
            'movement_date' => now()->toDateString(),
            'offline_id'    => 'SM-LOCAL-PUSH-200',
            'synced_at'     => null, // Pendente
        ]);

        // Mock do servidor de nuvem
        Http::fake([
            'https://mock-cloud.zbizplus.com/api/sync/ingest' => Http::response([
                'success' => true,
                'message' => 'Recebido com sucesso.',
            ], 200),
        ]);

        // Executar comando
        $this->artisan('zbiz:sync-push', ['--tenant' => $this->tenant->id])
            ->expectsOutputToContain('SINCRONIZAÇÃO ASSÍNCRONA LOCAL -> NUVEM')
            ->expectsOutputToContain('Sincronização concluída com sucesso!')
            ->assertExitCode(0);

        // Verificar que synced_at foi atualizado
        $this->assertNotNull($sale->fresh()->synced_at);
        $this->assertNotNull($mov->fresh()->synced_at);
    }

    public function test_artisan_sync_push_handles_network_failure_gracefully(): void
    {
        Sale::create([
            'tenant_id'      => $this->tenant->id,
            'branch_id'      => $this->branch->id,
            'customer_name'  => 'Cliente Queda de Rede',
            'total_amount'   => 50.00,
            'payment_method' => 'cash',
            'synced_at'      => null,
        ]);

        // Simular falha de rede / conexão recusada
        Http::fake([
            'https://mock-cloud.zbizplus.com/api/sync/ingest' => function () {
                throw new \Illuminate\Http\Client\ConnectionException("Connection refused");
            },
        ]);

        // Não deve quebrar com exceção fatal
        $this->artisan('zbiz:sync-push', ['--tenant' => $this->tenant->id])
            ->expectsOutputToContain('Falha de conectividade ao comunicar com a nuvem')
            ->assertExitCode(0);
    }

    public function test_admin_can_test_cloud_sync_connection_via_endpoint(): void
    {
        $this->actingAs($this->user);

        Http::fake([
            'https://mock-cloud.zbizplus.com/api/sync/ingest' => Http::response([
                'success' => true,
                'message' => 'Lote de sincronização processado com sucesso.',
                'license_meta' => [
                    'remote_license_status' => 'active',
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('admin.sync.test_connection'), [
            'cloud_url'  => 'https://mock-cloud.zbizplus.com/api/sync/ingest',
            'sync_token' => $this->syncToken,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure(['success', 'message', 'latency_ms', 'cloud_url']);
    }

    public function test_admin_can_update_cloud_sync_settings_and_view_stats(): void
    {
        $this->actingAs($this->user);

        // Criar venda pendente
        Sale::create([
            'tenant_id'      => $this->tenant->id,
            'branch_id'      => $this->branch->id,
            'customer_name'  => 'Cliente Sync UI Test',
            'total_amount'   => 120.00,
            'payment_method' => 'cash',
            'synced_at'      => null,
        ]);

        // Aceder à aba de definições de sincronização
        $viewResponse = $this->get(route('admin.settings', ['tab' => 'sync']));
        $viewResponse->assertOk()
            ->assertViewHas('pendingSyncSales', 1)
            ->assertSee('Sincronização Híbrida de Dados');

        // Atualizar URL e Token via formulário
        $postResponse = $this->post(route('admin.settings.update'), [
            'tab'              => 'sync',
            'company_name'     => $this->tenant->name,
            'business_type'    => 'pharmacy',
            'cloud_sync_url'   => 'https://custom-cloud.zbizplus.com/api/sync/ingest',
            'cloud_sync_token' => 'custom_token_456',
        ]);

        $postResponse->assertRedirect(route('admin.settings', ['tab' => 'sync']));

        $this->assertDatabaseHas('settings', [
            'tenant_id' => $this->tenant->id,
            'key'       => 'cloud_sync_url',
            'value'     => 'https://custom-cloud.zbizplus.com/api/sync/ingest',
        ]);
    }

    public function test_verify_license_endpoint_returns_tenant_and_sync_config(): void
    {
        $licenseKey = 'ZBIZ-TEST-VERI-FY99-0001';
        $license = LicenseKey::create([
            'tenant_id'  => $this->tenant->id,
            'plan_id'    => $this->tenant->plan_id ?? 1,
            'key_code'   => $licenseKey,
            'mode'       => 'local_online',
            'status'     => 'active',
            'starts_at'  => now()->subDay(),
            'expires_at' => now()->addYear(),
        ]);

        $response = $this->postJson(route('sync.verify_license'), [
            'license_key' => $licenseKey,
        ]);

        $response->assertOk()
            ->assertJson([
                'valid'   => true,
                'license' => [
                    'key_code' => $licenseKey,
                    'status'   => 'active',
                ],
                'tenant'  => [
                    'slug' => $this->tenant->slug,
                    'name' => $this->tenant->name,
                ],
            ])
            ->assertJsonStructure([
                'valid', 'license', 'tenant', 'plan', 'sync_config' => ['ingest_url', 'sync_token']
            ]);
    }

    public function test_sync_ingest_upserts_categories_and_products(): void
    {
        $payload = [
            'tenant_slug' => $this->tenant->slug,
            'categories'  => [
                [
                    'name'        => 'Bebidas e Refrescos',
                    'description' => 'Sucos, refrigerantes e águas',
                    'type'        => 'product',
                    'is_active'   => true,
                ]
            ],
            'products'    => [
                [
                    'name'           => 'Suco de Manga 1L',
                    'barcode'        => '6001234567890',
                    'sku'            => 'SUCO-MANGA-1L',
                    'category_name'  => 'Bebidas e Refrescos',
                    'selling_price'  => 85.00,
                    'purchase_price' => 50.00,
                    'stock_quantity' => 20,
                    'unit'           => 'garrafa',
                    'is_active'      => true,
                ]
            ],
            'sales'           => [],
            'stock_movements' => [],
        ];

        $response = $this->withHeaders([
            'X-Sync-Token' => $this->syncToken,
        ])->postJson(route('sync.ingest'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'summary' => [
                    'categories' => ['received' => 1, 'upserted' => 1],
                    'products'   => ['received' => 1, 'upserted' => 1],
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'tenant_id' => $this->tenant->id,
            'name'      => 'Bebidas e Refrescos',
        ]);

        $this->assertDatabaseHas('products', [
            'tenant_id' => $this->tenant->id,
            'barcode'   => '6001234567890',
            'sku'       => 'SUCO-MANGA-1L',
            'name'      => 'Suco de Manga 1L',
        ]);
    }
}
