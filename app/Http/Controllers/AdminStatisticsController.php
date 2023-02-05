<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Http\Requests\StatisticsRequest;
use App\Models\Answers;
use App\Models\ArticleUserAnswered;
use Carbon\Carbon;
use DB;

class AdminStatisticsController extends Controller
{
    const TOP_USERS_TAKE_ARRAY = [
        'top-five' => 5,
        'top-ten' => 10,
        'top-twenty' => 20,
    ];


    public function articlesByTypes(Request $request)
    {
        $articles = Article::query()
            ->selectRaw('type, count(type) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    // preferences by usage

    public function preferencesByUsage(Request $request)
    {
        $preferences = DB::table('article_preferences')
        ->selectRaw('preference_id, count(preference_id) as count, preferences.name as preference_name')
        ->join('preferences', 'preferences.id', '=', 'article_preferences.preference_id')
        ->groupBy('preference_id')
        ->pluck('count', 'preference_name');

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    // top 10 user by article answer point

    public function topTenUserByPerformancePoint(Request $request)
    {
        $take = $request->get('gather');

        if (!isset(self::TOP_USERS_TAKE_ARRAY[$take])) {
            return response()->json('Wrong parameter!', 400);
        }

        $limit = 5;
        if ($take) {
            $limit = self::TOP_USERS_TAKE_ARRAY[$take];
        }

        $topUsers = ArticleUserAnswered::query()
        ->selectRaw('user_id, sum(point) as sum, users.name as user_name')
        ->join('users', 'users.id', '=', 'article_user_answereds.user_id')
        ->groupBy('user_id')
        ->orderBy('sum', 'DESC')
        ->take($limit)
        ->pluck('sum', 'user_name');

        return response()->json([
            'success' => true,
            'data' => $topUsers,
        ]);
    }

    // top 10 user by answer quality
    public function topTenUserByAnswerAccuraty(Request $request)
    {
        $take = $request->get('gather');

        if (!isset(self::TOP_USERS_TAKE_ARRAY[$take])) {
            return response()->json('Wrong parameter!', 400);
        }

        $limit = 5;
        if ($take) {
            $limit = self::TOP_USERS_TAKE_ARRAY[$take];
        }
        $topUsers = Answers::query()
            ->select(
                'user_id',
                'users.name as user_name',
                \DB::raw('ROUND((SUM(if(is_correct = \'1\', 1, 0)) / (SUM(if(is_correct = \'1\', 1, 0)) + SUM(if(is_correct = \'0\', 1, 0))) * 100), 2) as quiz_performance'),
            )
            ->join('users', 'users.id', '=', 'answers.user_id')
            ->groupBy('user_id')
            ->orderBy('quiz_performance', 'DESC')
            ->take($limit)
            ->pluck('quiz_performance', 'user_name');


        return response()->json([
            'success' => true,
            'data' => $topUsers,
        ]);
    }
}
