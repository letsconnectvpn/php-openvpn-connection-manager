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

    /** @var string */
    private $addressFour;

    /** @var string */
    private $addressSix;

    /**
     * @param string $commonName
     * @param string $addressFour
     * @param string $addressSix
     */
    public function __construct($commonName, $addressFour, $addressSix)
    {
        $this->commonName = $commonName;
        $this->addressFour = $addressFour;
        $this->addressSix = $addressSix;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s: [%s,%s]', $this->commonName, $this->addressFour, $this->addressSix);
    }

    /**
     * @return string
     */
    public function getCommonName()
    {
        return $this->commonName;
    }

    /**
     * @return array<int,string>
     */
    public function getAddresses()
    {
        return [$this->addressFour, $this->addressSix];
    }
}
