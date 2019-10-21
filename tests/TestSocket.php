<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn\Tests;

use LC\OpenVpn\Exception\ManagementSocketException;
use LC\OpenVpn\ManagementSocketInterface;

/**
 * Abstraction to use the OpenVPN management interface using a socket
 * connection.
 */
class TestSocket implements ManagementSocketInterface
{
    /** @var bool */
    private $connectFail;

    /** @var array<int> */
    private $managementPortList = [];

    /**
     * @param bool $connectFail
     */
    public function __construct($connectFail = false)
    {
        $this->connectFail = $connectFail;
    }

    /**
     * @param string $socketAddress
     * @param int    $timeOut
     *
     * @return void
     */
    public function open($socketAddress, $timeOut = 5)
    {
        if ($this->connectFail) {
            throw new ManagementSocketException('unable to connect to socket');
        }
    }

    /**
     * @param string $command
     *
     * @return array<string>
     */
    public function command($command)
    {
        if (0 === \strpos($command, 'SET_OPENVPN_MANAGEMENT_PORT_LIST ')) {
            // record the management ports we want to query
            foreach (\explode(' ', \substr($command, 33)) as $managementPort) {
                $this->managementPortList[] = (int) $managementPort;
            }

            return [];
        }

        if ('LIST' === $command) {
            if ([11940] === $this->managementPortList) {
                return [];
            }
            if ([11941] === $this->managementPortList) {
                return ['67a7e629c4112b4a85fb254660129f2c 10.104.9.130 fdd3:1503:4c0e:1da1::1000'];
            }
            if ([11942] === $this->managementPortList) {
                return [
                    'f3bb6f8efb4dc64be35e1044cf1b5e76 10.128.7.3 fd60:4a08:2f59:ba0::1001',
                    '78f4a3c26062a434b01892e2b23126d1 10.128.7.4 fd60:4a08:2f59:ba0::1002',
                ];
            }

            return [];
        }

        if (0 === \strpos($command, 'DISCONNECT foo')) {
            return ['1'];
        }
    }

    /**
     * @return void
     */
    public function close()
    {
        // NOP
    }
}
