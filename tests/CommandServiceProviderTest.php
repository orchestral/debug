<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\CommandServiceProvider;

class CommandServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Orchestra\Debug\CommandServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = $this->app;
        $app['events'] = $events = m::mock('EventDispatcher');

        $stub = new CommandServiceProvider($app);

        $stub->register();

        $this->assertInstanceOf('\Orchestra\Debug\Console\DebugCommand', $app['command.debug']);
    }

    /**
     * Test Orchestra\Debug\CommandServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new CommandServiceProvider($this->app);

        $this->assertEquals(['command.debug'], $stub->provides());
    }
}
