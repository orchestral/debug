<?php namespace Orchestra\Debug\Console\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\Console\DebugCommand;
use Symfony\Component\Console\Output\OutputInterface;

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
     * Test Orchestra\Debug\Console\DebugCommand::fire() method.
     *
     * @test
     */
    public function testFireMethod()
    {
        $input     = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output    = m::mock('\Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::mock('\Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $socket    = m::mock('\React\Socket\Server');
        $loop      = m::mock('\React\EventLoop\LoopInterface');
        $laravel   = m::mock('\Illuminate\Contracts\Foundation\Application');

        $connection = m::mock('Connection');

        $input->shouldReceive('bind')->once()
            ->shouldReceive('hasArgument')->once()->andReturn(false)
            ->shouldReceive('isInteractive')->once()->andReturn(true)
            ->shouldReceive('validate')->once();

        $output->shouldReceive('writeln')->once()->with('<info>Live debugger started...</info>', 0)
            ->shouldReceive('write')->once()->with('Foobar', false, 0)
            ->shouldReceive('getVerbosity')->andReturn(0)
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $formatter->shouldReceive('setDecorated')->andReturn(false);

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
        $stub->setLaravel($laravel);

        $laravel->shouldReceive('call')->once()->andReturnUsing(function ($object, $parameters = []) {
            return call_user_func_array($object, $parameters);
        });

        $stub->run($input, $output);
    }
}
