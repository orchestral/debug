<?php namespace Orchestra\Debug\Traits;

use Monolog\Logger;

trait MonologTrait
{
    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * Get monolog instance.
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }

    /**
     * Set monolog instance.
     *
     * @param  \Monolog\Logger  $monolog
     *
     * @return $this
     */
    public function setMonolog(Logger $monolog)
    {
        $this->monolog = $monolog;

        return $this;
    }
}
