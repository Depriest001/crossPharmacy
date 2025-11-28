<?php

namespace App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;

class PosController extends Controller
{
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

    // Seller Page
    public function entryPage()
    {
        $user = Auth::user();

        $pendingSales = Sale::where('seller_id', $user->id)
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('admin.pos.entry', compact('pendingSales'));
    }


    // Save Seller Cart
    public function saveCart(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'cart_data' => 'required|string',
        ]);

        $cartData = json_decode($request->cart_data, true);

        $subtotal = array_sum(array_map(fn($item) => $item['total'], $cartData));
        
        if (empty($cartData)) {
            return back()->with('error', 'Cart cannot be empty.');
        }

        $sale = Sale::create([
            'seller_id' => $user->id,
            'branch_id' => $user->branch_id,
            'subtotal' => $subtotal,
            'grand_total' => $subtotal,
            'status' => 'pending',
        ]);

        foreach ($cartData as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'] ?? Product::where('barcode', $item['barcode'])->first()->id,
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        return back()->with('success', 'Cart sent to cashier.');
    }

    public function destroyPendingSale($id)
    {
        // Find the sale
        $sale = Sale::where('id', $id)
                    ->where('status', 'pending') // ensure only pending sales can be deleted
                    ->first();

        if (!$sale) {
            return back()->with('error', 'Pending sale not found or already processed.');
        }

        try {
            $sale->update([
                'status' => 'cancelled',
            ]); // will also delete related sale_items if cascade is set
            return back()->with('success', 'Pending sale cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // Display pending sales
    public function checkout()
    {
        $pendingSales = Sale::where('status', 'pending')
                            ->with('seller')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('admin.pos.checkout', compact('pendingSales'));
    }

    // Fetch sale items for a specific sale (AJAX)
    public function getSaleItems(Sale $sale)
    {
        $items = $sale->items()->with('product')->get()->map(function($item){
            return [
                'name' => $item->product->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->total,
            ];
        });

        return response()->json([
            'sale' => $sale,
            'items' => $items,
        ]);
    }

    // Complete the sale (update status, apply discount, payment method)
    public function completeSale(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,transfer',
            'grand_total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($data['sale_id']);

            $sale->update([
                'cashier_id' => $user->id,
                'discount' => $data['discount'] ?? 0,
                'grand_total' => $data['grand_total'],
                'payment_method' => $data['payment_method'],
                'status' => 'completed',
            ]);

            DB::commit();

            return redirect()->route('admin.sale.receipt', ['sale' => $sale->id])
                            ->with('success', 'Sale completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Failed to complete sale: " . $e->getMessage());
        }
    }
}
