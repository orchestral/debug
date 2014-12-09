<?php namespace Orchestra\Debug\Traits;

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
        $id = isset($this->timers[$name]) ? uniqid($name) : $name;

        $this->timers[$id] = [
            'name'    => $name,
            'start'   => microtime(true),
            'message' => $message,
        ];

        return $id;
    }

    /**
     * Calculate timed taken for a process to complete.
     *
     * @param  string|null  $name
     * @return void
     */
    public function timeEnd($name = null)
    {
        $id  = $name;
        $end = microtime(true);

        is_null($id) && $id = uniqid();

        if (! isset($this->timers[$id])) {
            $this->timers[$id] = [
                'name'    => $name,
                'start'   => constant('LARAVEL_START'),
                'message' => null,
            ];
        }

        $message = $this->timers[$id]['message'] ?: '{name} took {sec} seconds.';
        $name    = $this->timers[$id]['name'];
        $seconds = $end - $this->timers[$id]['start'];

        $this->monolog->addInfo(Str::replace($message, ['name' => $name, 'sec' => $seconds]));
    }
}
