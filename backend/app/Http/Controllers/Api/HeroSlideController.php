<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\JsonResponse;

class HeroSlideController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = HeroSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'image' => $slide->image,
                    'image_url' => asset('storage/' . $slide->image),
                    'subtitle' => $slide->subtitle,
                    'title' => $slide->title,
                    'highlighted_title' => $slide->highlighted_title,
                    'description' => $slide->description,
                    'sort_order' => $slide->sort_order,
                ];
            });

        return response()->json($slides);
    }
}
