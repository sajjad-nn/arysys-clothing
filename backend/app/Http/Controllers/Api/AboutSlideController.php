<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSlide;
use Illuminate\Http\JsonResponse;

class AboutSlideController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = AboutSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'name' => $slide->name,
                    'image' => $slide->image,
                    'image_url' => asset('storage/' . $slide->image),
                    'sort_order' => $slide->sort_order,
                ];
            });

        return response()->json($slides);
    }
}
