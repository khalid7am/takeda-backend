<?php

namespace App\Helpers\Relevance;

use App\Models\Article;
use App\Models\ArticlePreference;
use App\Models\RelevancePoint;

class UserPerformanceStoreHelper
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
        $article = Article::query()
            ->where('id', $this->articleId)
            ->with('questions', function ($query) {
                $query->with('answers', function ($query2) {
                    $query2->giveByUser($this->userId);
                });
            })
            ->first();



        // NO CONDITION, GIVE THAT RELEVANCE & QUESTION 1 POINT!
        $this->startStoring();


        foreach ($article->questions as $question) {
            foreach ($question->answers as $answer) {
                // IN CASE OF GOOD ANSWER
                // 1 POINT TO GRYFFINDOR
                if ($answer->is_correct) {
                    $this->startStoring();
                }
            }
        }

        return 'ok';
    }


    public function startStoring()
    {
        $articleCorrectAnswerUser = (new ArticleAnsweredCorrectUserStoreHelper)
            ->start()
            ->userId($this->userId)
            ->articleId($this->articleId)
            ->send();

        $storedRelevance = (new RelevanceStoreHelper)->start()
            ->userId($this->userId)
            ->articleId($this->articleId)
            ->send();
    }
}
