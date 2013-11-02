<?php namespace Orchestra\Debug;

use Exception;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

class Profiler
{
    /**
     * Container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Monolog\Logger                  $monolog
     */
    public function __construct(Container $container, Logger $monolog)
    {
        $this->container = $container;
        $this->monolog   = $monolog;
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
        if (! is_null($dispatcher = $this->getEventDispatcher())) {
            $dispatcher->fire('orchestra.debug: attaching', array($this->monolog));
        }
    }

    /**
     * Register Monolog handler and establish the connection.
     *
     * @return bool
     */
    protected function registerMonologHandler()
    {
        $this->addSocketHandler();

        return $this->establishConnection();
    }

    /**
     * Add the socket handler onto the Monolog stack.
     *
     * @return void
     */
    protected function addSocketHandler()
    {
        $this->monolog->pushHandler(new SocketHandler('tcp://127.0.0.1:8337'));
    }

    /**
     * Attempt to establish the socket handler connection.
     *
     * @return bool
     */
    protected function establishConnection()
    {
        try {
            $this->monolog->addInfo('Debug client connecting...');
        } catch (Exception $e) {
            $this->monolog->popHandler();

            return false;
        }

        return true;
    }

    /**
     * Set the event dispatcher instance to be used by connections.
     *
     * @param \Illuminate\Events\Dispatcher $dispatcher
     * @return void
     */
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->container->instance('events', $dispatcher);
    }

    /**
     * Get the current event dispatcher instance.
     *
     * @return \Illuminate\Events\Dispatcher
     */
    public function getEventDispatcher()
    {
        if ($this->container->bound('events')) {
            return $this->container['events'];
        }
    }

    /**
     * Get monolog instance.
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }
}
