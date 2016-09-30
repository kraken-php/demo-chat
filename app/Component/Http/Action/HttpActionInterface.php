<?php

namespace App\Component\Http\Action;

use App\Component\Job\JobQueueInterface;
use Kraken\Network\Http\HttpRequestInterface;
use Kraken\Network\Http\HttpResponseInterface;
use Kraken\Network\NetworkComponentInterface;
use Kraken\Promise\PromiseInterface;

interface HttpActionInterface extends NetworkComponentInterface, JobQueueInterface
{
    /**
     * @param HttpRequestInterface $request
     * @return HttpResponseInterface|string|PromiseInterface $response
     */
    public function action(HttpRequestInterface $request);
}
