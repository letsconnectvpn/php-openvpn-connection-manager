<?php

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
    /** @var string */
    private $remoteSocket;

    /** @var array<int> */
    private $managementPortList;

    /** @var ManagementSocketInterface */
    private $managementSocket;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * @param string                         $remoteSocket
     * @param array<int>                     $managementPortList
     * @param ManagementSocketInterface|null $managementSocket
     */
    public function __construct($remoteSocket, array $managementPortList, ManagementSocketInterface $managementSocket = null)
    {
        $this->remoteSocket = $remoteSocket;
        $this->managementPortList = $managementPortList;
        if (null === $managementSocket) {
            $managementSocket = new ManagementSocket();
        }
        $this->managementSocket = $managementSocket;
        $this->logger = new NullLogger();
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array<int, array>
     */
    public function connections()
    {
        try {
            $this->managementSocket->open($this->remoteSocket);
            $this->managementSocket->command(\sprintf('SET_OPENVPN_MANAGEMENT_PORT_LIST %s', \implode(' ', $this->managementPortList)));
            $connectionList = ClientListParser::parse($this->managementSocket->command('LIST'));
            $this->managementSocket->close();

            return $connectionList;
        } catch (ManagementSocketException $e) {
            $this->logger->error(
                \sprintf(
                    'error with socket "%s": "%s"',
                    $this->remoteSocket,
                    $e->getMessage()
                )
            );

            return [];
        }
    }

    /**
     * @param array<string> $commonNameList
     *
     * @return int
     */
    public function disconnect(array $commonNameList)
    {
        try {
            $this->managementSocket->open($this->remoteSocket);
            $this->managementSocket->command(\sprintf('SET_OPENVPN_MANAGEMENT_PORT_LIST %s', \implode(' ', $this->managementPortList)));
            $disconnectCount = 0;
            foreach ($commonNameList as $commonName) {
                $this->managementSocket->command(\sprintf('DISCONNECT %s', $commonName));
                // XXX parse result and increment disconnectCount!
                ++$disconnectCount;
            }
            $this->managementSocket->close();

            return $disconnectCount;
        } catch (ManagementSocketException $e) {
            $this->logger->error(
                \sprintf(
                    'error with socket "%s", message: "%s"',
                    $this->remoteSocket,
                    $e->getMessage()
                )
            );

            return 0;
        }
    }
}
