<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\DebugServiceProvider;

class DebugServiceProviderTest extends \PHPUnit_Framework_TestCase
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
        $this->app = new Container;
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
     * Test Orchestra\Debug\DebugServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app  = $this->app;
        $stub = new DebugServiceProvider($app);

        $stub->register();

        $this->assertInstanceOf('\Orchestra\Debug\Profiler', $app['orchestra.debug']);
    }

    /**
     * Test Orchestra\Debug\DebugServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new DebugServiceProvider($this->app);

        $this->assertEquals(array('orchestra.debug'), $stub->provides());
    }
}
