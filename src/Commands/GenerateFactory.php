<?php

namespace Aiiro\Factory\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class GenerateFactory
 *
 * @package Aiiro\Factory\Commands
 */
class GenerateFactory extends GeneratorCommand
{

    /**
     * @var string
     */
    protected $name = 'generate:factory';

    /**
     * @var array
     */
    protected $ignore_columns = [
        'id',
        'deleted_at',
        Model::CREATED_AT,
        Model::UPDATED_AT,
    ];

    public function handle()
    {

        $table = $this->getNameInput();
        $factory = Str::singular(Str::ucfirst(Str::camel($table))) . 'Factory';

        $name = $this->qualifyClass($factory);

        $path = $this->getPath($name);

        if ($this->alreadyExists($table)) {
            $this->error($this->type.' already exists!');

            return false;
        }

        /** @var \PDO $pdo */
        $pdo = \DB::connection()->getPdo();
        $stmt = $pdo->prepare("PRAGMA table_info($table)");
        $stmt->execute();

        $columns = [];
        foreach ($stmt->fetchAll() as $column) {
            $columns[] = $column['name'];
        }

        $this->makeDirectory($path);

        $this->createFile($path, $this->buildFactory($name, $columns));

        $this->info($this->type.' created successfully.');

    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/factory.stub';
    }

    /**
     * @param $name
     * @param $columns
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFactory($name, $columns)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $stub = $this->replaceColumns($stub, $columns);

        return $stub;
    }

    /**
     * @param $path
     * @param $name
     */
    protected function createFile($path, $name)
    {
        $this->files->put($path, $name);
    }

    /**
     * @param $stub
     * @param $columns
     * @return mixed
     */
    protected function replaceColumns($stub, $columns)
    {
        $content = '';

        foreach ($columns as $column) {
            if (in_array($column, $this->ignore_columns)) {
                continue;
            }

            // indent spaces
            $content .= "        ";
            $content .= "'{$column}' => null,";
            $content .= "\n";
        }

        if (Str::length($content) > 0) {
            // Remove newline character of the last line.
            $content = rtrim($content, "\n");
        }

        return str_replace('DummyColumns', $content, $stub);
    }
}
