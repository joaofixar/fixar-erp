<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi\Authorization;

final class ErpAdminAuthorization
{
    /** @param array<string,mixed> $frameworkContext */
    public function assertAuthorized(array $frameworkContext): void
    {
        if (
            !isset($frameworkContext['usuarioAtual'])
            || (int) $frameworkContext['usuarioAtual'] <= 0
            || !isset($frameworkContext['nivelAlcada'])
            || (int) $frameworkContext['nivelAlcada'] !== 4
        ) {
            throw new AdminAuthorizationException('MCP administration access denied.');
        }
    }
}
