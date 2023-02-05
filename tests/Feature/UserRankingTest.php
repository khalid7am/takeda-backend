<?php

namespace Tests\Feature;

use App\Helpers\Relevance\RelevanceStoreHelper;
use App\Helpers\Relevance\UserPerformanceStoreHelper;
use App\Jobs\PerformanceRelevance\UserPerformanceStoreJob;
use App\Models\Answers;
use App\Models\Article;
use App\Models\ArticleUserAnswered;
use App\Models\Preference;
use App\Models\Question;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Queue;
use Tests\TestCase;

class UserRankingTest extends TestCase
{
    use DatabaseTransactions;


    public $user;


    public function setUp() :void
    {
        parent::setUp();
        $this->artisan('db:seed');
        $this->user = User::factory()->create();
    }
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_user_performance_attributes_working_zero()
    {
        $performancePoints = $this->user->getUserPerformancePoints();

        $this->assertEquals(0, $performancePoints);
    }

    public function test_user_performance_attributes_working_and_has()
    {
        $givenPoint = 5;

        $articleAnsweredUser = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPoint,
        ]);

        $performancePoints = $this->user->getUserPerformancePoints();

        $this->assertEquals($givenPoint, $performancePoints);
    }

    public function test_user_performance_attributes_working_and_has_multiple()
    {
        $givenPoint = 5;
        $givenPointSecond = 3;

        $articleAnsweredUser = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPoint,
        ]);

        $articleAnsweredUserSecond = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPointSecond,
        ]);

        $performancePoints = $this->user->getUserPerformancePoints();

        $this->assertEquals(($givenPoint + $givenPointSecond), $performancePoints);
    }

    public function test_user_performance_attributes_working_and_has_diff_month()
    {
        $givenPoint = 5;

        $articleAnsweredUser = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPoint,
            'created_at' => Carbon::now()->subMonth(),
        ]);

        $nowDate = Carbon::now()->format('Y-m-d');
        $desiredDate = Carbon::now()->subMonth()->format('Y-m-d');
        $dateNoPoints = Carbon::now()->subMonths(2)->format('Y-m-d');
        $performancePoints = $this->user->getUserPerformancePoints();
        $performancePointsDesiredDate = $this->user->getUserPerformancePoints($desiredDate);
        $performancePointsDesiredDateNoPoint = $this->user->getUserPerformancePoints($dateNoPoints);

        $this->assertEquals($givenPoint, $performancePoints);
        $this->assertEquals($givenPoint, $performancePointsDesiredDate);
        $this->assertEquals(0, $performancePointsDesiredDateNoPoint);
    }

    public function test_user_static_title_attributes_working()
    {
        $staticTitle = $this->user->static_title;

        $firstLevelTitle = config('takeda-ranking.static.0.title');
        $firstLevelColor = config('takeda-ranking.static.0.color');

        $this->assertEquals($firstLevelTitle, $staticTitle);
        //$this->assertContains($firstLevelColor, $staticTitle);
    }

    public function test_user_static_title_attributes_working_another_level()
    {
        $givenLevelTitle = '';
        $givenLevelColor = '';
        $givenPoint = rand(1, 205);
        $articleAnsweredUser = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPoint,
        ]);

        $staticTitle = $this->user->static_title;

        $staticRankings = config('takeda-ranking.static');

        foreach ($staticRankings as $key => $staticRanking) {
            if ($key <= $givenPoint) {
                $givenLevelTitle = $staticRanking['title'];
                $givenLevelColor = $staticRanking['color'];
                break;
            }
        }

        $this->assertEquals($givenLevelTitle, $staticTitle);
        //$this->assertContains($givenLevelColor, $staticTitle);
    }

    public function test_user_static_color_attributes_working()
    {
        $staticTitle = $this->user->static_color;


        $firstLevelColor = config('takeda-ranking.static.0.color');


        $this->assertEquals($firstLevelColor, $staticTitle);
    }

    public function test_user_static_color_attributes_working_another_level()
    {
        $givenLevelColor = '';
        $givenPoint = rand(1, 205);
        $articleAnsweredUser = ArticleUserAnswered::factory()->create([
            'user_id' => $this->user->id,
            'point' => $givenPoint,
        ]);

        $staticColor = $this->user->static_color;

        $staticRankings = config('takeda-ranking.static');

        foreach ($staticRankings as $key => $staticRanking) {
            if ($key <= $givenPoint) {
                $givenLevelColor = $staticRanking['color'];
                break;
            }
        }

        $this->assertEquals($givenLevelColor, $staticColor);
        //$this->assertContains($givenLevelColor, $staticTitle);
    }

    public function test_user_current_position_percentage()
    {
        // Need to finalize this dynamic stuff.
        $this->assertTrue(false);
    }
}
