<?php namespace Orchestra\Debug;

use Closure;
use Orchestra\Debug\Traits\MonologTrait;
use Orchestra\Debug\Traits\TimerProfileTrait;

class Profiler
{
    use MonologTrait, TimerProfileTrait;

    /**
     * Listener instance.
     *
     * @var \Orchestra\Debug\Listener
     */
    protected $listener;

    /**
     * Construct a new instance.
     *
     * @param  \Orchestra\Debug\Listener    $listener
     */
    public function __construct(Listener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Extend the profiler.
     *
     * @param  \Closure    $callback
     * @return $this
     */
    public function extend(Closure $callback)
    {
        call_user_func($callback, $this->monolog);

        return $this;
    }

    /**
     * Get Listener instance.
     *
     * @return \Orchestra\Debug\Listener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getListener(), $method], $parameters);
    }
}
