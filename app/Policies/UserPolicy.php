<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determina si el usuario puede ver cualquier modelo.
     */
    public function viewAny(User $user): bool
    {
        // Ejemplo: Solo los usuarios con el rol 'admin' pueden ver la lista de usuarios
        return $user->hasRole('Administrador');
    }

    /**
     * Determina si el usuario puede ver el modelo.
     */
    public function view(User $user, User $model): bool
    {
        // Ejemplo: Un usuario solo puede ver su propio perfil
        return $user->id === $model->id;
    }

    /**
     * Determina si el usuario puede crear modelos.
     */
    public function create(User $user): bool
    {
        // Ejemplo: Solo los usuarios con el rol 'admin' pueden crear usuarios
        return $user->hasRole('Administrador');
    }

    /**
     * Determina si el usuario puede actualizar el modelo.
     */
    public function update(User $user, User $model): bool
    {
        // Ejemplo: Un usuario solo puede actualizar su propio perfil
        return $user->id === $model->id;
    }

    /**
     * Determina si el usuario puede eliminar el modelo.
     */
    public function delete(User $user, User $model): bool
    {
        // Ejemplo: Solo los usuarios con el rol 'admin' pueden eliminar usuarios
        return $user->hasRole('Administrador');
    }

    /**
     * Determina si el usuario puede restaurar el modelo.
     */
    public function restore(User $user, User $model): bool
    {
        // Aquí podrías definir una lógica similar a la de delete si necesitas restaurar usuarios
        return false;
    }

    /**
     * Determina si el usuario puede eliminar permanentemente el modelo.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Aquí podrías definir una lógica similar a la de delete si necesitas eliminar permanentemente usuarios
        return false;
    }

    /**
     * Determina si el usuario puede cambiar su propia contraseña.
     */
    public function changePassword(User $user, User $model): bool
    {
        // Ejemplo: Un usuario solo puede cambiar su propia contraseña
        return $user->id === $model->id;
    }

    /**
     * Determina si el usuario puede cambiar el estado del modelo (activo/inactivo).
     */
    public function changeStatus(User $user, User $model): bool
    {
        // Ejemplo: Solo los usuarios con el rol 'admin' pueden cambiar el estado de los usuarios
        return $user->hasRole('Administrador');
    }
}
