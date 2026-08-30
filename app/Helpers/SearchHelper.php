<?php

// Crie o arquivo app/Helpers/SearchHelper.php

if (!function_exists('userCan')) {
    /**
     * Verifica se o usuário atual tem uma permissão específica
     * 
     * @param string $permission
     * @return bool
     */
    function userCan($permission)
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        
        // Se for admin, tem todas as permissões
        if ($user->role === 'admin') {
            return true;
        }

        // Mapa de permissões por role
        $permissions = [
            'manager' => [
                // Dashboard
                'view_dashboard',
                
                // Produtos
                'view_products', 'create_products', 'edit_products', 'delete_products',
                'manage_categories',
                'view_stock_movements', 'create_stock_movements',
                
                // Vendas e Pedidos
                'view_sales', 'create_sales', 'edit_sales', 'edit_own_sales',
                'view_orders', 'create_orders', 'edit_orders',
                
                // Financeiro
                'view_debts', 'create_debts', 'edit_debts', 'manage_debts',
                'view_expenses', 'create_expenses', 'edit_expenses',
                'view_reports', 'view_basic_reports', 'export_reports',
                
                // Outros
                'backup_system', 'view_logs'
            ],
            
            'employee' => [
                // Dashboard
                'view_dashboard',
                
                // Produtos (apenas visualização)
                'view_products',
                'view_stock_movements',
                
                // Vendas
                'view_sales', 'create_sales', 'edit_own_sales',
                'view_orders', 'create_orders',
                
                // Financeiro básico
                'view_debts', 'create_debts',
                'view_expenses', 'create_expenses',
                'view_basic_reports'
            ]
        ];

        return in_array($permission, $permissions[$user->role] ?? []);
    }
}

if (!function_exists('userCanAny')) {
    /**
     * Verifica se o usuário tem pelo menos uma das permissões fornecidas
     * 
     * @param array $permissions
     * @return bool
     */
    function userCanAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (userCan($permission)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('userCanAll')) {
    /**
     * Verifica se o usuário tem todas as permissões fornecidas
     * 
     * @param array $permissions  
     * @return bool
     */
    function userCanAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!userCan($permission)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('formatSearchResult')) {
    /**
     * Formata resultado de busca para exibição
     * 
     * @param mixed $item
     * @param string $query
     * @return string
     */
    function formatSearchResult($item, $query)
    {
        if (is_array($item) && isset($item['title'])) {
            return highlightText($item['title'], $query);
        }
        
        if (is_object($item) && method_exists($item, 'getSearchTitle')) {
            return highlightText($item->getSearchTitle(), $query);
        }
        
        return $item;
    }
}

if (!function_exists('highlightText')) {
    /**
     * Destaca termo de busca no texto
     * 
     * @param string $text
     * @param string $term
     * @return string
     */
    function highlightText($text, $term)
    {
        if (empty($term)) {
            return $text;
        }
        
        $pattern = '/(' . preg_quote($term, '/') . ')/i';
        return preg_replace($pattern, '<mark class="bg-warning bg-opacity-25">$1</mark>', $text);
    }
}

if (!function_exists('getSearchableModels')) {
    /**
     * Retorna lista de models pesquisáveis baseado nas permissões do usuário
     * 
     * @return array
     */
    function getSearchableModels()
    {
        $models = [];
        
        if (userCan('view_products')) {
            $models['products'] = [
                'model' => \App\Models\Product::class,
                'fields' => ['name', 'description', 'sku'],
                'icon' => 'fas fa-cube',
                'route' => 'products.show'
            ];
        }
        
        if (userCan('view_sales')) {
            $models['sales'] = [
                'model' => \App\Models\Sale::class,
                'fields' => ['invoice_number', 'customer_name', 'customer_phone'],
                'icon' => 'fas fa-shopping-cart',
                'route' => 'sales.show'
            ];
        }
        
        if (userCanAny(['view_orders', 'create_orders'])) {
            $models['orders'] = [
                'model' => \App\Models\Order::class,
                'fields' => ['order_number', 'customer_name', 'customer_phone'],
                'icon' => 'fas fa-clipboard-list',
                'route' => 'orders.show'
            ];
        }
        
        if (userCan('manage_users')) {
            $models['users'] = [
                'model' => \App\Models\User::class,
                'fields' => ['name', 'email'],
                'icon' => 'fas fa-users',
                'route' => 'users.show'
            ];
        }
        
        return $models;
    }
}