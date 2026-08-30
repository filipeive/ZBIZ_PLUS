<?php

use App\Helpers\PermissionHelper;

    // ===== FUNÇÕES DE PERMISSÃO =====

    if (!function_exists('userCan')) {
        /**
         * Verificar se o usuário tem uma permissão específica
         */
        function userCan(string $permission): bool
        {
            return PermissionHelper::userCan($permission);
        }
    }

    if (!function_exists('userCanAll')) {
        /**
         * Verificar se o usuário tem todas as permissões
         */
        function userCanAll(array $permissions): bool
        {
            return PermissionHelper::userCanAll($permissions);
        }
    }

    if (!function_exists('userCanAny')) {
        /**
         * Verificar se o usuário tem pelo menos uma das permissões
         */
        function userCanAny(array $permissions): bool
        {
            return PermissionHelper::userCanAny($permissions);
        }
    }

    if (!function_exists('userCan')) {
        function userCan(string $permission): bool
        {
            return PermissionHelper::userCan($permission);
        }
    }

    if (!function_exists('isAdmin')) {
        function isAdmin(): bool
        {
            return PermissionHelper::isAdmin();
        }
    }

    if (!function_exists('isManager')) {
        function isManager(): bool
        {
            return PermissionHelper::isManager();
        }
    }

    if (!function_exists('isStaff')) {
        function isStaff(): bool
        {
            return PermissionHelper::isStaff();
        }
    }

    if (!function_exists('canEdit')) {
        function canEdit($resource): bool
        {
            return PermissionHelper::canEdit($resource);
        }
    }

    if (!function_exists('canView')) {
        function canView($resource): bool
        {
            return PermissionHelper::canView($resource);
        }
    }

    if (!function_exists('canDelete')) {
        function canDelete($resource): bool
        {
            return PermissionHelper::canDelete($resource);
        }
    }

    if (!function_exists('shouldShowMenuItem')) {
        function shouldShowMenuItem(string $menuItem): bool
        {
            return PermissionHelper::shouldShowMenuItem($menuItem);
        }
    }