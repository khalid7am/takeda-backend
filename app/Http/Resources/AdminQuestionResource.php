<?php

namespace App\Http\Resources;

use App\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminQuestionResource extends JsonResource
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

        foreach ($this->resource->images as $image) {
            if (substr($image['path'], 0, 4 ) === "http") {
                $image->path = $image['path'];
            } else {
                $image->path = config('app.url') . \Storage::url($image['path']);
            }
        }
        
        return [
            'order' => $this->resource->order,
            'choices' => $this->resource->choices,
            'images' => $this->resource->images,
            'paragraph_uuid' => $this->resource->paragraph_uuid,
            'question' => $this->resource->question,
            'reasons' => $this->resource->reasons,
            'show_at' => $this->resource->show_at,
            'uuid' => $this->resource->uuid,
        ];
    }
}
