<?php

namespace Tests\Feature;

use App\Helpers\Relevance\ArticleAnsweredCorrectUserStoreHelper;
use App\Helpers\Relevance\RelevanceStoreHelper;
use App\Models\Answers;
use App\Models\Article;
use App\Models\Preference;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleAnsweredCorrectUserHelperTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() :void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_article_answered_correct_user_will_be_saved()
    {
        $userId = User::factory()->create()->value('id');

        $articles = Article::factory()
            ->count(10)
            ->create()->each(function ($article) {
                $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
            });

        $article = $articles->first();

        $articlePreferenceIds = $article->preferences->pluck('id');

        $returnValue = (new ArticleAnsweredCorrectUserStoreHelper)->start()
            ->userId($userId)
            ->articleId($article->id)
            ->send();


        //$this->assertEquals($article, $returnValue);

        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
        ]);
    }

    // public function test_article_answered_correct_user_will_be_saved_by_correct_answer()
    // {
    //     $userId = User::factory()->create()->value('id');

    //     $articles = Article::factory()
    //         ->count(3)
    //         ->create()->each(function ($article) {
    //             $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
    //         });

    //     $desiredArticle = $articles->first();

    //     $questions = Question::factory()->count(5)->create([
    //         'article_id' => $desiredArticle->id,
    //     ]);

    //     $desiredQuestion = $questions->first();

    //     $answers = Answers::factory()->count(300)->create([
    //         'user_id' => $userId,
    //         'question_id' => $desiredQuestion->id,
    //         'is_correct' => 1,
    //     ]);

    //     $firstGoodAnswer = $answers->where('is_correct', 1)->first();

    //     $storedArticleId = $firstGoodAnswer->question->article_id;



    //     $this->assertDatabaseHas('article_user_answereds', [
    //         'user_id' => $userId,
    //         'article_id' => $storedArticleId,


    //     ]);
    // }

    public function test_article_answered_not_correct_user_not_saved()
    {
        $userId = User::factory()->create()->value('id');

        $articles = Article::factory()
            ->count(3)
            ->create()->each(function ($article) {
                $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
            });

        $desiredArticle = $articles->first();

        $questions = Question::factory()->count(5)->create([
            'article_id' => $desiredArticle->id,
        ]);

        $desiredQuestion = $questions->first();

        $answers = Answers::factory()->count(300)->create([
            'user_id' => $userId,
            'question_id' => $desiredQuestion->id,
            'is_correct' => 0,
        ]);

        $firstBadAnswer = $answers->where('is_correct', 0)->first();

        $storedArticleId = $firstBadAnswer->question->article_id;



        $this->assertDatabaseMissing('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $storedArticleId,
        ]);
    }
}
