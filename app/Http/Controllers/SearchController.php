<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\Debt;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        
        if (empty($query)) {
            return view('search.results', [
                'query' => $query,
                'results' => collect(),
                'totalResults' => 0
            ]);
        }

        $results = $this->performSearch($query, $type);
        
        return view('search.results', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'totalResults' => $results->sum(fn($group) => $group->count())
        ]);
    }

    public function api(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 10);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'query' => $query,
                'results' => [],
                'total' => 0
            ]);
        }

        $results = $this->performQuickSearch($query, $limit);
        
        return response()->json([
            'query' => $query,
            'results' => $results,
            'total' => collect($results)->sum(fn($group) => count($group['items']))
        ]);
    }

    private function performSearch($query, $type = 'all')
    {
        $results = collect();

        // Buscar Produtos
        if ($type === 'all' || $type === 'products') {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->with('category')
                ->where('is_active', true)
                ->limit(20)
                ->get()
                ->map(function ($product) {
                    return [
                        'type' => 'product',
                        'id' => $product->id,
                        'title' => $product->name,
                        'subtitle' => $product->category->name ?? 'Sem categoria',
                        'description' => $product->description ?? 'Sem descrição',
                        'price' => $product->selling_price,
                        'stock' => $product->stock_quantity,
                        'url' => route('products.show', $product->id),
                        'icon' => 'fas fa-cube',
                        'badge' => $product->stock_quantity > 0 ? 'Em estoque' : 'Fora de estoque',
                        'badge_class' => $product->stock_quantity > 0 ? 'bg-success' : 'bg-danger'
                    ];
                });
            
            if ($products->isNotEmpty()) {
                $results['products'] = $products;
            }
        }

        // Buscar Vendas
        if (($type === 'all' || $type === 'sales') && $this->userCan('view_sales')) {
            $sales = Sale::where('customer_name', 'LIKE', "%{$query}%")
                ->orWhere('customer_phone', 'LIKE', "%{$query}%")
                ->orWhere('id', 'LIKE', "%{$query}%") // Buscar por ID da venda
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($sale) {
                    return [
                        'type' => 'sale',
                        'id' => $sale->id,
                        'title' => "Venda #{$sale->id}",
                        'subtitle' => $sale->customer_name ?: 'Cliente não informado',
                        'description' => "Total: " . number_format($sale->total_amount, 2) . " MZN - " . $sale->sale_date,
                        'date' => $sale->created_at->format('d/m/Y H:i'),
                        'url' => route('sales.show', $sale->id),
                        'icon' => 'fas fa-shopping-cart',
                        'badge' => ucfirst($sale->payment_method),
                        'badge_class' => $sale->payment_method === 'cash' ? 'bg-success' : 'bg-info'
                    ];
                });

            if ($sales->isNotEmpty()) {
                $results['sales'] = $sales;
            }
        }

        // Buscar Pedidos
        if (($type === 'all' || $type === 'orders') && $this->userCanAny(['view_orders', 'create_orders'])) {
            $orders = Order::where('customer_name', 'LIKE', "%{$query}%")
                ->orWhere('customer_phone', 'LIKE', "%{$query}%")
                ->orWhere('customer_email', 'LIKE', "%{$query}%")
                ->orWhere('id', 'LIKE', "%{$query}%")
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order',
                        'id' => $order->id,
                        'title' => "Pedido #{$order->id}",
                        'subtitle' => $order->customer_name,
                        'description' => "Valor estimado: " . number_format($order->estimated_amount, 2) . " MZN",
                        'date' => $order->created_at->format('d/m/Y H:i'),
                        'delivery' => $order->delivery_date ? $order->delivery_date->format('d/m/Y') : 'Não definido',
                        'url' => route('orders.show', $order->id),
                        'icon' => 'fas fa-clipboard-list',
                        'badge' => ucfirst($order->status),
                        'badge_class' => $this->getOrderStatusClass($order->status)
                    ];
                });

            if ($orders->isNotEmpty()) {
                $results['orders'] = $orders;
            }
        }

        // Buscar Usuários (apenas para admins)
        if (($type === 'all' || $type === 'users') && $this->userCan('manage_users')) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user',
                        'id' => $user->id,
                        'title' => $user->name,
                        'subtitle' => $user->email,
                        'description' => 'Usuário do sistema',
                        'url' => route('users.show', $user->id),
                        'icon' => 'fas fa-user',
                        'badge' => $user->is_active ? 'Ativo' : 'Inativo',
                        'badge_class' => $user->is_active ? 'bg-success' : 'bg-secondary'
                    ];
                });

            if ($users->isNotEmpty()) {
                $results['users'] = $users;
            }
        }

        // Buscar Dívidas
        if (($type === 'all' || $type === 'debts') && $this->userCan('view_debts')) {
            $debts = Debt::where('customer_name', 'LIKE', "%{$query}%")
                ->orWhere('customer_phone', 'LIKE', "%{$query}%")
                ->orWhere('customer_document', 'LIKE', "%{$query}%")
                ->orderBy('debt_date', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($debt) {
                    return [
                        'type' => 'debt',
                        'id' => $debt->id,
                        'title' => "Dívida de {$debt->customer_name}",
                        'subtitle' => $debt->customer_phone ?: 'Sem telefone',
                        'description' => "Valor: " . number_format($debt->remaining_amount, 2) . " MZN",
                        'date' => $debt->debt_date,
                        'due_date' => $debt->due_date,
                        'url' => route('debts.show', $debt->id),
                        'icon' => 'fas fa-money-bill-wave',
                        'badge' => ucfirst($debt->status),
                        'badge_class' => $this->getDebtStatusClass($debt->status)
                    ];
                });

            if ($debts->isNotEmpty()) {
                $results['debts'] = $debts;
            }
        }

        // Buscar Categorias
        if (($type === 'all' || $type === 'categories') && $this->userCan('manage_categories')) {
            $categories = Category::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->where('status', 'active')
                ->limit(10)
                ->get()
                ->map(function ($category) {
                    return [
                        'type' => 'category',
                        'id' => $category->id,
                        'title' => $category->name,
                        'subtitle' => ucfirst($category->type),
                        'description' => $category->description ?: 'Sem descrição',
                        'url' => route('categories.show', $category->id),
                        'icon' => $category->icon ?: 'fas fa-folder',
                        'badge' => ucfirst($category->type),
                        'badge_class' => 'bg-primary'
                    ];
                });

            if ($categories->isNotEmpty()) {
                $results['categories'] = $categories;
            }
        }

        return $results;
    }

    private function performQuickSearch($query, $limit)
    {
        $results = [];

        // Busca rápida de produtos
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get(['id', 'name', 'selling_price', 'stock_quantity']);

        if ($products->isNotEmpty()) {
            $results['products'] = [
                'title' => 'Produtos',
                'icon' => 'fas fa-cube',
                'items' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'text' => $product->name,
                        'subtitle' => number_format($product->selling_price, 2) . ' MZN',
                        'url' => route('products.show', $product->id),
                        'stock' => $product->stock_quantity
                    ];
                })->toArray()
            ];
        }

        // Busca rápida de clientes (baseado em vendas)
        if ($this->userCan('view_sales')) {
            $customers = Sale::where('customer_name', 'LIKE', "%{$query}%")
                ->orWhere('customer_phone', 'LIKE', "%{$query}%")
                ->select('customer_name', 'customer_phone')
                ->distinct()
                ->limit(5)
                ->get()
                ->filter(fn($sale) => !empty($sale->customer_name));

            if ($customers->isNotEmpty()) {
                $results['customers'] = [
                    'title' => 'Clientes',
                    'icon' => 'fas fa-users',
                    'items' => $customers->map(function ($customer) {
                        return [
                            'text' => $customer->customer_name,
                            'subtitle' => $customer->customer_phone ?: 'Sem telefone',
                            'url' => route('sales.index', ['customer' => $customer->customer_name])
                        ];
                    })->toArray()
                ];
            }
        }

        // Busca rápida de pedidos pendentes
        if ($this->userCan('view_orders')) {
            $pendingOrders = Order::where('customer_name', 'LIKE', "%{$query}%")
                ->where('status', '!=', 'completed')
                ->limit(3)
                ->get(['id', 'customer_name', 'status', 'delivery_date']);

            if ($pendingOrders->isNotEmpty()) {
                $results['orders'] = [
                    'title' => 'Pedidos Pendentes',
                    'icon' => 'fas fa-clipboard-list',
                    'items' => $pendingOrders->map(function ($order) {
                        return [
                            'text' => "Pedido #{$order->id}",
                            'subtitle' => $order->customer_name . ' - ' . ucfirst($order->status),
                            'url' => route('orders.show', $order->id)
                        ];
                    })->toArray()
                ];
            }
        }

        return $results;
    }

    private function getOrderStatusClass($status)
    {
        return match($status) {
            'pending' => 'bg-warning',
            'in_progress' => 'bg-info',
            'completed' => 'bg-success',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    private function getDebtStatusClass($status)
    {
        return match($status) {
            'active' => 'bg-warning',
            'partial' => 'bg-info',
            'paid' => 'bg-success',
            'overdue' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Helper function para verificar permissões do usuário
     * Adapte conforme seu sistema de permissões
     */
    private function userCan($permission)
    {
        // Implementar conforme seu sistema de permissões
        // Por enquanto, retorna true para todos os usuários logados
        return auth()->check();
    }

    /**
     * Helper function para verificar múltiplas permissões
     */
    private function userCanAny($permissions)
    {
        // Implementar conforme seu sistema de permissões
        // Por enquanto, retorna true para todos os usuários logados
        return auth()->check();
    }
}