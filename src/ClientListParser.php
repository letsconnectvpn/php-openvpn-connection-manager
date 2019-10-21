<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn;

/**
 * Parses the response from the daemon "LIST" command.
 */
class ClientListParser
{
    /**
     * @param array<string> $responseList
     *
     * @return array<array>
     */
    public static function parse(array $responseList)
    {
        $clientList = [];
        foreach ($responseList as $clientData) {
            $clientInfo = \explode(' ', $clientData);
            $clientList[] = [
                'common_name' => $clientInfo[0],
                'virtual_address' => [
                    $clientInfo[1],
                    $clientInfo[2],
                ],
            ];
        }

        return $clientList;
    }
}
