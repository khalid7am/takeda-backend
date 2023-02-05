<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Types\RoleType;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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

        return [
            'uuid' => $this->resource->uuid,
            'name' => $this->resource->name,
            'registered_at' => $this->resource->created_at->format('d/m/Y'),
            'is_author' => $this->resource->role->code === RoleType::EDITOR,
            'role_name' => $this->resource->role->name,
            'role_code' => $this->resource->role->code,
            'email' => $this->resource->email,
            'institution' => $this->resource->institution,
            'seal_number' => $this->resource->seal_number,
            'status' => $this->resource->getStatusText(),
            $this->mergeWhen((optional($request->user())->isAdmin() && $this->resource->isAdmin()), [
                'is_online' => $this->resource->is_online,
            ]),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
