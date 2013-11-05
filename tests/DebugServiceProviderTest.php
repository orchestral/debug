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
        $app = m::mock('\Illuminate\Container\Container[error]');
        $monolog = m::mock('\Monolog\Logger');
        $app['db'] = $db = m::mock('DB');
        $app['events'] = $events = m::mock('Illuminate\Events\Dispatcher');
        $app['log'] = $logger = m::mock('Logger');
        $app['request'] = $request = m::mock('Illuminate\Http\Request');
        $stub = new DebugServiceProvider($app);

        $app->shouldReceive('error')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    $e = m::mock('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
                    $c($e);
                });

        $db->shouldReceive('prepareBindings')->once()->with(array(1))->andReturn(array(1));
        $events->shouldReceive('listen')->once()->with('illuminate.query', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($monolog) {
                    $c("SELECT * FROM `foo` WHERE id=?", array(1), 1);
                })
            ->shouldReceive('listen')->once()->with('orchestra.debug: attaching', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($monolog) {
                    $c($monolog);
                });
        $logger->shouldReceive('getMonolog')->once()->andReturn($monolog);
        $monolog->shouldReceive('addInfo')->once()->with('<info>Request: GET foobar</info>')
            ->shouldReceive('addInfo')->once()->with('<error>Request: GET foobar</error>')
            ->shouldReceive('addInfo')->once()->with('<comment>SELECT * FROM `foo` WHERE id=1 [1ms]</comment>');
        $request->shouldReceive('getMethod')->twice()->andReturn('GET')
            ->shouldReceive('path')->twice()->andReturn('foobar');

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
