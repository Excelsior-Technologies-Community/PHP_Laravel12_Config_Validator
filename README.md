# PHP_Laravel12_Config_Validator

## Introduction

PHP_Laravel12_Config_Validator is a beginner-friendly Laravel 12 project that ensures your application configuration is correct before runtime. Misconfigured .env values or invalid config files can cause runtime errors, which may break your application in production. This project leverages the Laravel Config Validator package to automatically validate your configuration files and provides clear, actionable error messages for easy debugging.

---

## Project Overview

PHP_Laravel12_Config_Validator is designed to prevent configuration-related runtime issues by validating Laravel configuration keys. It is particularly useful for developers who want to maintain a stable and consistent application environment.

### Key Highlights:

- Automated Config Validation: Detects invalid values in .env and config files before they cause issues.

- Nested Key Support: Validates deeply nested Laravel configuration keys like database.connections, cache.stores, and maintenance.driver.

- Friendly Error Reporting: Shows precise validation errors in the console using php artisan config:validate.

- Customizable Rules: All validation rules are stored in config-validation/config-validation.php, making it easy to adjust according to your project’s requirements.

- Service Provider Integration: The optional ConfigValidatorServiceProvider automatically loads validation rules when the application boots.

- Prevents Runtime Failures: Ensures consistency across development, testing, and production environments.

---

## Step 1: Install Laravel 12

```bash
composer create-project laravel/laravel PHP_Laravel12_Config_Validator "12.*"
cd PHP_Laravel12_Config_Validator
```
---

## Step 2: Install Config Validator Package

Run:

```bash
composer require ashallendesign/laravel-config-validator
```
---

## Step 3: Publishing the Default Rulesets

Run: 

```bash
php artisan vendor:publish --tag=config-validator-defaults
```

---

## Step 4: Configure config/config-validator.php

Update rules to reflect Laravel 12 structure, including nested keys:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Config Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define the config keys and expected types or patterns.
    | You can add any Laravel config keys to validate.
    |
    */

    'rules' => [
        'app.name' => 'string|required',
        'app.env' => 'in:local,production,testing,staging|required',
        'app.debug' => 'boolean|required',
        'app.url' => 'url|required',
        'database.connections.mysql.host' => 'string|required',
        'database.connections.mysql.port' => 'numeric|required',
        'database.connections.mysql.database' => 'string|required',
        'database.connections.mysql.username' => 'string|required',
        'database.connections.mysql.password' => 'string|nullable',
    ],

];
```

---

## Step 5: Create Config Validator Service Provider

Run:

```bash
php artisan make:provider ConfigValidatorServiceProvider
```
File: `app/Providers/ConfigValidatorServiceProvider.php`

```php
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
```

---

## Step 6: Register Service Provider

Open: `config/app.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

'providers' => [
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class, //  important
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    // App\Providers\AuthServiceProvider::class,      
    // App\Providers\EventServiceProvider::class,      
    // App\Providers\RouteServiceProvider::class,      
    App\Providers\ConfigValidatorServiceProvider::class,
],

];
```

---

## Step 7: Test Config Validator

```bash
php artisan config:validate
```

---

## Output

<img src="screenshots/Screenshot 2026-03-27 151508.png" width="1000">

---

## Project Structure

```
PHP_Laravel12_Config_Validator/
│
├── app/
│   └── Providers/
│       └── ConfigValidatorServiceProvider.php
├── config/
│   ├── app.php
│   └── config-validator.php
├── config-validation/
│     
├── routes/
│   └── web.php
├── .env
├── composer.json
└── README.md
```

---

Your PHP_Laravel12_Config_Validator Project is now ready!
