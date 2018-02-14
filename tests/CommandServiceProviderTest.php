<?php

namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\Console\DebugCommand;
use Orchestra\Debug\CommandServiceProvider;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class CommandServiceProviderTest extends PHPUnitTestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
        unset($this->app);

        m::close();
    }

    /** @test */
    public function service_can_be_registered()
    {
        $stub = (new CommandServiceProvider($this->app))->register();

        $this->assertInstanceOf(DebugCommand::class, $this->app['command.debug']);
    }

    /** @test */
    public function service_is_deferred()
    {
        $this->assertTrue((new CommandServiceProvider($this->app))->isDeferred());
    }

    /** @test */
    public function service_contains_proper_provides_for_deferred()
    {
        $stub = new CommandServiceProvider($this->app);

        $this->assertEquals(['command.debug'], $stub->provides());
    }
}
