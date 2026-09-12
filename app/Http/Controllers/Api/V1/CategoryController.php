<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $categories = Category::where('tenant_id', $this->tenantId($request))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $categories, 'total' => $categories->count()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id'] = $this->tenantId($request);
        $category = Category::create($data);

        return response()->json(['message' => 'Categoria criada.', 'data' => $category], 201);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        return response()->json(['data' => $category]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category->update($data);

        return response()->json(['message' => 'Categoria atualizada.', 'data' => $category]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();
        return response()->json(['message' => 'Categoria eliminada.']);
    }

    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update(['is_active' => !$category->is_active]);
        return response()->json(['message' => 'Estado alterado.', 'is_active' => $category->is_active]);
    }

    public function active(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID') ?? $request->user()?->tenant_id;
        $categories = Category::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('name')->get();
        return response()->json(['data' => $categories]);
    }
}
