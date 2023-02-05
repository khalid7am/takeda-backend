<?php

namespace App\Services;

use App\Models\Answers;
use App\Models\Article;
use App\Models\ArticleUserAnswered;
use App\Models\Preference;
use App\Models\Question;
use App\Models\RelevancePoint;
use DB;
use Illuminate\Support\Str;

class ArticleService
{
    public function getAnsweredArticleIds($userId)
    {
        $answeredArticleIds = ArticleUserAnswered::query()
            ->select('id', 'user_id', 'article_id')
            ->where('user_id', $userId)
            ->pluck('article_id');



        return $answeredArticleIds;
    }

    public function getNotAnsweredArticleIds($userId)
    {
        $answeredArticleIds = $this->getAnsweredArticleIds($userId);

        $notAnsweredArticleIds = Article::query()
            ->select('id')
            ->whereNotIn('id', $answeredArticleIds)
            ->pluck('id');



        return $notAnsweredArticleIds;
    }

    public function getArticlesNotAnswered($userId)
    {
        $answeredArticleIds = $this->getAnsweredArticleIds($userId);

        $articlesNotAnsweredByUser = Article::query()
        ->select('id')
        ->whereNotIn('id', $answeredArticleIds)
        ->get();

        return $articlesNotAnsweredByUser;
    }

    public function getUserRelevancePoints($userId)
    {
        $RelevancePoint = RelevancePoint::query()
           ->select('user_id', 'preference_id', 'point')
           ->userId($userId)
           ->get();

        return $RelevancePoint;
    }

    public function getArticles($userId, $searchTerm, $paginateAmount = 12)
    {

        // GET ANSWERED ARTICLE IDS
        $notAnsweredArticleIds = $this->getNotAnsweredArticleIds($userId);

        // GET THE PREFERRED ARTICLES
        // THESE ARTICLES HAVE THE PREFERENCES/TAGS WHICH ARE RELATED TO THE USER
        $preferredArticlesOrderedIds = Article::query()
            ->select('id', 'title', 'excerpt', 'content')
            ->whereIn('articles.id', $notAnsweredArticleIds)
            ->with('preferences')
            ->with('preferences')
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($query2) use ($searchTerm) {
                    $query2->where('title', 'like', '%'. $searchTerm . '%')
                    ->orWhere('excerpt', 'like', '%'. $searchTerm . '%')
                    ->orWhere('content', 'regexp', $searchTerm);
                });
            })

            ->get()
            ->sortByDesc('preference_point_by_user')
            ->pluck('id')
            ->toArray();

        // dd($preferredArticlesOrderedIds->take(10));

        // GENERATE THE RIGHT ORDER
        $preferredArticles = Article::query()
            ->whereIn('id', $preferredArticlesOrderedIds)
            ->orderByRaw('FIELD (id, ' . implode(', ', $preferredArticlesOrderedIds) . ') asc')
            ->paginate($paginateAmount);

        return $preferredArticles;
    }

    static public function getRelatedPreferences(array $preferencesId)
    {
        $relatedPreferenceIds = Preference::whereIn('id', $preferencesId)->with('related')->select('id')->get();

        foreach ($relatedPreferenceIds as $preferenceId) {
            $relatedPreferences = $preferenceId->related->pluck('id')->toArray();
            $preferencesId = array_unique(array_merge($preferencesId, $relatedPreferences));
        }

        return $preferencesId;
    }
}
