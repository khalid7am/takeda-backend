<?php

namespace Tests\Feature;

use App\Helpers\Relevance\RelevanceStoreHelper;
use App\Helpers\Relevance\UserPerformanceStoreHelper;
use App\Jobs\PerformanceRelevance\UserPerformanceStoreJob;
use App\Models\Answers;
use App\Models\Article;
use App\Models\Preference;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Queue;
use Tests\TestCase;

class UserPerformanceHelperTest extends TestCase
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

    public function test_user_performance_will_be_saved()
    {
        $userId = User::factory()->create()->value('id');

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();
        $questions = Question::factory()->count(3)->create([
            'article_id' => $article->id,
        ]);

        $answer = Answers::factory()->count(1)->create([
            'user_id' => $userId,
            'question_id' => $questions->first()->id,
            'is_correct' => 1,
        ]);

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreHelper)->start()
            ->articleId($article->id)
            ->userId($userId)
            ->send();

        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
            'point' => 2
        ]);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => 2
        ]);
    }

    public function test_user_performance_will_not_be_saved_if_not_user()
    {
        $user = User::factory()->create();

        $secondUser = User::factory()->create();

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();
        $questions = Question::factory()->count(3)->create([
            'article_id' => $article->id,
        ]);

        $answer = Answers::factory()->count(1)->create([
            'user_id' => $secondUser->id,
            'question_id' => $questions->first()->id,
            'is_correct' => 1,
        ]);

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreHelper)->start()
            ->articleId($article->id)
            ->userId($user->id)
            ->send();

        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseMissing('article_user_answereds', [
            'user_id' => $user->id,
            'article_id' => $article->id,
            'point' => 2
        ]);

        $this->assertDatabaseMissing('relevance_points', [
            'user_id' => $user->id,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => 2
        ]);
    }

    public function test_user_performance_will_be_saved_no_good_answer_just_started()
    {
        $userId = User::factory()->create()->value('id');

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();

        $badAnswerQuestionCount = 3;

        $questions = Question::factory()->count($badAnswerQuestionCount)->create([
            'article_id' => $article->id,
        ]);

        foreach ($questions as $key => $question) {
            $badAnswer = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 0,
            ]);
        }

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreHelper)->start()
            ->articleId($article->id)
            ->userId($userId)
            ->send();

        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
            'point' =>  1,
        ]);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => 1,
        ]);
    }

    public function test_user_performance_will_be_saved_multiple_good_answer()
    {
        $userId = User::factory()->create()->value('id');

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();

        $goodAnswerQuestionCount = 3;

        $questions = Question::factory()->count($goodAnswerQuestionCount)->create([
            'article_id' => $article->id,
        ]);

        foreach ($questions as $key => $question) {
            $goodAnswer = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 1,
            ]);
        }

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreHelper)->start()
            ->articleId($article->id)
            ->userId($userId)
            ->send();

        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
            'point' => $goodAnswerQuestionCount + 1,
        ]);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => $goodAnswerQuestionCount +1,
        ]);
    }

    public function test_user_performance_will_be_saved_multiple_good_answer_not_every_good()
    {
        $userId = User::factory()->create()->value('id');

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();

        $goodAnswerQuestionCount = 3;

        $questions = Question::factory()->count($goodAnswerQuestionCount)->create([
            'article_id' => $article->id,
        ]);

        foreach ($questions as $key => $question) {
            $goodAnswer = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 1,
            ]);
        }

        for ($i=0; $i < 2; $i++) {
            $badAnswers = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 0,
            ]);
        }

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreHelper)->start()
            ->articleId($article->id)
            ->userId($userId)
            ->send();

        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
            'point' => $goodAnswerQuestionCount + 1,
        ]);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => $goodAnswerQuestionCount +1,
        ]);
    }

    public function test_user_performance_will_be_saved_multiple_good_answer_not_every_good_with_job()
    {
        Queue::fake();

        $userId = User::factory()->create()->value('id');

        $preferences = Preference::factory()->count(3)->create();

        $articles = Article::factory()
            ->count(1)
            ->create()->each(function ($article) use ($preferences) {
                $article->preferences()->sync($preferences->pluck('id'));
            });


        $article = $articles->first();

        $goodAnswerQuestionCount = 3;

        $questions = Question::factory()->count($goodAnswerQuestionCount)->create([
            'article_id' => $article->id,
        ]);

        foreach ($questions as $key => $question) {
            $goodAnswer = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 1,
            ]);
        }

        for ($i=0; $i < 2; $i++) {
            $badAnswers = Answers::factory()->count(1)->create([
                'user_id' => $userId,
                'question_id' => $question->id,
                'is_correct' => 0,
            ]);
        }

        $articlePreferenceIds = $article->preferences->pluck('id');


        $userPerformanceStoreHelper = (new UserPerformanceStoreJob($userId, $article->id))->handle();



        // $this->assertEquals('ok', $returnValue);
        $this->assertDatabaseHas('article_user_answereds', [
            'user_id' => $userId,
            'article_id' => $article->id,
            'point' => $goodAnswerQuestionCount + 1,
        ]);

        $this->assertDatabaseHas('relevance_points', [
            'user_id' => $userId,
            'preference_id' => $articlePreferenceIds->shuffle()->first(),
            'point' => $goodAnswerQuestionCount +1,
        ]);
    }
}
