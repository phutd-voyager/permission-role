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
        $this->publishes([
            __DIR__ . '/config/permission-role.php' => config_path('permission-role.php'),
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'permission-role');

        $this->registerMiddleware();

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallExampleCommand::class,
        ]);
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
