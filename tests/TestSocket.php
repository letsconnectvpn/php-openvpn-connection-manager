<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\LC\OpenVpn\Tests;

use SURFnet\LC\OpenVpn\Exception\ManagementSocketException;
use SURFnet\LC\OpenVpn\ManagementSocketInterface;

/**
 * Abstraction to use the OpenVPN management interface using a socket
 * connection.
 */
class TestSocket implements ManagementSocketInterface
{
    /** @var bool */
    private $connectFail;

    /** @var null|string */
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
        if ('status 2' === $command) {
            if ('tcp://127.0.0.1:11940' === $this->socketAddress) {
                // send back the returnData as an array
                return \explode("\n", \file_get_contents(__DIR__.'/socket/status_with_clients.txt'));
            } else {
                return \explode("\n", \file_get_contents(__DIR__.'/socket/status_no_clients.txt'));
            }
        } elseif ('kill' === $command) {
            if ('tcp://127.0.0.1:11940' === $this->socketAddress) {
                return \explode("\n", \file_get_contents(__DIR__.'/socket/kill_success.txt'));
            } else {
                return \explode("\n", \file_get_contents(__DIR__.'/socket/kill_error.txt'));
            }
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
