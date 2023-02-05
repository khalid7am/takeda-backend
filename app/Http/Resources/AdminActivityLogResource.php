<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use Carbon\Carbon;

class AdminActivityLogResource extends JsonResource
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

        $range = [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->toDateString()];

        return [
            'approved_users' => SimpleUserResource::collection($this->resource->adminAcceptedUsers()->get()),
            'rejected_users' => SimpleUserResource::collection($this->resource->adminRejectedUsers()->get()),
            'monthly_deleted_comments' => $this->resource->adminDeletedComments->whereBetween('deleted_at', $range)->count(),
            'overall_deleted_comments' => $this->resource->adminDeletedComments->count(),
            'monthly_approved_articles' => $this->resource->adminPublishedArticles->whereBetween('published_at', $range)->count(),
            'overall_approved_articles' => $this->resource->adminPublishedArticles->count(),
            'monthly_rejected_articles' => $this->resource->adminRejectedArticles->whereBetween('deleted_at', $range)->count(),
            'overall_rejected_articles' => $this->resource->adminRejectedArticles->count(),
        ];
    }
}
