<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn;

class ClientInfo
{
    /** @var string */
    private $commonName;

    /** @var array<string> */
    private $addrList;

    /**
     * @param string        $commonName
     * @param array<string> $addrList
     */
    public function __construct($commonName, array $addrList)
    {
        $this->commonName = $commonName;
        $this->addrList = $addrList;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s [%s, %s]', $this->commonName, $this->addrList[0], $this->addrList[1]);
    }

    /**
     * @return string
     */
    public function getCommonName()
    {
        return $this->commonName;
    }

    /**
     * @return array<string>
     */
    public function getAddrList()
    {
        return $this->addrList;
    }
}
