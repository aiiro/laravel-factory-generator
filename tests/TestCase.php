<?php

namespace Aiiro\Factory\Tests;

use Aiiro\Factory\FactoryGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    protected function getPackageProviders($app)
    {
        return [
            FactoryGeneratorServiceProvider::class
        ];
    }

}
