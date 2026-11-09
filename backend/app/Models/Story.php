<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $fillable = [
        'title',
        'cover_image',
        'media_path',
        'button_text',
        'button_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getMediaTypeAttribute(): string
    {
        $ext = strtolower(pathinfo($this->media_path, PATHINFO_EXTENSION));

        return in_array($ext, ['mp4', 'webm', 'mov']) ? 'video' : 'image';
    }
}
