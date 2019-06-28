<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
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

    public function __construct(bool $connectFail = false)
    {
        $this->connectFail = $connectFail;
        $this->socketAddress = null;
    }

    public function open(string $socketAddress): void
    {
        $this->socketAddress = $socketAddress;
        if ($this->connectFail) {
            throw new ManagementSocketException('unable to connect to socket');
        }
    }

    /**
     * @return array<string>
     */
    public function command(string $command): array
    {
        switch ($command) {
            case 'status 2':
                switch ($this->socketAddress) {
                    case 'tcp://127.0.0.1:11940':
                        return \explode("\n", self::readFile(__DIR__.'/socket/status_with_clients.txt'));
                    case 'tcp://127.0.0.1:11941':
                        return \explode("\n", self::readFile(__DIR__.'/socket/status_no_clients.txt'));
                    case 'tcp://127.0.0.1:11945':
                        return \explode("\n", self::readFile(__DIR__.'/socket/status_with_clients.txt'));
                    case 'tcp://127.0.0.1:11946':
                        return \explode("\n", self::readFile(__DIR__.'/socket/status_with_clients_two.txt'));
                    default:
                        throw new Exception('no match for this command');
                }
                // no break
            case 'kill foo':
                if ('tcp://127.0.0.1:11940' === $this->socketAddress) {
                    return \explode("\n", self::readFile(__DIR__.'/socket/kill_success.txt'));
                }

                return \explode("\n", self::readFile(__DIR__.'/socket/kill_error.txt'));
            default:
                throw new Exception('no match for this command');
        }
    }

    public function close(): void
    {
        $this->socketAddress = null;
    }

    private static function readFile(string $fileName): string
    {
        if (false === $fileContent = \file_get_contents($fileName)) {
            throw new Exception(\sprintf('unable to read file "%s"', $fileName));
        }

        return $fileContent;
    }
}
