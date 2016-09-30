<?php

namespace App\Component\Http\Action\Index;

use App\Component\Http\Action\HttpAction;
use Kraken\Network\Http\HttpRequestInterface;

class IndexAction extends HttpAction
{
    /**
     * @override
     * @inheritDoc
     */
    public function action(HttpRequestInterface $request)
    {
        return $this->queue('res:read', [ 'type' => 'html', 'file' => 'index.html' ]);
    }
}
