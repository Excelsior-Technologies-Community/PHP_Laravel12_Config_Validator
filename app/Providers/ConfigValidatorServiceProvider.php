<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigValidatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Nothing needed here
    }

    public function boot()
    {
        // Nothing needed here
       // The package will read the rules defined in config/config-validation.php
    }
}