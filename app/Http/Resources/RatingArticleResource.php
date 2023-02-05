<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class RatingArticleResource extends JsonResource
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

        $this->resource->load(['preferences']);

        return [
            'slug' => $this->resource->slug,
            'title' => $this->resource->title,
            'type' => $this->resource->type,
            'created_at' => $this->resource->created_at,
            'preferences' => $this->resource->preferences,
            'views' => $this->resource->views->sum('count_views'),
            'downloads' => $this->resource->downloads->sum('count_downloads'),
            'comments' => $this->resource->comments->count(),
            'overall_rating' => $this->resource->reviews->count() == 0 ? 0 : ($this->resource->reviews->sum('rating') / $this->resource->reviews->count()),
        ];
    }
}
