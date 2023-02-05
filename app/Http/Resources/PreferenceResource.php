<?php

namespace App\Http\Resources;

use App\Models\Preference;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Storage;

class PreferenceResource extends JsonResource
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
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'uuid' => $this->resource->uuid,
            'slug' => $this->resource->slug,
            'category' => $this->resource->category,
            'image' => URL::to(Storage::url($this->resource->image)),
            $this->mergeWhen(optional($request->user())->isAdmin(), [
                'articles_count' => $this->resource->articles_count,
            ]),
        ];
    }
}
