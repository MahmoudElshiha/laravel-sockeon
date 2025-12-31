<?php

namespace Elshiha\LaravelSockeon;

use Illuminate\Support\ServiceProvider;
use Elshiha\LaravelSockeon\Commands\SockeonServeCommand;
use Elshiha\LaravelSockeon\Commands\SockeonLogsCommand;
use Elshiha\LaravelSockeon\Commands\SockeonMakeCommand;
use Elshiha\LaravelSockeon\Commands\SockeonRefreshCommand;

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
                SockeonRefreshCommand::class,
            ]);

            // Publish configuration
            $this->publishes([
                __DIR__.'/../config/sockeon.php' => config_path('sockeon.php'),
                __DIR__.'/../config/sockeon-server.php' => config_path('sockeon-server.php'),
            ], 'sockeon-config');

            // Publish stubs
            $this->publishes([
                __DIR__.'/stubs' => base_path('stubs'),
            ], 'sockeon-stubs');
        }
    }
}
