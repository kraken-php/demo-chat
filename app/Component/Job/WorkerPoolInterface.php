<?php

namespace App\Component\Job;

use Kraken\Promise\PromiseInterface;
use Kraken\Runtime\Runtime;

interface WorkerPoolInterface extends JobQueueInterface
{
    /**
     * @param string $alias
     * @param string $name
     */
    public function addWorker($alias, $name);

    /**
     * @param int $flags
     * @return PromiseInterface
     */
    public function create($flags = Runtime::CREATE_DEFAULT);

    /**
     * @param int $flags
     * @return PromiseInterface
     */
    public function destroy($flags = Runtime::DESTROY_FORCE);

    /**
     * @return PromiseInterface
     */
    public function start();

    /**
     * @return PromiseInterface
     */
    public function stop();
}
