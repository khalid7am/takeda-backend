<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Comment);
        return [
            "uuid" => $this->resource->uuid,
            "author" => SimpleUserResource::make($this->resource->user),
            "article" => $this->resource->article,
            "parent" => CommentResource::make($this->resource->parent),
            "content" => $this->resource->content,
            "created_at" => $this->resource->created_at,
            "is_author" => $this->resource->article ? $this->resource->user->id == $this->resource->article->author_id : false,
        ];
    }
}
