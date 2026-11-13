<?php
declare(strict_types=1);

session_start();

const TABLET_SEPARACAO_SESSION = 'tablet_separacao';
const TABLET_SEPARACAO_ROOT = __DIR__ . '/../../..';

function app_base_path(string $path = ''): string
{
    $base = '/hardness3/mobile/tablet_separacao';
    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function tablet_separacao_qr_handoff_url(string $code): string
{
    // Ambiente local de teste: o QR deve abrir pelo túnel para que o celular
    // alcance o ERP local. Fora de localhost, permanece a URL da instalação.
    $requestHost = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $requestHost = preg_replace('/:\\d+$/', '', $requestHost);
    if (in_array($requestHost, ['localhost', '127.0.0.1'], true)) {
        return 'https://spray-boats-viewing-objective.trycloudflare.com/separacao/abrir-qr.php?codigo='
            . rawurlencode($code);
    }

    $confUsuario = [];
    require TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';
    return rtrim((string) ($confUsuario['urlRaiz'] ?? ''), '/')
        . '/separacao/abrir-qr.php?codigo=' . rawurlencode($code);
}

function tablet_separacao_qr_handoff_directory(string $database): string
{
    $database = preg_replace('/[^A-Za-z0-9_-]/', '', $database);
    if ($database === '') {
        throw new RuntimeException('Banco de dados inválido para abertura da Separação.');
    }

    $directory = TABLET_SEPARACAO_ROOT . '/dados_usuarios/' . $database . '/tablet_separacao_qr_handoffs';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Não foi possível preparar a abertura da Separação no celular.');
    }
    return $directory;
}

function tablet_separacao_qr_issue_handoff(array $context): array
{
    $code = bin2hex(random_bytes(32));
    $database = (string) ($context['database'] ?? '');
    $payload = [
        'user_id' => (int) ($context['user_id'] ?? 0),
        'database' => $database,
        'expires_at' => time() + 120,
    ];
    $file = tablet_separacao_qr_handoff_directory($database) . '/' . hash('sha256', $code) . '.json';
    if (file_put_contents($file, json_encode($payload), LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível gerar o código de abertura da Separação.');
    }
    @chmod($file, 0600);
    return ['code' => $code, 'expires_at' => $payload['expires_at']];
}

function tablet_separacao_qr_consume_handoff(string $code): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $code)) {
        return null;
    }

    $confUsuario = [];
    require TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';
    $database = (string) ($confUsuario['dbDatabase'] ?? '');
    $file = tablet_separacao_qr_handoff_directory($database) . '/' . hash('sha256', $code) . '.json';
    $handle = @fopen($file, 'c+');
    if (!$handle) {
        return null;
    }

    $payload = null;
    if (flock($handle, LOCK_EX)) {
        if (is_file($file)) {
            rewind($handle);
            $candidate = json_decode((string) stream_get_contents($handle), true);
            if (is_array($candidate)
                && (int) ($candidate['expires_at'] ?? 0) >= time()
                && (string) ($candidate['database'] ?? '') === $database) {
                $payload = $candidate;
            }
        }
        @unlink($file);
        flock($handle, LOCK_UN);
    }
    fclose($handle);
    return $payload;
}

function tablet_separacao_qr_svg_data_uri(string $url): string
{
    require_once TABLET_SEPARACAO_ROOT . '/hardness/phpqrcode/phpqrcode.php';
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

function tablet_separacao_qr_csrf_token(): string
{
    if (empty($_SESSION[TABLET_SEPARACAO_SESSION]['qr_csrf'])) {
        $_SESSION[TABLET_SEPARACAO_SESSION]['qr_csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION[TABLET_SEPARACAO_SESSION]['qr_csrf'];
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function tablet_separacao_bootstrap(): void
{
    if (!isset($_SESSION[TABLET_SEPARACAO_SESSION])) {
        $_SESSION[TABLET_SEPARACAO_SESSION] = [
            'user' => null,
            'orders' => tablet_separacao_seed_orders(),
            'companies' => [],
            'active_company_id' => null,
            'active_profile_id' => null,
        ];
    }

    tablet_separacao_bridge_legacy_session();
    tablet_separacao_bootstrap_legacy_env();
}

function tablet_separacao_system_logo(): array
{
    static $brand = null;

    if ($brand !== null) {
        return $brand;
    }

    $brand = [
        'src' => '/hardness3/static/img/logo/logoHardnessH.svg',
        'alt' => 'Hardness Sistemas',
    ];

    $confUsuario = [];
    $sysLocal = [];
    $sys = [];

    $confFile = TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';
    $sysFile = TABLET_SEPARACAO_ROOT . '/configuracoes/sysConfig.php';

    if (is_file($confFile)) {
        require $confFile;
    }

    if (is_file($sysFile)) {
        require $sysFile;
    }

    if (!empty($sys['logoSistema'])) {
        $brand['src'] = (string) $sys['logoSistema'];
    }

    if (!empty($sys['nomeEmpresa'])) {
        $brand['alt'] = (string) $sys['nomeEmpresa'];
    }

    return $brand;
}

function tablet_separacao_current_database(): string
{
    static $database = null;

    if ($database !== null) {
        return $database;
    }

    $candidates = [
        $_SESSION['Banco_Dados'] ?? null,
        $_SESSION['bancoDados'] ?? null,
        $_SESSION['dbDatabase'] ?? null,
        $_SESSION['cBd_Configuracao'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        $candidate = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $candidate);
        if ($candidate !== '') {
            $database = $candidate;
            return $database;
        }
    }

    $database = '';
    return $database;
}

function tablet_separacao_config(string $key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = tablet_separacao_load_config();
    }

    return array_key_exists($key, $config) ? $config[$key] : $default;
}

function tablet_separacao_load_config(): array
{
    $configFile = __DIR__ . '/../personalizacaoConf/_GLOBAL.php';
    $config = is_file($configFile) ? (array) require $configFile : [];

    $database = tablet_separacao_current_database();
    if ($database === '') {
        return $config;
    }

    $overrideFiles = [
        __DIR__ . '/../personalizacaoConf/' . $database . '.php',
        __DIR__ . '/../personalizacaoConf/' . $database . '.tablet_separacao.php',
        __DIR__ . '/../personalizacaoConf/tablet_separacao/' . $database . '.php',
    ];

    foreach ($overrideFiles as $overrideFile) {
        if (!is_file($overrideFile)) {
            continue;
        }

        $overrideConfig = (array) require $overrideFile;
        $config = tablet_separacao_config_merge($config, $overrideConfig);
    }

    $config = tablet_separacao_apply_class_override($config);

    return $config;
}

function tablet_separacao_apply_class_override(array $config): array
{
    $targetClass = tablet_separacao_customization_class();
    if ($targetClass === null || !is_callable([$targetClass, 'overrides'])) {
        return $config;
    }

    $overrideConfig = (array) call_user_func([$targetClass, 'overrides'], $config);
    return tablet_separacao_config_merge($config, $overrideConfig);
}

function tablet_separacao_customization_class(): ?string
{
    static $resolved = false;
    static $targetClass = null;

    if ($resolved) {
        return $targetClass;
    }

    $resolved = true;
    $classFile = TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/TABLETSEPARACAO.php';
    if (is_file($classFile)) {
        require_once $classFile;
    }

    $classCandidates = [
        'TABLETSEPARACAO',
        'TABLETSEPARACAO_',
        '\\hardness\\TABLETSEPARACAO',
        '\\hardness\\TABLETSEPARACAO_',
    ];
    foreach ($classCandidates as $className) {
        if (class_exists($className)) {
            $targetClass = $className;
            break;
        }
    }

    return $targetClass;
}

function tablet_separacao_personalize_order_items(int $orderId, array $items): array
{
    $targetClass = tablet_separacao_customization_class();
    if ($targetClass === null || !is_callable([$targetClass, 'personalizarItensPedido'])) {
        return $items;
    }

    $personalizedItems = call_user_func([$targetClass, 'personalizarItensPedido'], $orderId, $items);
    return is_array($personalizedItems) ? $personalizedItems : $items;
}

function tablet_separacao_personalize_finalize_result($result): ?array
{
    $targetClass = tablet_separacao_customization_class();
    if ($targetClass === null || !is_callable([$targetClass, 'tratarRetornoFinalizacao'])) {
        return null;
    }

    $personalizedResult = call_user_func([$targetClass, 'tratarRetornoFinalizacao'], $result);
    return is_array($personalizedResult) ? $personalizedResult : null;
}

function tablet_separacao_config_merge(array $baseConfig, array $overrideConfig): array
{
    foreach ($overrideConfig as $key => $value) {
        if (
            array_key_exists($key, $baseConfig)
            && is_array($baseConfig[$key])
            && is_array($value)
            && tablet_separacao_is_assoc_array($baseConfig[$key])
            && tablet_separacao_is_assoc_array($value)
        ) {
            $baseConfig[$key] = tablet_separacao_config_merge($baseConfig[$key], $value);
            continue;
        }

        $baseConfig[$key] = $value;
    }

    return $baseConfig;
}

function tablet_separacao_is_assoc_array(array $value): bool
{
    if ($value === []) {
        return false;
    }

    return array_keys($value) !== range(0, count($value) - 1);
}

function tablet_separacao_seed_orders(): array
{
    return [
        2245 => [
            'id' => 2245,
            'client_code' => 774,
            'client_name' => 'ADALBERTO SACCHI',
            'neighborhood' => '',
            'city' => 'ABATIA',
            'uf' => 'PR',
            'issued_at' => '2026-04-01',
            'cfop' => '5.403',
            'carrier' => 'TRANSPORTADORA PADRAO',
            'box' => '',
            'seller' => 'USUARIO 255',
            'status' => 'Pendente',
            'status_flag' => '1',
            'items' => [
                101 => [
                    'id' => 101,
                    'code' => '7890001',
                    'name' => 'FRESA TOPO ACO RAPIDO 6,00MM 4 CORTES RETO HASTE 6MM DIN 844N',
                    'unit' => 'UN',
                    'required_qty' => 3.0,
                    'separated_qty' => 0.0,
                    'default_location' => 'LOTE ND - LOC: 00.0',
                    'location_barcode' => '00.0',
                    'address' => '00.0',
                    'locations' => [
                        ['label' => 'LOTE ND - LOC: 00.0', 'barcode' => '00.0', 'address' => '00.0', 'stock_qty' => 5.0],
                        ['label' => 'LOTE ZX - LOC: 00.1', 'barcode' => '00.1', 'address' => '00.1', 'stock_qty' => 2.0],
                    ],
                    'stock_qty' => 5.0,
                    'last_scan' => null,
                    'separations' => [],
                ],
                102 => [
                    'id' => 102,
                    'code' => '7890002',
                    'name' => 'BROCA HELICOIDAL 8,00MM HSS',
                    'unit' => 'UN',
                    'required_qty' => 2.0,
                    'separated_qty' => 1.0,
                    'default_location' => 'LOTE AX - LOC: 01.2',
                    'location_barcode' => '01.2',
                    'address' => '01.2',
                    'locations' => [
                        ['label' => 'LOTE AX - LOC: 01.2', 'barcode' => '01.2', 'address' => '01.2', 'stock_qty' => 4.0],
                    ],
                    'stock_qty' => 4.0,
                    'last_scan' => '01.2',
                    'separations' => [
                        [
                            'key' => 'mock-102-0',
                            'location_label' => 'LOTE AX - LOC: 01.2',
                            'location_barcode' => '01.2',
                            'address' => '01.2',
                            'quantity' => 1.0,
                        ],
                    ],
                ],
                103 => [
                    'id' => 103,
                    'code' => '7890003',
                    'name' => 'ALARGADOR CONICO 12MM',
                    'unit' => 'UN',
                    'required_qty' => 1.0,
                    'separated_qty' => 0.0,
                    'default_location' => 'LOTE BX - LOC: 05.3',
                    'location_barcode' => '05.3',
                    'address' => '05.3',
                    'locations' => [
                        ['label' => 'LOTE BX - LOC: 05.3', 'barcode' => '05.3', 'address' => '05.3', 'stock_qty' => 1.0],
                    ],
                    'stock_qty' => 1.0,
                    'last_scan' => null,
                    'separations' => [],
                ],
            ],
        ],
        1422 => [
            'id' => 1422,
            'client_code' => 512,
            'client_name' => 'GABRIEL PASQUINI',
            'neighborhood' => '',
            'city' => 'CURITIBA',
            'uf' => 'PR',
            'issued_at' => '2026-04-03',
            'cfop' => '5.101',
            'carrier' => 'EXPRESSO SUL',
            'box' => 'B12',
            'seller' => 'TESTE 1',
            'status' => 'Em separacao',
            'status_flag' => '2',
            'items' => [
                201 => [
                    'id' => 201,
                    'code' => '7891001',
                    'name' => 'PAQUIMETRO DIGITAL 150MM',
                    'unit' => 'UN',
                    'required_qty' => 1.0,
                    'separated_qty' => 0.0,
                    'default_location' => 'LOTE ZZ - LOC: 10.1',
                    'location_barcode' => '10.1',
                    'address' => '10.1',
                    'locations' => [
                        ['label' => 'LOTE ZZ - LOC: 10.1', 'barcode' => '10.1', 'address' => '10.1', 'stock_qty' => 1.0],
                    ],
                    'stock_qty' => 1.0,
                    'last_scan' => null,
                    'separations' => [],
                ],
            ],
        ],
    ];
}

function tablet_separacao_all_orders(): array
{
    tablet_separacao_bootstrap();
    return $_SESSION[TABLET_SEPARACAO_SESSION]['orders'];
}

function tablet_separacao_location_identifier_field(): string
{
    return (string) tablet_separacao_config('locacao_busca_por', 'D004_Local');
}

function tablet_separacao_bootstrap_legacy_env(): bool
{
    static $loaded = false;
    static $available = false;

    if ($loaded) {
        return $available;
    }

    $loaded = true;

    if (empty($_SESSION['Usuario_Atual']) || empty($_SESSION['Empresa_Atual'])) {
        return false;
    }

    global $g, $confUsuario;

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';

    $g['urlRaiz'] = $confUsuario['urlRaiz'];
    $g['pathRaiz'] = $confUsuario['pathRaiz'];
    $g['tabelasCache'] = [];

    chdir($g['pathRaiz']);

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/sessao.php';
    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/conexaoBanco.php';
    require_once TABLET_SEPARACAO_ROOT . '/sistema/interfaceUsuario.php';
    require_once TABLET_SEPARACAO_ROOT . '/sistema/automacaoBanco.php';
    require_once TABLET_SEPARACAO_ROOT . '/sistema/automacaoFormulario.php';
    require_once TABLET_SEPARACAO_ROOT . '/sistema/confSistema.php';
    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/restricoes.php';
    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/funcoesGerais.php';
    require_once TABLET_SEPARACAO_ROOT . '/sistema/apps.php';

    $databaseName = $g['dbDatabase'] ?? $confUsuario['dbDatabase'] ?? '';
    $databaseName = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $databaseName);
    $g['pathDados'] = "dados_usuarios/{$databaseName}/";

    $g['conf'] = new \hardness\confSistema();

    $empresaAtualSessao = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    $usuarioAtual = (int) ($g['usuarioAtual'] ?? 0);

    if ($usuarioAtual <= 0) {
        return false;
    }

    $g['pathDadosUsuario'] = $g['pathDados'] . 'usuarios/u_' . str_repeat('0', max(0, 6 - strlen((string) $usuarioAtual))) . $usuarioAtual . '/';
    $g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDados'] . '/';
    $g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $confUsuario['pathRaiz']) . $g['pathDadosUsuario'] . '/';

    $conn = $g['conexaoBanco'];

    $companies = tablet_separacao_fetch_user_companies($conn, $usuarioAtual);
    $_SESSION[TABLET_SEPARACAO_SESSION]['companies'] = $companies;

    $activeCompanyId = (int) ($_SESSION[TABLET_SEPARACAO_SESSION]['active_company_id'] ?? 0);
    if ($activeCompanyId <= 0 || !isset($companies[$activeCompanyId])) {
        $activeCompanyId = isset($companies[$empresaAtualSessao]) ? $empresaAtualSessao : (int) array_key_first($companies);
    }

    if ($activeCompanyId <= 0 || !isset($companies[$activeCompanyId])) {
        return false;
    }

    $activeProfileId = (int) ($companies[$activeCompanyId]['profile_id'] ?? 0);

    $_SESSION[TABLET_SEPARACAO_SESSION]['active_company_id'] = $activeCompanyId;
    $_SESSION[TABLET_SEPARACAO_SESSION]['active_profile_id'] = $activeProfileId;

    $g['empresaAtual'] = $activeCompanyId;
    $g['perfilUsuario'] = $activeProfileId;
    $empresaAtual = $activeCompanyId;
    $perfilUsuario = $activeProfileId;

    $resC031 = mysqli_query($conn, "SELECT * FROM C031 WHERE C031_C004_Id = '{$empresaAtual}'");
    while ($row = mysqli_fetch_assoc($resC031)) {
        $g['c031'][$row['C031_Campo']] = $row['C031_Valor'];
        $g['C031'][$row['C031_Campo']] = $row['C031_Valor'];
    }

    $resC020 = mysqli_query($conn, "SELECT * FROM C020 WHERE C020_C004_Id = '{$empresaAtual}'");
    $g['c020'] = $g['C020'] = $resC020 ? mysqli_fetch_assoc($resC020) : [];

    $resC004 = mysqli_query($conn, "SELECT * FROM C004 LEFT JOIN D020 ON D020_Id=C004_D020_Id LEFT JOIN D018 ON D018_Id=C004_D018_Id WHERE C004_Id='{$empresaAtual}'");
    $g['c004'] = $g['C004'] = $resC004 ? mysqli_fetch_assoc($resC004) : [];

    $resC007 = mysqli_query($conn, "SELECT * FROM C007 WHERE C007_Id='{$usuarioAtual}'");
    $g['c007'] = $g['C007'] = $resC007 ? mysqli_fetch_assoc($resC007) : [];

    $resC021 = mysqli_query($conn, "SELECT * FROM C021 WHERE C021_Id='{$perfilUsuario}'");
    $g['c021'] = $g['C021'] = $resC021 ? mysqli_fetch_assoc($resC021) : [];

    $g['c029Ids'] = [];
    $resC030 = mysqli_query($conn, "SELECT C030_C029_Id FROM C030 WHERE C030_C021_Id='{$perfilUsuario}' AND C030_Acesso_Liberado='S'");
    if ($resC030) {
        while ($row = mysqli_fetch_row($resC030)) {
            $g['c029Ids'][$row[0]] = true;
        }
    }

    $available = true;
    return true;
}

function tablet_separacao_fetch_user_companies(\mysqli $conn, int $userId): array
{
    $sql = <<<SQL
SELECT
    C019_C004_Id AS company_id,
    C019_C021_Id AS profile_id,
    C004_Nome_Empresa,
    C004_Nome_Abreviado
FROM C019
LEFT JOIN C004 ON C004_Id=C019_C004_Id
WHERE C019_C007_Id='{$userId}'
  AND C004_Flag_Ativo='S'
ORDER BY C004_Ordem_Empresa, C004_Id
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $companies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $companyId = (int) $row['company_id'];
        $companies[$companyId] = [
            'id' => $companyId,
            'profile_id' => (int) ($row['profile_id'] ?? 0),
            'name' => (string) ($row['C004_Nome_Empresa'] ?? ''),
            'short_name' => (string) ($row['C004_Nome_Abreviado'] ?? $row['C004_Nome_Empresa'] ?? ''),
        ];
    }

    return $companies;
}

function tablet_separacao_real_mode(): bool
{
    return tablet_separacao_bootstrap_legacy_env();
}

function tablet_separacao_db(): ?\mysqli
{
    if (!tablet_separacao_real_mode()) {
        return null;
    }

    global $g;
    return $g['conexaoBanco'] ?? null;
}

function tablet_separacao_company_id(): int
{
    tablet_separacao_bootstrap();
    return (int) ($_SESSION[TABLET_SEPARACAO_SESSION]['active_company_id'] ?? 0);
}

function tablet_separacao_user_id(): int
{
    $user = tablet_separacao_user();
    return (int) ($user['legacy_user_id'] ?? 0);
}

function tablet_separacao_profile_id(): int
{
    tablet_separacao_bootstrap();
    return (int) ($_SESSION[TABLET_SEPARACAO_SESSION]['active_profile_id'] ?? 0);
}

function tablet_separacao_companies(): array
{
    tablet_separacao_bootstrap();
    return $_SESSION[TABLET_SEPARACAO_SESSION]['companies'] ?? [];
}

function tablet_separacao_active_company(): ?array
{
    $companies = tablet_separacao_companies();
    $companyId = tablet_separacao_company_id();
    return $companies[$companyId] ?? null;
}

function tablet_separacao_switch_company(int $companyId): bool
{
    tablet_separacao_bootstrap();
    $companies = tablet_separacao_companies();

    if (!isset($companies[$companyId])) {
        return false;
    }

    $_SESSION[TABLET_SEPARACAO_SESSION]['active_company_id'] = $companyId;
    $_SESSION[TABLET_SEPARACAO_SESSION]['active_profile_id'] = (int) ($companies[$companyId]['profile_id'] ?? 0);

    return true;
}

function tablet_separacao_orders(string $queue = 'start'): array
{
    if (tablet_separacao_real_mode()) {
        return tablet_separacao_real_orders($queue);
    }

    $orders = tablet_separacao_all_orders();
    if ($queue === 'start') {
        $orders = array_filter($orders, static fn(array $order): bool => $order['status'] === 'Pendente');

        if ((bool) tablet_separacao_config('fila_iniciar_filtrar_entrega_ate_hoje', false)) {
            $orders = array_filter($orders, static fn(array $order): bool => tablet_separacao_order_delivery_available_for_start($order));
        }

        return $orders;
    }
    if ($queue === 'progress') {
        return array_filter($orders, static fn(array $order): bool => $order['status'] !== 'Pendente');
    }
    return $orders;
}

function tablet_separacao_order_delivery_available_for_start(array $order): bool
{
    $deliveryAt = substr((string) ($order['delivery_at'] ?? ''), 0, 10);
    return $deliveryAt === '' || $deliveryAt === '0000-00-00' || $deliveryAt <= date('Y-m-d');
}

function tablet_separacao_real_orders(string $queue): array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return [];
    }

    $empresaId = tablet_separacao_company_id();
    if ($empresaId <= 0) {
        return [];
    }

    $whereStatus = $queue === 'progress'
        ? "T005_Flag_Status='2'"
        : "T005_Flag_Status='1'";
    $whereDelivery = $queue === 'start' && (bool) tablet_separacao_config('fila_iniciar_filtrar_entrega_ate_hoje', false)
        ? "AND (T005_Data_Entrega = '0000-00-00' OR T005_Data_Entrega <= CURDATE())"
        : "";

    $sql = <<<SQL
SELECT
    T005_Id,
    T005_C004_Id,
    T005_Estoque_BOX,
    T005_Data_Emissao,
    T005_Data_Saida,
    T005_Data_Entrega,
    T005_Data_Hora_Almoxarifado,
    T005_Usuario_Pecas,
    T005_C007_Id_Separou,
    T005_Observacao_1,
    T005_Flag_ACP,
    D024_Id AS client_code,
    D024_Nome_Empresa,
    D024_Bairro,
    D020_Nome_Cidade,
    D018_UF,
    D022_Nome_Empresa AS Transportadora,
    Vendedor.C007_Primeiro_Nome AS Nome_Vendedor,
    Separou.C007_Primeiro_Nome AS Nome_Separou,
    Liberou.C007_Primeiro_Nome AS Nome_Liberou
FROM T005
LEFT JOIN D024 ON D024_Id=T005_D024_Id
LEFT JOIN D020 ON D020_Id=D024_D020_Id
LEFT JOIN D018 ON D018_Id=D024_D018_Id
LEFT JOIN D022 ON D022_Id=T005_D022_Id
LEFT JOIN C007 AS Vendedor ON Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
LEFT JOIN C007 AS Separou ON Separou.C007_Id=T005_C007_Id_Separou
LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberacao_Credito_Id
WHERE T005_C004_Id='{$empresaId}'
  AND {$whereStatus}
  {$whereDelivery}
GROUP BY T005_Id
ORDER BY T005_Id
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orderId = (int) $row['T005_Id'];
        $order = [
            'id' => $orderId,
            'client_code' => (int) ($row['client_code'] ?? 0),
            'client_name' => (string) ($row['D024_Nome_Empresa'] ?? ''),
            'neighborhood' => (string) ($row['D024_Bairro'] ?? ''),
            'city' => (string) ($row['D020_Nome_Cidade'] ?? ''),
            'uf' => (string) ($row['D018_UF'] ?? ''),
            'issued_at' => (string) ($row['T005_Data_Emissao'] ?? ''),
            'out_at' => (string) ($row['T005_Data_Saida'] ?? ''),
            'delivery_at' => (string) ($row['T005_Data_Entrega'] ?? ''),
            'warehouse_at' => (string) ($row['T005_Data_Hora_Almoxarifado'] ?? ''),
            'carrier' => (string) ($row['Transportadora'] ?? ''),
            'box' => (string) ($row['T005_Estoque_BOX'] ?? ''),
            'seller' => (string) ($row['Nome_Vendedor'] ?? ''),
            'separator' => (string) ($row['Nome_Separou'] ?? ''),
            'released_by' => (string) ($row['Nome_Liberou'] ?? ''),
            'observation' => (string) ($row['T005_Observacao_1'] ?? ''),
            'document_type' => tablet_separacao_document_type((string) ($row['T005_Flag_ACP'] ?? '')),
            'status' => $queue === 'progress' ? 'Separando' : 'Iniciar separação',
            'status_flag' => $queue === 'progress' ? '2' : '1',
            'company_id' => (int) ($row['T005_C004_Id'] ?? 0),
            'items' => [],
        ];
        $order['items'] = tablet_separacao_real_order_items($orderId);
        $orders[$orderId] = $order;
    }

    return $orders;
}

function tablet_separacao_real_order_items(int $orderId): array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return [];
    }

    $orderId = (int) $orderId;
    $sql = <<<SQL
SELECT
    T006_Id,
    T006_T005_Id,
    T006_D009_Id,
    T006_T066_Id,
    T006_Quantidade,
    COALESCE(T006_Quantidade_Separacao, 0) AS T006_Quantidade_Separacao,
    IFNULL((SELECT SUM(T238_Quantidade_Separacao) FROM T238 WHERE T238_T006_Id=T006_Id), 0) AS T238_Quantidade_Separacao_Soma,
    COALESCE(T006_Descricao_Produto, D001_Descricao_Produto) AS Produto,
    D001_Id,
    D001_Codigo_Produto,
    D037_Unidade,
    D004_Local,
    T066_Quantidade_Estoque
FROM T006
LEFT JOIN T005 ON T005_Id=T006_T005_Id
LEFT JOIN D009 ON D009_Id=T006_D009_Id
LEFT JOIN D049 ON D049_Id=D009_D049_Id
LEFT JOIN D001 ON D001_Id=D049_D001_Id
LEFT JOIN D037 ON D037_Id=D001_D037_Id
LEFT JOIN T066 ON T066_Id=T006_T066_Id
LEFT JOIN D004 ON D004_Id=T066_D004_Id
WHERE T006_T005_Id='{$orderId}'
  AND (T006_T006_Id IS NULL OR T006_T006_Id<=0)
  AND IF(IFNULL(T006_C004_Id_Estoque,0) > 0, T006_C004_Id_Estoque, T005_C004_Id) = T005_C004_Id
ORDER BY T006_Id
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $history = tablet_separacao_real_item_history($orderId);
    $items = [];
    $d009Ids = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $itemId = (int) $row['T006_Id'];
        $d009Id = (int) $row['T006_D009_Id'];
        $quantitySeparated = max(
            (float) ($row['T006_Quantidade_Separacao'] ?? 0),
            (float) ($row['T238_Quantidade_Separacao_Soma'] ?? 0)
        );
        if ($d009Id > 0) {
            $d009Ids[$d009Id] = true;
        }
        $items[$itemId] = [
            'id' => $itemId,
            'order_id' => (int) $row['T006_T005_Id'],
            'd009_id' => $d009Id,
            'd001_id' => (int) ($row['D001_Id'] ?? 0),
            'default_t066_id' => (int) ($row['T006_T066_Id'] ?? 0),
            'code' => (string) ($row['D001_Codigo_Produto'] ?? ''),
            'name' => (string) ($row['Produto'] ?? ''),
            'unit' => (string) ($row['D037_Unidade'] ?? ''),
            'required_qty' => (float) ($row['T006_Quantidade'] ?? 0),
            'separated_qty' => $quantitySeparated,
            'default_location' => (string) ($row['D004_Local'] ?? ''),
            'location_barcode' => (string) ($row['D004_Local'] ?? ''),
            'address' => (string) ($row['D004_Local'] ?? ''),
            'locations' => [],
            'stock_qty' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
            'product_media' => tablet_separacao_product_media((int) ($row['D001_Id'] ?? 0)),
            'last_scan' => !empty($history[$itemId]) ? ($history[$itemId][count($history[$itemId]) - 1]['location_barcode'] ?? null) : null,
            'separations' => $history[$itemId] ?? [],
        ];
    }

    $locationsByProduct = tablet_separacao_real_item_locations(array_keys($d009Ids));
    foreach ($items as &$item) {
        $itemLocations = $locationsByProduct[(int) ($item['d009_id'] ?? 0)] ?? [];
        $item['locations'] = $itemLocations;

        if (!empty($itemLocations)) {
            $firstLocation = $itemLocations[0];
            if (empty($item['default_location'])) {
                $item['default_location'] = (string) ($firstLocation['label'] ?? '');
                $item['location_barcode'] = (string) ($firstLocation['barcode'] ?? '');
                $item['address'] = (string) ($firstLocation['address'] ?? '');
                $item['stock_qty'] = (float) ($firstLocation['stock_qty'] ?? 0);
            }
        }
    }
    unset($item);

    $items = tablet_separacao_personalize_order_items($orderId, $items);

    uasort($items, static function (array $a, array $b): int {
        $locationA = strtoupper((string) ($a['locations'][0]['address'] ?? $a['address'] ?? ''));
        $locationB = strtoupper((string) ($b['locations'][0]['address'] ?? $b['address'] ?? ''));
        $compare = strcmp($locationA, $locationB);
        if ($compare !== 0) {
            return $compare;
        }

        return ((int) $a['id']) <=> ((int) $b['id']);
    });

    return $items;
}

function tablet_separacao_product_media(int $d001Id): ?array
{
    if ($d001Id <= 0) {
        return null;
    }

    $conn = tablet_separacao_db();
    if (!$conn) {
        return null;
    }

    $sql = <<<SQL
SELECT
    T172_Id,
    T172_D001_Id,
    T172_Nome_Arquivo,
    T172_Link_Video
FROM T172
WHERE T172_D001_Id='{$d001Id}'
  AND (T172_Nome_Arquivo <> '' OR T172_Link_Video <> '')
ORDER BY T172_Id
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        return null;
    }

    if (trim((string) ($row['T172_Link_Video'] ?? '')) !== '') {
        return [
            'type' => 'video',
            'label' => 'Produto com video',
            'url' => (string) $row['T172_Link_Video'],
        ];
    }

    $filename = (string) ($row['T172_Nome_Arquivo'] ?? '');
    $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
    if ($extension === '') {
        return null;
    }

    if ($extension === 'pdf') {
        return [
            'type' => 'pdf',
            'label' => 'Arquivo PDF',
            'url' => '/hardness3/static/img/icone_pdf.png',
        ];
    }

    $database = tablet_separacao_current_database();
    if ($database === '') {
        return null;
    }

    return [
        'type' => 'image',
        'label' => 'Foto do produto',
        'url' => "/hardness3/dados_usuarios/{$database}/produtos/{$d001Id}/fotos/{$row['T172_Id']}.{$extension}",
    ];
}

function tablet_separacao_real_item_locations(array $d009Ids): array
{
    $conn = tablet_separacao_db();
    if (!$conn || empty($d009Ids)) {
        return [];
    }

    $d009Ids = array_values(array_filter(array_map('intval', $d009Ids)));
    if (empty($d009Ids)) {
        return [];
    }

    $idsSql = implode(',', $d009Ids);
    $sql = <<<SQL
SELECT
    T066_D009_Id,
    T066_Id,
    T066_Quantidade_Estoque,
    D004_Id,
    D004_Local
FROM T066
LEFT JOIN D004 ON D004_Id=T066_D004_Id
WHERE T066_D009_Id IN ({$idsSql})
ORDER BY D004_Local, T066_Id
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $locations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $d009Id = (int) ($row['T066_D009_Id'] ?? 0);
        $address = (string) ($row['D004_Local'] ?? '');
        $locations[$d009Id][] = [
            'label' => $address !== '' ? $address : ('T066 ' . (int) $row['T066_Id']),
            'barcode' => $address,
            'address' => $address,
            't066_id' => (int) ($row['T066_Id'] ?? 0),
            'd004_id' => (int) ($row['D004_Id'] ?? 0),
            'stock_qty' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
        ];
    }

    return $locations;
}

function tablet_separacao_real_item_history(int $orderId): array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return [];
    }

    $sql = <<<SQL
SELECT
    T238_Id,
    T238_T006_Id,
    T238_Quantidade_Separacao,
    T238_T066_Id,
    T238_D004_Id,
    D004_Local
FROM T238
LEFT JOIN D004 ON D004_Id=T238_D004_Id
WHERE T238_T005_Id='{$orderId}'
ORDER BY T238_Id
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }

    $history = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $itemId = (int) $row['T238_T006_Id'];
        $barcode = (string) ($row['D004_Local'] ?? '');
        $history[$itemId][] = [
            'key' => (int) ($row['T238_Id'] ?? 0),
            'location_label' => $barcode !== '' ? $barcode : ('T066 ' . (int) $row['T238_T066_Id']),
            'location_barcode' => $barcode,
            'address' => $barcode,
            't066_id' => (int) ($row['T238_T066_Id'] ?? 0),
            'd004_id' => (int) ($row['T238_D004_Id'] ?? 0),
            'quantity' => (float) ($row['T238_Quantidade_Separacao'] ?? 0),
        ];
    }

    return $history;
}

function tablet_separacao_find_order(int $orderId): ?array
{
    if (tablet_separacao_real_mode()) {
        return tablet_separacao_real_order_by_id($orderId);
    }

    $orders = tablet_separacao_orders('all');
    return $orders[$orderId] ?? null;
}

function tablet_separacao_real_order_by_id(int $orderId): ?array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return null;
    }

    $empresaId = tablet_separacao_company_id();
    if ($empresaId <= 0 || $orderId <= 0) {
        return null;
    }

    $sql = <<<SQL
SELECT
    T005_Id,
    T005_C004_Id,
    T005_Estoque_BOX,
    T005_Data_Emissao,
    T005_Data_Entrega,
    T005_Data_Saida,
    T005_Data_Hora_Almoxarifado,
    T005_Flag_Status,
    T005_Observacao_1,
    T005_Flag_ACP,
    D024_Id AS client_code,
    D024_Nome_Empresa,
    D024_Bairro,
    D020_Nome_Cidade,
    D018_UF,
    D022_Nome_Empresa AS Transportadora,
    Vendedor.C007_Primeiro_Nome AS Nome_Vendedor,
    Separou.C007_Primeiro_Nome AS Nome_Separou,
    Liberou.C007_Primeiro_Nome AS Nome_Liberou
FROM T005
LEFT JOIN D024 ON D024_Id=T005_D024_Id
LEFT JOIN D020 ON D020_Id=D024_D020_Id
LEFT JOIN D018 ON D018_Id=D024_D018_Id
LEFT JOIN D022 ON D022_Id=T005_D022_Id
LEFT JOIN C007 AS Vendedor ON Vendedor.C007_Id=T005_C007_Id_Vendedor_Interno
LEFT JOIN C007 AS Separou ON Separou.C007_Id=T005_C007_Id_Separou
LEFT JOIN C007 AS Liberou ON Liberou.C007_Id=T005_Liberacao_Credito_Id
WHERE T005_C004_Id='{$empresaId}'
  AND T005_Id='{$orderId}'
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    if (!$row) {
        return null;
    }

    return [
        'id' => (int) $row['T005_Id'],
        'client_code' => (int) ($row['client_code'] ?? 0),
        'client_name' => (string) ($row['D024_Nome_Empresa'] ?? ''),
        'neighborhood' => (string) ($row['D024_Bairro'] ?? ''),
        'city' => (string) ($row['D020_Nome_Cidade'] ?? ''),
        'uf' => (string) ($row['D018_UF'] ?? ''),
        'issued_at' => (string) ($row['T005_Data_Emissao'] ?? ''),
        'out_at' => (string) ($row['T005_Data_Saida'] ?? ''),
        'delivery_at' => (string) ($row['T005_Data_Entrega'] ?? ''),
        'warehouse_at' => (string) ($row['T005_Data_Hora_Almoxarifado'] ?? ''),
        'carrier' => (string) ($row['Transportadora'] ?? ''),
        'box' => (string) ($row['T005_Estoque_BOX'] ?? ''),
        'seller' => (string) ($row['Nome_Vendedor'] ?? ''),
        'separator' => (string) ($row['Nome_Separou'] ?? ''),
        'released_by' => (string) ($row['Nome_Liberou'] ?? ''),
        'observation' => (string) ($row['T005_Observacao_1'] ?? ''),
        'document_type' => tablet_separacao_document_type((string) ($row['T005_Flag_ACP'] ?? '')),
        'status' => ((string) ($row['T005_Flag_Status'] ?? '') === '2') ? 'Separando' : 'Iniciar separação',
        'status_flag' => (string) ($row['T005_Flag_Status'] ?? ''),
        'company_id' => (int) ($row['T005_C004_Id'] ?? 0),
        'items' => tablet_separacao_real_order_items((int) $row['T005_Id']),
    ];
}

function tablet_separacao_document_type(string $flagAcp): string
{
    switch ($flagAcp) {
        case '1':
            return 'NOTA FISCAL';
        case '3':
            return 'ACP';
        case '5':
            return 'NOTA D1';
        case '2':
            return 'NAO FATURAR';
        case '6':
            return 'NF-e';
        case '7':
            return 'NFS-e';
        case '8':
            return 'NFC-e';
        case '4':
            return 'CUPOM FISCAL';
        default:
            return 'FALTA';
    }
}

function tablet_separacao_find_item(int $orderId, int $itemId): ?array
{
    $order = tablet_separacao_find_order($orderId);
    if (!$order) {
        return null;
    }

    return $order['items'][$itemId] ?? null;
}

function tablet_separacao_save_order(array $order): void
{
    if (tablet_separacao_real_mode()) {
        return;
    }
    $_SESSION[TABLET_SEPARACAO_SESSION]['orders'][$order['id']] = $order;
}

function tablet_separacao_authenticate(string $login, string $password)
{
    $login = trim($login);
    $password = trim($password);

    if ($login === '' || $password === '') {
        return 'Informe login e senha para entrar.';
    }

    $confUsuario = [];
    $sysLocal = [];
    $sys = [];

    require TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';
    require TABLET_SEPARACAO_ROOT . '/configuracoes/sysConfig.php';

    $conn = @mysqli_connect(
        (string) ($confUsuario['dbHost'] ?? ''),
        (string) ($confUsuario['dbUser'] ?? ''),
        (string) ($confUsuario['dbPass'] ?? ''),
        (string) ($confUsuario['dbDatabase'] ?? '')
    );

    if (!$conn) {
        return 'Não foi possível conectar ao banco do sistema.';
    }

    mysqli_set_charset($conn, 'utf8');
    mysqli_query($conn, 'SET NAMES utf8');

    $loginEsc = mysqli_real_escape_string($conn, $login);
    $sql = <<<SQL
SELECT
    C007.*,
    C019.C019_C004_Id AS company_id,
    C019.C019_C021_Id AS profile_id,
    C004.C004_Nome_Empresa,
    C004.C004_Nome_Abreviado,
    C004.C004_Caminho_Sistema
FROM C007
LEFT JOIN C019 ON C019.C019_C007_Id = C007.C007_Id
LEFT JOIN C004 ON C004.C004_Id = C019.C019_C004_Id
WHERE C007.C007_Login = '{$loginEsc}'
  AND C007.C007_Ativo = 'S'
  AND (C007.C007_D024_Id = 0 OR C007.C007_D024_Id IS NULL)
  AND C004.C004_Flag_Ativo = 'S'
ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id
LIMIT 1
SQL;

    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if (!$row) {
        mysqli_close($conn);
        return 'Login ou senha inválidos.';
    }

    $storedPassword = (string) ($row['C007_Senha'] ?? '');
    $userId = (int) ($row['C007_Id'] ?? 0);
    $usarSenhaRootPadrao = !in_array((string) ($_SERVER['HTTP_HOST'] ?? ''), ['hard.hardness.com.br', 'cha.chasistemas.com.br'], true);

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/segurancaSenha.php';
    $senhaValida = \hardness\SegurancaSenha::validarSenhaArmazenada($password, $storedPassword);
    if ($userId === 63 && $usarSenhaRootPadrao) {
        $md5Password = md5($password);
        $senhaValida = in_array($md5Password, [
            (string) ($sys['senhaRootSistema'] ?? ''),
            (string) ($sys['senhaRootMaster'] ?? ''),
            (string) ($sys['senhaRootSuporte'] ?? ''),
        ], true) || $senhaValida;
    }

    if (!$senhaValida) {
        mysqli_close($conn);
        return 'Login ou senha inválidos.';
    }

    if (($row['C007_Flag_Ausente'] ?? 'N') !== 'N' && strtolower($login) !== 'root') {
        mysqli_close($conn);
        return 'Acesso não permitido, o usuário está ausente.';
    }

    $dispositivo = (string) ($row['C007_Dispositivo_Acesso'] ?? '0');
    if (!in_array($dispositivo, ['0', '2', ''], true) && strtolower($login) !== 'root') {
        mysqli_close($conn);
        return 'Acesso não permitido por este dispositivo.';
    }

    $companyId = (int) ($row['company_id'] ?? 0);
    $profileId = (int) ($row['profile_id'] ?? 0);

    if ($companyId <= 0 || $profileId <= 0) {
        mysqli_close($conn);
        return 'Usuário sem empresa liberada para acesso.';
    }

    $row['_login'] = $login;
    $row['_database'] = (string) ($confUsuario['dbDatabase'] ?? '');
    mysqli_close($conn);
    return $row;
}

function tablet_separacao_start_authenticated_session(array $row, string $source): void
{
    session_regenerate_id(true);

    $login = (string) ($row['_login'] ?? $row['C007_Login'] ?? '');
    $userId = (int) ($row['C007_Id'] ?? 0);
    $profileId = (int) ($row['profile_id'] ?? 0);
    $companyId = (int) ($row['company_id'] ?? 0);

    $_SESSION['Usuario_Atual'] = $userId;
    $_SESSION['Nome_Usuario'] = (string) ($row['C007_Nome'] ?? $login);
    $_SESSION['Cargo_Usuario'] = (string) ($row['C007_Cargo'] ?? '');
    $_SESSION['Perfil_Usuario'] = $profileId;
    $_SESSION['Empresa_Atual'] = $companyId;
    $_SESSION['Empresa_Logada'] = $companyId;
    $_SESSION['Nome_Empresa_Atual'] = (string) ($row['C004_Nome_Empresa'] ?? '');
    $_SESSION['Banco_Dados'] = (string) ($row['_database'] ?? '');
    $_SESSION['Pasta_Empresa'] = (string) ($row['C004_Caminho_Sistema'] ?? '');
    $_SESSION['Login_Usuario'] = $login;
    $_SESSION['cBd_Configuracao'] = (string) ($row['_database'] ?? '');

    $_SESSION[TABLET_SEPARACAO_SESSION]['user'] = [
        'login' => $login,
        'name' => strtoupper((string) ($row['C007_Nome'] ?? $login)),
        'legacy_user_id' => $userId,
        'legacy_profile_id' => $profileId,
        'legacy_company_id' => $companyId,
        'source' => $source,
    ];
}

function tablet_separacao_login(string $login, string $password): ?string
{
    tablet_separacao_bootstrap();
    $authenticated = tablet_separacao_authenticate($login, $password);
    if (!is_array($authenticated)) {
        return $authenticated;
    }

    tablet_separacao_start_authenticated_session($authenticated, 'tablet_login');
    return null;
}

function tablet_separacao_qr_start_session(array $handoff): bool
{
    $database = (string) ($handoff['database'] ?? '');
    $userId = (int) ($handoff['user_id'] ?? 0);
    if ($database === '' || $userId <= 0) {
        return false;
    }

    $confUsuario = [];
    require TABLET_SEPARACAO_ROOT . '/bibliotecas/confUsuario.php';
    if ($database !== (string) ($confUsuario['dbDatabase'] ?? '')) {
        return false;
    }

    $conn = @mysqli_connect(
        (string) ($confUsuario['dbHost'] ?? ''),
        (string) ($confUsuario['dbUser'] ?? ''),
        (string) ($confUsuario['dbPass'] ?? ''),
        $database
    );
    if (!$conn) {
        return false;
    }
    mysqli_set_charset($conn, 'utf8');
    $sql = "SELECT C007.*, C019.C019_C004_Id AS company_id, C019.C019_C021_Id AS profile_id, C004.C004_Nome_Empresa, C004.C004_Caminho_Sistema FROM C007 INNER JOIN C019 ON C019.C019_C007_Id=C007.C007_Id INNER JOIN C004 ON C004.C004_Id=C019.C019_C004_Id WHERE C007.C007_Id='{$userId}' AND C007.C007_Ativo='S' AND C004.C004_Flag_Ativo='S' ORDER BY C004.C004_Ordem_Empresa, C004.C004_Id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_close($conn);
    if (!$row || (int) ($row['company_id'] ?? 0) <= 0 || (int) ($row['profile_id'] ?? 0) <= 0) {
        return false;
    }

    $row['_login'] = (string) ($row['C007_Login'] ?? '');
    $row['_database'] = $database;
    tablet_separacao_start_authenticated_session($row, 'qr_handoff');
    return true;
}

function tablet_separacao_logout(): void
{
    tablet_separacao_bootstrap();
    $_SESSION[TABLET_SEPARACAO_SESSION]['user'] = null;
    $_SESSION[TABLET_SEPARACAO_SESSION]['companies'] = [];
    $_SESSION[TABLET_SEPARACAO_SESSION]['active_company_id'] = null;
    $_SESSION[TABLET_SEPARACAO_SESSION]['active_profile_id'] = null;

    unset(
        $_SESSION['Usuario_Atual'],
        $_SESSION['Nome_Usuario'],
        $_SESSION['Cargo_Usuario'],
        $_SESSION['Perfil_Usuario'],
        $_SESSION['Empresa_Atual'],
        $_SESSION['Empresa_Logada'],
        $_SESSION['Nome_Empresa_Atual'],
        $_SESSION['Banco_Dados'],
        $_SESSION['Pasta_Empresa'],
        $_SESSION['Login_Usuario'],
        $_SESSION['cBd_Configuracao']
    );
}

function tablet_separacao_user(): ?array
{
    tablet_separacao_bootstrap();
    return $_SESSION[TABLET_SEPARACAO_SESSION]['user'];
}

function tablet_separacao_bridge_legacy_session(): void
{
    $currentUser = $_SESSION[TABLET_SEPARACAO_SESSION]['user'] ?? null;
    if ($currentUser !== null) {
        return;
    }

    $legacyUserId = $_SESSION['Usuario_Atual'] ?? null;
    if (empty($legacyUserId)) {
        return;
    }

    $legacyName = $_SESSION['Nome_Usuario'] ?? ('USUARIO ' . $legacyUserId);
    $legacyLogin = $_SESSION['Login_Usuario'] ?? $legacyName;

    $_SESSION[TABLET_SEPARACAO_SESSION]['user'] = [
        'login' => (string) $legacyLogin,
        'name' => strtoupper((string) $legacyName),
        'legacy_user_id' => (int) $legacyUserId,
        'legacy_profile_id' => isset($_SESSION['Perfil_Usuario']) ? (int) $_SESSION['Perfil_Usuario'] : null,
        'legacy_company_id' => isset($_SESSION['Empresa_Atual']) ? (int) $_SESSION['Empresa_Atual'] : null,
        'source' => 'legacy_session',
    ];
}

function tablet_separacao_require_auth(): void
{
    if (tablet_separacao_user() === null) {
        header('Location: ' . app_base_path('login.php'));
        exit;
    }
}

function tablet_separacao_pending_items(array $order): array
{
    return array_filter(
        $order['items'],
        static fn(array $item): bool => (float) $item['separated_qty'] < (float) $item['required_qty']
    );
}

function tablet_separacao_order_progress(array $order): array
{
    $total = count($order['items']);
    $done = 0;

    foreach ($order['items'] as $item) {
        if ((float) $item['separated_qty'] >= (float) $item['required_qty']) {
            $done++;
        }
    }

    return [$done, $total];
}

function tablet_separacao_order_progress_percent(array $order): float
{
    $required = 0.0;
    $separated = 0.0;

    foreach ($order['items'] as $item) {
        $required += (float) $item['required_qty'];
        $separated += min((float) $item['separated_qty'], (float) $item['required_qty']);
    }

    if ($required <= 0) {
        return 0.0;
    }

    return round(($separated / $required) * 100, 1);
}

function tablet_separacao_format_qty(float $value): string
{
    return number_format($value, 4, ',', '.');
}

function tablet_separacao_format_city_uf(array $order): string
{
    $neighborhood = trim((string) ($order['neighborhood'] ?? ''));
    $city = trim((string) ($order['city'] ?? ''));
    $uf = trim((string) ($order['uf'] ?? ''));
    $cityUf = trim($city . ($city !== '' && $uf !== '' ? '/' : '') . $uf);

    if ($neighborhood !== '' && $cityUf !== '') {
        return 'Bairro: ' . $neighborhood . ' - ' . $cityUf;
    }

    if ($neighborhood !== '') {
        return 'Bairro: ' . $neighborhood;
    }

    return $cityUf !== '' ? $cityUf : '-';
}

function tablet_separacao_delivery_status(?string $date): array
{
    if (empty($date) || $date === '0000-00-00') {
        return [
            'label' => '-',
            'class' => 'deadline-badge--neutral',
            'title' => 'Prazo não informado',
            'filter' => 'all',
        ];
    }

    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return [
            'label' => '-',
            'class' => 'deadline-badge--neutral',
            'title' => 'Prazo inválido',
            'filter' => 'all',
        ];
    }

    $today = strtotime(date('Y-m-d'));
    $delivery = strtotime(date('Y-m-d', $timestamp));
    $days = (int) round(($delivery - $today) / 86400);

    if ($days < 0) {
        return [
            'label' => date('d/m/Y', $timestamp),
            'class' => 'deadline-badge--late',
            'title' => 'Prazo atrasado',
            'filter' => 'late',
        ];
    }

    if ($days <= 1) {
        return [
            'label' => date('d/m/Y', $timestamp),
            'class' => 'deadline-badge--soon',
            'title' => 'Prazo próximo',
            'filter' => 'soon',
        ];
    }

    return [
        'label' => date('d/m/Y', $timestamp),
        'class' => 'deadline-badge--ok',
        'title' => 'Prazo dentro do esperado',
        'filter' => 'ok',
    ];
}

function tablet_separacao_item_percent(array $item): float
{
    $required = (float) $item['required_qty'];
    $separated = min((float) $item['separated_qty'], $required);

    if ($required <= 0) {
        return 0.0;
    }

    return round(($separated / $required) * 100, 1);
}

function tablet_separacao_set_flash(string $type, string $message): void
{
    $_SESSION[TABLET_SEPARACAO_SESSION]['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function tablet_separacao_pull_flash(): ?array
{
    $flash = $_SESSION[TABLET_SEPARACAO_SESSION]['flash'] ?? null;
    unset($_SESSION[TABLET_SEPARACAO_SESSION]['flash']);
    return $flash;
}

function tablet_separacao_resolve_location(array $item, string $barcode): ?array
{
    if (tablet_separacao_real_mode()) {
        return tablet_separacao_resolve_location_real($item, $barcode);
    }

    $normalized = trim($barcode);
    if ($normalized === '') {
        return null;
    }

    foreach (($item['locations'] ?? []) as $location) {
        if ((string) ($location['t066_id'] ?? '') !== '' && strcasecmp((string) $location['t066_id'], $normalized) === 0) {
            return tablet_separacao_location_payload($location, (string) ($location['barcode'] ?? ''));
        }
    }

    foreach (($item['locations'] ?? []) as $location) {
        foreach (['barcode', 'address'] as $field) {
            if (strcasecmp((string) ($location[$field] ?? ''), $normalized) === 0) {
                return tablet_separacao_location_payload($location, (string) ($location['barcode'] ?? ''));
            }
        }
    }

    return null;
}

function tablet_separacao_location_payload(array $location, string $barcode): array
{
    return [
        'label' => (string) ($location['label'] ?? ''),
        'barcode' => $barcode,
        'address' => (string) ($location['address'] ?? ''),
        't066_id' => (int) ($location['t066_id'] ?? 0),
        'd004_id' => (int) ($location['d004_id'] ?? 0),
        'stock_qty' => (float) ($location['stock_qty'] ?? 0),
    ];
}

function tablet_separacao_resolve_location_real(array $item, string $barcode): ?array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return null;
    }

    $barcode = trim($barcode);
    if ($barcode === '') {
        return null;
    }

    $d009Id = (int) ($item['d009_id'] ?? 0);
    if ($d009Id <= 0) {
        return null;
    }

    $sql = <<<SQL
SELECT
    T066_Id,
    T066_Quantidade_Estoque,
    D004_Id,
    D004_Local
FROM T066
LEFT JOIN D004 ON D004_Id=T066_D004_Id
WHERE T066_D009_Id='{$d009Id}'
ORDER BY IF(T066_Id='{$item['default_t066_id']}',1,0) DESC, T066_Id ASC
SQL;

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    $separatedByT066 = [];
    foreach (($item['separations'] ?? []) as $separation) {
        $separatedT066Id = (int) ($separation['t066_id'] ?? 0);
        if ($separatedT066Id <= 0) {
            continue;
        }
        $separatedByT066[$separatedT066Id] = ($separatedByT066[$separatedT066Id] ?? 0)
            + (float) ($separation['quantity'] ?? 0);
    }

    $suggestedT066Ids = [];
    $suggestedT066Pending = [];
    foreach (($item['suggested_locations'] ?? []) as $suggestedLocation) {
        $suggestedT066Id = (int) ($suggestedLocation['t066_id'] ?? 0);
        if ($suggestedT066Id > 0) {
            $suggestedT066Ids[$suggestedT066Id] = true;
            $suggestedQuantity = (float) ($suggestedLocation['suggested_qty'] ?? 0);
            $suggestedT066Pending[$suggestedT066Id] = $suggestedQuantity
                - (float) ($separatedByT066[$suggestedT066Id] ?? 0);
        }
    }

    if ($suggestedT066Ids) {
        usort($rows, static function (array $a, array $b) use ($suggestedT066Ids, $suggestedT066Pending): int {
            $aId = (int) ($a['T066_Id'] ?? 0);
            $bId = (int) ($b['T066_Id'] ?? 0);
            $aPending = ($suggestedT066Pending[$aId] ?? 0) > 0 ? 1 : 0;
            $bPending = ($suggestedT066Pending[$bId] ?? 0) > 0 ? 1 : 0;
            if ($aPending !== $bPending) {
                return $bPending <=> $aPending;
            }

            $aSuggested = isset($suggestedT066Ids[$aId]) ? 1 : 0;
            $bSuggested = isset($suggestedT066Ids[$bId]) ? 1 : 0;
            if ($aSuggested !== $bSuggested) {
                return $bSuggested <=> $aSuggested;
            }

            return $aId <=> $bId;
        });
    }

    foreach ($rows as $row) {
        if (strcasecmp((string) ($row['T066_Id'] ?? ''), $barcode) === 0) {
            return tablet_separacao_real_location_payload($row, (string) ($row['T066_Id'] ?? ''));
        }
    }

    $field = tablet_separacao_location_identifier_field();
    foreach ($rows as $row) {
        foreach (array_unique([$field, 'D004_Local']) as $candidateField) {
            $candidate = trim((string) ($row[$candidateField] ?? ''));
            if ($candidate !== '' && strcasecmp($candidate, $barcode) === 0) {
                return tablet_separacao_real_location_payload($row, $candidate);
            }
        }
    }

    return null;
}

function tablet_separacao_real_location_payload(array $row, string $barcode): array
{
    $address = (string) ($row['D004_Local'] ?? '');

    return [
        'label' => $address !== '' ? $address : ('T066 ' . (int) $row['T066_Id']),
        'barcode' => $barcode,
        'address' => $address,
        't066_id' => (int) $row['T066_Id'],
        'd004_id' => (int) ($row['D004_Id'] ?? 0),
        'stock_qty' => (float) ($row['T066_Quantidade_Estoque'] ?? 0),
    ];
}

function tablet_separacao_register_item_separation(array $order, int $itemId, array $location, float $quantity): array
{
    if (tablet_separacao_real_mode()) {
        return tablet_separacao_register_item_separation_real($order, $itemId, $location, $quantity);
    }

    $item = $order['items'][$itemId];
    $item['separated_qty'] = (float) $item['separated_qty'] + $quantity;
    $item['last_scan'] = $location['barcode'];

    if (!isset($item['separations']) || !is_array($item['separations'])) {
        $item['separations'] = [];
    }

    $merged = false;
    foreach ($item['separations'] as &$separation) {
        if (($separation['location_barcode'] ?? '') === $location['barcode']) {
            $separation['quantity'] = (float) $separation['quantity'] + $quantity;
            $merged = true;
            break;
        }
    }
    unset($separation);

    if (!$merged) {
        $item['separations'][] = [
            'key' => 'mock-' . $itemId . '-' . count($item['separations']),
            'location_label' => $location['label'],
            'location_barcode' => $location['barcode'],
            'address' => $location['address'],
            'quantity' => $quantity,
        ];
    }

    $order['items'][$itemId] = $item;
    return $order;
}

function tablet_separacao_register_item_separation_real(array $order, int $itemId, array $location, float $quantity): array
{
    $conn = tablet_separacao_db();
    if (!$conn) {
        return $order;
    }

    $item = $order['items'][$itemId];
    $remaining = (float) $item['required_qty'] - (float) $item['separated_qty'];

    if ($quantity <= 0 || $quantity > $remaining) {
        return $order;
    }

    $orderId = (int) $order['id'];
    $t066Id = (int) ($location['t066_id'] ?? 0);
    $d004Id = (int) ($location['d004_id'] ?? 0);
    $quantitySql = str_replace(',', '.', (string) $quantity);

    mysqli_begin_transaction($conn);
    try {
        $sqlInsert = "INSERT INTO T238 (T238_T005_Id, T238_T006_Id, T238_T066_Id, T238_D004_Id, T238_Quantidade_Separacao) VALUES ('{$orderId}', '{$itemId}', '{$t066Id}', '{$d004Id}', '{$quantitySql}')";
        if (!mysqli_query($conn, $sqlInsert)) {
            throw new \RuntimeException(mysqli_error($conn));
        }

        $sqlUpdate = <<<SQL
UPDATE T006
   SET T006_Quantidade_Separacao = IFNULL((SELECT SUM(T238_Quantidade_Separacao)
                                             FROM T238
                                            WHERE T238_T006_Id='{$itemId}'), 0)
 WHERE T006_Id='{$itemId}'
LIMIT 1
SQL;
        if (!mysqli_query($conn, $sqlUpdate)) {
            throw new \RuntimeException(mysqli_error($conn));
        }

        mysqli_commit($conn);
    } catch (\Throwable $e) {
        mysqli_rollback($conn);
        tablet_separacao_set_flash('error', 'Não foi possível gravar a separação real: ' . $e->getMessage());
        return $order;
    }

    return tablet_separacao_find_order($orderId) ?? $order;
}

function tablet_separacao_finalize_order(int $orderId, string $box): array
{
    if (!tablet_separacao_real_mode()) {
        $order = tablet_separacao_find_order($orderId);
        if (!$order) {
            return ['ok' => false, 'message' => 'Pedido não encontrado.'];
        }
        $order['box'] = $box;
        $order['status'] = 'Aguardando finalização backend';
        tablet_separacao_save_order($order);
        return ['ok' => true, 'message' => 'Pedido preparado para finalizar.'];
    }

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/GFormWrap.php';
    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/EST002.php';

    $form = new \hardness\GFormWrap_([
        'T005_Id' => $orderId,
        'T005_Estoque_Box' => $box,
    ]);

    $est002 = new \hardness\EST002();
    $result = $est002->SeparacaoPedido($form);

    if ($result === true) {
        return ['ok' => true, 'message' => 'Pedido finalizado com sucesso.'];
    }

    $personalizedResult = tablet_separacao_personalize_finalize_result($result);
    if ($personalizedResult !== null) {
        return $personalizedResult;
    }

    return ['ok' => false, 'message' => (string) $result];
}

function tablet_separacao_return_order_to_seller(int $orderId): array
{
    if (!tablet_separacao_real_mode()) {
        $order = tablet_separacao_find_order($orderId);
        if (!$order) {
            return ['ok' => false, 'message' => 'Pedido não encontrado.'];
        }

        $order['status'] = 'Iniciar separação';
        $order['status_flag'] = '1';
        $order['box'] = '';
        tablet_separacao_save_order($order);

        return ['ok' => true, 'message' => 'Pedido retornado para o vendedor.'];
    }

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/VEN012.php';

    $ven012 = new \hardness\VEN012();
    $result = $ven012->retornarPedido($orderId);

    if ($result === true) {
        return ['ok' => true, 'message' => 'Pedido retornado para o vendedor.'];
    }

    return ['ok' => false, 'message' => (string) $result];
}

function tablet_separacao_delete_item_separation(int $orderId, int $itemId, string $separationKey): array
{
    if (!tablet_separacao_real_mode()) {
        $order = tablet_separacao_find_order($orderId);
        if (!$order || empty($order['items'][$itemId])) {
            return ['ok' => false, 'message' => 'Item nao encontrado.'];
        }

        $item = $order['items'][$itemId];
        if (empty($item['separations']) || !is_array($item['separations'])) {
            return ['ok' => false, 'message' => 'Nenhuma separação encontrada para excluir.'];
        }

        $removed = null;
        foreach ($item['separations'] as $index => $separation) {
            if ((string) ($separation['key'] ?? '') === $separationKey) {
                $removed = $separation;
                unset($item['separations'][$index]);
                break;
            }
        }

        if ($removed === null) {
            return ['ok' => false, 'message' => 'Separação não encontrada.'];
        }

        $item['separations'] = array_values($item['separations']);
        $item['separated_qty'] = max(0, (float) $item['separated_qty'] - (float) $removed['quantity']);
        $item['last_scan'] = !empty($item['separations']) ? ($item['separations'][count($item['separations']) - 1]['location_barcode'] ?? null) : null;
        $order['items'][$itemId] = $item;
        tablet_separacao_save_order($order);

        return ['ok' => true, 'message' => 'Separação removida com sucesso.'];
    }

    $conn = tablet_separacao_db();
    if (!$conn) {
        return ['ok' => false, 'message' => 'Conexão indisponível.'];
    }

    $orderId = (int) $orderId;
    $itemId = (int) $itemId;
    $t238Id = (int) $separationKey;

    if ($t238Id <= 0) {
        return ['ok' => false, 'message' => 'Identificador da separação inválido.'];
    }

    $sql = "SELECT T238_Quantidade_Separacao FROM T238 WHERE T238_Id='{$t238Id}' AND T238_T005_Id='{$orderId}' AND T238_T006_Id='{$itemId}' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $row = $result ? mysqli_fetch_assoc($result) : null;

    if (!$row) {
        return ['ok' => false, 'message' => 'Separação não encontrada.'];
    }

    mysqli_begin_transaction($conn);
    try {
        if (!mysqli_query($conn, "DELETE FROM T238 WHERE T238_Id='{$t238Id}' LIMIT 1")) {
            throw new \RuntimeException(mysqli_error($conn));
        }

        $sqlUpdate = <<<SQL
UPDATE T006
   SET T006_Quantidade_Separacao = IFNULL((SELECT SUM(T238_Quantidade_Separacao)
                                             FROM T238
                                            WHERE T238_T006_Id='{$itemId}'), 0)
 WHERE T006_Id='{$itemId}'
LIMIT 1
SQL;
        if (!mysqli_query($conn, $sqlUpdate)) {
            throw new \RuntimeException(mysqli_error($conn));
        }

        mysqli_commit($conn);
    } catch (\Throwable $e) {
        mysqli_rollback($conn);
        return ['ok' => false, 'message' => 'Não foi possível excluir a separação: ' . $e->getMessage()];
    }

    return ['ok' => true, 'message' => 'Separação removida com sucesso.'];
}

function tablet_separacao_start_order(int $orderId): array
{
    if (!tablet_separacao_real_mode()) {
        $order = tablet_separacao_find_order($orderId);
        if (!$order) {
            return ['ok' => false, 'message' => 'Pedido não encontrado.'];
        }
        $order['status'] = 'Separando';
        $order['status_flag'] = '2';
        tablet_separacao_save_order($order);
        return ['ok' => true, 'message' => 'Pedido iniciado para separação.'];
    }

    $conn = tablet_separacao_db();
    if (!$conn) {
        return ['ok' => false, 'message' => 'Conexão indisponível.'];
    }

    $orderId = (int) $orderId;
    $companyId = tablet_separacao_company_id();
    $sqlStatus = "SELECT T005_Flag_Status, T005_Nome_Status FROM T005 WHERE T005_Id='{$orderId}' AND T005_C004_Id='{$companyId}' LIMIT 1";
    $resStatus = mysqli_query($conn, $sqlStatus);
    $rowStatus = $resStatus ? mysqli_fetch_assoc($resStatus) : null;

    if (!$rowStatus) {
        return ['ok' => false, 'message' => 'Pedido não encontrado para a empresa ativa.'];
    }

    if ((string) $rowStatus['T005_Flag_Status'] !== '1') {
        return ['ok' => false, 'message' => "Pedido já está com o status {$rowStatus['T005_Nome_Status']}."];
    }

    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/GFormWrap.php';
    require_once TABLET_SEPARACAO_ROOT . '/bibliotecas/classes/EST002.php';

    $form = new \hardness\GFormWrap_([
        'impressao_id' => (string) $orderId,
    ]);

    $est002 = new \hardness\EST002();
    $result = $est002->ImprimirPedidoUsuarioAutenticado($form);

    if ($result === true) {
        return ['ok' => true, 'message' => 'Pedido iniciado para separação.'];
    }

    return ['ok' => false, 'message' => (string) $result];
}
