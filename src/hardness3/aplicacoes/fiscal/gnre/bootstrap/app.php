<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

const GNRE_ROOT = __DIR__ . '/../../../..';
const GNRE_MODULE = __DIR__ . '/..';
const GNRE_SESSION = 'gnre';

function gnre_config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require GNRE_MODULE . '/config/module.php';
    }
    return $config[$key] ?? $default;
}

function gnre_url(string $path = ''): string
{
    $base = rtrim((string) gnre_config('base_path'), '/');
    $path = preg_replace('#^public/#', '', ltrim($path, '/'));
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function gnre_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function gnre_connect(): ?mysqli
{
    $confUsuario = [];
    require GNRE_ROOT . '/bibliotecas/confUsuario.php';
    $connection = @mysqli_connect(
        (string) ($confUsuario['dbHost'] ?? ''),
        (string) ($confUsuario['dbUser'] ?? ''),
        (string) ($confUsuario['dbPass'] ?? ''),
        (string) ($confUsuario['dbDatabase'] ?? '')
    );
    if (!$connection) return null;
    mysqli_set_charset($connection, 'utf8');
    return $connection;
}

function gnre_csrf_token(): string
{
    if (empty($_SESSION[GNRE_SESSION]['csrf'])) {
        $_SESSION[GNRE_SESSION]['csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION[GNRE_SESSION]['csrf'];
}

function gnre_require_csrf(): void
{
    $token = (string) ($_POST['csrf'] ?? '');
    if ($token === '' || !hash_equals(gnre_csrf_token(), $token)) {
        gnre_abort('Solicitação inválida. Atualize a página e tente novamente.', 403);
    }
}

function gnre_abort(string $message, int $status = 400): void
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    exit($message);
}

/**
 * Revalida o acesso na base a cada requisição. O módulo usa a sessão do ERP,
 * mas não a altera, não cria login alternativo e não confia em empresa/perfil
 * recebidos pelo navegador.
 */
/** Empresas ativas nas quais o usuário tem a permissão específica da GNRE. */
function gnre_authorized_companies(int $userId): array
{
    $connection = gnre_connect();
    if (!$connection) {
        gnre_abort('Não foi possível acessar a base de dados do ERP.', 503);
    }

    try {
        $permissionId = (int) gnre_config('permission_access');
        $sql = "SELECT DISTINCT C019.C019_C004_Id AS company_id,
                       C019.C019_C021_Id AS profile_id,
                       C004.C004_Nome_Empresa AS company_name,
                       C004.C004_Nome_Abreviado AS company_short_name,
                       C007.C007_Id AS user_id,
                       C007.C007_Nome AS user_name,
                       C007.C007_Primeiro_Nome AS user_first_name
                FROM C019
                INNER JOIN C030 ON C030.C030_C021_Id=C019.C019_C021_Id
                    AND C030.C030_C029_Id=? AND C030.C030_Acesso_Liberado='S'
                INNER JOIN C004 ON C004.C004_Id=C019.C019_C004_Id
                    AND C004.C004_Flag_Ativo='S'
                INNER JOIN C007 ON C007.C007_Id=C019.C019_C007_Id
                    AND C007.C007_Ativo='S'
                WHERE C019.C019_C007_Id=?
                    AND C019.C019_C021_Id > 0
                ORDER BY C004.C004_Nome_Abreviado, C004.C004_Nome_Empresa, C004.C004_Id";
        $statement = mysqli_prepare($connection, $sql);
        if (!$statement) {
            gnre_abort('Não foi possível validar o acesso à GNRE.', 500);
        }
        mysqli_stmt_bind_param($statement, 'ii', $permissionId, $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $companies = [];
        $seenCompanies = [];
        while ($result && ($row = mysqli_fetch_assoc($result))) {
            $row['company_id'] = (int) $row['company_id'];
            $row['profile_id'] = (int) $row['profile_id'];
            $row['user_id'] = (int) $row['user_id'];
            $row['company_name'] = (string) ($row['company_short_name'] ?: $row['company_name']);
            $row['user_name'] = (string) ($row['user_first_name'] ?: $row['user_name']);
            if (!isset($seenCompanies[$row['company_id']])) {
                $companies[] = $row;
                $seenCompanies[$row['company_id']] = true;
            }
        }
        mysqli_stmt_close($statement);
    } finally {
        mysqli_close($connection);
    }

    return $companies;
}

/**
 * A empresa da GNRE é deliberadamente separada de Empresa_Atual do ERP.
 * A cada request a lista e a empresa selecionada são conferidas novamente.
 */
function gnre_require_access(): array
{
    $userId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
    $erpCompanyId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    if ($userId <= 0 || $erpCompanyId <= 0) {
        gnre_abort('Entre no Hardness e selecione uma empresa antes de acessar a GNRE.', 403);
    }
    $companies = gnre_authorized_companies($userId);
    if (!$companies) {
        gnre_abort('Seu perfil não possui acesso à GNRE para a empresa selecionada.', 403);
    }
    // O atalho da tela inicial sugere a empresa ativa do ERP. O valor nunca
    // autoriza acesso por si: abaixo ele precisa constar na lista recém-lida
    // de C019/C030 para o usuário autenticado.
    $requestedCompanyId = filter_input(INPUT_GET, 'gnre_company', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $selected = $requestedCompanyId !== false && $requestedCompanyId !== null
        ? (int) $requestedCompanyId
        : (int) ($_SESSION[GNRE_SESSION]['company_id'] ?? $erpCompanyId);
    $context = null;
    foreach ($companies as $company) if ($company['company_id'] === $selected) { $context = $company; break; }
    if ($requestedCompanyId !== false && $requestedCompanyId !== null && $context === null) {
        gnre_abort('A empresa informada não está disponível para GNRE.', 403);
    }
    if ($context === null) {
        // A empresa do ERP é apenas a sugestão inicial; nunca é regravada aqui.
        foreach ($companies as $company) if ($company['company_id'] === $erpCompanyId) { $context = $company; break; }
        $context = $context ?: $companies[0];
        gnre_clear_company_state();
    }
    $_SESSION[GNRE_SESSION]['company_id'] = $context['company_id'];
    $_SESSION[GNRE_SESSION]['companies'] = $companies;
    $_SESSION[GNRE_SESSION]['last_company_id'] = $context['company_id'];
    return $context;
}

function gnre_clear_company_state(): void
{
    unset($_SESSION[GNRE_SESSION]['config_uf'], $_SESSION[GNRE_SESSION]['filters'], $_SESSION[GNRE_SESSION]['temporary']);
}

function gnre_select_company(int $companyId): array
{
    $userId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
    foreach (gnre_authorized_companies($userId) as $company) {
        if ($company['company_id'] === $companyId) {
            if ((int) ($_SESSION[GNRE_SESSION]['company_id'] ?? 0) !== $companyId) gnre_clear_company_state();
            $_SESSION[GNRE_SESSION]['company_id'] = $companyId;
            return $company;
        }
    }
    gnre_abort('A empresa selecionada não está disponível para GNRE.', 403);
}
