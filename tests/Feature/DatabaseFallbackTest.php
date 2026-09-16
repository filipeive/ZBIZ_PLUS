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
}

