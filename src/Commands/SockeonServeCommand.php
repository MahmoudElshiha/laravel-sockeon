<?php

namespace Elshiha\LaravelSockeon\Commands;

use Illuminate\Console\Command;
use Sockeon\Sockeon\Config\ServerConfig;
use Sockeon\Sockeon\Connection\Server;

class SockeonServeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sockeon:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Sockeon WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Load ServerConfig from published config file or fallback to package default
        $serverConfig = config('sockeon-server') ?? require __DIR__.'/../../config/sockeon-server.php';
        $sockeonConfig = config('sockeon') ?? require __DIR__.'/../../config/sockeon.php';

        // Create server with the loaded configuration
        $server = new Server($serverConfig);

        // Register controllers
        $controllers = $sockeonConfig['controllers'] ?? [];
        foreach ($controllers as $controllerClass) {
            if (class_exists($controllerClass)) {
                $server->registerController(new $controllerClass());
                $this->info("Registered controller: {$controllerClass}");
            } else {
                $this->warn("Controller not found: {$controllerClass}");
            }
        }

        // Get host and port for console output (read directly from env like ServerConfig does)
        $host = env('SOCKEON_HOST', '0.0.0.0');
        $port = env('SOCKEON_PORT', 8080);

        $this->info("Starting WebSocket server on ws://{$host}:{$port}");
        $this->info("Press Ctrl+C to stop");
        $this->newLine();

        // Start server
        try {
            $server->run();
        } catch (\Exception $e) {
            $this->error("Server error: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
