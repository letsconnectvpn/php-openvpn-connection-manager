<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn\Tests;

use Exception;
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

    /** @var string|null */
    private $socketAddress;

    /**
     * @param bool $connectFail
     */
    public function __construct($connectFail = false)
    {
        $this->connectFail = $connectFail;
        $this->socketAddress = null;
    }

    /**
     * @param string $socketAddress
     * @param int    $timeOut
     *
     * @return void
     */
    public function open($socketAddress, $timeOut = 5)
    {
        $this->socketAddress = $socketAddress;
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
        switch ($command) {
            case 'status 2':
                switch ($this->socketAddress) {
                    case 'tcp://127.0.0.1:11940':
                        return \explode("\n", \file_get_contents(__DIR__.'/socket/status_with_clients.txt'));
                    case 'tcp://127.0.0.1:11941':
                        return \explode("\n", \file_get_contents(__DIR__.'/socket/status_no_clients.txt'));
                    case 'tcp://127.0.0.1:11945':
                        return \explode("\n", \file_get_contents(__DIR__.'/socket/status_with_clients.txt'));
                    case 'tcp://127.0.0.1:11946':
                        return \explode("\n", \file_get_contents(__DIR__.'/socket/status_with_clients_two.txt'));
                    default:
                        throw new Exception('no match for this command');
                }
                // no break
            case 'kill foo':
                if ('tcp://127.0.0.1:11940' === $this->socketAddress) {
                    return \explode("\n", \file_get_contents(__DIR__.'/socket/kill_success.txt'));
                }

                return \explode("\n", \file_get_contents(__DIR__.'/socket/kill_error.txt'));
            default:
                throw new Exception('no match for this command');
        }
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->socketAddress = null;
    }
}
