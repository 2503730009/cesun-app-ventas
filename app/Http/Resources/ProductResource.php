<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'vendor_id'   => $this->vendor_id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (string) $this->price,
            'stock'       => $this->stock,
            'vendor'      => new VendorResource($this->whenLoaded('vendor')),
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
