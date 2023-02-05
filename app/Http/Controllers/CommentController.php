<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentsListRequest;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentsUpdateRequest;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function list(CommentsListRequest $request)
    {
        $preferences = $request->preferences;
        $tag = $request->tag;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $limit = $request->limit;

        $comments = Comment::query();

        if ($preferences) {
            $comments->whereHas('article.preferences', function ($query) use ($preferences) {
                $query->whereIn('preferences.id', explode(',', $preferences));
            });
        }

        if ($tag) {
            $comments->whereHas('article.preferences', function ($query) use ($tag) {
                $query->where('preferences.id', $tag);
            });
        }

        if ($date_from) {
            $from = Carbon::parse($date_from);
            $to = Carbon::parse($date_to) ?? Carbon::today();

            $comments->whereBetween('created_at', [$from, $to]);
        }

        // switch ($timeframe) {
        //     case 'today':
        //         $comments->whereDate('created_at', Carbon::today());
        //         break;
        //     case 'week':
        //         $comments->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        //         break;
        //     case 'month':
        //         $comments->whereMonth('created_at', Carbon::now()->month);
        //         break;
        // }

        if ($limit) {
            $comments->take($limit);
        }
        
        $comments = $comments->get();

        return CommentResource::collection($comments);
    }

    public function show(Article $article)
    {
        return CommentResource::collection($article->comments);
    }

    public function store(CommentStoreRequest $request)
    {
        Comment::create([
            'article_id' => Article::where('uuid', $request->get('article_uuid'))->first()->getKey(),
            'content' => $request->get('comment'),
            'parent_id' => optional(Comment::where('uuid', $request->get('parent_uuid'))->first())->getKey(),
            'user_id' => auth()->id()
        ]);

        return response()->noContent();
    }

    public function update(CommentsUpdateRequest $request, Comment $comment)
    {
        $comment->update($request->validated());
        return CommentResource::make($comment);
    }

    public function delete(Comment $comment)
    {
        $comment->update([
            'deleter_id' => auth()->id(),
        ]);
        $comment->delete();
        
        return response()->noContent();
    }
}
