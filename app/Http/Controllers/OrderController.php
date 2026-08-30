<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\StockMovement;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Models\Sale; // Adicionado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    // O método index() permanece praticamente o mesmo, está bom.
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Filtros (seu código existente está bom)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15);

        // Estatísticas (seu código existente está bom)
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'overdue' => Order::where('delivery_date', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count()
        ];
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();

        return view('orders.index', compact('orders', 'stats', 'products', 'categories'));
    }

    // Mostra o formulário de criação (página completa)
    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('status', 'active')->get();
        return view('orders.create', compact('products', 'categories'));
    }

    // Salva um novo pedido
    public function store(Request $request)
    {
        Log::info('Store method called', $request->all());

        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:100',
            'description' => 'required|string',
            'estimated_amount' => 'required|numeric|min:0.01',
            'advance_payment' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'items' => 'required', // Aceita string JSON ou array
            'create_debt' => 'boolean',
            'debt_due_date' => 'nullable|date|after_or_equal:delivery_date'
        ]);

        Log::info('Validation passed');

        try {
            // Decodificar itens - suporta tanto JSON string quanto array
            $items = $request->items;
            if (is_string($items)) {
                $items = json_decode($items, true);
            }

            // Se veio como array indexado do formulário (items[0][product_id], etc.)
            // converter para o formato esperado
            if (is_array($items)) {
                $items = array_map(function($item) {
                    // Se tiver product_id mas não item_name, buscar o nome do produto
                    if (isset($item['product_id']) && !isset($item['item_name'])) {
                        $product = Product::find($item['product_id']);
                        $item['item_name'] = $product ? $product->name : 'Produto desconhecido';
                    }
                    return $item;
                }, $items);
            }

            Log::info('Items decoded', ['items' => $items]);

            if (!$items || !is_array($items) || empty($items)) {
                Log::error('No items provided');
                return back()->withErrors(['items' => 'Nenhum item foi fornecido para o pedido.'])->withInput();
            }

            // Validar estrutura dos itens
            foreach ($items as $index => $item) {
                if (!isset($item['item_name']) || !isset($item['quantity']) || !isset($item['unit_price'])) {
                    Log::error('Invalid item structure', ['item' => $item, 'index' => $index]);
                    return back()->withErrors(['items' => "Item #{$index} com dados incompletos."])->withInput();
                }

                if ($item['quantity'] <= 0 || $item['unit_price'] < 0) {
                    Log::error('Invalid item values', ['item' => $item]);
                    return back()->withErrors(['items' => "Quantidade e preço devem ser valores positivos."])->withInput();
                }
            }

            $order = DB::transaction(function () use ($request, $items) {
                // Validar valor estimado com soma dos itens
                $calculatedTotal = array_sum(array_map(function ($item) {
                    return $item['quantity'] * $item['unit_price'];
                }, $items));

                $estimatedAmount = (float) $request->estimated_amount;

                // Permitir diferença de até 5% para flexibilidade
                if (abs($calculatedTotal - $estimatedAmount) > ($calculatedTotal * 0.05)) {
                    $estimatedAmount = $calculatedTotal;
                }

                Log::info('Creating order', [
                    'calculated_total' => $calculatedTotal,
                    'estimated_amount' => $estimatedAmount
                ]);

                // Criar o pedido
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'description' => $request->description,
                    'estimated_amount' => $estimatedAmount,
                    'advance_payment' => $request->advance_payment ?? 0,
                    'delivery_date' => $request->delivery_date,
                    'priority' => $request->priority,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);

                Log::info('Order created', ['order_id' => $order->id]);

                // Criar os itens do pedido
                foreach ($items as $item) {
                    $totalPrice = $item['quantity'] * $item['unit_price'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $totalPrice
                    ]);
                }

                Log::info('Order items created', ['count' => count($items)]);

                // Criar dívida se solicitado
                $remainingAmount = $order->estimated_amount - $order->advance_payment;
                if ($request->create_debt && $remainingAmount > 0) {
                    Debt::create([
                        'user_id' => auth()->id(),
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'original_amount' => $remainingAmount,
                        'remaining_amount' => $remainingAmount,
                        'debt_date' => now()->toDateString(),
                        'due_date' => $request->debt_due_date ?: now()->addDays(30)->toDateString(),
                        'description' => "Pedido #{$order->id} - {$order->description}",
                        'status' => 'active',
                        'notes' => "Valor em aberto do pedido #{$order->id}"
                    ]);

                    Log::info('Debt created', ['amount' => $remainingAmount]);
                }

                return $order;
            });

            Log::info('Order created successfully', ['order_id' => $order->id]);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pedido criado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation exception', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating order: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'exception' => $e
            ]);

            return back()->withErrors(['error' => 'Erro ao criar pedido: ' . $e->getMessage()])->withInput();
        }
    }

    // Método para criar dívida a partir do pedido
    public function createDebt(Request $request, Order $order)
    {
        if (!$order->canCreateDebt()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível criar dívida para este pedido.'
                ], 400);
            }
            return back()->with('error', 'Não é possível criar dívida para este pedido.');
        }

        $request->validate([
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $remainingAmount = $order->estimated_amount - $order->advance_payment;

            // Criar a dívida
            $debt = Debt::create([
                'debt_type' => 'product', // IMPORTANTE: definir como product
                'user_id' => auth()->id(),
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'original_amount' => $remainingAmount,
                'remaining_amount' => $remainingAmount,
                'debt_date' => now()->toDateString(),
                'due_date' => $request->due_date,
                'description' => $request->description ?: "Valor restante do Pedido #{$order->id}",
                'status' => 'active',
                'order_id' => $order->id,
            ]);

            // ADICIONAR: Criar os itens da dívida e movimentar stock
            foreach ($order->items as $orderItem) {
                // Criar item da dívida
                DebtItem::create([
                    'debt_id' => $debt->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'total_price' => $orderItem->total_price
                ]);

                // Movimentar stock usando o método centralizado (lida com produtos vinculados)
                if ($orderItem->product) {
                    $orderItem->product->updateStock(
                        $orderItem->quantity, 
                        'out', 
                        auth()->id(), 
                        "Dívida #$debt->id do Pedido #$order->id", 
                        $debt->id
                    );
                }
            }

            // Atualizar o pedido
            $order->update(['debt_id' => $debt->id]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dívida criada com sucesso!',
                    'redirect' => route('debts.show', $debt)
                ]);
            }

            return redirect()->route('debts.show', $debt)->with('success', 'Dívida criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar dívida: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar dívida: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao criar dívida: ' . $e->getMessage());
        }
    }
    // Método para converter pedido em venda
    public function convertToSale(Request $request, Order $order)
    {
        if (!$order->canBeConvertedToSale()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido não pode ser convertido em venda.'
                ], 400);
            }
            return back()->with('error', 'Este pedido não pode ser convertido em venda.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola'
        ]);

        try {
            $sale = null;

            DB::transaction(function () use ($request, $order, &$sale) {
                // Criar a venda
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_email' => $order->customer_email,
                    'subtotal' => $order->estimated_amount,
                    'total_amount' => $order->estimated_amount,
                    'payment_method' => $request->payment_method,
                    'sale_date' => now(),
                    'notes' => "Venda gerada a partir do Pedido #{$order->id} - {$order->description}",
                    'order_id' => $order->id,
                ]);

                // Criar itens da venda
                foreach ($order->items as $item) {
                    $sale->items()->create([
                        'product_id' => $item->product_id,
                        'product_name' => $item->item_name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                        'description' => $item->description,
                    ]);

                    // Atualizar stock usando o método centralizado
                    if ($item->product) {
                        $item->product->updateStock($item->quantity, 'out', auth()->id(), "Venda gerada do Pedido #{$order->id}", $sale->id);
                    }
                }

                // Atualizar status do pedido
                $order->update([
                    'status' => 'delivered',
                    'sale_id' => $sale->id
                ]);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido convertido em venda com sucesso!',
                    'redirect' => route('sales.show', $sale)
                ]);
            }

            return redirect()->route('sales.show', $sale)->with('success', 'Pedido convertido em venda com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao converter pedido em venda: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao converter pedido: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao converter pedido: ' . $e->getMessage());
        }
    }
    // Mostra os detalhes de um pedido (página cFompleta)
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'debt']);
        return view('orders.show', compact('order'));
    }

    // Mostra o formulário de edição (página completa)
    public function edit(Order $order)
    {
        if (!$order->canBeEdited()) {
            return redirect()->route('orders.show', $order)->with('error', 'Este pedido não pode ser editado pois ja foi Entregue.');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $order->load('items'); // Carrega os itens do pedido

        return view('orders.edit', compact('order', 'products'));
    }

    // Atualiza um pedido existente
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'description' => 'required|string',
            'delivery_date' => 'nullable|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'advance_payment' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                $totalAmount = 0;
                foreach ($validated['items'] as $item) {
                    $totalAmount += $item['quantity'] * $item['unit_price'];
                }

                $order->update([
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'description' => $validated['description'],
                    'estimated_amount' => $totalAmount,
                    'advance_payment' => $validated['advance_payment'] ?? 0,
                    'delivery_date' => $validated['delivery_date'],
                    'priority' => $validated['priority'],
                    'notes' => $validated['notes'],
                ]);

                // Sincronizar itens (remove os antigos e adiciona os novos)
                $order->items()->delete();
                foreach ($validated['items'] as $itemData) {
                    $product = Product::find($itemData['product_id']);
                    $order->items()->create([
                        'product_id' => $product->id,
                        'item_name' => $product->name,
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                    ]);
                }
            });

            return redirect()->route('orders.show', $order)->with('success', 'Pedido atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar pedido #{$order->id}: " . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage())->withInput();
        }
    }

    // Atualiza apenas o status (ação rápida da listagem)
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'delivered', 'cancelled'])],
        ]);

        try {
            // Se for uma requisição AJAX/JSON, retorne JSON
            if ($request->expectsJson() || $request->ajax()) {
                $order->update(['status' => $request->status]);

                return response()->json([
                    'success' => true,
                    'message' => 'Status do pedido atualizado com sucesso!',
                    'new_status' => $request->status,
                    'status_text' => $order->status_text,
                    'status_badge' => $order->status_badge
                ]);
            }

            // Se for requisição normal, redireciona
            $order->update(['status' => $request->status]);
            return redirect()->route('orders.index')->with('success', 'Status do pedido atualizado!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status: ' . $e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar status: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao atualizar status.');
        }
    }

    /**
     * NOVA: Mostra a página para concluir um pedido (gerar venda ou dívida).
     */
    public function complete(Order $order)
    {
        if ($order->status !== 'in_progress' && $order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Apenas pedidos em andamento ou pendentes podem ser concluídos.');
        }

        $remainingAmount = $order->estimated_amount - $order->advance_payment;

        return view('orders.complete', compact('order', 'remainingAmount'));
    }

    /**
     * NOVA: Processa a conclusão do pedido.
     */
    public function processCompletion(Request $request, Order $order)
    {
        $request->validate([
            'action' => 'required|in:create_sale,create_debt',
            'payment_method' => 'required_if:action,create_sale|in:cash,card,transfer,mpesa,emola',
            'debt_due_date' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                if ($request->action === 'create_sale') {
                    // Lógica para criar a venda
                    $sale = Sale::create([
                        'user_id' => auth()->id(),
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'subtotal' => $order->estimated_amount,
                        'total_amount' => $order->estimated_amount,
                        'payment_method' => $request->payment_method,
                        'sale_date' => now(),
                        'notes' => "Venda gerada a partir do Pedido #{$order->id}",
                    ]);

                    foreach ($order->items as $item) {
                        $sale->items()->create([
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'original_unit_price' => $item->unit_price,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ]);
                        // Aqui você pode adicionar a lógica de baixar stock se ainda não foi feito
                    }

                    $order->update(['status' => 'delivered', 'sale_id' => $sale->id]);
                } elseif ($request->action === 'create_debt') {
                    // Lógica para criar a dívida
                    $remaining = $order->estimated_amount - $order->advance_payment;
                    if ($remaining > 0) {
                        $debt = Debt::create([
                            'user_id' => auth()->id(),
                            'customer_name' => $order->customer_name,
                            'customer_phone' => $order->customer_phone,
                            'original_amount' => $remaining,
                            'remaining_amount' => $remaining,
                            'debt_date' => now()->toDateString(),
                            'due_date' => $request->debt_due_date ?: now()->addDays(30)->toDateString(),
                            'description' => "Valor restante do Pedido #{$order->id}",
                            'status' => 'active',
                            'order_id' => $order->id,
                        ]);
                        $order->update(['status' => 'completed', 'debt_id' => $debt->id]);
                    } else {
                        // Se não há valor restante, apenas marca como concluído
                        $order->update(['status' => 'completed']);
                    }
                }
            });

            return redirect()->route('orders.show', $order)->with('success', 'Pedido concluído com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro ao processar conclusão do pedido #{$order->id}: " . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro: ' . $e->getMessage());
        }
    }

    // O método destroy() pode ser simplificado para apenas cancelar.
    public function destroy(Order $order)
    {
        if (!$order->canBeCancelled()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido não pode ser cancelado.'
                ], 400);
            }
            return back()->with('error', 'Este pedido não pode ser cancelado.');
        }

        $order->update(['status' => 'cancelled']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado com sucesso.',
                'redirect' => route('orders.index')
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Pedido cancelado com sucesso.');
    }

    // O método duplicate() está bom, apenas precisa apontar para a view `create` refatorada.
    public function duplicate(Order $order)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('status', 'active')->get();
        $order->load('items');

        // Passamos o pedido a ser duplicado para a view de criação
        return view('orders.create', [
            'products' => $products,
            'categories' => $categories,
            'orderToDuplicate' => $order
        ]);
    }
    // Relatório de pedidos com filtros avançados
    public function report(Request $request)
    {
        $query = Order::with(['user', 'items']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->get();

        // Estatísticas do relatório
        $reportStats = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('estimated_amount'),
            'total_advance' => $orders->sum('advance_payment'),
            'total_pending' => $orders->sum(function ($order) {
                return $order->estimated_amount - $order->advance_payment;
            }),
            'by_status' => [
                'pending' => $orders->where('status', 'pending')->count(),
                'in_progress' => $orders->where('status', 'in_progress')->count(),
                'completed' => $orders->where('status', 'completed')->count(),
                'delivered' => $orders->where('status', 'delivered')->count(),
                'cancelled' => $orders->where('status', 'cancelled')->count(),
            ],
            'by_priority' => [
                'low' => $orders->where('priority', 'low')->count(),
                'medium' => $orders->where('priority', 'medium')->count(),
                'high' => $orders->where('priority', 'high')->count(),
                'urgent' => $orders->where('priority', 'urgent')->count(),
            ],
            'overdue_count' => $orders->filter(function ($order) {
                return $order->delivery_date && $order->delivery_date < now() &&
                    in_array($order->status, ['pending', 'in_progress']);
            })->count()
        ];

        return view('orders.report', compact('orders', 'reportStats'));
    }

    // API para busca de produtos
    public function searchProducts(Request $request)
    {
        $term = $request->get('term', $request->get('q', ''));

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('description', 'like', '%' . $term . '%');
            })
            ->with('category')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->selling_price,
                    'stock' => $product->stock_quantity,
                    'category' => $product->category->name ?? 'Sem categoria'
                ];
            });

        return response()->json($products);
    }
}
