<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Article;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasArticleId
{
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}