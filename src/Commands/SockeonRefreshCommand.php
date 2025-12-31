<?php

namespace Elshiha\LaravelSockeon\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class SockeonRefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sockeon:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh (restart) the running Sockeon WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pidFile = storage_path('sockeon.pid');

        // Check if PID file exists
        if (!file_exists($pidFile)) {
            $this->error('No running Sockeon server found. Start the server with: php artisan sockeon:serve');
            return Command::FAILURE;
        }

        // Read the PID
        $pid = (int) trim(file_get_contents($pidFile));

        if (!$pid) {
            $this->error('Invalid PID file. Please delete it and start the server manually.');
            return Command::FAILURE;
        }

        $this->info("Stopping Sockeon server (PID: {$pid})...");

        // Stop the process (Windows compatible)
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Use taskkill
            $result = Process::run("taskkill /PID {$pid} /F");
        } else {
            // Unix/Linux/Mac: Use kill
            $result = Process::run("kill {$pid}");
        }

        // Wait a moment for the process to stop
        sleep(1);

        // Check if process is still running
        if ($this->isProcessRunning($pid)) {
            $this->error('Failed to stop the server. Please stop it manually.');
            return Command::FAILURE;
        }

        $this->info('Server stopped successfully.');

        // Remove the PID file
        @unlink($pidFile);

        // Restart the server in the background
        $this->info('Starting new Sockeon server...');
        
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Use start command with /B for background
            Process::run('start /B php artisan sockeon:serve');
        } else {
            // Unix/Linux/Mac: Use nohup or background process
            Process::run('nohup php artisan sockeon:serve > /dev/null 2>&1 &');
        }

        // Give the server a moment to start
        sleep(2);

        // Check if new PID file was created
        if (file_exists($pidFile)) {
            $newPid = trim(file_get_contents($pidFile));
            $this->info("Server restarted successfully with new PID: {$newPid}");
            return Command::SUCCESS;
        }

        $this->warn('Server may have started, but PID file was not created. Check logs for details.');
        return Command::SUCCESS;
    }

    /**
     * Check if a process is running
     */
    private function isProcessRunning(int $pid): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $result = Process::run("tasklist /FI \"PID eq {$pid}\" /NH");
            return str_contains($result->output(), (string) $pid);
        } else {
            $result = Process::run("ps -p {$pid}");
            return $result->successful();
        }
    }
}
