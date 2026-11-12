<?php

declare(strict_types=1);

namespace HardnessWebhooks\Admin\Authorization;

/**
 * Authorizes only an ERP-resolved administrative context.
 *
 * This class intentionally accepts neither request data nor a company/user ID.
 * The future ERP bridge must pass its already authenticated $g context here.
 */
final class ErpWebhookAdminAuthorization
{
    public const C029_ID = 307;

    /** @param array<string,mixed> $frameworkContext */
    public function assertAuthorized(array $frameworkContext): void
    {
        if (!$this->isAuthorized($frameworkContext)) {
            throw new WebhookAdminAuthorizationException('Webhook administration access denied.');
        }
    }

    /** @param array<string,mixed> $frameworkContext */
    public function isAuthorized(array $frameworkContext): bool
    {
        $userId = $this->positiveInt($frameworkContext['usuarioAtual'] ?? null);
        $profileId = $this->positiveInt($frameworkContext['perfilUsuario'] ?? null);
        $companyId = $this->positiveInt($frameworkContext['empresaAtual'] ?? null);

        if ($userId === null || $profileId === null || $companyId === null) {
            return false;
        }

        if (!$this->matchesResolvedRecord($frameworkContext['C007'] ?? null, 'C007_Id', $userId)
            || !$this->matchesResolvedRecord($frameworkContext['C021'] ?? null, 'C021_Id', $profileId)
            || !$this->matchesResolvedRecord($frameworkContext['C004'] ?? null, 'C004_Id', $companyId)) {
            return false;
        }

        if (!isset($frameworkContext['c029Ids']) || !is_array($frameworkContext['c029Ids'])) {
            return false;
        }

        return !empty($frameworkContext['c029Ids'][self::C029_ID]);
    }

    /** @param mixed $value */
    private function positiveInt($value): ?int
    {
        if (!is_int($value) && !is_string($value)) {
            return null;
        }

        $normalized = filter_var($value, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        return $normalized === false ? null : (int) $normalized;
    }

    /** @param mixed $record */
    private function matchesResolvedRecord($record, string $field, int $expectedId): bool
    {
        return is_array($record)
            && isset($record[$field])
            && $this->positiveInt($record[$field]) === $expectedId;
    }
}
