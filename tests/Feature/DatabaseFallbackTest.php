<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DatabaseFallbackTest extends TestCase
{
    public function test_database_connection_exception_renders_friendly_fallback_view(): void
    {
        Route::get('/test-db-error', function () {
            throw new QueryException(
                'mysql',
                'select * from `sessions` where `id` = ? limit 1',
                [],
                new \PDOException('SQLSTATE[HY000] [2002] No such file or directory', 2002)
            );
        });

        $response = $this->get('/test-db-error');

        $response->assertStatus(503);
        $response->assertSee('Base de Dados a Inicializar');
        $response->assertSee('Tentar Novamente');
        $response->assertSee('Suporte Técnico Moçambique');
    }

    public function test_database_connection_exception_returns_json_for_api_requests(): void
    {
        Route::get('/api/test-db-error', function () {
            throw new \PDOException('SQLSTATE[HY000] [2002] Connection refused', 2002);
        });

        $response = $this->getJson('/api/test-db-error');

        $response->assertStatus(503);
        $response->assertJson([
            'status' => 'database_offline',
            'retry_after' => 10,
        ]);
    }

    public function test_health_endpoint_returns_minimal_public_payload_on_200_and_details_with_monitoring_token(): void
    {
        // 1. Resposta pública mínima: apenas status e timestamp (sem expor version nem checks)
        $publicResponse = $this->getJson('/api/health');

        $publicResponse->assertOk()
            ->assertExactJson([
                'status'    => 'ok',
                'timestamp' => $publicResponse->json('timestamp'),
            ]);
        $this->assertArrayNotHasKey('version', $publicResponse->json());
        $this->assertArrayNotHasKey('checks', $publicResponse->json());

        // 2. Com token de monitorização e driver queue=database -> devolve checks reais
        config([
            'services.health.token' => 'secret_health_monitor_token',
            'queue.default'         => 'database',
        ]);

        // Em DatabaseFallbackTest (sem RefreshDatabase), garantir que a tabela jobs existe na conexão de teste
        if (!\Illuminate\Support\Facades\Schema::hasTable('jobs')) {
            \Illuminate\Support\Facades\Schema::create('jobs', function ($table) {
                $table->id();
                $table->string('queue');
            });
        }

        $detailedResponse = $this->withHeaders([
            'X-Health-Token' => 'secret_health_monitor_token',
        ])->getJson('/api/health');

        $detailedResponse->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database', 'up')
            ->assertJsonPath('checks.storage', 'writable')
            ->assertJsonPath('checks.queue', 'up');
    }

    public function test_health_endpoint_returns_503_when_storage_or_queue_is_degraded(): void
    {
        config([
            'services.health.token'        => 'secret_health_monitor_token',
            'services.health.storage_path' => '/caminho/inexistente/nao/gravavel',
        ]);

        $response = $this->withHeaders([
            'X-Health-Token' => 'secret_health_monitor_token',
        ])->getJson('/api/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('checks.storage', 'readonly');
    }
}

