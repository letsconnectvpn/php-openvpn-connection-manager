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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ConnectionManager
{
    /** @var array<string> */
    private $socketAddressList;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var ManagementSocketInterface */
    private $managementSocket;

    /**
     * @param array<string> $socketAddressList
     */
    public function __construct(array $socketAddressList)
    {
        $this->socketAddressList = $socketAddressList;
        $this->logger = new NullLogger();
        $this->managementSocket = new ManagementSocket();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setManagementSocket(ManagementSocketInterface $managementSocket): void
    {
        $this->managementSocket = $managementSocket;
    }

    /**
     * @return array<array>
     */
    public function connections(): array
    {
        $connectionList = [];
        foreach ($this->socketAddressList as $socketAddress) {
            try {
                $this->managementSocket->open($socketAddress);
                $connectionList = \array_merge($connectionList, StatusParser::parse($this->managementSocket->command('status 2')));
                $this->managementSocket->close();
            } catch (ManagementSocketException $e) {
                $this->logger->error(
                    \sprintf(
                        'error with socket "%s": "%s"',
                        $socketAddress,
                        $e->getMessage()
                    )
                );
            }
        }

        return $connectionList;
    }

    /**
     * @param array<string> $commonNameList
     */
    public function disconnect(array $commonNameList): int
    {
        $disconnectCount = 0;
        foreach ($this->socketAddressList as $socketAddress) {
            try {
                $this->managementSocket->open($socketAddress);
                foreach ($commonNameList as $commonName) {
                    $result = $this->managementSocket->command(\sprintf('kill %s', $commonName));
                    if (0 === \strpos($result[0], 'SUCCESS: ')) {
                        ++$disconnectCount;
                    }
                }
                $this->managementSocket->close();
            } catch (ManagementSocketException $e) {
                $this->logger->error(
                    \sprintf(
                        'error with socket "%s", message: "%s"',
                        $socketAddress,
                        $e->getMessage()
                    )
                );
            }
        }

        return $disconnectCount;
    }
}
