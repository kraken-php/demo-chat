<?php

namespace App\Command\Resource;

use Kraken\Runtime\Command\Command;
use Kraken\Runtime\Command\CommandInterface;
use Kraken\Throwable\Exception\Runtime\ReadException;

class ResourceReadCommand extends Command implements CommandInterface
{
    /**
     * @override
     * @inheritDoc
     */
    protected function command($params = [])
    {
        $type = $params['type'];
        $file = $params['file'];
        $path = $this->runtime->getCore()->getBasePath() . '/public';

        switch ($type)
        {
            case 'html':
                $path .= '/' . $file;
                break;

            default:
                $path .= '/' . $type . '/'. $file;
        }

        if (!file_exists($path))
        {
            throw new ReadException("File [$path] does not exist!");
        }

        return file_get_contents($path);
    }
}
