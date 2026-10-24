<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * نمایش لیست محصولات
     */
    public function index()
    {
        $products = Product::with(['category', 'variants'])
            ->where('is_active', true)
            ->latest()
            ->get();

        return ProductResource::collection($products);
    }

    /**
     * ایجاد محصول
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);

        return new ProductResource($product);
    }

    /**
     * نمایش یک محصول
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants']);
        return new ProductResource($product);
    }

    /**
     * ویرایش محصول
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return new ProductResource($product);
    }

    /**
     * حذف محصول
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'محصول با موفقیت حذف شد.',
        ]);
    }
}
