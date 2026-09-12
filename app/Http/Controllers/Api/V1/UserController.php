<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $users = User::where('tenant_id', $this->tenantId($request))
            ->with('role')
            ->orderBy('name')
            ->paginate($request->get('per_page', 20));

        return response()->json($users);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        return response()->json(['data' => $user->load('role')]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:8',
            'role_id'   => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'phone'     => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
        ]);

        $data['tenant_id'] = $this->tenantId($request);
        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = true;

        $user = User::create($data);

        return response()->json(['message' => 'Utilizador criado.', 'data' => $user->load('role')], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
            'role_id'   => 'nullable|exists:roles,id',
        ]);

        $user->update($data);

        return response()->json(['message' => 'Utilizador atualizado.', 'data' => $user]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'Utilizador eliminado.']);
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['message' => 'Estado alterado.', 'is_active' => $user->is_active]);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
