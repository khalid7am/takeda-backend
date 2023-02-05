<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Article;

use App\Models\User;
use App\Types\ArticleType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        // const DEMO = 'DEM';
        // const LEGO = 'LEG';
        // const VIDEO = 'VID';
        // const AUDIO = 'AUD';
        $types = [
            ArticleType::AUDIO,
            ArticleType::DEMO,
            ArticleType::LEGO,
            ArticleType::VIDEO
        ];
        $title = $this->faker->jobTitle;

        return [
            'uuid' => $this->faker->uuid,
            'type' => array_rand($types),
            'author_id' => User::inRandomOrder()->first(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->title,
            'content' => $this->faker->paragraph,
            //'media' => ' ',
            'publisher_id' => User::inRandomOrder()->first(),
            'published_at' => Carbon::now()->subDay(),
        ];
    }
}
