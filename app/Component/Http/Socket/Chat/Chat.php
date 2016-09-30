<?php

namespace App\Component\Http\Socket\Chat;

use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use Kraken\Network\NetworkComponentInterface;
use Kraken\Runtime\RuntimeContainerInterface;
use Error;
use SplObjectStorage;

class Chat implements NetworkComponentInterface
{
    /**
     * @var RuntimeContainerInterface
     */
    private $runtime;

    /**
     * @var
     */
    private $conns;

    /**
     *
     */
    public function __construct()
    {
        $this->runtime = null;
        $this->conns = new SplObjectStorage();
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->runtime);
        unset($this->conns);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleConnect(NetworkConnectionInterface $conn)
    {
        $id    = $conn->getResourceId();
        $name  = 'User #' . $id;
        $color = '#' . dechex(rand(0x000000, 0xFFFFFF));

        $bubble = [
            'type'  => 'connect',
            'data'  => [
                'id'    => $id,
                'name'  => $name,
                'color' => $color,
                'date'  => date('H:i:s')
            ]
        ];
        $this->broadcast($bubble);

        $conn->data = [
            'id'    => $id,
            'name'  => $name,
            'color' => $color
        ];
        $this->conns->attach($conn);

        $users = [];
        foreach ($this->conns as $conn)
        {
            $users[] = [
                'id'    => $conn->data['id'],
                'name'  => $conn->data['name'],
                'color' => $conn->data['color']
            ];
        }

        $bubble['type'] = 'init';
        $bubble['data']['users'] = $users;

        $conn->send((string)json_encode($bubble));
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleDisconnect(NetworkConnectionInterface $conn)
    {
        $this->conns->detach($conn);

        $bubble = [
            'type'  => 'disconnect',
            'data'  => [
                'id'    => $conn->data['id'],
                'name'  => $conn->data['name'],
                'color' => $conn->data['color'],
                'date'  => date('H:i:s')
            ]
        ];
        $this->broadcast($bubble);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleMessage(NetworkConnectionInterface $conn, NetworkMessageInterface $message)
    {
        $data = json_decode($message->read(), true);
        $type = $data['type'];
        $data = $data['data'];

        if ($type === 'message')
        {
            $bubble = [
                'type' => 'message',
                'data' => [
                    'id'    => $conn->data['id'],
                    'name'  => $conn->data['name'],
                    'color' => $conn->data['color'],
                    'date'  => date('H:i:s'),
                    'mssg'  => $data
                ]
            ];
            return $this->broadcast($bubble);
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleError(NetworkConnectionInterface $conn, $ex)
    {}

    /**
     * @param mixed[] $message
     */
    protected function broadcast($message)
    {
        foreach ($this->conns as $conn)
        {
            $conn->send((string) json_encode($message));
        }
    }

    /**
     * @return string
     */
    private function getDirPublic()
    {
        return realpath(__DIR__ . '/../../../public');
    }
}