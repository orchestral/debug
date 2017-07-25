<?php

namespace Orchestra\Debug;

use Laravie\Profiler\Events\Request;
use Laravie\Profiler\Events\DatabaseQuery;
use Laravie\Profiler\ProfileServiceProvider;
use Laravie\Profiler\Contracts\Profiler as ProfilerContract;

class DebugServiceProvider extends ProfileServiceProvider
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
        parent::register();

        $this->registerSocket();

        $this->registerDebugger();

        $this->registerEvents();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerSocket()
    {
        $this->app->singleton('orchestra.debug.socket', function ($app) {
            return new SocketBroadcast($app->make('events'));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerDebugger()
    {
        $this->app->singleton('orchestra.debug', function ($app) {
            return new Debugger($app->make(ProfilerContract::class), $app->make('orchestra.debug.socket'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'orchestra.debug',
            'orchestra.debug.socket',
            ProfilerContract::class,
        ];
    }
}
