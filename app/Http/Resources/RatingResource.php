<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        return [
            "views" => $this->resource->authorArticleViews->sum('count_views'),
            "downloads" => $this->resource->authorArticleDownloads->sum('count_downloads'),
            "overall_rating" => $this->resource->authorArticleReviews->count() == 0 ? 0 : ($this->resource->authorArticleReviews->sum('rating')/$this->resource->authorArticleReviews->count()),
        ];
    }
}
