<?php
header("X-Robots-Tag: noindex, nofollow", true);
require_once(__DIR__ . '/../../bibliotecas/confUsuario.php');
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['Usuario_Atual'])) { header('Location: ./index.php'); exit; }
$erro = '';
$mensagem = '';
$tabela = 'SMT207';
$registroId = isset($_GET['id']) ? preg_replace('/\D+/', '', (string) $_GET['id']) : '';
$candidatoAgendaId = isset($_GET['agenda_candidato_id']) ? preg_replace('/\D+/', '', (string) $_GET['agenda_candidato_id']) : '';
$candidatoAgendaLabel = isset($_GET['agenda_candidato_label']) ? trim((string) $_GET['agenda_candidato_label']) : '';
$mysqli = mysqli_init();
$conexaoOk = $mysqli && @mysqli_real_connect($mysqli, $confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexaoOk) { $erro = 'Nao foi possivel conectar ao banco de dados.'; } else { mysqli_set_charset($mysqli, 'utf8'); }
function h($v){ return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function q($mysqli, $v){ return ($v === '' || $v === null) ? 'NULL' : "'" . mysqli_real_escape_string($mysqli, (string) $v) . "'"; }
function detectarCampo($colunas, $preferidos, $contens = array()){ foreach ($preferidos as $p) { foreach ($colunas as $c) { if (strcasecmp($c['Field'], $p) === 0) { return $c['Field']; } } } foreach ($contens as $t) { foreach ($colunas as $c) { if (stripos($c['Field'], $t) !== false) { return $c['Field']; } } } return ''; }
function detectarPk($colunas){ foreach ($colunas as $c) { if (!empty($c['Key']) && strtoupper((string) $c['Key']) === 'PRI') { return $c['Field']; } } return ''; }
function dirBaseFotos($confUsuario){ return 'dados_usuarios/' . $confUsuario['dbDatabase'] . '/candidatos'; }
function buscarFoto($confUsuario, $registroId){ $dir = rtrim($confUsuario['pathRaiz'], '/\\') . '/' . dirBaseFotos($confUsuario) . '/' . $registroId . '/fotos'; if (!is_dir($dir)) { return false; } $arquivos = glob($dir . '/*.*'); if (empty($arquivos)) { return false; } usort($arquivos, function($a,$b){ return filemtime($b) - filemtime($a); }); $nome = basename($arquivos[0]); return rtrim($confUsuario['urlRaiz'], '/\\') . '/hardness3/' . dirBaseFotos($confUsuario) . '/' . $registroId . '/fotos/' . rawurlencode($nome); }
$colunas207 = array(); $colunas206 = array(); $candidatos = array(); $indicadores = array(); $registroAtual = array(); $candidatoAtual = array();
$form = array('candidato_id' => '', 'candidato_label' => '', 'entrevista' => '', 'indicacao' => '', 'indicador_id' => '', 'indicador_label' => '', 'resultado' => '');
$chave207 = $campoCandidato207 = $campoEntrevista207 = $campoIndicacao207 = $campoIndicador207 = $campoResultado207 = $campoDataHora207 = $campoC007 = '';
$campoPk206 = $campoNome206 = $campoCpf206 = '';
$campoNomeD109 = 'D109_Nome'; $campoCpfD109 = 'D109_Cpf'; $campoNomeC007 = 'C007_Nome';
if ($conexaoOk) {
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM SMT207'); while ($res && ($row = mysqli_fetch_assoc($res))) { $colunas207[] = $row; }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM SMT206'); while ($res && ($row = mysqli_fetch_assoc($res))) { $colunas206[] = $row; }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM C007'); while ($res && ($row = mysqli_fetch_assoc($res))) { if (strcasecmp($row['Field'], 'C007_Primeiro_Nome') === 0) { $campoNomeC007 = 'C007_Primeiro_Nome'; } }
    $res = mysqli_query($mysqli, 'SHOW COLUMNS FROM D109'); while ($res && ($row = mysqli_fetch_assoc($res))) { if (strcasecmp($row['Field'], 'D109_Nome') === 0) { $campoNomeD109 = $row['Field']; } if (strcasecmp($row['Field'], 'D109_Cpf') === 0) { $campoCpfD109 = $row['Field']; } }
    $chave207 = detectarPk($colunas207);
    $campoCandidato207 = detectarCampo($colunas207, array('SMT207_SMT206_Id', 'SMT207_Candidato_Id'), array('206', 'candidato'));
    $campoEntrevista207 = detectarCampo($colunas207, array('SMT207_Entrevista'), array('entrevista'));
    $campoIndicacao207 = detectarCampo($colunas207, array('SMT207_Indicacao'), array('indicacao'));
    $campoIndicador207 = detectarCampo($colunas207, array('SMT207_D109_Id'), array('d109', 'indic'));
    $campoResultado207 = detectarCampo($colunas207, array('SMT207_Flag_Aprovado'), array('aprov'));
    $campoDataHora207 = detectarCampo($colunas207, array('SMT207_Data_Hora_Entrevista'), array('data_hora_entrevista'));
    $campoC007 = detectarCampo($colunas207, array('SMT207_C007_Id'), array('c007'));
    $campoPk206 = detectarPk($colunas206);
    $campoNome206 = detectarCampo($colunas206, array('SMT206_Nome_Candidato', 'SMT206_Nome'), array('nome'));
    $campoCpf206 = detectarCampo($colunas206, array('SMT206_CPF', 'SMT206_Cpf'), array('cpf'));
    if ($campoPk206 !== '' && $campoNome206 !== '') {
        $sql = 'SELECT ' . $campoPk206 . ' AS id, ' . $campoNome206 . ' AS nome, ' . ($campoCpf206 !== '' ? $campoCpf206 : "''") . ' AS cpf FROM SMT206 ORDER BY ' . $campoNome206;
        $res = mysqli_query($mysqli, $sql);
        while ($res && ($row = mysqli_fetch_assoc($res))) { $label = trim((string) $row['nome']); if (!empty($row['cpf'])) { $label .= ' - CPF ' . $row['cpf']; } $label .= ' - #' . $row['id']; $candidatos[] = array('id' => (string) $row['id'], 'label' => $label); }
    }
    $sql = 'SELECT D109_Id AS id, ' . $campoNomeD109 . ' AS nome, ' . $campoCpfD109 . ' AS cpf FROM D109 WHERE COALESCE(' . $campoNomeD109 . ', "") <> "" ORDER BY ' . $campoNomeD109;
    $res = mysqli_query($mysqli, $sql);
    while ($res && ($row = mysqli_fetch_assoc($res))) { $label = trim((string) $row['nome']); if (!empty($row['cpf'])) { $label .= ' - CPF ' . $row['cpf']; } $label .= ' - #' . $row['id']; $indicadores[] = array('id' => (string) $row['id'], 'label' => $label); }
    if ($registroId !== '' && $chave207 !== '') {
        $res = mysqli_query($mysqli, 'SELECT * FROM SMT207 WHERE ' . $chave207 . ' = ' . q($mysqli, $registroId) . ' LIMIT 1');
        if ($res && ($registroAtual = mysqli_fetch_assoc($res))) {
            if ($campoCandidato207 !== '' && isset($registroAtual[$campoCandidato207])) { $form['candidato_id'] = (string) $registroAtual[$campoCandidato207]; }
            if ($campoEntrevista207 !== '' && isset($registroAtual[$campoEntrevista207])) { $form['entrevista'] = (string) $registroAtual[$campoEntrevista207]; }
            if ($campoIndicacao207 !== '' && isset($registroAtual[$campoIndicacao207])) { $form['indicacao'] = strtoupper(trim((string) $registroAtual[$campoIndicacao207])); }
            if ($campoIndicador207 !== '' && isset($registroAtual[$campoIndicador207])) { $form['indicador_id'] = (string) $registroAtual[$campoIndicador207]; }
            if ($campoResultado207 !== '' && isset($registroAtual[$campoResultado207])) { $form['resultado'] = strtoupper(trim((string) $registroAtual[$campoResultado207])); }
        }
    }
}
if ($conexaoOk && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $registroId = isset($_POST['registro_id']) ? preg_replace('/\D+/', '', (string) $_POST['registro_id']) : $registroId;
    $form['candidato_id'] = isset($_POST['candidato_id_busca']) ? preg_replace('/\D+/', '', (string) $_POST['candidato_id_busca']) : '';
    $form['candidato_label'] = isset($_POST['candidato_busca']) ? trim((string) $_POST['candidato_busca']) : '';
    $form['entrevista'] = isset($_POST['entrevista_texto']) ? trim((string) $_POST['entrevista_texto']) : '';
    $form['indicacao'] = isset($_POST['indicacao']) ? strtoupper(trim((string) $_POST['indicacao'])) : '';
    $form['indicador_id'] = isset($_POST['indicador_id_busca']) ? preg_replace('/\D+/', '', (string) $_POST['indicador_id_busca']) : '';
    $form['indicador_label'] = isset($_POST['indicador_busca']) ? trim((string) $_POST['indicador_busca']) : '';
    $form['resultado'] = isset($_POST['resultado']) ? strtoupper(trim((string) $_POST['resultado'])) : '';
    if ($form['candidato_id'] === '' && preg_match('/#(\d+)/', $form['candidato_label'], $m)) { $form['candidato_id'] = $m[1]; }
    if ($form['indicador_id'] === '' && preg_match('/#(\d+)/', $form['indicador_label'], $m)) { $form['indicador_id'] = $m[1]; }
    if ($campoCandidato207 === '') { $erro = 'Nao foi possivel identificar o campo do candidato na SMT207.'; }
    elseif ($form['candidato_id'] === '') { $erro = 'Selecione um candidato cadastrado.'; }
    elseif ($campoEntrevista207 === '' || $form['entrevista'] === '') { $erro = 'Preencha o campo Entrevista.'; }
    elseif ($form['indicacao'] === '') { $erro = 'Informe se possui indicacao.'; }
    elseif ($form['indicacao'] === 'S' && $form['indicador_id'] === '') { $erro = 'Selecione o colaborador que indicou.'; }
    elseif (isset($_POST['acao']) && $_POST['acao'] === 'finalizar' && $campoResultado207 !== '' && $form['resultado'] === '') { $erro = 'Preencha o resultado final para finalizar a entrevista.'; }
    else {
        $campos = array(); $valores = array(); $sets = array();
        foreach ($colunas207 as $coluna) {
            $field = $coluna['Field']; if (strtolower((string) $coluna['Extra']) === 'auto_increment') { continue; }
            $usar = false; $valor = '';
            if ($field === $campoCandidato207) { $usar = true; $valor = $form['candidato_id']; }
            elseif ($field === $campoEntrevista207) { $usar = true; $valor = $form['entrevista']; }
            elseif ($field === $campoIndicacao207) { $usar = true; $valor = $form['indicacao']; }
            elseif ($field === $campoIndicador207) { $usar = true; $valor = $form['indicacao'] === 'S' ? $form['indicador_id'] : ''; }
            elseif ($field === $campoResultado207) { $usar = true; $valor = $form['resultado']; }
            elseif ($field === $campoDataHora207) { $usar = true; $valor = date('Y-m-d H:i:s'); }
            elseif ($field === $campoC007) { $usar = true; $valor = isset($_SESSION['Usuario_Atual']) ? (string) $_SESSION['Usuario_Atual'] : ''; }
            elseif (stripos($field, 'data_cadastro') !== false) { $usar = true; $valor = date('Y-m-d'); }
            if (!$usar) { continue; }
            $sqlValor = q($mysqli, $valor); $campos[] = $field; $valores[] = $sqlValor; if ($field !== $chave207) { $sets[] = $field . ' = ' . $sqlValor; }
        }
        $ehUpdate = $registroId !== '' && $chave207 !== '';
        $sql = $ehUpdate ? 'UPDATE SMT207 SET ' . implode(', ', $sets) . ' WHERE ' . $chave207 . ' = ' . q($mysqli, $registroId) . ' LIMIT 1' : 'INSERT INTO SMT207 (' . implode(', ', $campos) . ') VALUES (' . implode(', ', $valores) . ')';
        if (mysqli_query($mysqli, $sql)) { if (!$ehUpdate) { $registroId = (string) mysqli_insert_id($mysqli); } $mensagem = isset($_POST['acao']) && $_POST['acao'] === 'finalizar' ? 'Entrevista finalizada com sucesso.' : 'Entrevista salva com sucesso.'; }
        else { $erro = 'Nao foi possivel gravar a entrevista. ' . mysqli_error($mysqli); }
    }
}
if ($form['candidato_id'] === '' && $candidatoAgendaId !== '') { $form['candidato_id'] = $candidatoAgendaId; $form['candidato_label'] = $candidatoAgendaLabel; }
if ($conexaoOk && $form['candidato_id'] !== '' && $campoPk206 !== '' && $campoNome206 !== '') {
    $res = mysqli_query($mysqli, 'SELECT * FROM SMT206 WHERE ' . $campoPk206 . ' = ' . q($mysqli, $form['candidato_id']) . ' LIMIT 1');
    if ($res && ($candidatoAtual = mysqli_fetch_assoc($res))) {
        if ($form['candidato_label'] === '') { $form['candidato_label'] = $candidatoAtual[$campoNome206] . ($campoCpf206 !== '' && !empty($candidatoAtual[$campoCpf206]) ? ' - CPF ' . $candidatoAtual[$campoCpf206] : '') . ' - #' . $candidatoAtual[$campoPk206]; }
    }
}
$fotoAtual = (!empty($form['candidato_id']) ? buscarFoto($confUsuario, $form['candidato_id']) : false);
$guia = array(
    array('Apresente-se', 'contar sobre voce, seu historico e como chegou aqui.'),
    array('Fumante', 'algum problema de saude, como coluna, pulmoes...?'),
    array('Rotinas e responsabilidade', 'horarios e como administra os problemas do dia a dia.'),
    array('Trabalho em equipe', 'prefere atividades em equipe, ou tarefas que possa executar sem depender de outros, gosta de ensinar?'),
    array('Normas e regras', 'o que voce faz em uma situacao da lideranca te pedir para trabalhar em setor diferente ou alguma funcao diferente / em caso de erro de algum processo, como voce age?'),
    array('Disponibilidade de horario', 'qual a preferencia de turno, tem disponibilidade para trabalhar em outro se necessario, pode fazer HE, problemas de saude que comprometem pegar peso, trabalhos repetitivos ou trabalhos dinamicos?'),
    array('Encerramento', 'porque o interesse pela Inecel, conhece alguem que trabalha na Inecel, teve alguma indicacao de colaboradores, o que espera da empresa?')
);
?>
<!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>RH - Entrevista</title>
<style>
:root{--bg-start:#f8efe1;--bg-end:#f2dfc3;--sidebar-top:#7e470f;--sidebar-bottom:#b46d20;--card:#fffaf3;--border:#efd1af;--primary:#b56a18;--primary-strong:#eb7a17;--text:#2f241a;--muted:#705f4e;--shadow:0 18px 40px rgba(125,81,21,.14)}*{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:Segoe UI,Arial,sans-serif;color:var(--text);background:linear-gradient(135deg,var(--bg-start),var(--bg-end))}body{min-height:100dvh}.shell{min-height:100dvh;display:grid;grid-template-columns:clamp(210px,18vw,240px) minmax(0,1fr)}.sidebar{min-height:100%;padding:16px 14px;background:linear-gradient(180deg,var(--sidebar-top),var(--sidebar-bottom));color:#fff7ef}.sidebar h1{margin:0 0 18px;font-size:30px}.menu{display:grid;gap:10px}.menu a,.logout{display:block;padding:14px 16px;border-radius:16px;text-decoration:none;color:#fff;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.14)}.logout{margin-top:18px;background:transparent;border:none;padding:10px 2px}.content{min-width:0;padding:18px}.card{width:min(100%,1240px);background:var(--card);border-radius:28px;padding:28px;box-shadow:var(--shadow)}.eyebrow{margin:0 0 8px;color:var(--primary);font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}h2{margin:0 0 18px;font-size:28px}.alert{margin-bottom:16px;padding:12px 14px;border-radius:14px;font-size:14px}.success{background:#e7f5eb;color:#216b42}.error{background:#fae8e4;color:#a03e2f}.layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:22px;align-items:start}.photo-panel{background:linear-gradient(180deg,#fffefd,#faefe1);border:1px solid var(--border);border-radius:24px;padding:16px}.photo-frame{width:100%;aspect-ratio:4/5;border:1px dashed var(--border);border-radius:20px;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;color:var(--muted);text-align:center}.photo-frame img{width:100%;height:100%;object-fit:cover}.photo-caption{margin-top:12px;font-size:13px;color:var(--muted)}.field{display:grid;gap:8px;margin-bottom:16px}label{font-size:14px;font-weight:700}input[type=text],select,textarea{width:100%;border:1px solid var(--border);border-radius:18px;padding:14px 16px;font:inherit;color:var(--text);background:#fffdf9}textarea{min-height:220px;resize:vertical}.guide{border:1px solid var(--border);border-radius:20px;background:linear-gradient(180deg,#fffefc,#fbf1e4);padding:18px;margin-bottom:18px}.guide h3{margin:0 0 10px;font-size:16px}.guide ul{margin:0;padding-left:22px;color:var(--muted)}.guide li{margin-bottom:8px;line-height:1.4}.guide li strong{color:var(--text)}.guide-note{margin-top:12px;font-size:12px;line-height:1.45;color:var(--muted)}.bottom-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;align-items:end}.actions{display:flex;gap:12px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 18px;border-radius:16px;border:1px solid var(--border);background:#fff;color:var(--text);text-decoration:none;font:inherit;font-weight:700;cursor:pointer}.btn.primary{background:linear-gradient(135deg,var(--primary),var(--primary-strong));color:#fff;border-color:transparent}.btn.soft{background:#fff3e5;color:var(--primary)}.hidden{display:none!important}.candidate-meta{margin:0 0 14px;font-size:13px;color:var(--muted)}@media (max-width:1100px){.layout{grid-template-columns:1fr}.photo-panel{max-width:320px}.bottom-grid{grid-template-columns:1fr 1fr}}@media (max-width:860px){.shell{grid-template-columns:1fr}.content,.sidebar{padding:16px}.card{padding:20px;border-radius:24px}}@media (max-width:640px){.bottom-grid{grid-template-columns:1fr}.actions{flex-direction:column}.btn{width:100%}.photo-panel{max-width:none}textarea{min-height:180px}}
</style></head><body><div class="shell"><aside class="sidebar"><h1>RH</h1><nav class="menu"><a href="./painel.php">Painel inicial</a><a href="./cadastro_smt206.php">Cadastro de Candidato</a></nav><a class="logout" href="./index.php">Voltar ao login</a></aside><main class="content"><section class="card">
<?php if ($mensagem !== '') { ?><div class="alert success"><?php echo h($mensagem); ?></div><?php } ?>
<?php if ($erro !== '') { ?><div class="alert error"><?php echo h($erro); ?></div><?php } ?>
<h2>Entrevista</h2><div class="layout"><div class="photo-panel"><div class="photo-frame">
<?php if ($fotoAtual) { ?><img src="<?php echo h($fotoAtual); ?>" alt="Foto do candidato"><?php } else { ?><span>Selecione um candidato<br>para visualizar a foto.</span><?php } ?>
</div><div class="photo-caption">Foto vinculada ao cadastro do candidato.</div></div><div><form method="post" action="./entrevista_smt207.php<?php echo $registroId !== '' ? '?id=' . urlencode($registroId) : ''; ?>"><input type="hidden" name="registro_id" value="<?php echo h($registroId); ?>"><input type="hidden" name="candidato_id_busca" id="candidato_id_busca" value="<?php echo h($form['candidato_id']); ?>"><input type="hidden" name="indicador_id_busca" id="indicador_id_busca" value="<?php echo h($form['indicador_id']); ?>"><div class="field"><label for="candidato_busca">Candidato</label><input id="candidato_busca" name="candidato_busca" type="text" list="lista_candidatos" value="<?php echo h($form['candidato_label']); ?>" placeholder="Digite o nome ou CPF do candidato"><datalist id="lista_candidatos"><?php foreach ($candidatos as $item) { ?><option value="<?php echo h($item['label']); ?>"></option><?php } ?></datalist></div>
<?php if (!empty($candidatoAtual) && $campoNome206 !== '') { ?><p class="candidate-meta"><?php echo h($candidatoAtual[$campoNome206]); ?><?php echo $campoCpf206 !== '' && !empty($candidatoAtual[$campoCpf206]) ? ' - CPF ' . h($candidatoAtual[$campoCpf206]) : ''; ?></p><?php } ?>
<div class="guide"><h3>Guia da entrevista</h3><ul><?php foreach ($guia as $linha) { ?><li><strong><?php echo h($linha[0]); ?>:</strong> <?php echo h($linha[1]); ?></li><?php } ?></ul><div class="guide-note">Estamos desde 2013 no mercado com duas frentes de negocio e alem de fabricar eletrocalhas, iniciamos a distribuicao de aco, estamos com 200 funcionarios e expandindo. Trabalhamos com 1o 2o turnos e Horario comercial.</div></div>
<div class="field"><label for="entrevista_texto">Entrevista</label><textarea id="entrevista_texto" name="entrevista_texto" placeholder="Registre aqui as informacoes coletadas na entrevista..."><?php echo h($form['entrevista']); ?></textarea></div>
<div class="bottom-grid"><div class="field"><label for="indicacao">Possui indicacao</label><select id="indicacao" name="indicacao"><option value="">Selecione</option><option value="S" <?php echo $form['indicacao'] === 'S' ? 'selected' : ''; ?>>Sim</option><option value="N" <?php echo $form['indicacao'] === 'N' ? 'selected' : ''; ?>>Nao</option></select></div><div class="field" id="indicador_wrap"><label for="indicador_busca">Colaborador que indicou</label><input id="indicador_busca" name="indicador_busca" type="text" list="lista_indicadores" value="<?php echo h($form['indicador_label']); ?>" placeholder="Pesquisar por nome ou CPF"><datalist id="lista_indicadores"><?php foreach ($indicadores as $item) { ?><option value="<?php echo h($item['label']); ?>"></option><?php } ?></datalist></div><div class="field"><label for="resultado">Resultado final</label><select id="resultado" name="resultado"><option value="" <?php echo $form['resultado'] === '' ? 'selected' : ''; ?>>Em branco</option><option value="S" <?php echo $form['resultado'] === 'S' ? 'selected' : ''; ?>>Aprovado</option><option value="N" <?php echo $form['resultado'] === 'N' ? 'selected' : ''; ?>>Reprovado</option></select></div></div>
<div class="actions"><button class="btn primary" type="submit" name="acao" value="finalizar">Finalizar entrevista</button></div></form></div></div></section></main></div>
<script>
(function(){
var candidatos = <?php echo json_encode($candidatos); ?>;
var indicadores = <?php echo json_encode($indicadores); ?>;
var candidatoInput = document.getElementById('candidato_busca');
var candidatoIdInput = document.getElementById('candidato_id_busca');
var indicadorInput = document.getElementById('indicador_busca');
var indicadorIdInput = document.getElementById('indicador_id_busca');
var indicacaoSelect = document.getElementById('indicacao');
var indicadorWrap = document.getElementById('indicador_wrap');
function resolverId(valor, lista){ for (var i = 0; i < lista.length; i++) { if (lista[i].label === valor) { return lista[i].id; } } var match = (valor || '').match(/#(\d+)$/); return match ? match[1] : ''; }
function atualizarIndicador(){ var mostrar = indicacaoSelect && indicacaoSelect.value === 'S'; if (indicadorWrap) { indicadorWrap.classList.toggle('hidden', !mostrar); } if (!mostrar) { if (indicadorInput) { indicadorInput.value = ''; } if (indicadorIdInput) { indicadorIdInput.value = ''; } } }
if (candidatoInput && candidatoIdInput) { var syncCandidato = function(){ candidatoIdInput.value = resolverId(candidatoInput.value, candidatos); }; candidatoInput.addEventListener('change', syncCandidato); candidatoInput.addEventListener('blur', syncCandidato); }
if (indicadorInput && indicadorIdInput) { var syncIndicador = function(){ indicadorIdInput.value = resolverId(indicadorInput.value, indicadores); }; indicadorInput.addEventListener('change', syncIndicador); indicadorInput.addEventListener('blur', syncIndicador); }
if (indicacaoSelect) { indicacaoSelect.addEventListener('change', atualizarIndicador); atualizarIndicador(); }
})();
</script></body></html>