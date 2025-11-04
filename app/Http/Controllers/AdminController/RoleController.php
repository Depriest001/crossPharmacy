<?php

namespace App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('admin.role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'role_type' =>  'required|string',
            'description' => 'required|string',
        ]);

        Role::create([
            'name' => $request->name,
            'role_type' => $request->role_type,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        // Find the role by ID or fail with 404
        $role = Role::findOrFail($id);

        // Return edit view with the role data
        return view('admin.role.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        // ✅ Validate incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $id,
            'role_type' =>  'required|string',
            'description' => 'required|string',
        ]);

        try {
            // ✅ Find the role
            $role = Role::findOrFail($id);

            // ✅ Update fields
            $role->name = $validatedData['name'];
            $role->role_type = $validatedData['role_type'];
            $role->description = $validatedData['description'];
            $role->save();

            // ✅ Redirect with success
            return redirect()
                ->route('role.index')
                ->with('success', 'Role updated successfully!');

        } catch (\Exception $e) {
            // ❌ Redirect back with error
            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while updating the role.']);
                
        }
    }
    
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('role.index')->with('success', 'Role deleted successfully!');
    }

}
