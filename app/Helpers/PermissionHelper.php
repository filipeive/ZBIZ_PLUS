<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Services\PermissionService;

class PermissionHelper
{
    protected static function getAuthenticatedUser()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        if (property_exists($user, 'is_active') && !$user->is_active) {
            return null;
        }

        return $user;
    }

    public static function hasRole(string $roleName): bool
    {
        $user = self::getAuthenticatedUser();
        return $user && $user->role 
            ? strtolower($user->role->name) === strtolower($roleName) 
            : false;
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isManager(): bool
    {
        return self::hasRole('manager');
    }

    public static function isStaff(): bool
    {
        return self::hasRole('staff');
    }

    public static function getUserLevel(): string
    {
        $user = self::getAuthenticatedUser();
        return $user && $user->role ? $user->role->name : 'guest';
    }

    public static function checkPermissionFallback($role, string $permission): bool
    {
        $permissions = [
            'admin'   => ['manage_users', 'manage_products', 'view_reports'],
            'manager' => ['manage_products', 'view_reports'],
            'staff'   => ['view_reports'],
        ];

        $roleName = is_object($role) ? $role->name : $role;
        return in_array($permission, $permissions[$roleName] ?? []);
    }

    public static function userCan(string $permission): bool
    {
        $user = self::getAuthenticatedUser();
        if (!$user) return false;

        if (self::isAdmin()) return true;

        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($permission);
        }

        return self::checkPermissionFallback($user->role?->name, $permission);
    }

    public static function userCanAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::userCan($permission)) {
                return false;
            }
        }
        return true;
    }

    public static function userCanAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::userCan($permission)) {
                return true;
            }
        }
        return false;
    }

    public static function getAllPermissions(): array
    {
        $user = self::getAuthenticatedUser();
        if (!$user) return [];

        if (self::isAdmin()) {
            return array_keys(config('auth_permissions.all_permissions', []));
        }

        if (method_exists($user, 'getRolePermissions')) {
            return $user->getRolePermissions();
        }

        $rolePermissions = config('auth_permissions.role_permissions', []);
        return $rolePermissions[$user->role?->name] ?? [];
    }

    public static function canEdit($resource): bool
    {
        $user = self::getAuthenticatedUser();
        if (!$user) return false;

        if (self::isAdmin() || self::isManager()) {
            return true;
        }

        return isset($resource->user_id) && $resource->user_id === $user->id;
    }

    public static function canView($resource): bool
    {
        if (self::isAdmin() || self::isManager()) return true;
        return self::canEdit($resource);
    }

    public static function canDelete($resource): bool
    {
        if (self::isAdmin()) return true;

        if (self::isManager()) {
            $className = strtolower(class_basename($resource));
            return self::userCan('delete_' . $className);
        }

        return false;
    }

    public static function getRoleDisplay(): string
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return 'Visitante';
        }

        $roles = [
            'admin'   => 'Administrador',
            'manager' => 'Gerente',
            'staff'   => 'Funcionário',
        ];

        return $roles[$user->role?->name] ?? ucfirst($user->role?->name ?? 'Sem função');
    }

    public static function shouldShowMenuItem(string $menuItem): bool
    {
        $menuConfig = config('auth_permissions.menu_items.' . $menuItem, []);

        if (self::isAdmin()) return true;

        if (empty($menuConfig)) {
            return self::getAuthenticatedUser() !== null;
        }

        if (isset($menuConfig['permission'])) {
            return self::userCan($menuConfig['permission']);
        }

        return false;
    }

    public static function getCurrentUserInfo(): array
    {
        $user = self::getAuthenticatedUser();
        if (!$user) {
            return [
                'name' => 'Visitante',
                'role' => 'guest',
                'role_display' => 'Visitante',
            ];
        }

        return [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'role'         => $user->role?->name,
            'role_display' => self::getRoleDisplay(),
            'initials'     => method_exists($user, 'getInitialsAttribute')
                                ? $user->initials
                                : strtoupper(substr($user->name, 0, 2)),
        ];
    }

    public static function getProductPermissions()
    {
        return [
            'view_products'   => 'Visualizar produtos e serviços',
            'create_products' => 'Criar novos produtos e serviços',
            'edit_products'   => 'Editar produtos e serviços',
            'delete_products' => 'Excluir produtos e serviços',
            'adjust_stock'    => 'Ajustar estoque de produtos',
            'view_categories' => 'Visualizar categorias',
            'create_categories' => 'Criar novas categorias',
            'edit_categories'   => 'Editar categorias',
            'delete_categories' => 'Excluir categorias',
        ];
    }
}
