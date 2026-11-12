<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Context;

use HardnessWebhooks\Admin\Authorization\ErpWebhookAdminAuthorization;

/** Builds the Webhooks context only after the ERP-resolved context is authorized. */
final class ErpWebhookAdminContextFactory
{
    private ErpWebhookAdminAuthorization $authorization;

    public function __construct(?ErpWebhookAdminAuthorization $authorization = null)
    {
        $this->authorization = $authorization ?? new ErpWebhookAdminAuthorization();
    }

    /** @param array<string,mixed> $frameworkContext */
    public function create(array $frameworkContext): WebhookAdminContext
    {
        $this->authorization->assertAuthorized($frameworkContext);

        return new WebhookAdminContext(
            (int) $frameworkContext['usuarioAtual'],
            (int) $frameworkContext['perfilUsuario'],
            (int) $frameworkContext['empresaAtual']
        );
    }

    /** @param mixed $companyId */
    public function createForCompany(array $frameworkContext, \mysqli $connection, $companyId): WebhookAdminContext
    {
        $base = $this->create($frameworkContext);
        return (new WebhookAdminCompanyResolver())->resolve($base, $connection, $companyId);
    }
}
