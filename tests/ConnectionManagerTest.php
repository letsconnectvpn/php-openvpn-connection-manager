<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn\Tests;

use LC\OpenVpn\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ConnectionManagerTest extends TestCase
{
    public function testConnections()
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11940',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $clientInfoList = $serverManager->connections();
        $this->assertSame(2, \count($clientInfoList));
        $this->assertSame(
            'f3bb6f8efb4dc64be35e1044cf1b5e76 [10.128.7.3, fd60:4a08:2f59:ba0::1001]',
            (string) $clientInfoList[0]
        );
        $this->assertSame(
            '78f4a3c26062a434b01892e2b23126d1 [10.128.7.4, fd60:4a08:2f59:ba0::1002]',
            (string) $clientInfoList[1]
        );
    }

    public function testConnectionsNoConnections()
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11941',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $this->assertSame(
            [],
            $serverManager->connections()
        );
    }

    public function testDisconnect()
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11940',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $this->assertSame(1, $serverManager->disconnect(['foo']));
    }

    public function testDisconnectNotThere()
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11941',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $this->assertSame(0, $serverManager->disconnect(['foo']));
    }
}
