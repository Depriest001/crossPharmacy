<?php

namespace App\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

use App\Http\Controllers\Controller;


class ProductController extends Controller
{
    public function index()
    {
        // Get only active categories
        $categories = Category::where('status', 'active')->latest()->get();

        // Get all products (you can add filters if needed)
        $products = Product::with('category')
        ->latest()
        ->get();

        // Pass both to your view
        return view('admin.product.index', compact('products', 'categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'category_id' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'required|date',
        ]);

        // Generate a unique 12-digit barcode
        $barcode = $this->generateUniqueBarcode();

        // Save product
        $product = Product::create([
            'barcode' => $barcode,
            'product_name' => $request->product_name,
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'unit' => $request->unit,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
        ]);

        // return redirect()->back()->with('success', 'Product added successfully with barcode: ' . $barcode);
        return redirect()
        ->route('product.show', $product->id)
        ->with('success', 'Product added successfully with barcode: ' . $barcode);
    }

    private function generateUniqueBarcode()
    {
        do {
            // Generate 12-digit numeric barcode
            $barcode = mt_rand(100000000000, 999999999999);
        } while (Product::where('barcode', $barcode)->exists());

        return $barcode;
    }
    
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('status', 'active')->get();

        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'brand'        => 'nullable|string|max:255',
            'unit'         => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'quantity'     => 'required|integer|min:0',
            'expiry_date'  => 'required|date',
            'status'       => 'required|in:active,inactive',
        ]);

        $product = Product::findOrFail($id);

        $product->update($validated);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('admin.product.show', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index')
                         ->with('success', 'Product deleted successfully.');
    }

}
