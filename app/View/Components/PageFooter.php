<?php

namespace App\View\Components;

use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Michelf\MarkdownExtra;

class PageFooter extends Component
{
    /**
     * Last updated at string
     *
     * @var string
     */
    public $last_updated_at;

    /**
     * Site name string
     *
     * @var string
     */
    public $footer_text;

    /**
     * Site description string
     *
     * @var string
     */
    public $about_text;

    /**
     * Show repository link boolean
     *
     * @var bool
     */
    public $show_repository_link;

    /**
     * Repository link
     *
     * @var string
     */
    public $repository_link;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(GeneralSettings $settings)
    {
        $parser = new MarkdownExtra();
        $parser->hard_wrap = true;

        $this->last_updated_at = Cache::get('last_updated_at', null);

        $this->footer_text = $parser->transform($settings->footer_text);
        $this->about_text = $parser->transform($settings->about_text);
        $this->repository_link = $settings->repository_link;
        $this->show_repository_link = $settings->show_repository_link;
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
