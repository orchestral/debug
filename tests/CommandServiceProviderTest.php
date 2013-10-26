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
     * Test Orchestra\Debug\CommandServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = $this->app;
        $app['events'] = $events = m::mock('EventDispatcher');

        $events->shouldReceive('listen')->once()
                ->with('artisan.start', m::type('Closure'))->andReturn(null);

        $stub = new CommandServiceProvider($app);

        $stub->register();

        $this->assertInstanceOf('\Orchestra\Debug\Console\DebugCommand', $app['command.debug']);
    }

     /**
     * Test Orchestra\Debug\CommandServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = m::mock('\Illuminate\Container\Container');
        $log = m::mock('Logger');
        $monolog = m::mock('Monolog');
        $events = m::mock('EventDispatcher');
        $request = m::mock('Request');

        $app->shouldReceive('offsetGet')->twice()->with('log')->andReturn($log)
            ->shouldReceive('offsetGet')->once()->with('events')->andReturn($events)
            ->shouldReceive('before')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($request) {
                    $c($request);
                });

        $request->shouldReceive('getMethod')->once()->andReturn('GET')
            ->shouldReceive('path')->once()->andReturn('foobar');

        $events->shouldReceive('listen')->once()->with('illuminate.query', m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($monolog) {
                $c('SELECT * FROM foo WHERE id=? AND name=?', array('2', 'foo'), 10);
            });

        $log->shouldReceive('getMonolog')->twice()->andReturn($monolog);

        $monolog->shouldReceive('pushHandler')->once()->andReturn(null)
            ->shouldReceive('addInfo')->once()->once()->with('Debug client connecting...')->andReturn(null)
            ->shouldReceive('addInfo')->once()->with('<info>get foobar</info>')->andReturn(null)
            ->shouldReceive('addInfo')->once()
                ->with('<comment>SELECT * FROM foo WHERE id=2 AND name=foo [10ms]</comment>')->andReturn(null);

        $stub = new CommandServiceProvider($app);

        $stub->boot();
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
        $app['log'] = $log = m::mock('Logger');
        $monolog = m::mock('Monolog');

        $log->shouldReceive('getMonolog')->once()->andReturn($monolog);
        $monolog->shouldReceive('pushHandler')->once()->andReturn(null)
            ->shouldReceive('addInfo')->once()->andThrow('\Exception')
            ->shouldReceive('popHandler')->once()->andReturn(null);

        $stub = new CommandServiceProvider($app);

        $stub->boot();
    }

    /**
     * Test Orchestra\Debug\CommandServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new CommandServiceProvider($this->app);

        $this->assertEquals(array('command.debug'), $stub->provides());
    }
}
