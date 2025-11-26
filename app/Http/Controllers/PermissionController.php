<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        return Inertia::render('Permission/Index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()->back()->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $permission = Permission::find($id);
        $permission->name = $request->name;
        $permission->save();

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}
