<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch1;
    protected Branch $branch2;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Restaurante Sabor',
            'slug' => 'restaurante-sabor',
            'business_type' => 'restaurant',
            'status' => 'active',
        ]);

        $this->branch1 = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Filial Matola',
            'code' => 'BR-MT',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->branch2 = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Filial Maputo',
            'code' => 'BR-MP',
            'is_main' => false,
            'is_active' => true,
        ]);

        $role = \App\Models\Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web', 'description' => 'Super Administrador']
        );

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch1->id,
            'role_id' => $role->id,
            'name' => 'Gestor Restaurante',
            'email' => 'gestor@restaurante.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }

    public function test_can_create_category_with_branch_type_color_and_icon(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch1->id,
            ])
            ->post(route('categories.store'), [
                'name' => 'Sumos & Refrescos',
                'description' => 'Sumos naturais e refrigerantes gelados',
                'type' => 'product',
                'color' => '#10b981',
                'icon' => 'fa-wine-glass',
                'is_active' => 1,
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch1->id,
            'name' => 'Sumos & Refrescos',
            'type' => 'product',
            'color' => '#10b981',
            'icon' => 'fa-wine-glass',
            'is_active' => 1,
        ]);
    }

    public function test_categories_are_scoped_by_branch(): void
    {
        // Global Category (branch_id = null)
        Category::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => null,
            'name' => 'Entradas Gerais',
            'type' => 'product',
            'color' => '#3b82f6',
            'icon' => 'fa-utensils',
            'is_active' => true,
        ]);

        // Branch 1 Category
        Category::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch1->id,
            'name' => 'Especiais Matola',
            'type' => 'product',
            'color' => '#ef4444',
            'icon' => 'fa-fire',
            'is_active' => true,
        ]);

        // Branch 2 Category
        Category::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch2->id,
            'name' => 'Especiais Maputo',
            'type' => 'product',
            'color' => '#8b5cf6',
            'icon' => 'fa-star',
            'is_active' => true,
        ]);

        // Accessing index as Branch 1 user
        $responseBranch1 = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch1->id,
            ])
            ->get(route('categories.index'));

        $responseBranch1->assertStatus(200);
        $responseBranch1->assertSeeText('Entradas Gerais');
        $responseBranch1->assertSeeText('Especiais Matola');
        $responseBranch1->assertDontSeeText('Especiais Maputo');
    }
}
