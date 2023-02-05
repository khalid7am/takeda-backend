<?php

namespace App\Models;

use App\Helpers\Relevance\ArticleAnsweredCorrectUserStoreHelper;
use App\Helpers\Relevance\RelevanceStoreHelper;
use App\Traits\HasUserId;
use App\Traits\HasQuestionId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answers extends Model
{
    use HasFactory;
    use HasUserId;
    use HasQuestionId;

    protected $guarded =  ['id'];

    protected $casts = [
        "is_correct" => "bool"
    ];

    public function choice()
    {
        return $this->belongsTo(QuestionChoice::class, "choice_id", "id");
    }

    public function scopeIsCorrect($query)
    {
        $query->where('is_correct', 1);
    }

    public function scopeIsNotCorrect($query)
    {
        $query->where('is_correct', 0);
    }

    public function scopeGiveByUser($query, $userId)
    {
        $query->where('user_id', $userId);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($answer) {
            // IF THE ANSWER IS CORRECT
            // RUN THE HELPER TO STORE THE ARTICLE-USER
            if ($answer->is_correct) {
                $articleCorrectAnswerUser = (new ArticleAnsweredCorrectUserStoreHelper)
                    ->start()
                    ->userId($answer->user_id)
                    ->articleId($answer->question?->article_id)
                    ->send();

                $storedRelevance = (new RelevanceStoreHelper)->start()
                    ->userId($answer->user_id)
                    ->articleId($answer->question?->article_id)
                    ->send();
            }
        });
    }
}
