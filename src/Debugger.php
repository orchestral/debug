<?php

namespace Orchestra\Debug;

use Laravie\Profiler\Events\Request;
use Laravie\Profiler\Events\DatabaseQuery;
use Laravie\Profiler\Contracts\Profiler as ProfilerContract;

class Debugger
{
    /**
     * The profiler implementation.
     *
     * @var \Laravie\Profiler\Contracts\Profiler
     */
    protected $profiler;

    /**
     * The socket broadcaster.
     *
     * @var \Orchestra\Debug\SocketBroadcast
     */
    protected $broadcaster;

    /**
     * Create a new debugger.
     *
     * @param \Laravie\Profiler\Contracts\Profiler  $profiler
     * @param \Orchestra\Debug\SocketBroadcast  $broadcaster
     */
    public function __construct(ProfilerContract $profiler, SocketBroadcast $broadcaster)
    {
        $this->profiler = $profiler;
        $this->broadcaster = $broadcaster;
    }

    /**
     * Start debugging.
     *
     * @return $this
     */
    public function start()
    {
        if ($this->broadcaster->connect()) {
            $this->registerEvents();
        }

        return $this;
    }

    /**
     * Register the live debugger events.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $this->profiler->extend(new DatabaseQuery())
            ->extend(new Request());
    }

    /**
     * Pass through call to profiler.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        $this->profiler->{$method}(...$parameters);
    }
}
