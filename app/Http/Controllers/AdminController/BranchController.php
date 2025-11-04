<?php

namespace App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('admin.branch.index', compact('branches'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        Branch::create($request->only('name', 'address'));

        return redirect()->back()->with('success', 'Branch created successfully!');
    }

    public function edit($id)
    {
        // Find the role by ID or fail with 404
        $branch = Branch::findOrFail($id);

        // Return edit view with the role data
        return view('admin.branch.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($request->only('name', 'address', 'status'));

       return redirect()->route('branch.index')
            ->with('success', 'Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->back()->with('success', 'Branch deleted successfully!');
    }

}
