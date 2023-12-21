<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class LogEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model' => $this->faker->text($maxNbChars = 7),
            'route' => $this->faker->text($maxNbChars = 5),
            'original_data'  => Str::random(10),
            'new_data'  => Str::random(10),
            'ip_address' => '127.0.0.1',
            'remote_user_id' => $this->faker->randomDigit(),
            'user_id' => 1
        ];
    }
}
