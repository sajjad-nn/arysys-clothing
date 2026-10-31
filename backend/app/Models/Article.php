<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);

                if (empty($article->slug)) {
                    $article->slug = 'article-' . uniqid();
                }
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(
            ArticleCategory::class,
            'article_category_id'
        );
    }
}
