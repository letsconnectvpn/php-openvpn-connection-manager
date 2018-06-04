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

use SURFnet\LC\OpenVpn\ConnectionManager;
use SURFnet\LC\OpenVpn\ErrorLogger;

$connMan = new ConnectionManager(
    \array_slice($argv, 1),
    new ErrorLogger()
);

$connMan->connections();
