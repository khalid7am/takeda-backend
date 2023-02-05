<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Storage;

class SimpleUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof User);

        $this->resource->load(['role', 'profession', 'preferences']);

        return [
            'uuid' => $this->resource->uuid,
            'name' => $this->resource->name,
            'email'=> $this->resource->email,
            'role_code' => $this->resource->role->code,
            'role_name' => $this->resource->role->name,
            'institution' => $this->resource->institution,
            'seal_number' => $this->resource->seal_number,
            'profession_code' => $this->resource->profession->code,
            'profession_name' => $this->resource->profession->name,
            'profile_picture' => URL::to(Storage::url($this->resource->profile_picture)),
            'preferences' => PreferenceResource::collection($this->resource->preferences),
            'accepted_at' => $this->resource->accepted_at,
            $this->mergeWhen(optional($request->user())->isAdmin(), [
                'rejected_at' => $this->resource->rejected_at,
            ]),
            'is_lecturer' => $this->resource->is_lecturer,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'is_author_verified' => $this->resource->is_author_verified,
            'rank_title' => $this->resource->dynamic_position_name,
            'rank_icon' => $this->resource->rank_icons['105']['svg'],
            'current_position_percentage' => $this->current_position_percentage,
        ];
    }
}
