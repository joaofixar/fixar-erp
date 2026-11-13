<?php
namespace hardness;

require_once("old/lib/funcoes.php");
require_once("bibliotecas/segurancaSenha.php");
require_once("rest_api/autoload.php");

definirClasse('API001PolicyAdmin');

class API001PolicyAdmin_
{
    private const API_REAUTH_TTL = 900;

    /** @var array<string, mixed> */
    private array $confUsuario;

    /** @var array<string, mixed> */
    private array $g;

    /** @var array<string, mixed> */
    private array $sys;

    public function __construct()
    {
        global $confUsuario, $g, $sys;

        $this->confUsuario = is_array($confUsuario) ? $confUsuario : [];
        $this->g = is_array($g) ? $g : [];
        $this->sys = is_array($sys) ? $sys : [];
    }

    public function assertAuthorized(): void
    {
        $this->startSessionIfNeeded();
        $userId = (int) ($this->g['usuarioAtual'] ?? 0);
        $isSupportUser = $this->isCurrentUserSupport();

        if ($userId !== 63 && !$isSupportUser) {
            throw new \RuntimeException('Acesso não autorizado para esta funcionalidade.');
        }
    }

    public function assertRestApiUnlocked(): void
    {
        $this->assertAuthorized();
        if (!$this->isRestApiUnlocked()) {
            throw new \RuntimeException('Informe sua senha de acesso para liberar esta área.');
        }
    }

    public function resetRestApiUnlock(): void
    {
        $this->assertAuthorized();
        $this->startSessionIfNeeded();
        unset($_SESSION[$this->getRestApiSessionKey()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getRestApiAccessStatus(): array
    {
        $this->assertAuthorized();
        $this->startSessionIfNeeded();
        $expiresAt = (int) ($_SESSION[$this->getRestApiSessionKey()] ?? 0);

        return [
            'unlocked' => $expiresAt > time(),
            'expires_at' => $expiresAt > time() ? $expiresAt : 0,
            'ttl_minutes' => (int) floor(self::API_REAUTH_TTL / 60),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function unlockRestApi(string $password): array
    {
        $this->assertAuthorized();
        if (!$this->isLocalhostAccess()) {
            if ($password === '') {
                throw new \RuntimeException('Informe sua senha de acesso.');
            }

            if (!$this->isCurrentUserPasswordValid($password)) {
                throw new \RuntimeException('Senha de acesso inválida.');
            }
        }

        $this->startSessionIfNeeded();
        $expiresAt = time() + self::API_REAUTH_TTL;
        $_SESSION[$this->getRestApiSessionKey()] = $expiresAt;

        return [
            'unlocked' => true,
            'expires_at' => $expiresAt,
            'ttl_minutes' => (int) floor(self::API_REAUTH_TTL / 60),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getPolicyStatus(): array
    {
        return $this->getService()->getPolicyStatus();
    }

    /**
     * @return array<int, string>
     */
    public function listAvailableTables(): array
    {
        return $this->getService()->listAvailableTables();
    }

    /**
     * @return array<string, mixed>
     */
    public function loadTable(string $table): array
    {
        return $this->getService()->loadTable($table);
    }

    /**
     * @param array<int, string> $fields
     */
    public function previewTable(string $table, string $mode, array $fields): string
    {
        return $this->getService()->previewTable($table, $mode, $fields);
    }

    /**
     * @param array<int, string> $fields
     * @return array<string, mixed>
     */
    public function saveTable(string $table, string $mode, array $fields): array
    {
        return $this->getService()->saveTable($table, $mode, $fields);
    }

    /**
     * @return array<string, mixed>
     */
    public function removeTable(string $table): array
    {
        return $this->getService()->removeTable($table);
    }

    /**
     * @return array<string, mixed>
     */
    public function getTokenStatus(): array
    {
        return $this->getTokenService()->getTokenStatus();
    }

    /**
     * @return array<string, mixed>
     */
    public function createToken(string $name, string $observation = ''): array
    {
        return $this->getTokenService()->createToken($name, $observation);
    }

    /**
     * @return array<string, mixed>
     */
    public function updateTokenStatus(int $tokenId, string $status): array
    {
        return $this->getTokenService()->updateTokenStatus($tokenId, $status);
    }

    private function getService(): \RestApi\Admin\PolicyAdminService
    {
        static $service;
        $appConfig = $this->getAppConfig();
        $connectionFactory = new \RestApi\Database\ConnectionFactory($appConfig);

        if ($service instanceof \RestApi\Admin\PolicyAdminService) {
            return $service;
        }

        $repository = new \RestApi\Security\AccessPolicyRepository($appConfig);
        $schemaRepository = new \RestApi\Database\SchemaRepository($appConfig);

        $tenantContext = [
            'host' => (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'),
            'dbDatabase' => (string) ($this->confUsuario['dbDatabase'] ?? ''),
            'dbHost' => (string) ($this->confUsuario['dbHost'] ?? ''),
            'dbUser' => (string) ($this->confUsuario['dbUser'] ?? ''),
            'dbPass' => (string) ($this->confUsuario['dbPass'] ?? ''),
            'pathRaiz' => (string) ($this->confUsuario['pathRaiz'] ?? ''),
            'urlRaiz' => (string) ($this->confUsuario['urlRaiz'] ?? ''),
        ];

        $service = (new \RestApi\Admin\PolicyAdminService(
            $repository,
            $schemaRepository,
            $connectionFactory
        ))->forTenant($tenantContext);

        return $service;
    }

    private function getTokenService(): \RestApi\Admin\TokenAdminService
    {
        static $service;

        if ($service instanceof \RestApi\Admin\TokenAdminService) {
            return $service;
        }

        $appConfig = $this->getAppConfig();
        $connectionFactory = new \RestApi\Database\ConnectionFactory($appConfig);
        $tenantContext = [
            'host' => (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'),
            'dbDatabase' => (string) ($this->confUsuario['dbDatabase'] ?? ''),
            'dbHost' => (string) ($this->confUsuario['dbHost'] ?? ''),
            'dbUser' => (string) ($this->confUsuario['dbUser'] ?? ''),
            'dbPass' => (string) ($this->confUsuario['dbPass'] ?? ''),
            'pathRaiz' => (string) ($this->confUsuario['pathRaiz'] ?? ''),
            'urlRaiz' => (string) ($this->confUsuario['urlRaiz'] ?? ''),
        ];

        $service = (new \RestApi\Admin\TokenAdminService(
            $connectionFactory,
            $appConfig
        ))->forTenant($tenantContext);

        return $service;
    }

    /**
     * @return array<string, mixed>
     */
    private function getAppConfig(): array
    {
        static $appConfig;

        if (is_array($appConfig)) {
            return $appConfig;
        }

        $config = new \RestApi\Core\Config('rest_api/config');
        $appConfig = $config->load('app');

        return $appConfig;
    }

    private function isRestApiUnlocked(): bool
    {
        $this->startSessionIfNeeded();
        $expiresAt = (int) ($_SESSION[$this->getRestApiSessionKey()] ?? 0);
        if ($expiresAt <= time()) {
            unset($_SESSION[$this->getRestApiSessionKey()]);
            return false;
        }

        return true;
    }

    private function getRestApiSessionKey(): string
    {
        $userId = (int) ($this->g['usuarioAtual'] ?? 0);
        $db = (string) ($this->confUsuario['dbDatabase'] ?? '');

        return 'api001_rest_api_unlock_' . $userId . '_' . $db;
    }

    private function startSessionIfNeeded(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    private function isCurrentUserPasswordValid(string $password): bool
    {
        $user = $this->getCurrentUser();

        return is_array($user)
            && \hardness\SegurancaSenha::validarSenhaArmazenada($password, (string) ($user['C007_Senha'] ?? ''));
    }

    private function isCurrentUserSupport(): bool
    {
        $user = $this->getCurrentUser();

        return is_array($user)
            && (string) ($user['C007_Usuario_Suporte'] ?? 'N') === 'S';
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getCurrentUser(): ?array
    {
        $userId = (int) ($this->g['usuarioAtual'] ?? 0);
        if ($userId <= 0) {
            return null;
        }

        $result = mysql_query("SELECT C007_Senha, C007_Usuario_Suporte FROM C007 WHERE C007_Id = '{$userId}' AND C007_Ativo = 'S' LIMIT 1");
        $user = $result ? mysql_fetch_assoc($result) : null;

        return is_array($user) ? $user : null;
    }

    private function isLocalhostAccess(): bool
    {
        $host = strtolower(trim((string) ($_SERVER['HTTP_HOST'] ?? '')));
        $host = preg_replace('/:\d+$/', '', $host) ?? '';

        return $host === 'localhost' || $host === '127.0.0.1' || $host === '::1';
    }

}
