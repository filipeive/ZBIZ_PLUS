<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $roles = \App\Models\Role::all(); // Carregar todas as funções disponíveis

        return view('profile.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        // Verificar se o usuário pode alterar a própria função
        if (!$user->isAdmin() && $validated['role_id'] != $user->role_id) {
            return $this->error('profile.edit', 'Você não tem permissão para alterar sua função.');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $this->success('profile.edit', 'Perfil atualizado com sucesso!');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Deletar foto antiga se existir
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        // Salvar nova foto
        $photoPath = $request->file('photo')->store('user-photos', 'public');
        $user->update(['photo_path' => $photoPath]);

        return $this->success('profile.edit', 'Foto de perfil atualizada com sucesso!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success('profile.edit', 'Senha atualizada com sucesso!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = $request->user();

        // Evitar exclusão de admin (opcional)
        if ($user->hasRole('admin')) {
            return $this->error('profile.edit', 'Não é possível excluir contas de administrador.');
        }

        // Deletar foto de perfil se existir
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $userName = $user->name;
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success('/', "Conta de {$userName} excluída com sucesso!");
    }

    /**
     * Show user statistics and dashboard
     */
    public function stats()
    {
        $user = auth()->user();

        $stats = [
            'total_sales' => $user->sales()->count(),
            'total_sales_value' => $user->sales()->sum('total_amount'),
            'sales_this_month' => $user->sales()
                ->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total_amount'),
            'sales_today' => $user->sales()
                ->whereDate('sale_date', today())
                ->sum('total_amount'),
            'total_expenses' => $user->expenses()->count(),
            'expenses_this_month' => $user->expenses()
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'products_created' => $user->products()->count(),
        ];

        // Atividades recentes
        $activities = collect();

        // Vendas recentes
        $recentSales = $user->sales()
            ->with('items.product')
            ->orderBy('sale_date', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentSales as $sale) {
            $activities->push([
                'type' => 'sale',
                'date' => $sale->sale_date,
                'title' => 'Venda Registrada',
                'description' => "Venda de {$sale->items->count()} item(s) - MT " . number_format($sale->total_amount, 2, ',', '.'),
                'icon' => 'fas fa-shopping-cart',
                'color' => 'success'
            ]);
        }

        // Despesas recentes
        $recentExpenses = $user->expenses()
            ->orderBy('expense_date', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentExpenses as $expense) {
            $activities->push([
                'type' => 'expense',
                'date' => $expense->expense_date,
                'title' => 'Despesa Registrada',
                'description' => "{$expense->description} - MT " . number_format($expense->amount, 2, ',', '.'),
                'icon' => 'fas fa-receipt',
                'color' => 'danger'
            ]);
        }

        $activities = $activities->sortByDesc('date')->take(10);

        return view('profile.stats', compact('user', 'stats', 'activities'));
    }

    /**
     * Show user profile with basic info
     */
    public function show()
    {
        $user = auth()->user();

        return view('profile.show', compact('user'));
    }

    /**
     * Performance metrics (last 6 months)
     */
    public function performance()
    {
        $user = auth()->user();

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthData = [
                'month' => $date->format('M Y'),
                'sales' => $user->sales()
                    ->whereMonth('sale_date', $date->month)
                    ->whereYear('sale_date', $date->year)
                    ->sum('total_amount'),
                'expenses' => $user->expenses()
                    ->whereMonth('expense_date', $date->month)
                    ->whereYear('expense_date', $date->year)
                    ->sum('amount'),
            ];
            $months->push($monthData);
        }

        return view('profile.performance', compact('user', 'months'));
    }
}