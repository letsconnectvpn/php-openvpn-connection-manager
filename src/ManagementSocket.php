<?php

declare(strict_types=1);

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

    public function open(string $socketAddress, int $timeOut = 5): void
    {
        /** @var false|resource $socket */
        $socket = \stream_socket_client($socketAddress, $errno, $errstr, $timeOut);
        if (false === $socket) {
            throw new ManagementSocketException(
                \sprintf('%s (%d)', $errstr, $errno)
            );
        }
        $this->socket = $socket;

        // turn off logging as the output may interfere with our management
        // session, we do not care about the output
        $this->command('log off');
    }

    /**
     * @return array<string>
     */
    public function command(string $command): array
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

    public function close(): void
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
     */
    private static function write($socket, string $data): void
    {
        if (false === \fwrite($socket, $data)) {
            throw new ManagementSocketException('unable to write to socket');
        }
    }

    /**
     * @param resource $socket
     *
     * @return array<string>
     */
    private static function read($socket): array
    {
        $dataBuffer = [];
        while (!\feof($socket) && !self::isEndOfResponse(\end($dataBuffer))) {
            /** @var false|string $readData */
            $readData = \fgets($socket, 4096);
            if (false === $readData) {
                throw new ManagementSocketException('unable to read from socket');
            }
            $dataBuffer[] = \trim($readData);
        }

        return $dataBuffer;
    }

    private static function isEndOfResponse(string $lastLine): bool
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
