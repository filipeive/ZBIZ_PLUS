<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'debt_type',
        'user_id',
        'employee_id',
        'customer_name',
        'customer_phone',
        'customer_document',
        'employee_name',
        'employee_phone',
        'employee_document',
        'original_amount',
        'remaining_amount',
        'debt_date',
        'due_date',
        'status',
        'description',
        'notes',
        'sale_id',
        'generated_sale_id'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'debt_date' => 'date',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'debt_type' => 'product',
        'status' => 'active'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DebtItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function generatedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'generated_sale_id');
    }

    public function hasItems()
    {
        return $this->items && $this->items->count() > 0;
    }
    // Scopes
    public function scopeProductDebts($query)
    {
        return $query->where('debt_type', 'product');
    }

    public function scopeMoneyDebts($query)
    {
        return $query->where('debt_type', 'money');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
                    ->where('due_date', '<', now()->toDateString());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Accessors
    public function getDebtorNameAttribute(): string
    {
        return $this->debt_type === 'money' ? $this->employee_name : $this->customer_name;
    }

    public function getDebtorPhoneAttribute(): ?string
    {
        return $this->debt_type === 'money' ? $this->employee_phone : $this->customer_phone;
    }

    public function getDebtorDocumentAttribute(): ?string
    {
        return $this->debt_type === 'money' ? $this->employee_document : $this->customer_document;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }

    public function getAmountPaidAttribute(): float
    {
        return $this->original_amount - $this->remaining_amount;
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->original_amount <= 0) {
            return 0;
        }
        
        return ($this->amount_paid / $this->original_amount) * 100;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => $this->is_overdue ? 'bg-danger' : 'bg-warning',
            'paid' => 'bg-success',
            'cancelled' => 'bg-secondary',
            'overdue' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->status === 'active' && $this->is_overdue) {
            return 'Vencida';
        }
        
        return match ($this->status) {
            'active' => 'Ativa',
            'paid' => 'Paga',
            'cancelled' => 'Cancelada',
            'overdue' => 'Vencida',
            default => ucfirst($this->status),
        };
    }

    public function getDebtTypeTextAttribute(): string
    {
        return match ($this->debt_type) {
            'product' => 'Produtos',
            'money' => 'Dinheiro',
            default => 'Indefinido'
        };
    }

    public function getDebtTypeIconAttribute(): string
    {
        return match ($this->debt_type) {
            'product' => 'fa-shopping-cart',
            'money' => 'fa-money-bill-wave',
            default => 'fa-question-circle'
        };
    }

    // Métodos
    public function isProductDebt(): bool
    {
        return $this->debt_type === 'product';
    }

    public function isMoneyDebt(): bool
    {
        return $this->debt_type === 'money';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['active']);
    }

    public function canReceivePayment(): bool
    {
        return $this->status === 'active' && $this->remaining_amount > 0;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['active']);
    }

    public function canBeMarkedAsPaid(): bool
    {
        return $this->status === 'active' && $this->remaining_amount > 0;
    }

    /* public function hasItems(): bool
    {
        return $this->items()->exists();
    } */

    public function hasPayments(): bool
    {
        return $this->payments()->exists();
    }

    public function getTotalItemsQuantity(): int
    {
        return $this->items()->sum('quantity');
    }

    public function getFormattedOriginalAmountAttribute(): string
    {
        return 'MT ' . number_format($this->original_amount, 2, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'MT ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return 'MT ' . number_format($this->amount_paid, 2, ',', '.');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($debt) {
            // Auto-definir data de vencimento se não fornecida
            if (!$debt->due_date && $debt->debt_date) {
                $debt->due_date = $debt->debt_date->copy()->addDays(30);
            }
            
            // Garantir que remaining_amount não seja negativo
            if ($debt->remaining_amount < 0) {
                $debt->remaining_amount = 0;
            }
            
            // Auto-marcar como paga se remaining_amount for zero
            if ($debt->remaining_amount <= 0 && $debt->status === 'active') {
                $debt->status = 'paid';
            }
        });

        static::updating(function ($debt) {
            // Se status mudou para 'paid', definir remaining_amount como 0
            if ($debt->status === 'paid') {
                $debt->remaining_amount = 0;
            }
        });
    }
}