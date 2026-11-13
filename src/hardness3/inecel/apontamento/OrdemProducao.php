<?php
namespace hardness;

header("X-Robots-Tag: noindex, nofollow", true);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit;
}

if (empty($_SESSION['Usuario_Atual'])) {
    header('Location: ./index.php');
    exit;
}

$g = array(
    'usuarioAtual' => $_SESSION['Usuario_Atual'],
    'empresaAtual' => isset($_SESSION['Empresa_Atual']) ? $_SESSION['Empresa_Atual'] : null,
);

require_once('../../bibliotecas/confUsuario.php');
include_once('../../configuracoes/sysConfig.php');
require_once('../../bibliotecas/conexaoBanco.php');
require_once('../../bibliotecas/funcoesGerais.php');

function ordemEscapar($valor)
{
    global $g;
    return mysqli_real_escape_string(trim((string) $valor), $g['conexaoBanco']);
}

function ordemNumero($valor): string
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return '0';
    }

    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);

    return is_numeric($valor) ? (string) $valor : '0';
}

function ordemBuscarLinhas($sql)
{
    $resultado = mysqli_query($sql);
    $linhas = array();

    if ($resultado) {
        while ($linha = mysql_fetch_assoc($resultado)) {
            $linhas[] = $linha;
        }
    }

    return $linhas;
}

function ordemFlash(?string $tipo = null, ?string $mensagem = null): array
{
    if ($tipo !== null && $mensagem !== null) {
        $_SESSION['ordem_producao_flash'] = array(
            'tipo' => $tipo,
            'mensagem' => $mensagem,
        );

        return $_SESSION['ordem_producao_flash'];
    }

    if (!empty($_SESSION['ordem_producao_flash']) && is_array($_SESSION['ordem_producao_flash'])) {
        $flash = $_SESSION['ordem_producao_flash'];
        unset($_SESSION['ordem_producao_flash']);
        return $flash;
    }

    return array();
}

function ordemCompanyRows(): array
{
    global $g;

    $usuarioId = (int) ($g['usuarioAtual'] ?? 0);
    if ($usuarioId <= 0) {
        return array();
    }

    $sql = "
        SELECT
            C019.C019_C004_Id,
            C019.C019_C021_Id,
            C019.C019_Ordem_Empresa,
            C004.C004_Nome_Empresa,
            C004.C004_Nome_Abreviado,
            C004.C004_Caminho_Sistema,
            C004.C004_Banco_Configuracao
        FROM C019
        INNER JOIN C004 ON C004.C004_Id = C019.C019_C004_Id
        WHERE C019.C019_C007_Id = '{$usuarioId}'
          AND C004.C004_Flag_Ativo = 'S'
        ORDER BY
            CASE
                WHEN IFNULL(C019.C019_Ordem_Empresa, 0) > 0 THEN 0
                ELSE 1
            END ASC,
            CASE
                WHEN IFNULL(C019.C019_Ordem_Empresa, 0) > 0 THEN C019.C019_Ordem_Empresa
                ELSE C004.C004_Ordem_Empresa
            END ASC,
            C004.C004_Id ASC
    ";

    $resultado = mysqli_query($sql);
    $linhas = array();

    if ($resultado) {
        while ($linha = mysql_fetch_assoc($resultado)) {
            $linhas[] = $linha;
        }
    }

    return $linhas;
}

function ordemApplyCompanySession(array $company): void
{
    $_SESSION['Perfil_Usuario'] = $company['C019_C021_Id'];
    $_SESSION['Empresa_Atual'] = $company['C019_C004_Id'];
    $_SESSION['Empresa_Logada'] = $company['C019_C004_Id'];
    $_SESSION['Nome_Empresa_Atual'] = $company['C004_Nome_Empresa'];
    $_SESSION['Pasta_Empresa'] = $company['C004_Caminho_Sistema'];
    $_SESSION['cBd_Configuracao'] = $company['C004_Banco_Configuracao'];
}

function ordemSwitchCompany(int $empresaId, array $companies): bool
{
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaId) {
            ordemApplyCompanySession($company);
            return true;
        }
    }

    return false;
}

function ordemSelectedCompanyName(array $companies): string
{
    $empresaAtual = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaAtual) {
            return (string) ($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa']);
        }
    }

    return (string) ($_SESSION['Nome_Empresa_Atual'] ?? 'Empresa');
}

function ordemCompanyTheme(int $empresaId): array
{
    $themes = array(
        array('bg' => '#eef6ff', 'panel' => '#ffffff', 'line' => '#c9dffc', 'primary' => '#155eef', 'accent' => '#dceaff'),
        array('bg' => '#f3fbf5', 'panel' => '#ffffff', 'line' => '#cfe8d5', 'primary' => '#168a4a', 'accent' => '#dff5e6'),
        array('bg' => '#fff8ef', 'panel' => '#ffffff', 'line' => '#f0dac0', 'primary' => '#b35a00', 'accent' => '#ffe8cf'),
        array('bg' => '#fff1f3', 'panel' => '#ffffff', 'line' => '#f1ccd4', 'primary' => '#c23d5a', 'accent' => '#ffdfe5'),
        array('bg' => '#f5f3ff', 'panel' => '#ffffff', 'line' => '#ddd7fb', 'primary' => '#6941c6', 'accent' => '#ebe5ff'),
        array('bg' => '#eefaf9', 'panel' => '#ffffff', 'line' => '#c8e9e4', 'primary' => '#0f8b8d', 'accent' => '#d7f4ef'),
    );

    return $themes[$empresaId % count($themes)];
}

$companies = ordemCompanyRows();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['empresa_id'])) {
    $empresaId = (int) $_POST['empresa_id'];
    if (ordemSwitchCompany($empresaId, $companies)) {
        header('Location: ./OrdemProducao.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_medidas_op') {
    $opMedidasId = isset($_POST['op_id']) ? (int) $_POST['op_id'] : 0;
    $buscaRedirect = isset($_POST['q']) ? trim((string) $_POST['q']) : '';

    if ($opMedidasId > 0) {
        $pesoEtiqueta = ordemNumero($_POST['peso_etiqueta'] ?? '');
        $larguraTrena = ordemNumero($_POST['largura_trena'] ?? '');
        $pesoEntrada = ordemNumero($_POST['peso_entrada'] ?? '');
        $pesoTubo = ordemNumero($_POST['peso_tubo'] ?? '');
        $pesoRefilo = ordemNumero($_POST['peso_refilo'] ?? '');
        $pesoSaida = ordemNumero($_POST['peso_saida'] ?? '');

        $sqlSalvarMedidas = "
            INSERT INTO T255A (
                T255A_T255_Id,
                T255A_Peso_Etiqueta,
                T255A_Largura_Trena,
                T255A_Peso_Entrada,
                T255A_Peso_Tubo,
                T255A_Peso_Refilo,
                T255A_Peso_Saida
            ) VALUES (
                '{$opMedidasId}',
                '{$pesoEtiqueta}',
                '{$larguraTrena}',
                '{$pesoEntrada}',
                '{$pesoTubo}',
                '{$pesoRefilo}',
                '{$pesoSaida}'
            )
            ON DUPLICATE KEY UPDATE
                T255A_Peso_Etiqueta = VALUES(T255A_Peso_Etiqueta),
                T255A_Largura_Trena = VALUES(T255A_Largura_Trena),
                T255A_Peso_Entrada = VALUES(T255A_Peso_Entrada),
                T255A_Peso_Tubo = VALUES(T255A_Peso_Tubo),
                T255A_Peso_Refilo = VALUES(T255A_Peso_Refilo),
                T255A_Peso_Saida = VALUES(T255A_Peso_Saida)
        ";

        if (mysqli_query($sqlSalvarMedidas)) {
            ordemFlash('sucesso', 'Medidas da OP salvas com sucesso.');
        } else {
            ordemFlash('erro', 'Nao foi possivel salvar as medidas da OP.');
        }
    } else {
        ordemFlash('erro', 'OP invalida para salvar as medidas.');
    }

    $params = array();
    if ($buscaRedirect !== '') {
        $params[] = 'q=' . urlencode($buscaRedirect);
    }
    $params[] = 'op_medidas=' . $opMedidasId;
    header('Location: ./OrdemProducao.php?' . implode('&', $params));
    exit;
}

$busca = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$opMedidasAberta = isset($_GET['op_medidas']) ? (int) $_GET['op_medidas'] : 0;
$where = array();
$where[] = "T255_C004_Id = '" . (int) $g['empresaAtual'] . "'";
$where[] = "T255_Flag_Status != '99'";
if ($busca !== '') {
    $buscaSql = ordemEscapar($busca);
    $where[] = "T255_Id LIKE '%{$buscaSql}%'";
}

$sqlOrdens = "
    SELECT
        T255_Id,
        T255_Data_Emissao,
        C007_Primeiro_Nome,
        IFNULL(T255_Total_Itens, 0) AS T255_Total_Itens,
        IFNULL(T255A.T255A_Peso_Etiqueta, 0) AS T255A_Peso_Etiqueta,
        IFNULL(T255A.T255A_Largura_Trena, 0) AS T255A_Largura_Trena,
        IFNULL(T255A.T255A_Peso_Entrada, 0) AS T255A_Peso_Entrada,
        IFNULL(T255A.T255A_Peso_Tubo, 0) AS T255A_Peso_Tubo,
        IFNULL(T255A.T255A_Peso_Refilo, 0) AS T255A_Peso_Refilo,
        IFNULL(T255A.T255A_Peso_Saida, 0) AS T255A_Peso_Saida,
        T255_Flag_Status,
        IF(T255_Flag_Status = '99', 'Finalizado', 'Aberto') AS Status_Descricao,
        COUNT(DISTINCT CASE
            WHEN COALESCE(T256.T256_Quantidade_Saldo, IFNULL(T256.T256_Quantidade, 0) - IFNULL(T256.T256_Quantidade_Produzida, 0)) > 0 THEN T256.T256_Id
            ELSE NULL
        END) AS Itens_Com_Saldo
    FROM T255
    LEFT JOIN C007 ON C007_Id = T255_C007_Id
    LEFT JOIN T255A ON T255A.T255A_T255_Id = T255_Id
    LEFT JOIN T256 ON T256.T256_T255_Id = T255_Id
    WHERE " . implode(' AND ', $where) . "
    GROUP BY T255_Id
    ORDER BY T255_Id DESC
    LIMIT 250
";

$ordens = ordemBuscarLinhas($sqlOrdens);
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$empresaCurta = ordemSelectedCompanyName($companies);
$companyTheme = ordemCompanyTheme($empresaAtualId);
$flash = ordemFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ordem Produ&ccedil;&atilde;o</title>
    <style>
        :root{--bg:<?php echo htmlspecialchars($companyTheme['bg'], ENT_QUOTES, 'UTF-8'); ?>;--card:<?php echo htmlspecialchars($companyTheme['panel'], ENT_QUOTES, 'UTF-8'); ?>;--text:#1f3350;--muted:#677b93;--line:<?php echo htmlspecialchars($companyTheme['line'], ENT_QUOTES, 'UTF-8'); ?>;--primary:<?php echo htmlspecialchars($companyTheme['primary'], ENT_QUOTES, 'UTF-8'); ?>;--primary-dark:#0b4f9c;--shadow:0 20px 45px rgba(18,49,86,.11);--warning-bg:#fff5dd;--warning-text:#9d6a10}*{box-sizing:border-box}body{margin:0;font-family:"Segoe UI",Arial,sans-serif;color:var(--text);background:radial-gradient(circle at top left,rgba(255,255,255,.92),transparent 25%),linear-gradient(180deg,#f8fbfd 0%,var(--bg) 100%)}.page{max-width:1280px;margin:0 auto;padding:28px 24px 40px}.topbar{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:18px;flex-wrap:wrap}.title h1{margin:0;font-size:34px;line-height:1.05}.title p{margin:8px 0 0;color:var(--muted)}.userbox{min-width:300px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:16px 18px;box-shadow:var(--shadow)}.userbox strong,.userbox span{display:block}.userbox span{color:var(--muted);font-size:14px;margin-top:3px}.company-switch{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:10px}.company-switch select{min-height:40px;border:1px solid var(--line);border-radius:12px;padding:0 12px;background:#fbfdff;color:var(--text)}.company-chip{display:inline-flex;align-items:center;min-height:40px;padding:0 12px;border-radius:999px;background:var(--accent);color:var(--primary);font-size:12px;font-weight:700}.filters{padding:18px;display:grid;grid-template-columns:minmax(280px,1fr) auto auto;gap:12px;align-items:end;margin-bottom:18px;background:var(--card);border:1px solid var(--line);border-radius:22px;box-shadow:var(--shadow)}.field label{display:block;margin-bottom:8px;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)}.field input{width:100%;border:1px solid var(--line);border-radius:14px;padding:12px 14px;background:#fbfdff;font-size:15px;color:var(--text)}.button{border:0;border-radius:14px;padding:12px 18px;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.button.primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff}.button.ghost{background:#fff;color:var(--text);border:1px solid var(--line)}.button.secondary{background:#fff;color:var(--primary);border:1px solid var(--line)}.flash{margin:0 0 18px;padding:12px 16px;border-radius:14px;font-size:14px;border:1px solid transparent}.flash.sucesso{background:#ebfaf1;color:#216f4b;border-color:#b9e7cc}.flash.erro{background:#fff0f0;color:#a53a3a;border-color:#f0c4c4}.grid-wrap{background:var(--card);border:1px solid var(--line);border-radius:22px;overflow:hidden;box-shadow:var(--shadow)}.grid-header{padding:18px 22px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fbfdff 0%,#f4f8fc 100%)}.grid-header h2{margin:0;font-size:22px}.grid-header p{margin:5px 0 0;color:var(--muted)}.table-scroll{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:980px}th,td{padding:15px 16px;border-bottom:1px solid #e7edf3;text-align:left;vertical-align:top;font-size:14px}th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;background:#f9fbfd}td.center,th.center{text-align:center}tr:hover td{background:#fbfdff}.op-link{color:var(--primary);font-weight:700;text-decoration:none}.badge{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:7px 11px;font-size:12px;font-weight:700;background:var(--warning-bg);color:var(--warning-text)}.empty{padding:44px 24px;text-align:center;color:var(--muted)}.row-actions{display:flex;gap:8px;justify-content:center;flex-wrap:wrap}.op-extra-row td{background:#f9fbfd;padding:18px 16px}.op-extra-card{border:1px solid var(--line);border-radius:18px;background:#fff;padding:18px;box-shadow:0 10px 26px rgba(18,49,86,.06)}.op-extra-card h3{margin:0 0 14px;font-size:18px}.measure-grid{display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:12px}.measure-field label{display:block;margin-bottom:8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--muted)}.measure-field input{width:100%;border:1px solid var(--line);border-radius:12px;padding:11px 12px;background:#fbfdff;font-size:14px;color:var(--text)}.measure-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px;flex-wrap:wrap}@media (max-width:980px){.filters{grid-template-columns:1fr}.measure-grid{grid-template-columns:1fr 1fr}}@media (max-width:720px){.topbar{flex-direction:column}.userbox{min-width:100%}.measure-grid{grid-template-columns:1fr}.row-actions{flex-direction:column}}
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div class="title">
            <h1>Ordem Produ&ccedil;&atilde;o</h1>
            <p>Grade inicial de OPs abertas para apontamento.</p>
        </div>
        <div class="userbox">
            <strong><?php echo htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8'); ?></strong>
            <span>Empresa: <?php echo htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if ($companies): ?>
                <div class="company-switch">
                    <span class="company-chip"><?php echo htmlspecialchars($empresaCurta, ENT_QUOTES, 'UTF-8'); ?></span>
                    <form method="post">
                        <select name="empresa_id" onchange="this.form.submit()">
                            <?php foreach ($companies as $company): ?>
                                <?php $companyId = (int) $company['C019_C004_Id']; ?>
                                <option value="<?php echo $companyId; ?>" <?php echo $companyId === $empresaAtualId ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <a class="button ghost" href="./OrdemProducao.php?logout=1">Sair</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="flash <?php echo htmlspecialchars($flash['tipo'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($flash['mensagem'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form class="filters" method="get" action="./OrdemProducao.php">
        <div class="field">
            <label for="q">Buscar OP</label>
            <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ex.: 1254" autofocus inputmode="none">
        </div>
        <a class="button ghost" href="./OrdemProducao.php">Limpar</a>
        <button class="button primary" type="submit">Filtrar</button>
    </form>

    <section class="grid-wrap">
        <div class="grid-header">
            <h2>Ordens abertas</h2>
        </div>
        <?php if (empty($ordens)): ?>
            <div class="empty">Nenhuma ordem de produ&ccedil;&atilde;o aberta encontrada com os filtros atuais.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th class="center">OP</th>
                            <th>Emissao</th>
                            <th>Nome</th>
                            <th>Status</th>
                            <th class="center">Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordens as $ordem): ?>
                            <?php
                                $ordemId = (int) $ordem['T255_Id'];
                                $link = './itensOp.php?op_id=' . $ordemId;
                                $linkMedidas = './OrdemProducao.php?op_medidas=' . $ordemId . ($busca !== '' ? '&q=' . urlencode($busca) : '');
                            ?>
                            <tr>
                                <td class="center"><a class="op-link" href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $ordemId; ?></a></td>
                                <td><?php echo !empty($ordem['T255_Data_Emissao']) ? htmlspecialchars(gCorrigeData($ordem['T255_Data_Emissao']), ENT_QUOTES, 'UTF-8') : '-'; ?></td>
                                <td><?php echo htmlspecialchars($ordem['C007_Primeiro_Nome'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars($ordem['Status_Descricao'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td class="center">
                                    <div class="row-actions">
                                        <a class="button primary" href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">Abrir itens</a>
                                        <a class="button secondary" href="<?php echo htmlspecialchars($linkMedidas, ENT_QUOTES, 'UTF-8'); ?>">Pesos</a>
                                        <a class="button secondary" href="javascript:void(0)" onclick="imprimirOrdemProducao(<?php echo $ordemId; ?>)">Impressão OP</a>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($opMedidasAberta === $ordemId): ?>
                                <tr class="op-extra-row">
                                    <td colspan="5">
                                        <div class="op-extra-card">
                                            <h3>Pesos da OP <?php echo $ordemId; ?></h3>
                                            <form method="post" action="./OrdemProducao.php<?php echo $busca !== '' ? '?q=' . urlencode($busca) : ''; ?>">
                                                <input type="hidden" name="acao" value="salvar_medidas_op">
                                                <input type="hidden" name="op_id" value="<?php echo $ordemId; ?>">
                                                <input type="hidden" name="q" value="<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="measure-grid">
                                                    <div class="measure-field"><label for="peso_etiqueta_<?php echo $ordemId; ?>">Peso Etiqueta</label><input id="peso_etiqueta_<?php echo $ordemId; ?>" name="peso_etiqueta" type="text" value="<?php echo (float) $ordem['T255A_Peso_Etiqueta'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Peso_Etiqueta'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                    <div class="measure-field"><label for="largura_trena_<?php echo $ordemId; ?>">Largura Trena</label><input id="largura_trena_<?php echo $ordemId; ?>" name="largura_trena" type="text" value="<?php echo (float) $ordem['T255A_Largura_Trena'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Largura_Trena'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                    <div class="measure-field"><label for="peso_entrada_<?php echo $ordemId; ?>">Peso Entrada</label><input id="peso_entrada_<?php echo $ordemId; ?>" name="peso_entrada" type="text" value="<?php echo (float) $ordem['T255A_Peso_Entrada'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Peso_Entrada'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                    <div class="measure-field"><label for="peso_tubo_<?php echo $ordemId; ?>">Peso Tubo</label><input id="peso_tubo_<?php echo $ordemId; ?>" name="peso_tubo" type="text" value="<?php echo (float) $ordem['T255A_Peso_Tubo'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Peso_Tubo'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                    <div class="measure-field"><label for="peso_refilo_<?php echo $ordemId; ?>">Peso Refilo</label><input id="peso_refilo_<?php echo $ordemId; ?>" name="peso_refilo" type="text" value="<?php echo (float) $ordem['T255A_Peso_Refilo'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Peso_Refilo'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                    <div class="measure-field"><label for="peso_saida_<?php echo $ordemId; ?>">Peso Saida</label><input id="peso_saida_<?php echo $ordemId; ?>" name="peso_saida" type="text" value="<?php echo (float) $ordem['T255A_Peso_Saida'] > 0 ? htmlspecialchars(number_format((float) $ordem['T255A_Peso_Saida'], 4, ',', '.'), ENT_QUOTES, 'UTF-8') : ''; ?>"></div>
                                                </div>
                                                <div class="measure-actions">
                                                    <a class="button ghost" href="./OrdemProducao.php<?php echo $busca !== '' ? '?q=' . urlencode($busca) : ''; ?>">Fechar</a>
                                                    <button class="button primary" type="submit">Salvar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>
<script>
function imprimirOrdemProducao(opId) {
    var url = '/hardness3/est/est017/outro/imprimirOrdemProducao/?ajax=true&imprimirSequencia=false&T255_Id=' + encodeURIComponent(opId);
    window.open(url, 'ImprimirOP' + opId, 'menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=750,height=500');
}

(function () {
    // O campo fica com autofocus para o leitor de codigo de barras digitar
    // direto nele sem abrir o teclado virtual do touchscreen (inputmode
    // "none" impede o teclado de aparecer so por causa do foco/leitura de
    // codigo de barras). O teclado so deve abrir quando o usuario realmente
    // quer digitar a mao, via duplo clique/toque.
    var buscaOp = document.getElementById('q');
    if (buscaOp) {
        buscaOp.addEventListener('dblclick', function () {
            buscaOp.setAttribute('inputmode', 'text');
            buscaOp.focus();
        });
        buscaOp.addEventListener('blur', function () {
            buscaOp.setAttribute('inputmode', 'none');
        });
    }
})();
</script>
</body>
</html>
