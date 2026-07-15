<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ConfigValidateFixCommand extends Command
{
    protected $signature   = 'config:validate {--fix : Auto-fix common config issues}';
    protected $description = 'Validate config keys. Use --fix to auto-fix common issues.';

    // Default fix values for common keys
    private array $fixes = [
        'APP_ENV'              => 'local',
        'APP_DEBUG'            => 'true',
        'APP_URL'              => 'http://localhost',
        'DB_CONNECTION'        => 'mysql',
        'DB_HOST'              => '127.0.0.1',
        'DB_PORT'              => '3306',
        'MAIL_MAILER'          => 'log',
        'MAIL_FROM_ADDRESS'    => 'hello@example.com',
        'MAIL_FROM_NAME'       => '${APP_NAME}',
        'CACHE_STORE'          => 'file',
        'QUEUE_CONNECTION'     => 'database',
    ];

    public function handle(): void
    {
        $rules   = config('config-validator.rules', []);
        $doFix   = $this->option('fix');
        $fixed   = [];
        $failed  = [];
        $passed  = [];

        foreach ($rules as $key => $rule) {
            $value     = config($key);
            $validator = Validator::make(['value' => $value], ['value' => $rule]);

            if ($validator->fails()) {
                $error    = $validator->errors()->first('value');
                $failed[] = ['key' => $key, 'error' => $error];
            } else {
                $passed[] = $key;
            }
        }

        // Show passed
        foreach ($passed as $key) {
            $this->line("  <fg=green>✔</> {$key}");
        }

        // Show failed
        foreach ($failed as $item) {
            $this->line("  <fg=red>✘</> {$item['key']} — {$item['error']}");
        }

        $this->newLine();
        $this->info('Total: ' . count($rules) . ' | Passed: ' . count($passed) . ' | Failed: ' . count($failed));

        // --fix mode
        if ($doFix && count($failed) > 0) {
            $this->newLine();
            $this->warn('Attempting auto-fix...');

            $envPath    = base_path('.env');
            $envContent = file_get_contents($envPath);

            foreach ($failed as $item) {
                // Convert config key to ENV key: app.debug -> APP_DEBUG
                $envKey = strtoupper(str_replace(['.', '-'], '_', $item['key']));

                // Use only last segment for simple keys (app.debug -> APP_DEBUG)
                $parts  = explode('.', $item['key']);
                $envKey = strtoupper(end($parts));

                // Map known config keys to ENV keys
                $envMap = [
                    'app.env'              => 'APP_ENV',
                    'app.debug'            => 'APP_DEBUG',
                    'app.url'              => 'APP_URL',
                    'app.name'             => 'APP_NAME',
                    'mail.default'         => 'MAIL_MAILER',
                    'mail.from.address'    => 'MAIL_FROM_ADDRESS',
                    'mail.from.name'       => 'MAIL_FROM_NAME',
                    'cache.default'        => 'CACHE_STORE',
                    'queue.default'        => 'QUEUE_CONNECTION',
                    'database.connections.mysql.host'     => 'DB_HOST',
                    'database.connections.mysql.port'     => 'DB_PORT',
                    'database.connections.mysql.database' => 'DB_DATABASE',
                    'database.connections.mysql.username' => 'DB_USERNAME',
                ];

                $envKey   = $envMap[$item['key']] ?? strtoupper(str_replace('.', '_', $item['key']));
                $fixValue = $this->fixes[$envKey] ?? null;

                if ($fixValue && str_contains($envContent, $envKey . '=')) {
                    $envContent = preg_replace(
                        "/^{$envKey}=.*/m",
                        "{$envKey}={$fixValue}",
                        $envContent
                    );
                    $fixed[] = "{$envKey}={$fixValue}";
                    $this->line("  <fg=yellow>Fixed:</> {$envKey} = {$fixValue}");
                } else {
                    $this->line("  <fg=red>Cannot auto-fix:</> {$item['key']} (no default available)");
                }
            }

            if (!empty($fixed)) {
                file_put_contents($envPath, $envContent);
                $this->newLine();
                $this->info(count($fixed) . ' issue(s) fixed in .env. Run php artisan config:clear to apply.');
            }
        } elseif ($doFix && count($failed) === 0) {
            $this->info('No issues to fix!');
        }
    }
}
