<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\LC\OpenVpn;

use SURFnet\LC\OpenVpn\Exception\ManagementSocketException;

/**
 * Abstraction to use the OpenVPN management interface using a socket
 * connection.
 */
class ManagementSocket implements ManagementSocketInterface
{
    /** @var null|resource */
    private $socket = null;

    /**
     * @param string $socketAddress
     * @param int    $timeOut
     *
     * @return void
     */
    public function open($socketAddress, $timeOut = 5)
    {
        $this->socket = @\stream_socket_client($socketAddress, $errno, $errstr, $timeOut);
        if (false === $this->socket) {
            throw new ManagementSocketException(
                \sprintf('%s (%s)', $errstr, $errno)
            );
        }
        // turn off logging as the output may interfere with our management
        // session, we do not care about the output
        $this->command('log off');
    }

    /**
     * @param string $command
     *
     * @return array<string>
     */
    public function command($command)
    {
        if (null === $this->socket) {
            throw new ManagementSocketException('socket not open');
        }
        self::write(
            $this->socket,
            \sprintf("%s\n", $command)
        );

        return self::read($this->socket);
    }

    /**
     * @return void
     */
    public function close()
    {
        if (null === $this->socket) {
            throw new ManagementSocketException('socket not open');
        }
        if (false === @\fclose($this->socket)) {
            throw new ManagementSocketException('unable to close the socket');
        }
    }

    /**
     * @param resource $socket
     * @param string   $data
     *
     * @return void
     */
    private static function write($socket, $data)
    {
        if (false === @\fwrite($socket, $data)) {
            throw new ManagementSocketException('unable to write to socket');
        }
    }

    /**
     * @param \resource $socket
     *
     * @return array<string>
     */
    private static function read($socket)
    {
        $dataBuffer = [];
        while (!\feof($socket) && !self::isEndOfResponse(\end($dataBuffer))) {
            if (false === $readData = @\fgets($socket, 4096)) {
                throw new ManagementSocketException('unable to read from socket');
            }
            $dataBuffer[] = \trim($readData);
        }

        return $dataBuffer;
    }

    /**
     * @param string $lastLine
     *
     * @return bool
     */
    private static function isEndOfResponse($lastLine)
    {
        $endMarkers = ['END', 'SUCCESS: ', 'ERROR: '];
        foreach ($endMarkers as $endMarker) {
            if (0 === \strpos($lastLine, $endMarker)) {
                return true;
            }
        }

        return false;
    }
}
