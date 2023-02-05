<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleUserAnswered extends Model
{
    // THE PURPOSE IF THIS MODEL IS SIMPLE
    // THIS TABLE CAN STORE THE CORRECTLY ANSWERED ARTICLES FOR USERS
    // THIS IS AN INTERMEDIATE, MANY TO MANY TABLE & MODEL
    // WITH THE HELP IF THIS MODEL IT WILL BE EASIER TO MAKE READEABLE QUERIES

    use HasFactory;

    protected $table = 'article_user_answereds';

    protected $fillable = [
        'user_id',
        'article_id',
        'point'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeUserId($query, $userId)
    {
        $query->where('user_id', $userId);
    }

    public function scopeArticleId($query, $articleId)
    {
        $query->where('article_id', $articleId);
    }

    /**
     * $date = ->format('Y-m');
     * example:
     * $date = 1970-01
     */

    public function scopeExactDate($query, $date)
    {
        $formattedDate = Carbon::createFromFormat('Y-m-d', $date);

        $query->whereDate('updated_at', $formattedDate);
    }
}
