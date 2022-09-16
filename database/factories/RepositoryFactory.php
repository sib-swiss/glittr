<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Repository>
 */
class RepositoryFactory extends Factory
{
    public static $validRepositories = [
        'https://github.com/carpentries-incubator/python-interactive-data-visualizations',
        'https://github.com/PlantsAndPython/PlantsAndPython',
        'https://github.com/ISUgenomics/bioinformatics-workbook',
        'https://gitlab.com/mperalc/bulk_RNA-seq_workshop_2021',
        'https://github.com/sib-swiss/NGS-variants-training/',
    ];

    public static $invalidRepositories = [
        'https://github.com/test/notworking',
        'https://gitlab.com/test/notworking',
    ];

    /**
     * Get a random repository url
     **/
    public static function getRandomRepositories(bool $valid = true): string
    {
        $collection = collect($valid ? self::$validRepositories : self::$invalidRepositories);

        return $collection->random();
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => fake()->url(),
            'website' => fake()->optional(0.9)->url(),
            'stargazers' => fake()->numberBetween(0, 4000),
            'enabled' => fake()->boolean(95),
            'last_push' => fake()->optional(0.9)->dateTimeThisYear(),
            'refreshed_at' => fake()->dateTimeThisMonth(),
        ];
    }

    public function enabled(bool $enabled = true): Factory
    {
        return $this->state(function (array $attritubes) use($enabled) {
            return [
                'enabled' => $enabled,
            ];
        });
    }

    public function valid(bool $valid = true): Factory
    {
        return $this->state(function (array $attritubes) use($valid) {
            return [
                'url' => self::getRandomRepositories($valid),
            ];
        });
    }

}
