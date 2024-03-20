<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use Illuminate\Support\ServiceProvider;
use Michelf\MarkdownExtra;

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
        view()->composer(
            ['layouts.guest'],
            function ($view) {
                $view->with('site_name', app(GeneralSettings::class)->site_name);
                $view->with('site_description', app(GeneralSettings::class)->site_description);
            }
        );
        view()->composer(
            ['emails.*'],
            function ($view) {
                $parser = new MarkdownExtra();
                $parser->hard_wrap = true;
                $view->with('mail_signature', $parser->transform(app(GeneralSettings::class)->mail_signature));
            }
        );
    }
}
