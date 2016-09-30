<?php

namespace App\Component\Job;

use Kraken\Promise\PromiseInterface;

interface JobQueueInterface
{
    /**
     * @param string $command
     * @param string[] $params
     * @return PromiseInterface
     */
    public function queue($command, $params);
}
