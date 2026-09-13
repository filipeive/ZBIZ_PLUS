<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    protected ?User $user;
    protected int $cacheTime = 3600; // 1 hora de cache

    public function __construct(User $user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    /**
     * Retorna o nome da role do usuário.
     */
    protected function getRoleName(): ?string
    {
        return $this->user?->role?->name;
    }

    /**
     * Retorna o mapa consolidado de permissões por role para o tenant atual.
     */
    public static function getRolePermissionsMap(?object $tenant = null): array
    {
        $defaultPermissions = config('auth_permissions.role_permissions', []);
        $tenantObj = $tenant ?? current_tenant();
        $custom = $tenantObj?->settings['role_permissions'] ?? null;

        if (is_array($custom) && !empty($custom)) {
            foreach ($custom as $role => $perms) {
                if (is_array($perms)) {
                    $defaultPermissions[$role] = array_values(array_unique($perms));
                }
            }
        }

        return $defaultPermissions;
    }

    /**
     * Obtém todas as permissões do usuário, utilizando cache.
     * Admins recebem todas as permissões do sistema.
     */
    public function getUserPermissions(): array
    {
        if (!$this->user || !$this->user->is_active) {
            return [];
        }

        // Admins têm todas as permissões
        if ($this->user->isAdmin()) {
            return array_keys(config('auth_permissions.all_permissions', []));
        }

        $roleName = $this->getRoleName();
        $tenantId = $this->user->tenant_id ?? 0;

        return Cache::remember("user_permissions_{$this->user->id}_t{$tenantId}", $this->cacheTime, function () use ($roleName) {
            $rolePermissions = self::getRolePermissionsMap($this->user->tenant);
            return $rolePermissions[$roleName] ?? [];
        });
    }

    /**
     * Verifica se o usuário tem uma permissão específica.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->user || !$this->user->is_active) {
            return false;
        }

        // Admins têm todas as permissões
        if ($this->user->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->getUserPermissions());
    }

    /**
     * Verifica se o usuário tem todas as permissões listadas.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verifica se o usuário tem pelo menos uma das permissões listadas.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retorna todas as permissões disponíveis para o menu.
     */
    public function getMenuItems(): array
    {
        $menu = config('auth_permissions.menu_items', []);

        $filtered = [];

        foreach ($menu as $key => $item) {
            $permission = $item['permission'] ?? null;
            $adminOnly = $item['admin_only'] ?? false;

            if ($adminOnly && !$this->user->isAdmin()) {
                continue;
            }

            if ($permission && !$this->hasPermission($permission)) {
                continue;
            }

            $filtered[$key] = $item;
        }

        return $filtered;
    }

    /**
     * Limpa o cache de permissões do usuário.
     */
    public function clearCache(): void
    {
        if ($this->user) {
            $tenantId = $this->user->tenant_id ?? 0;
            Cache::forget("user_permissions_{$this->user->id}_t{$tenantId}");
            Cache::forget("user_permissions_{$this->user->id}");
        }
    }
}
