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

function itensBuscarLinhas($sql)
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

function itensFormatar($valor, $casas = 3)
{
    return number_format((float) $valor, $casas, ',', '.');
}

function inecel_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function inecel_db(): \mysqli
{
    global $g;
    return $g['conexaoBanco'];
}

function inecel_company_rows(): array
{
    $conn = inecel_db();
    $usuarioId = (int) ($_SESSION['Usuario_Atual'] ?? 0);
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
WHERE C019.C019_C007_Id = {$usuarioId}
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

    $result = mysqli_query($sql, $conn);
    if (!$result) {
        return array();
    }

    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

function inecel_apply_company_session(array $company): void
{
    $_SESSION['Perfil_Usuario'] = $company['C019_C021_Id'];
    $_SESSION['Empresa_Atual'] = $company['C019_C004_Id'];
    $_SESSION['Empresa_Logada'] = $company['C019_C004_Id'];
    $_SESSION['Nome_Empresa_Atual'] = $company['C004_Nome_Empresa'];
    $_SESSION['Pasta_Empresa'] = $company['C004_Caminho_Sistema'];
    $_SESSION['cBd_Configuracao'] = $company['C004_Banco_Configuracao'];
}

function inecel_switch_company(int $empresaId, array $companies): bool
{
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaId) {
            inecel_apply_company_session($company);
            return true;
        }
    }

    return false;
}

function inecel_company_theme(int $empresaId): array
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

function inecel_selected_company_name(array $companies): string
{
    $empresaAtual = (int) ($_SESSION['Empresa_Atual'] ?? 0);
    foreach ($companies as $company) {
        if ((int) $company['C019_C004_Id'] === $empresaAtual) {
            return (string) ($company['C004_Nome_Abreviado'] ?: $company['C004_Nome_Empresa']);
        }
    }

    return (string) ($_SESSION['Nome_Empresa_Atual'] ?? 'Empresa');
}

$opId = isset($_GET['op_id']) ? (int) $_GET['op_id'] : 0;
$where = array();
$where[] = $opId > 0 ? "T256_T255_Id = '{$opId}'" : '1 = 0';

// O botao "Apontar" so aparece quando a OP tem um plano de corte (SMT134)
// vinculado. "Apontar Horas" nao depende disso e continua aparecendo sempre.
$opPossuiPlanoCorte = false;
if ($opId > 0) {
    $planoCorte = itensBuscarLinhas("SELECT SMT134_Id FROM SMT134 WHERE SMT134_T255_Id = '{$opId}' LIMIT 1");
    $opPossuiPlanoCorte = !empty($planoCorte);
}

$sqlItens = "
    SELECT
        T256_Id,
        D001_Id,
        D001_Codigo_Produto,
        D001_Descricao_Produto,
        D037_Unidade,
        IFNULL(T256_Quantidade, 0) AS T256_Quantidade,
        IFNULL(T256_Quantidade_Produzida, 0) AS T256_Quantidade_Produzida,
        IFNULL(T256_Quantidade_Entrada_Estoque, 0) AS T256_Quantidade_Entrada_Estoque,
        COALESCE(T256_Quantidade_Saldo, IFNULL(T256_Quantidade, 0) - IFNULL(T256_Quantidade_Produzida, 0)) AS T256_Quantidade_Saldo,
        T256_Data_Previsao_Entrega,
        T256_Observacao_Producao,
        T005_Id,
        D024_Nome_Empresa
    FROM T256
    LEFT JOIN T256A ON T256_Id = T256A_T256_Id
    LEFT JOIN D009 ON D009_Id = T256_D009_Id
    LEFT JOIN D049 ON D049_Id = D009_D049_Id
    LEFT JOIN D001 ON D001_Id = D049_D001_Id
    LEFT JOIN D037 ON D037_Id = D001_D037_Id
    LEFT JOIN T006 ON T006_Id = T256_T006_Id
    LEFT JOIN T005 ON T005_Id = T006_T005_Id
    LEFT JOIN D024 ON D024_Id = T005_D024_Id
    WHERE " . implode(' AND ', $where) . "
    GROUP BY T256_Id
    ORDER BY T256_Id ASC
";

$itens = itensBuscarLinhas($sqlItens);

$fotosPorProduto = array();
$produtoIds = array();
foreach ($itens as $itemFoto) {
    $produtoId = (int) $itemFoto['D001_Id'];
    if ($produtoId > 0) {
        $produtoIds[$produtoId] = $produtoId;
    }
}
if (!empty($produtoIds)) {
    $sqlFotos = "SELECT T172_Id, T172_D001_Id, T172_Nome_Arquivo
                   FROM T172
                  WHERE T172_D001_Id IN (" . implode(',', $produtoIds) . ")
                  ORDER BY T172_Id DESC";
    foreach (itensBuscarLinhas($sqlFotos) as $foto) {
        if (empty($foto['T172_Nome_Arquivo'])) {
            continue;
        }
        $extensao = pathinfo($foto['T172_Nome_Arquivo'], PATHINFO_EXTENSION);
        $urlFoto = "/hardness3/dados_usuarios/{$confUsuario['dbDatabase']}/produtos/{$foto['T172_D001_Id']}/fotos/{$foto['T172_Id']}.{$extensao}";
        $fotosPorProduto[(int) $foto['T172_D001_Id']][] = $urlFoto;
    }
}

$companies = inecel_company_rows();

$totalQuantidade = 0.0;
foreach ($itens as $itemTotalizador) {
    $totalQuantidade += (float) $itemTotalizador['T256_Quantidade'];
}
$usuario = $_SESSION['Primeiro_Nome_Usuario'] ?? $_SESSION['Nome_Usuario'] ?? 'Usuario';
$empresa = $_SESSION['Nome_Empresa_Atual'] ?? 'Empresa';
$empresaAtualId = (int) ($_SESSION['Empresa_Atual'] ?? 0);
$companyTheme = inecel_company_theme($empresaAtualId);
$empresaCurta = inecel_selected_company_name($companies);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Itens da OP</title>
    <style>
        :root { --bg:<?php echo inecel_h($companyTheme['bg']); ?>; --panel:<?php echo inecel_h($companyTheme['panel']); ?>; --line:<?php echo inecel_h($companyTheme['line']); ?>; --primary:<?php echo inecel_h($companyTheme['primary']); ?>; --accent:<?php echo inecel_h($companyTheme['accent']); ?>; --text:#17202a; --muted:#667085; --ok:#168a4a; --warn:#a05a00; }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Arial, Helvetica, sans-serif; }
        .app { width:min(1480px, 100%); margin:0 auto; padding:14px; }
        .topbar { display:flex; gap:12px; align-items:center; justify-content:space-between; padding:12px 0 16px; }
        .title h1 { margin:0; font-size:22px; line-height:1.15; }
        .title p { margin:4px 0 0; color:var(--muted); font-size:13px; }
        .topbar-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .company-switch { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:8px 10px; border:1px solid var(--line); border-radius:8px; background:var(--panel); }
        .summary-card { display:inline-flex; flex-direction:column; min-width:220px; padding:14px 18px; border:1px solid var(--line); border-radius:16px; background:linear-gradient(180deg,#fbfdff 0%,#f4f8fc 100%); box-shadow:0 20px 45px rgba(18,49,86,.08); }
        .summary-card span { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.05em; }
        .summary-card strong { margin-top:6px; font-size:30px; line-height:1; color:var(--primary); }
        .company-chip { display:inline-flex; align-items:center; min-height:34px; padding:0 12px; border-radius:999px; background:var(--accent); color:var(--primary); font-size:12px; font-weight:700; }
        .logout { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:0 14px; border-radius:7px; background:var(--panel); border:1px solid var(--line); color:var(--primary); font-weight:700; text-decoration:none; white-space:nowrap; }
        .userbox { min-width:260px; background:var(--panel); border:1px solid var(--line); border-radius:18px; padding:14px 16px; box-shadow:0 20px 45px rgba(18,49,86,.11); }
        .userbox strong { display:block; }
        .userbox span { display:block; color:var(--muted); font-size:14px; margin-top:3px; }
        .controls { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
        .button { border:0; border-radius:12px; padding:11px 16px; font-size:14px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
        .button.primary { background:linear-gradient(135deg,var(--primary),#0b4f9c); color:#fff; }
        .button.ghost { background:#fff; color:var(--text); border:1px solid var(--line); }
        .grid-wrap { background:var(--panel); border:1px solid var(--line); border-radius:22px; overflow:hidden; box-shadow:0 20px 45px rgba(18,49,86,.11); }
        .grid-header { padding:18px 22px; border-bottom:1px solid var(--line); background:linear-gradient(180deg,#fbfdff 0%,#f4f8fc 100%); }
        .grid-header h2 { margin:0; font-size:22px; }
        .grid-header p { margin:5px 0 0; color:var(--muted); }
        .table-scroll { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; min-width:1400px; }
        th, td { padding:14px 12px; border-bottom:1px solid #e7edf3; font-size:13px; vertical-align:top; }
        th { font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; background:#f9fbfd; text-align:center; }
        .center { text-align:center; }
        .right { text-align:right; }
        .soft { background:#fff8d9; }
        tr:hover td { background:#fbfdff; }
        .empty { padding:36px 22px; text-align:center; color:var(--muted); }
        .apontar, .button-fotos { display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--line); border-radius:8px; padding:7px 12px; background:#fff; color:var(--primary); font-weight:700; font-size:13px; text-decoration:none; cursor:pointer; }
        .fotos-modal-overlay { position:fixed; inset:0; background:rgba(15,25,40,.6); display:none; align-items:center; justify-content:center; padding:24px; z-index:9999; }
        .fotos-modal-overlay.is-visible { display:flex; }
        .fotos-modal { background:#fff; border-radius:16px; max-width:900px; width:100%; max-height:85vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 30px 70px rgba(0,0,0,.35); }
        .fotos-modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--line); }
        .fotos-modal-header h3 { margin:0; font-size:16px; }
        .fotos-modal-close { border:0; background:#f1f3f6; border-radius:999px; width:32px; height:32px; font-size:16px; cursor:pointer; color:var(--text); }
        .fotos-modal-body { padding:18px 20px; overflow-y:auto; display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:14px; }
        .fotos-modal-body a { display:block; border:1px solid var(--line); border-radius:10px; overflow:hidden; }
        .fotos-modal-body img { display:block; width:100%; height:160px; object-fit:cover; }
        @media (max-width:760px) { .topbar { flex-direction:column; } .topbar-actions { width:100%; justify-content:stretch; } .company-switch, .logout, .button { width:100%; } }
    </style>
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="title">
            <h1>Itens da OP <?php echo (int) $opId; ?></h1>
            <p>Etapa intermedi&aacute;ria do fluxo legado. Clique em <strong>Apontar</strong> para abrir o apontamento detalhado.</p>
        </div>
        <div class="topbar-actions">
            <div class="userbox">
                <strong><?php echo inecel_h($usuario); ?></strong>
                <span><?php echo inecel_h($empresa); ?></span>
                <?php if (!empty($empresaCurta)): ?>
                    <div class="company-switch">
                        <span class="company-chip"><?php echo inecel_h($empresaCurta); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <a class="logout" href="./itensOp.php?logout=1&op_id=<?php echo (int) $opId; ?>">Sair</a>
        </div>
    </header>

    <div class="controls">
        <a class="button ghost" href="./OrdemProducao.php">Voltar para Ordem Produ&ccedil;&atilde;o</a>
        <div class="summary-card">
            <span>Total Quantidade</span>
            <strong><?php echo itensFormatar($totalQuantidade); ?></strong>
        </div>
    </div>

    <section class="grid-wrap">
        <div class="grid-header">
            <h2>Itens para apontamento</h2>
        </div>
        <?php if (empty($itens)): ?>
            <div class="empty">Nenhum item encontrado para esta OP.</div>
        <?php else: ?>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <?php if ($opPossuiPlanoCorte): ?>
                                <th>Apontar</th>
                            <?php endif; ?>
                            <th>Apontar Horas</th>
                            <th>Fotos</th>
                            <th>C&oacute;digo</th>
                            <th>Descri&ccedil;&atilde;o</th>
                            <th>Un</th>
                            <th>Quantidade</th>
                            <th>Produzido</th>
                            <th>Estoque</th>
                            <th>Saldo</th>
                            <th class="soft">Entrega</th>
                            <th class="soft">Observa&ccedil;&atilde;o</th>
                            <th class="soft">Pedido</th>
                            <th class="soft">Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens as $item): ?>
                            <?php
                                $apontarLink = './apontar.php?op_id=' . (int) $opId . '&item_id=' . (int) $item['T256_Id'];
                                $apontarHorasLink = './apontarHoras.php?op_id=' . (int) $opId . '&item_id=' . (int) $item['T256_Id'];
                                $produtoIdLinha = (int) $item['D001_Id'];
                                $qtdeFotos = !empty($fotosPorProduto[$produtoIdLinha]) ? count($fotosPorProduto[$produtoIdLinha]) : 0;
                            ?>
                            <tr>
                                <?php if ($opPossuiPlanoCorte): ?>
                                    <td class="center"><a class="apontar" href="<?php echo htmlspecialchars($apontarLink, ENT_QUOTES, 'UTF-8'); ?>">Apontar</a></td>
                                <?php endif; ?>
                                <td class="center"><a class="apontar" href="<?php echo htmlspecialchars($apontarHorasLink, ENT_QUOTES, 'UTF-8'); ?>">Apontar Horas</a></td>
                                <td class="center">
                                    <?php if ($qtdeFotos > 0): ?>
                                        <button type="button" class="button-fotos" onclick="abrirFotos(<?php echo $produtoIdLinha; ?>)">Fotos</button>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="center"><?php echo inecel_h($item['D001_Codigo_Produto']); ?></td>
                                <td><?php echo inecel_h($item['D001_Descricao_Produto']); ?></td>
                                <td class="center"><?php echo inecel_h($item['D037_Unidade']); ?></td>
                                <td class="right"><?php echo itensFormatar($item['T256_Quantidade']); ?></td>
                                <td class="right"><?php echo itensFormatar($item['T256_Quantidade_Produzida']); ?></td>
                                <td class="right"><?php echo itensFormatar($item['T256_Quantidade_Entrada_Estoque']); ?></td>
                                <td class="right"><?php echo itensFormatar($item['T256_Quantidade_Saldo']); ?></td>
                                <td class="center soft"><?php echo (!empty($item['T256_Data_Previsao_Entrega']) && $item['T256_Data_Previsao_Entrega'] !== '0000-00-00') ? inecel_h(gCorrigeData($item['T256_Data_Previsao_Entrega'])) : '-'; ?></td>
                                <td class="soft"><?php echo inecel_h($item['T256_Observacao_Producao']); ?></td>
                                <td class="center soft"><?php echo !empty($item['T005_Id']) ? (int) $item['T005_Id'] : '-'; ?></td>
                                <td class="soft"><?php echo inecel_h($item['D024_Nome_Empresa']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<div class="fotos-modal-overlay" id="fotos-modal-overlay">
    <div class="fotos-modal">
        <div class="fotos-modal-header">
            <h3 id="fotos-modal-titulo">Fotos do produto</h3>
            <button type="button" class="fotos-modal-close" onclick="fecharFotos()">&times;</button>
        </div>
        <div class="fotos-modal-body" id="fotos-modal-body"></div>
    </div>
</div>
<script>
var fotosProdutos = <?php echo json_encode($fotosPorProduto, JSON_UNESCAPED_SLASHES); ?>;

function abrirFotos(produtoId) {
    var fotos = fotosProdutos[produtoId] || [];
    var corpo = document.getElementById('fotos-modal-body');
    corpo.innerHTML = '';
    fotos.forEach(function (url) {
        var link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener';
        var img = document.createElement('img');
        img.src = url;
        img.alt = 'Foto do produto';
        link.appendChild(img);
        corpo.appendChild(link);
    });
    document.getElementById('fotos-modal-titulo').textContent = 'Fotos do produto';
    document.getElementById('fotos-modal-overlay').classList.add('is-visible');
}

function fecharFotos() {
    document.getElementById('fotos-modal-overlay').classList.remove('is-visible');
}

document.getElementById('fotos-modal-overlay').addEventListener('click', function (evento) {
    if (evento.target === this) {
        fecharFotos();
    }
});
</script>
</body>
</html>

