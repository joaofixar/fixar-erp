<?php

declare(strict_types=1);

namespace HardnessMcp\Auth;

final class ApiKeyParser
{
    public function parseAuthorization(?string $authorization): ?ApiKey
    {
        if ($authorization === null || preg_match('/\\ABearer[ ]+(hrd_mcp_v1_([A-Z2-7]{16})_([A-Za-z0-9_-]{43}))\\z/', $authorization, $matches) !== 1) {
            return null;
        }
        return new ApiKey($matches[2], $matches[3]);
    }
}
