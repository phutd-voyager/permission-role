<?php

namespace VoyagerInc\PermissionRole;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->registerServices();
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerMiddleware();
    }

    protected function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('permission-role', Middleware\RoleMiddleware::class);
    }

    protected function registerServices()
    {
        $this->app->bind(Services\Contracts\UserRoleServiceInterface::class, function () {
            return new Services\UserRoleService();
        });
    }
}
