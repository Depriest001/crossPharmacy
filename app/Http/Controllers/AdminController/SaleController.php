<?php

namespace App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;

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
        $sale = Sale::with(['items.product', 'staff'])->findOrFail($saleId);

        return view('admin.sale.receipt', compact('sale'));
    }

}
