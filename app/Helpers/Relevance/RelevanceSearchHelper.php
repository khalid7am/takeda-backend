<?php

namespace App\Helpers\Relevance;

use App\Models\Answers;
use App\Models\Article;
use App\Models\Question;
use App\Services\ArticleService;

class RelevanceSearchHelper
{
    public $userId;
    public $searchTerm;
    public $paginateAmount = 12;

    public static function start()
    {
        return new static;
    }

    public function userId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function searchFor($searchTerm)
    {
        $this->searchTerm = $searchTerm;
        return $this;
    }

    public function paginate($paginateAmount)
    {
        $this->paginateAmount = $paginateAmount ?? 12;
        return $this;
    }

    public function get()
    {
        $result = (new ArticleService)->getArticles($this->userId, $this->searchTerm, $this->paginateAmount);

        return $result;
    }
}
