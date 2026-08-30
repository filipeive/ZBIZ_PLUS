<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone', 
        'customer_email',
        'description',
        'estimated_amount',
        'advance_payment',
        'delivery_date',
        'priority',
        'status',
        'payment_status',
        'notes',
        'internal_notes'
    ];

    // Casts de atributos
    protected $casts = [
        'delivery_date' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ============================
     * RELACIONAMENTOS
     * ============================ */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function debt(): HasOne
    {
        return $this->hasOne(Debt::class, 'order_id');
    }

    /* ============================
     * ACCESSORS
     * ============================ */
    public function getRemainingAmountAttribute()
    {
        return $this->estimated_amount - $this->advance_payment;
    }

    public function getAdvancePercentageAttribute()
    {
        if ($this->estimated_amount <= 0) return 0;
        return ($this->advance_payment / $this->estimated_amount) * 100;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending'     => 'bg-warning text-dark',
            'in_progress' => 'bg-info text-white',
            'completed'   => 'bg-success text-white',
            'delivered'   => 'bg-primary text-white',
            'cancelled'   => 'bg-danger text-white'
        ];
        
        return $badges[$this->status] ?? 'bg-secondary text-white';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending'     => 'Pendente',
            'in_progress' => 'Em Andamento',
            'completed'   => 'Concluído',
            'delivered'   => 'Entregue',
            'cancelled'   => 'Cancelado'
        ];
        
        return $texts[$this->status] ?? 'Desconhecido';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low'    => 'bg-secondary text-white',
            'medium' => 'bg-primary text-white',
            'high'   => 'bg-warning text-dark',
            'urgent' => 'bg-danger text-white'
        ];
        
        return $badges[$this->priority] ?? 'bg-secondary text-white';
    }

    public function getPriorityTextAttribute()
    {
        $texts = [
            'low'    => 'Baixa',
            'medium' => 'Média',
            'high'   => 'Alta',
            'urgent' => 'Urgente'
        ];
        
        return $texts[$this->priority] ?? 'Não definida';
    }

    /* ============================
     * MÉTODOS DE VERIFICAÇÃO
     * ============================ */
    public function canBeEdited() : bool
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }

    public function isOverdue(): bool
    {
        return $this->delivery_date && 
               $this->delivery_date < now() && 
               in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeDelivered(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['completed', 'delivered', 'cancelled']);
    }

    public function canBeConvertedToSale(): bool
    {
        return in_array($this->status, ['completed', 'delivered']);
    }

    /* ============================
     * MÉTODOS DE NEGÓCIO
     * ============================ */
   
        // No modelo Order.php
    public function canCreateDebt()
    {
        return in_array($this->status, ['completed', 'delivered']) && 
            !$this->debt && 
            ($this->estimated_amount - $this->advance_payment) > 0;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function convertToSale()
    {
        // Esta função deve ser chamada dentro de um try/catch no controller
        // para garantir que transações de banco de dados e outros erros sejam capturados.
        
        // 1. Crie a venda
        $sale = new Sale();
        $sale->user_id = $this->user_id;
        $sale->customer_name = $this->customer_name;
        $sale->customer_phone = $this->customer_phone;
        $sale->total_amount = $this->estimated_amount;
        
        // Use o novo valor 'order_conversion' que agora é válido no seu ENUM
        $sale->payment_method = 'order_conversion'; 
        
        // Adicione uma nota para rastreabilidade
        $sale->notes = "Venda convertida do pedido #{$this->id}. Sinal recebido: MZN {$this->advance_payment}";
        $sale->sale_date = now();
        $sale->save();

        // 2. Crie os itens da venda a partir dos itens do pedido
        foreach ($this->items as $orderItem) {
            $saleItem = new SaleItem();
            $saleItem->sale_id = $sale->id;
            $saleItem->product_id = $orderItem->product_id;
            $saleItem->quantity = $orderItem->quantity;
            $saleItem->unit_price = $orderItem->unit_price;
            $saleItem->total_price = $orderItem->total_price;
            $saleItem->save();
            
            // Se for um produto (e não um serviço), baixe o estoque
            if ($orderItem->product && $orderItem->product->type === 'product') {
                $orderItem->product->decrement('stock_quantity', $orderItem->quantity);
            }
        }
        
        // 3. Atualize o status do pedido para indicar que foi concluído
        $this->status = 'delivered'; 
        $this->save();

        // 4. Verifique se existe uma dívida e atualize-a se necessário
        if ($this->advance_payment < $this->estimated_amount) {
            $remainingAmount = $this->estimated_amount - $this->advance_payment;
            Debt::create([
                'user_id' => $this->user_id,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'original_amount' => $remainingAmount,
                'remaining_amount' => $remainingAmount,
                'debt_date' => now()->toDateString(),
                'due_date' => $this->delivery_date ? now()->addDays(30) : now()->addDays(30),
                'description' => "Venda a crédito do pedido #{$this->id}",
                'status' => 'active',
                'notes' => 'Gerado automaticamente ao converter o pedido em venda'
            ]);
        }
        
        return $sale;
    }

    public function duplicate()
    {
        $newOrder = $this->replicate();
        $newOrder->status = 'pending';
        $newOrder->created_at = now();
        $newOrder->updated_at = now();
        $newOrder->save();

        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->order_id = $newOrder->id;
            $newItem->save();
        }

        return $newOrder;
    }

    /* ============================
     * SCOPES
     * ============================ */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('delivery_date', '<', now())
                     ->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCustomer($query, $customerName)
    {
        return $query->where('customer_name', 'like', "%{$customerName}%");
    }

    public function scopeInDateRange($query, $from, $to)
    {
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to) $query->whereDate('created_at', '<=', $to);
        return $query;
    }
}
