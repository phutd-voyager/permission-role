<?php

namespace VoyagerInc\PermissionRole\Middleware;

use Closure;
use VoyagerInc\PermissionRole\Services\Contracts\UserRoleServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use VoyagerInc\PermissionRole\Services\Contracts\ConfigDataServiceInterface;

class RoleMiddleware
{
    private $userRoleService;
    private $configDataService;

    public function __construct(UserRoleServiceInterface $userRoleService, ConfigDataServiceInterface $configDataService)
    {
        $this->userRoleService = $userRoleService;
        $this->configDataService = $configDataService;
    }

    public function handle($request, Closure $next, $role)
    {
        if ($this->configDataService->get('enable_middleware') === false) {
            return $next($request);
        }

        if (empty($role)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        if ($this->userRoleService->hasRole($role)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
    }
}
