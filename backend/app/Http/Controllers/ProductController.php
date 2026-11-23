<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * دریافت محصولات همراه با فیلتر و صفحه‌بندی
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'category' => 'nullable|string',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'on_sale' => 'nullable|boolean',
            'in_stock' => 'nullable|boolean',
            'sort' => 'nullable|in:latest,price_asc,price_desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:48',
        ]);

        $query = Product::query()
            ->with([
                'category',
                'variants' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->where('is_active', true);

        // فیلتر دسته‌بندی بر اساس slug یا ID
        if (!empty($validated['category'])) {
            $category = $validated['category'];

            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);

                if (is_numeric($category)) {
                    $q->orWhere('id', $category);
                }
            });
        }

        // فیلتر رنگ و سایز روی یک تنوع مشترک
        if (
            !empty($validated['color']) ||
            !empty($validated['size'])
        ) {
            $query->whereHas('variants', function ($q) use ($validated) {
                $q->where('is_active', true);

                if (!empty($validated['color'])) {
                    $q->where('color', $validated['color']);
                }

                if (!empty($validated['size'])) {
                    $q->where('size', $validated['size']);
                }
            });
        }

        // فیلتر محدوده قیمت
        if (
            isset($validated['min_price']) ||
            isset($validated['max_price'])
        ) {
            $min = $validated['min_price'] ?? 0;
            $max = $validated['max_price'] ?? PHP_INT_MAX;

            $query->where(function ($q) use ($min, $max) {
                // محصولات بدون تنوع فعال
                $q->where(function ($productQuery) use ($min, $max) {
                    $productQuery
                        ->whereDoesntHave('variants', function ($variantQuery) {
                            $variantQuery->where('is_active', true);
                        })
                        ->whereRaw(
                            'CASE
                                WHEN discount_price IS NOT NULL
                                     AND discount_price >= 0
                                     AND discount_price < price
                                THEN discount_price
                                ELSE price
                            END BETWEEN ? AND ?',
                            [$min, $max]
                        );
                });

                // محصولات دارای تنوع فعال
                $q->orWhereHas('variants', function ($variantQuery) use ($min, $max) {
                    $variantQuery
                        ->where('is_active', true)
                        ->whereRaw(
                            'CASE
                                WHEN discount_price IS NOT NULL
                                     AND discount_price >= 0
                                     AND discount_price < price
                                THEN discount_price
                                ELSE price
                            END BETWEEN ? AND ?',
                            [$min, $max]
                        );
                });
            });
        }

        // فقط محصولات تخفیف‌دار
        if (!empty($validated['on_sale'])) {
            $query->where(function ($q) {
                $q->where(function ($productQuery) {
                    $productQuery
                        ->whereDoesntHave('variants', function ($variantQuery) {
                            $variantQuery->where('is_active', true);
                        })
                        ->whereNotNull('discount_price')
                        ->whereColumn('discount_price', '<', 'price');
                });

                $q->orWhereHas('variants', function ($variantQuery) {
                    $variantQuery
                        ->where('is_active', true)
                        ->whereNotNull('discount_price')
                        ->whereColumn('discount_price', '<', 'price');
                });
            });
        }

        // فقط محصولات موجود
        if (!empty($validated['in_stock'])) {
            $query->where(function ($q) {
                $q->where(function ($productQuery) {
                    $productQuery
                        ->whereDoesntHave('variants', function ($variantQuery) {
                            $variantQuery->where('is_active', true);
                        })
                        ->where('stock', '>', 0);
                });

                $q->orWhereHas('variants', function ($variantQuery) {
                    $variantQuery
                        ->where('is_active', true)
                        ->where('stock', '>', 0);
                });
            });
        }

        // مرتب‌سازی
        switch ($validated['sort'] ?? 'latest') {
            case 'price_asc':
                $query->orderByRaw('
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                              AND pv.is_active = 1
                        )
                        THEN (
                            SELECT MIN(
                                CASE
                                    WHEN pv.discount_price IS NOT NULL
                                         AND pv.discount_price >= 0
                                         AND pv.discount_price < pv.price
                                    THEN pv.discount_price
                                    ELSE pv.price
                                END
                            )
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                              AND pv.is_active = 1
                        )
                        ELSE
                            CASE
                                WHEN discount_price IS NOT NULL
                                     AND discount_price >= 0
                                     AND discount_price < price
                                THEN discount_price
                                ELSE price
                            END
                    END ASC
                ');
                break;

            case 'price_desc':
                $query->orderByRaw('
                    CASE
                        WHEN EXISTS (
                            SELECT 1
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                              AND pv.is_active = 1
                        )
                        THEN (
                            SELECT MAX(
                                CASE
                                    WHEN pv.discount_price IS NOT NULL
                                         AND pv.discount_price >= 0
                                         AND pv.discount_price < pv.price
                                    THEN pv.discount_price
                                    ELSE pv.price
                                END
                            )
                            FROM product_variants pv
                            WHERE pv.product_id = products.id
                              AND pv.is_active = 1
                        )
                        ELSE
                            CASE
                                WHEN discount_price IS NOT NULL
                                     AND discount_price >= 0
                                     AND discount_price < price
                                THEN discount_price
                                ELSE price
                            END
                    END DESC
                ');
                break;

            default:
                $query->latest();
                break;
        }

        // صفحه‌بندی
        $perPage = $validated['per_page'] ?? 12;

        $products = $query->paginate($perPage);

        // گزینه‌های فیلتر
        $colors = ProductVariant::query()
            ->where('is_active', true)
            ->whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->orderBy('color')
            ->pluck('color');

        $sizes = ProductVariant::query()
            ->where('is_active', true)
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->orderBy('size')
            ->pluck('size');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return ProductResource::collection($products)->additional([
            'filters' => [
                'colors' => $colors,
                'sizes' => $sizes,
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * نمایش جزئیات یک محصول
     */
    public function show(Product $product)
    {
        $product->load(['category', 'variants']);

        return new ProductResource($product);
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
