<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Question);

        return [
            'uuid' => $this->resource->uuid,
            'paragraph_uuid' => $this->resource->paragraph_uuid,
            'question' => $this->resource->question,
            'order' => $this->resource->order,
            'show_at' => $this->resource->show_at ?? null,
            'choices' => QuestionChoiceResource::collection($this->resource->choices),
        ];
    }
}
