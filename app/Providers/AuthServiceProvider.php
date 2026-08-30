<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Definir Gates baseados nas permissões
        $permissions = config('auth_permissions.role_permissions');
        
        if ($permissions) {
            foreach ($permissions as $role => $rolePermissions) {
                foreach ($rolePermissions as $permission) {
                    Gate::define($permission, function (User $user) use ($permission) {
                        return $user->hasPermission($permission);
                    });
                }
            }
        }

        // Gate para verificar se user pode editar próprios recursos
        Gate::define('edit-own-resource', function (User $user, $resource) {
            return $user->isAdmin() || $user->id === $resource->user_id;
        });

        // Gate para verificar se user pode ver todos os recursos ou apenas próprios
        Gate::define('view-all-resources', function (User $user) {
            return $user->isAdmin() || $user->role === 'manager';
        });

        // Gates genéricos para recursos (mapeados para métodos do modelo User)
        Gate::define('canEdit', fn (User $user, $resource) => $user->canEdit($resource));
        Gate::define('canView', fn (User $user, $resource) => $user->canView($resource));
        Gate::define('canDelete', fn (User $user, $resource) => $user->canDelete($resource));
    }
}