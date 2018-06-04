<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2018, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\LC\OpenVpn;

interface ManagementSocketInterface
{
    /**
     * @param string $socketAddress
     * @param int    $timeOut
     *
     * @return void
     */
    public function open($socketAddress, $timeOut = 5);

    /**
     * @param string $command
     *
     * @return array<string>
     */
    public function command($command);

    /**
     * @return void
     */
    public function close();
}
