<?php
declare(strict_types=1);
namespace Hardness\Observability\Legacy;
interface LegacyCredentialValidator { public function authenticate(array $tenant, string $login, string $password): ?array; }
