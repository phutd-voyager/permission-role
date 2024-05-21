<?php

namespace VoyagerInc\PermissionRole\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallExampleCommand extends Command
{
    protected $name = 'permission-role:install-example';

    protected $description = 'Install the example for the permission role package';


    public function handle()
    {
        $this->install();
    }

    protected function install()
    {
        $this->info('Installing example...');

        // Controllers
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/app/Controllers', app_path('Http/Controllers'));

        // Routes
        copy(__DIR__ . '/../../stubs/routes/permission_role.php', base_path('routes/permission_role.php'));

        $this->line('');
        $this->components->info('Package scaffolding installed successfully.');
    }
}
