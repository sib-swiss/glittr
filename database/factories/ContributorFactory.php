<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contributor>
 */
class ContributorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'remote_id' => strval(fake()->unique()->randomNumber(8)),
            'api' => 'github',
            'username' => fake()->userName(),
            'full_name' => fake()->optional(0.7)->name(),
            'profile_url' => fake()->url(),
            'avatar_url' => fake()->optional(0.8)->imageUrl(),
            'company' => null,
            'orcid' => null,
            'orcid_fetched_at' => null,
        ];
    }
}
