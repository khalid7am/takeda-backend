<?php

namespace App\Http\Resources;

use App\Models\BlogArticle;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof BlogArticle);

        $this->resource->load('article');

        return [
            'uuid' => $this->whenLoaded('article', fn() => $this->resource?->article?->uuid),
            'type' => $this->whenLoaded('article', fn() => $this->resource?->article?->type),
            'title' => $this->whenLoaded('article', fn() => $this->resource?->article?->title),
            'slug' => $this->whenLoaded('article', fn() => $this->resource?->article?->slug),
            'excerpt' => $this->whenLoaded('article', fn() => $this->resource?->article?->excerpt),
            'content' => $this->whenLoaded('article', fn() => $this->resource?->article?->content),
            'created_at' => $this->whenLoaded('article', fn() => $this->resource?->article?->created_at),
            'preferences' => $this->whenLoaded('article', fn() => PreferenceResource::collection($this->resource?->article?->preferences)),
            'thumbnail' => $this->whenLoaded('article', fn() => $this->resource?->article?->getThumbnailUrl()),
            'published_at' => $this->whenLoaded('article', fn() => $this->resource?->article?->published_at),
            'media' => $this->whenLoaded('article', fn() => $this->resource?->article?->media()),
        ];
    }
}
