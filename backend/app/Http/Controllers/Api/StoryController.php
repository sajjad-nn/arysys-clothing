<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Story;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return StoryResource::collection($stories);
    }
}
