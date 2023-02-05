<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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

        $this->resource->load('lecturer');

        return [
            'uuid' => $this->resource->uuid,
            'type' => $this->resource->type,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'excerpt' => $this->resource->excerpt,
            'content' => $this->resource->content,
            'created_at' => $this->resource->created_at,
            'author' => UserListResource::make($this->resource->author),
            'lecturer_name' => $this->whenLoaded('lecturer', fn() => $this->resource?->lecturer?->name),
            'lecturer_uuid' => $this->whenLoaded('lecturer', fn() => $this->resource?->lecturer?->uuid),
            'average_rating' => $this->resource->getAverageRating(),
            'reviews' => ReviewResource::collection($this->resource->reviews),
            'comments' => CommentResource::collection($this->resource->comments),
            'preferences' => PreferenceResource::collection($this->resource->preferences),
            'publisher' => UserListResource::make($this->resource->publisher),
            'questions' => optional($request->user())->isAdmin() ? AdminQuestionResource::collection($this->resource->questions) : QuestionResource::collection($this->resource->questions),
            'questions_count' => $this->resource->questions()->count(),
            'thumbnail' => $this->resource->getThumbnailUrl(),
            'published_at' => $this->resource->published_at,
            'media' => $this->resource->media()
        ];
    }
}
