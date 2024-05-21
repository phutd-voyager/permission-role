<?php

namespace VoyagerInc\PermissionRole\Services\Contracts;

interface UserRoleServiceInterface
{
    public function getUserRole($user);

    public function hasRole($role);
}