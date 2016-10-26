<?php

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    // just for solving warning about:
    // No tests found in class "TestCase".
    public function testNothing()
    {
        $this->assertTrue(true);
    }
}
