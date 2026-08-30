<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'ZBIZ Starter',
                'slug'          => 'starter',
                'description'   => 'Ideal para bancas, micro-lojas e negócios individuais em início.',
                'monthly_price' => 1250.00,
                'annual_price'  => 12500.00, // 2 months free
                'max_branches'  => 1,
                'max_users'     => 2,
                'max_products'  => 500,
                'features'      => ['pos', 'sales', 'stock_basic', 'cash_management'],
                'sort_order'    => 1,
            ],
            [
                'name'          => 'ZBIZ Pro',
                'slug'          => 'pro',
                'description'   => 'Para lojas estruturadas, oficinas e empresas em expansão.',
                'monthly_price' => 2950.00,
                'annual_price'  => 29500.00,
                'max_branches'  => 2,
                'max_users'     => 5,
                'max_products'  => 5000,
                'features'      => ['pos', 'sales', 'stock_basic', 'cash_management', 'debts', 'salaries', 'reports_advanced'],
                'sort_order'    => 2,
            ],
            [
                'name'          => 'ZBIZ Business',
                'slug'          => 'business',
                'description'   => 'Para médias empresas, redes de lojas e múltiplos pontos de venda.',
                'monthly_price' => 5500.00,
                'annual_price'  => 55000.00,
                'max_branches'  => 5,
                'max_users'     => 15,
                'max_products'  => 0, // unlimited
                'features'      => ['pos', 'sales', 'stock_basic', 'cash_management', 'debts', 'salaries', 'reports_advanced', 'multi_branch', 'stock_transfers', 'mpesa_api'],
                'sort_order'    => 3,
            ],
            [
                'name'          => 'ZBIZ Pharmacy+',
                'slug'          => 'pharmacy_plus',
                'description'   => 'Especializado para Farmácias, Drogarias e Clínicas com conformidade ANARME.',
                'monthly_price' => 4500.00,
                'annual_price'  => 45000.00,
                'max_branches'  => 2,
                'max_users'     => 8,
                'max_products'  => 0,
                'features'      => ['pos', 'sales', 'cash_management', 'debts', 'pharmacy', 'pharmacy_batches', 'fefo_expiry_alerts', 'prescription_records', 'reports_advanced'],
                'sort_order'    => 4,
            ],
            [
                'name'          => 'ZBIZ Enterprise',
                'slug'          => 'enterprise',
                'description'   => 'Solução corporativa com filiais e usuários ilimitados e suporte 24/7.',
                'monthly_price' => 9500.00,
                'annual_price'  => 95000.00,
                'max_branches'  => 0, // unlimited
                'max_users'     => 0,
                'max_products'  => 0,
                'features'      => ['*'], // all features
                'sort_order'    => 5,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(['slug' => $planData['slug']], $planData);
        }
    }
}
