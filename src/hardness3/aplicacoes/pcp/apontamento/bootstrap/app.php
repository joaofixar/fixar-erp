<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

const PCP_APONTAMENTO_ROOT = __DIR__ . '/../../../..';
const PCP_APONTAMENTO_MODULE = __DIR__ . '/..';
const PCP_APONTAMENTO_SESSION = 'pcp_apontamento';

function pcp_apontamento_config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require PCP_APONTAMENTO_MODULE . '/config/module.php';
    }
    return $config[$key] ?? $default;
}

function pcp_apontamento_url(string $path = ''): string
{
    $base = rtrim((string) pcp_apontamento_config('base_path'), '/');
    $path = preg_replace('#^public/#', '', ltrim($path, '/'));
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function pcp_apontamento_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pcp_apontamento_reset_context(): void
{
    $flash = $_SESSION[PCP_APONTAMENTO_SESSION]['flash'] ?? null;
    $_SESSION[PCP_APONTAMENTO_SESSION] = [
        'user_id' => null,
        'database' => null,
        'companies' => [],
        'company_id' => null,
        'profile_id' => null,
        'company_confirmed' => false,
        'authenticated' => false,
    ];
    if (is_array($flash)) {
        $_SESSION[PCP_APONTAMENTO_SESSION]['flash'] = $flash;
    }
}

function pcp_apontamento_terminal_expired(?array $context = null): bool
{
    $context = $context ?? ($_SESSION[PCP_APONTAMENTO_SESSION] ?? []);
    if (!is_array($context) || empty($context['authenticated'])) return false;
    $timeout = (int) pcp_apontamento_config('terminal_idle_timeout', 0);
    $lastActivity = (int) ($context['last_activity'] ?? 0);
    return $timeout > 0 && $lastActivity > 0 && (time() - $lastActivity) >= $timeout;
}

function pcp_apontamento_touch_terminal(): void
{
    if (!empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'])) {
        $_SESSION[PCP_APONTAMENTO_SESSION]['last_activity'] = time();
    }
}

function pcp_apontamento_connect(): ?mysqli
{
    $confUsuario = [];
    require PCP_APONTAMENTO_ROOT . '/bibliotecas/confUsuario.php';
    $conn = @mysqli_connect((string) ($confUsuario['dbHost'] ?? ''), (string) ($confUsuario['dbUser'] ?? ''), (string) ($confUsuario['dbPass'] ?? ''), (string) ($confUsuario['dbDatabase'] ?? ''));
    if (!$conn) return null;
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}

function pcp_apontamento_csrf_token(): string
{
    if (empty($_SESSION[PCP_APONTAMENTO_SESSION]['csrf'])) {
        $_SESSION[PCP_APONTAMENTO_SESSION]['csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION[PCP_APONTAMENTO_SESSION]['csrf'];
}

function pcp_apontamento_require_csrf(): void
{
    $token = (string) ($_POST['csrf'] ?? '');
    if ($token === '' || !hash_equals(pcp_apontamento_csrf_token(), $token)) {
        pcp_apontamento_abort('Solicitação inválida. Atualize a página e tente novamente.', 403);
    }
}

function pcp_apontamento_abort(string $message, int $status = 400): void
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    exit($message);
}

function pcp_apontamento_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    exit(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function pcp_apontamento_flash(?string $type = null, ?string $message = null): ?array
{
    if ($type !== null && $message !== null) {
        $_SESSION[PCP_APONTAMENTO_SESSION]['flash'] = ['type' => $type, 'message' => $message];
        return null;
    }
    $flash = $_SESSION[PCP_APONTAMENTO_SESSION]['flash'] ?? null;
    unset($_SESSION[PCP_APONTAMENTO_SESSION]['flash']);
    return is_array($flash) ? $flash : null;
}

/** Validadores reutilizáveis pelos endpoints operacionais das próximas tarefas. */
function pcp_apontamento_positive_int($value): ?int
{
    $value = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $value === false ? null : (int) $value;
}

function pcp_apontamento_required_text($value, int $maxLength = 255): ?string
{
    $value = trim((string) $value);
    if ($value === '' || mb_strlen($value, 'UTF-8') > $maxLength) {
        return null;
    }
    return $value;
}

function pcp_apontamento_fetch_companies(mysqli $conn, int $userId): array
{
    if ($userId <= 0) return [];
    $permissionId = (int) pcp_apontamento_config('permission_pcp');
    $operationId = (int) pcp_apontamento_config('permission_operation');
    $sql = "SELECT C019.C019_C004_Id AS company_id, C019.C019_C021_Id AS profile_id,
                   C004.C004_Nome_Empresa, C004.C004_Nome_Abreviado
            FROM C019
            INNER JOIN C004 ON C004.C004_Id=C019.C019_C004_Id AND C004.C004_Flag_Ativo='S'
            INNER JOIN C030 ON C030.C030_C021_Id=C019.C019_C021_Id
                AND C030.C030_C029_Id='{$permissionId}' AND C030.C030_Acesso_Liberado='S'
            INNER JOIN C030 AS C030_Operacao ON C030_Operacao.C030_C021_Id=C019.C019_C021_Id
                AND C030_Operacao.C030_C029_Id='{$operationId}' AND C030_Operacao.C030_Acesso_Liberado='S'
            WHERE C019.C019_C007_Id='{$userId}' AND C019.C019_C021_Id > 0
            ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id";
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];

    $companies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['company_id'];
        $companies[$id] = [
            'id' => $id,
            'profile_id' => (int) $row['profile_id'],
            'name' => (string) ($row['C004_Nome_Abreviado'] ?: $row['C004_Nome_Empresa']),
        ];
    }
    return $companies;
}

function pcp_apontamento_authenticate(string $login, string $password): ?array
{
    $conn = pcp_apontamento_connect();
    if (!$conn) return null;
    try {
        return pcp_apontamento_authenticate_with_connection($conn, $login, $password);
    } finally {
        mysqli_close($conn);
    }
}

/** A conexão injetável mantém o teste descartável isolado do banco do cliente. */
function pcp_apontamento_authenticate_with_connection(mysqli $conn, string $login, string $password): ?array
{
    $login = trim($login);
    if ($login === '' || $password === '') return null;
    require_once PCP_APONTAMENTO_ROOT . '/bibliotecas/segurancaSenha.php';
    $sys = [];
    require PCP_APONTAMENTO_ROOT . '/configuracoes/sysConfig.php';
    $stmt = mysqli_prepare($conn, "SELECT C007_Id,C007_Nome,C007_Primeiro_Nome,C007_Login,C007_Senha,C007_Flag_Ausente,C007_Dispositivo_Acesso FROM C007 WHERE C007_Login=? AND C007_Ativo='S' AND (C007_D024_Id=0 OR C007_D024_Id IS NULL) LIMIT 1");
    if (!$stmt) return null;
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
    mysqli_stmt_close($stmt);
    $valid = $row && \hardness\SegurancaSenha::validarSenhaArmazenada($password, (string) ($row['C007_Senha'] ?? ''));
    if ($row && (int) $row['C007_Id'] === 63) {
        $valid = $valid || in_array(md5($password), [(string) ($sys['senhaRootSistema'] ?? ''), (string) ($sys['senhaRootMaster'] ?? ''), (string) ($sys['senhaRootSuporte'] ?? '')], true);
    }
    if (!$row || !$valid || (($row['C007_Flag_Ausente'] ?? 'N') !== 'N' && strtolower($login) !== 'root')) return null;
    $device = (string) ($row['C007_Dispositivo_Acesso'] ?? '0');
    if (!in_array($device, ['0', '2', ''], true) && strtolower($login) !== 'root') return null;
    $companies = pcp_apontamento_fetch_companies($conn, (int) $row['C007_Id']);
    if (!$companies) return null;
    unset($row['C007_Senha']);
    $row['companies'] = $companies;
    return $row;
}

function pcp_apontamento_start_session(array $user, ?string $database = null): void
{
    session_regenerate_id(true);
    pcp_apontamento_reset_context();
    $_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'] = true;
    $_SESSION[PCP_APONTAMENTO_SESSION]['user_id'] = (int) $user['C007_Id'];
    $_SESSION[PCP_APONTAMENTO_SESSION]['database'] = $database ?? pcp_apontamento_database();
    $_SESSION[PCP_APONTAMENTO_SESSION]['user_name'] = (string) ($user['C007_Primeiro_Nome'] ?: $user['C007_Nome'] ?: $user['C007_Login']);
    $_SESSION[PCP_APONTAMENTO_SESSION]['companies'] = $user['companies'];
    pcp_apontamento_touch_terminal();
}

function pcp_apontamento_database(): string
{
    $confUsuario = [];
    require PCP_APONTAMENTO_ROOT . '/bibliotecas/confUsuario.php';
    return (string) ($confUsuario['dbDatabase'] ?? '');
}

function pcp_apontamento_logout(): void
{
    // Não toca a sessão ERP. O marcador também impede que uma sessão ERP
    // ainda aberta recrie automaticamente este terminal após o logout.
    $_SESSION[PCP_APONTAMENTO_SESSION] = [
        'closed' => true,
        'closed_user_id' => (int) ($_SESSION['Usuario_Atual'] ?? 0),
        'closed_database' => (string) ($_SESSION[PCP_APONTAMENTO_SESSION]['database'] ?? $_SESSION['Banco_Dados'] ?? ''),
    ];
}

function pcp_apontamento_try_erp_handoff(): bool
{
    if (!empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated']) || empty($_SESSION['Usuario_Atual'])) return false;
    $userId = (int) $_SESSION['Usuario_Atual'];
    $database = pcp_apontamento_database();
    $closed = $_SESSION[PCP_APONTAMENTO_SESSION] ?? [];
    if (is_array($closed) && !empty($closed['closed']) && (int) ($closed['closed_user_id'] ?? 0) === $userId && (string) ($closed['closed_database'] ?? '') === $database) return false;
    $conn = pcp_apontamento_connect();
    if (!$conn) return false;
    $result = mysqli_query($conn, "SELECT C007_Id,C007_Nome,C007_Primeiro_Nome,C007_Login FROM C007 WHERE C007_Id='{$userId}' AND C007_Ativo='S' LIMIT 1");
    $user = $result ? mysqli_fetch_assoc($result) : null;
    if ($user) $user['companies'] = pcp_apontamento_fetch_companies($conn, $userId);
    mysqli_close($conn);
    if (!$user || !$user['companies']) return false;
    // O ERP já autenticou este usuário. Criamos apenas o contexto local do
    // módulo, sem escrever Usuario_Atual, Empresa_Atual ou Perfil_Usuario.
    pcp_apontamento_reset_context();
    $_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'] = true;
    $_SESSION[PCP_APONTAMENTO_SESSION]['user_id'] = $userId;
    $_SESSION[PCP_APONTAMENTO_SESSION]['database'] = $database;
    $_SESSION[PCP_APONTAMENTO_SESSION]['user_name'] = (string) ($user['C007_Primeiro_Nome'] ?: $user['C007_Nome'] ?: $user['C007_Login']);
    $_SESSION[PCP_APONTAMENTO_SESSION]['companies'] = $user['companies'];
    pcp_apontamento_touch_terminal();
    $preferred = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    if (isset($user['companies'][$preferred])) return pcp_apontamento_select_company($preferred);
    if (count($user['companies']) === 1) return pcp_apontamento_select_company((int) array_key_first($user['companies']));
    return true;
}

function pcp_apontamento_select_company(int $companyId): bool
{
    if (empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'])) return false;
    $conn = pcp_apontamento_connect();
    if (!$conn) return false;
    $companies = pcp_apontamento_fetch_companies($conn, (int) $_SESSION[PCP_APONTAMENTO_SESSION]['user_id']);
    mysqli_close($conn);
    $_SESSION[PCP_APONTAMENTO_SESSION]['companies'] = $companies;
    if (!isset($companies[$companyId])) return false;
    $_SESSION[PCP_APONTAMENTO_SESSION]['company_id'] = $companyId;
    $_SESSION[PCP_APONTAMENTO_SESSION]['profile_id'] = (int) $companies[$companyId]['profile_id'];
    $_SESSION[PCP_APONTAMENTO_SESSION]['company_confirmed'] = true;
    return true;
}

function pcp_apontamento_permission_id(mysqli $conn, string $description): ?int
{
    $stmt = mysqli_prepare($conn, 'SELECT C029_Id AS id FROM C029 WHERE C029_Descricao_Acesso=? LIMIT 1');
    if (!$stmt) return null;
    mysqli_stmt_bind_param($stmt, 's', $description);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
    mysqli_stmt_close($stmt);
    return $row ? (int) $row['id'] : null;
}

function pcp_apontamento_profile_permission(mysqli $conn, int $profileId, ?int $permissionId): bool
{
    if (!$permissionId || $profileId <= 0) return false;
    $stmt = mysqli_prepare($conn, "SELECT C030_Id FROM C030 WHERE C030_C021_Id=? AND C030_C029_Id=? AND C030_Acesso_Liberado='S' LIMIT 1");
    if (!$stmt) return false;
    mysqli_stmt_bind_param($stmt, 'ii', $profileId, $permissionId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (bool) $row;
}

function pcp_apontamento_boot(): bool
{
    static $loaded = false;
    static $available = false;
    if ($loaded) return $available;
    $loaded = true;

    $module = $_SESSION[PCP_APONTAMENTO_SESSION] ?? [];
    if (!is_array($module) || empty($module['authenticated']) || empty($module['user_id'])) return false;
    if (pcp_apontamento_terminal_expired($module)) {
        pcp_apontamento_logout();
        return false;
    }
    pcp_apontamento_touch_terminal();
    if (empty($module['company_confirmed'])) return false;

    global $g, $confUsuario;
    require PCP_APONTAMENTO_ROOT . '/bibliotecas/confUsuario.php';
    $g['urlRaiz'] = $confUsuario['urlRaiz'] ?? '';
    $g['pathRaiz'] = (string) ($confUsuario['pathRaiz'] ?? PCP_APONTAMENTO_ROOT);
    if (!is_dir($g['pathRaiz'])) $g['pathRaiz'] = PCP_APONTAMENTO_ROOT;
    $g['tabelasCache'] = [];
    chdir($g['pathRaiz']);

    $g['pathDados'] = $g['pathDados'] ?? 'dados_usuarios/' . ($confUsuario['dbDatabase'] ?? '') . '/';
    $userId = (int) $module['user_id'];
    $database = (string) ($confUsuario['dbDatabase'] ?? '');
    if ($userId <= 0 || $database === '' || (string) ($module['database'] ?? '') !== $database) return false;
    $g['conexaoBanco'] = pcp_apontamento_connect();
    if (!$g['conexaoBanco']) return false;

    $companies = pcp_apontamento_fetch_companies($g['conexaoBanco'], $userId);
    if (!$companies) return false;
    $_SESSION[PCP_APONTAMENTO_SESSION]['companies'] = $companies;
    $companyId = (int) ($_SESSION[PCP_APONTAMENTO_SESSION]['company_id'] ?? 0);
    if (!isset($companies[$companyId])) return false;
    $_SESSION[PCP_APONTAMENTO_SESSION]['company_id'] = $companyId;
    $_SESSION[PCP_APONTAMENTO_SESSION]['profile_id'] = (int) $companies[$companyId]['profile_id'];
    $g['empresaAtual'] = $companyId;
    $g['perfilUsuario'] = (int) $companies[$companyId]['profile_id'];

    $user = mysqli_query($g['conexaoBanco'], "SELECT C007_Id, C007_Nome, C007_Primeiro_Nome, C007_Login FROM C007 WHERE C007_Id='{$userId}' AND C007_Ativo='S' LIMIT 1");
    $g['C007'] = $g['c007'] = $user ? (mysqli_fetch_assoc($user) ?: []) : [];
    $available = true;
    return true;
}

function pcp_apontamento_context(): ?array
{
    if (!pcp_apontamento_boot()) return null;
    global $g;
    $companyId = (int) $_SESSION[PCP_APONTAMENTO_SESSION]['company_id'];
    $company = $_SESSION[PCP_APONTAMENTO_SESSION]['companies'][$companyId] ?? null;
    if (!is_array($company)) return null;
    $profileId = (int) $_SESSION[PCP_APONTAMENTO_SESSION]['profile_id'];
    // A elegibilidade e o acesso operacional usam o ID C029 estável 308;
    // o texto da permissão é somente referência de interface/documentação.
    $operationPermission = (int) pcp_apontamento_config('permission_operation');
    $managePermission = (int) pcp_apontamento_config('permission_manage');
    $editPermission = (int) pcp_apontamento_config('permission_edit');
    return [
        'user_id' => (int) $_SESSION[PCP_APONTAMENTO_SESSION]['user_id'],
        'user_name' => (string) ($_SESSION[PCP_APONTAMENTO_SESSION]['user_name'] ?? $g['C007']['C007_Primeiro_Nome'] ?? $g['C007']['C007_Nome'] ?? 'Usuário'),
        'company_id' => $companyId,
        'company_name' => (string) $company['name'],
        'can_operate' => pcp_apontamento_profile_permission($g['conexaoBanco'], $profileId, $operationPermission),
        'can_manage' => pcp_apontamento_profile_permission($g['conexaoBanco'], $profileId, $managePermission),
        'can_edit' => pcp_apontamento_profile_permission($g['conexaoBanco'], $profileId, $editPermission),
    ];
}

function pcp_apontamento_require_access(): array
{
    $context = pcp_apontamento_context();
    if ($context === null) {
        pcp_apontamento_abort('Acesso indisponível. Entre no Hardness com um usuário autorizado ao PCP e selecione uma empresa permitida.', 403);
    }
    if (!$context['can_operate']) pcp_apontamento_abort('Seu perfil não possui acesso operacional ao Apontamento PCP.', 403);
    return $context;
}

function pcp_apontamento_require_manage(): array
{
    $context = pcp_apontamento_require_access();
    if (!$context['can_manage']) pcp_apontamento_abort('Seu perfil não possui permissão para gerenciar o Apontamento PCP.', 403);
    return $context;
}

function pcp_apontamento_require_edit(): array
{
    $context = pcp_apontamento_require_access();
    if (!$context['can_edit']) pcp_apontamento_abort('Seu perfil não possui permissão para editar ou excluir apontamentos PCP.', 403);
    return $context;
}

/** Use este contexto e o company_id em toda consulta operacional futura. */
function pcp_apontamento_require_company(): int
{
    return (int) pcp_apontamento_require_access()['company_id'];
}

function pcp_apontamento_header(string $title, array $context): void
{
    $style = PCP_APONTAMENTO_MODULE . '/public/assets/css/app.css';
    $flash = pcp_apontamento_flash();
    $page = array_key_exists('pagina', $_GET) ? (string) $_GET['pagina'] : ((isset($_REQUEST['matricula']) || $_SERVER['REQUEST_METHOD'] === 'POST') ? 'individual' : 'visao-geral');
    $icons = [
        'visao-geral' => '&#xf015;',
        'individual' => '&#xf0c0;',
        'gerenciar' => '&#xf03a;',
        'configuracoes' => '&#xf013;',
    ];
    $subtitles = ['visao-geral'=>'Acompanhamento do terminal', 'individual'=>'Registrar uma matrícula', 'gerenciar'=>'Consulta operacional', 'configuracoes'=>'Cadastros e permissões'];
    $link = static function (string $label, string $target, bool $visible = true) use ($page, $icons, $subtitles): void { if (!$visible) return; $active = $page === $target || ($target === 'individual' && $page === ''); echo '<a class="nav-link' . ($active ? ' is-active' : '') . '" href="' . pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=' . rawurlencode($target))) . '"><span class="nav-icon">' . $icons[$target] . '</span><span class="nav-copy"><b>' . pcp_apontamento_h($label) . '</b><small>' . pcp_apontamento_h($subtitles[$target]) . '</small></span><span class="nav-chevron">›</span></a>'; };
    ?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex"><title><?php echo pcp_apontamento_h($title); ?> | Apontamento PCP</title><link rel="stylesheet" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('assets/css/app.css') . '?v=' . (string) @filemtime($style)); ?>"></head><body><div class="app-layout"><aside class="sidebar"><div class="brand"><img src="/hardness3/static/img/logo_hardness.png" alt="Hardness"></div><div class="sidebar-heading"><span>Apontamento</span><span><?php echo $context['can_manage'] ? '4 opções' : '3 opções'; ?></span></div><nav><?php $link('Visão geral', 'visao-geral'); $link('Apontamento', 'individual'); $link('Gerenciar apontamentos', 'gerenciar'); $link('Configurações', 'configuracoes', $context['can_manage']); ?></nav><div class="sidebar-context"><span class="context-dot"></span><div><strong>Empresa ativa</strong><span><?php echo pcp_apontamento_h($context['company_name']); ?></span></div></div></aside><div class="shell"><header class="topbar"><div><p>HARDNESS · PCP</p><h1><?php echo pcp_apontamento_h($title); ?></h1></div><div class="header-company" title="Empresa usada nos apontamentos desta sessão"><span class="header-company__icon" aria-hidden="true">&#xf1ad;</span><span><small><i></i> Empresa ativa</small><strong><?php echo pcp_apontamento_h($context['company_name']); ?></strong></span></div><div class="session"><span>Terminal: <?php echo pcp_apontamento_h($context['user_name']); ?></span><form method="post" action="<?php echo pcp_apontamento_h(pcp_apontamento_url('logout.php')); ?>"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><button class="terminal-logout" type="submit">Sair do terminal</button></form></div></header><main><?php if ($flash): ?><div class="alert alert--<?php echo pcp_apontamento_h($flash['type']); ?>"<?php echo !empty($flash['inline_op']) ? ' data-pcp-inline-op' : ''; ?>><?php echo pcp_apontamento_h($flash['message']); ?></div><?php endif; ?><?php if (($flash['dialog'] ?? '') === 'inicio_concluido'): ?><dialog class="success-dialog" data-pcp-success-dialog><div class="success-dialog__icon">✓</div><p class="eyebrow">Apontamento registrado</p><h2>Operação concluída com sucesso</h2><p><?php echo pcp_apontamento_h($flash['message']); ?></p><button type="button" data-pcp-close-success>Iniciar novo apontamento</button></dialog><?php elseif (($flash['dialog'] ?? '') === 'finalizacao_concluida'): ?><dialog class="success-dialog" data-pcp-finish-dialog><div class="success-dialog__icon">✓</div><p class="eyebrow">Apontamento finalizado</p><h2>Deseja iniciar novo apontamento?</h2><p>O apontamento deste colaborador foi finalizado com sucesso.</p><div class="success-dialog__actions"><a class="button-link" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=individual')); ?>">Sim</a><a class="button-link secondary-link" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=individual')); ?>">Não</a></div></dialog><?php endif; ?><?php
}

function pcp_apontamento_footer(): void
{
    $script = PCP_APONTAMENTO_MODULE . '/public/assets/js/app.js';
    echo '</main></div></div><script src="' . pcp_apontamento_h(pcp_apontamento_url('assets/js/app.js') . '?v=' . (string) @filemtime($script)) . '"></script></body></html>';
}
