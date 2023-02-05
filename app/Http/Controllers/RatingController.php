<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RatingResource;
use App\Http\Resources\RatingArticleResource;
use App\Http\Requests\ArticleListRequest;

class RatingController extends Controller
{
    public function rating()
    {
        return new RatingResource(auth()->user());
    }

    public function articles(ArticleListRequest $request)
    {
        $filter = $request->get('rating_filter');

        $user = auth()->user();

        $articles = $user->articles();
        
        switch ($filter) {
            case 'popular':
                $articles = $articles->withCount('answers')
                    ->orderByDesc('answers_count')
                    ->get()
                    ->sortByDesc('reviewAverage');
                break;
            case 'a_to_z':
                $articles = $articles->orderBy('title')->get();
                break;
            case 'by_topic':
                $articles = $articles->with('preferences', function ($query) {
                    $query->orderBy('name', 'DESC');
                })->get();
                break;
            case 'by_type':
                $articles = $articles->orderBy('type')->get();
                break;
            default:
                $articles = $articles->orderBy('created_at', 'DESC')->get();
                break;
        }

        return RatingArticleResource::collection($articles);
    }
}
