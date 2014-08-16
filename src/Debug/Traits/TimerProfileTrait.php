<?php namespace Orchestra\Debug\Traits;

use InvalidArgumentException;
use Illuminate\Support\Arr;
use Orchestra\Support\Str;

trait TimerProfileTrait
{
    /**
     * List of timers.
     *
     * @var array
     */
    protected $timers = [];

    /**
     * Time a process.
     *
     * @param  string       $name
     * @param  string|null  $message
     * @return string
     */
    public function time($name, $message = null)
    {
        if (isset($this->timers[$name])) {
            $name = uniqid($name);
        }

        $this->timers[$name] = [
            'name'    => $name,
            'start'   => microtime(true),
            'message' => $message,
        ];

        return $name;
    }

    /**
     * Calculate timed taken for a process to complete.
     * @param $name
     */
    public function endTime($name)
    {
        $end = microtime(true);

        if (! isset($this->timers[$name])) {
            throw new InvalidArgumentException("Timer [{$name}] is not available.");
        }

        $start   = Arr::get($this->timers, "{$name}.start");
        $seconds = $end - $start;
        $message = Arr::get($this->timers, "{$name}.message", '{name} took {sec} seconds.');

        $this->monolog->addInfo(Str::replace($message, ['name' => $name, 'sec' => $seconds]));
    }
}
