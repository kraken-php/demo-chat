<?php

namespace App\Component\Http;

use Kraken\Network\NetworkComponentInterface;

interface HttpInterface
{
    /**
     * @param string $route
     * @param NetworkComponentInterface $component
     */
    public function addRoute($route, NetworkComponentInterface $component);

    /**
     * @param string $route
     */
    public function removeRoute($route);

    /**
     * Starts HTTP service
     */
    public function start();

    /**
     * Stops HTTP service
     */
    public function stop();
}
