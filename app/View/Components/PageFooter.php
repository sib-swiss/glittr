<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class PageFooter extends Component
{

    /**
     * Last updated at string
     *
     * @var string
     */
    public $last_updated_at;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->last_updated_at = Cache::get('last_updated_at', null);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.page-footer');
    }
}
