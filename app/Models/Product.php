<?php
namespace App\Models;

use App\Traits\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant;
    use SoftDeletes; 

    protected $dates = ['deleted_at']; 

    protected $fillable = [
        'tenant_id', 'branch_id',
        'category_id', 'linked_product_id', 'barcode', 'sku', 'name', 'description', 'type', 
        'purchase_price', 'selling_price', 'promotional_price', 'is_on_promotion', 'promotion_discount_percent', 'promotion_ends_at',
        'stock_quantity', 'min_stock_level', 'unit', 'is_active',
        'deleted_at', 'original_name',
    ];

    protected $casts = [
        'purchase_price'              => 'decimal:2',
        'selling_price'               => 'decimal:2',
        'promotional_price'           => 'decimal:2',
        'is_on_promotion'             => 'boolean',
        'promotion_discount_percent'  => 'decimal:2',
        'promotion_ends_at'           => 'datetime',
        'is_active'                   => 'boolean',
    ];

    /**
     * Verifica se o produto está atualmente com promoção ativa.
     */
    public function isOnPromotion(): bool
    {
        if (!$this->is_on_promotion) {
            return false;
        }

        if ($this->promotion_ends_at && $this->promotion_ends_at->isPast()) {
            return false;
        }

        return ($this->promotional_price !== null && $this->promotional_price < $this->selling_price)
            || ($this->promotion_discount_percent !== null && $this->promotion_discount_percent > 0);
    }

    /**
     * Retorna o preço de venda efetivo (com promoção se ativa).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->isOnPromotion()) {
            if ($this->promotional_price !== null && $this->promotional_price > 0) {
                return (float)$this->promotional_price;
            }
            if ($this->promotion_discount_percent !== null && $this->promotion_discount_percent > 0) {
                $discount = (float)$this->selling_price * ((float)$this->promotion_discount_percent / 100);
                return max(0, (float)$this->selling_price - $discount);
            }
        }
        return (float)$this->selling_price;
    }

    /**
     * Retorna o valor do desconto unitário automático em MT.
     */
    public function getAutomaticUnitDiscountAttribute(): float
    {
        return max(0, (float)$this->selling_price - (float)$this->effective_price);
    }

    /**
     * Retorna a percentagem do desconto promocional.
     */
    public function getAutomaticDiscountPercentAttribute(): float
    {
        if ((float)$this->selling_price <= 0 || !$this->isOnPromotion()) {
            return 0;
        }
        return round(($this->automatic_unit_discount / (float)$this->selling_price) * 100, 1);
    }

    
    
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(ProductInsumo::class, 'parent_product_id');
    }
    public function productBranches(): HasMany
    {
        return $this->hasMany(ProductBranch::class);
    }

    public function getStockForBranch(?int $branchId = null): int
    {
        $branchId ??= current_branch_id();
        if ($branchId) {
            $pb = $this->productBranches()->where('branch_id', $branchId)->first();
            return $pb ? (int)$pb->stock_quantity : 0;
        }
        return (int)$this->stock_quantity;
    }
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

    public function isPhysical(): bool
    {
        return in_array($this->type, ['product', 'physical']);
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

        // Caso contrário, atualiza o próprio stock (se for do tipo produto físico)
        if ($this->isPhysical()) {
            if ($type === 'out') {
                $this->decrement('stock_quantity', $quantity);
            } else {
                $this->increment('stock_quantity', $quantity);
            }

            // Registrar movimentação de stock
            StockMovement::create([
                'tenant_id' => $this->tenant_id ?? current_tenant_id() ?? auth()->user()?->tenant_id,
                'branch_id' => current_branch_id() ?? auth()->user()?->branch_id ?? $this->branch_id,
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
