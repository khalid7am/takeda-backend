<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasUserId;
use App\Traits\HasArticleId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    use HasUuid;
    use HasUserId;
    use HasArticleId;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($review) {
            $review->comment()->create([
                'article_id' => $review->article_id,
                'content' => $review->comment,
                'user_id' => $review->user_id,
            ]);
        });
    }

    public function comment()
    {
        return $this->hasOne(Comment::class, 'review_id', 'id');
    }
}
