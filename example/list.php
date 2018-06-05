<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

$baseDir = \dirname(__DIR__);
/** @psalm-suppress UnresolvableInclude */
require_once \sprintf('%s/vendor/autoload.php', $baseDir);

use LC\OpenVpn\ConnectionManager;
use LC\OpenVpn\ErrorLogger;

$connMan = new ConnectionManager(
    \array_slice($argv, 1),
    new ErrorLogger()
);

$connectionList = $connMan->connections();
foreach ($connectionList as $connectionInfo) {
    $commonName = $connectionInfo['common_name'];
    $virtualAddress = $connectionInfo['virtual_address'];
    echo \sprintf('[%s]: %s', $commonName, \implode(', ', $virtualAddress)).PHP_EOL;
}
