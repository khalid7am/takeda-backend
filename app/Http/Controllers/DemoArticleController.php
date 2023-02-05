<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Types\ArticleType;
use App\Helpers\PPT\ArticlePptHelper;
use App\Http\Requests\ArticleSlideStoreRequest;
use App\Http\Requests\ArticlePptStoreRequest;
use App\Http\Resources\ArticleDemoSliderResource;
use App\Http\Resources\DemoSlideResource;

class DemoArticleController extends Controller
{
    public function storePpt(ArticlePptStoreRequest $request)
    {
        $pptHelper = (new ArticlePptHelper)->uploadPpt($request);
        return ArticleDemoSliderResource::make($pptHelper);
    }

    public function storeSlide(ArticleSlideStoreRequest $request)
    {
        $pptHelper = (new ArticlePptHelper)->saveArticleQuestionAsNewSlide(
                $request->question,
                $request->order,
                $request->identifier,
                $request->question_uuid,
                auth()->id());
        return ArticleDemoSliderResource::make($pptHelper);
    }

    public function updateSlide(ArticleSlideStoreRequest $request)
    {
        $pptHelper = (new ArticlePptHelper)->updateQuestionSlideFromArticle(
            $request->question,
            $request->identifier,
            $request->question_uuid,
            auth()->id());
        return ArticleDemoSliderResource::make($pptHelper);
    }

    public function deleteSlide(ArticleSlideStoreRequest $request)
    {
        $pptHelper = (new ArticlePptHelper)->deleteQuestionSlideFromArticle(
            $request->identifier,
            $request->question_uuid,
            auth()->id());
        return ArticleDemoSliderResource::make($pptHelper);
    }

    public function getSlides(Article $article)
    {
        if ($article->type !== ArticleType::DEMO) {
            abort(404);
        }

        $slides = $article->slideImages;
        return DemoSlideResource::collection($slides);
    }

    public function downloadPpt(Article $article)
    {
        if ($article->type !== ArticleType::DEMO) {
            abort(404);
        }

        $filename = $article->title.'.pdf';
        $articleAsSlide = $article->articleAsSlides->whereNotNull('article_id')->first();
        $articleAsSlide->load('slideImages');

        $pptHelper = (new ArticlePptHelper)->downloadPpt(
            $articleAsSlide->slideImages,
            $filename);

        if (!$pptHelper) {
            abort(500);
        }
        
        $headers = array(
            'Content-Type: application/pdf',
        );

        return response()->download(storage_path($filename), $filename, $headers)
            ->deleteFileAfterSend(true);
    }
}
