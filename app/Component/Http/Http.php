<?php

namespace App\Component\Http;

use Kraken\Network\NetworkComponentInterface;
use Kraken\Network\NetworkServerInterface;

class Http implements HttpInterface
{
    /**
     * @var NetworkServerInterface
     */
    protected $server;

    /**
     * @var NetworkComponentInterface[]
     */
    protected $routes;

    /**
     * @param NetworkServerInterface $server
     * @param NetworkComponentInterface[] $routes
     */
    public function __construct(NetworkServerInterface $server, $routes = [])
    {
        $this->server = $server;
        $this->routes = $routes;
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->server);
        unset($this->routes);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function addRoute($route, NetworkComponentInterface $component)
    {
        $this->routes[$route] = $component;
    }

    /**
     * @override
     * @inheritDoc
     */
    public function removeRoute($route)
    {
        unset($this->routes[$route]);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function start()
    {
        $server = $this->server;

        foreach ($this->routes as $route=>$component)
        {
            $server->addRoute($route, $component);
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function stop()
    {
        $server = $this->server;

        foreach ($this->routes as $route=>$component)
        {
            $server->removeRoute($route);
        }
    }
}
