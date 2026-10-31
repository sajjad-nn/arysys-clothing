<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * نمایش مقالات منتشرشده
     */
    public function index()
    {
        $articles = Article::with('category')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->get();

        return ArticleResource::collection($articles);
    }

    /**
     * نمایش یک مقاله با استفاده از slug
     */
    public function show(string $slug)
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return new ArticleResource($article);
    }
}
