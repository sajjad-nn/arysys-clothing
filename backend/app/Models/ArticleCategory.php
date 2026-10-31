<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (ArticleCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);

                if (empty($category->slug)) {
                    $category->slug = 'article-category-' . uniqid();
                }
            }
        });
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
