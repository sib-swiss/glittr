<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Test DEV',
            'email' => 'test@example.com',
            'password' => Hash::make('test123456'),
        ]);

        // Main tags and categories
        $tag_categories = Config::get('tags.default', []);
        foreach ($tag_categories as $cat_name => $cat_data) {
            $category = Category::create([
                'name' => $cat_name,
                'color' => $cat_data['color'] ?? '#ae191a',
            ]);
            // Attach tags
            if (isset($cat_data['tags']) && ! empty($cat_data['tags'])) {
                $category->tags()->saveMany(
                    collect($cat_data['tags'])->map(function ($tag) {
                        return new Tag(['name' => $tag]);
                    })
                );
            }
        }
    }
}
