<?php

namespace App\Http\Resources;

use App\Models\UserSession;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof UserSession);

        return [
            'name' => $this->resource->user->name,
            'uuid' => $this->resource->user->uuid,
            'login_at' => $this->resource->login_at->format('d.m.Y H:i'),
            'logout_at' => $this->resource->logout_at ? $this->resource->logout_at->format('d.m.Y H:i') : 'munkamenet lejárt',
        ];
    }
}
