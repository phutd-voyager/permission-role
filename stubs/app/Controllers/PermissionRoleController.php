<?php

namespace App\Http\Controllers;

class PermissionRoleController extends Controller
{
    public function admin()
    {
        return response()->json(['message' => 'You are an ADMIN!']);
    }

    public function user()
    {
        return response()->json(['message' => 'You are an USER!']);
    }

    public function everyone()
    {
        return response()->json(['message' => 'Welcome, everyone!']);
    }
}