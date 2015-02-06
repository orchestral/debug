<?php namespace Orchestra\Debug;

use Exception;
use Monolog\Logger;
use Illuminate\Support\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
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

            $listener->setEventDispatcher($app['events']);
            $listener->setMonolog($app['log']->getMonolog());

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
            $profiler = new Profiler($app['orchestra.debug.listener']);

            $profiler->setMonolog($app['log']->getMonolog());

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
            foreach (['Database', 'NotFoundException', 'Request'] as $event) {
                call_user_func([$this, "register{$event}Logger"], $monolog);
            }
        });
    }

    /**
     * Register the database query listener.
     *
     * @param  \Monolog\Logger  $monolog
     * @return void
     */
    public function registerDatabaseLogger(Logger $monolog)
    {
        $db = $this->app['db'];

        $callback = function ($sql, $bindings, $time) use ($db, $monolog) {
            $sql = str_replace_array('\?', $db->prepareBindings($bindings), $sql);
            $monolog->addInfo('<comment>'.$sql.' ['.$time.'ms]</comment>');
        };

        foreach ($db->getQueryLog() as $query) {
            call_user_func($callback, $query['query'], $query['bindings'], $query['time']);
        }

        $this->app['events']->listen('illuminate.query', $callback);
    }

    /**
     * Register the not found exception logger event.
     *
     * @param  \Monolog\Logger  $monolog
     * @return void
     */
    public function registerNotFoundExceptionLogger(Logger $monolog)
    {
        $route = $this->getCurrentRoute();

        $this->app->error(function (Exception $e) use ($monolog, $route) {
            $monolog->addInfo('<comment>Exception <error>'.get_class($e).'</error> on '.$route.'</comment>');
        });
    }

    /**
    * Register the request logger event.
    *
    * @param  \Monolog\Logger  $monolog
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
            'orchestra.debug.listener'
        ];
    }
}