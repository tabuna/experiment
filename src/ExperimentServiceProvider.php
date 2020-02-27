<?php

declare(strict_types=1);

namespace Orchid\Experiment;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ExperimentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('experiment', function (string $key, $value) {
            return Experiment::getCookieValue($key) === $value;
        });
    }
}
