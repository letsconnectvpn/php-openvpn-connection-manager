<?php

declare(strict_types=1);

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\OpenVpn;

interface ManagementSocketInterface
{
    public function open(string $socketAddress): void;

    /**
     * @return array<string>
     */
    public function command(string $command): array;

    public function close(): void;
}
