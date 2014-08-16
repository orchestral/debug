<?php namespace Orchestra\Debug;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;
use Orchestra\Debug\Traits\MonologTrait;

class Listener
{
    use MonologTrait;

    /**
     * Container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Container\Container  $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
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
            $dispatcher->fire('orchestra.debug: attaching', [$this->monolog]);
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
     * @return \Illuminate\Events\Dispatcher|null
     */
    public function getEventDispatcher()
    {
        if ($this->container->bound('events')) {
            return $this->container['events'];
        }

        return null;
    }
}
