<?php

namespace App\Http\Resources;

use App\Models\ArticleUserAnswered;
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
        assert($this->resource instanceof ArticleUserAnswered);

        return [
            'user_id' => UserListResource::make($this->user),
            'article_id' => ArticleResource::make($this->article),
            'point' => $this->point,
        ];
    }
}
