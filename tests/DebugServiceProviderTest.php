<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Orchestra\Debug\DebugServiceProvider;
use Illuminate\Database\Events\QueryExecuted;

class DebugServiceProviderTest extends TestCase
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
     * Test Orchestra\Debug\DebugServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = new Container();
        $monolog = m::mock('\Monolog\Logger');
        $app['db'] = $db = m::mock('\Illuminate\Database\Connection');
        $app['events'] = $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['log'] = $logger = m::mock('\Illuminate\Log\Writer');
        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $queryLog = [
            [
                'query'    => "SELECT * FROM `users` WHERE id=?",
                'bindings' => [10],
                'time'     => 3,
            ],
        ];

        $stub = new DebugServiceProvider($app);

        $db->shouldReceive('prepareBindings')->once()->with([1])->andReturn([1])
            ->shouldReceive('prepareBindings')->once()->with([10])->andReturn([10])
            ->shouldReceive('getQueryLog')->once()->andReturn($queryLog)
            ->shouldReceive('getName')->twice()->andReturn('mysql');

        $events->shouldReceive('listen')->with(QueryExecuted::class, m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($db) {
                    $c(new QueryExecuted("SELECT * FROM `foo` WHERE id=?", [1], 1, $db));
                })
            ->shouldReceive('listen')->once()->with('orchestra.debug: attaching', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($monolog) {
                    $c($monolog);
                });

        $logger->shouldReceive('getMonolog')->twice()->andReturn($monolog);

        $monolog->shouldReceive('addInfo')->once()->with('<info>Request: GET /foobar</info>')
            ->shouldReceive('addInfo')->once()->with('<comment>SELECT * FROM `foo` WHERE id=1 [1ms]</comment>')
            ->shouldReceive('addInfo')->once()->with('<comment>SELECT * FROM `users` WHERE id=10 [3ms]</comment>');

        $request->shouldReceive('getMethod')->once()->andReturn('GET')
            ->shouldReceive('getHost')->once()->andReturn(null)
            ->shouldReceive('path')->once()->andReturn('foobar');

        $stub->register();

        $this->assertInstanceOf('\Orchestra\Debug\Profiler', $app['orchestra.debug']);
        $this->assertInstanceOf('\Orchestra\Debug\Listener', $app['orchestra.debug.listener']);
    }

    /**
     * Test Orchestra\Debug\DebugServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new DebugServiceProvider($this->app);

        $this->assertEquals(['orchestra.debug', 'orchestra.debug.listener'], $stub->provides());
    }
}
