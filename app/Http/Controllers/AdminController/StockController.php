<?php
namespace App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Models\Product;

class StockController extends Controller
{
    // Display all products with stock info
    public function index()
    {
        // Get all active products with their categories
        $products = Product::where('status', 'active')
            ->with('category')
            ->orderBy('quantity', 'asc')
            ->get();

        // --- Compute Stock Summary ---
        $today = now();

        // Available Stock = Sum of quantities not expired
        $availableStock = Product::where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $today);
            })
            ->sum('quantity');

        // Expired Stock = Sum of quantities expired
        $expiredStock = Product::where('status', 'active')
            ->where('expiry_date', '<=', $today)->sum('quantity');

        // Low Stock = Count of products with low quantity (≤ 10)
        $lowStockCount = Product::where('status', 'active')
            ->where('quantity', '<=', 10)->count();

        return view('admin.stock.index', compact(
            'products',
            'availableStock',
            'expiredStock',
            'lowStockCount'
        ));
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'adjust_type'  => 'required|in:add,remove',
            'quantity'     => 'required|integer|min:1',
            'expiry_date'  => 'nullable|date',
            'reason'       => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldQty = $product->quantity;

        // Calculate new quantity
        if ($request->adjust_type === 'add') {
            $newQty = $oldQty + $request->quantity;
        } else {
            $newQty = max(0, $oldQty - $request->quantity);
        }

        // Record adjustment
        StockAdjustment::create([
            'product_id'    => $product->id,
            'user_id'       => auth()->id(),
            'adjust_type'   => $request->adjust_type,
            'quantity'      => $request->quantity,
            'old_quantity'  => $oldQty,
            'new_quantity'  => $newQty,
            'reason'        => $request->reason,
        ]);

        // Update product
        $updateData = ['quantity' => $newQty];

        // ✅ Only update expiry_date when stock is added and a date was provided
        if ($request->adjust_type === 'add' && $request->filled('expiry_date')) {
            $updateData['expiry_date'] = $request->expiry_date;
        }

        $product->update($updateData);

        return back()->with('success', 'Stock adjusted successfully.');
    }
}
