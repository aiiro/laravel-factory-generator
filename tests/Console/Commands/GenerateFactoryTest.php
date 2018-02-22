<?php

namespace Aiiro\Factory\Tests;

class GenerateFactoryTest extends TestCase
{

    /**
     * @test
     */
    public function it_works_normally()
    {
        $result = $this->artisan('generate:factory');

        $this->assertEquals(0, $result);
    }

}
