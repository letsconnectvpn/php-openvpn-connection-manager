<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\LC\OpenVpn;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SURFnet\LC\OpenVpn\Exception\ManagementSocketException;

class ConnectionManager
{
    /** @var array<string> */
    private $socketAddressList;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var ManagementSocketInterface */
    private $managementSocket;

    public function __construct(array $socketAddressList, LoggerInterface $logger = null, ManagementSocketInterface $managementSocket = null)
    {
        $this->socketAddressList = $socketAddressList;
        if (null === $logger) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;

        if (null === $managementSocket) {
            $managementSocket = new ManagementSocket();
        }
        $this->managementSocket = $managementSocket;
    }

    /**
     * @return array
     */
    public function connections()
    {
        $connectionList = [];
        foreach ($this->socketAddressList as $socketAddress) {
            try {
                $this->managementSocket->open($socketAddress);
                $connectionList += StatusParser::parse($this->managementSocket->command('status 2'));
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
     *
     * @return int
     */
    public function disconnect(array $commonNameList)
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
