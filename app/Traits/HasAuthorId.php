<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuthorId
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id')->withTrashed();
    }
}