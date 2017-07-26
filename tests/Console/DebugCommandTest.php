<?php

namespace Orchestra\Debug\TestCase\Console;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Debug\Console\DebugCommand;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class DebugCommandTest extends PHPUnitTestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
        unset($this->app);

        m::close();
    }

    /** @test */
    public function command_can_be_handled()
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
