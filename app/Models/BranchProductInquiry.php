<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class BranchProductInquiry extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'sender_branch_id',
        'recipient_branch_id',
        'product_id',
        'product_name',
        'user_id',
        'quantity',
        'message',
        'status',
        'response',
        'response_by',
        'read_at',
    ];

    protected $casts = [
        'quantity'  => 'integer',
        'read_at'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'read_at',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_RESPONDED = 'responded';
    public const STATUS_CANCELLED = 'cancelled';

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function senderBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'sender_branch_id');
    }

    public function recipientBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'recipient_branch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responseBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'response_by');
    }

    public function scopeReceivedByBranch(Builder $query, ?int $branchId): Builder
    {
        return $query->where('recipient_branch_id', $branchId);
    }

    public function scopeSentByBranch(Builder $query, ?int $branchId): Builder
    {
        return $query->where('sender_branch_id', $branchId);
    }

    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeResponded(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RESPONDED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isResponded(): bool
    {
        return $this->status === self::STATUS_RESPONDED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_RESPONDED => 'Respondida',
            self::STATUS_CANCELLED => 'Cancelada',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_RESPONDED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'info',
        };
    }
}
