<?php

namespace App\Http\Resources;

use App\Models\QuestionChoice;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionChoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof QuestionChoice);

        return [
            'uuid' => $this->resource->uuid,
            'choice' => $this->resource->answer,
            'count_answers' => $this->resource->answers_count ?? null,
        ];
    }
}
