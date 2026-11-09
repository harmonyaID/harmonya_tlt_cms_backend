<?php

namespace App\Console\Commands\Generator;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:number')]
class NumberMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:number';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:number';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Number class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Number';

    public function handle()
    {
        $nameInput = $this->getNameInput();

        $name = $this->qualifyClass("Generator\\$nameInput");
        $path = $this->getPath($name);

        $this->makeDirectory($path);
        $this->info($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type . ' Generator created successfully.');
    }

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/number.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services\Number';
    }
}
