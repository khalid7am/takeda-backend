<?php

namespace App\Repositories;

use App\Models\ArticleUserAnswered;

class ArticleUserAnsweredRepository
{

    /**
     *
     * $date = ->format('Y-m');
     * example:
     * $date = 1970-01
     * Get the users with their sum points at the given month.
     * return  will be an array
     *  $results = [
     *      'username' => 10,
     *      'username2' => 20,
     *      ....
     *  ];
     *
     * Second parameter is optional, you can set the maximum return row number.
     * What does it mean?
     * It will return the "first" X row.
     */
    public function getExactMonthPointsByUsers($date, $topUserCount = null)
    {
        $dbResult = ArticleUserAnswered::query()
            ->exactDate($date)
            ->orderByDesc('point')
            ->orderByDesc('updated_at')
            ->when($topUserCount, function ($query) use ($topUserCount) {
                $query->take($topUserCount);
            })
            ->get()
            ->groupBy('user_id');

        $results = $dbResult->mapWithKeys(function ($group, $key) {
            // STRUCTURE
            // NAME => POINT
            return [
                $group->first()->user?->name ?? $group->first()->user?->email => $group->sum('point'),
            ];
        });

        return $results;
    }


    /**
     * return bool
     */
    public function userFilledThisArticle($userId, $articleId)
    {
        $userFilledThisArticle = false;

        if (ArticleUserAnswered::userId($userId)->articleId($articleId)->exists()) {
            $userFilledThisArticle = true;
        }

        return $userFilledThisArticle;
    }
}
