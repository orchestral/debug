<?php namespace Orchestra\Debug\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\DebugCommand;

class DebugCommandTest extends \PHPUnit_Framework_TestCase
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
     * Test Orchestra\Debug\DebugCommand::fire() method.
     *
     * @test
     */
    public function testFireMethod()
    {
        $input  = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\OutputInterface');
        $socket = m::mock('\React\Socket\Server');
        $loop   = m::mock('\React\EventLoop\LoopInterface');

        $connection = m::mock('Connection');

        $input->shouldReceive('bind')->once()
            ->shouldReceive('isInteractive')->once()->andReturn(true)
            ->shouldReceive('validate')->once();

        $output->shouldReceive('writeln')->once()->with('<info>Live debugger started...</info>')
            ->shouldReceive('write')->once()->with('Foobar');

        $loop->shouldReceive('run')->once();
        $socket->shouldReceive('listen')->once()->with(8337, '127.0.0.1')->andReturn(null)
            ->shouldReceive('on')->once()->with('connection', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($connection) {
                    $c($connection);
                });
        $connection->shouldReceive('on')->once()->with('data', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    $c('Foobar');
                });

        $stub = new DebugCommand($socket, $loop);

        $stub->run($input, $output);
    }
}
