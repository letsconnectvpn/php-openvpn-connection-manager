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
                return \explode("\n", \file_get_contents(__DIR__.'/socket/list_no_clients.txt'));
            }
            if ([11941] === $this->managementPortList) {
                return \explode("\n", \file_get_contents(__DIR__.'/socket/list_one_client.txt'));
            }
            if ([11942] === $this->managementPortList) {
                return \explode("\n", \file_get_contents(__DIR__.'/socket/list_two_clients.txt'));
            }

            return ['OK: 0'];
        }

        if (0 === \strpos($command, 'DISCONNECT foo')) {
            return \explode("\n", \file_get_contents(__DIR__.'/socket/disconnect.txt'));
        }

        return ['ERR: INVALID_COMMAND'];
    }

    /**
     * @return void
     */
    public function close()
    {
        // NOP
    }
}
