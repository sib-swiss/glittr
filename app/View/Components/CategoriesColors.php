<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Mexitek\PHPColors\Color;

class CategoriesColors extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {


    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        /**
         * List of categories colors for custom css injection.
         */
        $categories_colors = Cache::tags('categories')->rememberForever('categories_colors', function () {
            $values = [];
            foreach (Category::all() as $cat) {
                $color = new Color($cat->color);
                while (! $color->isDark()) {
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

        return view('components.categories-colors', compact('categories_colors'));
    }
}
