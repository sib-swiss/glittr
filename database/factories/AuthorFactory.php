<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'api' => '',
            'display_name' => fake()->name(),
            'location' => fake()->optional(0.4)->city(),
            'bio' => fake()->optional(0.2)->text(),
            'url' => fake()->optional(0.9)->url(),
            'website' => fake()->optional(0.6)->url(),
        ];
    }
}
