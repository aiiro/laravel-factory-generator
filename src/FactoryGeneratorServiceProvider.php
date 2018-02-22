<?php

namespace Aiiro\Factory;

use Aiiro\Factory\Console\Commands\GenerateFactory;
use Illuminate\Support\ServiceProvider;

class FactoryGeneratorServiceProvider extends ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
        $this->commands([
            GenerateFactory::class,
        ]);
    }
}
