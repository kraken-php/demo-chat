<?php

namespace App\Provider\Network;

use App\Component\Http\Http;
use Kraken\Container\ContainerInterface;
use Kraken\Container\ServiceProvider;
use Kraken\Container\ServiceProviderInterface;
use Kraken\Ipc\Socket\SocketListener;
use Kraken\Network\NetworkServer;

class NetworkProvider extends ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string[]
     */
    protected $requires = [
        'Kraken\Loop\LoopInterface',
    ];

    /**
     * @var string[]
     */
    protected $provides = [
        'Kraken\Network\NetworkServerInterface',
        'App\Component\Http\HttpInterface'
    ];

    /**
     * @param ContainerInterface $container
     */
    protected function register(ContainerInterface $container)
    {
        $loop = $container->make('Kraken\Loop\LoopInterface');

        $listener = new SocketListener('tcp://127.0.0.1:6080', $loop);
        $server   = new NetworkServer($listener);
        $http     = new Http($server);

        $container->instance(
            'Kraken\Network\NetworkServerInterface',
            $server
        );

        $container->instance(
            'App\Component\Http\HttpInterface',
            $http
        );
    }

    /**
     * @param ContainerInterface $container
     */
    protected function unregister(ContainerInterface $container)
    {
        $container->remove(
            'Kraken\Network\NetworkServerInterface'
        );

        $container->remove(
            'App\Component\Http\HttpInterface'
        );
    }

    /**
     * @param ContainerInterface $container
     */
    protected function boot(ContainerInterface $container)
    {
        $runtime = $container->make('Kraken\Runtime\RuntimeContainerInterface');
        $http    = $container->make('App\Component\Http\HttpInterface');

        $runtime->onStart(function() use($http) {
            $http->start();
        });
        $runtime->onStop(function() use($http) {
            $http->stop();
        });
    }
}
