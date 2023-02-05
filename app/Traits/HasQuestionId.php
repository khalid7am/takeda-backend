<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasQuestionId
{
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, "question_id", "id");
    }
}