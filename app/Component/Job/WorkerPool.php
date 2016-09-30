<?php

namespace App\Component\Job;

use Kraken\Promise\Promise;
use Kraken\Runtime\Runtime;
use Kraken\Runtime\RuntimeCommand;
use Kraken\Runtime\RuntimeManagerInterface;

class WorkerPool implements WorkerPoolInterface
{
    /**
     * @var RuntimeManagerInterface
     */
    protected $manager;

    /**
     * @var mixed[]
     */
    protected $workers;

    /**
     * @var int
     */
    private $workersPointer;

    /**
     * @var int
     */
    private $workersSize;

    /**
     * @param RuntimeManagerInterface $manager
     * @param mixed[] $workers
     */
    public function __construct(RuntimeManagerInterface $manager, $workers = [])
    {
        $this->manager = $manager;
        $this->workers = [];
        $this->workersPointer = 0;
        $this->workersSize = 0;

        foreach ($workers as $alias=>$name)
        {
            $this->addWorker($alias, $name);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->manager);
        unset($this->workers);
        unset($this->workersPointer);
        unset($this->workersSize);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function addWorker($alias, $name)
    {
        $this->workers[] = [
            'alias' => $alias,
            'name'  => $name
        ];
        $this->workersSize++;
    }

    /**
     * @override
     * @inheritDoc
     */
    public function queue($command, $params)
    {
        $worker = $this->getCurrentWorker();

        return $this->manager->sendRequest(
            $worker['alias'],
            new RuntimeCommand($command, $params)
        );
    }

    /**
     * @override
     * @inheritDoc
     */
    public function create($flags = Runtime::CREATE_DEFAULT)
    {
        $promises = [];

        foreach ($this->workers as $worker)
        {
            $promises[] = $this->manager->createProcess($worker['alias'], $worker['name'], $flags);
        }

        return Promise::all($promises);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function destroy($flags = Runtime::DESTROY_FORCE)
    {
        $promises = [];

        foreach ($this->workers as $worker)
        {
            $promises[] = $this->manager->destroyProcess($worker['alias'], $flags);
        }

        return Promise::all($promises);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function start()
    {
        $promises = [];

        foreach ($this->workers as $worker)
        {
            $promises[] = $this->manager->startProcess($worker['alias']);
        }

        return Promise::all($promises);
    }

    /**
     * @override
     * @inheritDoc
     */
    public function stop()
    {
        $promises = [];

        foreach ($this->workers as $worker)
        {
            $promises[] = $this->manager->stopProcess($worker['alias']);
        }

        return Promise::all($promises);
    }

    /**
     * @override
     * @inheritDoc
     */
    private function getCurrentWorker()
    {
        $index = $this->workersPointer++;

        if ($this->workersPointer >= $this->workersSize)
        {
            $this->workersPointer = 0;
        }

        return $this->workers[$index];
    }
}
