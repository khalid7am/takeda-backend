<?php

namespace App\Services;

use App\Models\Answers;
use App\Models\Article;
use App\Models\ArticleUserAnswered;
use App\Models\Question;
use App\Models\RelevancePoint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserService
{
    public function getUserPerformancePoints(User $user, $date = null)
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $point = ArticleUserAnswered::query()
            ->userId($user->id)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->sum('point');

        return $point;
    }

    public function getUserPerformancePointsOverallAndCurrentMonth(User $user)
    {
        // INIT
        $points = [];
        //$point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $overall = ArticleUserAnswered::query()
            ->userId($user->id)
            // ->when($date, function ($query) use ($date) {
            //     $query->whereDate('created_at', $date);
            // })
            ->sum('point');

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();


        $currentMonth = ArticleUserAnswered::query()
            ->userId($user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('point');

        $points['overall'] = $overall;
        $points['current_month'] = $currentMonth;


        return $points;
    }

    public function getAuthorPerformancePoints(User $user, $date = null)
    {
        // INIT
        $point = 0;
        // GET THE SUM OF POINTS, BASED ON PERFORMANCE POINTS
        // WITH THE HELP OF A COLLECTiON METHOD
        $point = Article::query()
            ->where('author_id', $user->id)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('published_at', $date);
            })
            ->count();

        return $point;
    }

    public function getAverageAnswerResult(User $user)
    {
        $percentage = 100;
        $answers = Answers::select('is_correct')->giveByUser($user->id)->get();

        $totalAnswersCount = $answers->count();
        $correctAnswersCount = $answers->where('is_correct', 1)->count();


        if ($totalAnswersCount > 0) {
            $percentage = ($correctAnswersCount / $totalAnswersCount) * 100;
        }


        return round($percentage, 2);
    }

    public function getUserResults(User $user)
    {
        $completedArticles = ArticleUserAnswered::userId($user->id)->get();

        $completedArticlesCount = $completedArticles->count();
        $completedArticlesThisWeek = $completedArticles->filter(function ($article) {
            return $article->created_at >= Carbon::now()->subWeek();
        })->count();

        $completedArticlesThisMonth = $completedArticles->filter(function ($article) {
            return $article->created_at >= Carbon::now()->subMonth();
        })->count();

        // foreach (config('takeda-ranking.static') as $key => $value) {
        // }

        $positions = (new RankingService)->getNormalUserPosition($user->id);
        $currentPosition = $positions['place'];
        $totalUserCount = $positions['usersCount'];
        $currentPositionPercentage = $positions['placePercentage'];


        $nextColorNeedAnswer = (new RankingService)->getNextStaticRankingPoint($user);
        $averageAnswerResult = (new UserService)->getAverageAnswerResult($user);

        $preferencesAndInfos = (new RankingService)->getNormalUserPreferenceInfos($user->id);

//        dd($preferencesAndInfos);


        $usersData = [
            'name' => $user->name,
            'title' => $user->rank_title,
            'icon' => $user->rank_icons['438']['svg'], //? (config('app.url') . \Storage::url($user->rank_icons['438']['svg'])) : '-',
            'currentPosition' => $currentPosition,
            'totalUserCount' => $totalUserCount,
            'currentPositionPercentage' => $currentPositionPercentage,

            'performancePoint' => $user->getUserPerformancePoints(),
            'performancePointNext' => '-',
            'completedArticles' => $completedArticlesCount,
            'completedArticlesThisWeek' => $completedArticlesThisWeek,
            'completedArticlesThisMonth' => $completedArticlesThisMonth,
            'nextColorNeedAnswer' => $nextColorNeedAnswer,

            'averageAnswerResult' => $averageAnswerResult,
            'preferencesAndInfos' => $preferencesAndInfos,
        ];

        return $usersData;
    }
}
