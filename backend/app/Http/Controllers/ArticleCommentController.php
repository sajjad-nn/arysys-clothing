<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    /**
     * نمایش نظرات تأییدشده یک مقاله
     */
    public function index(Article $article)
    {
        $comments = $article->comments()
            ->where('status', 'approved')
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * ثبت نظر برای مقاله
     */
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'body' => 'required|string|min:3|max:2000',
        ]);

        $comment = $article->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'نظر شما ثبت شد و پس از تأیید مدیریت نمایش داده می‌شود.',
            'comment' => $comment,
        ], 201);
    }
}
