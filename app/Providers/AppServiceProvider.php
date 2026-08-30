<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\PermissionHelper;
//event
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;


class AppServiceProvider extends ServiceProvider
{
   /**
     * Register any application services.
     */
    public function register(): void
    {
        // Certifique-se de que o ficheiro de helpers de permissão é carregado
        if (file_exists(app_path('Helpers/permissions.php'))) {
            require_once app_path('Helpers/permissions.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerViewComposers();
         Event::listen('search.no_results', function ($query) {
        Log::info('No search results', ['query' => $query]);
    });
    }

    /**
     * Registrar Blade directives customizadas
     */
    private function registerBladeDirectives(): void
    {
        Blade::directive('userCan', function ($permission) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::userCan($permission)): ?>";
        });

        Blade::directive('enduserCan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('admin', function () {
            return "<?php if(\\App\\Helpers\\PermissionHelper::isAdmin()): ?>";
        });

        Blade::directive('endadmin', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('manager', function () {
            return "<?php if(\\App\\Helpers\\PermissionHelper::isManager()): ?>";
        });

        Blade::directive('endmanager', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('staff', function () {
            return "<?php if(\\App\\Helpers\\PermissionHelper::isStaff()): ?>";
        });

        Blade::directive('endstaff', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('role', function ($role) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::getUserLevel() === $role): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('showMenuItem', function ($menuItem) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::shouldShowMenuItem($menuItem)): ?>";
        });

        Blade::directive('endshowMenuItem', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canEdit', function ($resource) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canEdit($resource)): ?>";
        });

        Blade::directive('endcanEdit', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canDelete', function ($resource) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canDelete($resource)): ?>";
        });

        Blade::directive('endcanDelete', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Registrar view composers
     */
    private function registerViewComposers(): void
    {
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'currentUser' => PermissionHelper::getCurrentUserInfo(),
                ]);
            }
        });

        view()->composer(['layouts.app', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                $menuItems = [];
                $configMenuItems = config('auth_permissions.menu_items', []);

                foreach ($configMenuItems as $key => $config) {
                    if (PermissionHelper::shouldShowMenuItem($key)) {
                        $menuItems[$key] = $config;
                    }
                }

                $view->with('menuItems', $menuItems);
            }
        });
    }
}
 
   
