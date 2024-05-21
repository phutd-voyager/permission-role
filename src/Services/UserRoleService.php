<?php

namespace VoyagerInc\PermissionRole\Services;

class UserRoleService implements Contracts\UserRoleServiceInterface
{
    public function getUserRole($user)
    {
        return $user->role;
    }

    public function hasRole($role)
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $this->getUserRole(auth()->user()) === $role;
    }
}
