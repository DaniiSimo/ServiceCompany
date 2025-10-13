<?php

namespace App\Providers;

use App\Rules\ClosedPolygonRule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Rule::macro(name: 'closedPolygon', macro: fn() => new ClosedPolygonRule());
    }
}
