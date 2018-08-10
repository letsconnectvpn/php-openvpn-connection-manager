<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn\Tests;

use LC\OpenVpn\ClientInfo;
use PHPUnit\Framework\TestCase;

class ClientInfoTest extends TestCase
{
    public function testInfo()
    {
        $clientInfo = new ClientInfo('foo', '10.0.1.1', 'fd00::1');
        $this->assertSame('foo', $clientInfo->getCommonName());
        $this->assertSame(['10.0.1.1', 'fd00::1'], $clientInfo->getAddresses());
    }
}
