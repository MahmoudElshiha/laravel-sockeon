<?php

namespace Elshiha\LaravelSockeon\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'sockeon:make')]
class SockeonMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'sockeon:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Sockeon WebSocket controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Sockeon Controller';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/sockeon-controller.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        // First check if user has published the stub
        $customPath = base_path(trim($stub, '/'));
        if (file_exists($customPath)) {
            return $customPath;
        }

        // Fall back to package stub
        return __DIR__.'/../stubs/sockeon-controller.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\WebSocket';
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the Sockeon controller'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $result = parent::handle();

        if ($result === false) {
            return self::FAILURE;
        }

        $this->components->info(sprintf('%s [%s] created successfully.', $this->type, $this->getNameInput()));
        
        // Remind user to register the controller
        $this->newLine();
        $this->components->warn('Remember to register this controller in config/sockeon.php:');
        $this->line("  'controllers' => [");
        $this->line("      \\{$this->qualifyClass($this->getNameInput())}::class,");
        $this->line("  ],");

        return self::SUCCESS;
    }
}
