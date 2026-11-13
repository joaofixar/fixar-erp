<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

const REFORMA_TRIBUTARIA_ROOT = __DIR__ . '/../../../..';
const REFORMA_TRIBUTARIA_MODULE = __DIR__ . '/..';

function reforma_tributaria_config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require REFORMA_TRIBUTARIA_MODULE . '/config/module.php';
    }
    return $config[$key] ?? $default;
}

function reforma_tributaria_url(string $path = ''): string
{
    $base = rtrim((string) reforma_tributaria_config('base_path'), '/');
    return $path === '' ? $base . '/' : $base . '/' . ltrim($path, '/');
}

function reforma_tributaria_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function reforma_tributaria_abort(string $message, int $status = 400): void
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    exit($message);
}

function reforma_tributaria_connect(): ?mysqli
{
    $confUsuario = [];
    require REFORMA_TRIBUTARIA_ROOT . '/bibliotecas/confUsuario.php';
    $connection = @mysqli_connect(
        (string) ($confUsuario['dbHost'] ?? ''),
        (string) ($confUsuario['dbUser'] ?? ''),
        (string) ($confUsuario['dbPass'] ?? ''),
        (string) ($confUsuario['dbDatabase'] ?? '')
    );
    if (!$connection) {
        return null;
    }
    mysqli_set_charset($connection, 'utf8');
    return $connection;
}

/**
 * This module is available to every authenticated Hardness user and has no
 * company context. Empresa_Atual is neither read nor changed.
 */
function reforma_tributaria_require_access(): array
{
    $userId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
    if ($userId <= 0) {
        reforma_tributaria_abort('Entre no Hardness antes de acessar o Painel da Reforma Tributária.', 403);
    }

    $connection = reforma_tributaria_connect();
    if (!$connection) {
        reforma_tributaria_abort('Não foi possível acessar a base de dados do ERP.', 503);
    }

    try {
        $sql = "SELECT C007.C007_Id AS user_id, C007.C007_Nome AS user_name, C007.C007_Primeiro_Nome AS user_first_name
                  FROM C007
                 WHERE C007.C007_Id=? AND C007.C007_Ativo='S'
                 LIMIT 1";
        $statement = mysqli_prepare($connection, $sql);
        if (!$statement) {
            reforma_tributaria_abort('Não foi possível validar a sessão do Painel da Reforma Tributária.', 500);
        }
        mysqli_stmt_bind_param($statement, 'i', $userId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $context = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);
    } finally {
        mysqli_close($connection);
    }

    if (!$context) {
        reforma_tributaria_abort('Sua sessão não está ativa no Hardness.', 403);
    }

    return [
        'user_id' => (int) $context['user_id'],
        'user_name' => (string) ($context['user_first_name'] ?: $context['user_name']),
        'is_root' => (int) $context['user_id'] === 63,
    ];
}

/**
 * Empresas que o usuário já possui vinculadas no ERP. Esta consulta não usa
 * permissões C029 e não muda o contexto global de empresa da sessão.
 */
function reforma_tributaria_user_companies(mysqli $connection, int $userId): array
{
    $root = $userId === 63;
    $sql = "SELECT DISTINCT C004.C004_Id AS company_id, C004.C004_Nome_Empresa AS company_name,
                   C004.C004_Nome_Abreviado AS short_name
              FROM C004" . ($root ? '' : ' INNER JOIN C019 ON C004.C004_Id=C019.C019_C004_Id') . "
             WHERE C004.C004_Flag_Ativo='S'" . ($root ? '' : ' AND C019.C019_C007_Id=?') . "
             ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id";
    $statement = mysqli_prepare($connection, $sql);
    if (!$statement) return [];
    if (!$root) mysqli_stmt_bind_param($statement, 'i', $userId);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $companies = [];
    while ($result && ($row = mysqli_fetch_assoc($result))) {
        $id = (int) $row['company_id'];
        $companies[$id] = [
            'id' => $id,
            'name' => (string) $row['company_name'],
            'short_name' => (string) ($row['short_name'] ?: $row['company_name']),
        ];
    }
    mysqli_stmt_close($statement);
    return $companies;
}
