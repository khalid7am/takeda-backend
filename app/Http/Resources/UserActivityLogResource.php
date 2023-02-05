<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use Carbon\Carbon;

class UserActivityLogResource extends JsonResource
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
            'monthly_points' => $this->resource->overall_performancePoints['current_month'],
            'overall_points' => $this->resource->overall_performancePoints['overall'],
            'monthly_articles_created' => $this->resource->articles->whereBetween('created_at', $range)->count(),
            'overall_articles_created' => $this->resource->articles->count(),
            'monthly_comments' => $this->resource->comments->whereBetween('created_at', $range)->count(),
            'overall_comments' => $this->resource->comments->count(),
            'deleted_comments' => Comment::onlyTrashed()->where('user_id', $this->resource->id)->count(),
            'monthly_pending_articles' => $this->resource->articles->whereNull('published_at')->whereBetween('created_at', $range)->count(),
            'overall_pending_articles' => $this->resource->articles->whereNull('published_at')->count(),
            'monthly_correct_answers' => $this->resource->answers->where('is_correct', true)->whereBetween('created_at', $range)->count(),
            'monthly_incorrect_answers' => $this->resource->answers->where('is_correct', false)->whereBetween('created_at', $range)->count(),
            'overall_correct_answers' => $this->resource->answers->where('is_correct', true)->count(),
            'overall_incorrect_answers' => $this->resource->answers->where('is_correct', false)->count(),
            'monthly_finished_quizzes' => $this->resource->views->where('is_finished', true)->whereBetween('created_at', $range)->count(),
            'overall_finished_quizzes' => $this->resource->views->where('is_finished', true)->count(),
        ];
    }
}
