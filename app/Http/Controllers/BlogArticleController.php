<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\BlogArticleResource;
use App\Models\Article;
use App\Models\BlogArticle;

class BlogArticleController extends Controller
{
    public function list(Request $request)
    {
        $articles = BlogArticle::latest()->paginate(18);

        return BlogArticleResource::collection($articles);
    }
}
