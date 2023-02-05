<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleThumbnailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Article);

        return [
            'uuid' => $this->resource->uuid,
            'thumbnail' => $this->resource->getThumbnailUrl(),
            'title' => $this->resource->title,
            'author' => UserListResource::make($this->resource->author),
            'excerpt' => $this->resource->excerpt,
            'created_at' => $this->resource->created_at,
            'published_at' => $this->resource->published_at,
            'user_point' => $this->resource?->loggedInUserArticleAnswer?->point ?? 0,

        ];
    }
}
