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
     * @param array<int, string> $statusData
     *
     * @return array<int, array>
     */
    public static function parse(array $statusData)
    {
        $clientList = [];

        // verify "OK: <n>"
        if (0 !== \strpos($statusData[0], 'OK: ')) {
            // the response does not start with "OK: <n>", so give up
            return [];
        }
        $connectionCount = (int) \substr($statusData[0], 4);
        for ($i = 1; $i <= $connectionCount; ++$i) {
            $clientInfo = \explode(' ', $statusData[$i]);
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
