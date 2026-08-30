<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\AppBaseController;

// HERDA DO SEU NOVO BASECONTROLLER
class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        // Construir query com filtros
        $query = Product::with('category');

        // Aplicar filtros se fornecidos
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Ordenar e paginar
        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        // Buscar categorias para filtros
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        // Calcular estatísticas básicas
        $allProducts = Product::all();
        $lowStockCount = Product::where('type', 'product')
                                ->whereRaw('stock_quantity <= min_stock_level')
                                ->where('is_active', true)
                                ->count();

        return view('products.index', compact('products', 'categories', 'allProducts', 'lowStockCount'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request)
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        // Produtos físicos que podem ser vinculados a serviços (ex: Papel A4)
        $physicalProducts = Product::where('type', 'product')->where('is_active', true)->orderBy('name')->get();
        return view('products.create', compact('categories', 'physicalProducts'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            $validationRules = [
                'name' => 'required|string|max:150',
                'category_id' => 'required|exists:categories,id',
                'linked_product_id' => 'nullable|exists:products,id',
                'type' => 'required|in:product,service',
                'selling_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'unit' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean'
            ];

            // Validações adicionais se for produto
            if ($request->type === 'product') {
                $validationRules['stock_quantity'] = 'required|integer|min:0';
                $validationRules['min_stock_level'] = 'required|integer|min:0';
            }

            $validated = $request->validate($validationRules);

            DB::beginTransaction();

            $data = collect($validated)->only([
                'name', 'category_id', 'linked_product_id', 'type', 'selling_price',
                'purchase_price', 'unit', 'description'
            ])->toArray();

            $data['is_active'] = $request->boolean('is_active', true);

            if ($request->type === 'product') {
                $data['stock_quantity'] = (int) $request->input('stock_quantity', 0);
                $data['min_stock_level'] = (int) $request->input('min_stock_level', 0);
            } else {
                $data['stock_quantity'] = 0;
                $data['min_stock_level'] = 0;
                $data['unit'] = null;
            }

            $product = Product::create($data);

            // Criar movimento inicial de estoque se necessário
            if ($product->type === 'product' && $product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id'     => $product->id,
                    'user_id'        => auth()->id(),
                    'movement_type'  => 'in',
                    'quantity'       => $product->stock_quantity,
                    'reason'         => 'Estoque inicial do produto',
                    'movement_date'  => now(),
                ]);
            }

            DB::commit();

            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->success('products.index', 'Produto criado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar produto: ' . $e->getMessage());
            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->error('products.index', 'Erro ao criar produto. Tente novamente.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'stockMovements.user']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        // Produtos físicos que podem ser vinculados (exceto o próprio para evitar loops)
        $physicalProducts = Product::where('type', 'product')
                                   ->where('is_active', true)
                                   ->where('id', '!=', $product->id)
                                   ->orderBy('name')
                                   ->get();
        return view('products.edit', compact('product', 'categories', 'physicalProducts'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            if ($request->has('toggle_status')) {
                $product->update([
                    'is_active' => $request->boolean('is_active')
                ]);

                $status = $request->boolean('is_active') ? 'ativado' : 'desativado';
                // ✅ PASSANDO O PARÂMETRO 'product'
                return $this->success('products.index', "Produto '{$product->name}' {$status} com sucesso!", [$product]);
            }

            // Validação normal para edição completa
            $validationRules = [
                'name' => 'required|string|max:150',
                'category_id' => 'required|exists:categories,id',
                'linked_product_id' => 'nullable|exists:products,id',
                'selling_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'unit' => 'nullable|string|max:20',
                'description' => 'nullable|string|max:500',
                'is_active' => 'boolean'
            ];

            if ($product->type === 'product') {
                $validationRules['min_stock_level'] = 'required|integer|min:0';
            }

            $validated = $request->validate($validationRules);

            $data = collect($validated)->only([
                'name', 'category_id', 'linked_product_id', 'selling_price',
                'purchase_price', 'unit', 'description'
            ])->toArray();

            $data['is_active'] = $request->boolean('is_active', true);

            if ($product->type === 'product') {
                $data['min_stock_level'] = (int) $request->input('min_stock_level', 0);
            }

            $product->update($data);

            // ✅ PASSANDO O PARÂMETRO 'product'
            return $this->success('products.show', 'Produto atualizado com sucesso!', [$product]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage());
            // ✅ MANTÉM O REDIRECIONAMENTO SEM PARÂMETROS (para index)
            return $this->error('products.index', 'Erro ao atualizar produto.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    /**/
    public function destroy(Product $product)
    {
        try {
            // Verificar se já está excluído
            if ($product->is_deleted) {
                return $this->error('products.index', 'Este produto já foi excluído do sistema.', []);
            }

            DB::beginTransaction();

            // Se for produto e tiver estoque, registrar movimento de saída final
            if ($product->type === 'product' && $product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'movement_type' => 'out',
                    'quantity' => $product->stock_quantity,
                    'reason' => "PRODUTO EXCLUÍDO: {$product->name} (ID: {$product->id}) — Estoque zerado por exclusão do sistema.",
                    'movement_date' => now(),
                ]);

                // Zerar o estoque
                $product->stock_quantity = 0;
            }

            // Marcar como excluído
            $product->is_deleted = true;
            $product->is_active = false;
            $product->deleted_at = now();
            $product->save();

            DB::commit();

            return $this->success(
                'products.index',
                "Produto '{$product->name}' foi excluído com sucesso! Seu histórico e movimentações foram preservados.",
                []
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao excluir produto: ' . $e->getMessage());
            return $this->error('products.index', 'Erro ao processar a exclusão do produto.', []);
        }
    }

    /**
     * Adjust stock for a product.
     */
    public function adjustStock(Request $request, Product $product)
    {
        try {
            if ($product->type !== 'product') {
                return $this->error('products.show', 'Apenas produtos podem ter estoque ajustado.', [$product]);
            }

            $request->validate([
                'adjustment_type' => 'required|in:increase,decrease',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:200',
            ]);

            DB::beginTransaction();

            $movementType = $request->adjustment_type === 'increase' ? 'in' : 'out';
            $quantity = $request->integer('quantity');

            if ($request->adjustment_type === 'decrease') {
                if ($product->stock_quantity < $quantity) {
                    return $this->error('products.show', "Estoque insuficiente. Disponível: {$product->stock_quantity} {$product->unit}.", [$product]);
                }
            }

            if ($request->adjustment_type === 'increase') {
                $product->stock_quantity += $quantity;
            } else {
                $product->stock_quantity -= $quantity;
            }

            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reason' => $request->reason,
                'movement_date' => now(),
            ]);

            DB::commit();

            $action = $request->adjustment_type === 'increase' ? 'Entrada' : 'Saída';
            return $this->success('products.show', "{$action} de {$quantity} {$product->unit} registrada com sucesso! Novo estoque: {$product->stock_quantity}", [$product]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao ajustar estoque: ' . $e->getMessage());
            return $this->error('products.show', 'Erro ao ajustar estoque.', [$product]);
        }
    }
    /**
     * Generate product report.
     */
    public function report(Request $request)
    {
        $query = Product::with('category');

        // Aplicar filtros
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('stock_status') && $request->stock_status !== '') {
            switch ($request->stock_status) {
                case 'low':
                    $query->where('type', 'product')->whereRaw('stock_quantity <= min_stock_level');
                    break;
                case 'normal':
                    $query->where('type', 'product')->whereRaw('stock_quantity > min_stock_level AND stock_quantity <= min_stock_level * 3');
                    break;
                case 'high':
                    $query->where('type', 'product')->whereRaw('stock_quantity > min_stock_level * 3');
                    break;
            }
        }

        if ($request->filled('period')) {
            $now = Carbon::now();
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    break;
                case 'quarter':
                    $quarter = ceil($now->month / 3);
                    $startMonth = (($quarter - 1) * 3) + 1;
                    $endMonth = $quarter * 3;
                    $query->whereBetween('created_at', [
                        Carbon::create($now->year, $startMonth, 1)->startOfMonth(),
                        Carbon::create($now->year, $endMonth, 1)->endOfMonth()
                    ]);
                    break;
                case 'year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->get();
        $reportStats = $this->calculateReportStats($products);
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('products.report', compact('products', 'categories', 'reportStats'));
    }

    /**
     * Calculate statistics for the product report.
     */
    private function calculateReportStats($products)
    {
        $totalProducts = $products->where('type', 'product')->count();
        $totalServices = $products->where('type', 'service')->count();

        $totalValue = $products->where('type', 'product')->sum(function ($product) {
            return $product->selling_price * $product->stock_quantity;
        });

        $lowStockCount = $products->where('type', 'product')->filter(function ($product) {
            return $product->stock_quantity <= $product->min_stock_level;
        })->count();

        $byCategory = $products->groupBy(function ($product) {
            return $product->category ? $product->category->name : 'Sem categoria';
        })->map->count();

        $stockAnalysis = [
            'low' => $products->where('type', 'product')->filter(function ($product) {
                return $product->stock_quantity <= $product->min_stock_level;
            })->count(),
            'normal' => $products->where('type', 'product')->filter(function ($product) {
                return $product->stock_quantity > $product->min_stock_level &&
                       $product->stock_quantity <= $product->min_stock_level * 3;
            })->count(),
            'high' => $products->where('type', 'product')->filter(function ($product) {
                return $product->stock_quantity > $product->min_stock_level * 3;
            })->count(),
        ];

        return [
            'total_products' => $totalProducts,
            'total_services' => $totalServices,
            'total_value' => $totalValue,
            'low_stock_count' => $lowStockCount,
            'active_categories' => Category::where('status', 'active')->count(),
            'inactive_products' => $products->where('is_active', false)->count(),
            'by_category' => $byCategory,
            'stock_analysis' => $stockAnalysis,
            'top_by_price' => $products->sortByDesc('selling_price'),
            'top_by_stock' => $products->where('type', 'product')->sortByDesc('stock_quantity'),
        ];
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $term = $request->get('q');

        if (empty($term)) {
            return redirect()->route('products.index');
        }

        $products = Product::with('category')
            ->where('name', 'like', '%' . $term . '%')
            ->orWhere('description', 'like', '%' . $term . '%')
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $allProducts = Product::all();
        $lowStockCount = Product::where('type', 'product')
                                ->whereRaw('stock_quantity <= min_stock_level')
                                ->where('is_active', true)
                                ->count();

        return view('products.index', compact('products', 'categories', 'allProducts', 'lowStockCount'))
            ->with('searchTerm', $term);
    }

    /**
     * Duplicate a product.
     */
    public function duplicate(Product $product)
    {
        try {
            DB::beginTransaction();

            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Cópia)';
            $newProduct->stock_quantity = 0;
            $newProduct->is_active = false;
            $newProduct->save();

            DB::commit();

            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->info('products.edit', 'Produto duplicado com sucesso! Ajuste os dados conforme necessário.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao duplicar produto: ' . $e->getMessage());
            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->error('products.index', 'Erro ao duplicar produto.');
        }
    }

    /**
     * Bulk toggle product status.
     */
    public function bulkToggle(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'action' => 'required|in:activate,deactivate'
        ]);

        try {
            $productIds = $request->products;
            $isActive = $request->action === 'activate';

            Product::whereIn('id', $productIds)->update([
                'is_active' => $isActive
            ]);

            $count = count($productIds);
            $action = $isActive ? 'ativados' : 'desativados';

            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->success('products.index', "{$count} produtos foram {$action} com sucesso!");

        } catch (\Exception $e) {
            Log::error('Erro em operação em lote: ' . $e->getMessage());
            // ✅ USANDO O MÉTODO DO BASECONTROLLER
            return $this->error('products.index', 'Erro na operação em lote.');
        }
    }
}