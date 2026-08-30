<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'customer_id',
        'patient_name',
        'patient_nuit',
        'prescriber_name',
        'prescriber_license',
        'health_facility',
        'prescription_date',
        'dispensed_at',
        'notes',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'dispensed_at'      => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
