<?php

namespace App\Http\Resources;

use App\Models\ArticleAsSlideImage;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Storage;

class DemoSlideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof ArticleAsSlideImage);
        return [
            'article_as_slide_id' => $this->resource->article_as_slide_id,
            'article_id' => $this->resource->article_id,
            'created_by_id' => $this->resource->created_by_id,
            'order' => $this->resource->order,
            'order_question' => $this->resource->order_question,
            'path' => URL::to(Storage::disk('ppt_files')->url($this->resource->path)),
        ];
    }
}
