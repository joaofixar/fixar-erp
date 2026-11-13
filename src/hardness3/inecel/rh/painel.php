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

$mensagem = '';
$erro = '';
$mesesPtBr = array(
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'marco',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro'
);
$diasSemanaPtBr = array('Segunda-feira', 'Terca-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira');
$agoraTimestamp = time();
$hojeIso = date('Y-m-d', $agoraTimestamp);

$indicadores = array(
    'entrevistas_mes' => 0,
    'aprovadas_mes' => 0,
    'reprovadas_mes' => 0
);
$resumoMes = 'Mes atual';
$linkDashboard = './lista_entrevistas.php';
$linkAprovadas = './lista_entrevistas.php';
$linkReprovadas = './lista_entrevistas.php';

$mesAtual = isset($_GET['mes']) ? preg_replace('/[^0-9\-]/', '', (string) $_GET['mes']) : date('Y-m');
if (!preg_match('/^\d{4}\-\d{2}$/', $mesAtual)) {
    $mesAtual = date('Y-m');
}

$dataBaseCalendario = strtotime($mesAtual . '-01');
if (!$dataBaseCalendario) {
    $dataBaseCalendario = strtotime(date('Y-m-01'));
    $mesAtual = date('Y-m', $dataBaseCalendario);
}

$mesAnterior = date('Y-m', strtotime('-1 month', $dataBaseCalendario));
$mesSeguinte = date('Y-m', strtotime('+1 month', $dataBaseCalendario));
$tituloCalendario = $mesesPtBr[(int) date('n', $dataBaseCalendario)] . ' de ' . date('Y', $dataBaseCalendario);
$dataFiltroInicioMes = date('Y-m-01', $dataBaseCalendario);
$dataFiltroFimMes = date('Y-m-t', $dataBaseCalendario);
$linkDashboard = './lista_entrevistas.php?data_de=' . urlencode($dataFiltroInicioMes) . '&data_ate=' . urlencode($dataFiltroFimMes);

$agendaForm = array(
    'agenda_id' => '',
    'candidato_label' => '',
    'candidato_id' => '',
    'entrevistador_label' => '',
    'entrevistador_id' => '',
    'data_inicio' => $hojeIso,
    'hora_inicio' => '08:00',
    'data_fim' => $hojeIso,
    'hora_fim' => '08:30'
);

$candidatos = array();
$entrevistadores = array();
$agendas = array();
$horariosDisponiveis = array();

for ($hora = 0; $hora < 24; $hora++) {
    foreach (array('00', '30') as $minuto) {
        $horariosDisponiveis[] = str_pad((string) $hora, 2, '0', STR_PAD_LEFT) . ':' . $minuto;
    }
}

$mysqli = mysqli_init();
$conexaoOk = $mysqli && @mysqli_real_connect(
    $mysqli,
    $confUsuario['dbHost'],
    $confUsuario['dbUser'],
    $confUsuario['dbPass'],
    $confUsuario['dbDatabase']
);

function rhEscaparPainel($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function rhEscaparSqlPainel($mysqli, $valor)
{
    if ($valor === '' || $valor === null) {
        return 'NULL';
    }

    return "'" . mysqli_real_escape_string($mysqli, (string) $valor) . "'";
}

function rhDetectarCampoPainel($colunas, $preferidos, $contens = array())
{
    foreach ($preferidos as $preferido) {
        foreach ($colunas as $coluna) {
            if (strcasecmp($coluna['Field'], $preferido) === 0) {
                return $coluna['Field'];
            }
        }
    }

    foreach ($contens as $trecho) {
        foreach ($colunas as $coluna) {
            if (stripos($coluna['Field'], $trecho) !== false) {
                return $coluna['Field'];
            }
        }
    }

    return '';
}

function rhDetectarChavePrimaria($colunas)
{
    foreach ($colunas as $coluna) {
        if (!empty($coluna['Key']) && strtoupper((string) $coluna['Key']) === 'PRI') {
            return $coluna['Field'];
        }
    }

    return '';
}

function rhMontarValorDataAgenda($data, $tipo)
{
    $data = trim((string) $data);
    if ($data === '') {
        return '';
    }

    $tipo = strtolower((string) $tipo);
    if (strpos($tipo, 'datetime') !== false || strpos($tipo, 'timestamp') !== false) {
        return $data . ' 00:00:00';
    }

    return $data;
}

function rhMontarValorHoraAgenda($hora, $tipo)
{
    $hora = trim((string) $hora);
    if ($hora === '') {
        return '';
    }

    $tipo = strtolower((string) $tipo);
    if (strpos($tipo, 'time') !== false) {
        return $hora . ':00';
    }

    return $hora;
}

function rhMontarDataHoraAgenda($data, $hora, $tipo)
{
    $data = trim((string) $data);
    $hora = trim((string) $hora);
    if ($data === '') {
        return '';
    }

    $tipo = strtolower((string) $tipo);
    if (strpos($tipo, 'date') !== false && strpos($tipo, 'time') === false) {
        return $data;
    }

    if ($hora === '') {
        $hora = '00:00';
    }

    return $data . ' ' . $hora . ':00';
}

function rhHorarioFimMaior($dataInicio, $horaInicio, $dataFim, $horaFim)
{
    $inicio = strtotime($dataInicio . ' ' . $horaInicio . ':00');
    $fim = strtotime($dataFim . ' ' . $horaFim . ':00');
    return $inicio !== false && $fim !== false && $fim > $inicio;
}

function rhHorarioNoPassado($dataInicio, $horaInicio)
{
    $inicio = strtotime($dataInicio . ' ' . $horaInicio . ':00');
    return $inicio !== false && $inicio < time();
}

function rhValorHoraTela($valor)
{
    if ($valor === null || $valor === '') {
        return '';
    }

    $texto = trim((string) $valor);
    if ($texto === '' || $texto === '00:00:00' || $texto === '00:00') {
        return '';
    }

    if (preg_match('/^\d{2}\:\d{2}\:\d{2}$/', $texto)) {
        return substr($texto, 0, 5);
    }

    if (preg_match('/^\d{2}\:\d{2}$/', $texto)) {
        return $texto;
    }

    $timestamp = strtotime($texto);
    if ($timestamp) {
        return date('H:i', $timestamp);
    }

    return $texto;
}

function rhValorDataTela($valor)
{
    if ($valor === null || $valor === '') {
        return '';
    }

    $texto = trim((string) $valor);
    if ($texto === '' || $texto === '0000-00-00' || $texto === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($texto);
    if (!$timestamp) {
        return $texto;
    }

    return date('d/m/Y', $timestamp);
}

function rhObterDataAgenda($agenda, $campoData, $campoDataHora)
{
    if (!empty($campoData) && !empty($agenda[$campoData])) {
        return date('Y-m-d', strtotime((string) $agenda[$campoData]));
    }

    if (!empty($campoDataHora) && !empty($agenda[$campoDataHora])) {
        return date('Y-m-d', strtotime((string) $agenda[$campoDataHora]));
    }

    return '';
}

function rhObterHoraAgenda($agenda, $campoHora, $campoDataHora)
{
    if (!empty($campoHora) && !empty($agenda[$campoHora])) {
        return rhValorHoraTela($agenda[$campoHora]);
    }

    if (!empty($campoDataHora) && !empty($agenda[$campoDataHora])) {
        return date('H:i', strtotime((string) $agenda[$campoDataHora]));
    }

    return '';
}

function rhObterTextoHorarioAgenda($agenda, $campoDataInicio, $campoHoraInicio, $campoInicioCompleto, $campoDataFim, $campoHoraFim, $campoFimCompleto)
{
    $horaInicio = rhObterHoraAgenda($agenda, $campoHoraInicio, $campoInicioCompleto);
    $horaFim = rhObterHoraAgenda($agenda, $campoHoraFim, $campoFimCompleto);

    if ($horaInicio !== '' && $horaFim !== '') {
        return $horaInicio . ' - ' . $horaFim;
    }

    if ($horaInicio !== '') {
        return $horaInicio;
    }

    if ($horaFim !== '') {
        return $horaFim;
    }

    return '';
}

function rhObterTextoDataHoraAgenda($agenda, $campoDataInicio, $campoHoraInicio, $campoInicioCompleto, $campoDataFim, $campoHoraFim, $campoFimCompleto)
{
    $dataInicio = rhObterDataAgenda($agenda, $campoDataInicio, $campoInicioCompleto);
    $dataFim = rhObterDataAgenda($agenda, $campoDataFim, $campoFimCompleto);
    $horaInicio = rhObterHoraAgenda($agenda, $campoHoraInicio, $campoInicioCompleto);
    $horaFim = rhObterHoraAgenda($agenda, $campoHoraFim, $campoFimCompleto);

    $texto = '';
    if ($dataInicio !== '') {
        $texto = rhValorDataTela($dataInicio);
    }
    if ($horaInicio !== '') {
        $texto .= ($texto !== '' ? ' ' : '') . $horaInicio;
    }

    if ($dataFim !== '' || $horaFim !== '') {
        $textoFim = '';
        if ($dataFim !== '') {
            $textoFim = rhValorDataTela($dataFim);
        }
        if ($horaFim !== '') {
            $textoFim .= ($textoFim !== '' ? ' ' : '') . $horaFim;
        }
        if ($textoFim !== '') {
            $texto .= ($texto !== '' ? ' ate ' : '') . $textoFim;
        }
    }

    return trim($texto);
}

if (!$conexaoOk) {
    $erro = 'Nao foi possivel conectar ao banco de dados.';
} else {
    mysqli_set_charset($mysqli, 'utf8');

    $colunasSmt207 = array();
    $resCols207 = mysqli_query($mysqli, "SHOW COLUMNS FROM SMT207");
    while ($resCols207 && ($col = mysqli_fetch_assoc($resCols207))) {
        $colunasSmt207[] = $col;
    }

    $campoDataHora207 = rhDetectarCampoPainel($colunasSmt207, array('SMT207_Data_Hora_Entrevista'), array('data_hora_entrevista', 'hora_entrevista'));
    $campoAprovado207 = rhDetectarCampoPainel($colunasSmt207, array('SMT207_Flag_Aprovado'), array('aprov'));

    if (!empty($campoDataHora207)) {
        $sqlIndicadores = "
            SELECT
                COUNT(*) AS entrevistas_mes,
                SUM(CASE WHEN COALESCE({$campoAprovado207}, '') = 'S' THEN 1 ELSE 0 END) AS aprovadas_mes,
                SUM(CASE WHEN COALESCE({$campoAprovado207}, '') = 'N' THEN 1 ELSE 0 END) AS reprovadas_mes,
                DATE_FORMAT(CURDATE(), '%m/%Y') AS mes_referencia
            FROM SMT207
            WHERE {$campoDataHora207} >= DATE_FORMAT(CURDATE(), '%Y-%m-01 00:00:00')
              AND {$campoDataHora207} < DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01 00:00:00')
        ";

        $resIndicadores = mysqli_query($mysqli, $sqlIndicadores);
        if ($resIndicadores && ($row = mysqli_fetch_assoc($resIndicadores))) {
            $indicadores['entrevistas_mes'] = (int) $row['entrevistas_mes'];
            $indicadores['aprovadas_mes'] = (int) $row['aprovadas_mes'];
            $indicadores['reprovadas_mes'] = (int) $row['reprovadas_mes'];
            $resumoMes = !empty($row['mes_referencia']) ? $row['mes_referencia'] : $resumoMes;
        }
    }

    if (!empty($campoAprovado207)) {
        $linkAprovadas = './lista_entrevistas.php?data_de=' . urlencode($dataFiltroInicioMes) . '&data_ate=' . urlencode($dataFiltroFimMes) . '&resultado=S';
        $linkReprovadas = './lista_entrevistas.php?data_de=' . urlencode($dataFiltroInicioMes) . '&data_ate=' . urlencode($dataFiltroFimMes) . '&resultado=N';
    }

    $colunasSmt208 = array();
    $resCols208 = mysqli_query($mysqli, "SHOW COLUMNS FROM SMT208");
    while ($resCols208 && ($col = mysqli_fetch_assoc($resCols208))) {
        $colunasSmt208[] = $col;
    }

    $campoAgendaPk = rhDetectarChavePrimaria($colunasSmt208);
    $campoAgendaCandidato = rhDetectarCampoPainel($colunasSmt208, array('SMT208_SMT206_Id', 'SMT208_Candidato_Id', 'SMT208_Cadastro_Candidato_Id', 'SMT208_RH_Candidato_Id'), array('206', 'candidato'));
    $campoAgendaEntrevistador = rhDetectarCampoPainel($colunasSmt208, array('SMT208_C007_Id', 'SMT208_Entrevistador_C007_Id', 'SMT208_C007_Id_Entrevistador'), array('c007', 'entrevistador'));
    $campoAgendaDataInicio = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Data_Inicio'), array('data_inicio'));
    $campoAgendaHoraInicio = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Hora_Inicio'), array('hora_inicio'));
    $campoAgendaDataFim = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Data_Fim'), array('data_fim'));
    $campoAgendaHoraFim = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Hora_Fim'), array('hora_fim'));
    $campoAgendaInicioCompleto = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Data_Hora_Inicio', 'SMT208_Data_Hora_Agenda', 'SMT208_Data_Hora', 'SMT208_Data_Entrevista', 'SMT208_Data'), array('data_hora_inicio', 'data_hora', 'agenda'));
    $campoAgendaFimCompleto = rhDetectarCampoPainel($colunasSmt208, array('SMT208_Data_Hora_Fim'), array('data_hora_fim'));

    $temInicioSeparado = !empty($campoAgendaDataInicio) || !empty($campoAgendaHoraInicio);
    $temFimSeparado = !empty($campoAgendaDataFim) || !empty($campoAgendaHoraFim);

    $campoCandidatoPk = '';
    $campoCandidatoNome = '';
    $campoCandidatoCpf = '';
    $colunasCandidato = array();
    $resColsCand = mysqli_query($mysqli, "SHOW COLUMNS FROM SMT206");
    while ($resColsCand && ($col = mysqli_fetch_assoc($resColsCand))) {
        $colunasCandidato[] = $col;
        if ($col['Key'] === 'PRI' && empty($campoCandidatoPk)) {
            $campoCandidatoPk = $col['Field'];
        }
    }
    $campoCandidatoNome = rhDetectarCampoPainel($colunasCandidato, array('SMT206_Nome_Candidato', 'SMT206_Nome'), array('nome'));
    $campoCandidatoCpf = rhDetectarCampoPainel($colunasCandidato, array('SMT206_CPF', 'SMT206_Cpf'), array('cpf'));

    if (!empty($campoCandidatoPk) && !empty($campoCandidatoNome)) {
        $sqlCandidatos = "SELECT {$campoCandidatoPk} AS id, {$campoCandidatoNome} AS nome" . (!empty($campoCandidatoCpf) ? ", {$campoCandidatoCpf} AS cpf" : ", '' AS cpf") . " FROM SMT206 ORDER BY {$campoCandidatoNome}";
        $resCandidatos = mysqli_query($mysqli, $sqlCandidatos);
        while ($resCandidatos && ($row = mysqli_fetch_assoc($resCandidatos))) {
            $label = trim((string) $row['nome']);
            if (!empty($row['cpf'])) {
                $label .= ' - CPF ' . $row['cpf'];
            }
            $label .= ' - #' . $row['id'];
            $candidatos[] = array('id' => $row['id'], 'nome' => $row['nome'], 'cpf' => $row['cpf'], 'label' => $label);
        }
    }

    $temPrimeiroNome = false;
    $resColsC007 = mysqli_query($mysqli, "SHOW COLUMNS FROM C007");
    while ($resColsC007 && ($col = mysqli_fetch_assoc($resColsC007))) {
        if (strcasecmp($col['Field'], 'C007_Primeiro_Nome') === 0) {
            $temPrimeiroNome = true;
            break;
        }
    }
    $campoNomeC007 = $temPrimeiroNome ? 'C007_Primeiro_Nome' : 'C007_Nome';

    $sqlEntrevistadores = "SELECT C007_Id AS id, {$campoNomeC007} AS nome, C007_Nome AS nome_completo FROM C007 WHERE COALESCE(C007_Nome, '') <> '' ORDER BY {$campoNomeC007}";
    $resEntrevistadores = mysqli_query($mysqli, $sqlEntrevistadores);
    while ($resEntrevistadores && ($row = mysqli_fetch_assoc($resEntrevistadores))) {
        $label = trim((string) $row['nome']);
        if (!empty($row['nome_completo']) && $row['nome_completo'] !== $row['nome']) {
            $label .= ' - ' . $row['nome_completo'];
        }
        $label .= ' - #' . $row['id'];
        $entrevistadores[] = array('id' => $row['id'], 'nome' => $row['nome'], 'label' => $label);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar_agenda') {
        $agendaForm['agenda_id'] = isset($_POST['agenda_id']) ? trim((string) $_POST['agenda_id']) : '';
        $agendaForm['candidato_label'] = isset($_POST['agenda_candidato_label']) ? trim((string) $_POST['agenda_candidato_label']) : '';
        $agendaForm['candidato_id'] = isset($_POST['agenda_candidato_id']) ? trim((string) $_POST['agenda_candidato_id']) : '';
        $agendaForm['entrevistador_label'] = isset($_POST['agenda_entrevistador_label']) ? trim((string) $_POST['agenda_entrevistador_label']) : '';
        $agendaForm['entrevistador_id'] = isset($_POST['agenda_entrevistador_id']) ? trim((string) $_POST['agenda_entrevistador_id']) : '';
        $agendaForm['data_inicio'] = isset($_POST['agenda_data_inicio']) ? trim((string) $_POST['agenda_data_inicio']) : '';
        $agendaForm['hora_inicio'] = isset($_POST['agenda_hora_inicio']) ? trim((string) $_POST['agenda_hora_inicio']) : '';
        $agendaForm['data_fim'] = isset($_POST['agenda_data_fim']) ? trim((string) $_POST['agenda_data_fim']) : '';
        $agendaForm['hora_fim'] = isset($_POST['agenda_hora_fim']) ? trim((string) $_POST['agenda_hora_fim']) : '';

        if (empty($campoAgendaCandidato) || empty($campoAgendaEntrevistador)) {
            $erro = 'Nao foi possivel identificar os campos principais da tabela SMT208 para gravar a agenda.';
        } elseif (empty($campoAgendaDataInicio) && empty($campoAgendaInicioCompleto)) {
            $erro = 'Nao foi possivel identificar o campo de data de inicio da agenda na SMT208.';
        } elseif (empty($campoAgendaDataFim) && empty($campoAgendaFimCompleto)) {
            $erro = 'Nao foi possivel identificar o campo de data de fim da agenda na SMT208.';
        } elseif ($agendaForm['candidato_id'] === '') {
            $erro = 'Selecione um candidato para a agenda.';
        } elseif ($agendaForm['entrevistador_id'] === '') {
            $erro = 'Selecione um entrevistador para a agenda.';
        } elseif ($agendaForm['data_inicio'] === '' || $agendaForm['data_fim'] === '') {
            $erro = 'Informe data de inicio e data de fim.';
        } elseif ((int) date('N', strtotime($agendaForm['data_inicio'])) >= 6 || (int) date('N', strtotime($agendaForm['data_fim'])) >= 6) {
            $erro = 'Nao e permitido agendar entrevistas para sabado ou domingo.';
        } elseif ($agendaForm['hora_inicio'] === '' || $agendaForm['hora_fim'] === '') {
            $erro = 'Informe hora de inicio e hora de fim.';
        } elseif (rhHorarioNoPassado($agendaForm['data_inicio'], $agendaForm['hora_inicio'])) {
            $erro = 'Nao e permitido gravar agenda no passado.';
        } elseif (!rhHorarioFimMaior($agendaForm['data_inicio'], $agendaForm['hora_inicio'], $agendaForm['data_fim'], $agendaForm['hora_fim'])) {
            $erro = 'A data e hora final precisam ser maiores que a data e hora inicial.';
        } else {
            $camposSet = array();
            $camposInsert = array();
            $valoresInsert = array();

            foreach ($colunasSmt208 as $coluna) {
                $field = $coluna['Field'];
                if (strtolower((string) $coluna['Extra']) === 'auto_increment') {
                    continue;
                }

                $valorBanco = '';
                if ($field === $campoAgendaCandidato) {
                    $valorBanco = $agendaForm['candidato_id'];
                } elseif ($field === $campoAgendaEntrevistador) {
                    $valorBanco = $agendaForm['entrevistador_id'];
                } elseif (!empty($campoAgendaDataInicio) && $field === $campoAgendaDataInicio) {
                    $valorBanco = rhMontarValorDataAgenda($agendaForm['data_inicio'], $coluna['Type']);
                } elseif (!empty($campoAgendaHoraInicio) && $field === $campoAgendaHoraInicio) {
                    $valorBanco = rhMontarValorHoraAgenda($agendaForm['hora_inicio'], $coluna['Type']);
                } elseif (!empty($campoAgendaDataFim) && $field === $campoAgendaDataFim) {
                    $valorBanco = rhMontarValorDataAgenda($agendaForm['data_fim'], $coluna['Type']);
                } elseif (!empty($campoAgendaHoraFim) && $field === $campoAgendaHoraFim) {
                    $valorBanco = rhMontarValorHoraAgenda($agendaForm['hora_fim'], $coluna['Type']);
                } elseif (!$temInicioSeparado && !empty($campoAgendaInicioCompleto) && $field === $campoAgendaInicioCompleto) {
                    $valorBanco = rhMontarDataHoraAgenda($agendaForm['data_inicio'], $agendaForm['hora_inicio'], $coluna['Type']);
                } elseif (!$temFimSeparado && !empty($campoAgendaFimCompleto) && $field === $campoAgendaFimCompleto) {
                    $valorBanco = rhMontarDataHoraAgenda($agendaForm['data_fim'], $agendaForm['hora_fim'], $coluna['Type']);
                } elseif (stripos($field, 'data_cadastro') !== false && $agendaForm['agenda_id'] === '') {
                    $valorBanco = date('Y-m-d H:i:s');
                } elseif (stripos($field, 'c007') !== false && $field !== $campoAgendaEntrevistador) {
                    $valorBanco = isset($_SESSION['Usuario_Atual']) ? (string) $_SESSION['Usuario_Atual'] : '';
                }

                $valorSql = rhEscaparSqlPainel($mysqli, $valorBanco);
                $camposInsert[] = $field;
                $valoresInsert[] = $valorSql;
                if ($field !== $campoAgendaPk) {
                    $camposSet[] = $field . ' = ' . $valorSql;
                }
            }

            $operacaoEhUpdate = $agendaForm['agenda_id'] !== '' && $campoAgendaPk !== '';
            if ($operacaoEhUpdate) {
                $sqlSalvarAgenda = "UPDATE SMT208 SET " . implode(', ', $camposSet) . " WHERE {$campoAgendaPk} = " . rhEscaparSqlPainel($mysqli, $agendaForm['agenda_id']) . " LIMIT 1";
            } else {
                $sqlSalvarAgenda = "INSERT INTO SMT208 (" . implode(', ', $camposInsert) . ") VALUES (" . implode(', ', $valoresInsert) . ")";
            }

            if (mysqli_query($mysqli, $sqlSalvarAgenda)) {
                $mensagem = $operacaoEhUpdate ? 'Agenda reagendada com sucesso.' : 'Agenda cadastrada com sucesso.';
                $agendaForm = array(
                    'agenda_id' => '',
                    'candidato_label' => '',
                    'candidato_id' => '',
                    'entrevistador_label' => '',
                    'entrevistador_id' => '',
                    'data_inicio' => $hojeIso,
                    'hora_inicio' => '08:00',
                    'data_fim' => $hojeIso,
                    'hora_fim' => '08:30'
                );
            } else {
                $erro = 'Nao foi possivel gravar a agenda. ' . mysqli_error($mysqli);
            }
        }
    }

    if (empty($erro) && (!empty($campoAgendaDataInicio) || !empty($campoAgendaInicioCompleto))) {
        $camposAgendaSelect = array('a.*');
        $joinsAgenda = array();

        if (!empty($campoAgendaCandidato) && !empty($campoCandidatoPk)) {
            $joinsAgenda[] = "LEFT JOIN SMT206 cand ON cand.{$campoCandidatoPk} = a.{$campoAgendaCandidato}";
            if (!empty($campoCandidatoNome)) {
                $camposAgendaSelect[] = "cand.{$campoCandidatoNome} AS candidato_nome";
            }
        }

        if (!empty($campoAgendaEntrevistador)) {
            $joinsAgenda[] = "LEFT JOIN C007 ent ON ent.C007_Id = a.{$campoAgendaEntrevistador}";
            $camposAgendaSelect[] = "ent.{$campoNomeC007} AS entrevistador_nome";
        }

        if (!empty($campoAgendaDataInicio)) {
            $inicioMesSql = date('Y-m-01', $dataBaseCalendario);
            $fimMesSql = date('Y-m-01', strtotime('+1 month', $dataBaseCalendario));
            $sqlAgendas = "
                SELECT " . implode(', ', $camposAgendaSelect) . "
                FROM SMT208 a
                " . implode("\n", $joinsAgenda) . "
                WHERE a.{$campoAgendaDataInicio} >= " . rhEscaparSqlPainel($mysqli, $inicioMesSql) . "
                  AND a.{$campoAgendaDataInicio} < " . rhEscaparSqlPainel($mysqli, $fimMesSql) . "
                ORDER BY a.{$campoAgendaDataInicio}" . (!empty($campoAgendaHoraInicio) ? ", a.{$campoAgendaHoraInicio}" : '') . "
            ";
        } else {
            $inicioMesSql = date('Y-m-01 00:00:00', $dataBaseCalendario);
            $fimMesSql = date('Y-m-01 00:00:00', strtotime('+1 month', $dataBaseCalendario));
            $sqlAgendas = "
                SELECT " . implode(', ', $camposAgendaSelect) . "
                FROM SMT208 a
                " . implode("\n", $joinsAgenda) . "
                WHERE a.{$campoAgendaInicioCompleto} >= " . rhEscaparSqlPainel($mysqli, $inicioMesSql) . "
                  AND a.{$campoAgendaInicioCompleto} < " . rhEscaparSqlPainel($mysqli, $fimMesSql) . "
                ORDER BY a.{$campoAgendaInicioCompleto}
            ";
        }

        $resAgendas = mysqli_query($mysqli, $sqlAgendas);
        while ($resAgendas && ($row = mysqli_fetch_assoc($resAgendas))) {
            $agendas[] = $row;
        }
    }
}

$agendaPorDia = array();
$agendasNaoPassadas = array();
$agoraAgendaTimestamp = time();
foreach ($agendas as $agenda) {
    $dia = rhObterDataAgenda($agenda, $campoAgendaDataInicio, $campoAgendaInicioCompleto);
    if ($dia === '') {
        continue;
    }
    if (!isset($agendaPorDia[$dia])) {
        $agendaPorDia[$dia] = array();
    }
    $agendaPorDia[$dia][] = $agenda;

    $dataComparacao = rhObterDataAgenda($agenda, $campoAgendaDataFim, $campoAgendaFimCompleto);
    if ($dataComparacao === '') {
        $dataComparacao = $dia;
    }
    $horaComparacao = rhObterHoraAgenda($agenda, $campoAgendaHoraFim, $campoAgendaFimCompleto);
    if ($horaComparacao === '') {
        $horaComparacao = rhObterHoraAgenda($agenda, $campoAgendaHoraInicio, $campoAgendaInicioCompleto);
    }
    if ($horaComparacao === '') {
        $horaComparacao = '23:59';
    }

    $agendaTimestamp = strtotime($dataComparacao . ' ' . $horaComparacao . ':00');
    if ($dia === $hojeIso || ($agendaTimestamp !== false && $agendaTimestamp >= $agoraAgendaTimestamp)) {
        $agendasNaoPassadas[] = $agenda;
    }
}

$primeiroDiaGrid = strtotime('monday this week', $dataBaseCalendario);
if ((int) date('N', $dataBaseCalendario) === 1) {
    $primeiroDiaGrid = $dataBaseCalendario;
}
$ultimoDiaMes = strtotime(date('Y-m-t', $dataBaseCalendario));
$ultimoDiaGrid = strtotime('friday this week', $ultimoDiaMes);
$diasCalendario = array();
for ($dia = $primeiroDiaGrid; $dia <= $ultimoDiaGrid; $dia = strtotime('+1 day', $dia)) {
    if ((int) date('N', $dia) <= 5) {
        $diasCalendario[] = $dia;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>RH - Painel</title>
    <style>
        :root {
            --bg-1:#f7f0e7;
            --bg-2:#e6d2b7;
            --primary:#8e5117;
            --accent:#eb6b1b;
            --accent-soft:#f7e1cc;
            --text:#332314;
            --muted:#6e5a46;
            --success:#2e7d4f;
            --danger:#b74737;
            --border:#edd9c2;
            --shadow:0 26px 60px rgba(84, 46, 13, .16);
            --tooltip-shadow:0 18px 32px rgba(67, 36, 10, .18);
        }
        * { box-sizing:border-box; }
        body {
            margin:0;
            min-height:100vh;
            font-family:"Segoe UI", Arial, sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at 20% 0%, rgba(255,255,255,.75), transparent 28%),
                radial-gradient(circle at 100% 0%, rgba(255,240,223,.7), transparent 35%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
        }
        .shell { display:grid; grid-template-columns:clamp(210px, 16vw, 240px) minmax(0, 1fr); align-items:stretch; }
        .sidebar {
            background:linear-gradient(180deg, #6d3d10, #8e5117 54%, #a5601d);
            color:#fff7ef;
            padding:16px 14px;
        }
        .sidebar h1 { margin:0 0 8px; font-size:30px; letter-spacing:.02em; }
        .sidebar p { margin:0 0 28px; line-height:1.65; color:rgba(255,247,239,.82); }
        .menu-title {
            margin:0 0 12px;
            font-size:10px;
            letter-spacing:.08em;
            text-transform:uppercase;
            color:rgba(255,247,239,.64);
        }
        .menu { display:grid; gap:8px; }
        .menu a {
            display:block;
            padding:14px 16px;
            border-radius:16px;
            color:#fff;
            text-decoration:none;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.12);
        }
        .logout {
            margin-top:18px;
            display:inline-block;
            color:rgba(255,247,239,.88);
            text-decoration:none;
        }
        .content {
            padding:10px;
            display:grid;
            gap:10px;
            align-content:start;
            min-height:100vh;
        }
        .stats {
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:8px;
        }
        .stats-card,
        .calendar-card,
        .form-card {
            background:rgba(255,253,249,.92);
            border:1px solid rgba(237,217,194,.72);
            border-radius:28px;
            box-shadow:var(--shadow);
        }
        .stats-card {
            display:block;
            padding:12px 14px;
            text-decoration:none;
            color:inherit;
            min-height:96px;
        }
        .eyebrow {
            margin:0 0 10px;
            color:var(--primary);
            font-size:10px;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
        }
        .stats-card strong {
            display:block;
            font-size:28px;
            line-height:1;
            margin-bottom:8px;
        }
        .stats-card h2 { margin:0; font-size:16px; }
        .stats-card p { margin:0; color:var(--muted); line-height:1.65; }
        .stats-card.primary strong { color:var(--primary); }
        .stats-card.success strong { color:var(--success); }
        .stats-card.danger strong { color:var(--danger); }
        .agenda-grid {
            display:grid;
            grid-template-columns:minmax(0, 1.75fr) minmax(300px, .78fr);
            gap:10px;
            align-items:stretch;
        }
        .calendar-card,
        .form-card { padding:10px; }
        .calendar-weekdays,
        .calendar-days {
            display:grid;
            grid-template-columns:repeat(5, minmax(0,1fr));
            gap:5px;
        }
        .calendar-weekdays { margin-bottom:6px; }
        .weekday {
            padding:6px 8px;
            font-size:10px;
            font-weight:800;
            color:var(--primary);
            text-transform:uppercase;
        }
        .day {
            min-height:72px;
            border:1px solid var(--border);
            border-radius:12px;
            background:linear-gradient(180deg, #fffdfa, #fff7ef);
            padding:6px;
            display:flex;
            flex-direction:column;
            gap:3px;
            overflow:visible;
        }
        .day.other-month { opacity:.48; background:#f7efe6; }
        .day.today { border-color:rgba(235,107,27,.65); }
        .day-top {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:8px;
        }
        .day-number {
            font-weight:800;
            color:var(--primary);
            font-size:9px;
        }
        .today-badge {
            padding:3px 6px;
            border-radius:999px;
            background:var(--accent-soft);
            color:var(--primary);
            font-size:9px;
            font-weight:800;
            text-transform:uppercase;
        }
        .day-events { display:grid; gap:3px; align-content:start; overflow:visible; }
        .event-wrap {
            position:relative;
            overflow:visible;
            justify-self:start;
        }
        .event-pill {
            display:inline-block;
            width:auto;
            max-width:100%;
            padding:4px 7px;
            border-radius:9px;
            background:linear-gradient(135deg, #f8e7d6, #fff6ed);
            color:var(--primary);
            font-size:10px;
            line-height:1;
            font-weight:800;
            border:1px solid rgba(235,107,27,.12);
            text-align:left;
            cursor:pointer;
        }
        .event-tooltip {
            position:absolute;
            left:calc(100% + 12px);
            top:0;
            width:250px;
            padding:14px 16px;
            border-radius:12px;
            border:1px solid rgba(237,217,194,.9);
            background:#fffdf9;
            box-shadow:var(--tooltip-shadow);
            opacity:0;
            pointer-events:none;
            transform:translateY(4px);
            transition:opacity .18s ease, transform .18s ease;
            z-index:9999;
        }
        .event-wrap:hover .event-tooltip {
            opacity:1;
            transform:translateY(0);
        }
        .event-tooltip::before {
            content:"";
            position:absolute;
            left:-7px;
            top:18px;
            width:14px;
            height:14px;
            background:#fffdf9;
            border-left:1px solid rgba(237,217,194,.9);
            border-bottom:1px solid rgba(237,217,194,.9);
            transform:rotate(45deg);
        }
        .tooltip-title {
            margin:0 0 10px;
            font-size:10px;
            font-weight:800;
            color:var(--primary);
            line-height:1.3;
        }
        .tooltip-line {
            margin:0 0 6px;
            font-size:11px;
            color:var(--text);
            line-height:1.45;
        }
        .tooltip-line:last-child { margin-bottom:0; }
        .tooltip-line strong { color:var(--primary); }
        .tooltip-actions {
            margin-top:8px;
            padding-top:10px;
            border-top:1px solid rgba(237,217,194,.85);
        }
        .tooltip-reagendar {
            border:0;
            background:transparent;
            color:var(--accent);
            font-size:11px;
            font-weight:800;
            padding:0;
            cursor:pointer;
        }
        .event-meta { display:block; color:var(--muted); font-size:10px; }
        .alert {
            padding:14px 16px;
            border-radius:16px;
            font-weight:600;
            margin-bottom:14px;
        }
        .alert.success { background:#ecf8f0; color:var(--success); }
        .alert.error { background:#fbeeed; color:var(--danger); }
        .form-grid { display:grid; gap:8px; }
        .field { display:grid; gap:3px; }
        .line-agenda {
            display:grid;
            grid-template-columns:74px minmax(0, 1fr) 118px;
            gap:8px;
            align-items:center;
        }
        .line-title {
            margin:0;
            font-size:11px;
            font-weight:800;
            color:var(--text);
        }
        .agenda-status {
            display:none;
            padding:8px 10px;
            border-radius:12px;
            background:#fff2e6;
            color:var(--primary);
            border:1px solid rgba(235,107,27,.18);
            font-size:11px;
            font-weight:700;
        }
        .agenda-status.active { display:block; }
        label { font-size:11px; font-weight:700; }
        input, select {
            width:100%;
            min-width:0;
            border:1px solid var(--border);
            border-radius:12px;
            padding:12px 13px;
            font-size:9px;
            line-height:1.25;
            color:var(--text);
            background:#fffdf9;
        }
        input[type="date"] { min-width:0; }
        .helper {
            font-size:10px;
            color:var(--muted);
            line-height:1.5;
        }
        .calendar-nav {
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }
        .nav-btn,
        .btn {
            border:0;
            border-radius:9px;
            padding:4px 8px;
            font-weight:700;
            font-size:11px;
            text-decoration:none;
            cursor:pointer;
        }
        .nav-btn,
        .btn.secondary {
            background:#fff;
            color:var(--text);
            border:1px solid var(--border);
        }
        .btn.primary {
            background:linear-gradient(135deg, var(--accent), var(--primary));
            color:#fff;
        }
        .upcoming { margin-top:6px; display:grid; gap:6px; overflow:auto; padding-right:4px; max-height:none; min-height:0; align-content:start; }
        .upcoming-item {
            padding:8px 10px;
            border-radius:12px;
            background:#f8f0e6;
            border:1px solid rgba(237,217,194,.8);
        }
        .upcoming-item strong { color:var(--primary); display:block; margin-bottom:2px; font-size:12px; line-height:1.2; }
        .calendar-card {
            display:block;
            min-height:auto;
            overflow:visible;
            position:relative;
            z-index:3;
        }
        .form-card {
            display:grid;
            grid-template-rows:auto auto auto 1fr;
            min-height:auto;
            overflow:hidden;
            position:relative;
            z-index:1;
        }
        .calendar-days {
            align-content:start;
            overflow:visible;
            padding-right:0;
        }
        .calendar-nav { margin-bottom:8px !important; gap:6px; }
        .form-card form { display:grid; gap:0; align-content:start; }
        .form-card .calendar-nav { margin-top:10px !important; }
        .upcoming-actions {
            margin-top:6px;
            display:flex;
            gap:6px;
            flex-wrap:wrap;
        }
        .upcoming-action {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:5px 9px;
            border-radius:10px;
            border:1px solid rgba(235,107,27,.18);
            background:#fffaf4;
            color:var(--primary);
            font-size:10px;
            font-weight:800;
            text-decoration:none;
            cursor:pointer;
        }
        button.upcoming-action {
            font-family:inherit;
        }
        @media (max-width:1500px) {
            .agenda-grid { grid-template-columns:minmax(0, 1fr) 350px; }
            .stats-card { min-height:88px; }
            .stats-card h2 { font-size:15px; }
            .day { min-height:clamp(56px, 6vw, 68px); }
        }
        @media (max-width:1280px) {
            .shell { grid-template-columns:170px minmax(0, 1fr); }
            .sidebar { padding:14px 12px; min-height:100%; }
            .sidebar h1 { font-size:28px; }
            .menu a { padding:12px 14px; border-radius:14px; }
            .stats { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .stats-card:last-child { grid-column:1 / -1; }
            .agenda-grid { grid-template-columns:1fr; min-height:auto; height:auto; }
            .form-card { order:2; height:auto; }
            .calendar-card { order:1; height:auto; }
            .upcoming { max-height:260px; }
        }
        @media (max-width:980px) {
            .shell { grid-template-columns:1fr; }
            .sidebar { padding:14px 12px; min-height:100%; }
            .menu { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .stats { grid-template-columns:1fr; }
            .stats-card:last-child { grid-column:auto; }
            .content { padding:14px 12px 22px; }
            .calendar-days, .calendar-weekdays { gap:6px; }
            .day { min-height:clamp(72px, 12vw, 98px); }
            .line-agenda { grid-template-columns:1fr; gap:6px; }
            .line-title { margin-bottom:2px; }
            .event-tooltip {
                left:0;
                right:0;
                top:calc(100% + 8px);
                width:auto;
            }
            .event-tooltip::before {
                left:22px;
                top:-7px;
                border-left:1px solid rgba(237,217,194,.9);
                border-bottom:none;
                border-top:1px solid rgba(237,217,194,.9);
                transform:rotate(45deg);
            }
        }
        @media (max-width:760px) {
            .menu { grid-template-columns:1fr; }
            .sidebar h1 { font-size:26px; }
            .calendar-card, .form-card, .stats-card { border-radius:22px; }
            .calendar-weekdays { grid-template-columns:repeat(5, minmax(0, 1fr)); }
            .weekday { padding:4px 2px; font-size:8px; }
            .day { min-height:clamp(68px, 15vw, 82px); padding:5px; }
            .day-number { font-size:10px; }
            .today-badge { font-size:8px; padding:2px 5px; }
            .event-pill { font-size:9px; padding:3px 6px; }
            .upcoming-item { padding:7px 8px; }
            input, select { font-size:12px; padding:10px 11px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <h1>RH</h1>
            
            <div class="menu-title">Menu</div>
            <nav class="menu">
                <a href="./cadastro_smt206.php">Cadastro de Candidato</a>
                <a href="./entrevista_smt207.php">Entrevista</a>
                <a href="./lista_entrevistas.php">Lista de Entrevistas</a>
            </nav>
            <a class="logout" href="./index.php">Voltar ao login</a>
        </aside>

        <main class="content">
            <section class="stats">
                <a class="stats-card primary" href="<?php echo rhEscaparPainel($linkDashboard); ?>">
                    <p class="eyebrow">Dashboard <?php echo rhEscaparPainel($resumoMes); ?></p>
                    <strong><?php echo (int) $indicadores['entrevistas_mes']; ?></strong>
                    <h2>Entrevistas no mes</h2>
                    
                </a>
                <a class="stats-card success" href="<?php echo rhEscaparPainel($linkAprovadas); ?>">
                    <p class="eyebrow">Aprovadas</p>
                    <strong><?php echo (int) $indicadores['aprovadas_mes']; ?></strong>
                    <h2>Entrevistas aprovadas</h2>
                    
                </a>
                <a class="stats-card danger" href="<?php echo rhEscaparPainel($linkReprovadas); ?>">
                    <p class="eyebrow">Reprovadas</p>
                    <strong><?php echo (int) $indicadores['reprovadas_mes']; ?></strong>
                    <h2>Entrevistas reprovadas</h2>
                    
                </a>
            </section>

            <section class="agenda-grid">
                <div class="calendar-card">
                    
                    <h2 style="margin:0 0 4px; font-size:20px;"><?php echo rhEscaparPainel(ucfirst($tituloCalendario)); ?></h2>
                    

                    <div class="calendar-nav" style="margin-bottom:18px;">
                        <a class="nav-btn" href="./painel.php?mes=<?php echo urlencode($mesAnterior); ?>">Mes anterior</a>
                        <a class="nav-btn" href="./painel.php?mes=<?php echo urlencode(date('Y-m')); ?>">Mes atual</a>
                        <a class="nav-btn" href="./painel.php?mes=<?php echo urlencode($mesSeguinte); ?>">Proximo mes</a>
                    </div>

                    <div class="calendar-weekdays">
                        <?php foreach ($diasSemanaPtBr as $diaSemana) { ?>
                            <div class="weekday"><?php echo rhEscaparPainel($diaSemana); ?></div>
                        <?php } ?>
                    </div>

                    <div class="calendar-days">
                        <?php foreach ($diasCalendario as $diaTimestamp) { ?>
                            <?php
                            $dataIso = date('Y-m-d', $diaTimestamp);
                            $eventosDia = isset($agendaPorDia[$dataIso]) ? $agendaPorDia[$dataIso] : array();
                            $classesDia = array('day');
                            if (date('Y-m', $diaTimestamp) !== $mesAtual) {
                                $classesDia[] = 'other-month';
                            }
                            if ($dataIso === date('Y-m-d')) {
                                $classesDia[] = 'today';
                            }
                            ?>
                            <div class="<?php echo rhEscaparPainel(implode(' ', $classesDia)); ?>">
                                <div class="day-top">
                                    <span class="day-number"><?php echo date('d', $diaTimestamp); ?></span>
                                    <?php if ($dataIso === date('Y-m-d')) { ?>
                                        <span class="today-badge">Hoje</span>
                                    <?php } ?>
                                </div>
                                <div class="day-events">
                                    <?php foreach ($eventosDia as $indice => $agenda) { ?>
                                        <?php if ($indice >= 4) { break; } ?>
                                        <?php
                                        $textoHora = rhObterTextoHorarioAgenda(
                                            $agenda,
                                            $campoAgendaDataInicio,
                                            $campoAgendaHoraInicio,
                                            $campoAgendaInicioCompleto,
                                            $campoAgendaDataFim,
                                            $campoAgendaHoraFim,
                                            $campoAgendaFimCompleto
                                        );
                                        $textoDetalhe = rhObterTextoDataHoraAgenda(
                                            $agenda,
                                            $campoAgendaDataInicio,
                                            $campoAgendaHoraInicio,
                                            $campoAgendaInicioCompleto,
                                            $campoAgendaDataFim,
                                            $campoAgendaHoraFim,
                                            $campoAgendaFimCompleto
                                        );
                                        $agendaPayload = array(
                                            'agenda_id' => !empty($campoAgendaPk) && isset($agenda[$campoAgendaPk]) ? (string) $agenda[$campoAgendaPk] : '',
                                            'candidato_id' => !empty($campoAgendaCandidato) && isset($agenda[$campoAgendaCandidato]) ? (string) $agenda[$campoAgendaCandidato] : '',
                                            'candidato_label' => isset($agenda['candidato_nome']) ? (string) $agenda['candidato_nome'] : '',
                                            'entrevistador_id' => !empty($campoAgendaEntrevistador) && isset($agenda[$campoAgendaEntrevistador]) ? (string) $agenda[$campoAgendaEntrevistador] : '',
                                            'entrevistador_label' => isset($agenda['entrevistador_nome']) ? (string) $agenda['entrevistador_nome'] : '',
                                            'data_inicio' => rhObterDataAgenda($agenda, $campoAgendaDataInicio, $campoAgendaInicioCompleto),
                                            'hora_inicio' => rhObterHoraAgenda($agenda, $campoAgendaHoraInicio, $campoAgendaInicioCompleto),
                                            'data_fim' => rhObterDataAgenda($agenda, $campoAgendaDataFim, $campoAgendaFimCompleto),
                                            'hora_fim' => rhObterHoraAgenda($agenda, $campoAgendaHoraFim, $campoAgendaFimCompleto)
                                        );
                                        $linkEntrevistaCalendario = './entrevista_smt207.php?' . http_build_query(array(
                                            'agenda_candidato_id' => $agendaPayload['candidato_id'],
                                            'agenda_candidato_label' => $agendaPayload['candidato_label']
                                        ));
                                        ?>
                                        <div class="event-wrap">
                                            <a class="event-pill" href="<?php echo rhEscaparPainel($linkEntrevistaCalendario); ?>"><?php echo rhEscaparPainel($textoHora !== '' ? $textoHora : '--:--'); ?></a>
                                            <div class="event-tooltip">
                                                <p class="tooltip-title"><?php echo rhEscaparPainel(isset($agenda['candidato_nome']) ? $agenda['candidato_nome'] : 'Candidato'); ?></p>
                                                <p class="tooltip-line"><strong>Horario:</strong> <?php echo rhEscaparPainel($textoDetalhe); ?></p>
                                                <p class="tooltip-line"><strong>Entrevistador:</strong> <?php echo rhEscaparPainel(isset($agenda['entrevistador_nome']) ? $agenda['entrevistador_nome'] : 'Nao informado'); ?></p>

                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (count($eventosDia) > 4) { ?>
                                        <span class="event-meta">+<?php echo count($eventosDia) - 4; ?> agenda(s)</span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-card">
                    <p class="eyebrow">Nova agenda</p>
                    <h3 id="agenda_form_title" style="margin:0 0 6px; font-size:16px;">Agendar entrevista</h3>
                    

                    <?php if (!empty($mensagem)) { ?><div class="alert success"><?php echo rhEscaparPainel($mensagem); ?></div><?php } ?>
                    <?php if (!empty($erro)) { ?><div class="alert error"><?php echo rhEscaparPainel($erro); ?></div><?php } ?>

                    <form id="agenda_form" method="post" action="./painel.php?mes=<?php echo urlencode($mesAtual); ?>">
                        <input type="hidden" name="acao" value="salvar_agenda">
                        <input type="hidden" name="agenda_id" id="agenda_id" value="<?php echo rhEscaparPainel($agendaForm['agenda_id']); ?>">
                        <input type="hidden" name="agenda_candidato_id" id="agenda_candidato_id" value="<?php echo rhEscaparPainel($agendaForm['candidato_id']); ?>">
                        <input type="hidden" name="agenda_entrevistador_id" id="agenda_entrevistador_id" value="<?php echo rhEscaparPainel($agendaForm['entrevistador_id']); ?>">

                        <div class="form-grid">
                            <div id="agenda_status" class="agenda-status"></div>

                            <div class="field">
                                <label for="agenda_candidato_label">Candidato</label>
                                <input id="agenda_candidato_label" name="agenda_candidato_label" list="lista_candidatos_agenda" value="<?php echo rhEscaparPainel($agendaForm['candidato_label']); ?>" placeholder="Pesquisar candidato por nome ou CPF">
                                <datalist id="lista_candidatos_agenda">
                                    <?php foreach ($candidatos as $candidato) { ?>
                                        <option value="<?php echo rhEscaparPainel($candidato['label']); ?>"></option>
                                    <?php } ?>
                                </datalist>
                            </div>

                            <div class="field">
                                <label for="agenda_entrevistador_label">Entrevistador</label>
                                <input id="agenda_entrevistador_label" name="agenda_entrevistador_label" list="lista_entrevistadores_agenda" value="<?php echo rhEscaparPainel($agendaForm['entrevistador_label']); ?>" placeholder="Pesquisar entrevistador">
                                <datalist id="lista_entrevistadores_agenda">
                                    <?php foreach ($entrevistadores as $entrevistador) { ?>
                                        <option value="<?php echo rhEscaparPainel($entrevistador['label']); ?>"></option>
                                    <?php } ?>
                                </datalist>
                            </div>

                            <div class="line-agenda">
                                <label class="line-title" for="agenda_data_inicio">Inicio</label>
                                <input id="agenda_data_inicio" name="agenda_data_inicio" type="date" min="<?php echo rhEscaparPainel($hojeIso); ?>" value="<?php echo rhEscaparPainel($agendaForm['data_inicio']); ?>">
                                <select id="agenda_hora_inicio" name="agenda_hora_inicio">
                                    <?php foreach ($horariosDisponiveis as $horario) { ?>
                                        <option value="<?php echo rhEscaparPainel($horario); ?>" <?php echo $agendaForm['hora_inicio'] === $horario ? 'selected' : ''; ?>><?php echo rhEscaparPainel($horario); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="line-agenda">
                                <label class="line-title" for="agenda_data_fim">Termino</label>
                                <input id="agenda_data_fim" name="agenda_data_fim" type="date" min="<?php echo rhEscaparPainel($hojeIso); ?>" value="<?php echo rhEscaparPainel($agendaForm['data_fim']); ?>">
                                <select id="agenda_hora_fim" name="agenda_hora_fim">
                                    <?php foreach ($horariosDisponiveis as $horario) { ?>
                                        <option value="<?php echo rhEscaparPainel($horario); ?>" <?php echo $agendaForm['hora_fim'] === $horario ? 'selected' : ''; ?>><?php echo rhEscaparPainel($horario); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            
                        </div>

                        <div class="calendar-nav" style="margin-top:18px;">
                            <button id="agenda_submit" class="btn primary" type="submit">Salvar agenda</button>
                            <button id="agenda_cancelar" class="btn secondary" type="button" style="display:none;">Cancelar reagendamento</button>
                            <a class="btn secondary" href="./painel.php?mes=<?php echo urlencode($mesAtual); ?>">Limpar</a>
                        </div>
                    </form>

                    <div class="upcoming">
                        <?php if (empty($agendasNaoPassadas)) { ?>
                            <div class="upcoming-item">
                                <strong>Sem agendas neste mes</strong>
                                Nenhum compromisso futuro cadastrado na SMT208 para o periodo selecionado.
                            </div>
                        <?php } else { ?>
                            <?php foreach (array_slice($agendasNaoPassadas, 0, 5) as $agenda) { ?>
                                <?php
                                $textoHorario = rhObterTextoDataHoraAgenda(
                                    $agenda,
                                    $campoAgendaDataInicio,
                                    $campoAgendaHoraInicio,
                                    $campoAgendaInicioCompleto,
                                    $campoAgendaDataFim,
                                    $campoAgendaHoraFim,
                                    $campoAgendaFimCompleto
                                );
                                $agendaPayload = array(
                                    'agenda_id' => !empty($campoAgendaPk) && isset($agenda[$campoAgendaPk]) ? (string) $agenda[$campoAgendaPk] : '',
                                    'candidato_id' => !empty($campoAgendaCandidato) && isset($agenda[$campoAgendaCandidato]) ? (string) $agenda[$campoAgendaCandidato] : '',
                                    'candidato_label' => isset($agenda['candidato_nome']) ? (string) $agenda['candidato_nome'] : '',
                                    'entrevistador_id' => !empty($campoAgendaEntrevistador) && isset($agenda[$campoAgendaEntrevistador]) ? (string) $agenda[$campoAgendaEntrevistador] : '',
                                    'entrevistador_label' => isset($agenda['entrevistador_nome']) ? (string) $agenda['entrevistador_nome'] : '',
                                    'data_inicio' => rhObterDataAgenda($agenda, $campoAgendaDataInicio, $campoAgendaInicioCompleto),
                                    'hora_inicio' => rhObterHoraAgenda($agenda, $campoAgendaHoraInicio, $campoAgendaInicioCompleto),
                                    'data_fim' => rhObterDataAgenda($agenda, $campoAgendaDataFim, $campoAgendaFimCompleto),
                                    'hora_fim' => rhObterHoraAgenda($agenda, $campoAgendaHoraFim, $campoAgendaFimCompleto)
                                );
                                $linkEntrevista = './entrevista_smt207.php?' . http_build_query(array(
                                    'agenda_candidato_id' => $agendaPayload['candidato_id'],
                                    'agenda_candidato_label' => $agendaPayload['candidato_label']
                                ));
                                ?>
                                <div class="upcoming-item">
                                    <strong><?php echo rhEscaparPainel($textoHorario); ?></strong>
                                    <span style="display:block;font-size:11px;line-height:1.15;"><?php echo rhEscaparPainel(isset($agenda['candidato_nome']) ? $agenda['candidato_nome'] : 'Candidato'); ?></span>
                                    <span style="display:block;font-size:10px;line-height:1.1;color:#6f5f50;"><?php echo rhEscaparPainel(isset($agenda['entrevistador_nome']) ? $agenda['entrevistador_nome'] : 'Entrevistador'); ?></span>
                                    <div class="upcoming-actions">
                                        <button type="button" class="upcoming-action js-reagendar-agenda" data-agenda="<?php echo rhEscaparPainel(json_encode($agendaPayload)); ?>">Reagendar</button>
                                        <a class="upcoming-action" href="<?php echo rhEscaparPainel($linkEntrevista); ?>">Entrevista</a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        (function () {
            var candidatos = <?php echo json_encode($candidatos); ?>;
            var entrevistadores = <?php echo json_encode($entrevistadores); ?>;
            var candidatoInput = document.getElementById('agenda_candidato_label');
            var candidatoIdInput = document.getElementById('agenda_candidato_id');
            var entrevistadorInput = document.getElementById('agenda_entrevistador_label');
            var entrevistadorIdInput = document.getElementById('agenda_entrevistador_id');
            var agendaIdInput = document.getElementById('agenda_id');
            var dataInicio = document.getElementById('agenda_data_inicio');
            var dataFim = document.getElementById('agenda_data_fim');
            var horaInicio = document.getElementById('agenda_hora_inicio');
            var horaFim = document.getElementById('agenda_hora_fim');
            var agendaStatus = document.getElementById('agenda_status');
            var agendaTitle = document.getElementById('agenda_form_title');
            var agendaSubmit = document.getElementById('agenda_submit');
            var agendaCancelar = document.getElementById('agenda_cancelar');
            var agendaForm = document.getElementById('agenda_form');
            var hojeIso = <?php echo json_encode($hojeIso); ?>;
            var agoraIso = <?php echo json_encode(date('c', $agoraTimestamp)); ?>;

            function sincronizar(lista, texto, hidden) {
                if (!texto || !hidden) {
                    return;
                }
                var item = lista.find(function (registro) {
                    return registro.label === texto.value;
                });
                hidden.value = item ? item.id : '';
            }

            function buscarPorId(lista, id) {
                return lista.find(function (registro) {
                    return String(registro.id) === String(id);
                }) || null;
            }

            function dataEhFimDeSemana(dataTexto) {
                if (!dataTexto) {
                    return false;
                }
                var partes = dataTexto.split('-');
                if (partes.length !== 3) {
                    return false;
                }
                var data = new Date(parseInt(partes[0], 10), parseInt(partes[1], 10) - 1, parseInt(partes[2], 10));
                var diaSemana = data.getDay();
                return diaSemana === 0 || diaSemana === 6;
            }

            function dataHoraNoPassado(dataTexto, horaTexto) {
                if (!dataTexto || !horaTexto) {
                    return false;
                }
                var inicio = new Date(dataTexto + 'T' + horaTexto + ':00');
                var agora = new Date(agoraIso);
                return inicio < agora;
            }

            function definirFimPadrao() {
                if (!dataInicio || !dataFim || !horaInicio || !horaFim) {
                    return;
                }

                if (!dataFim.value) {
                    dataFim.value = dataInicio.value;
                }

                var inicio = new Date(dataInicio.value + 'T' + horaInicio.value + ':00');
                var fim = new Date(dataFim.value + 'T' + horaFim.value + ':00');

                if (fim <= inicio) {
                    dataFim.value = dataInicio.value;
                    var indice = horaInicio.selectedIndex + 1;
                    if (indice < horaFim.options.length) {
                        horaFim.selectedIndex = indice;
                    }
                }
            }

            function ativarModoReagendamento() {
                if (agendaStatus) {
                    agendaStatus.classList.add('active');
                    agendaStatus.textContent = 'Modo reagendamento ativo. Ajuste os horarios e salve novamente.';
                }
                if (agendaTitle) {
                    agendaTitle.textContent = 'Reagendar entrevista';
                }
                if (agendaSubmit) {
                    agendaSubmit.textContent = 'Salvar reagendamento';
                }
                if (agendaCancelar) {
                    agendaCancelar.style.display = 'inline-flex';
                }
            }

            function limparModoReagendamento() {
                if (agendaIdInput) {
                    agendaIdInput.value = '';
                }
                if (agendaStatus) {
                    agendaStatus.classList.remove('active');
                    agendaStatus.textContent = '';
                }
                if (agendaTitle) {
                    agendaTitle.textContent = 'Agendar entrevista';
                }
                if (agendaSubmit) {
                    agendaSubmit.textContent = 'Salvar agenda';
                }
                if (agendaCancelar) {
                    agendaCancelar.style.display = 'none';
                }
            }

            function preencherAgenda(payload) {
                if (!payload) {
                    return;
                }

                if (agendaIdInput) {
                    agendaIdInput.value = payload.agenda_id || '';
                }

                if (candidatoIdInput) {
                    candidatoIdInput.value = payload.candidato_id || '';
                }
                if (entrevistadorIdInput) {
                    entrevistadorIdInput.value = payload.entrevistador_id || '';
                }

                if (candidatoInput) {
                    var candidato = buscarPorId(candidatos, payload.candidato_id);
                    candidatoInput.value = candidato ? candidato.label : (payload.candidato_label || '');
                }
                if (entrevistadorInput) {
                    var entrevistador = buscarPorId(entrevistadores, payload.entrevistador_id);
                    entrevistadorInput.value = entrevistador ? entrevistador.label : (payload.entrevistador_label || '');
                }

                if (dataInicio) {
                    dataInicio.value = payload.data_inicio || hojeIso;
                }
                if (horaInicio) {
                    horaInicio.value = payload.hora_inicio || '08:00';
                }
                if (dataFim) {
                    dataFim.value = payload.data_fim || payload.data_inicio || hojeIso;
                }
                if (horaFim) {
                    horaFim.value = payload.hora_fim || '08:30';
                }

                ativarModoReagendamento();
                definirFimPadrao();
                window.scrollTo({ top: agendaForm.offsetTop - 20, behavior: 'smooth' });
            }

            if (candidatoInput && candidatoIdInput) {
                candidatoInput.addEventListener('input', function () {
                    sincronizar(candidatos, candidatoInput, candidatoIdInput);
                });
                sincronizar(candidatos, candidatoInput, candidatoIdInput);
            }

            if (entrevistadorInput && entrevistadorIdInput) {
                entrevistadorInput.addEventListener('input', function () {
                    sincronizar(entrevistadores, entrevistadorInput, entrevistadorIdInput);
                });
                sincronizar(entrevistadores, entrevistadorInput, entrevistadorIdInput);
            }

            [dataInicio, dataFim].forEach(function (campo) {
                if (!campo) {
                    return;
                }
                campo.addEventListener('change', function () {
                    if (campo.value && campo.value < hojeIso) {
                        alert('Nao e permitido agendar entrevistas no passado.');
                        campo.value = hojeIso;
                    }
                    if (dataEhFimDeSemana(campo.value)) {
                        alert('Nao e permitido agendar entrevistas para sabado ou domingo.');
                        campo.value = hojeIso;
                        return;
                    }
                    definirFimPadrao();
                });
            });

            [horaInicio, horaFim].forEach(function (campo) {
                if (!campo) {
                    return;
                }
                campo.addEventListener('change', definirFimPadrao);
            });

            if (agendaCancelar) {
                agendaCancelar.addEventListener('click', function () {
                    limparModoReagendamento();
                    if (agendaForm) {
                        agendaForm.reset();
                    }
                    if (dataInicio) {
                        dataInicio.value = hojeIso;
                    }
                    if (dataFim) {
                        dataFim.value = hojeIso;
                    }
                    if (horaInicio) {
                        horaInicio.value = '08:00';
                    }
                    if (horaFim) {
                        horaFim.value = '08:30';
                    }
                    if (candidatoIdInput) {
                        candidatoIdInput.value = '';
                    }
                    if (entrevistadorIdInput) {
                        entrevistadorIdInput.value = '';
                    }
                });
            }

            Array.prototype.forEach.call(document.querySelectorAll('.js-reagendar-agenda'), function (botao) {
                botao.addEventListener('click', function () {
                    var payload = null;
                    try {
                        payload = JSON.parse(botao.getAttribute('data-agenda'));
                    } catch (erro) {
                        payload = null;
                    }
                    preencherAgenda(payload);
                });
            });

            if (agendaForm) {
                agendaForm.addEventListener('submit', function (evento) {
                    if (dataInicio && horaInicio && dataHoraNoPassado(dataInicio.value, horaInicio.value)) {
                        evento.preventDefault();
                        alert('Nao e permitido gravar agenda no passado.');
                        return;
                    }
                });
            }

            if (agendaIdInput && agendaIdInput.value) {
                ativarModoReagendamento();
            }
        })();
    </script>
</body>
</html>