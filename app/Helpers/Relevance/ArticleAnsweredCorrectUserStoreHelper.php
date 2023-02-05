<?php

namespace App\Helpers\Relevance;

use App\Models\Article;
use App\Models\ArticlePreference;
use App\Models\ArticleUserAnswered;
use App\Models\RelevancePoint;

class ArticleAnsweredCorrectUserStoreHelper
{
    public $userId;
    public $article;

    public static function start()
    {
        return new static;
    }

    public function userId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function articleId($articleId)
    {
        $this->articleId = $articleId;
        return $this;
    }

    public function send()
    {
        if ($this->userId && $this->articleId) {
            $articleAnswered = ArticleUserAnswered::firstOrCreate([
                'user_id' => $this->userId,
                'article_id' => $this->articleId,
            ]);

            $articleAnswered->increment('point');


            return $articleAnswered;
        }
    }
}
