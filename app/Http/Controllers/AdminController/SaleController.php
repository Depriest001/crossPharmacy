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
    public function index()
    {
        return view('admin.sale.index');
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)
        ->where('status', 'active')
        ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'name' => $product->product_name,
            'barcode' => $product->barcode,
            'price' => $product->price,
            'quantity' => $product->quantity,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'cart_data' => 'required|string',
        ]);

        $cartItems = json_decode($data['cart_data'], true);

        if (empty($cartItems)) {
            return back()->with('error', 'Cart cannot be empty.');
        }

        try {
            DB::beginTransaction();

            // 1️⃣ Create Sale record
            $sale = Sale::create([
                'staff_id' => auth()->id(),
                'subtotal' => $data['subtotal'],
                'discount' => $data['discount'] ?? 0,
                'grand_total' => $data['grand_total'],
                'payment_method' => $data['payment_method'],
            ]);

            // 2️⃣ Loop through cart items
            foreach ($cartItems as $item) {
                $product = Product::where('barcode', $item['barcode'])->first();

                if (!$product) continue;

                // Ensure stock is available
                if ($product->quantity < $item['qty']) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient stock for {$product->product_name}");
                }

                // Reduce stock
                $product->decrement('quantity', $item['qty']);

                // Save sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            // 3️⃣ Redirect to print receipt
            return redirect()->route('admin.sale.receipt', ['sale' => $sale->id])
                            ->with('success', 'Sale completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while completing the sale: ' . $e->getMessage());
        }
    }

    public function printReceipt($saleId)
    {
        $systemInfo = SystemInfo::first();
        $sale = Sale::with(['items.product', 'staff'])->findOrFail($saleId);

        return view('admin.sale.receipt', compact('sale','systemInfo'));
    }

    // public function salereport(Request $request)
    // {
    //     $role = auth('staff')->user()->role->role_type ?? '';

    //     $query = Sale::with(['staff', 'items' => function($q) {
    //         $q->select('sale_id', DB::raw('SUM(quantity) as items_sum_quantity'))
    //         ->groupBy('sale_id');
    //     }]);

    //     if ($request->filled('start_date')) {
    //         $query->whereDate('created_at', '>=', $request->start_date);
    //     }

    //     if ($request->filled('end_date')) {
    //         $query->whereDate('created_at', '<=', $request->end_date);
    //     }

    //     $sales = $query->latest()->get();

    //     return view('admin.sale.report', compact('sales','role'));
    // }

    public function salereport(Request $request)
    {
        // Get logged-in staff and role
        $staff = auth('staff')->user();
        $role = $staff->role->role_type ?? '';

        // Base query with relationships
        $query = Sale::with([
            'staff.branch',
            'items' => function ($q) {
                $q->select('sale_id', DB::raw('SUM(quantity) as items_sum_quantity'))
                ->groupBy('sale_id');
            }
        ]);

        // 🔒 Restrict data for non-admin users
        if ($role !== 'Admin') {
            $query->whereHas('staff', function ($q) use ($staff) {
                $q->where('branch_id', $staff->branch_id);
            });
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
