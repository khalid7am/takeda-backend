<?php

namespace App\Http\Resources;

use App\Models\Preference;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferenceRelatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Preference);

        return [
            'parent' => PreferenceResource::make($this->resource),
            'childs' => PreferenceResource::collection($this->resource->related),
        ];
    }
}
