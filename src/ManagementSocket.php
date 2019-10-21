<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn;

use LC\OpenVpn\Exception\ManagementSocketException;

/**
 * Abstraction to use the OpenVPN management interface using a socket
 * connection.
 */
class ManagementSocket implements ManagementSocketInterface
{
    /** @var resource|null */
    private $socket = null;

    /**
     * @param string $socketAddress
     * @param int    $timeOut
     *
     * @return void
     */
    public function open($socketAddress, $timeOut = 5)
    {
        /** @var false|resource $socket */
        $socket = \stream_socket_client($socketAddress, $errno, $errstr, $timeOut);
        if (false === $socket) {
            throw new ManagementSocketException(
                \sprintf('%s (%d)', $errstr, $errno)
            );
        }
        $this->socket = $socket;
    }

    /**
     * @param string $command
     *
     * @return array<string>
     */
    public function command($command)
    {
        echo $command.PHP_EOL;
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
        if (false === \fclose($this->socket)) {
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
        if (false === \fwrite($socket, $data)) {
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
        // find OK: <n>
        $readData = \fgets($socket);
        echo $readData;
        if (0 !== \strpos($readData, 'OK: ')) {
            throw new ManagementSocketException(\sprintf('expected OK: <n> response, got "%s"', $readData));
        }
        $lineCount = (int) \substr($readData, 4);
        // read the rest in the buffer
        $dataBuffer = [];
        for ($i = 0; $i < $lineCount; ++$i) {
            $d = \trim(\fgets($socket));
            echo $d.PHP_EOL;

            $dataBuffer[] = $d;
        }

        return $dataBuffer;
    }
}
