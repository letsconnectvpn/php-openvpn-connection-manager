<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

require_once \dirname(__DIR__).'/vendor/autoload.php';

use LC\OpenVpn\ConnectionManager;

$connMan = new ConnectionManager(
    'tcp://localhost:41194',
    [11940, 11941]
);

$connectionList = $connMan->connections();
$commonNameList = [];
foreach ($connectionList as $connectionInfo) {
    $commonName = $connectionInfo['common_name'];
    $commonNameList[] = $commonName;
}

$clientCount = $connMan->disconnect($commonNameList);
echo \sprintf('Disconnected %d clients!', $clientCount).PHP_EOL;
