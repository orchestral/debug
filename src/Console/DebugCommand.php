<?php

namespace Orchestra\Debug\Console;

use Closure;
use Illuminate\Console\Command;
use React\EventLoop\LoopInterface;
use React\Socket\Server as SocketServer;

class DebugCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a live debug console';

    /**
     * Socket server instance,.
     *
     * @var \React\Socket\Server
     */
    protected $socket;

    /**
     * Event Loop instance,.
     *
     * @var object
     */
    protected $loop;

    /**
     * Construct a new instance.
     *
     * @param  \React\EventLoop\LoopInterface  $loop
     */
    public function __construct(LoopInterface $loop)
    {
        parent::__construct();

        $this->loop = $loop;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->configureSocket();

        $this->info('Live debugger started...');
        $this->loop->run();
    }

    /**
     * Configure the given socket server.
     *
     * @return void
     */
    protected function configureSocket(): void
    {
        $socket = new SocketServer('127.0.0.1:8337', $this->loop);
        $output = $this->output;

        // Here we will pass the callback that will handle incoming data to the console
        // and we can log it out however we want. We will just write it out using an
        // implementation of a consoles OutputInterface which should perform fine.
        $this->onIncoming($socket, function ($data) use ($output) {
            $output->write($data);
        });
    }

    /**
     * Register a callback for incoming data.
     *
     * @param  \React\Socket\Server  $socket
     * @param  \Closure  $callback
     *
     * @return void
     */
    protected function onIncoming(SocketServer $socket, Closure $callback): void
    {
        $socket->on('connection', function ($conn) use ($callback) {
            $conn->on('data', $callback);
        });
    }
}
