<?php namespace Orchestra\Debug;

use Exception;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

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
        $this->registerProfiler();
        $this->registerEvents();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerProfiler()
    {
        $this->app['orchestra.debug'] = $this->app->share(function ($app) {
            $profiler = new Profiler($app, $app['log']->getMonolog());

            $profiler->setEventDispatcher($app['events']);

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
        $me = $this;

        $this->app['events']->listen('orchestra.debug: attaching', function ($monolog) use ($me) {
            foreach (array('Database', 'NotFoundException', 'Request') as $event) {
                call_user_func(array($me, "register{$event}Logger"), $monolog);
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

        return strtoupper($request->getMethod()).' '.$request->path();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('orchestra.debug');
    }
}
