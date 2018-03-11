<?php

namespace Aiiro\Factory\Commands;

use Aiiro\Factory\Exceptions\UnknownConnectionException;
use Illuminate\Config\Repository;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Filesystem\Filesystem;
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

    /** @var \Illuminate\Config\Repository */
    protected $config;

    /**
     * GenerateFactory constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(Filesystem $files, Repository $config)
    {
        parent::__construct($files);
        $this->config = $config;
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Aiiro\Factory\Exceptions\UnknownConnectionException
     */
    public function handle()
    {

        $table = $this->getNameInput();
        $model = Str::ucfirst(Str::camel($table));
        $factory = Str::singular($model) . 'Factory';

        $path = $this->getPath($factory);

        if ($this->alreadyExists($path)) {
            $this->error($factory . ' already exists!');

            return false;
        }

        /** @var Connection $conn */
        $conn = \DB::connection();
        /** @var \PDO $pdo */
        $pdo = \DB::connection()->getPdo();

        $stmt = null;
        $columns = [];

        if ($conn instanceof MySqlConnection) {
            $stmt = $pdo->prepare("DESCRIBE $table");
            $stmt->execute();
            foreach ($stmt->fetchAll() as $column) {
                $columns[] = $column['Field'];
            }
        } elseif ($conn instanceof SQLiteConnection) {
            $stmt = $pdo->prepare("PRAGMA table_info($table)");
            $stmt->execute();
            foreach ($stmt->fetchAll() as $column) {
                $columns[] = $column['name'];
            }
        } else {
            throw new UnknownConnectionException('Unknown connection is set.');
        }

        $factoryContent = $this->buildFactory(
            $this->config->get('factory-generator.namespace.model'),
            $model,
            $columns
        );

        $this->createFile($path, $factoryContent);

        $this->info($factory . ' created successfully.');

    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        return base_path() . '/database/factories/' . $name . '.php';
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return !!$this->files->exists($path);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/factory.stub';
    }

    /**
     * @param $namespace
     * @param $name
     * @param $columns
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFactory($namespace, $name, $columns)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $namespace)->replaceClass($stub, $name);

        $stub = $this->replaceColumns($stub, $columns);

        return $stub;
    }

    /**
     * @param $path
     * @param $content
     */
    protected function createFile($path, $content)
    {
        $this->files->put($path, $content);
    }

    /**
     * @param string $stub
     * @param string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace('DummyModelNamespace', $name, $stub);

        return $this;
    }

    /**
     * @param $stub
     * @param $columns
     * @return mixed
     */
    protected function replaceColumns($stub, $columns)
    {
        $content = '';

        $ignoreColumns = $this->config->get('factory-generator.ignore_columns');

        foreach ($columns as $column) {
            if (in_array($column, $ignoreColumns)) {
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
