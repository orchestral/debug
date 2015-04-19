<?php namespace Orchestra\Debug;

use Illuminate\Support\ServiceProvider;
use React\Socket\Server as SocketServer;
use Orchestra\Debug\Console\DebugCommand;
use React\EventLoop\Factory as LoopFactory;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.debug', function () {
            $loop   = LoopFactory::create();
            $socket = new SocketServer($loop);

            return new DebugCommand($socket, $loop);
        });

        $this->commands('command.debug');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.debug'];
    }
}
