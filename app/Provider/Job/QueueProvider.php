<?php

namespace App\Provider\Job;

use App\Component\Job\WorkerPool;
use Kraken\Container\ContainerInterface;
use Kraken\Container\ServiceProvider;
use Kraken\Container\ServiceProviderInterface;

class QueueProvider extends ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var string[]
     */
    protected $requires = [
        'Kraken\Runtime\RuntimeManagerInterface'
    ];

    /**
     * @var string[]
     */
    protected $provides = [
        'App\Component\Job\WorkerPoolInterface',
        'App\Component\Job\JobQueueInterface',
    ];

    /**
     * @param ContainerInterface $container
     */
    protected function register(ContainerInterface $container)
    {
        $manager = $container->make('Kraken\Runtime\RuntimeManagerInterface');

        $pool = new WorkerPool($manager, [
            'W1' => 'HttpWorker',
            'W2' => 'HttpWorker',
            'W3' => 'HttpWorker'
        ]);

        $container->instance(
            'App\Component\Job\WorkerPoolInterface',
            $pool
        );

        $container->instance(
            'App\Component\Job\JobQueueInterface',
            $pool
        );
    }

    /**
     * @param ContainerInterface $container
     */
    protected function unregister(ContainerInterface $container)
    {
        $container->remove(
            'App\Component\Job\WorkerPoolInterface'
        );

        $container->remove(
            'App\Component\Job\JobQueueInterface'
        );
    }

    /**
     * @param ContainerInterface $container
     */
    protected function boot(ContainerInterface $container)
    {
        $runtime = $container->make('Kraken\Runtime\RuntimeContainerInterface');
        $pool    = $container->make('App\Component\Job\WorkerPoolInterface');

        $runtime->onStart(function() use($pool) {
            $pool->start();
        });
        $runtime->onStop(function() use($pool) {
            $pool->stop();
        });

        $runtime->once('start', function() use($pool) {
            $pool->create();
        });
    }
}
