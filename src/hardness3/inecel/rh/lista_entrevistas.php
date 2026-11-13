<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once(__DIR__ . '/../../bibliotecas/confUsuario.php');
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['Usuario_Atual'])) { header('Location: ./index.php'); exit; }
$erro = '';
$mysqli = mysqli_init();
$conexaoOk = $mysqli && @mysqli_real_connect($mysqli, $confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexaoOk) { $erro = 'Nao foi possivel conectar ao banco de dados.'; } else { mysqli_set_charset($mysqli, 'utf8'); }
function h($v){ return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function q($mysqli, $v){ return ($v === '' || $v === null) ? 'NULL' : "'" . mysqli_real_escape_string($mysqli, (string) $v) . "'"; }
function detectarCampo($colunas, $preferidos, $contens = array()){ foreach ($preferidos as $p) { foreach ($colunas as $c) { if (strcasecmp($c['Field'], $p) === 0) { return $c['Field']; } } } foreach ($contens as $t) { foreach ($colunas as $c) { if (stripos($c['Field'], $t) !== false) { return $c['Field']; } } } return ''; }
function detectarPk($colunas){ foreach ($colunas as $c) { if (!empty($c['Key']) && strtoupper((string) $c['Key']) === 'PRI') { return $c['Field']; } } return ''; }
function normalizarResultadoFiltro($valor){ $valor = strtoupper(trim((string) $valor)); if ($valor === 'APROVADO') { return 'S'; } if ($valor === 'REPROVADO' || $valor === 'NAO APROVADO') { return 'N'; } return $valor; }
function normalizarDataFiltro($valor){ $valor = trim((string) $valor); if ($valor === '') { return ''; } if (preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $valor)) { return $valor; } if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $valor, $m)) { return $m[3] . '-' . $m[2] . '-' . $m[1]; } return ''; }
$colunas207 = array(); $colunas206 = array(); $campoNomeC007 = 'C007_Nome'; $rows = array();
$filtros = array('nome' => isset($_GET['nome']) ? trim((string) $_GET['nome']) : '', 'cpf' => isset($_GET['cpf']) ? trim((string) $_GET['cpf']) : '', 'resultado' => isset($_GET['resultado']) ? normalizarResultadoFiltro($_GET['resultado']) : '', 'data_de' => isset($_GET['data_de']) ? normalizarDataFiltro($_GET['data_de']) : '', 'data_ate' => isset($_GET['data_ate']) ? normalizarDataFiltro($_GET['data_ate']) : '', 'entrevistador' => isset($_GET['entrevistador']) ? trim((string) $_GET['entrevistador']) : '');
$chave207 = $campoCandidato207 = $campoEntrevista207 = $campoIndicacao207 = $campoIndicador207 = $campoResultado207 = $campoDataHora207 = $campoC007 = '';
$campoPk206 = $campoNome206 = $campoCpf206 = '';
$campoNomeD109 = 'D109_Nome';
if ($conexaoOk) {
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM SMT207'); while ($res && ($row = mysqli_fetch_assoc($res))) { $colunas207[] = $row; }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM SMT206'); while ($res && ($row = mysqli_fetch_assoc($res))) { $colunas206[] = $row; }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM C007'); while ($res && ($row = mysqli_fetch_assoc($res))) { if (strcasecmp($row['Field'], 'C007_Primeiro_Nome') === 0) { $campoNomeC007 = 'C007_Primeiro_Nome'; } }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM D109'); while ($res && ($row = mysqli_fetch_assoc($res))) { if (strcasecmp($row['Field'], 'D109_Nome') === 0) { $campoNomeD109 = $row['Field']; } }
    $chave207 = detectarPk($colunas207);
    $campoCandidato207 = detectarCampo($colunas207, array('SMT207_SMT206_Id', 'SMT207_Candidato_Id'), array('206', 'candidato'));
    $campoEntrevista207 = detectarCampo($colunas207, array('SMT207_Entrevista'), array('entrevista'));
    $campoIndicacao207 = detectarCampo($colunas207, array('SMT207_Indicacao'), array('indicacao'));
    $campoIndicador207 = detectarCampo($colunas207, array('SMT207_D109_Id'), array('d109', 'indic'));
    $campoResultado207 = detectarCampo($colunas207, array('SMT207_Flag_Aprovado'), array('aprov'));
    if ($filtros['resultado'] === '' && !empty($campoResultado207) && isset($_GET[$campoResultado207])) { $filtros['resultado'] = normalizarResultadoFiltro($_GET[$campoResultado207]); }
    $campoDataHora207 = detectarCampo($colunas207, array('SMT207_Data_Hora_Entrevista'), array('data_hora_entrevista'));
    $campoC007 = detectarCampo($colunas207, array('SMT207_C007_Id'), array('c007'));
    $campoPk206 = detectarPk($colunas206);
    $campoNome206 = detectarCampo($colunas206, array('SMT206_Nome_Candidato', 'SMT206_Nome'), array('nome'));
    $campoCpf206 = detectarCampo($colunas206, array('SMT206_CPF', 'SMT206_Cpf'), array('cpf'));
    if ($campoCandidato207 !== '' && $campoPk206 !== '' && $campoNome206 !== '') {
        $where = array('1=1');
        if ($filtros['nome'] !== '') { $where[] = 'cand.' . $campoNome206 . ' LIKE ' . q($mysqli, '%' . $filtros['nome'] . '%'); }
        if ($filtros['cpf'] !== '' && $campoCpf206 !== '') { $where[] = 'cand.' . $campoCpf206 . ' LIKE ' . q($mysqli, '%' . $filtros['cpf'] . '%'); }
        if ($filtros['resultado'] !== '' && $campoResultado207 !== '') { $where[] = 'a.' . $campoResultado207 . ' = ' . q($mysqli, $filtros['resultado']); }
        if ($filtros['entrevistador'] !== '' && $campoC007 !== '') { $where[] = 'ent.' . $campoNomeC007 . ' LIKE ' . q($mysqli, '%' . $filtros['entrevistador'] . '%'); }
        if ($filtros['data_de'] !== '' && $campoDataHora207 !== '') { $where[] = 'DATE(a.' . $campoDataHora207 . ') >= ' . q($mysqli, $filtros['data_de']); }
        if ($filtros['data_ate'] !== '' && $campoDataHora207 !== '') { $where[] = 'DATE(a.' . $campoDataHora207 . ') <= ' . q($mysqli, $filtros['data_ate']); }
        $sql = 'SELECT a.*';
        $sql .= ', cand.' . $campoNome206 . ' AS candidato_nome';
        $sql .= ', ' . ($campoCpf206 !== '' ? 'cand.' . $campoCpf206 : "''") . ' AS candidato_cpf';
        $sql .= ', ent.' . $campoNomeC007 . ' AS entrevistador_nome';
        $sql .= ', d109.' . $campoNomeD109 . ' AS indicador_nome';
        $sql .= ' FROM SMT207 a';
        $sql .= ' LEFT JOIN SMT206 cand ON cand.' . $campoPk206 . ' = a.' . $campoCandidato207;
        if ($campoC007 !== '') { $sql .= ' LEFT JOIN C007 ent ON ent.C007_Id = a.' . $campoC007; }
        if ($campoIndicador207 !== '') { $sql .= ' LEFT JOIN D109 d109 ON d109.D109_Id = a.' . $campoIndicador207; }
        $sql .= ' WHERE ' . implode(' AND ', $where);
        if ($campoDataHora207 !== '') { $sql .= ' ORDER BY a.' . $campoDataHora207 . ' DESC'; }
        $res = mysqli_query($mysqli, $sql);
        while ($res && ($row = mysqli_fetch_assoc($res))) { $rows[] = $row; }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>RH - Lista de Entrevistas</title>
<style>
:root{--bg-start:#f8efe1;--bg-end:#f2dfc3;--sidebar-top:#7e470f;--sidebar-bottom:#b46d20;--card:#fffaf3;--border:#efd1af;--primary:#b56a18;--primary-strong:#eb7a17;--text:#2f241a;--muted:#705f4e;--shadow:0 18px 40px rgba(125,81,21,.14)}*{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:Segoe UI,Arial,sans-serif;color:var(--text);background:linear-gradient(135deg,var(--bg-start),var(--bg-end))}body{min-height:100dvh}.shell{min-height:100dvh;display:grid;grid-template-columns:clamp(210px,18vw,240px) minmax(0,1fr)}.sidebar{min-height:100%;padding:16px 14px;background:linear-gradient(180deg,var(--sidebar-top),var(--sidebar-bottom));color:#fff7ef}.sidebar h1{margin:0 0 18px;font-size:30px}.menu{display:grid;gap:10px}.menu a,.logout{display:block;padding:14px 16px;border-radius:16px;text-decoration:none;color:#fff;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.14)}.logout{margin-top:18px;background:transparent;border:none;padding:10px 2px}.content{min-width:0;padding:18px}.card{width:min(100%,1380px);background:var(--card);border-radius:28px;padding:28px;box-shadow:var(--shadow)}h2{margin:0 0 18px;font-size:28px}.alert{margin-bottom:16px;padding:12px 14px;border-radius:14px;font-size:14px}.error{background:#fae8e4;color:#a03e2f}.filters{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;align-items:end;margin-bottom:18px}.field{display:grid;gap:8px}label{font-size:14px;font-weight:700}input[type=text],input[type=date],select{width:100%;border:1px solid var(--border);border-radius:18px;padding:14px 16px;font:inherit;color:var(--text);background:#fffdf9}.actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 18px;border-radius:16px;border:1px solid var(--border);background:#fff;color:var(--text);text-decoration:none;font:inherit;font-weight:700;cursor:pointer}.btn.primary{background:linear-gradient(135deg,var(--primary),var(--primary-strong));color:#fff;border-color:transparent}.table-wrap{overflow-x:auto;overflow-y:hidden}.table{width:100%;min-width:1240px;border-collapse:collapse;table-layout:fixed}.table th,.table td{padding:14px 10px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top;overflow-wrap:anywhere;word-break:break-word}.table th{color:var(--primary);font-size:14px}.table td.candidato,.table th.candidato{width:170px}.table td.cpf,.table th.cpf{white-space:nowrap;width:130px}.table td.data,.table th.data{width:130px}.table td.resultado,.table th.resultado{width:130px}.table td.indicacao,.table th.indicacao{width:105px}.table td.indicador,.table th.indicador{width:170px}.table td.entrevistador,.table th.entrevistador{width:170px}.table td.entrevista,.table th.entrevista{width:260px}.table td.acao,.table th.acao{width:100px;white-space:nowrap}.pill{display:inline-flex;padding:8px 14px;border-radius:999px;font-weight:700}.pill.aprovado{background:#e6f5ea;color:#1e7a49}.pill.reprovado{background:#fbe9e5;color:#cb4c35}.resumo{color:var(--muted);line-height:1.45}.resumo-bloco{display:block;max-width:100%;line-height:1.45;color:var(--muted);display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden}.link-abrir{color:var(--primary);font-weight:700;text-decoration:none;display:inline-flex}@media (max-width:1100px){.filters{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (max-width:860px){.shell{grid-template-columns:1fr}.content,.sidebar{padding:16px}.card{padding:20px}}@media (max-width:620px){.filters{grid-template-columns:1fr}.actions{flex-direction:column}.btn{width:100%}}
</style></head><body><div class="shell"><aside class="sidebar"><h1>RH</h1><nav class="menu"><a href="./painel.php">Painel inicial</a><a href="./cadastro_smt206.php">Cadastro de Candidato</a><a href="./entrevista_smt207.php">Entrevista</a></nav><a class="logout" href="./index.php">Voltar ao login</a></aside><main class="content"><section class="card">
<?php if ($erro !== '') { ?><div class="alert error"><?php echo h($erro); ?></div><?php } ?>
<h2>Lista de Entrevistas</h2>
<form method="get" action="./lista_entrevistas.php"><div class="filters">
<div class="field"><label for="nome">Nome do candidato</label><input id="nome" name="nome" type="text" value="<?php echo h($filtros['nome']); ?>"></div>
<div class="field"><label for="cpf">CPF do candidato</label><input id="cpf" name="cpf" type="text" value="<?php echo h($filtros['cpf']); ?>"></div>
<div class="field"><label for="resultado">Resultado final</label><select id="resultado" name="resultado"><option value="">Todos</option><option value="S" <?php echo $filtros['resultado'] === 'S' ? 'selected' : ''; ?>>Aprovado</option><option value="N" <?php echo $filtros['resultado'] === 'N' ? 'selected' : ''; ?>>Reprovado</option></select></div>
<div class="field"><label for="data_de">Data da entrevista de</label><input id="data_de" name="data_de" type="date" value="<?php echo h($filtros['data_de']); ?>"></div>
<div class="field"><label for="data_ate">Data da entrevista ate</label><input id="data_ate" name="data_ate" type="date" value="<?php echo h($filtros['data_ate']); ?>"></div>
<div class="field"><label for="entrevistador">Entrevistador</label><input id="entrevistador" name="entrevistador" type="text" value="<?php echo h($filtros['entrevistador']); ?>"></div>
</div><div class="actions"><button class="btn primary" type="submit">Pesquisar</button><a class="btn" href="./lista_entrevistas.php">Limpar filtros</a></div></form>
<div class="table-wrap"><table class="table"><thead><tr><th class="candidato">Candidato</th><th class="cpf">CPF</th><th class="data">Data</th><th class="resultado">Resultado</th><th class="indicacao">Indicacao</th><th class="indicador">Indicador</th><th class="entrevistador">Entrevistador</th><th class="entrevista">Entrevista</th><th class="acao">Acoes</th></tr></thead><tbody>
<?php if (empty($rows)) { ?><tr><td colspan="9" class="resumo">Nenhuma entrevista encontrada para os filtros informados.</td></tr><?php } ?>
<?php foreach ($rows as $row) { $resultado = $campoResultado207 !== '' && isset($row[$campoResultado207]) ? strtoupper(trim((string) $row[$campoResultado207])) : ''; $resultadoTexto = $resultado === 'S' ? 'Aprovado' : ($resultado === 'N' ? 'Reprovado' : 'Em branco'); $pillClasse = $resultado === 'S' ? 'aprovado' : ($resultado === 'N' ? 'reprovado' : ''); $textoData = ''; if ($campoDataHora207 !== '' && !empty($row[$campoDataHora207])) { $ts = strtotime($row[$campoDataHora207]); $textoData = $ts ? date('d/m/Y H:i', $ts) : $row[$campoDataHora207]; } $textoIndicacao = $campoIndicacao207 !== '' && isset($row[$campoIndicacao207]) ? strtoupper(trim((string) $row[$campoIndicacao207])) : ''; $textoIndicacao = $textoIndicacao === 'S' ? 'Sim' : ($textoIndicacao === 'N' ? 'Nao' : ''); $resumo = $campoEntrevista207 !== '' && !empty($row[$campoEntrevista207]) ? trim((string) $row[$campoEntrevista207]) : ''; if (strlen($resumo) > 110) { $resumo = substr($resumo, 0, 110) . '...'; } ?>
<tr><td class="candidato"><?php echo h(isset($row['candidato_nome']) ? $row['candidato_nome'] : ''); ?></td><td class="cpf"><?php echo h(isset($row['candidato_cpf']) ? $row['candidato_cpf'] : ''); ?></td><td class="data"><?php echo h($textoData); ?></td><td class="resultado"><?php if ($pillClasse !== '') { ?><span class="pill <?php echo h($pillClasse); ?>"><?php echo h($resultadoTexto); ?></span><?php } else { echo h($resultadoTexto); } ?></td><td class="indicacao"><?php echo h($textoIndicacao); ?></td><td class="indicador"><?php echo h(isset($row['indicador_nome']) ? $row['indicador_nome'] : ''); ?></td><td class="entrevistador"><?php echo h(isset($row['entrevistador_nome']) ? $row['entrevistador_nome'] : ''); ?></td><td class="entrevista"><span class="resumo-bloco"><?php echo h($resumo); ?></span></td><td class="acao"><?php if ($chave207 !== '' && isset($row[$chave207])) { ?><a class="link-abrir" href="./entrevista_smt207.php?id=<?php echo urlencode((string) $row[$chave207]); ?>">Abrir</a><?php } ?></td></tr>
<?php } ?>
</tbody></table></div></section></main></div></body></html>