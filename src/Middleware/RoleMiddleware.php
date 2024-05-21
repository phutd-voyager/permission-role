<?php

namespace VoyagerInc\PermissionRole\Middleware;

use Closure;
use VoyagerInc\PermissionRole\Services\Contracts\UserRoleServiceInterface;

class RoleMiddleware
{
    private $userRoleService;

    public function __construct(UserRoleServiceInterface $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function handle($request, Closure $next, $role)
    {
        if (config('permission-role.enable_middleware') === false) {
            return $next($request);
        }

        if ($this->userRoleService->hasRole($role)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
