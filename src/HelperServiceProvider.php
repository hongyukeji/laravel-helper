<?php

namespace Hongyukeji\LaravelHelper;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(__DIR__ . '/Helpers/*.php') as $file) {
            require_once "{$file}";
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/helper.php' => config_path('helper.php'),
        ], 'helper_config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/helper.php', 'helper'
        );
    }
}
