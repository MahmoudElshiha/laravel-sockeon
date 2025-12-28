<?php

namespace Elshiha\LaravelSockeon;

use Illuminate\Support\ServiceProvider;
use Elshiha\LaravelSockeon\Commands\SockeonServeCommand;
use Elshiha\LaravelSockeon\Commands\SockeonLogsCommand;
use Elshiha\LaravelSockeon\Commands\SockeonMakeCommand;

class SockeonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sockeon.php', 'sockeon'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Register commands
            $this->commands([
                SockeonServeCommand::class,
                SockeonLogsCommand::class,
                SockeonMakeCommand::class,
            ]);

            // Publish configuration
            $this->publishes([
                __DIR__.'/../config/sockeon.php' => config_path('sockeon.php'),
            ], 'sockeon-config');

            // Publish stubs
            $this->publishes([
                __DIR__.'/stubs' => base_path('stubs'),
            ], 'sockeon-stubs');
        }
    }
}
