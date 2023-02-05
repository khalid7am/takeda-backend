<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Preference;
use App\Models\RelevancePoint;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class RelevancePointFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RelevancePoint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first(),
            'preference_id' => Preference::inRandomOrder()->first(),
            'point' => rand(1, 10),
        ];
    }
}
