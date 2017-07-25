<?php

namespace Orchestra\Debug;

use Laravie\Profiler\Traits\Logger;

class SocketBroadcast
{
    use Logger;

    /**
     * Attempt to establish the socket handler connection.
     *
     * @return bool
     */
    public function connect()
    {
        $monolog = $this->getMonolog();

        $monolog->pushHandler(new SocketHandler('tcp://127.0.0.1:8337'));

        try {
            $monolog->addInfo('Debug client connecting...');
        } catch (Exception $e) {
            $monolog->popHandler();

            return false;
        }

        return true;
    }
}
