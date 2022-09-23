<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Mexitek\PHPColors\Color;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $categories_colors = Cache::rememberForever('categories_colors', function() {
            $values = [];
            foreach (Category::all() as $cat) {
                $color = new Color($cat->color);
                while(!$color->isDark()) {
                    $color = new Color($color->darken());
                }
                $values[$cat->id] = [
                    'hex' => $cat->color,
                    'rgb' => $color->getRgb(),
                    'isDark' => $color->isDark(),
                ];
            }
            return $values;
        });
        View::share('categories_colors', $categories_colors);
    }
}
