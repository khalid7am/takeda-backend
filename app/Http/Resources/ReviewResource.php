<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Review);

        return [
            'uuid' => $this->resource->uuid,
            'rating' => $this->resource->rating,
            'message' => $this->resource->comment,
            'user' => UserListResource::make($this->resource->user),
            'created_at' => $this->resource->created_at
        ];
    }
}
