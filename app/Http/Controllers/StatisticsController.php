<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Http\Requests\StatisticsRequest;
use App\Models\ArticleDownload;
use App\Models\ArticleView;
use App\Models\Preference;
use App\Models\Review;
use App\Models\UserSession;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function articles(StatisticsRequest $request)
    {
        $label = $request->articles_label;
        $preferenceId = $request->preferenceId;

        if (!$label) {
            abort(404);
        }

        if ($preferenceId) {
            $preference = Preference::where('slug', $preferenceId)->first();
            $articlesData = Article::query()->whereIn('id', $preference->articles()->get()->pluck('id'));
        } else {
            $articlesData = Article::query();
        }

        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'all':
                $articlesData
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'great':
                $articlesData
                    ->whereRelation('reviews', 'rating', 5)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'good':
                $articlesData
                    ->whereRelation('reviews', 'rating', 4)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'normal':
                $articlesData
                    ->whereRelation('reviews', 'rating', 3)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'poor':
                $articlesData
                    ->whereRelation('reviews', 'rating', 2)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'bad':
                $articlesData
                    ->whereRelation('reviews', 'rating', 1)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
        }

        $articlesData = $articlesData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $articlesData
        ]);
    }

    public function articleTypes(StatisticsRequest $request)
    {
        $label = $request->articletypes_label;

        if (!$label) {
            abort(404);
        }

        $articleTypesData = Article::query();
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'lego':
                $articleTypesData
                    ->where('type', 'LEG')
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'demo':
                $articleTypesData
                    ->where('type', 'DEM')
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'video':
                $articleTypesData
                    ->where('type', 'VID')
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'audio':
                $articleTypesData
                    ->where('type', 'AUD')
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
        }

        $articleTypesData = $articleTypesData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $articleTypesData
        ]);
    }

    public function comments(StatisticsRequest $request)
    {
        $label = $request->comments_label;
        $preferenceId = $request->preferenceId;

        if (!$label) {
            abort(404);
        }

        if ($preferenceId) {
            $preference = Preference::where('slug', $preferenceId)->first();
            $commentsData = Comment::query()->whereIn('id', array_merge(...$preference->articles()->get()->pluck('comments.*.id')));
        } else {
            $commentsData = Comment::query();
        }

        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'all':
                $commentsData
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'great':
                $commentsData
                    ->with('review')
                    ->whereHas('review', function ($query) {
                        $query->where('rating', 5);
                    })
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'good':
                $commentsData
                    ->with('review')
                    ->whereHas('review', function ($query) {
                        $query->where('rating', 4);
                    })
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'normal':
                $commentsData
                    ->with('review')
                    ->whereHas('review', function ($query) {
                        $query->where('rating', 3);
                    })
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'poor':
                $commentsData
                    ->with('review')
                    ->whereHas('review', function ($query) {
                        $query->where('rating', 2);
                    })
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'bad':
                $commentsData
                    ->with('review')
                    ->whereHas('review', function ($query) {
                        $query->where('rating', 1);
                    })
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
        }

        $commentsData = $commentsData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $commentsData
        ]);
    }

    public function users(StatisticsRequest $request)
    {
        $label = $request->users_label;

        if (!$label) {
            abort(404);
        }

        $usersData = User::query();
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'all':
                $usersData
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'new':
                $usersData
                    ->where(function ($query) use ($range) {
                        return $query
                            ->where('accepted_at', '>', 'created_at')
                            ->whereBetween('accepted_at', $range);
                    })
                    ->orWhere(function ($query) use ($range) {
                        return $query
                            ->where('rejected_at', '>', 'created_at')
                            ->whereBetween('rejected_at', $range);
                    })
                    ->selectRaw('DAYOFMONTH(accepted_at) monthDay, count(*) count');
                break;
            case 'active':
                $usersData
                    ->whereNotNull('accepted_at')
                    ->whereBetween('accepted_at', $range)
                    ->selectRaw('DAYOFMONTH(accepted_at) monthDay, count(*) count');
                break;
            case 'pending':
                $usersData
                    ->where(function ($query) use ($range) {
                        return $query
                            ->whereNull('accepted_at')
                            ->whereBetween('accepted_at', $range);
                    })
                    ->orWhere(function ($query) use ($range) {
                        return $query
                            ->whereNull('rejected_at')
                            ->orWhereBetween('rejected_at', $range);
                    })
                    ->selectRaw('DAYOFMONTH(accepted_at) monthDay, count(*) count');
                break;
        }

        $usersData = $usersData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $usersData
        ]);
    }

    public function profileAdmin(StatisticsRequest $request, User $admin)
    {
        $label = $request->admin_label;

        if (!$label) {
            abort(404);
        }

        $adminData = collect(new User);
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'admin-logins':
                $adminData = UserSession::whereBetween('login_at', $range)
                    ->where('user_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(login_at) monthDay, count(*) count');
                break;
            case 'approved-posts':
                $adminData = Article::withTrashed()
                    ->whereBetween('published_at', $range)
                    ->where('publisher_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(published_at) monthDay, count(*) count');
                break;
            case 'rejected-posts':
                $adminData = Article::withTrashed()
                    ->whereBetween('deleted_at', $range)
                    ->where('deleter_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(deleted_at) monthDay, count(*) count');
                break;
            case 'updated-comments':
                $adminData = Comment::withTrashed()
                    ->whereBetween('updated_at', $range)
                    ->where('updater_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(updated_at) monthDay, count(*) count');
                break;
            case 'deleted-comments':
                $adminData = Comment::withTrashed()
                    ->whereBetween('deleted_at', $range)
                    ->where('deleter_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(deleted_at) monthDay, count(*) count');
                break;
            case 'accepted-users':
                $adminData = User::withTrashed()
                    ->whereBetween('accepted_at', $range)
                    ->where('accepter_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(accepted_at) monthDay, count(*) count');
                break;
            case 'rejected-users':
                $adminData = User::withTrashed()
                    ->whereBetween('rejected_at', $range)
                    ->where('rejecter_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(rejected_at) monthDay, count(*) count');
                break;
            case 'deleted-users':
                $adminData = User::withTrashed()
                    ->whereBetween('deleted_at', $range)
                    ->where('deleter_id', $admin->id)
                    ->selectRaw('DAYOFMONTH(deleted_at) monthDay, count(*) count');
                break;
        }

        $adminData = $adminData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $adminData
        ]);
    }

    public function profileUser(StatisticsRequest $request, User $user)
    {
        $label = $request->user_label;

        if (!$label) {
            abort(404);
        }

        $userData = collect(new User);
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'taken-quizzes':
                $userData = ArticleView::whereBetween('created_at', $range)
                    ->where('user_id', $user->id)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'finished-quizzes':
                $userData = ArticleView::whereBetween('created_at', $range)
                    ->where('user_id', $user->id)
                    ->where('is_finished', true)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'posts-viewed':
                $userData = ArticleView::whereBetween('created_at', $range)
                    ->where('user_id', $user->id)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, SUM(count_views) count');
                break;
            case 'comments-made':
                $userData = Comment::withTrashed()
                    ->whereBetween('created_at', $range)
                    ->where('user_id', $user->id)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'posts-created':
                $userData = Article::withTrashed()
                    ->whereBetween('created_at', $range)
                    ->where('author_id', $user->id)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
        }

        $userData = $userData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    public function articleComments(StatisticsRequest $request, Article $article)
    {
        $label = $request->comments_label;

        if (!$label) {
            abort(404);
        }

        $commentsData = collect(new Comment);
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'all':
                $commentsData = Review::where('article_id', $article->id)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'great':
                $commentsData = Review::where('article_id', $article->id)
                    ->where('rating', 2)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'good':
                $commentsData = Review::where('article_id', $article->id)
                    ->where('rating', 2)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'normal':
                $commentsData = Review::where('article_id', $article->id)
                    ->where('rating', 3)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'poor':
                $commentsData = Review::where('article_id', $article->id)
                    ->where('rating', 2)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'bad':
                $commentsData = Review::where('article_id', $article->id)
                ->where('rating', 1)
                ->whereBetween('created_at', $range)
                ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
        }

        $commentsData = $commentsData->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $commentsData
        ]);
    }

    public function articleViewsDownloads(StatisticsRequest $request, Article $article)
    {
        $label = $request->article_views_downloads_label;

        if (!$label) {
            abort(404);
        }

        $data = collect([]);
        $range = [Carbon::now()->startOfMonth(), Carbon::now()];

        switch ($label) {
            case 'views':
                $data = ArticleView::where('article_id', $article->id)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, SUM(count_views) count');
                break;
            case 'finished-quiz':
                $data = ArticleView::where('article_id', $article->id)
                    ->whereBetween('created_at', $range)
                    ->where('is_finished', true)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, count(*) count');
                break;
            case 'downloads':
                $data = ArticleDownload::where('article_id', $article->id)
                    ->whereBetween('created_at', $range)
                    ->selectRaw('DAYOFMONTH(created_at) monthDay, SUM(count_downloads) count');
                break;
        }

        $data = $data->groupBy('monthDay')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function articleAnswers(Article $article)
    {
        $correct_count = $article->answers->where('is_correct', true)->count();
        $incorrect_count = $article->answers->where('is_correct', false)->count();

        return response()->json([
            'success' => true,
            'data' => [$correct_count, $incorrect_count]
        ]);
    }

    public function articlesMore(StatisticsRequest $request)
    {
        $preferenceId = $request->preferenceId;
        $lego_count = $demo_count = $video_count = $audio_count = 0;

        if (!$preferenceId) {
            $lego_count = Article::where('type', 'LEG')->count();
            $demo_count = Article::where('type', 'DEM')->count();
            $video_count = Article::where('type', 'VID')->count();
            $audio_count = Article::where('type', 'AUD')->count();
        } else {
            $preference = Preference::where('slug', $preferenceId)->first();
            $lego_count = $preference->articles->where('type', 'LEG')->count();
            $demo_count = $preference->articles->where('type', 'DEM')->count();
            $video_count = $preference->articles->where('type', 'VID')->count();
            $audio_count = $preference->articles->where('type', 'AUD')->count();
        }
        
        return response()->json([
            'success' => true,
            'labels' => ['Lego', 'Demo', 'Video', 'Audio'],
            'data' => [$lego_count, $demo_count, $video_count, $audio_count]
        ]);
    }
}
