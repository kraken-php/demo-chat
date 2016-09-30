<?php

namespace App\Component\Http\Action\Resource;

use App\Component\Http\Action\HttpAction;
use Kraken\Network\Http\HttpRequestInterface;

class ResourceAction extends HttpAction
{
    /**
     * @override
     * @inheritDoc
     */
    public function action(HttpRequestInterface $request)
    {
        $target = $request->getTarget();
        $target = ltrim($target, '/');

        list($type, $file) = explode('/', $target);

        return $this->queue('res:read', [ 'type' => $type, 'file' => $file ]);
    }
}
