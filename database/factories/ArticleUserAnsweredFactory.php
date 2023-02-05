<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleUserAnswered;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleUserAnsweredFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArticleUserAnswered::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'article_id' => Article::factory(),
            'point' => rand(1, 1000),
        ];
    }
}
