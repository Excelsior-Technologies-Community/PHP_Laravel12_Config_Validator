<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Production ma auto-validate on boot
        if (app()->environment('production')) {
            $this->validateProductionConfig();
        }
    }

    private function validateProductionConfig(): void
    {
        $rules = config('config-validator.rules', []);
        $productionRules = config('config-validator.production_rules', []);
        $allRules = array_merge($rules, $productionRules);

        $failures = [];

        foreach ($allRules as $key => $rule) {
            $value = config($key);
            $validator = Validator::make(['value' => $value], ['value' => $rule]);
            if ($validator->fails()) {
                $failures[] = "[{$key}]: " . $validator->errors()->first('value');
            }
        }

        // Production ma APP_DEBUG true hoy to block karo
        if (config('app.debug') === true) {
            $failures[] = '[app.debug]: Must be false in production!';
        }

        if (!empty($failures)) {
            abort(500, 'Invalid production config: ' . implode(' | ', $failures));
        }
    }
}
