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

        // Base queries
        $staffQuery = Staff::where('status', 'active');
        $salesQuery = Sale::query();
        $saleItemsQuery = SaleItem::query();

        if (!$isAdmin) {
            // Restrict staff to the same branch
            $staffQuery->where('branch_id', $staff->branch_id);

            // Restrict sales to staff within the same branch
            $salesQuery->whereHas('staff', function ($q) use ($staff) {
                $q->where('branch_id', $staff->branch_id);
            });

            // Restrict sale items to sales made by staff in the same branch
            $saleItemsQuery->whereHas('sale.staff', function ($q) use ($staff) {
                $q->where('branch_id', $staff->branch_id);
            });
        }

        // Stats
        $userCount = $staffQuery->count();
        $totalProductSales = $saleItemsQuery->sum('quantity');
        $totalSales = $salesQuery->sum('grand_total');
        $productCount = Product::count();

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