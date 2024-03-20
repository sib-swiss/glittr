<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Has Sidebar
     *
     * @var bool
     */
    public $sidebar = false;

    /**
     * Page title
     *
     * @var ?string
     */
    public $page_title;

    /**
     * Create the component
     */
    public function __construct(
        public ?string $title = null,
        public bool $show_header = true,
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
