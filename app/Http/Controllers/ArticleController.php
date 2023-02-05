<?php

namespace App\Http\Controllers;

use App\Helpers\Relevance\RelevanceSearchHelper;
use DB;
use App\Models\Review;
use App\Models\Article;
use App\Types\RoleType;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleListRequest;
use App\Http\Requests\ArticleStoreRequest;
use App\Services\QuestionService;
use App\Http\Requests\ArticleReviewStoreRequest;
use App\Http\Resources\ArticleThumbnailResource;
use App\Models\ArticleAsSlide;
use App\Models\ArticleUserAnswered;
use App\Types\ArticleType;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function list(ArticleListRequest $request)
    {
        $type = $request->filter;
        $preferences = $request->tags;
        $count = $request->limit ?? 3;

        if ($preferences) {
            $articles = Article::with('author')->withCount('answers')
            ->addSelect(['avg_review_rating' => Review::selectRaw('AVG(reviews.rating)')
                ->whereColumn('reviews.article_id', 'articles.id')
            ])
            ->whereHas('preferences', function ($query) use ($preferences) {
                $query->whereIn('preferences.id', explode(',', $preferences));
            })
            ->orderByDesc('answers_count')
            ->orderByDesc('avg_review_rating')
            ->take($count)
            ->get();
        } else {
            $articles = Article::with(['author', 'comments', 'reviews', 'preferences'])
                ->when(auth()->user()->role->code == RoleType::EDITOR, function ($query) {
                    return $query->authorIs(auth()->user());
                })->when(! empty($type), function ($query) use ($type) {
                    return $query->type($type);
                })
            ->orderBy('published_at', 'DESC')
            ->take($count)->get();
        }

        if (count($articles) < $count) {
            $articles = $articles->concat(Article::when(! empty($type), function ($query) use ($type) {
                            return $query->type($type);
                        })
                        ->published()
                        ->inRandomOrder()
                        ->whereNotIn('id', $articles->pluck('id'))
                        ->limit($count - count($articles))
                        ->get());
        }

        return ArticleResource::collection($articles);
    }

    public function readArticles(ArticleListRequest $request)
    {
        $type = $request->get('filter');

        $count = $request->get('limit') ?? 100;

        $readArticleIds = ArticleUserAnswered::select('article_id')->userId(auth()->id())->pluck('article_id');


        $articles = Article::query()
            ->whereIn('id', $readArticleIds)
            ->with('author', 'articleUserAnswered')

            // FOR READ ARTICLES,
            // I DON'T THINK IT IS NEEDED TO NARROW DOWN FOR AUTHORS

            // ->when(auth()->user()->role->code == RoleType::EDITOR, function ($query) {
            //     return $query->authorIs(auth()->user());
            // })
            ->when($type, function ($query) use ($type) {
                $query->type($type);
            })
            ->orderBy('published_at', 'DESC')
            ->take($count)
            ->get();

        return ArticleThumbnailResource::collection($articles);
    }

    public function search(ArticleListRequest $request)
    {
        $search = $request->search;
        $words = preg_split('/\s+/', $search, -1);

        $articles = Article::where(function ($query) use ($words) {
            foreach ($words as $word) {
                $query->search($word);
            }
        })->get();

        return ArticleResource::collection($articles);
    }

    public function show(Article $article)
    {
        $article->viewed();
        return new ArticleResource($article);
    }

    public function review(Article $article)
    {
        return new ArticleResource($article);
    }

    public function recommended(ArticleListRequest $request)
    {
        $articles = (new RelevanceSearchHelper)->start()
        ->userId(auth()->id()) //->userId($userId)
        //->searchFor('asd')
        ->get();

        return ArticleResource::collection($articles);
    }

    public function filter(ArticleListRequest $request)
    {
        $filter = $request->get('filter');
        $orderBy = $request->get('order_by');
        $searchTerm = $request->get('search', null);
        $limit = $request->get('limit');

        $articles = Article::query()
                     ->when($searchTerm, fn ($query) => $query->search($searchTerm));

        switch ($filter) {
            case 'LEG':
                $articles->lego();
                break;
            case 'DEM':
                $articles->demo();
                break;
            case 'VID':
                $articles->video();
                break;
            case 'AUD':
                $articles->audio();
                break;
            case 'new':
                $articles->notPublished();
                break;
        }

        if ($orderBy) {
            $articles->orderBy('title', $orderBy);
        } else {
            $articles->orderBy('created_at', 'DESC');
        }

        if ($limit) {
            $articles->take($limit);
        }

        $articles = $articles->get();

        return ArticleResource::collection($articles);
    }

    public function store(ArticleStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $article = Article::create([
                'type' => $request->type,
                'author_id' => auth()->id(),
                'title' => $request->title,
                'excerpt' => $request->excerpt,
                'content' => json_encode($request->get('content')),
                'media' => $request->media ?? null,
                // 'lecturer_id' => User::where('uuid', $request->lecturer)->value('id'),
            ]);

            $article->preferences()->attach(ArticleService::getRelatedPreferences($request->preferences));

            QuestionService::createFromRequestArray($article, $request->questions);

            if ($request->type == ArticleType::DEMO) {
                $articleAsSlide = ArticleAsSlide::createdById(auth()->id())->identifier($request->uniqueSlidesId)->latest()->first();
                $articleAsSlide->update(['article_id' => $article->id]);
                $articleAsSlide->slideImages()->update(['article_id' => $article->id]);
            }

            // $pptName = $request->get('pptName');
            // $questionsArray = $request->get('questionsArray');
            // if ($questionsArray && $pptName) {
            //     $articleAsSlide = (new ArticlePptHelper)->saveMultipleArticleQuestionAsNewSlide($questionsArray, $pptName, $article->id, auth()->id());
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'article_slug' => $article->slug,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            abort(500);
        }
    }

    public function accept(Article $article)
    {
        $article->update([
            'published_at' => now(),
            'publisher_id' => auth()->id(),
            'deleted_at' => null,
            'deleter_id' => null,
        ]);

        return response()->noContent();
    }

    public function reject(Article $article)
    {
        $article->update([
            'deleted_at' => now(),
            'deleter_id' => auth()->id(),
            'published_at' => null,
            'publisher_id' => null,
        ]);

        return response()->noContent();
    }

    public function delete(Article $article)
    {
        $article->comments()->delete();
        $article->questions()->delete();
        $article->reviews()->delete();
        $article->answers()->delete();
        $article->views()->delete();
        $article->downloads()->delete();
        $article->delete();

        return response()->noContent();
    }

    public function update(Article $article, ArticleStoreRequest $request)
    {
        $article->update([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => json_encode($request->content),
            'media' => $request->media ?? null,
            // 'lecturer_id' => User::where('uuid', $request->lecturer)->value('id'),
            'published_at' => now(),
            'publisher_id' => auth()->id(),
            'deleted_at' => null,
            'deleter_id' => null,
        ]);
        
        $article->preferences()->sync($request->preferences);

        QuestionService::createFromRequestArray($article, $request->questions);

        return response()->noContent();
    }

    public function storeReview(ArticleReviewStoreRequest $request)
    {
        Review::create([
            'article_id' => Article::where('uuid', $request->get('article_uuid'))->first()->getKey(),
            'comment' => $request->get('comment'),
            'rating' => $request->get('rating'),
            'user_id' => auth()->id()
        ]);
        return response()->noContent();
    }

    public function finished(Article $article)
    {
        $article->finished();
        return response()->noContent();
    }

    public function downloaded(Article $article)
    {
        $article->downloaded();
        return response()->noContent();
    }

    public function updateEditor(Article $article, User $user)
    {
        if (!$user->is_author_or_admin) {
            return response()->json('Editor is not an author!', 400);
        }
        
        $article->update([
            'author_id' => $user->id,
        ]);

        return response()->noContent();
    }

    public function updateLecturer(Article $article, User $user)
    {
        if (!$user->is_lecturer) {
            return response()->json('Only lecturers are accepted!', 400);
        }
        
        $article->update([
            'lecturer_id' => $user->id,
        ]);

        return response()->noContent();
    }
}
