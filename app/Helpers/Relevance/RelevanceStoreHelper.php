<?php

namespace App\Helpers\Relevance;

use App\Models\Article;
use App\Models\ArticlePreference;
use App\Models\RelevancePoint;

class RelevanceStoreHelper
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
        $preferenceIds = ArticlePreference::query()
            ->select('article_id', 'preference_id')
            ->articleId($this->articleId)
            ->pluck('preference_id');



        foreach ($preferenceIds as $preferenceId) {
            $relevancePoint = RelevancePoint::firstOrCreate([
                'user_id' => $this->userId,
                'preference_id' => $preferenceId,

            ]);
            $relevancePoint->increment('point');
        }


        return 'ok';
    }
}
