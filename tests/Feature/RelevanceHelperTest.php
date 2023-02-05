<?php

namespace Tests\Feature;

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

class RelevanceStoreHelperTest extends TestCase
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
    public function test_relevance_will_be_saved()
    {
        $userId = User::factory()->create()->value('id');

        $articles = Article::factory()
            ->count(10)
            ->create()->each(function ($article) {
                $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
            });

        $article = $articles->first();

        $articlePreferenceIds = $article->preferences->pluck('id');

        $returnValue = (new RelevanceStoreHelper)->start()
            ->userId($userId)
            ->articleId($article->id)
            ->send();


        $this->assertEquals('ok', $returnValue);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => 1
        ]);
    }

    public function test_relevance_will_be_incremented()
    {
        $userId = User::factory()->create()->value('id');

        $articles = Article::factory()
            ->count(10)
            ->create()->each(function ($article) {
                $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
            });

        $article = $articles->first();

        $articlePreferenceIds = $article->preferences->pluck('id');

        $testedPreferenceId = $articlePreferenceIds->shuffle()->first();

        $returnValue = (new RelevanceStoreHelper)->start()
            ->userId($userId)
            ->articleId($article->id)
            ->send();

        $testedArticle = Article::with('preferences')->whereHas('preferences', function ($query) use ($testedPreferenceId) {
            $query->where('preference_id', $testedPreferenceId);
        })->first();



        $incrementedReturnValue = (new RelevanceStoreHelper)->start()
        ->userId($userId)
        ->articleId($testedArticle->id)
        ->send();


        $this->assertEquals('ok', $returnValue);
        $this->assertEquals('ok', $incrementedReturnValue);


        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $testedPreferenceId,
            'point' => 2
        ]);
    }


    // public function test_relevance_will_be_saved_on_answer_save()
    // {
    //     $userId = User::factory()->create()->value('id');



    //     $articles = Article::factory()
    //         ->count(1)
    //         ->create()->each(function ($article) {
    //             $article->preferences()->sync(Preference::factory()->count(3)->create()->pluck('id'));
    //         });


    //     $article = $articles->first();
    //     $questions = Question::factory()->count(3)->create([
    //         'article_id' => $article->id,
    //     ]);

    //     $answer = Answers::factory()->count(1)->create([
    //         'user_id' => $userId,
    //         'question_id' => $questions->first()->id,
    //         'is_correct' => 1,
    //     ]);

    //     $articlePreferenceIds = $article->preferences->pluck('id');

    //     // $returnValue = (new RelevanceStoreHelper)->start()
    //     //     ->userId($userId)
    //     //     ->articleId($article->id)
    //     //     ->send();


    //     // $this->assertEquals('ok', $returnValue);

    //     $this->assertDatabaseHas('relevance_points', [
    //         'user_id' => $userId,
    //         'preference_id' => $articlePreferenceIds->shuffle()->first(),
    //         'point' => 1
    //     ]);
    // }
}
