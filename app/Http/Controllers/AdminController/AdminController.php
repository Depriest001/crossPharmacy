<?php

namespace App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\staff;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;

class AdminController extends Controller
{
    public function admindashboard()
    {
        $staff = auth('staff')->user();
        $isAdmin = $staff->role->role_type === 'Admin';

        // Base queries depend on role
        $staffQuery = Staff::where('status', 'active');
        $salesQuery = Sale::query();
        $saleItemsQuery = SaleItem::query();

        if (!$isAdmin) {
            // Restrict to user's branch
            $staffQuery->where('branch_id', $staff->branch_id);
            $salesQuery->where('branch_id', $staff->branch_id);
            $saleItemsQuery->whereHas('sale', function ($q) use ($staff) {
                $q->where('branch_id', $staff->branch_id);
            });
        }

        // Counts
        $userCount = $staffQuery->count(); // staff count (branch-specific if not admin)
        $totalProductSales = $saleItemsQuery->sum('quantity'); // total items sold
        $totalSales = $salesQuery->sum('grand_total'); // total ₦ amount sold
        $productCount = Product::count(); // all products (can also restrict by branch if needed)

        // Chart Data
        $yearlySales = $salesQuery
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return view('admin.index', compact(
            'userCount',
            'totalSales',
            'totalProductSales',
            'productCount',
            'yearlySales'
        ));
    }

    public function adminprofile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $staff = auth('staff')->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        $staff->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $staff = auth('staff')->user();

        // Check old password
        if (!Hash::check($request->old_password, $staff->password)) {
            return back()->withErrors(['old_password' => 'Your current password is incorrect.']);
        }

        // Update password
        $staff->password = $request->new_password;
        $staff->save();

        return back()->with('success', 'Password updated successfully!');
    }

}