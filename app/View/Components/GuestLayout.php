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
     * Create the component
     *
     * @param  bool  $sidebar
     * @return void
     */
    public function __construct($sidebar = false)
    {
        $this->sidebar = $sidebar;
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
