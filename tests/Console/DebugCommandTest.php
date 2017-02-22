<?php namespace Orchestra\Debug\Console\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\Console\DebugCommand;

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
        $input = m::mock('\Symfony\Component\Console\Input\InputInterface');
        $output = m::mock('\Symfony\Component\Console\Output\OutputInterface');
        $formatter = m::mock('\Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $loop = m::mock('\React\EventLoop\LoopInterface');
        $laravel = m::mock('\Illuminate\Contracts\Foundation\Application');

        $connection = m::mock('Connection');

        $input->shouldReceive('bind')->once()
            ->shouldReceive('hasArgument')->andReturn(false)
            ->shouldReceive('isInteractive')->once()->andReturn(true)
            ->shouldReceive('validate')->once();

        $output->shouldReceive('writeln')->once()->with('<info>Live debugger started...</info>', 32)
            ->shouldReceive('getVerbosity')->andReturn(0)
            ->shouldReceive('getFormatter')->andReturn($formatter);

        $formatter->shouldReceive('setDecorated')->andReturn(false);

        $loop->shouldReceive('addReadStream')->andReturn(null)
            ->shouldReceive('run')->andReturnNull();

        $stub = new DebugCommand($loop);
        $stub->setLaravel($laravel);

        $laravel->shouldReceive('call')->once()->andReturnUsing(function ($object, $parameters = []) {
            return call_user_func_array($object, $parameters);
        });

        $stub->run($input, $output);
    }
}
