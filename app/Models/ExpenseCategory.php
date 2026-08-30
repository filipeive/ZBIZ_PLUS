<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_operational',
        'is_rent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_operational' => 'boolean',
        'is_rent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeOperational($query)
    {
        return $query->where('is_operational', true);
    }

    public function scopeRent($query)
    {
        return $query->where('is_rent', true);
    }

    /**
     * Relacionamento: Uma categoria possui muitas despesas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Escopo: Categorias que possuem despesas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->has('expenses');
    }

    /**
     * Escopo: Categorias sem despesas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmpty($query)
    {
        return $query->doesntHave('expenses');
    }

    /**
     * Acessor: Nome formatado com primeira letra maiúscula.
     *
     * @return string
     */
    public function getFormattedNameAttribute()
    {
        return ucfirst($this->name);
    }

    /**
     * Acessor: Total de despesas nesta categoria.
     *
     * @return float
     */
    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Verificar se a categoria pode ser excluída.
     *
     * @return bool
     */
    public function canBeDeleted()
    {
        return $this->expenses()->count() === 0;
    }

    /**
     * Boot do modelo.
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de deletar, verificar se há despesas
        static::deleting(function ($category) {
            if ($category->expenses()->exists()) {
                throw new \Exception('Não é possível excluir uma categoria com despesas associadas.');
            }
        });
    }
}
