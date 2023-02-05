<?php

namespace App\Http\Resources;

use App\Models\Preference;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferencePostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Preference);
        return [
            'preference' => $this->resource->name,
            'posts' => ArticleResource::collection($this->resource->articles)
        ];
    }
}
