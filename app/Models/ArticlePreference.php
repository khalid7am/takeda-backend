<?php

namespace App\Models;

use App\Traits\HasArticleId;
use App\Traits\HasPreferenceId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlePreference extends Model
{
    use HasFactory;
    use HasArticleId;
    use HasPreferenceId;

    public $timestamps = false;

    protected $guarded = ['id'];


    public function scopeArticleId($query, $articleId)
    {
        $query->where('article_id', $articleId);
    }
}
