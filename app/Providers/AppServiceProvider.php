<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('github_contributors', function (object $job) {
            return Limit::perHour(4500);
        });

        RateLimiter::for('github_scraping', function (object $job) {
            return Limit::perMinute(4);
        });

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
