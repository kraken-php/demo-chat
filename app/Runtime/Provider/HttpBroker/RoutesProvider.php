<?php

namespace App\Runtime\Provider\HttpBroker;

use App\Component\Http\Action\Index\IndexAction;
use App\Component\Http\Action\Resource\ResourceAction;
use App\Component\Http\Socket\Chat\Chat;
use Kraken\Container\ContainerInterface;
use Kraken\Container\ServiceProvider;
use Kraken\Container\ServiceProviderInterface;
use Kraken\Network\Websocket\WsServer;

class RoutesProvider extends ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string[]
     */
    protected $requires = [
        'App\Component\Http\HttpInterface',
        'App\Component\Job\JobQueueInterface'
    ];

    /**
     * @param ContainerInterface $container
     */
    protected function boot(ContainerInterface $container)
    {
        $http  = $container->make('App\Component\Http\HttpInterface');
        $queue = $container->make('App\Component\Job\JobQueueInterface');

        $routes = [
            '/'              => new IndexAction($queue),
            '/chat'          => new WsServer(null, new Chat),
            '/{type}/{file}' => new ResourceAction($queue)
        ];

        foreach ($routes as $route=>$component)
        {
            $http->addRoute($route, $component);
        }
    }
}
