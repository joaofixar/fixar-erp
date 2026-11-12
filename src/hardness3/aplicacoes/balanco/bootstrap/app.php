<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Mantém o mesmo nível de relato do ponto de entrada legado ao executar CAD002.
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

const BALANCO_ROOT = __DIR__ . '/../../..';
const BALANCO_MODULE = __DIR__ . '/..';
const BALANCO_SESSION = 'balanco';

function balanco_config(string $key, $default = null)
{
    static $config;
    if ($config === null) {
        $config = require BALANCO_MODULE . '/config/module.php';
    }
    return $config[$key] ?? $default;
}

function balanco_url(string $path = ''): string
{
    $base = rtrim((string) balanco_config('base_path'), '/');
    $path = preg_replace('#^public/#', '', ltrim($path, '/'));
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function balanco_qr_handoff_url(string $code): string
{
    $confUsuario = [];
    require BALANCO_ROOT . '/bibliotecas/confUsuario.php';
    // A URL pública pertence à configuração do cliente. O módulo não define
    // domínio, túnel ou ambiente alternativo para o QR Code.
    $base = (string) ($confUsuario['urlRaiz'] ?? '');
    $base = rtrim($base, '/');
    return $base . '/balanco/abrir-qr.php?codigo=' . rawurlencode($code);
}

function balanco_qr_handoff_directory(string $database): string
{
    $database = preg_replace('/[^A-Za-z0-9_-]/', '', $database);
    if ($database === '') throw new RuntimeException('Banco de dados inválido para abertura do Balanço.');
    $directory = BALANCO_ROOT . '/dados_usuarios/' . $database . '/balanco_qr_handoffs';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível preparar a abertura do Balanço no celular.');
    }
    return $directory;
}

function balanco_qr_issue_handoff(array $context): array
{
    $code = bin2hex(random_bytes(32));
    $database = (string) ($context['database'] ?? $_SESSION['Banco_Dados'] ?? '');
    $expiresAt = time() + 120;
    $payload = [
        'user_id' => (int) $context['user_id'],
        'database' => $database,
        'expires_at' => $expiresAt,
    ];
    $file = balanco_qr_handoff_directory($database) . '/' . hash('sha256', $code) . '.json';
    if (file_put_contents($file, json_encode($payload), LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível gerar o código de abertura do Balanço.');
    }
    @chmod($file, 0600);
    return ['code' => $code, 'expires_at' => $expiresAt];
}

function balanco_qr_consume_handoff(string $code): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $code)) return null;
    $confUsuario = [];
    require BALANCO_ROOT . '/bibliotecas/confUsuario.php';
    $database = (string) ($confUsuario['dbDatabase'] ?? '');
    $file = balanco_qr_handoff_directory($database) . '/' . hash('sha256', $code) . '.json';
    $handle = @fopen($file, 'c+');
    if (!$handle) return null;

    $payload = null;
    if (flock($handle, LOCK_EX)) {
        // Outro processo pode já ter aberto este mesmo arquivo e consumido o
        // código antes de este lock ser obtido. A existência ainda precisa ser
        // conferida após o lock, pois o descritor continua válido após unlink.
        if (is_file($file)) {
            rewind($handle);
            $data = stream_get_contents($handle);
            $candidate = json_decode((string) $data, true);
            if (is_array($candidate) && (int) ($candidate['expires_at'] ?? 0) >= time() && (string) ($candidate['database'] ?? '') === $database) {
                $payload = $candidate;
            }
        }
        // Remover sob lock torna o código utilizável apenas uma vez, inclusive
        // quando dois celulares tentam abri-lo simultaneamente.
        @unlink($file);
        flock($handle, LOCK_UN);
    }
    fclose($handle);
    return $payload;
}

function balanco_qr_svg_data_uri(string $url): string
{
    require_once BALANCO_ROOT . '/hardness/phpqrcode/phpqrcode.php';
    $matrix = \QRcode::text($url, false, QR_ECLEVEL_M, 1, 4);
    $moduleCount = count($matrix);
    $margin = 4;
    $dimension = $moduleCount + ($margin * 2);
    $path = '';
    foreach ($matrix as $y => $line) {
        for ($x = 0; $x < $moduleCount; $x++) {
            if (($line[$x] ?? '0') === '1') {
                $path .= 'M' . ($x + $margin) . ' ' . ($y + $margin) . 'h1v1h-1z';
            }
        }
    }
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $dimension . ' ' . $dimension . '" shape-rendering="crispEdges"><rect width="100%" height="100%" fill="#fff"/><path d="' . $path . '" fill="#000"/></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function balanco_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function balanco_format_quantity($value, int $decimals = 0): string
{
    if ($value === null || $value === '') return '0';
    if ($decimals > 0) return number_format((float) $value, $decimals, ',', '.');
    $formatted = number_format((float) $value, 5, ',', '.');
    return rtrim(rtrim($formatted, '0'), ',');
}

function balanco_format_balance_date($value): string
{
    $value = (string) $value;
    if ($value === '' || $value === '0000-00-00') return 'Nunca balanceado';
    $time = strtotime($value);
    return $time ? date('d/m/Y', $time) : 'Nunca balanceado';
}

function balanco_truncate($value, int $limit = 60): string
{
    $value = trim((string) $value);
    return mb_strlen($value, 'UTF-8') > $limit ? mb_substr($value, 0, $limit - 1, 'UTF-8') . '…' : $value;
}

function balanco_reset_module_context(): void
{
    $flash = $_SESSION[BALANCO_SESSION]['flash'] ?? null;
    $_SESSION[BALANCO_SESSION] = [
        'user_id' => null,
        'database' => null,
        'companies' => [],
        'active_company_id' => null,
        'active_profile_id' => null,
        'company_confirmed' => false,
        'auth_source' => null,
    ];
    if (is_array($flash)) $_SESSION[BALANCO_SESSION]['flash'] = $flash;
}

function balanco_logout(): void
{
    // Mantém um marcador local para que a sessão Hardness ainda válida não
    // recrie o contexto do módulo na primeira requisição após a saída.
    $_SESSION[BALANCO_SESSION] = [
        'closed' => true,
        'closed_user_id' => (int) ($_SESSION['Usuario_Atual'] ?? 0),
        'closed_database' => (string) ($_SESSION['Banco_Dados'] ?? ''),
    ];
}

function balanco_fetch_user_companies(mysqli $conn, int $userId): array
{
    if ($userId <= 0) return [];
    $permissionId = (int) balanco_config('permission_process_balance');
    $sql = "SELECT C019.C019_C004_Id AS company_id, C019.C019_C021_Id AS profile_id, C004.C004_Nome_Empresa, C004.C004_Nome_Abreviado
            FROM C019
            INNER JOIN C004 ON C004.C004_Id=C019.C019_C004_Id
            INNER JOIN C030 ON C030.C030_C021_Id=C019.C019_C021_Id
                AND C030.C030_C029_Id='{$permissionId}'
                AND C030.C030_Acesso_Liberado='S'
            WHERE C019.C019_C007_Id='{$userId}'
              AND C019.C019_C021_Id > 0
              AND C004.C004_Flag_Ativo='S'
            ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id";
    $result = mysqli_query($conn, $sql);
    if (!$result) return [];

    $companies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $companyId = (int) $row['company_id'];
        $companies[$companyId] = [
            'id' => $companyId,
            'profile_id' => (int) $row['profile_id'],
            'name' => (string) ($row['C004_Nome_Empresa'] ?? ''),
            'short_name' => (string) ($row['C004_Nome_Abreviado'] ?? $row['C004_Nome_Empresa'] ?? ''),
        ];
    }
    return $companies;
}

function balanco_companies(): array
{
    return $_SESSION[BALANCO_SESSION]['companies'] ?? [];
}

function balanco_active_company(): ?array
{
    $companyId = (int) ($_SESSION[BALANCO_SESSION]['active_company_id'] ?? 0);
    return balanco_companies()[$companyId] ?? null;
}

function balanco_clear_operational_context(): void
{
    if (!isset($_SESSION[BALANCO_SESSION]) || !is_array($_SESSION[BALANCO_SESSION])) {
        balanco_reset_module_context();
    }
    $_SESSION[BALANCO_SESSION]['active_company_id'] = null;
    $_SESSION[BALANCO_SESSION]['active_profile_id'] = null;
    $_SESSION[BALANCO_SESSION]['company_confirmed'] = false;
}

function balanco_select_company(int $companyId): bool
{
    if (!balanco_boot()) return false;
    // A confirmação precisa consultar C019 novamente, mesmo quando a mesma
    // requisição já executou balanco_boot() para desenhar a tela.
    global $g;
    $companies = balanco_fetch_user_companies($g['conexaoBanco'], (int) ($_SESSION['Usuario_Atual'] ?? 0));
    $_SESSION[BALANCO_SESSION]['companies'] = $companies;
    if (!isset($companies[$companyId])) return false;

    $_SESSION[BALANCO_SESSION]['active_company_id'] = $companyId;
    $_SESSION[BALANCO_SESSION]['active_profile_id'] = (int) $companies[$companyId]['profile_id'];
    $_SESSION[BALANCO_SESSION]['company_confirmed'] = false;
    return true;
}

function balanco_confirm_company(int $companyId): bool
{
    if (!balanco_select_company($companyId)) return false;
    // balanco_select_company() valida a empresa no C019 recarregado nesta
    // mesma requisição; só depois disso o contexto operacional é liberado.
    $_SESSION[BALANCO_SESSION]['company_confirmed'] = true;
    return true;
}

function balanco_company_is_confirmed(): bool
{
    return !empty($_SESSION[BALANCO_SESSION]['company_confirmed'])
        && balanco_active_company() !== null;
}

function balanco_boot(): bool
{
    static $loaded = false;
    static $available = false;
    if ($loaded) {
        return $available;
    }
    $loaded = true;

    if (empty($_SESSION['Usuario_Atual'])) {
        return false;
    }

    $sessionUserId = (int) $_SESSION['Usuario_Atual'];
    $closedContext = $_SESSION[BALANCO_SESSION] ?? null;
    if (
        is_array($closedContext)
        && !empty($closedContext['closed'])
        && (int) ($closedContext['closed_user_id'] ?? 0) === $sessionUserId
        && (string) ($closedContext['closed_database'] ?? '') === (string) ($_SESSION['Banco_Dados'] ?? '')
    ) {
        return false;
    }

    global $g, $confUsuario;
    // Este arquivo define $confUsuario no escopo atual. Não pode ser
    // require_once: a validação do QR já o carregou em outro escopo antes de
    // esta nova sessão precisar das credenciais de banco.
    require BALANCO_ROOT . '/bibliotecas/confUsuario.php';

    $g['urlRaiz'] = $confUsuario['urlRaiz'] ?? '';
    // A raiz do módulo é a mesma raiz do ERP quando pathRaiz não estiver
    // disponível na configuração do cliente.
    $g['pathRaiz'] = (string) ($confUsuario['pathRaiz'] ?? '');
    if ($g['pathRaiz'] === '' || !is_dir($g['pathRaiz'])) {
        $g['pathRaiz'] = BALANCO_ROOT;
    }
    $g['tabelasCache'] = [];
    chdir($g['pathRaiz']);

    require_once BALANCO_ROOT . '/bibliotecas/sessao.php';
    require_once BALANCO_ROOT . '/bibliotecas/conexaoBanco.php';
    require_once BALANCO_ROOT . '/sistema/interfaceUsuario.php';
    require_once BALANCO_ROOT . '/sistema/automacaoBanco.php';
    require_once BALANCO_ROOT . '/sistema/automacaoFormulario.php';
    require_once BALANCO_ROOT . '/sistema/confSistema.php';
    require_once BALANCO_ROOT . '/sistema/apps.php';
    require_once BALANCO_ROOT . '/bibliotecas/restricoes.php';
    require_once BALANCO_ROOT . '/bibliotecas/funcoesGerais.php';

    // EST003/CAD002 usam o wrapper legado mysql_query(), que depende deste
    // contexto mínimo do framework mesmo fora do index-framework.php.
    $g['pathDados'] = $g['pathDados'] ?? 'dados_usuarios/' . $confUsuario['dbDatabase'] . '/';
    $g['modoDesenvolvedor'] = $g['modoDesenvolvedor'] ?? false;
    $g['modoDesenvolvedorApp'] = $g['modoDesenvolvedorApp'] ?? false;
    $g['time'] = $g['time'] ?? ['start' => microtime(true), 'sql' => 0.0];
    $g['tabelasAuditoria'] = $g['tabelasAuditoria'] ?? [];
    $g['conf'] = $g['conf'] ?? new \hardness\confSistema();

    $userId = (int) ($_SESSION['Usuario_Atual'] ?? $g['usuarioAtual'] ?? 0);
    if ($userId <= 0 || empty($g['conexaoBanco'])) {
        return false;
    }

    $conn = $g['conexaoBanco'];
    if (!isset($_SESSION[BALANCO_SESSION]) || !is_array($_SESSION[BALANCO_SESSION])) {
        balanco_reset_module_context();
    }

    $database = (string) ($confUsuario['dbDatabase'] ?? '');
    if (
        (int) ($_SESSION[BALANCO_SESSION]['user_id'] ?? 0) !== $userId
        || (string) ($_SESSION[BALANCO_SESSION]['database'] ?? '') !== $database
    ) {
        balanco_reset_module_context();
    }

    $companies = balanco_fetch_user_companies($conn, $userId);
    if (!$companies) return false;
    $_SESSION[BALANCO_SESSION]['user_id'] = $userId;
    $_SESSION[BALANCO_SESSION]['database'] = $database;
    $_SESSION[BALANCO_SESSION]['companies'] = $companies;
    $_SESSION[BALANCO_SESSION]['auth_source'] = $_SESSION[BALANCO_SESSION]['auth_source'] ?? 'hardness_session';

    $activeCompanyId = (int) ($_SESSION[BALANCO_SESSION]['active_company_id'] ?? 0);
    if (!isset($companies[$activeCompanyId])) {
        balanco_clear_operational_context();
        $activeCompanyId = 0;
    }

    $user = mysqli_query($conn, "SELECT * FROM C007 WHERE C007_Id='{$userId}'");
    $g['C007'] = $g['c007'] = $user ? (mysqli_fetch_assoc($user) ?: []) : [];

    // Antes da confirmação não existe empresa operacional. Isso impede que
    // qualquer dado do ERP ou do QR Code seja assumido pelo Balanço.
    if (!balanco_company_is_confirmed()) {
        $available = true;
        return true;
    }

    $profileId = (int) $companies[$activeCompanyId]['profile_id'];
    if ($profileId <= 0) return false;
    $_SESSION[BALANCO_SESSION]['active_profile_id'] = $profileId;

    $g['empresaAtual'] = $activeCompanyId;
    $g['perfilUsuario'] = $profileId;
    $g['c029Ids'] = [];
    $permissions = mysqli_query($conn, "SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$profileId}' AND C030_Acesso_Liberado='S'");
    if ($permissions) {
        while ($row = mysqli_fetch_row($permissions)) {
            $g['c029Ids'][(int) $row[0]] = true;
        }
    }

    $company = mysqli_query($conn, "SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$activeCompanyId}'");
    $g['C004'] = $g['c004'] = $company ? (mysqli_fetch_assoc($company) ?: []) : [];
    $c020 = mysqli_query($conn, "SELECT * FROM C020 WHERE C020_C004_Id='{$activeCompanyId}'");
    $g['C020'] = $g['c020'] = $c020 ? (mysqli_fetch_assoc($c020) ?: []) : [];
    $c021 = mysqli_query($conn, "SELECT * FROM C021 WHERE C021_Id='{$profileId}'");
    $g['C021'] = $g['c021'] = $c021 ? (mysqli_fetch_assoc($c021) ?: []) : [];
    $g['C031'] = $g['c031'] = [];
    $c031 = mysqli_query($conn, "SELECT C031_Campo, C031_Valor FROM C031 WHERE C031_C004_Id='{$activeCompanyId}'");
    if ($c031) while ($row = mysqli_fetch_assoc($c031)) $g['C031'][$row['C031_Campo']] = $g['c031'][$row['C031_Campo']] = $row['C031_Valor'];
    $available = true;
    return true;
}

function balanco_context(): ?array
{
    if (!balanco_boot() || !balanco_company_is_confirmed()) {
        return null;
    }
    global $g;
    return [
        'user_id' => (int) ($_SESSION['Usuario_Atual'] ?? $g['usuarioAtual'] ?? 0),
        'user_name' => (string) ($g['C007']['C007_Primeiro_Nome'] ?? $g['C007']['C007_Nome'] ?? ''),
        'company_id' => (int) $g['empresaAtual'],
        'company_name' => (string) ($g['C004']['C004_Nome_Abreviado'] ?? $g['C004']['C004_Nome_Empresa'] ?? ''),
    ];
}

function balanco_selection_context(): ?array
{
    if (!balanco_boot()) return null;
    global $g;
    return [
        'user_id' => (int) ($_SESSION['Usuario_Atual'] ?? $g['usuarioAtual'] ?? 0),
        'user_name' => (string) ($g['C007']['C007_Primeiro_Nome'] ?? $g['C007']['C007_Nome'] ?? ''),
    ];
}

function balanco_can_process(): bool
{
    if (!balanco_boot()) {
        return false;
    }
    global $g;
    return isset($g['c029Ids'][(int) balanco_config('permission_process_balance')]);
}

function balanco_can_manage_locations(string $action): bool
{
    if (!balanco_boot()) {
        return false;
    }
    global $g;
    $permissions = (array) balanco_config('location_permissions', []);
    $permissionId = (int) ($permissions[$action] ?? 0);
    return $permissionId > 0
        && (($g['C031']['habilitarMultiplasLocacoes'] ?? 'N') === 'S')
        && isset($g['c029Ids'][$permissionId]);
}

function balanco_require_location_permission(string $action): void
{
    if (!balanco_can_manage_locations($action)) {
        throw new RuntimeException('Seu perfil não possui permissão para esta ação de locação.');
    }
}

function balanco_render_access_denied(array $context): void
{
    http_response_code(403);
    balanco_header('Acesso não autorizado', $context);
    echo '<section class="panel empty"><h2>Acesso não autorizado</h2><p>Seu perfil não possui a permissão para processar balanço.</p></section>';
    balanco_footer();
    exit;
}

function balanco_label_models(): array
{
    return (array) balanco_config('label_models', []);
}

function balanco_label_context_for_t066(int $t066Id, array $context): ?array
{
    if ($t066Id <= 0) return null;
    global $g;
    $company = (int) $context['company_id'];
    $user = (int) $context['user_id'];
    $sql = "SELECT T066.T066_Id, T066.T066_D009_Id, T066.T066_D004_Id, D001.D001_Id
            FROM T066
            INNER JOIN D009 ON D009.D009_Id=T066.T066_D009_Id
            INNER JOIN D049 ON D049.D049_Id=D009.D009_D049_Id
            INNER JOIN D001 ON D001.D001_Id=D049.D049_D001_Id
            INNER JOIN D004 ON D004.D004_Id=T066.T066_D004_Id
            WHERE T066.T066_Id='{$t066Id}'
              AND D009.D009_C004_Id='{$company}'
              AND D004.D004_C004_Id='{$company}'
              AND T066.T066_Flag_Balanco='S'
              AND T066.T066_C007_Id_Marcou_Balanco='{$user}'
            LIMIT 1";
    $result = mysqli_query($g['conexaoBanco'], $sql);
    return $result ? (mysqli_fetch_assoc($result) ?: null) : null;
}

function balanco_generate_label_pdf(array $model, int $contextId, array $context): array
{
    global $g;
    $modelId = (string) ($model['model'] ?? '');
    $idType = (string) ($model['id_type'] ?? '');
    if (!preg_match('/^[a-f0-9]{32}$/', $modelId) || !preg_match('/^[A-Za-z0-9_]+$/', $idType)) {
        return ['ok' => false, 'message' => 'Modelo de etiqueta inválido.'];
    }
    if ($contextId <= 0) return ['ok' => false, 'message' => 'Não foi possível localizar os dados da etiqueta no T066 atual.'];

    $callback = 'balancoLabelCallback';
    $database = (string) ($_SESSION['Banco_Dados'] ?? $_SESSION['bancoDados'] ?? $_SESSION['cBd_Configuracao'] ?? '');
    $sourceLink = '/sistema/personalizacao/tela/' . $modelId . '/?ajax=true&' . $idType . '=' . rawurlencode((string) $contextId);
    $params = [
        'ajax' => 'true',
        'acaoId' => $modelId,
        'link' => $sourceLink,
        'callback' => $callback,
        'sessionCookie' => session_id(),
        'p' => (string) $context['company_id'],
        'u' => (string) $context['user_id'],
        'db' => $database,
        'noEmp' => 'true',
    ];
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $path = '/hardness3/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/?' . http_build_query($params);
    $cookies = [];
    foreach ($_COOKIE as $name => $value) $cookies[] = rawurlencode((string) $name) . '=' . rawurlencode((string) $value);

    // O gerador abre uma nova requisição que usa a mesma sessão Hardness.
    // Liberar o lock evita que ela fique aguardando até o timeout.
    session_write_close();
    $stream = stream_context_create(['http' => [
        'header' => implode("\r\n", array_filter([$cookies ? 'Cookie: ' . implode('; ', $cookies) : '', 'ngrok-skip-browser-warning: true'])),
        'timeout' => 150,
    ]]);
    $responseBody = false;
    foreach (array_unique([$scheme . '://' . $host . $path, 'http://127.0.0.1' . $path, 'http://localhost' . $path]) as $url) {
        $responseBody = @file_get_contents($url, false, $stream);
        if (is_string($responseBody) && trim($responseBody) !== '') break;
    }
    if (!is_string($responseBody) || !preg_match('/^' . preg_quote($callback, '/') . '\\((.*)\\);?\\s*$/s', trim($responseBody), $matches)) {
        return ['ok' => false, 'message' => 'Não foi possível gerar o PDF da etiqueta.'];
    }
    $response = json_decode($matches[1], true);
    $pdfUrl = is_array($response) ? (string) ($response['data'] ?? '') : '';
    if ($pdfUrl === '') return ['ok' => false, 'message' => 'O gerador não retornou o PDF da etiqueta.'];
    if (strpos($pdfUrl, '/') === 0) $pdfUrl = $scheme . '://' . $host . $pdfUrl;
    return ['ok' => true, 'url' => $pdfUrl];
}

function balanco_require_access(): array
{
    $context = balanco_context();
    if ($context === null) {
        if (balanco_selection_context() !== null) {
            header('Location: ' . balanco_url('select-company.php'));
        } else {
            header('Location: ' . balanco_url('login.php'));
        }
        exit;
    }
    return $context;
}

function balanco_flash(?string $type = null, ?string $message = null): ?array
{
    if ($type !== null && $message !== null) {
        $_SESSION[BALANCO_SESSION]['flash'] = ['type' => $type, 'message' => $message];
        return null;
    }
    $flash = $_SESSION[BALANCO_SESSION]['flash'] ?? null;
    unset($_SESSION[BALANCO_SESSION]['flash']);
    return is_array($flash) ? $flash : null;
}

function balanco_feedback_dialog(string $type, string $message, string $focusSelector = '', bool $clearFocusField = false): void
{
    $title = $type === 'success' ? 'Concluído' : 'Não foi possível continuar';
    $class = $type === 'success' ? 'feedback-dialog--success' : 'feedback-dialog--error';
    ?>
    <dialog class="confirm-dialog feedback-dialog <?php echo balanco_h($class); ?>" id="balanco-feedback-dialog" aria-labelledby="balanco-feedback-title"<?php echo $focusSelector !== '' ? ' data-focus-selector="' . balanco_h($focusSelector) . '"' : ''; ?><?php echo $clearFocusField ? ' data-clear-focus-field="true"' : ''; ?>>
        <h2 id="balanco-feedback-title"><?php echo balanco_h($title); ?></h2>
        <p><?php echo balanco_h($message); ?></p>
        <div class="button-row"><button class="button button--secondary" type="button" id="close-balanco-feedback">Entendi</button></div>
    </dialog>
    <script>(function(){var dialog=document.getElementById('balanco-feedback-dialog'),button=document.getElementById('close-balanco-feedback');if(!dialog){return;}dialog.addEventListener('close',function(){var selector=dialog.getAttribute('data-focus-selector'),field=selector?document.querySelector(selector):null;if(!field){return;}if(dialog.hasAttribute('data-clear-focus-field')){field.value='';}window.setTimeout(function(){field.focus();},0);});if(typeof dialog.showModal==='function'){dialog.showModal();}if(button){button.onclick=function(){dialog.close();};}})();</script>
    <?php
}

function balanco_csrf_token(): string
{
    if (empty($_SESSION[BALANCO_SESSION]['csrf'])) {
        $_SESSION[BALANCO_SESSION]['csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION[BALANCO_SESSION]['csrf'];
}

function balanco_require_csrf(): void
{
    $token = (string) ($_POST['csrf'] ?? '');
    if ($token === '' || !hash_equals(balanco_csrf_token(), $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

function balanco_t066_for_update(mysqli $conn, int $t066Id, int $companyId): ?array
{
    if ($t066Id <= 0) return null;
    $sql = "SELECT T066.T066_Id, T066.T066_D009_Id, T066.T066_Flag_Balanco, T066.T066_C007_Id_Marcou_Balanco, C007.C007_Primeiro_Nome, C007.C007_Login FROM T066 INNER JOIN D009 ON D009.D009_Id=T066.T066_D009_Id INNER JOIN D004 ON D004.D004_Id=T066.T066_D004_Id LEFT JOIN C007 ON C007.C007_Id=T066.T066_C007_Id_Marcou_Balanco WHERE T066.T066_Id='{$t066Id}' AND D009.D009_C004_Id='{$companyId}' AND D004.D004_C004_Id='{$companyId}' LIMIT 1 FOR UPDATE";
    $result = mysqli_query($conn, $sql);
    if (!$result) throw new RuntimeException('Não foi possível validar a locação para balanço.');
    return mysqli_fetch_assoc($result) ?: null;
}

function balanco_acquire_t066(int $t066Id, array $context): array
{
    global $g;
    $conn = $g['conexaoBanco'];
    $company = (int) $context['company_id'];
    $user = (int) $context['user_id'];
    mysqli_begin_transaction($conn);
    try {
        $item = balanco_t066_for_update($conn, $t066Id, $company);
        if (!$item) { mysqli_rollback($conn); return ['status' => 'not_found']; }
        $flagged = $item['T066_Flag_Balanco'] === 'S';
        $owner = (int) $item['T066_C007_Id_Marcou_Balanco'];
        if ($flagged && $owner !== $user) {
            mysqli_rollback($conn);
            return ['status' => 'blocked', 'owner_name' => $item['C007_Primeiro_Nome'] ?: $item['C007_Login'] ?: 'outro usuário'];
        }
        if (!$flagged) {
            $sql = "UPDATE T066 SET T066_Flag_Balanco='S', T066_C007_Id_Marcou_Balanco='{$user}' WHERE T066_Id='{$t066Id}'";
            if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível reservar o item para balanço.');
        }
        mysqli_commit($conn);
        return ['status' => $flagged ? 'resumed' : 'acquired', 'd009_id' => (int) $item['T066_D009_Id']];
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

function balanco_abandon_t066(int $t066Id, array $context): array
{
    global $g;
    $conn = $g['conexaoBanco'];
    $company = (int) $context['company_id'];
    $user = (int) $context['user_id'];
    mysqli_begin_transaction($conn);
    try {
        $item = balanco_t066_for_update($conn, $t066Id, $company);
        if (!$item) { mysqli_rollback($conn); return ['status' => 'not_found']; }
        if ($item['T066_Flag_Balanco'] !== 'S' || (int) $item['T066_C007_Id_Marcou_Balanco'] !== $user) {
            mysqli_rollback($conn);
            return ['status' => 'not_owner'];
        }
        $sql = "UPDATE T066 SET T066_Flag_Balanco='N', T066_C007_Id_Marcou_Balanco='' WHERE T066_Id='{$t066Id}'";
        if (!mysqli_query($conn, $sql)) throw new RuntimeException('Não foi possível liberar a marcação de balanço.');
        mysqli_commit($conn);
        return ['status' => 'released', 'd009_id' => (int) $item['T066_D009_Id']];
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        throw $e;
    }
}

function balanco_parse_counted_quantity($value): ?string
{
    $value = trim((string) $value);
    if (!preg_match('/^\d+(?:[,.]\d{1,2})?$/', $value)) return null;
    return number_format((float) str_replace(',', '.', $value), 2, '.', '');
}

function balanco_finalize_t066(int $t066Id, string $quantity, array $context): array
{
    if ($t066Id <= 0) return ['status' => 'not_found'];

    $quantity = balanco_parse_counted_quantity($quantity);
    if ($quantity === null) {
        return ['status' => 'invalid_quantity', 'message' => 'Informe uma quantidade válida com até duas casas decimais.'];
    }

    require_once BALANCO_ROOT . '/bibliotecas/classes/EST003.php';
    $est003 = new \hardness\EST003();
    $result = $est003->est003EfetivarBalancoT066($t066Id, $quantity);
    if ($result !== true) {
        return ['status' => 'error', 'message' => (string) $result];
    }
    return ['status' => 'completed'];
}

function balanco_authenticate(string $login, string $password)
{
    $login = trim($login);
    if ($login === '' || $password === '') {
        return 'Informe login e senha.';
    }
    $confUsuario = [];
    $sys = [];
    require BALANCO_ROOT . '/bibliotecas/confUsuario.php';
    require BALANCO_ROOT . '/configuracoes/sysConfig.php';
    require_once BALANCO_ROOT . '/bibliotecas/segurancaSenha.php';
    $conn = @mysqli_connect((string) ($confUsuario['dbHost'] ?? ''), (string) ($confUsuario['dbUser'] ?? ''), (string) ($confUsuario['dbPass'] ?? ''), (string) ($confUsuario['dbDatabase'] ?? ''));
    if (!$conn) {
        return 'Não foi possível conectar ao sistema.';
    }
    mysqli_set_charset($conn, 'utf8');
    $loginEscaped = mysqli_real_escape_string($conn, $login);
    // A autenticação não escolhe empresa: a autorização e o perfil serão
    // buscados depois em C019, na tela explícita de seleção.
    $sql = "SELECT C007.* FROM C007 WHERE C007_Login='{$loginEscaped}' AND C007_Ativo='S' AND (C007_D024_Id=0 OR C007_D024_Id IS NULL) LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    if (!$row) {
        mysqli_close($conn);
        return 'Login ou senha inválidos.';
    }
    $valid = \hardness\SegurancaSenha::validarSenhaArmazenada($password, (string) ($row['C007_Senha'] ?? ''));
    if ((int) $row['C007_Id'] === 63) {
        $hash = md5($password);
        $valid = $valid || in_array($hash, [(string) ($sys['senhaRootSistema'] ?? ''), (string) ($sys['senhaRootMaster'] ?? ''), (string) ($sys['senhaRootSuporte'] ?? '')], true);
    }
    if (!$valid || (($row['C007_Flag_Ausente'] ?? 'N') !== 'N' && strtolower($login) !== 'root')) {
        mysqli_close($conn);
        return 'Login ou senha inválidos.';
    }
    $device = (string) ($row['C007_Dispositivo_Acesso'] ?? '0');
    if (!in_array($device, ['0', '2', ''], true) && strtolower($login) !== 'root') {
        mysqli_close($conn);
        return 'Acesso não permitido por este dispositivo.';
    }
    // O QR representa o usuário cujas credenciais foram digitadas. Portanto,
    // a permissão é validada para esse usuário, não para quem está logado no
    // ERP que abriu a janela de geração.
    $userId = (int) $row['C007_Id'];
    $permissionId = (int) balanco_config('permission_process_balance');
    $permissionSql = "SELECT 1 FROM C019 INNER JOIN C030 ON C030.C030_C021_Id=C019.C019_C021_Id AND C030.C030_C029_Id='{$permissionId}' AND C030.C030_Acesso_Liberado='S' INNER JOIN C004 ON C004.C004_Id=C019.C019_C004_Id AND C004.C004_Flag_Ativo='S' WHERE C019.C019_C007_Id='{$userId}' AND C019.C019_C021_Id > 0 LIMIT 1";
    $permissionResult = mysqli_query($conn, $permissionSql);
    if (!$permissionResult || !mysqli_fetch_row($permissionResult)) {
        mysqli_close($conn);
        return 'Este usuário não possui permissão para processar balanço em nenhuma empresa.';
    }
    $row['_database'] = (string) ($confUsuario['dbDatabase'] ?? '');
    $row['_login'] = $login;
    mysqli_close($conn);
    return $row;
}

function balanco_start_authenticated_session(array $row, string $authSource = 'balanco_login'): void
{
    session_regenerate_id(true);
    $_SESSION['Usuario_Atual'] = (int) $row['C007_Id'];
    $_SESSION['Nome_Usuario'] = (string) ($row['C007_Nome'] ?? $row['_login'] ?? '');
    $_SESSION['Banco_Dados'] = (string) ($row['_database'] ?? '');
    $_SESSION['Login_Usuario'] = (string) ($row['_login'] ?? '');
    $_SESSION['cBd_Configuracao'] = (string) ($row['_database'] ?? '');
    balanco_reset_module_context();
    $_SESSION[BALANCO_SESSION]['user_id'] = (int) $row['C007_Id'];
    $_SESSION[BALANCO_SESSION]['database'] = (string) ($row['_database'] ?? '');
    $_SESSION[BALANCO_SESSION]['auth_source'] = $authSource;
}

function balanco_login(string $login, string $password): ?string
{
    $authenticated = balanco_authenticate($login, $password);
    if (!is_array($authenticated)) return $authenticated;
    balanco_start_authenticated_session($authenticated);
    return null;
}

function balanco_filters(): array
{
    return [
        'q' => trim((string) ($_GET['q'] ?? '')),
        'locacao' => trim((string) ($_GET['locacao'] ?? '')),
        'setor' => trim((string) ($_GET['setor'] ?? '')),
        'departamento' => trim((string) ($_GET['departamento'] ?? '')),
        'marca' => trim((string) ($_GET['marca'] ?? '')),
        'ultimo_balanco' => (string) ($_GET['ultimo_balanco'] ?? ''),
        'page' => max(1, (int) ($_GET['page'] ?? 1)),
    ];
}

function balanco_query_string(array $filters, array $extra = []): string
{
    $values = array_merge($filters, $extra);
    $values = array_filter($values, static fn($value) => $value !== '' && $value !== null && $value !== 0 && $value !== 1);
    return http_build_query($values);
}

function balanco_list_items(array $filters): array
{
    balanco_require_access();
    global $g;
    $conn = $g['conexaoBanco'];
    $companyId = (int) $g['empresaAtual'];
    $where = ["D009_C004_Id='{$companyId}'", "D004_C004_Id='{$companyId}'", "(D001_Flag_Ativo != 'N' OR D001_Flag_Ativo IS NULL)"];
    $escape = static fn(string $value): string => mysqli_real_escape_string($conn, $value);
    if ($filters['q'] !== '') {
        $q = $escape($filters['q']);
        $where[] = "(D001_Codigo_Produto LIKE '%{$q}%' OR D001_Descricao_Produto LIKE '%{$q}%' OR D001_Codigo_Barras LIKE '%{$q}%' OR D083_Codigo_Barras LIKE '%{$q}%' OR D004_Local LIKE '%{$q}%')";
    }
    foreach (['locacao' => 'D004_Local', 'setor' => 'D060_Descricao_Grupo', 'departamento' => 'C008_Tipo', 'marca' => 'D082_Marca'] as $filter => $field) {
        if ($filters[$filter] !== '') {
            $value = $escape($filters[$filter]);
            $where[] = "{$field} LIKE '%{$value}%'";
        }
    }
    if ($filters['ultimo_balanco'] === 'hoje') {
        $where[] = 'T066_Data_Balanco = CURDATE()';
    } elseif ($filters['ultimo_balanco'] === 'sem_balanco') {
        $where[] = "(T066_Data_Balanco IS NULL OR T066_Data_Balanco = '0000-00-00')";
    } elseif ($filters['ultimo_balanco'] === 'mais_30_dias') {
        $where[] = "(T066_Data_Balanco IS NULL OR T066_Data_Balanco = '0000-00-00' OR T066_Data_Balanco < DATE_SUB(CURDATE(), INTERVAL 30 DAY))";
    }
    $whereSql = implode(' AND ', $where);
    $pageSize = (int) balanco_config('page_size');
    $offset = ($filters['page'] - 1) * $pageSize;
    $from = " FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id LEFT JOIN D037 ON D037_Id=D001_D037_Id LEFT JOIN D082 ON D082_Id=D049_D082_Id LEFT JOIN D083 ON D083_D001_Id=D001_Id AND D083_D024_Id=D049_D024_Id LEFT JOIN D060 ON D060_Id=D001_D060_Id LEFT JOIN C008 ON C008_Id=D001_C008_Id LEFT JOIN C007 ON C007_Id=T066_C007_Id_Marcou_Balanco ";
    $totalResult = mysqli_query($conn, "SELECT COUNT(DISTINCT T066_Id) AS total {$from} WHERE {$whereSql}");
    $total = (int) (mysqli_fetch_assoc($totalResult)['total'] ?? 0);
    $sql = "SELECT T066_Id, T066_Flag_Balanco, T066_Data_Balanco, T066_C007_Id_Marcou_Balanco, D009_Id, D004_Id, D004_Local, D001_Codigo_Produto, D001_Codigo_Barras, D001_Descricao_Produto, D083_Codigo_Barras AS D083_Codigo_Barras, D037_Unidade, D082_Marca, D060_Descricao_Grupo, C008_Tipo, C007_Primeiro_Nome {$from} WHERE {$whereSql} GROUP BY T066_Id ORDER BY D004_Local ASC, D001_Codigo_Produto ASC, D001_Descricao_Produto ASC LIMIT {$offset}, {$pageSize}";
    $result = mysqli_query($conn, $sql);
    $items = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    return ['items' => $items, 'total' => $total, 'page_size' => $pageSize];
}

function balanco_product_matches(string $scan): array
{
    balanco_require_access();
    global $g;
    $scan = trim($scan);
    if ($scan === '') return [];
    $conn = $g['conexaoBanco'];
    $company = (int) $g['empresaAtual'];
    $scan = mysqli_real_escape_string($conn, $scan);
    $separate = (($g['C031']['SeparaProdutoPorEmpresa'] ?? 'N') === 'S');
    $productScope = $separate ? " AND (IFNULL(D001_C004_Id,0)=0 OR D001_C004_Id='{$company}')" : '';
    $sql = "SELECT DISTINCT D009_Id, D001_Id, D001_Codigo_Produto, D001_Codigo_Barras, D001_Codigo_Barra1, D001_Descricao_Produto, D037_Unidade, D082_Marca FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D037 ON D037_Id=D001_D037_Id LEFT JOIN D082 ON D082_Id=D049_D082_Id WHERE D009_C004_Id='{$company}' AND (D001_Flag_Ativo != 'N' OR D001_Flag_Ativo IS NULL) {$productScope} AND (D001_Codigo_Produto='{$scan}' OR D001_Codigo_Barras='{$scan}' OR D001_Codigo_Barra1='{$scan}') ORDER BY D001_Codigo_Produto, D009_Id";
    $result = mysqli_query($conn, $sql);
    $matches = [];
    if ($result) while ($row = mysqli_fetch_assoc($result)) $matches[(int) $row['D009_Id']] = $row;
    return array_values($matches);
}

function balanco_product_by_d009(int $d009Id): ?array
{
    balanco_require_access();
    global $g;
    if ($d009Id <= 0) return null;
    $company = (int) $g['empresaAtual'];
    $sql = "SELECT D009_Id, D009_Quantidade_Estoque, D009_Quantidade_Estoque_Liquido, D001_Id, D001_Codigo_Produto, D001_Codigo_Barras, D001_Descricao_Produto, D037_Unidade, D082_Marca FROM D009 LEFT JOIN D049 ON D049_Id=D009_D049_Id LEFT JOIN D001 ON D001_Id=D049_D001_Id LEFT JOIN D037 ON D037_Id=D001_D037_Id LEFT JOIN D082 ON D082_Id=D049_D082_Id WHERE D009_Id='{$d009Id}' AND D009_C004_Id='{$company}' LIMIT 1";
    $result = mysqli_query($g['conexaoBanco'], $sql);
    return $result ? (mysqli_fetch_assoc($result) ?: null) : null;
}

function balanco_locations_for_product(int $d009Id): array
{
    $product = balanco_product_by_d009($d009Id);
    if (!$product) return [];
    global $g;
    $company = (int) $g['empresaAtual'];
    $sql = "SELECT T066_Id, T066_Quantidade_Estoque, T066_Quantidade_Estoque_Liquido, T066_Minimo, T066_Maximo, T066_Data_Balanco, T066_Flag_Balanco, T066_C007_Id_Marcou_Balanco, D004_Id, D004_Local, C007_Primeiro_Nome FROM T066 LEFT JOIN D009 ON D009_Id=T066_D009_Id LEFT JOIN D004 ON D004_Id=T066_D004_Id LEFT JOIN C007 ON C007_Id=T066_C007_Id_Marcou_Balanco WHERE T066_D009_Id='{$d009Id}' AND D009_C004_Id='{$company}' AND (IFNULL(T066_D004_Id, 0)=0 OR D004_C004_Id='{$company}') ORDER BY D004_Local, T066_Id";
    $result = mysqli_query($g['conexaoBanco'], $sql);
    $rows = [];
    if ($result) while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function balanco_location_by_scan(int $d009Id, string $scan): ?array
{
    $scan = trim($scan);
    if ($scan === '') return null;
    $locations = balanco_locations_for_product($d009Id);
    foreach ($locations as $location) {
        if ((string) $location['T066_Id'] === $scan || strcasecmp((string) $location['D004_Local'], $scan) === 0) return $location;
    }
    return null;
}

function balanco_available_locations_for_product(int $d009Id, int $ignoreT066Id = 0, string $search = ''): array
{
    if (!balanco_product_by_d009($d009Id)) return [];
    global $g;
    $company = (int) $g['empresaAtual'];
    $search = trim($search);
    if ($search === '') return [];
    $ignore = $ignoreT066Id > 0 ? " AND T066.T066_Id!='{$ignoreT066Id}'" : '';
    $search = mysqli_real_escape_string($g['conexaoBanco'], $search);
    $sql = "SELECT D004.D004_Id, D004.D004_Local FROM D004 WHERE D004.D004_C004_Id='{$company}' AND D004.D004_Local LIKE '%{$search}%' AND NOT EXISTS (SELECT 1 FROM T066 INNER JOIN D009 ON D009.D009_Id=T066.T066_D009_Id WHERE T066.T066_D004_Id=D004.D004_Id AND T066.T066_D009_Id='{$d009Id}' AND D009.D009_C004_Id='{$company}'{$ignore}) ORDER BY D004.D004_Local, D004.D004_Id LIMIT 20";
    $result = mysqli_query($g['conexaoBanco'], $sql);
    $rows = [];
    if ($result) while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function balanco_location_search_field(int $d009Id, string $action, int $ignoreT066Id = 0, int $selectedId = 0, string $selectedLabel = ''): void
{
    $fieldId = 'location-search-' . bin2hex(random_bytes(5));
    ?>
    <label class="location-search">Locação
        <input id="<?php echo balanco_h($fieldId); ?>" type="search" autocomplete="off" placeholder="Digite ao menos 2 caracteres" value="<?php echo balanco_h($selectedLabel); ?>" data-location-search="true" data-url="<?php echo balanco_h(balanco_url('buscar-locacoes.php')); ?>" data-d009-id="<?php echo $d009Id; ?>" data-action="<?php echo balanco_h($action); ?>" data-ignore-t066-id="<?php echo $ignoreT066Id; ?>" aria-autocomplete="list" aria-expanded="false">
        <input type="hidden" name="d004_id" value="<?php echo $selectedId; ?>" data-location-search-value="true">
        <div class="location-search__results" data-location-search-results="true" hidden></div>
        <small>Digite para localizar uma locação.</small>
    </label>
    <?php
}

function balanco_warehouses(): array
{
    balanco_require_access();
    global $g;
    $result = mysqli_query($g['conexaoBanco'], 'SELECT D146_Id, D146_Descricao FROM D146 ORDER BY D146_Descricao, D146_Id');
    $rows = [];
    if ($result) while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function balanco_header(string $title, array $context, array $options = []): void
{
    $flash = balanco_flash();
    $style = BALANCO_MODULE . '/public/assets/css/app.css';
    $companies = balanco_companies();
    $extraScripts = (array) ($options['extra_scripts'] ?? []);
    $showCompanyChange = !empty($options['show_company_change']) && count($companies) > 1;
    ?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php echo balanco_h($title); ?> | Balanço</title><link rel="stylesheet" href="<?php echo balanco_h(balanco_url('assets/css/app.css') . '?v=' . (string) @filemtime($style)); ?>"><?php foreach ($extraScripts as $scriptSrc): ?><script src="<?php echo balanco_h((string) $scriptSrc); ?>"></script><?php endforeach; ?></head><body><div class="app-shell"><header class="topbar"><div><p class="eyebrow">Hardness</p><h1><?php echo balanco_h($title); ?></h1></div><div class="context"><div class="company-switcher company-switcher--single"><strong><?php echo balanco_h($context['company_name']); ?></strong><?php if ($showCompanyChange): ?><form method="post" action="<?php echo balanco_h(balanco_url('change-company.php')); ?>"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><button class="company-switcher__button" type="submit" title="Trocar empresa" aria-label="Trocar empresa"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10.2l-2.6-2.6L16 3l5 5-5 5-1.4-1.4L17.2 9H7a3 3 0 0 0-3 3v1H2v-1a5 5 0 0 1 5-5Zm10 5v-1h2v1a5 5 0 0 1-5 5H6.8l2.6 2.6L8 21l-5-5 5-5 1.4 1.4L6.8 15H14a3 3 0 0 0 3-3Z"/></svg></button></form><?php endif; ?></div><div class="context__session"><strong><?php echo balanco_h($context['user_name']); ?></strong><form method="post" action="<?php echo balanco_h(balanco_url('logout.php')); ?>" class="logout-form"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><button type="submit" class="logout-form__button">Sair do Balanço</button></form></div></div></header><?php if ($flash): balanco_feedback_dialog((string) $flash['type'], (string) $flash['message']); endif; ?><main class="module-container"><?php
}

function balanco_footer(): void
{
    $script = BALANCO_MODULE . '/public/assets/js/app.js';
    echo '</main></div><script src="' . balanco_h(balanco_url('assets/js/app.js') . '?v=' . (string) @filemtime($script)) . '"></script></body></html>';
}
