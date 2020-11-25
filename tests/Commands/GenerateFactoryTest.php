<?php

namespace Aiiro\Factory\Tests\Commands;

use Aiiro\Factory\Tests\TestCase;
use Aiiro\Factory\Commands\GenerateFactory;

class GenerateFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_replace_the_dummy_content_with_the_table_column_names()
    {
        // Arrange
        $columns = [
            'id',
            'name',
            'email',
            'address',
            'deleted_at',
            'created_at',
            'updated_at',
        ];

        $stub = 'DummyColumns';

        /** @var ExtendGenerateFactory $command */
        $command = app(ExtendGenerateFactory::class);

        // Act
        $content = $command->replaceColumns($stub, $columns);


        // Assert
$expect = <<<EOT
        'name' => null,
        'email' => null,
        'address' => null,
        'deleted_at' => null,
        'created_at' => null,
        'updated_at' => null,
EOT;

        $this->assertEquals($expect, $content);
    }

}

class ExtendGenerateFactory extends GenerateFactory
{

    public function replaceColumns($stub, $columns)
    {
        return parent::replaceColumns($stub, $columns);
    }

}
