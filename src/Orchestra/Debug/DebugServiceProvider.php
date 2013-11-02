<?php namespace Orchestra\Debug;

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
        $events  = $this->app['events'];
        $request = $this->app['request'];
        $db      = $this->app['db'];

        $events->listen('orchestra.debug: attaching', function ($monolog) use ($db, $events, $request) {
            $monolog->addInfo('<info>'.strtolower($request->getMethod()).' '.$request->path().'</info>');

            $events->listen('illuminate.query', function ($sql, $bindings, $time) use ($db, $monolog) {
                $sql = str_replace_array('\?', $db->prepareBindings($bindings), $sql);

                $monolog->addInfo('<comment>'.$sql.' ['.$time.'ms]</comment>');
            });
        });
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
