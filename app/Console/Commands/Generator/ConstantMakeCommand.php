<?php

namespace App\Console\Commands\Generator;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:constant')]
class ConstantMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:constant';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:constant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Constant class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Constant';

    protected function getStub()
    {
        if ($this->option('code')) {
            return $this->resolveStubPath('/stubs/constant.code.stub');
        }

        return $this->resolveStubPath('/stubs/constant.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services\Constant';
    }

    protected function getOptions()
    {
        return [
            ['code', 'c', InputOption::VALUE_NONE, 'Generate a constant helper with code and name string type'],
        ];
    }
}
