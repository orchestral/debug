<?php

namespace Orchestra\Debug;

use Monolog\Logger;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;

class DebugServiceProvider extends ServiceProvider
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
        $this->registerListener();

        $this->registerProfiler();

        $this->registerEvents();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->app->singleton('orchestra.debug.listener', function ($app) {
            $listener = new Listener($app);

            $listener->setEventDispatcher($app->make('events'));
            $listener->setMonolog($app->make('log')->getMonolog());

            return $listener;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerProfiler()
    {
        $this->app->singleton('orchestra.debug', function ($app) {
            $profiler = new Profiler($app->make('orchestra.debug.listener'));

            $profiler->setMonolog($app->make('log')->getMonolog());

            return $profiler;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $this->app['events']->listen('orchestra.debug: attaching', function ($monolog) {
            foreach (['Database', 'Request'] as $event) {
                $this->{"register{$event}Logger"}($monolog);
            }
        });
    }

    /**
     * Register the database query listener.
     *
     * @param  \Monolog\Logger  $monolog
     *
     * @return void
     */
    public function registerDatabaseLogger(Logger $monolog)
    {
        $db = $this->app->make('db');

        $callback = function (QueryExecuted $query) use ($monolog) {
            $sql = Str::replaceArray('?', $query->connection->prepareBindings($query->bindings), $query->sql);
            $monolog->addInfo("<comment>{$sql} [{$query->time}ms]</comment>");
        };

        foreach ($db->getQueryLog() as $query) {
            $callback(new QueryExecuted($query['query'], $query['bindings'], $query['time'], $db));
        }

        $this->app->make('events')->listen(QueryExecuted::class, $callback);
    }

    /**
     * Register the request logger event.
     *
     * @param  \Monolog\Logger  $monolog
     *
     * @return void
     */
    public function registerRequestLogger(Logger $monolog)
    {
        $monolog->addInfo('<info>Request: '.$this->getCurrentRoute().'</info>');
    }

    /**
     * Get current route.
     *
     * @return string
     */
    protected function getCurrentRoute()
    {
        $request = $this->app['request'];
        $method  = strtoupper($request->getMethod());
        $path    = ltrim($request->path(), '/');
        $host    = $request->getHost();

        ! is_null($host) && $host = rtrim($host, '/');

        return "{$method} {$host}/{$path}";
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
            'orchestra.debug.listener',
        ];
    }
}
