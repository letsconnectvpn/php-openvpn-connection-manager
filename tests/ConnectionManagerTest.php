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

        $connectionList = $serverManager->connections();
        $this->assertSame(2, \count($connectionList));
        $this->assertSame('f3bb6f8efb4dc64be35e1044cf1b5e76: [10.128.7.3,fd60:4a08:2f59:ba0::1001]', (string) $connectionList[0]);
        $this->assertSame('78f4a3c26062a434b01892e2b23126d1: [10.128.7.4,fd60:4a08:2f59:ba0::1002]', (string) $connectionList[1]);
    }

    public function testConnectionsTwoSockets()
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11945',
                'tcp://127.0.0.1:11946',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $connectionList = $serverManager->connections();
        $this->assertSame(3, \count($connectionList));
        $this->assertSame('f3bb6f8efb4dc64be35e1044cf1b5e76: [10.128.7.3,fd60:4a08:2f59:ba0::1001]', (string) $connectionList[0]);
        $this->assertSame('78f4a3c26062a434b01892e2b23126d1: [10.128.7.4,fd60:4a08:2f59:ba0::1002]', (string) $connectionList[1]);
        $this->assertSame('67a7e629c4112b4a85fb254660129f2c: [10.104.9.130,fdd3:1503:4c0e:1da1::1000]', (string) $connectionList[2]);
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
