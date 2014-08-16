<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\Listener;

class ListenerTest extends \PHPUnit_Framework_TestCase
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
     * Test Orchestra\Debug\Listener::attachDebugger() method.
     *
     * @test
     */
    public function testAttachDebuggerMethod()
    {
        $app     = new Container;
        $monolog = m::mock('\Monolog\Logger');
        $events  = m::mock('\Illuminate\Events\Dispatcher');

        $events->shouldReceive('fire')->once()->with('orchestra.debug: attaching', m::type('Array'));

        $monolog->shouldReceive('pushHandler')->once()->andReturn(null)
            ->shouldReceive('addInfo')->once()->with('Debug client connecting...')->andReturn(null);

        $stub = new Listener($app);
        $stub->setMonolog($monolog);

        $this->assertNull($stub->getEventDispatcher());

        $stub->setEventDispatcher($events);

        $this->assertEquals($events, $stub->getEventDispatcher());

        $stub->attachDebugger();

        $this->assertEquals($monolog, $stub->getMonolog());
    }

     /**
     * Test Orchestra\Debug\CommandServiceProvider::register() method when
     * unable to establish connection to monolog.
     *
     * @test
     */
    public function testRegisterMethodWhenMonologIsNotConnected()
    {
        $app = $this->app;
        $monolog = m::mock('\Monolog\Logger');

        $monolog->shouldReceive('pushHandler')->once()->andReturn(null)
            ->shouldReceive('addInfo')->once()->andThrow('\Exception')
            ->shouldReceive('popHandler')->once()->andReturn(null);

        $stub = new Listener($app);
        $stub->setMonolog($monolog);

        $stub->attachDebugger();
    }
}
