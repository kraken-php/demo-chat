<?php

namespace App\Component\Http\Action;

use App\Component\Job\JobQueueInterface;
use App\Component\Job\Throwable\JobQueueNotFoundException;
use Kraken\Network\Http\HttpRequestInterface;
use Kraken\Network\Http\HttpResponse;
use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use Kraken\Promise\Promise;
use Kraken\Promise\PromiseInterface;

class HttpAction implements HttpActionInterface
{
    /**
     * @var JobQueueInterface|null
     */
    protected $queue;

    /**
     * @param JobQueueInterface|null $queue
     */
    public function __construct(JobQueueInterface $queue = null)
    {
        $this->queue = $queue;
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->queue);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function queue($command, $params)
    {
        if ($this->queue === null)
        {
            return Promise::doReject(
                new JobQueueNotFoundException('There is no queue.')
            );
        }

        return $this->queue->queue($command, $params);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function action(HttpRequestInterface $request)
    {
        return new HttpResponse(404);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleConnect(NetworkConnectionInterface $conn)
    {}

    /**
     * @override
     * @inheritDoc
     */
    public function handleDisconnect(NetworkConnectionInterface $conn)
    {}

    /**
     * @override
     * @inheritDoc
     */
    public function handleMessage(NetworkConnectionInterface $conn, NetworkMessageInterface $message)
    {
        $response = $this->action($message);

        if ($response instanceof PromiseInterface)
        {
            $response->done(
                function($response) use($conn) {
                    $rep = new HttpResponse(200, [], $response);
                    $conn->send($rep);
                    $conn->close();
                },
                function($ex) use($conn) {
                    $rep = new HttpResponse(404);
                    $conn->send($rep);
                    $conn->close();
                },
                function($ex) use($conn) {
                    $rep = new HttpResponse(500);
                    $conn->send($rep);
                    $conn->close();
                }
            );
        }
        else
        {
            if (is_string($response))
            {
                $response = new HttpResponse(200, [], $response);
            }

            $conn->send($response);
            $conn->close();
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleError(NetworkConnectionInterface $conn, $ex)
    {}
}
