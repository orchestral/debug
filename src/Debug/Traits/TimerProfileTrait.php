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
     *
     * @param  string|null  $name
     * @return void
     */
    public function timeEnd($name = null)
    {
        $end = microtime(true);

        is_null($name) && $name = uniqid();

        if (! isset($this->timers[$name])) {
            Arr::set($this->timers, "{$name}.start", constant('LARAVEL_START'));
        }

        $start   = Arr::get($this->timers, "{$name}.start");
        $seconds = $end - $start;
        $message = Arr::get($this->timers, "{$name}.message", '{name} took {sec} seconds.');

        $this->monolog->addInfo(Str::replace($message, ['name' => $name, 'sec' => $seconds]));
    }
}
