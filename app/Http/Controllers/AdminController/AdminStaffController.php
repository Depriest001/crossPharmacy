<?php

namespace App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Staff;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staff = auth('staff')->user();

        $staffs = Staff::with(['role', 'branch'])
            ->when($staff->role->role_type !== 'Admin', function ($query) use ($staff) {
                $query->where('branch_id', $staff->branch_id);
            })->where('status', '!=', 'deleted')
            ->latest()
            ->get();

        $roles = Role::when($staff->role->role_type !== 'Admin', function ($q) {
            $q->where('role_type', '!=', 'Admin'); // exclude Admin from dropdown
        })->get();
        $branches = Branch::where('status', 'active')->get();

        return view('admin.staffs.index', compact('staffs', 'roles', 'branches'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'password' => 'required|string|min:6', // ✅ confirm password rule
        ]);

        Staff::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'role_id' => $request->role_id,
            'branch_id' => $request->branch_id,
            'password' => $request->password,
        ]);

        return redirect()->route('staffs.index')->with('success', 'Staff added successfully!');
    }

    /**
     * Display the specified resource.
    */
    public function show($id)
    {
        $staff = Staff::with(['role', 'branch'])->findOrFail($id);
        return view('admin.staffs.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
    */
    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $roles = Role::all();
        $branches = Branch::where('status', 'active')->get();

        return view('admin.staffs.edit', compact('staff', 'roles', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:staff,email,' . $staff->id, // ignore current email
            'phone_number' => 'required|string|max:20',
            'address'      => 'required|string|max:255',
            'role_id'      => 'required|exists:roles,id',
            'branch_id'    => 'required|exists:branches,id',
            'status'       => 'required|in:active,inactive',
        ]);

        $staff->update([
            'full_name'    => $request->full_name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'address'      => $request->address,
            'role_id'      => $request->role_id,
            'branch_id'    => $request->branch_id,
            'status'       => $request->status,
        ]);

        return redirect()
            ->route('staffs.index')
            ->with('success', 'Staff details updated successfully!');
    }

    public function softdelete (Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $staff->update([
            'status' => 'deleted',
        ]);

        return redirect()
            ->route('staffs.index')
            ->with('success', 'Staff deleted successfully!');
    }

    /**
     * Remove the specified resource from storage.
    */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        if (strtolower($staff->role->name) === 'admin') {
            return back()->with('error', 'Admin accounts cannot be deleted.');
        }

        $staff->delete();

        return back()->with('success', 'Staff deleted successfully.');
    }


}
