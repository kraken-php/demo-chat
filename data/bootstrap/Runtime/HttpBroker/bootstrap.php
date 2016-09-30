<?php
/**
 * Bootstrap file for ProcessContainers.
 * Any modifications in this file should be done with exceptional care.
 */

$core = new \Kraken\Root\Runtime\Core\Factory($type);
$core = $core->create(
    realpath(__DIR__ . '/../../../')
);

$providers = $core->getDefaultProviders();
$providers = array_merge($providers,
    [
        'App\Provider\Job\QueueProvider',
        'App\Provider\Network\NetworkProvider',
        'App\Runtime\Provider\HttpBroker\RoutesProvider'
    ]
);

$aliases = $core->getDefaultAliases();
$aliases = array_merge($aliases,
    [
        'Network'       => 'Kraken\Network\NetworkServerInterface',
        'Network.Http'  => 'App\Component\Http\HttpInterface',
        'Jobs.Queue'    => 'App\Component\Job\JobQueueInterface'
    ]
);

$core->registerProviders($providers);
$core->registerAliases($aliases);

return $core;
