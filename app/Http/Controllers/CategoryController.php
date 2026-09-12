<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Exibir lista de categorias
     */
    public function index()
    {
        $categories = Category::withCount('products')
                            ->orderBy('name')
                            ->get();
        
        return view('categories.index', compact('categories'));
    }
    //creayte
    public function create()
    {
        //products
        $categories = Category::all();
        $products = Category::where('type', 'product')->get();
        $services = Category::where('type', 'service')->get();
        return view('categories.create', compact('categories', 'products', 'services'));
    }
    /**
     * Criar nova categoria
     */
    
    public function store(Request $request)
    {
        try {
            $request->merge([
                'color' => $request->input('color', '#10b981'),
                'icon' => $request->input('icon', 'fa-tag'),
                'type' => $request->input('type', 'product'),
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($request->input('status') !== 'inactive'),
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'type' => 'nullable|in:product,service',
                'color' => 'nullable|string|max:7',
                'icon' => 'nullable|string|max:100',
                'is_active' => 'nullable|boolean',
            ]);

            Category::create([
                'tenant_id' => current_tenant_id(),
                'branch_id' => current_branch_id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'] ?? 'product',
                'color' => $validated['color'] ?? '#10b981',
                'icon' => $validated['icon'] ?? 'fa-tag',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria criada com sucesso!'
                ]);
            }

            return redirect()->back()->with('success', 'Categoria criada com sucesso!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar categoria.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Erro interno do servidor: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar categoria existente
     */
    public function update(Request $request, $id){
        try {
            $category = Category::findOrFail($id);

            $request->merge([
                'color' => $request->input('color', $category->color ?? '#10b981'),
                'icon' => $request->input('icon', $category->icon ?? 'fa-tag'),
                'type' => $request->input('type', $category->type ?? 'product'),
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($request->input('status') !== 'inactive'),
            ]);

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->where(function ($query) {
                        return $query->where('tenant_id', current_tenant_id());
                    })->ignore($category->id),
                ],
                'description' => 'nullable|string|max:500',
                'type' => 'nullable|in:product,service',
                'color' => 'nullable|string|max:7',
                'icon' => 'nullable|string|max:100',
                'is_active' => 'nullable|boolean',
            ]);

            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'] ?? $category->type ?? 'product',
                'color' => $validated['color'] ?? $category->color ?? '#10b981',
                'icon' => $validated['icon'] ?? $category->icon ?? 'fa-tag',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria atualizada com sucesso!'
                ]);
            }

            return redirect()->back()->with('success', 'Categoria atualizada com sucesso!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Categoria não encontrada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar categoria.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Erro interno do servidor.');
        }
    }

    /**
     * Excluir categoria
     */
    
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            if ($category->products()->count() > 0) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não é possível excluir esta categoria pois ela possui produtos associados.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Não é possível excluir esta categoria pois ela possui produtos associados.');
            }

            $category->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria excluída com sucesso!'
                ]);
            }

            return redirect()->back()->with('success', 'Categoria excluída com sucesso!');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria não encontrada.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Categoria não encontrada.');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir categoria.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Erro ao excluir categoria.');
        }
    }

    /**
     * Obter categoria específica (para AJAX)
     */
    public function show($id)
    {
        try {
            $category = Category::withCount('products')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }
    }
    /**
     * Alternar status da categoria
     */
    public function toggleStatus($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = $category->is_active ? 'inactive' : 'active';
            $category->save();

            return redirect()->back()
                           ->with('success', 'Status da categoria alterado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erro ao alterar status da categoria.');
        }
    }
}