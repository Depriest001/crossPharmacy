<?php

namespace App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\staff;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SystemInfo;

class SaleController extends Controller
{
        public function printReceipt($saleId)
    {
        $systemInfo = SystemInfo::first();
        $sale = Sale::with(['items.product', 'staff'])->findOrFail($saleId);

        return view('admin.sale.receipt', compact('sale','systemInfo'));
    }

    public function salereport(Request $request)
    {
        // Get logged-in staff and role
        $staff = auth('staff')->user();
        $role = $staff->role->role_type ?? '';

        // Base query with relationships
        $query = Sale::with([
            'seller.branch',
            'items' => function ($q) {
                $q->select('sale_id', DB::raw('SUM(quantity) as items_sum_quantity'))
                ->groupBy('sale_id');
            }
        ]);

        // 🔒 Restrict data for non-admin users
        if ($role !== 'Admin') {
            $query->where('branch_id', $staff->branch_id);
        }

        // 📅 Apply optional date filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 🧾 Get sales sorted by latest
        $sales = $query->latest()->get();

        return view('admin.sale.report', compact('sales', 'role'));
    }

}
