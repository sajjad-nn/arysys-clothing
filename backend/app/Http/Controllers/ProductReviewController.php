<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * نمایش نظرات تأییدشده یک محصول
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->where('status', 'approved')
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    /**
     * ثبت نظر برای محصول
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:3|max:2000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $existingReview = ProductReview::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($existingReview) {
            return response()->json([
                'message' => 'شما قبلاً برای این محصول نظر ثبت کرده‌اید.',
            ], 409);
        }

        $review = $product->reviews()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'rating' => $validated['rating'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'نظر شما ثبت شد و پس از تأیید مدیریت نمایش داده می‌شود.',
            'review' => $review,
        ], 201);
    }
}
