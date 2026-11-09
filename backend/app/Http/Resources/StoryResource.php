<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover' => asset('storage/' . $this->cover_image),
            'media' => asset('storage/' . $this->media_path),
            'type' => $this->media_type,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
        ];
    }
}
