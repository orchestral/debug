<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Orchestra\Debug\Profiler;
use PHPUnit\Framework\TestCase;

class ProfilerTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Debug\Profiler::extend() method.
     *
     * @test
     */
    public function testExtendMethod()
    {
        $listener = m::mock('\Orchestra\Debug\Listener');
        $monolog = m::mock('\Monolog\Logger');

        $monolog->shouldReceive('addInfo')->once()->with('Called!')->andReturn(null);

        $stub = new Profiler($listener);
        $stub->setMonolog($monolog);

        $callback = function ($monolog) {
            $monolog->addInfo('Called!');
        };

        $this->assertEquals($stub, $stub->extend($callback));
    }

    /**
     * Test Orchestra\Debug\Profiler::extend() method.
     *
     * @test
     */
    public function testGetListenerMethod()
    {
        $listener = m::mock('\Orchestra\Debug\Listener');
        $monolog = m::mock('\Monolog\Logger');

        $stub = new Profiler($listener);
        $stub->setMonolog($monolog);

        $this->assertEquals($listener, $stub->getListener());
    }

    /**
     * Test Orchestra\Debug\Profiler::__call() method.
     *
     * @test
     */
    public function testCallListenerMethod()
    {
        $listener = m::mock('\Orchestra\Debug\Listener[attachDebugger]', [m::mock('\Illuminate\Container\Container')]);
        $monolog = m::mock('\Monolog\Logger');

        $listener->shouldReceive('attachDebugger')->once()->andReturnNull();

        $stub = new Profiler($listener);
        $stub->setMonolog($monolog);

        $this->assertNull($stub->attachDebugger());
    }
}
