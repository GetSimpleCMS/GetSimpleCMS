<?php

namespace RectorPrefix202212\React\Socket;

use RectorPrefix202212\React\EventLoop\Loop;
use RectorPrefix202212\React\EventLoop\LoopInterface;
use RectorPrefix202212\React\Promise\Timer;
use RectorPrefix202212\React\Promise\Timer\TimeoutException;
final class TimeoutConnector implements ConnectorInterface
{
    private $connector;
    private $timeout;
    private $loop;
    public function __construct(ConnectorInterface $connector, $timeout, LoopInterface $loop = null)
    {
        $this->connector = $connector;
        $this->timeout = $timeout;
        $this->loop = $loop ?: Loop::get();
    }
    public function connect($uri)
    {
        return Timer\timeout($this->connector->connect($uri), $this->timeout, $this->loop)->then(null, self::handler($uri));
    }
    /**
     * Creates a static rejection handler that reports a proper error message in case of a timeout.
     *
     * This uses a private static helper method to ensure this closure is not
     * bound to this instance and the exception trace does not include a
     * reference to this instance and its connector stack as a result.
     *
     * @param string $uri
     * @return callable
     */
    private static function handler($uri)
    {
        return function (\Exception $e) use($uri) {
            if ($e instanceof TimeoutException) {
                throw new \RuntimeException('Connection to ' . $uri . ' timed out after ' . $e->getTimeout() . ' seconds (ETIMEDOUT)', \defined('SOCKET_ETIMEDOUT') ? \SOCKET_ETIMEDOUT : 110);
            }
            throw $e;
        };
    }
}
