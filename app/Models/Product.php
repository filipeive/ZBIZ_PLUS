<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'category_id', 'linked_product_id', 'name', 'description', 'type', 
        'purchase_price', 'selling_price', 'stock_quantity',
        'min_stock_level', 'unit', 'is_active',
        'deleted_at','original_name',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function linkedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'linked_product_id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Update stock, potentially delegating to a linked product.
     */
    public function updateStock(int $quantity, string $type = 'out', ?int $userId = null, string $reason = 'Ajuste', ?int $referenceId = null): void
    {
        // Se este produto tem um produto vinculado, o stock deve ser reduzido do vinculado
        if ($this->linked_product_id) {
            $targetProduct = $this->linkedProduct;
            if ($targetProduct) {
                $targetProduct->updateStock($quantity, $type, $userId, "$reason (via {$this->name})", $referenceId);
                return;
            }
        }

        // Caso contrário, atualiza o próprio stock (se for do tipo produto)
        if ($this->type === 'product') {
            if ($type === 'out') {
                $this->decrement('stock_quantity', $quantity);
            } else {
                $this->increment('stock_quantity', $quantity);
            }

            // Registrar movimentação de stock
            StockMovement::create([
                'product_id' => $this->id,
                'user_id' => $userId ?? auth()->id(),
                'movement_type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'reference_id' => $referenceId,
                'movement_date' => now()->toDateString(),
            ]);
        }
    }
    // Accessor para exibir o nome com marcação de exclusão
    public function getNameAttribute($value)
    {
        if ($this->is_deleted) {
            return $value . ' 🚫 (EXCLUÍDO)';
        }
        return $value;
    }
       // Mutator para salvar o nome original ao excluir
    public function markAsDeleted()
    {
        if (!$this->is_deleted) {
            $this->original_name = $this->name;
            $this->name = $this->name . ' (EXCLUÍDO)';
            $this->is_deleted = true;
            $this->deleted_at = now();
            $this->is_active = false;
            $this->save();
        }
    }
}
