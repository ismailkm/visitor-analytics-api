<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Sensor;
use App\Observers\SensorObserver;
use App\Models\Visitor;
use App\Observers\VisitorObserver;

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
        Sensor::observe(SensorObserver::class);
        Visitor::observe(VisitorObserver::class);
    }
}
