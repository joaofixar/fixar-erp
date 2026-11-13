<?php
namespace hardness;

header("X-Robots-Tag: noindex, nofollow", true);

require_once(__DIR__ . '/../../bibliotecas/confUsuario.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['Usuario_Atual'])) {
    header('Location: ./index.php');
    exit;
}

$mysqli = mysqli_init();
$conexaoOk = $mysqli && @mysqli_real_connect(
    $mysqli,
    $confUsuario['dbHost'],
    $confUsuario['dbUser'],
    $confUsuario['dbPass'],
    $confUsuario['dbDatabase']
);

$erro = '';
$mensagem = '';
$tabela = 'SMT206';
$colunas = array();
$camposEditaveis = array();
$registroAtual = array();
$chavePrimaria = '';
$estados = array();
$cidades = array();
$paises = array();

if (!$conexaoOk) {
    $erro = 'Nao foi possivel conectar ao banco de dados.';
} else {
    mysqli_set_charset($mysqli, 'utf8');
}

function rhEscapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function rhEscaparSql($mysqli, $valor)
{
    if ($valor === '' || $valor === null) {
        return 'NULL';
    }

    return "'" . mysqli_real_escape_string($mysqli, (string) $valor) . "'";
}

function rhSomenteNumeros($valor)
{
    return preg_replace('/\D+/', '', (string) $valor);
}

function rhTituloCampo($campo)
{
    $mapa = array(
        'D018_Id' => 'Estado',
        'D020_Id' => 'Cidade',
        'D030_Id' => 'Pais',
        'Cep' => 'CEP',
        'Cpf' => 'CPF',
        'RG' => 'RG',
        'Rg' => 'RG',
        'Uf' => 'UF',
        'Ddd' => 'DDD',
        'Data_Nascimento' => 'Data de nascimento',
        'Data_Emissao' => 'Data de emissao',
        'Data_Cadastro' => 'Data de cadastro',
        'Celular' => 'Celular',
        'Telefone' => 'Telefone',
        'Email' => 'E-mail',
        'Endereco' => 'Endereco',
        'Numero' => 'Numero',
        'Bairro' => 'Bairro',
        'Complemento' => 'Complemento',
        'Observacao' => 'Observacao',
        'Observacoes' => 'Observacoes',
        'Nome' => 'Nome',
        'Nome_Candidato' => 'Nome do candidato'
    );

    $limpo = preg_replace('/^SMT206_/', '', $campo);
    if (isset($mapa[$limpo])) {
        return $mapa[$limpo];
    }

    $limpo = str_replace('_Id', '', $limpo);
    $limpo = str_replace('_', ' ', $limpo);
    $limpo = ucwords(strtolower($limpo));
    $limpo = str_replace('Cpf', 'CPF', $limpo);
    $limpo = str_replace('Rg', 'RG', $limpo);
    $limpo = str_replace('Cep', 'CEP', $limpo);
    $limpo = str_replace('Uf', 'UF', $limpo);
    $limpo = str_replace('Ddd', 'DDD', $limpo);
    return $limpo;
}

function rhCampoEhTextarea($tipo, $campo)
{
    $tipo = strtolower($tipo);
    return strpos($tipo, 'text') !== false || stripos($campo, 'observa') !== false || stripos($campo, 'descricao') !== false;
}

function rhCampoEhData($tipo)
{
    $tipo = strtolower($tipo);
    return strpos($tipo, 'date') !== false || strpos($tipo, 'time') !== false;
}

function rhCampoEhCpf($campo)
{
    return stripos($campo, 'cpf') !== false;
}

function rhCpfValido($cpf)
{
    $cpf = preg_replace('/\D+/', '', (string) $cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($posicao = 9; $posicao < 11; $posicao++) {
        $soma = 0;
        for ($i = 0; $i < $posicao; $i++) {
            $soma += (int) $cpf[$i] * (($posicao + 1) - $i);
        }
        $digito = (($soma * 10) % 11) % 10;
        if ((int) $cpf[$posicao] !== $digito) {
            return false;
        }
    }

    return true;
}

function rhCampoEhDataCadastro($campo)
{
    return strtolower($campo) === 'smt206_data_cadastro';
}

function rhCampoAceitaMetade($campo)
{
    $campos = array(
        'SMT206_D018_Id',
        'SMT206_D020_Id',
        'SMT206_D030_Id',
        'SMT206_Cep',
        'SMT206_CPF',
        'SMT206_Cpf',
        'SMT206_RG',
        'SMT206_Rg',
        'SMT206_Data_Nascimento',
        'SMT206_Telefone',
        'SMT206_Celular',
        'SMT206_Email'
    );

    return in_array($campo, $campos);
}

function rhClasseCampoLayout($campo, $tipo)
{
    if (rhCampoEhTextarea($tipo, $campo)) {
        return 'field span-12';
    }

    $campoMin = strtolower($campo);

    $span2 = array('smt206_numero', 'smt206_ddd');
    $span3 = array('smt206_d018_id', 'smt206_d030_id', 'smt206_cep', 'smt206_data_nascimento', 'smt206_data_emissao', 'smt206_data_cadastro');
    $span4 = array('smt206_cpf', 'smt206_rg', 'smt206_bairro', 'smt206_telefone', 'smt206_celular');
    $span5 = array('smt206_d020_id', 'smt206_email');
    $span8 = array('smt206_endereco', 'smt206_complemento');
    $span12 = array('smt206_nome', 'smt206_nome_candidato');

    if (in_array($campoMin, $span2, true)) {
        return 'field span-2';
    }

    if (in_array($campoMin, $span3, true)) {
        return 'field span-3';
    }

    if (in_array($campoMin, $span4, true)) {
        return 'field span-4';
    }

    if (in_array($campoMin, $span5, true)) {
        return 'field span-5';
    }

    if (in_array($campoMin, $span8, true)) {
        return 'field span-8';
    }

    if (in_array($campoMin, $span12, true)) {
        return 'field span-12';
    }

    return rhCampoAceitaMetade($campo) ? 'field span-6' : 'field span-12';
}

function rhDataBancoParaFormulario($valor)
{
    if (empty($valor) || $valor === '0000-00-00' || $valor === '0000-00-00 00:00:00') {
        return '';
    }

    $parte = explode(' ', $valor);
    $pedacos = explode('-', $parte[0]);
    if (count($pedacos) !== 3) {
        return $valor;
    }

    return $pedacos[2] . '/' . $pedacos[1] . '/' . $pedacos[0];
}

function rhDataFormularioParaBanco($valor)
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return '';
    }

    if (strpos($valor, '/') === false) {
        return $valor;
    }

    $pedacos = explode('/', $valor);
    if (count($pedacos) !== 3) {
        return $valor;
    }

    return $pedacos[2] . '-' . $pedacos[1] . '-' . $pedacos[0];
}

function rhValorFormulario($valor, $tipo)
{
    return rhCampoEhData($tipo) ? rhDataBancoParaFormulario($valor) : $valor;
}

function rhValorBanco($valor, $tipo)
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        return '';
    }

    return rhCampoEhData($tipo) ? rhDataFormularioParaBanco($valor) : $valor;
}

function rhCampoNomePrincipal($colunas)
{
    $preferidos = array('SMT206_Nome_Candidato', 'SMT206_Nome', 'SMT206_Descricao', 'SMT206_Email');
    foreach ($preferidos as $campo) {
        foreach ($colunas as $coluna) {
            if ($coluna['Field'] === $campo) {
                return $campo;
            }
        }
    }

    foreach ($colunas as $coluna) {
        if (stripos($coluna['Field'], 'nome') !== false) {
            return $coluna['Field'];
        }
    }

    return '';
}

function rhCampoCpfPrincipal($colunas)
{
    foreach ($colunas as $coluna) {
        if (rhCampoEhCpf($coluna['Field'])) {
            return $coluna['Field'];
        }
    }

    return '';
}

function rhDiretorioBaseFotos($confUsuario)
{
    return 'dados_usuarios/' . $confUsuario['dbDatabase'] . '/candidatos';
}

function rhDiretorioFotoFisico($confUsuario, $registroId)
{
    return rtrim($confUsuario['pathRaiz'], '/\\') . '/' . rhDiretorioBaseFotos($confUsuario) . '/' . $registroId . '/fotos';
}

function rhDiretorioFotoWeb($confUsuario, $registroId)
{
    return rtrim($confUsuario['urlRaiz'], '/\\') . '/hardness3/' . rhDiretorioBaseFotos($confUsuario) . '/' . $registroId . '/fotos';
}

function rhBuscarFotoCandidato($confUsuario, $registroId)
{
    $dir = rhDiretorioFotoFisico($confUsuario, $registroId);
    if (!is_dir($dir)) {
        return false;
    }

    $arquivos = glob($dir . '/*.*');
    if (empty($arquivos)) {
        return false;
    }

    usort($arquivos, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $arquivo = $arquivos[0];
    return array(
        'nome' => basename($arquivo),
        'url' => rhDiretorioFotoWeb($confUsuario, $registroId) . '/' . rawurlencode(basename($arquivo))
    );
}

function rhSalvarFotoCandidato($confUsuario, $registroId)
{
    if (empty($_FILES['foto_candidato']) || $_FILES['foto_candidato']['error'] !== UPLOAD_ERR_OK) {
        return array('code' => true);
    }

    $arquivo = $_FILES['foto_candidato'];
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidas = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    if (!in_array($ext, $permitidas)) {
        return array('code' => false, 'data' => 'Formato de foto invalido. Use JPG, PNG, GIF ou WEBP.');
    }

    $dir = rhDiretorioFotoFisico($confUsuario, $registroId);
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }

    foreach (glob($dir . '/*.*') as $arquivoAntigo) {
        @unlink($arquivoAntigo);
    }

    $nomeFinal = 'foto_principal.' . $ext;
    $destino = $dir . '/' . $nomeFinal;
    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        return array('code' => false, 'data' => 'Nao foi possivel salvar a foto do candidato.');
    }

    return array('code' => true);
}

if ($conexaoOk) {
    $sqlColunas = mysqli_query($mysqli, "SHOW COLUMNS FROM {$tabela}");
    if (!$sqlColunas) {
        $erro = 'Nao foi possivel ler a estrutura da tabela SMT206. ' . mysqli_error($mysqli);
    } else {
        while ($coluna = mysqli_fetch_assoc($sqlColunas)) {
            $colunas[] = $coluna;
            if ($coluna['Key'] === 'PRI' && empty($chavePrimaria)) {
                $chavePrimaria = $coluna['Field'];
            }
        }
    }
}

foreach ($colunas as $coluna) {
    if (strtolower((string) $coluna['Extra']) === 'auto_increment') {
        continue;
    }
    $camposEditaveis[] = $coluna;
}

$campoCpf = rhCampoCpfPrincipal($camposEditaveis);

if (isset($_GET['verificar_cpf'])) {
    header('Content-Type: application/json; charset=utf-8');
    $resposta = array('valido' => false, 'existe' => false, 'mensagem' => '');

    if (!$conexaoOk || empty($campoCpf)) {
        $resposta['mensagem'] = 'Nao foi possivel validar o CPF agora.';
        echo json_encode($resposta);
        exit;
    }

    $cpfInformado = rhSomenteNumeros($_GET['verificar_cpf']);
    $registroIdAtual = isset($_GET['registro_id']) ? trim($_GET['registro_id']) : '';

    if (!rhCpfValido($cpfInformado)) {
        $resposta['mensagem'] = 'CPF invalido.';
        echo json_encode($resposta);
        exit;
    }

    $resposta['valido'] = true;

    $filtroCpf = "REPLACE(REPLACE(REPLACE(REPLACE(COALESCE({$campoCpf}, ''), '.', ''), '-', ''), '/', ''), ' ', '') = " . rhEscaparSql($mysqli, $cpfInformado);
    $sqlCpf = "SELECT {$chavePrimaria} FROM {$tabela} WHERE {$filtroCpf}";
    if ($registroIdAtual !== '' && !empty($chavePrimaria)) {
        $sqlCpf .= " AND {$chavePrimaria} <> " . rhEscaparSql($mysqli, $registroIdAtual);
    }
    $sqlCpf .= " LIMIT 1";
    $resCpf = mysqli_query($mysqli, $sqlCpf);

    if ($resCpf && mysqli_fetch_assoc($resCpf)) {
        $resposta['existe'] = true;
        $resposta['mensagem'] = 'Ja existe um candidato cadastrado com este CPF.';
    }

    echo json_encode($resposta);
    exit;
}

if ($conexaoOk) {
    $res = mysqli_query($mysqli, "SELECT D018_Id, D018_UF FROM D018 WHERE D018_UF != '' ORDER BY D018_UF");
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $estados[] = array('value' => $row['D018_Id'], 'label' => $row['D018_UF']);
    }

    $res = mysqli_query($mysqli, "SELECT D020_Id, D020_Nome_Cidade, D020_D018_Id FROM D020 WHERE D020_Nome_Cidade != '' ORDER BY D020_Nome_Cidade");
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $cidades[] = $row;
    }

    $res = mysqli_query($mysqli, "SELECT D030_Id, D030_Nome_Pais FROM D030 WHERE D030_Nome_Pais != '' ORDER BY D030_Nome_Pais");
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $paises[] = array('value' => $row['D030_Id'], 'label' => $row['D030_Nome_Pais']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($erro)) {
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    if ($acao === 'salvar') {
        $idRegistro = isset($_POST['registro_id']) ? trim($_POST['registro_id']) : '';
        $campos = array();
        $valores = array();
        $updates = array();

        foreach ($camposEditaveis as $coluna) {
            $field = $coluna['Field'];
            if (rhCampoEhDataCadastro($field)) {
                continue;
            }
            if (isset($_POST[$field])) {
                $registroAtual[$field] = $_POST[$field];
            }
        }
        if (!empty($idRegistro) && !empty($chavePrimaria)) {
            $registroAtual[$chavePrimaria] = $idRegistro;
        }

        if (!empty($campoCpf)) {
            $cpfInformado = isset($_POST[$campoCpf]) ? rhSomenteNumeros($_POST[$campoCpf]) : '';
            if ($cpfInformado === '') {
                $erro = 'Informe o CPF do candidato.';
            } else if (!rhCpfValido($cpfInformado)) {
                $erro = 'CPF invalido.';
            } else {
                $filtroCpf = "REPLACE(REPLACE(REPLACE(REPLACE(COALESCE({$campoCpf}, ''), '.', ''), '-', ''), '/', ''), ' ', '') = " . rhEscaparSql($mysqli, $cpfInformado);
                $sqlCpf = "SELECT {$chavePrimaria} FROM {$tabela} WHERE {$filtroCpf}";
                if (!empty($idRegistro) && !empty($chavePrimaria)) {
                    $sqlCpf .= " AND {$chavePrimaria} <> " . rhEscaparSql($mysqli, $idRegistro);
                }
                $sqlCpf .= " LIMIT 1";
                $resCpf = mysqli_query($mysqli, $sqlCpf);

                if (!$resCpf) {
                    $erro = 'Nao foi possivel validar o CPF informado. ' . mysqli_error($mysqli);
                } else if (mysqli_fetch_assoc($resCpf)) {
                    $erro = 'Ja existe um candidato cadastrado com este CPF.';
                }
            }
        }

        if (empty($erro)) {
            foreach ($camposEditaveis as $coluna) {
                $field = $coluna['Field'];

                if (rhCampoEhDataCadastro($field)) {
                    if (!empty($idRegistro)) {
                        continue;
                    }
                    $valorBanco = date('Y-m-d');
                } else {
                    $valorBanco = isset($_POST[$field]) ? rhValorBanco($_POST[$field], $coluna['Type']) : '';
                }

                $campos[] = $field;
                $valores[] = rhEscaparSql($mysqli, $valorBanco);
                $updates[] = "{$field} = " . rhEscaparSql($mysqli, $valorBanco);
                $registroAtual[$field] = rhValorFormulario($valorBanco, $coluna['Type']);
            }

            if (!empty($idRegistro) && !empty($chavePrimaria)) {
                $sqlSalvar = "UPDATE {$tabela} SET " . implode(', ', $updates) . " WHERE {$chavePrimaria} = " . rhEscaparSql($mysqli, $idRegistro);
                $salvou = mysqli_query($mysqli, $sqlSalvar);
                $registroSalvoId = $idRegistro;
            } else {
                $sqlSalvar = "INSERT INTO {$tabela} (" . implode(', ', $campos) . ") VALUES (" . implode(', ', $valores) . ")";
                $salvou = mysqli_query($mysqli, $sqlSalvar);
                $registroSalvoId = $salvou ? mysqli_insert_id($mysqli) : '';
            }

            if ($salvou) {
                $foto = rhSalvarFotoCandidato($confUsuario, $registroSalvoId);
                if (!$foto['code']) {
                    $erro = $foto['data'];
                } else {
                    $mensagem = 'Cadastro salvo com sucesso.';
                    if (!empty($chavePrimaria)) {
                        $registroAtual[$chavePrimaria] = $registroSalvoId;
                    }
                }
            } else {
                $erro = 'Nao foi possivel salvar o cadastro do candidato. ' . mysqli_error($mysqli);
            }
        }
    }

    if ($acao === 'excluir' && !empty($chavePrimaria) && !empty($_POST['registro_id'])) {
        $registroId = $_POST['registro_id'];
        $sqlExcluir = "DELETE FROM {$tabela} WHERE {$chavePrimaria} = " . rhEscaparSql($mysqli, $registroId);
        if (mysqli_query($mysqli, $sqlExcluir)) {
            $dirFoto = rhDiretorioFotoFisico($confUsuario, $registroId);
            if (is_dir($dirFoto)) {
                foreach (glob($dirFoto . '/*.*') as $arquivoFoto) {
                    @unlink($arquivoFoto);
                }
            }
            $mensagem = 'Cadastro excluido com sucesso.';
            $registroAtual = array();
        } else {
            $erro = 'Nao foi possivel excluir o cadastro do candidato. ' . mysqli_error($mysqli);
        }
    }
}

if (empty($erro) && !empty($chavePrimaria) && !empty($_GET['editar'])) {
    $registroId = $_GET['editar'];
    $sqlEditar = "SELECT * FROM {$tabela} WHERE {$chavePrimaria} = " . rhEscaparSql($mysqli, $registroId) . " LIMIT 1";
    $res = mysqli_query($mysqli, $sqlEditar);
    if ($res) {
        $registroAtual = mysqli_fetch_assoc($res);
    }
}

$fotoAtual = false;
if (!empty($registroAtual) && !empty($chavePrimaria) && !empty($registroAtual[$chavePrimaria])) {
    $fotoAtual = rhBuscarFotoCandidato($confUsuario, $registroAtual[$chavePrimaria]);
}

$campoNomePrincipal = rhCampoNomePrincipal($colunas);
$registros = array();
if ($conexaoOk && empty($erro) && !empty($chavePrimaria)) {
    $res = mysqli_query($mysqli, "SELECT * FROM {$tabela} ORDER BY {$chavePrimaria} DESC LIMIT 30");
    while ($res && ($row = mysqli_fetch_assoc($res))) {
        $registros[] = $row;
    }
}

$cidadesJson = array();
foreach ($cidades as $cidade) {
    $estadoId = $cidade['D020_D018_Id'];
    if (!isset($cidadesJson[$estadoId])) {
        $cidadesJson[$estadoId] = array();
    }
    $cidadesJson[$estadoId][] = array('id' => $cidade['D020_Id'], 'nome' => $cidade['D020_Nome_Cidade']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>RH - Cadastro de Candidato</title>
    <style>
        :root { --bg-start:#f6f0e7; --bg-end:#eadbc8; --sidebar-top:#6e3f12; --sidebar-bottom:#9a5b1f; --card:rgba(255,255,255,.96); --primary:#9a5b1f; --primary-dark:#744311; --text:#352313; --muted:#705a45; --border:#eadbc8; --danger:#b54033; --success:#2c7c4c; --shadow:0 26px 60px rgba(86,49,16,.14); }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; font-family:"Segoe UI",Arial,sans-serif; color:var(--text); background:radial-gradient(circle at top right,rgba(255,255,255,.92),transparent 30%),linear-gradient(135deg,var(--bg-start),var(--bg-end)); }
        .shell { min-height:100dvh; display:grid; grid-template-columns:clamp(210px, 18vw, 240px) minmax(0,1fr); align-items:stretch; }
        .sidebar { padding:16px 14px; background:linear-gradient(180deg,var(--sidebar-top),var(--sidebar-bottom)); color:#fff7ef; min-height:100%; }
        .sidebar h1 { margin:0 0 6px; font-size:24px; }
        .sidebar p { margin:0 0 20px; line-height:1.5; color:rgba(255,247,239,.84); font-size:14px; }
        .menu { display:grid; gap:8px; }
        .menu a { display:block; padding:12px 14px; border-radius:12px; color:#fff; text-decoration:none; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); }
        .menu a.active,.menu a:hover { background:rgba(255,255,255,.16); }
        .content { padding:16px; display:grid; gap:14px; align-content:start; min-width:0; }
        .card { width:min(100%, 1180px); background:var(--card); border-radius:22px; padding:18px; box-shadow:var(--shadow); }
        .eyebrow { margin:0 0 8px; color:var(--primary); font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
        h3 { margin:0 0 10px; font-size:22px; }
        p { margin:0; line-height:1.6; color:var(--muted); }
        .alert { padding:14px 16px; border-radius:14px; margin-bottom:16px; font-weight:600; white-space:pre-wrap; }
        .alert.success { background:#ecf8f0; color:var(--success); }
        .alert.error { background:#fbeeed; color:var(--danger); }
        .layout { display:grid; grid-template-columns:minmax(180px, 220px) minmax(0,1fr); gap:18px; align-items:start; min-width:0; }
        .photo-panel { display:grid; gap:12px; align-content:start; position:sticky; top:20px; }
        .photo-frame { width:100%; aspect-ratio:4 / 5; border-radius:18px; background:linear-gradient(180deg,rgba(154,91,31,.08),rgba(243,195,140,.2)),#fff; border:1px dashed rgba(154,91,31,.25); display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .photo-frame img { width:100%; height:100%; object-fit:cover; }
        .photo-placeholder { padding:18px; text-align:center; color:var(--muted); line-height:1.5; font-size:14px; }
        .field-grid { display:grid; grid-template-columns:repeat(12, minmax(0, 1fr)); gap:12px; align-items:start; }
        .field { display:grid; gap:6px; min-width:0; }
        .span-2 { grid-column:span 2; }
        .span-3 { grid-column:span 3; }
        .span-4 { grid-column:span 4; }
        .span-5 { grid-column:span 5; }
        .span-6 { grid-column:span 6; }
        .span-8 { grid-column:span 8; }
        .span-12 { grid-column:1 / -1; }
        label { font-size:13px; font-weight:700; }
        input,textarea,select { width:100%; min-width:0; border:1px solid var(--border); border-radius:14px; padding:12px 13px; font-size:14px; line-height:1.2; color:var(--text); background:#fffdf9; }
        input[type="file"] { padding:10px 12px; }
        textarea { min-height:110px; resize:vertical; }
        .hint { font-size:12px; color:var(--muted); }
        .actions { margin-top:16px; display:flex; gap:10px; flex-wrap:wrap; }
        .btn { border:0; border-radius:14px; padding:11px 15px; cursor:pointer; font-weight:700; text-decoration:none; }
        .btn.primary { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; }
        .btn.secondary { background:#fff; color:var(--text); border:1px solid var(--border); }
        .list-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; }
        .table-wrap { overflow:auto; }
        table { width:100%; border-collapse:collapse; min-width:860px; }
        th,td { padding:12px 10px; border-bottom:1px solid var(--border); text-align:left; vertical-align:middle; font-size:13px; }
        th { color:var(--primary); white-space:nowrap; }
        .mini-photo { width:42px; height:42px; border-radius:12px; overflow:hidden; background:#f5eee5; display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:11px; text-align:center; }
        .mini-photo img { width:100%; height:100%; object-fit:cover; }
        .mini-actions { display:flex; gap:10px; }
        .mini-actions a,.mini-actions button { border:0; background:transparent; color:var(--primary); font-weight:700; cursor:pointer; padding:0; }
        .section-title { margin-bottom:16px; }
        @media (max-width:1280px) {
            .card { width:100%; }
            .field-grid { grid-template-columns:repeat(8, minmax(0, 1fr)); }`r`n            .layout { grid-template-columns:190px minmax(0,1fr); }
            .span-2 { grid-column:span 2; }
            .span-3 { grid-column:span 2; }
            .span-4 { grid-column:span 4; }
            .span-5 { grid-column:span 4; }
            .span-6 { grid-column:span 4; }
            .span-8 { grid-column:1 / -1; }
        }
        @media (max-width:980px) {
            .shell { grid-template-columns:1fr; }
            .sidebar { padding:16px; }
            .content { padding:16px; }
            .layout { grid-template-columns:1fr; }
            .photo-panel { position:static; grid-template-columns:160px minmax(0,1fr); align-items:start; }
            .photo-frame { max-width:160px; }
        }
        @media (max-width:720px) {
            .content { padding:12px; }
            .card { padding:16px; border-radius:18px; }
            .section-title { margin-bottom:12px; }
            h3 { font-size:19px; }
            .photo-panel { grid-template-columns:1fr; }
            .photo-frame { max-width:180px; }
            .field-grid { grid-template-columns:1fr; gap:12px; }
            .span-2,.span-3,.span-4,.span-5,.span-6,.span-8,.span-12 { grid-column:1 / -1; }
            input,textarea,select { font-size:16px; padding:12px 13px; }
            .actions { flex-direction:column; }
            .btn { width:100%; text-align:center; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <h1>RH</h1>
            <p></p>
            <nav class="menu">
                <a href="./painel.php">Painel inicial</a>
                <a href="./entrevista_smt207.php">Entrevista</a>
                <a href="./lista_entrevistas.php">Lista de Entrevistas</a>
            </nav>
        </aside>

        <main class="content">
            <section class="card">
                <?php if (!empty($mensagem)) { ?><div class="alert success"><?php echo rhEscapar($mensagem); ?></div><?php } ?>
                <?php if (!empty($erro)) { ?><div class="alert error"><?php echo rhEscapar($erro); ?></div><?php } ?>

                <div class="section-title">
                    <p class="eyebrow">Formulario</p>
                    <h3><?php echo !empty($registroAtual) ? 'Editar candidato' : 'Novo candidato'; ?></h3>
                </div>

                <form method="post" enctype="multipart/form-data" action="./cadastro_smt206.php<?php echo (!empty($registroAtual) && !empty($chavePrimaria) && !empty($registroAtual[$chavePrimaria])) ? '?editar=' . urlencode($registroAtual[$chavePrimaria]) : ''; ?>">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="registro_id" value="<?php echo (!empty($registroAtual) && !empty($chavePrimaria) && !empty($registroAtual[$chavePrimaria])) ? rhEscapar($registroAtual[$chavePrimaria]) : ''; ?>">

                    <div class="layout">
                        <aside class="photo-panel">
                            <div class="photo-frame">
                                <?php if ($fotoAtual) { ?><img src="<?php echo rhEscapar($fotoAtual['url']); ?>" alt="Foto do candidato"><?php } else { ?><div class="photo-placeholder">Foto do candidato<br>A imagem enviada aparece aqui depois do salvamento.</div><?php } ?>
                            </div>
                            <div class="field">
                                <label for="foto_candidato">Foto do candidato</label>
                                <input id="foto_candidato" name="foto_candidato" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
                                <span class="hint">Use uma foto frontal em JPG ou PNG para melhor resultado.</span>
                            </div>
                        </aside>

                        <section>
                            <div class="field-grid">
                                <?php foreach ($camposEditaveis as $coluna) { ?>
                                    <?php
                                    $field = $coluna['Field'];
                                    $tipo = strtolower($coluna['Type']);
                                    if (rhCampoEhDataCadastro($field)) {
                                        continue;
                                    }
                                    $valor = isset($registroAtual[$field]) ? rhValorFormulario($registroAtual[$field], $coluna['Type']) : '';
                                    $isTextarea = rhCampoEhTextarea($tipo, $field);
                                    $isDate = rhCampoEhData($tipo);
                                    $isCpf = rhCampoEhCpf($field);
                                    $classe = rhClasseCampoLayout($field, $tipo);
                                    ?>
                                    <div class="<?php echo $classe; ?>">
                                        <label for="<?php echo rhEscapar($field); ?>"><?php echo rhEscapar(rhTituloCampo($field)); ?></label>
                                        <?php if ($field === 'SMT206_D018_Id') { ?>
                                            <select id="<?php echo rhEscapar($field); ?>" name="<?php echo rhEscapar($field); ?>">
                                                <option value="">Selecione</option>
                                                <?php foreach ($estados as $estado) { ?><option value="<?php echo rhEscapar($estado['value']); ?>" <?php echo ((string) $valor === (string) $estado['value']) ? 'selected' : ''; ?>><?php echo rhEscapar($estado['label']); ?></option><?php } ?>
                                            </select>
                                        <?php } else if ($field === 'SMT206_D020_Id') { ?>
                                            <select id="<?php echo rhEscapar($field); ?>" name="<?php echo rhEscapar($field); ?>"><option value="">Selecione</option></select>
                                        <?php } else if ($field === 'SMT206_D030_Id') { ?>
                                            <select id="<?php echo rhEscapar($field); ?>" name="<?php echo rhEscapar($field); ?>">
                                                <option value="">Selecione</option>
                                                <?php foreach ($paises as $pais) { ?><option value="<?php echo rhEscapar($pais['value']); ?>" <?php echo ((string) $valor === (string) $pais['value']) ? 'selected' : ''; ?>><?php echo rhEscapar($pais['label']); ?></option><?php } ?>
                                            </select>
                                        <?php } else if ($isTextarea) { ?>
                                            <textarea id="<?php echo rhEscapar($field); ?>" name="<?php echo rhEscapar($field); ?>"><?php echo rhEscapar($valor); ?></textarea>
                                        <?php } else { ?>
                                            <input id="<?php echo rhEscapar($field); ?>" name="<?php echo rhEscapar($field); ?>" type="<?php echo (stripos($field, 'email') !== false) ? 'email' : 'text'; ?>" value="<?php echo rhEscapar($valor); ?>" placeholder="<?php echo $isDate ? 'dd/mm/aaaa' : ($isCpf ? '000.000.000-00' : ''); ?>" <?php echo $isCpf ? 'data-mask="cpf" required' : ''; ?> <?php echo ($field === $chavePrimaria) ? 'readonly' : ''; ?>>
                                            <?php if ($isCpf) { ?><span class="hint" data-cpf-feedback></span><?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="actions">
                                <button class="btn primary" type="submit">Salvar cadastro</button>
                                <a class="btn secondary" href="./cadastro_smt206.php">Novo</a>
                                <a class="btn secondary" href="./painel.php">Voltar ao painel</a>
                            </div>
                        </section>
                    </div>
                </form>
            </section>

            <section class="card">
                <div class="list-header">
                    <div>
                        <p class="eyebrow">Listagem</p>
                        <h3>Ultimos candidatos cadastrados</h3>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th><?php echo rhEscapar(!empty($campoNomePrincipal) ? rhTituloCampo($campoNomePrincipal) : 'Registro'); ?></th>
                                <?php $headerCount = 0; ?>
                                <?php foreach ($colunas as $coluna) { ?>
                                    <?php if ($coluna['Field'] === $chavePrimaria || $coluna['Field'] === $campoNomePrincipal) { continue; } ?>
                                    <?php if (count($colunas) > 8 && stripos($coluna['Field'], 'observa') !== false) { continue; } ?>
                                    <th><?php echo rhEscapar(rhTituloCampo($coluna['Field'])); ?></th>
                                    <?php if (++$headerCount >= 4) { break; } ?>
                                <?php } ?>
                                <th>Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($registros)) { ?>
                                <?php foreach ($registros as $linha) { ?>
                                    <?php $fotoLinha = rhBuscarFotoCandidato($confUsuario, $linha[$chavePrimaria]); ?>
                                    <tr>
                                        <td><div class="mini-photo"><?php if ($fotoLinha) { ?><img src="<?php echo rhEscapar($fotoLinha['url']); ?>" alt="Foto"><?php } else { ?>Sem foto<?php } ?></div></td>
                                        <td><?php echo rhEscapar(!empty($campoNomePrincipal) ? $linha[$campoNomePrincipal] : $linha[$chavePrimaria]); ?></td>
                                        <?php $countLinha = 0; ?>
                                        <?php foreach ($colunas as $coluna) { ?>
                                            <?php
                                            $field = $coluna['Field'];
                                            if ($field === $chavePrimaria || $field === $campoNomePrincipal) { continue; }
                                            if (count($colunas) > 8 && stripos($field, 'observa') !== false) { continue; }
                                            $valorLinha = isset($linha[$field]) ? $linha[$field] : '';
                                            if ($field === 'SMT206_D018_Id') {
                                                foreach ($estados as $estado) {
                                                    if ((string) $estado['value'] === (string) $valorLinha) { $valorLinha = $estado['label']; break; }
                                                }
                                            } else if ($field === 'SMT206_D020_Id') {
                                                foreach ($cidades as $cidade) {
                                                    if ((string) $cidade['D020_Id'] === (string) $valorLinha) { $valorLinha = $cidade['D020_Nome_Cidade']; break; }
                                                }
                                            } else if ($field === 'SMT206_D030_Id') {
                                                foreach ($paises as $pais) {
                                                    if ((string) $pais['value'] === (string) $valorLinha) { $valorLinha = $pais['label']; break; }
                                                }
                                            } else if (rhCampoEhData($coluna['Type'])) {
                                                $valorLinha = rhValorFormulario($valorLinha, $coluna['Type']);
                                            }
                                            ?>
                                            <td><?php echo rhEscapar(substr((string) $valorLinha, 0, 80)); ?></td>
                                            <?php if (++$countLinha >= 4) { break; } ?>
                                        <?php } ?>
                                        <td>
                                            <div class="mini-actions">
                                                <a href="./cadastro_smt206.php?editar=<?php echo urlencode($linha[$chavePrimaria]); ?>">Editar</a>
                                                <form method="post" action="./cadastro_smt206.php" onsubmit="return confirm('Deseja excluir este candidato?');">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="registro_id" value="<?php echo rhEscapar($linha[$chavePrimaria]); ?>">
                                                    <button type="submit">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="7">Nenhum candidato encontrado na tabela SMT206.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        (function () {
            var cidadesPorEstado = <?php echo json_encode($cidadesJson); ?>;
            var estado = document.getElementById('SMT206_D018_Id');
            var cidade = document.getElementById('SMT206_D020_Id');
            var cidadeSelecionada = <?php echo json_encode(isset($registroAtual['SMT206_D020_Id']) ? (string) $registroAtual['SMT206_D020_Id'] : ''); ?>;

            function renderizarCidades() {
                if (!estado || !cidade) return;
                var estadoId = estado.value;
                var opcoes = cidadesPorEstado[estadoId] || [];
                cidade.innerHTML = '';
                var primeira = document.createElement('option');
                primeira.value = '';
                primeira.textContent = 'Selecione';
                cidade.appendChild(primeira);
                for (var i = 0; i < opcoes.length; i++) {
                    var option = document.createElement('option');
                    option.value = opcoes[i].id;
                    option.textContent = opcoes[i].nome;
                    if (cidadeSelecionada && String(opcoes[i].id) === String(cidadeSelecionada)) option.selected = true;
                    cidade.appendChild(option);
                }
            }

            function mascararData(input) {
                input.addEventListener('input', function () {
                    var valor = input.value.replace(/\D/g, '').slice(0, 8);
                    if (valor.length > 4) valor = valor.replace(/(\d{2})(\d{2})(\d{1,4})/, '$1/$2/$3');
                    else if (valor.length > 2) valor = valor.replace(/(\d{2})(\d{1,2})/, '$1/$2');
                    input.value = valor;
                });
            }

            function mascararCpf(input) {
                input.addEventListener('input', function () {
                    var valor = input.value.replace(/\D/g, '').slice(0, 11);
                    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                    input.value = valor;
                });
            }

            function cpfValidoJs(cpf) {
                cpf = cpf.replace(/\D/g, '');
                if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
                for (var t = 9; t < 11; t++) {
                    var soma = 0;
                    for (var i = 0; i < t; i++) soma += parseInt(cpf.charAt(i), 10) * ((t + 1) - i);
                    var resto = (soma * 10) % 11;
                    if (resto === 10) resto = 0;
                    if (resto !== parseInt(cpf.charAt(t), 10)) return false;
                }
                return true;
            }

            function configurarValidacaoCpf(input) {
                var campo = input.closest('.field');
                var feedback = campo ? campo.querySelector('[data-cpf-feedback]') : null;
                if (!feedback) return;

                var registroIdInput = document.querySelector('input[name="registro_id"]');
                var timer = null;
                var ultimaConsulta = '';

                function definirFeedback(texto, cor) {
                    feedback.textContent = texto;
                    feedback.style.color = cor || '';
                    input.setCustomValidity(cor === '#b54033' ? texto : '');
                }

                function verificar() {
                    var digitos = input.value.replace(/\D/g, '');

                    if (digitos.length === 0) {
                        definirFeedback('', '');
                        return;
                    }

                    if (digitos.length < 11) {
                        definirFeedback('CPF incompleto.', '#b54033');
                        return;
                    }

                    if (!cpfValidoJs(digitos)) {
                        definirFeedback('CPF invalido.', '#b54033');
                        return;
                    }

                    definirFeedback('Verificando...', '#705a45');
                    ultimaConsulta = digitos;

                    var params = new URLSearchParams();
                    params.set('verificar_cpf', digitos);
                    params.set('registro_id', registroIdInput ? registroIdInput.value : '');

                    fetch('./cadastro_smt206.php?' + params.toString())
                        .then(function (resp) { return resp.json(); })
                        .then(function (data) {
                            if (ultimaConsulta !== input.value.replace(/\D/g, '')) return;
                            if (data.existe) {
                                definirFeedback(data.mensagem || 'Ja existe um candidato cadastrado com este CPF.', '#b54033');
                            } else if (!data.valido) {
                                definirFeedback(data.mensagem || 'CPF invalido.', '#b54033');
                            } else {
                                definirFeedback('CPF disponivel.', '#2c7c4c');
                            }
                        })
                        .catch(function () {
                            definirFeedback('', '');
                        });
                }

                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(verificar, 400);
                });

                if (input.value) verificar();
            }

            if (estado && cidade) {
                estado.addEventListener('change', function () {
                    cidadeSelecionada = '';
                    renderizarCidades();
                });
                renderizarCidades();
            }

            var camposData = document.querySelectorAll('input[placeholder="dd/mm/aaaa"]');
            for (var i = 0; i < camposData.length; i++) mascararData(camposData[i]);

            var camposCpf = document.querySelectorAll('input[data-mask="cpf"]');
            for (var j = 0; j < camposCpf.length; j++) {
                mascararCpf(camposCpf[j]);
                configurarValidacaoCpf(camposCpf[j]);
            }
        })();
    </script>
</body>
</html>
