<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn\Tests;

use LC\OpenVpn\ConnectionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ConnectionManagerTest extends TestCase
{
    public function testConnections(): void
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11940',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $this->assertSame(
            [
                [
                    'common_name' => 'f3bb6f8efb4dc64be35e1044cf1b5e76',
                    'virtual_address' => [
                        '10.128.7.3',
                        'fd60:4a08:2f59:ba0::1001',
                    ],
                ],
                [
                    'common_name' => '78f4a3c26062a434b01892e2b23126d1',
                    'virtual_address' => [
                        '10.128.7.4',
                        'fd60:4a08:2f59:ba0::1002',
                    ],
                ],
            ],
            $serverManager->connections()
        );
    }

    public function testConnectionsTwoSockets(): void
    {
        $serverManager = new ConnectionManager(
            [
                'tcp://127.0.0.1:11945',
                'tcp://127.0.0.1:11946',
            ],
            new NullLogger(),
            new TestSocket()
        );

        $this->assertSame(
            [
                [
                    'common_name' => 'f3bb6f8efb4dc64be35e1044cf1b5e76',
                    'virtual_address' => [
                        '10.128.7.3',
                        'fd60:4a08:2f59:ba0::1001',
                    ],
                ],
                [
                    'common_name' => '78f4a3c26062a434b01892e2b23126d1',
                    'virtual_address' => [
                        '10.128.7.4',
                        'fd60:4a08:2f59:ba0::1002',
                    ],
                ],
                [
                    'common_name' => '67a7e629c4112b4a85fb254660129f2c',
                    'virtual_address' => [
                        '10.104.9.130',
                        'fdd3:1503:4c0e:1da1::1000',
                    ],
                ],
            ],
            $serverManager->connections()
        );
    }

    public function testConnectionsNoConnections(): void
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

    public function testDisconnect(): void
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

    public function testDisconnectNotThere(): void
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
