<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'stock' => $this->stock,
            'image' => $this->image,
            'is_active' => $this->is_active,

            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),

            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants
                    ->where('is_active', true)
                    ->values()
                    ->map(fn ($variant) => [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'size' => $variant->size,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'discount_price' => $variant->discount_price,
                        'stock' => $variant->stock,
                        'image' => $variant->image,
                        'is_active' => $variant->is_active,
                    ]);
            }),
        ];
    }
}
