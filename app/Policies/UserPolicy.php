<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->canView($model);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->canEdit($model);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->canDelete($model);
    }

    // Mapear 'canDelete' para o método do modelo para compatibilidade com UserController
    public function canDelete(User $user, User $model): bool
    {
        return $user->canDelete($model);
    }

    public function canEdit(User $user, User $model): bool
    {
        return $user->canEdit($model);
    }

    public function canView(User $user, User $model): bool
    {
        return $user->canView($model);
    }
}
