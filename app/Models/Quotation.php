<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'customer_id',
        'user_id',
        'quotation_number',
        'customer_name',
        'customer_nuit',
        'customer_email',
        'customer_phone',
        'customer_address',
        'date',
        'valid_until',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'tax_regime',
        'tax_exemption_reason',
        'prices_include_tax',
        'status',
        'converted_sale_id',
        'converted_at',
        'notes',
        'terms_conditions',
    ];

    protected $casts = [
        'date'               => 'date',
        'valid_until'        => 'date',
        'converted_at'       => 'datetime',
        'subtotal'           => 'decimal:2',
        'discount_amount'    => 'decimal:2',
        'tax_rate'           => 'decimal:2',
        'tax_amount'         => 'decimal:2',
        'total_amount'       => 'decimal:2',
        'prices_include_tax' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    /**
     * Gera o próximo número sequencial anual de cotação para o tenant.
     * Formato: COT-YYYY/0001
     */
    public static function generateNextNumber(int $tenantId): string
    {
        $year = date('Y');
        $prefix = "COT-{$year}/";

        $lastQuotation = self::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('quotation_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastQuotation && preg_match("/COT-{$year}\/(\d+)/", $lastQuotation->quotation_number, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Recalcular totais e impostos da cotação.
     */
    public function calculateTotals(): void
    {
        $items = $this->items()->get();

        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $total = 0.0;

        foreach ($items as $item) {
            $itemSubtotal = (float)$item->unit_price * (float)$item->quantity;
            $itemDiscount = (float)$item->discount_amount;
            $itemNet = max(0, $itemSubtotal - $itemDiscount);

            // Calcular imposto por item
            $itemTax = 0.0;
            if (!$item->is_tax_exempt && $this->tax_regime === 'normal' && (float)$item->tax_rate > 0) {
                if ($this->prices_include_tax) {
                    // Preço já inclui IVA: desdobramento
                    $taxBase = $itemNet / (1 + ((float)$item->tax_rate / 100));
                    $itemTax = $itemNet - $taxBase;
                } else {
                    // Preço líquido: soma IVA
                    $itemTax = $itemNet * ((float)$item->tax_rate / 100);
                }
            }

            $item->update([
                'tax_amount'  => round($itemTax, 2),
                'total_price' => round($this->prices_include_tax ? $itemNet : ($itemNet + $itemTax), 2),
            ]);

            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
            $totalTax += $itemTax;
        }

        $netBeforeTax = max(0, $subtotal - $totalDiscount);

        if ($this->prices_include_tax) {
            $total = $netBeforeTax;
        } else {
            $total = $netBeforeTax + $totalTax;
        }

        $this->update([
            'subtotal'        => round($subtotal, 2),
            'discount_amount' => round($totalDiscount, 2),
            'tax_amount'      => round($totalTax, 2),
            'total_amount'    => round($total, 2),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && !in_array($this->status, ['converted', 'approved']);
    }

    public function canBeConverted(): bool
    {
        return !in_array($this->status, ['converted', 'rejected']);
    }
}

