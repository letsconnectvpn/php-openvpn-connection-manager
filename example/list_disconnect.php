<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

require_once \dirname(__DIR__).'/vendor/autoload.php';

use LC\OpenVpn\ConnectionManager;
use LC\OpenVpn\ErrorLogger;

$connMan = new ConnectionManager(
    \array_slice($argv, 1),
    new ErrorLogger()
);

$connectionList = $connMan->connections();
$commonNameList = [];
foreach ($connectionList as $clientInfo) {
    $commonNameList[] = $clientInfo->getCommonName();
    echo $clientInfo . PHP_EOL;
    echo \sprintf('[%s]: %s', $clientInfo->getCommonName(), \implode(', ', $clientInfo->getAddresses())).PHP_EOL;
}

$clientCount = $connMan->disconnect($commonNameList);
echo \sprintf('Disconnected %d clients!', $clientCount).PHP_EOL;
