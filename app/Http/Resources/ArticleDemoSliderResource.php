<?php

namespace App\Http\Resources;

use App\Models\ArticleAsSlide;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDemoSliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof ArticleAsSlide);
        return [
            'pdf_name' => $this->resource->pdf_name,
            'ppt_name' => $this->resource->ppt_name,
            'identifier' => $this->resource->unique_identifier,
            'path' => $this->resource->path,
            'created_by_id' => $this->resource->created_by_id,
            'article_id' => $this->resource->article_id,
            'slides' => DemoSlideResource::collection($this->resource->slideImages),
        ];
    }
}
