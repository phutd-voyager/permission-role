# Simple block blacklist and whitelist IP

[`PHP v8.2`](https://php.net)

[`Laravel v11.x`](https://github.com/laravel/laravel)

## Installation

```bash
composer require voyager-inc/permission-role
```

- Publish provider
```bash
php artisan vendor:publish --provider="VoyagerInc\PermissionRole\ServiceProvider"
```

- Run migration
```bash
php artisan migrate
```

- Install example code if you want
```bash
php artisan permission-role:install-example
```
and now the package will generate `Controller` and `Route`
- `PermissionRoleController.php`
- `permission_role.php`

## Usage

- We have a middleware alias with name  `permission-role:<role>`
Example: `permission-role:admin` or `permission-role:user`

- We can enable/disable middleware with config `enable_middleware` in `permission_role.php` file with value is `true` to enable or `false` to disable.

- For example:
- In `web.php` or `api.php` add this line below to load `permission_role` route of package
```bash
require __DIR__.'/permission_role.php';
```

- `permission_role.php` file route with content:
```bash
Route::middleware(['permission-role:admin'])->get('/permission-role/admin', [\App\Http\Controllers\PermissionRoleController::class, 'admin']);
Route::middleware(['permission-role:user'])->get('/permission-role/user', [\App\Http\Controllers\PermissionRoleController::class, 'user']);
Route::get('/permission-role/everyone', [\App\Http\Controllers\PermissionRoleController::class, 'everyone']);
```