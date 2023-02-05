<?php

namespace App\Traits;

use App\Models\Article;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToArticle
{
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}