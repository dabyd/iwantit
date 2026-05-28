<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            $parts = explode('-', $p->name);

            return $parts[0] ?? 'other';
        });

        return view('admin.permissions.index', compact('permissions'));
    }

    public function show(Permission $permission)
    {
        return view('admin.permissions.show', compact('permission'));
    }
}
