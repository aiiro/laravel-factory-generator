<?php

namespace Aiiro\Factory;

use Aiiro\Factory\Commands\GenerateFactory;
use Illuminate\Support\ServiceProvider;

class FactoryGeneratorServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $configPath = __DIR__ . '/../config/factory-generator.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('factory-generator.php');
        } else {
            $publishPath = base_path('config/factory-generator.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/factory-generator.php';
        $this->mergeConfigFrom($configPath, 'factory-generator');

        $this->commands([
            GenerateFactory::class,
        ]);
    }
}
