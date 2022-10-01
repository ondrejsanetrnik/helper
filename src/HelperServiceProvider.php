<?php

namespace Ondrejsanetrnik\Helper;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/helper.php' => config_path('helper.php'),
        ], 'config');
    }

    public function register()
    {
    }
}
