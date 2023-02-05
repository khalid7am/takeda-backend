<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\HasUserId;
use App\Traits\HasArticleId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use HasUuid;
    use HasArticleId;
    use HasUserId;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'id');
    }
}
