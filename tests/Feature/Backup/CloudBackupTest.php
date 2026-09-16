<?php

namespace Tests\Feature\Backup;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CloudBackupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Garantir tenant para os testes
        Tenant::create([
            'id'            => 1,
            'name'          => 'Farmácia Modelo Teste',
            'slug'          => 'farmacia-modelo-teste',
            'business_type' => 'pharmacy',
            'status'        => 'active',
            'nuit'          => '999888777',
        ]);
    }

    public function test_cloud_backup_endpoint_rejects_unauthorized_requests(): void
    {
        $response = $this->postJson('/api/sync/backup-upload', [
            'tenant_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_cloud_backup_endpoint_accepts_valid_upload_with_token(): void
    {
        $token = 'test_sync_token_456';
        config(['services.sync.token' => $token]);

        $file = UploadedFile::fake()->create('test_backup.sql', 10, 'application/sql');

        $response = $this->withHeaders([
            'X-Sync-Token' => $token,
        ])->postJson('/api/sync/backup-upload', [
            'tenant_id'   => 1,
            'backup_file' => $file,
            'filename'    => 'test_automated_backup.sql',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'  => true,
            'filename' => 'test_automated_backup.sql',
        ]);

        $savedPath = storage_path('app/cloud_backups/tenant_1/test_automated_backup.sql');
        $this->assertTrue(File::exists($savedPath));

        // Limpeza
        File::delete($savedPath);
    }

    public function test_artisan_pharma_catalog_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('zbiz:seed-pharma-catalog', [
            'tenant_id' => 1,
        ]);

        $this->assertEquals(0, $exitCode);
    }
}
