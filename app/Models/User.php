<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Role;
use App\Models\Notification;
use App\Models\SalaryPayment;
use App\Models\UserActivity;
use App\Models\TemporaryPassword;
use App\Services\PermissionService;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_code',
        'email',
        'phone',
        'monthly_salary',
        'hire_date',
        'job_title',
        'document_number',
        'password',
        'role_id',
        'is_active',
        'photo_path', 
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'monthly_salary' => 'decimal:2',
        'hire_date' => 'date',
    ];

    // ===== RELACIONAMENTOS =====
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('read', false);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class)->latest();
    }

    public function temporaryPasswords(): HasMany
    {
        return $this->hasMany(TemporaryPassword::class)->latest();
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class)->latest('payment_date');
    }

    public function activeTemporaryPasswords(): HasMany
    {
        return $this->temporaryPasswords()->active();
    }

    // ===== ACCESSORS =====

    protected function lastLoginDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->last_login_at 
                ? $this->last_login_at->diffForHumans() 
                : 'Nunca fez login'
        );
    }

    protected function lastLoginFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->last_login_at 
                ? $this->last_login_at->format('d/m/Y H:i') 
                : 'Nunca'
        );
    }

    public function getRoleDisplayAttribute(): string
    {
        $roles = [
            'admin'   => 'Administrador',
            'manager' => 'Gerente',
            'staff'   => 'Funcionário',
        ];

        return $roles[$this->role?->name] ?? ucfirst($this->role?->name ?? 'Sem função');
    }

    public function getStatusDisplayAttribute(): string
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    public function getFormattedMonthlySalaryAttribute(): string
    {
        return 'MT ' . number_format((float) ($this->monthly_salary ?? 0), 2, ',', '.');
    }

    public function getEmployeeLabelAttribute(): string
    {
        return $this->employee_code
            ? "{$this->employee_code} - {$this->name}"
            : $this->name;
    }

    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        return strtoupper(collect($names)->take(2)->map(fn($n) => substr($n,0,1))->join(''));
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0] ?? '';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->photo_path
            ? Storage::url($this->photo_path)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF&size=256';
    }

    // ===== MÉTODOS DE SENHA TEMPORÁRIA =====
    public function hasActiveTemporaryPassword(): bool
    {
        return $this->activeTemporaryPasswords()->exists();
    }

    public function getActiveTemporaryPassword(): ?TemporaryPassword
    {
        return $this->activeTemporaryPasswords()->first();
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
        
        // Registrar atividade de login
        UserActivity::create([
            'user_id' => $this->id,
            'action' => 'login',
            'description' => 'Usuário fez login no sistema',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // ===== MÉTODOS DE ROLE/PERMISSÃO =====
    protected function permissions(): PermissionService
    {
        return new PermissionService($this);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->hasPermission($permission);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->isSuperAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    // ===== AUTORIZAÇÃO DE RECURSOS =====
    public function canEdit($resource): bool
    {
        if ($this->isAdmin() || $this->isManager()) {
            return true;
        }

        return $resource->user_id === $this->id
            || (property_exists($resource, 'created_by') && $resource->created_by === $this->id);
    }

    public function canView($resource): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->canEdit($resource);
    }

    public function canDelete($resource): bool
    {
        // Não pode eliminar a si próprio
        if ($resource instanceof User && $this->id === $resource->id) {
            return false;
        }

        // Super Admin pode tudo
        if ($this->isSuperAdmin()) return true;

        if ($this->isAdmin()) {
            // Admin não pode eliminar outros Admins ou Super Admins
            if ($resource instanceof User && ($resource->isAdmin() || $resource->isSuperAdmin())) {
                return false;
            }
            return true;
        }

        if ($this->isManager()) return $this->hasPermission('delete_' . class_basename($resource));
        
        return false;
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', $role));
    }

    public function scopeAdmins($query)
    {
        return $query->byRole('admin');
    }

    public function scopeStaff($query)
    {
        return $query->byRole('staff');
    }
}
