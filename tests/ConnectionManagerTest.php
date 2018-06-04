<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\LC\OpenVpn\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use SURFnet\LC\OpenVpn\ConnectionManager;

class ConnectionManagerTest extends TestCase
{
    public function testConnect()
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
}
