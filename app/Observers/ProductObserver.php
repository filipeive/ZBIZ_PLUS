<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Notification;
use App\Models\User;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Verificar se houve alteração no estoque e se ficou baixo
        if ($product->isDirty('stock_quantity')) {
            $oldStock = $product->getOriginal('stock_quantity');
            $newStock = $product->stock_quantity;
            $minStock = $product->min_stock_level ?? 5;

            // Se o estoque acabou de ficar baixo ou esgotou
            if ($oldStock > $minStock && $newStock <= $minStock) {
                $this->notifyAdminsAndManagers(
                    'Estoque Baixo Detectado',
                    "O produto '{$product->name}' atingiu o estoque mínimo ({$newStock} unidades).",
                    'warning',
                    'fas fa-box-open',
                    route('products.show', $product->id)
                );
            }
            
            // Se o estoque esgotou totalmente
            if ($oldStock > 0 && $newStock <= 0) {
                $this->notifyAdminsAndManagers(
                    'Produto Esgotado',
                    "O produto '{$product->name}' está sem estoque!",
                    'error',
                    'fas fa-exclamation-circle',
                    route('products.show', $product->id)
                );
            }
        }
    }

    private function notifyAdminsAndManagers(string $title, string $message, string $type, string $icon, string $url): void
    {
        // Encontrar admins e gerentes
        $users = User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'manager', 'super_admin']);
        })->where('is_active', true)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type, // success, warning, error, info
                'icon' => $icon,
                'action_url' => $url,
                'read' => false
            ]);
        }
    }
}
