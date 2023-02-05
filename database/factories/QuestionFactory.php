<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Answers;
use App\Models\Article;
use App\Models\Preference;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'article_id' => Article::factory(),
            'question' => $this->faker->text,
            //'show_at' => Carbon::now(), ?? INT ??
            'order' => rand(1, 6),
        ];
    }
}
