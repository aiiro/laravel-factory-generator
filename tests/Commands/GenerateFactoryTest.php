<?php

namespace Aiiro\Factory\Tests\Commands;

use Aiiro\Factory\Tests\TestCase;

class GenerateFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function it_works_normally()
    {
        $result = $this->artisan('generate:factory', ['name' => 'foo']);

        $this->assertEquals(0, $result);
    }

}
