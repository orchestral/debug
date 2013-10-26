<?php namespace Orchestra\Debug;

use Exception;
use Monolog\Handler\SocketHandler;
use Illuminate\Container\Container;

class Profiler
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Attach the debugger.
     *
     * @return Profiler
     */
    public function attachDebugger()
    {
        if ($this->registerMonologHandler()) {
            $this->registerEvents();
        }
    }

    /**
     * Register the live debugger events.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $monolog = $this->app['log']->getMonolog();

        foreach (array('Request', 'Database') as $event) {
            $this->{"register{$event}Logger"}($monolog);
        }
    }

    /**
     * Register the request logger event.
     *
     * @param  \Monolog\Logger  $monolog
     * @return void
     */
    protected function registerRequestLogger($monolog)
    {
        $this->app->before(function ($request) use ($monolog) {
            $monolog->addInfo('<info>'.strtolower($request->getMethod()).' '.$request->path().'</info>');
        });
    }

    /**
     * Register the database query listener.
     *
     * @param  \Monolog\Logger  $monolog
     * @return void
     */
    protected function registerDatabaseLogger($monolog)
    {
        $db = $this->app['db'];

        $this->app['events']->listen('illuminate.query', function ($sql, $bindings, $time) use ($db, $monolog) {
            $sql = str_replace_array('\?', $db->prepareBindings($bindings), $sql);

            $monolog->addInfo('<comment>'.$sql.' ['.$time.'ms]</comment>');
        });
    }

    /**
     * Register Monolog handler and establish the connection.
     *
     * @return bool
     */
    protected function registerMonologHandler()
    {
        $monolog = $this->app['log']->getMonolog();

        $this->addSocketHandler($monolog);

        return $this->establishConnection($monolog);
    }

    /**
     * Add the socket handler onto the Monolog stack.
     *
     * @param  \Monolog\Logger  $monolog
     * @return void
     */
    protected function addSocketHandler($monolog)
    {
        $monolog->pushHandler(new SocketHandler('tcp://127.0.0.1:8337'));
    }

    /**
     * Attempt to establish the socket handler connection.
     *
     * @param  \Monolog\Logger  $monolog
     * @return bool
     */
    protected function establishConnection($monolog)
    {
        try {
            $monolog->addInfo('Debug client connecting...');
        } catch (Exception $e) {
            $monolog->popHandler();

            return false;
        }

        return true;
    }
}
