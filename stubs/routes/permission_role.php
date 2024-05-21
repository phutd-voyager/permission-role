<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['permission-role:admin'])->get('/permission-role/admin', [\App\Http\Controllers\PermissionRoleController::class, 'admin']);
Route::middleware(['permission-role:user'])->get('/permission-role/user', [\App\Http\Controllers\PermissionRoleController::class, 'user']);
Route::get('/permission-role/everyone', [\App\Http\Controllers\PermissionRoleController::class, 'everyone']);