<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    Route::group(['middleware' => ['guest']], function () {
        Route::post('login', \App\Http\Controllers\Auth\LoginController::class)->name('login');
        Route::post('register', \App\Http\Controllers\Auth\RegisterController::class)->name('register');
        Route::post('reset-password/request', [\App\Http\Controllers\Auth\PasswordResetController::class, 'request'])->name('reset-password.request');
        Route::post('reset-password/check-token', [\App\Http\Controllers\Auth\PasswordResetController::class, 'checkToken'])->name('reset-password.request');
        Route::post('reset-password/save-new-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('reset-password.request');
        Route::get('preferences/list', [\App\Http\Controllers\PreferenceController::class, 'list'])->name('list');

        Route::post('admin/login', \App\Http\Controllers\Auth\Admin\LoginController::class)->name('login');
    });

    Route::post('register/{user}/author', \App\Http\Controllers\Auth\RegisterAuthorController::class)->name('register.author');

    Route::group(['middleware' => ['auth:sanctum', 'user.accepted']], function () {
        Route::group(['as' => 'user.', 'prefix' => 'user'], function () {
            Route::get('me', [\App\Http\Controllers\UserController::class,'me'])->name('me');
            Route::post('update/preferences', [\App\Http\Controllers\UserController::class,'updatePreferences'])->name('update.preferences');
            Route::post('update', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
            Route::get('results', [\App\Http\Controllers\UserController::class,'userResults'])->name('userResults');
            Route::get('author/{user}/information', [\App\Http\Controllers\UserController::class, 'authorInformation'])->name('author.information');

            Route::post('logout', \App\Http\Controllers\Auth\LogoutController::class)->name('logout');
            
            Route::group(['middleware' => 'notuser'], function () {
                Route::get('rating', [\App\Http\Controllers\RatingController::class, 'rating'])->name('rating');
                Route::get('rating/articles', [\App\Http\Controllers\RatingController::class, 'articles'])->name('rating.articles');
            });
        });

        Route::group(['as' => 'users.', 'prefix' => 'users', 'middleware' => 'admin'], function () {
            Route::get('list', [\App\Http\Controllers\UserController::class, 'list'])->name('list');
            Route::get('list/new', [\App\Http\Controllers\UserController::class, 'new'])->name('new');
            Route::get('lecturers', [\App\Http\Controllers\UserController::class, 'lecturers'])->name('lecturers');
            Route::get('{user}/show', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
            Route::post('{user}/accept', [\App\Http\Controllers\UserController::class, 'accept'])->name('accept');
            Route::post('{user}/reject', [\App\Http\Controllers\UserController::class, 'reject'])->name('reject');
            Route::post('{user}/pend', [\App\Http\Controllers\UserController::class, 'pend'])->name('pend');
            Route::delete('{user}/delete', [\App\Http\Controllers\UserController::class, 'delete'])->name('delete');
            Route::post('{user}/restore', [\App\Http\Controllers\UserController::class, 'restore'])->name('restore');
            Route::get('{user}/status', [\App\Http\Controllers\UserController::class, 'status'])->name('status');
            Route::get('{user}/articles', [\App\Http\Controllers\UserController::class, 'articles'])->name('articles');
            Route::get('{user}/comments', [\App\Http\Controllers\UserController::class, 'comments'])->name('comments');
            Route::get('search', [\App\Http\Controllers\UserController::class, 'search'])->name('search');

            Route::group(['middleware' => ['user.role:'.\App\Types\RoleType::SUPERADMIN]], function () {
                Route::post('{user}/update-role/{role}', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('updateRole');
            });
        });
        
        Route::group(['as' => 'preferences.', 'prefix' => 'preferences'], function () {
            Route::get('{preference}/articles', [\App\Http\Controllers\PreferenceController::class, 'articles'])->name('articles');
            Route::get('{preference}/slug-quizzes', [\App\Http\Controllers\PreferenceController::class,'listPreferenceQuizzes']);

            Route::group(['middleware' => 'admin'], function () {
                Route::get('all', [\App\Http\Controllers\PreferenceController::class, 'all'])->name('all');
                Route::post('create', [\App\Http\Controllers\PreferenceController::class, 'create'])->name('create');
                Route::post('update/{preference}/name', [\App\Http\Controllers\PreferenceController::class, 'updateName'])->name('update.name');
                Route::post('update/{preference}/image', [\App\Http\Controllers\PreferenceController::class, 'updateImage'])->name('update.image');
                Route::delete('{preference}/delete', [\App\Http\Controllers\PreferenceController::class, 'delete'])->name('delete');
                Route::get('related', [\App\Http\Controllers\PreferenceController::class, 'related'])->name('related');
                Route::post('related/update', [\App\Http\Controllers\PreferenceController::class, 'updateRelated'])->name('update.related');
                Route::post('related/{preference}/delete', [\App\Http\Controllers\PreferenceController::class, 'deleteRelated'])->name('delete.related');
            });
        });

        Route::group(['as' => 'comments.', 'prefix' => 'comments'], function () {
            Route::get('{article}/list', [\App\Http\Controllers\CommentController::class, 'show'])->name('article');
            Route::post('create', [\App\Http\Controllers\CommentController::class, 'store'])->name('create');
            Route::post('{comment}/update', [\App\Http\Controllers\CommentController::class, 'update'])->name('update');
            Route::delete('{comment}/delete', [\App\Http\Controllers\CommentController::class, 'delete'])->name('delete');

            Route::group(['middleware' => 'admin'], function () {
                Route::get('list', [\App\Http\Controllers\CommentController::class, 'list'])->name('list');
            });
        });

        Route::group(['as' => 'articles.', 'prefix' => 'articles'], function () {
            Route::get('{article}/show', [\App\Http\Controllers\ArticleController::class, 'show'])->name('show');
            Route::get('{article}/review', [\App\Http\Controllers\ArticleController::class, 'review'])->name('review');
            Route::get('list', [\App\Http\Controllers\ArticleController::class, 'list'])->name('list');
            Route::get('read-articles', [\App\Http\Controllers\ArticleController::class, 'readArticles'])->name('readArticles');

            Route::get('search', [\App\Http\Controllers\ArticleController::class, 'search'])->name('search');
            Route::post('create', [\App\Http\Controllers\ArticleController::class, 'store'])->name('create');
            Route::post('create/ppt', [\App\Http\Controllers\DemoArticleController::class, 'storePpt'])->name('create.ppt');
            Route::post('create/demo/slide', [\App\Http\Controllers\DemoArticleController::class, 'storeSlide'])->name('create.slide');
            Route::post('update/demo/slide', [\App\Http\Controllers\DemoArticleController::class, 'updateSlide'])->name('update.slide');
            Route::post('delete/demo/slide', [\App\Http\Controllers\DemoArticleController::class, 'deleteSlide'])->name('delete.slide');
            Route::get('{article}/demo/slides', [\App\Http\Controllers\DemoArticleController::class, 'getSlides'])->name('show.slides');
            Route::get('{article}/demo/download', [\App\Http\Controllers\DemoArticleController::class, 'downloadPpt'])->name('download.ppt');
            Route::post('{article}/update', [\App\Http\Controllers\ArticleController::class, 'update'])->name('update');
            Route::delete('{article}/delete', [\App\Http\Controllers\ArticleController::class, 'delete'])->name('delete');
            Route::post('review/store', [\App\Http\Controllers\ArticleController::class,'storeReview'])->name('review.store');
            Route::post('{article}/finished', [\App\Http\Controllers\ArticleController::class, 'finished'])->name('finished');
            Route::post('{article}/downloaded', [\App\Http\Controllers\ArticleController::class, 'downloaded'])->name('downloaded');

            // RECOMMEND SECTION
            Route::get('recommended', [\App\Http\Controllers\ArticleController::class, 'recommended'])->name('recommended');
            Route::get('download-material/audio', [\App\Http\Controllers\FileDownloadController::class, 'downloadAudio']);
            Route::get('download-material/image', [\App\Http\Controllers\FileDownloadController::class, 'downloadImage']);
            
            Route::group(['middleware' => 'admin'], function () {
                Route::get('list/filter', [\App\Http\Controllers\ArticleController::class, 'filter'])->name('filter');
                Route::post('{article}/accept', [\App\Http\Controllers\ArticleController::class, 'accept'])->name('accept');
                Route::post('{article}/reject', [\App\Http\Controllers\ArticleController::class, 'reject'])->name('reject');
                Route::post('{article}/update', [\App\Http\Controllers\ArticleController::class, 'update'])->name('update');
                Route::post('{article}/delete', [\App\Http\Controllers\ArticleController::class, 'delete'])->name('delete');
                Route::post('{article}/{user}/updateEditor', [\App\Http\Controllers\ArticleController::class, 'updateEditor'])->name('updateEditor');
                Route::post('{article}/{user}/updateLecturer', [\App\Http\Controllers\ArticleController::class, 'updateLecturer'])->name('updateLecturer');
            });
        });

        Route::group(['as' => 'blogs.', 'prefix' => 'blogs'], function () {
            Route::get('list', [\App\Http\Controllers\BlogArticleController::class, 'list'])->name('list');
        });

        Route::post('questions/answer', [\App\Http\Controllers\AnswersController::class,'answer']);
        Route::post('newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class,'subscribe']);
        Route::post('newsletter/unsubscribe', [\App\Http\Controllers\NewsletterController::class,'unsubscribe']);
        Route::get('disclaimer/show', [\App\Http\Controllers\PageController::class, 'getDisclaimer']);

        Route::group(['as' => 'file-upload.','prefix' => 'file-upload'], function () {
            Route::post('image', [\App\Http\Controllers\FileUploadController::class, 'image'])->name('file-upload.image');
            Route::post('video', [\App\Http\Controllers\FileUploadController::class, 'video'])->name('file-upload.video');
            Route::post('document', [\App\Http\Controllers\FileUploadController::class, 'document'])->name('file-upload.document');
            Route::post('any', [\App\Http\Controllers\FileUploadController::class, 'any'])->name('file-upload.any');
        });

        // admin routes
        Route::group(['as' => 'admins.', 'prefix' => 'admins', 'middleware' => 'admin'], function () {
            Route::get('me', [\App\Http\Controllers\AdminController::class,'me'])->name('me');
            Route::post('{user}/update', [\App\Http\Controllers\AdminController::class, 'update'])->name('update.user');
            Route::get('list', [\App\Http\Controllers\AdminController::class,'list'])->name('list');
            Route::get('editors/list', [\App\Http\Controllers\AdminController::class,'editors'])->name('list.editors');
            Route::get('{admin}/show', [\App\Http\Controllers\AdminController::class,'show'])->name('show');
            Route::get('{admin}/activity/log', [\App\Http\Controllers\AdminController::class,'activityLog'])->name('activity.log');
            Route::get('{user}/activity/user', [\App\Http\Controllers\AdminController::class,'activityUser'])->name('activity.user');
            Route::get('{admin}/sessions', [\App\Http\Controllers\AdminController::class,'sessions'])->name('sessions');
            Route::get('all', [\App\Http\Controllers\AdminController::class,'all'])->name('all');
            Route::post('logout', \App\Http\Controllers\Auth\Admin\LogoutController::class)->name('logout');

            Route::group(['middleware' => ['user.role:'.\App\Types\RoleType::SUPERADMIN]], function () {
                Route::post('{user}/update/admin', [\App\Http\Controllers\AdminController::class, 'updateAdmin'])->name('update.admin');
            });
        });

        Route::group(['middleware' => 'admin'], function () {
            Route::post('disclaimer/update', [\App\Http\Controllers\PageController::class, 'updateDisclaimer'])->name('disclaimer.update');
        });

        Route::group(['as' => 'statistics.', 'prefix' => 'statistics', 'middleware' => 'admin'], function () {
            Route::get('users', [\App\Http\Controllers\StatisticsController::class,'users'])->name('users');
            Route::get('articles', [\App\Http\Controllers\StatisticsController::class,'articles'])->name('articles');
            Route::get('articletypes', [\App\Http\Controllers\StatisticsController::class,'articleTypes'])->name('article.types');
            Route::get('comments', [\App\Http\Controllers\StatisticsController::class,'comments'])->name('comments');
            Route::get('articles-more', [\App\Http\Controllers\StatisticsController::class,'articlesMore'])->name('article.more');
            Route::get('profile/{admin}/admin', [\App\Http\Controllers\StatisticsController::class,'profileAdmin'])->name('profile.admin');
            Route::get('profile/{user}/user', [\App\Http\Controllers\StatisticsController::class,'profileUser'])->name('profile.user');
            Route::get('article/{article}/comments', [\App\Http\Controllers\StatisticsController::class,'articleComments'])->name('article.comments');
            Route::get('article/{article}/views-and-downloads', [\App\Http\Controllers\StatisticsController::class,'articleViewsDownloads'])->name('article.views-and-downloads');
            Route::get('article/{article}/answers', [\App\Http\Controllers\StatisticsController::class,'articleAnswers'])->name('article.answers');
            
            Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
                Route::get('articles-by-types', [\App\Http\Controllers\AdminStatisticsController::class,'articlesByTypes'])->name('articlesByTypes');
                Route::get('preferences-by-usage', [\App\Http\Controllers\AdminStatisticsController::class,'preferencesByUsage'])->name('preferencesByUsage');
                Route::get('top-ten-user-by-performance-points', [\App\Http\Controllers\AdminStatisticsController::class,'topTenUserByPerformancePoint'])->name('topTenUserByPerformancePoint');
                Route::get('top-ten-user-by-answer-accuraty', [\App\Http\Controllers\AdminStatisticsController::class,'topTenUserByAnswerAccuraty'])->name('topTenUserByAnswerAccuraty');
            });
        });
    });
});
