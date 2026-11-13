<?php
declare(strict_types=1);

/* Cadastros do módulo: nunca alteram D109; apenas consultam o cadastro
 * mestre e mantêm os vínculos e catálogos explicitamente previstos no PCP. */
function pcp_apontamento_management_text($value, int $max = 100): string
{
    $text = pcp_apontamento_required_text($value, $max);
    if ($text === null) throw new DomainException('Informe uma descrição válida.');
    return $text;
}

function pcp_apontamento_edicao_justificativa($value): string
{
    $text = pcp_apontamento_required_text($value, 500);
    if ($text === null) throw new DomainException('Informe a justificativa da inativação.');
    return $text;
}

function pcp_apontamento_edicao_auditar(mysqli $conn, int $grupoId, ?int $participanteId, string $acao, string $justificativa, array $anterior, array $novo, int $userId, ?int $eventoId = null): void
{
    pcp_apontamento_stmt($conn, 'INSERT INTO PCP007 (PCP007_PCP001_Id,PCP007_PCP002_Id,PCP007_PCP003_Id,PCP007_Acao,PCP007_Justificativa,PCP007_Dados_Anteriores,PCP007_Dados_Novos,PCP007_Data_Hora,PCP007_C007_Id) VALUES (?,?,?,?,?,?,?,NOW(),?)', 'iiissssi', [$grupoId, $participanteId, $eventoId, $acao, $justificativa, json_encode($anterior, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), json_encode($novo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $userId]);
}

function pcp_apontamento_sincronizar_grupo(mysqli $conn, int $grupoId): void
{
    $resumo = pcp_apontamento_one($conn, "SELECT MIN(PCP002_Data_Hora_Inicio) AS inicio,MAX(PCP002_Data_Hora_Fim) AS fim,SUM(PCP002_Status<>'F') AS pendentes FROM PCP002 WHERE PCP002_PCP001_Id=? AND PCP002_Status<>'I'", 'i', [$grupoId]);
    if (!$resumo || $resumo['inicio'] === null) return;
    $fim = (int) $resumo['pendentes'] === 0 ? $resumo['fim'] : null;
    pcp_apontamento_stmt($conn, 'UPDATE PCP001 SET PCP001_Data_Hora_Inicio=?,PCP001_Data_Hora_Fim=?,PCP001_Status=? WHERE PCP001_Id=?', 'sssi', [$resumo['inicio'], $fim, $fim === null ? 'A' : 'F', $grupoId]);
}

function pcp_apontamento_validar_sequencia_eventos(mysqli $conn, int $participanteId): void
{
    $eventos = pcp_apontamento_all($conn, "SELECT PCP003_Tipo AS tipo FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' ORDER BY PCP003_Data_Hora,PCP003_Id", 'i', [$participanteId]);
    $anterior = null;
    foreach ($eventos as $indice => $evento) {
        $tipo = $evento['tipo'];
        $valido = ($indice === 0 && $tipo === 'I') || ($tipo === 'P' && in_array($anterior, ['I','R'], true)) || ($tipo === 'R' && $anterior === 'P') || ($tipo === 'F' && in_array($anterior, ['I','P','R'], true));
        if (!$valido || ($anterior === 'F')) throw new DomainException('A alteração deixaria a sequência de eventos inválida.');
        $anterior = $tipo;
    }
}

function pcp_apontamento_inativar_apontamento(mysqli $conn, array $context, int $grupoId, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $grupo = pcp_apontamento_one($conn, 'SELECT PCP001.PCP001_Id AS id,PCP001.PCP001_Status AS status,PCP001.PCP001_Data_Hora_Inicio AS inicio,PCP001.PCP001_Data_Hora_Fim AS fim,PCP001.PCP001_SMT006_Id AS processo_id FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP001.PCP001_Id=? LIMIT 1 FOR UPDATE', 'ii', [(int) $context['company_id'], $grupoId]);
        if (!$grupo) throw new DomainException('Apontamento não encontrado para a empresa ativa.');
        if ($grupo['status'] === 'I') throw new DomainException('Este apontamento já está inativado.');
        $participantes = pcp_apontamento_all($conn, 'SELECT PCP002_Id AS id,PCP002_Status AS status FROM PCP002 WHERE PCP002_PCP001_Id=? FOR UPDATE', 'i', [$grupoId]);
        pcp_apontamento_stmt($conn, "UPDATE PCP001 SET PCP001_Status='I',PCP001_Data_Hora_Inativacao=NOW(),PCP001_C007_Id_Inativacao=?,PCP001_Motivo_Inativacao=? WHERE PCP001_Id=?", 'isi', [(int) $context['user_id'], $justificativa, $grupoId]);
        pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='I',PCP002_Data_Hora_Inativacao=NOW(),PCP002_C007_Id_Inativacao=?,PCP002_Motivo_Inativacao=? WHERE PCP002_PCP001_Id=? AND PCP002_Status<>'I'", 'isi', [(int) $context['user_id'], $justificativa, $grupoId]);
        pcp_apontamento_edicao_auditar($conn, $grupoId, null, 'inativar_apontamento', $justificativa, ['apontamento'=>$grupo, 'participantes'=>$participantes], ['status'=>'I'], (int) $context['user_id']);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_inativar_participante(mysqli $conn, array $context, int $grupoId, int $participanteId, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $participante = pcp_apontamento_one($conn, 'SELECT PCP002.PCP002_Id AS id,PCP002.PCP002_Status AS status,PCP002.PCP002_Data_Hora_Inicio AS inicio,PCP002.PCP002_Data_Hora_Fim AS fim,PCP002.PCP002_D109_Id AS colaborador_id FROM PCP002 INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP002.PCP002_Id=? AND PCP002.PCP002_PCP001_Id=? LIMIT 1 FOR UPDATE', 'iii', [(int) $context['company_id'], $participanteId, $grupoId]);
        if (!$participante) throw new DomainException('Colaborador não encontrado neste apontamento.');
        if ($participante['status'] === 'I') throw new DomainException('Este colaborador já está inativado no apontamento.');
        pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='I',PCP002_Data_Hora_Inativacao=NOW(),PCP002_C007_Id_Inativacao=?,PCP002_Motivo_Inativacao=? WHERE PCP002_Id=?", 'isi', [(int) $context['user_id'], $justificativa, $participanteId]);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'inativar_participante', $justificativa, $participante, ['status'=>'I'], (int) $context['user_id']);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_edicao_data($value, string $campo): string
{
    $data = DateTime::createFromFormat('Y-m-d\\TH:i', trim((string) $value));
    if (!$data || $data->format('Y-m-d\\TH:i') !== trim((string) $value)) throw new DomainException("Informe $campo válido.");
    return $data->format('Y-m-d H:i:s');
}

function pcp_apontamento_editar_processo(mysqli $conn, array $context, int $grupoId, int $processoId, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $grupo = pcp_apontamento_one($conn, 'SELECT PCP001.PCP001_Id AS id,PCP001.PCP001_Status AS status,PCP001.PCP001_SMT006_Id AS processo_id FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP001.PCP001_Id=? FOR UPDATE', 'ii', [(int) $context['company_id'], $grupoId]);
        if (!$grupo || $grupo['status'] === 'I') throw new DomainException('Apontamento indisponível para alteração.');
        if ((int) $grupo['processo_id'] === $processoId) throw new DomainException('Selecione um processo diferente do atual.');
        $participantes = pcp_apontamento_all($conn, "SELECT PCP002.PCP002_D109_Id AS d109_id FROM PCP002 WHERE PCP002.PCP002_PCP001_Id=? AND PCP002.PCP002_Status<>'I' FOR UPDATE", 'i', [$grupoId]);
        if (!$participantes) throw new DomainException('Não há colaboradores ativos neste apontamento.');
        foreach ($participantes as $participante) {
            $permitido = pcp_apontamento_one($conn, "SELECT PCP004.PCP004_Id AS id FROM PCP006 INNER JOIN PCP004 ON PCP004.PCP004_PCP006_Id=PCP006.PCP006_Id AND PCP004.PCP004_Status='A' INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP004.PCP004_SMT006_Id AND SMT006.SMT006_Flag_Apontamento='S' WHERE PCP006.PCP006_D109_Id=? AND PCP006.PCP006_Status='A' AND PCP004.PCP004_SMT006_Id=? LIMIT 1", 'ii', [(int) $participante['d109_id'], $processoId]);
            if (!$permitido) throw new DomainException('O processo selecionado não está liberado para todos os colaboradores ativos.');
        }
        pcp_apontamento_stmt($conn, 'UPDATE PCP001 SET PCP001_SMT006_Id=? WHERE PCP001_Id=?', 'ii', [$processoId, $grupoId]);
        pcp_apontamento_edicao_auditar($conn, $grupoId, null, 'alterar_processo', $justificativa, ['processo_id'=>(int) $grupo['processo_id']], ['processo_id'=>$processoId], (int) $context['user_id']);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_adicionar_participante(mysqli $conn, array $context, int $grupoId, string $matricula, string $inicio, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $grupo = pcp_apontamento_one($conn, "SELECT PCP001.PCP001_Id AS id,PCP001.PCP001_SMT006_Id AS processo_id FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP001.PCP001_Id=? AND PCP001.PCP001_Status='A' FOR UPDATE", 'ii', [(int) $context['company_id'], $grupoId]);
        if (!$grupo) throw new DomainException('Somente apontamento ativo permite incluir colaborador.');
        $colaborador = pcp_apontamento_colaborador($conn, $matricula, true);
        if (!$colaborador) throw new DomainException('Colaborador ativo não encontrado para a matrícula informada.');
        if (pcp_apontamento_aberto($conn, (int) $context['company_id'], (int) $colaborador['id'], true)) throw new DomainException('Este colaborador já possui apontamento ativo ou pausado.');
        $permitido = pcp_apontamento_one($conn, "SELECT PCP004.PCP004_Id AS id FROM PCP006 INNER JOIN PCP004 ON PCP004.PCP004_PCP006_Id=PCP006.PCP006_Id AND PCP004.PCP004_Status='A' WHERE PCP006.PCP006_D109_Id=? AND PCP006.PCP006_Status='A' AND PCP004.PCP004_SMT006_Id=? LIMIT 1", 'ii', [(int) $colaborador['id'], (int) $grupo['processo_id']]);
        if (!$permitido) throw new DomainException('O processo deste apontamento não está liberado para o colaborador.');
        pcp_apontamento_stmt($conn, "INSERT INTO PCP002 (PCP002_PCP001_Id,PCP002_D109_Id,PCP002_Status,PCP002_Data_Hora_Inicio) VALUES (?,?,'A',?)", 'iis', [$grupoId, (int) $colaborador['id'], $inicio]);
        $participanteId = mysqli_insert_id($conn);
        pcp_apontamento_stmt($conn, "INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_C007_Id) VALUES (?,?,'I',?,?)", 'iisi', [$grupoId, $participanteId, $inicio, (int) $context['user_id']]);
        pcp_apontamento_sincronizar_grupo($conn, $grupoId);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'incluir_participante', $justificativa, [], ['colaborador_id'=>(int) $colaborador['id'], 'matricula'=>$matricula, 'inicio'=>$inicio], (int) $context['user_id']);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_editar_participante(mysqli $conn, array $context, int $grupoId, int $participanteId, string $inicio, ?string $fim, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $participante = pcp_apontamento_one($conn, "SELECT PCP002.PCP002_Id AS id,PCP002.PCP002_Status AS status,PCP002.PCP002_Data_Hora_Inicio AS inicio,PCP002.PCP002_Data_Hora_Fim AS fim FROM PCP002 INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP002.PCP002_Id=? AND PCP002.PCP002_PCP001_Id=? AND PCP001.PCP001_Status<>'I' FOR UPDATE", 'iii', [(int) $context['company_id'], $participanteId, $grupoId]);
        if (!$participante || $participante['status'] === 'I') throw new DomainException('Colaborador indisponível para alteração.');
        if ($fim !== null && $fim < $inicio) throw new DomainException('O fim não pode ser anterior ao início.');
        if ($participante['status'] === 'F' && $fim === null) throw new DomainException('Participação finalizada exige data/hora de fim.');
        $limite = $fim ?: '9999-12-31 23:59:59';
        $eventosForaDoPeriodo = pcp_apontamento_one($conn, "SELECT PCP003_Id AS id FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' AND PCP003_Tipo<>'I' AND (PCP003_Data_Hora<? OR PCP003_Data_Hora>?) LIMIT 1", 'iss', [$participanteId, $inicio, $limite]);
        if ($eventosForaDoPeriodo) throw new DomainException('Há evento fora do novo período informado. Ajuste os eventos antes de alterar o horário.');
        pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Data_Hora_Inicio=?,PCP002_Data_Hora_Fim=? WHERE PCP002_Id=?', 'ssi', [$inicio, $fim, $participanteId]);
        pcp_apontamento_stmt($conn, "UPDATE PCP003 SET PCP003_Data_Hora=? WHERE PCP003_PCP002_Id=? AND PCP003_Tipo='I'", 'si', [$inicio, $participanteId]);
        if ($fim !== null) pcp_apontamento_stmt($conn, "UPDATE PCP003 SET PCP003_Data_Hora=? WHERE PCP003_PCP002_Id=? AND PCP003_Tipo='F'", 'si', [$fim, $participanteId]);
        pcp_apontamento_sincronizar_grupo($conn, $grupoId);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'alterar_horarios_participante', $justificativa, ['inicio'=>$participante['inicio'], 'fim'=>$participante['fim']], ['inicio'=>$inicio, 'fim'=>$fim], (int) $context['user_id']);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_editar_evento(mysqli $conn, array $context, int $grupoId, int $participanteId, int $eventoId, string $dataHora, ?int $motivoPausa, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $evento = pcp_apontamento_one($conn, "SELECT PCP003.PCP003_Id AS id,PCP003.PCP003_Tipo AS tipo,PCP003.PCP003_Data_Hora AS data_hora,PCP003.PCP003_SMT012_Id AS motivo_id,PCP002.PCP002_Status AS participante_status,PCP002.PCP002_Data_Hora_Inicio AS inicio,PCP002.PCP002_Data_Hora_Fim AS fim FROM PCP003 INNER JOIN PCP002 ON PCP002.PCP002_Id=PCP003.PCP003_PCP002_Id INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP003.PCP003_Id=? AND PCP003.PCP003_PCP001_Id=? AND PCP003.PCP003_PCP002_Id=? AND PCP003.PCP003_Status='A' AND PCP001.PCP001_Status<>'I' AND PCP002.PCP002_Status<>'I' FOR UPDATE", 'iiii', [(int) $context['company_id'], $eventoId, $grupoId, $participanteId]);
        if (!$evento) throw new DomainException('Evento não encontrado para este colaborador.');
        if ($evento['tipo'] === 'I') {
            $anterior = pcp_apontamento_one($conn, "SELECT PCP003_Id AS id FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Id<>? AND PCP003_Status='A' AND PCP003_Data_Hora<? LIMIT 1", 'iis', [$participanteId, $eventoId, $dataHora]);
            if ($anterior) throw new DomainException('O início não pode ficar após outro evento do colaborador.');
            $proximo = pcp_apontamento_one($conn, "SELECT PCP003_Data_Hora AS data_hora,PCP003_Tipo AS tipo FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Id<>? AND PCP003_Status='A' ORDER BY PCP003_Data_Hora,PCP003_Id LIMIT 1", 'ii', [$participanteId, $eventoId]);
            if ($proximo && $dataHora >= $proximo['data_hora']) throw new DomainException('O início precisa ser anterior ao próximo evento ativo (' . date('d/m/Y H:i', strtotime($proximo['data_hora'])) . ').');
        } elseif ($evento['tipo'] === 'F') {
            $posterior = pcp_apontamento_one($conn, 'SELECT PCP003_Id AS id FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Id<>? AND PCP003_Data_Hora>? LIMIT 1', 'iis', [$participanteId, $eventoId, $dataHora]);
            if ($posterior) throw new DomainException('A finalização não pode ficar antes de outro evento do colaborador.');
        } elseif ($dataHora < $evento['inicio'] || ($evento['fim'] !== null && $dataHora > $evento['fim'])) throw new DomainException('O evento precisa estar dentro do período da participação.');
        if ($evento['tipo'] === 'P') {
            if (!$motivoPausa || !pcp_apontamento_one($conn, 'SELECT SMT012_Id AS id FROM SMT012 WHERE SMT012_Id=?', 'i', [$motivoPausa])) throw new DomainException('Selecione um motivo de pausa válido.');
        } else $motivoPausa = null;
        pcp_apontamento_stmt($conn, 'UPDATE PCP003 SET PCP003_Data_Hora=?,PCP003_SMT012_Id=? WHERE PCP003_Id=?', 'sii', [$dataHora, $motivoPausa, $eventoId]);
        pcp_apontamento_validar_sequencia_eventos($conn, $participanteId);
        if ($evento['tipo'] === 'I') pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Data_Hora_Inicio=? WHERE PCP002_Id=?', 'si', [$dataHora, $participanteId]);
        if ($evento['tipo'] === 'F') pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Data_Hora_Fim=? WHERE PCP002_Id=?', 'si', [$dataHora, $participanteId]);
        pcp_apontamento_sincronizar_grupo($conn, $grupoId);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'alterar_evento_' . $evento['tipo'], $justificativa, ['evento_id'=>$eventoId, 'data_hora'=>$evento['data_hora'], 'motivo_id'=>$evento['motivo_id']], ['data_hora'=>$dataHora, 'motivo_id'=>$motivoPausa], (int) $context['user_id'], $eventoId);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_adicionar_evento(mysqli $conn, array $context, int $grupoId, int $participanteId, string $tipo, string $dataHora, ?int $motivoPausa, string $justificativa): void
{
    if (!in_array($tipo, ['P', 'R', 'F'], true)) throw new DomainException('Tipo de evento inválido.');
    mysqli_begin_transaction($conn);
    try {
        $participante = pcp_apontamento_one($conn, "SELECT PCP002.PCP002_Id AS id,PCP002.PCP002_Status AS status,PCP002.PCP002_Data_Hora_Inicio AS inicio FROM PCP002 INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? WHERE PCP002.PCP002_Id=? AND PCP002.PCP002_PCP001_Id=? AND PCP001.PCP001_Status<>'I' AND PCP002.PCP002_Status<>'I' FOR UPDATE", 'iii', [(int) $context['company_id'], $participanteId, $grupoId]);
        if (!$participante) throw new DomainException('Colaborador indisponível para novo evento.');
        $ultimo = pcp_apontamento_one($conn, "SELECT PCP003_Tipo AS tipo,PCP003_Data_Hora AS data_hora FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' ORDER BY PCP003_Data_Hora DESC,PCP003_Id DESC LIMIT 1 FOR UPDATE", 'i', [$participanteId]);
        if (!$ultimo || $dataHora < $ultimo['data_hora']) throw new DomainException('A data/hora precisa ser posterior ao último evento.');
        $permitido = ($tipo === 'P' && in_array($ultimo['tipo'], ['I','R'], true)) || ($tipo === 'R' && $ultimo['tipo'] === 'P') || ($tipo === 'F' && in_array($ultimo['tipo'], ['I','P','R'], true));
        if (!$permitido) throw new DomainException('Este evento não é permitido após o último evento registrado.');
        if ($tipo === 'P' && (!$motivoPausa || !pcp_apontamento_one($conn, 'SELECT SMT012_Id AS id FROM SMT012 WHERE SMT012_Id=?', 'i', [$motivoPausa]))) throw new DomainException('Selecione um motivo de pausa válido.');
        if ($tipo !== 'P') $motivoPausa = null;
        pcp_apontamento_stmt($conn, 'INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_SMT012_Id,PCP003_C007_Id) VALUES (?,?,?,?,?,?)', 'iissii', [$grupoId, $participanteId, $tipo, $dataHora, $motivoPausa, (int) $context['user_id']]);
        $novoEventoId = (int) mysqli_insert_id($conn);
        if ($tipo === 'F') pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='F',PCP002_Data_Hora_Fim=? WHERE PCP002_Id=?", 'si', [$dataHora, $participanteId]);
        else pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Status=? WHERE PCP002_Id=?', 'si', [$tipo === 'P' ? 'P' : 'A', $participanteId]);
        pcp_apontamento_sincronizar_grupo($conn, $grupoId);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'incluir_evento_' . $tipo, $justificativa, [], ['tipo'=>$tipo,'data_hora'=>$dataHora,'motivo_id'=>$motivoPausa], (int) $context['user_id'], $novoEventoId);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_cancelar_evento(mysqli $conn, array $context, int $grupoId, int $participanteId, int $eventoId, string $justificativa): void
{
    mysqli_begin_transaction($conn);
    try {
        $evento = pcp_apontamento_one($conn, "SELECT PCP003_Id AS id,PCP003_Tipo AS tipo,PCP003_Data_Hora AS data_hora FROM PCP003 WHERE PCP003_Id=? AND PCP003_PCP001_Id=? AND PCP003_PCP002_Id=? AND PCP003_Status='A' FOR UPDATE", 'iii', [$eventoId, $grupoId, $participanteId]);
        if (!$evento) throw new DomainException('Evento ativo não encontrado.');
        if ($evento['tipo'] === 'I') throw new DomainException('O evento de início não pode ser cancelado.');
        $ids = [$eventoId];
        if ($evento['tipo'] === 'P') $par = pcp_apontamento_one($conn, "SELECT PCP003_Id AS id FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' AND PCP003_Tipo='R' AND PCP003_Data_Hora>? ORDER BY PCP003_Data_Hora,PCP003_Id LIMIT 1", 'is', [$participanteId, $evento['data_hora']]);
        elseif ($evento['tipo'] === 'R') $par = pcp_apontamento_one($conn, "SELECT PCP003_Id AS id FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' AND PCP003_Tipo='P' AND PCP003_Data_Hora<? ORDER BY PCP003_Data_Hora DESC,PCP003_Id DESC LIMIT 1", 'is', [$participanteId, $evento['data_hora']]);
        else $par = null;
        if (isset($par) && !$par) throw new DomainException('A pausa não possui retomada correspondente para cancelamento.');
        if ($par) $ids[] = (int) $par['id'];
        $marks = implode(',', array_fill(0, count($ids), '?'));
        pcp_apontamento_stmt($conn, "UPDATE PCP003 SET PCP003_Status='I',PCP003_Data_Hora_Inativacao=NOW(),PCP003_C007_Id_Inativacao=?,PCP003_Motivo_Inativacao=? WHERE PCP003_Id IN ($marks)", 'is' . str_repeat('i', count($ids)), array_merge([(int) $context['user_id'], $justificativa], $ids));
        $ultimo = pcp_apontamento_one($conn, "SELECT PCP003_Tipo AS tipo,PCP003_Data_Hora AS data_hora FROM PCP003 WHERE PCP003_PCP002_Id=? AND PCP003_Status='A' ORDER BY PCP003_Data_Hora DESC,PCP003_Id DESC LIMIT 1", 'i', [$participanteId]);
        $status = $ultimo['tipo'] === 'P' ? 'P' : 'A'; $fim = $ultimo['tipo'] === 'F' ? $ultimo['data_hora'] : null;
        if ($ultimo['tipo'] === 'F') $status = 'F';
        pcp_apontamento_stmt($conn, 'UPDATE PCP002 SET PCP002_Status=?,PCP002_Data_Hora_Fim=? WHERE PCP002_Id=?', 'ssi', [$status, $fim, $participanteId]);
        pcp_apontamento_sincronizar_grupo($conn, $grupoId);
        pcp_apontamento_edicao_auditar($conn, $grupoId, $participanteId, 'cancelar_evento_' . $evento['tipo'], $justificativa, ['eventos'=>$ids], ['status'=>'I'], (int) $context['user_id'], $eventoId);
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_management_save(mysqli $conn, string $action): void
{
    if ($action === 'create_employee') {
        $d109Id = pcp_apontamento_positive_int($_POST['d109_id'] ?? null);
        $matricula = pcp_apontamento_management_text($_POST['matricula'] ?? '', 250);
        $d109 = $d109Id ? pcp_apontamento_one($conn, "SELECT D109_Id FROM D109 WHERE D109_Id=? AND D109_Flag_Ativo='S'", 'i', [$d109Id]) : null;
        if (!$d109) throw new DomainException('Selecione um funcionário ativo no cadastro mestre antes de incluí-lo no apontamento.');
        if (pcp_apontamento_one($conn, 'SELECT PCP006_Id FROM PCP006 WHERE PCP006_D109_Id=? LIMIT 1', 'i', [$d109Id])) throw new DomainException('Este funcionário já possui cadastro no apontamento.');
        if (pcp_apontamento_one($conn, 'SELECT PCP006_Id FROM PCP006 WHERE PCP006_Matricula=? LIMIT 1', 's', [$matricula])) throw new DomainException('Esta matrícula PCP já está em uso.');
        pcp_apontamento_stmt($conn, "INSERT INTO PCP006 (PCP006_D109_Id,PCP006_Matricula,PCP006_Status) VALUES (?,?,'A')", 'is', [$d109Id, $matricula]);
    } elseif ($action === 'update_employee') {
        $id = pcp_apontamento_positive_int($_POST['pcp006_id'] ?? null); $matricula = pcp_apontamento_management_text($_POST['matricula'] ?? '', 250);
        if (!$id || !pcp_apontamento_one($conn, 'SELECT PCP006_Id FROM PCP006 WHERE PCP006_Id=?', 'i', [$id])) throw new DomainException('Colaborador PCP inválido.');
        if (pcp_apontamento_one($conn, 'SELECT PCP006_Id FROM PCP006 WHERE PCP006_Matricula=? AND PCP006_Id<>? LIMIT 1', 'si', [$matricula, $id])) throw new DomainException('Esta matrícula PCP já está em uso.');
        pcp_apontamento_stmt($conn, 'UPDATE PCP006 SET PCP006_Matricula=? WHERE PCP006_Id=?', 'si', [$matricula, $id]);
    } elseif ($action === 'toggle_employee') {
        $id = pcp_apontamento_positive_int($_POST['pcp006_id'] ?? null); $status = ($_POST['status'] ?? '') === 'A' ? 'A' : 'I';
        $employee = $id ? pcp_apontamento_one($conn, 'SELECT PCP006.PCP006_Id AS id,D109.D109_Flag_Ativo AS d109_status FROM PCP006 INNER JOIN D109 ON D109.D109_Id=PCP006.PCP006_D109_Id WHERE PCP006.PCP006_Id=?', 'i', [$id]) : null;
        if (!$employee) throw new DomainException('Colaborador PCP inválido.');
        if ($status === 'A' && $employee['d109_status'] !== 'S') throw new DomainException('Não é possível ativar no apontamento enquanto o funcionário estiver inativo no cadastro mestre. Regularize o funcionário primeiro.');
        pcp_apontamento_stmt($conn, 'UPDATE PCP006 SET PCP006_Status=? WHERE PCP006_Id=?', 'si', [$status, $id]);
    } elseif ($action === 'link_process') {
        $pcp006Id = pcp_apontamento_positive_int($_POST['pcp006_id'] ?? null); $process = pcp_apontamento_positive_int($_POST['smt006_id'] ?? null);
        $employee = $pcp006Id ? pcp_apontamento_one($conn, "SELECT PCP006.PCP006_Id AS id FROM PCP006 INNER JOIN D109 ON D109.D109_Id=PCP006.PCP006_D109_Id WHERE PCP006.PCP006_Id=? AND PCP006.PCP006_Status='A' AND D109.D109_Flag_Ativo='S'", 'i', [$pcp006Id]) : null;
        if (!$employee || !$process || !pcp_apontamento_one($conn, "SELECT SMT006_Id FROM SMT006 WHERE SMT006_Id=? AND SMT006_Flag_Apontamento='S'", 'i', [$process])) throw new DomainException('Colaborador PCP ativo ou processo liberado inválido.');
        $existing = pcp_apontamento_one($conn, 'SELECT PCP004_Id AS id FROM PCP004 WHERE PCP004_PCP006_Id=? AND PCP004_SMT006_Id=? LIMIT 1', 'ii', [$pcp006Id, $process]);
        if ($existing) pcp_apontamento_stmt($conn, "UPDATE PCP004 SET PCP004_Status='A' WHERE PCP004_Id=?", 'i', [(int) $existing['id']]);
        else pcp_apontamento_stmt($conn, "INSERT INTO PCP004 (PCP004_PCP006_Id,PCP004_SMT006_Id,PCP004_Status) VALUES (?,?,'A')", 'ii', [$pcp006Id, $process]);
    } elseif ($action === 'toggle_process') {
        $id = pcp_apontamento_positive_int($_POST['pcp004_id'] ?? null); $status = ($_POST['status'] ?? '') === 'A' ? 'A' : 'I';
        if (!$id || !pcp_apontamento_one($conn, 'SELECT PCP004_Id FROM PCP004 WHERE PCP004_Id=?', 'i', [$id])) throw new DomainException('Vínculo inválido.');
        pcp_apontamento_stmt($conn, 'UPDATE PCP004 SET PCP004_Status=? WHERE PCP004_Id=?', 'si', [$status, $id]);
    } elseif ($action === 'save_process') {
        $id = pcp_apontamento_positive_int($_POST['id'] ?? null); $description = pcp_apontamento_management_text($_POST['descricao'] ?? '');
        $value = trim((string) ($_POST['valor'] ?? '')); $value = $value === '' ? null : str_replace(',', '.', $value);
        if ($value !== null && (!is_numeric($value) || (float) $value < 0)) throw new DomainException('Valor do processo inválido.');
        $stageId = trim((string) ($_POST['etapa_id'] ?? '')); $stageId = $stageId === '' ? null : pcp_apontamento_positive_int($stageId);
        if (trim((string) ($_POST['etapa_id'] ?? '')) !== '' && !$stageId) throw new DomainException('Etapa inválida.');
        $paint = ($_POST['pintura'] ?? 'N') === 'S' ? 'S' : 'N';
        $apontamento = ($_POST['apontamento'] ?? 'N') === 'S' ? 'S' : 'N';
        if ($id) pcp_apontamento_stmt($conn, 'UPDATE SMT006 SET SMT006_Descricao=?, SMT006_Valor=?, SMT006_Flag_Pintura=?, SMT006_Flag_Apontamento=?, SMT006_SMT201_Id=? WHERE SMT006_Id=?', 'sdssii', [$description, $value, $paint, $apontamento, $stageId, $id]);
        else pcp_apontamento_stmt($conn, 'INSERT INTO SMT006 (SMT006_Descricao,SMT006_Valor,SMT006_Flag_Pintura,SMT006_Flag_Apontamento,SMT006_SMT201_Id) VALUES (?,?,?,?,?)', 'sdssi', [$description, $value, $paint, $apontamento, $stageId]);
    } elseif ($action === 'save_pause') {
        $id = pcp_apontamento_positive_int($_POST['id'] ?? null); $description = pcp_apontamento_management_text($_POST['descricao'] ?? '');
        $color = trim((string) ($_POST['cor'] ?? '')); if ($color !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) throw new DomainException('A cor deve estar no formato #RRGGBB.');
        $required = ($_POST['obriga_quantidade'] ?? 'N') === 'S' ? 'S' : 'N';
        if ($id && array_key_exists('obriga_quantidade', $_POST)) pcp_apontamento_stmt($conn, 'UPDATE SMT012 SET SMT012_Descricao=?, SMT012_Cor=?, SMT012_Obriga_Apontar_Quantidade=? WHERE SMT012_Id=?', 'sssi', [$description, $color ?: null, $required, $id]);
        elseif ($id) pcp_apontamento_stmt($conn, 'UPDATE SMT012 SET SMT012_Descricao=?, SMT012_Cor=? WHERE SMT012_Id=?', 'ssi', [$description, $color ?: null, $id]);
        else pcp_apontamento_stmt($conn, 'INSERT INTO SMT012 (SMT012_Descricao,SMT012_Cor,SMT012_Obriga_Apontar_Quantidade) VALUES (?,?,?)', 'sss', [$description, $color ?: null, $required]);
    } elseif ($action === 'save_rework') {
        $id = pcp_apontamento_positive_int($_POST['id'] ?? null); $description = pcp_apontamento_management_text($_POST['descricao'] ?? ''); $status = ($_POST['status'] ?? 'A') === 'I' ? 'I' : 'A';
        if ($id) pcp_apontamento_stmt($conn, 'UPDATE PCP005 SET PCP005_Descricao=?, PCP005_Status=? WHERE PCP005_Id=?', 'ssi', [$description, $status, $id]);
        else pcp_apontamento_stmt($conn, 'INSERT INTO PCP005 (PCP005_Descricao,PCP005_Status) VALUES (?,?)', 'ss', [$description, $status]);
    } else throw new DomainException('Solicitação de cadastro inválida.');
}

function pcp_apontamento_management_page(mysqli $conn, array $context): void
{
    pcp_apontamento_require_manage();
    if (($_GET['ajax'] ?? '') === 'buscar_d109') {
        $term = trim((string) ($_GET['termo'] ?? ''));
        if (mb_strlen($term, 'UTF-8') < 2) pcp_apontamento_json(['items' => []]);
        $like = '%' . $term . '%';
        $items = pcp_apontamento_all($conn, "SELECT D109.D109_Id AS id,D109.D109_Nome AS nome,D109.D109_Flag_Ativo AS status FROM D109 LEFT JOIN PCP006 ON PCP006.PCP006_D109_Id=D109.D109_Id WHERE PCP006.PCP006_Id IS NULL AND (D109.D109_Nome LIKE ? OR CAST(D109.D109_Id AS CHAR) LIKE ?) ORDER BY D109.D109_Nome LIMIT 15", 'ss', [$like, $like]);
        pcp_apontamento_json(['items' => $items, 'csrf' => pcp_apontamento_csrf_token()]);
    }
    $section = (string) ($_GET['secao'] ?? '');
    if ($section === '') {
        pcp_apontamento_header('Configurações', $context); ?>
        <section class="terminal-card settings-home"><p class="eyebrow">Gestão do módulo</p><h2>Configurações</h2><p>Escolha uma área para configurar o apontamento. A empresa exibida é somente o contexto ativo da sessão Hardness.</p><div class="settings-grid">
            <?php pcp_apontamento_management_card('Colaboradores e processos', 'Localize colaboradores existentes e mantenha suas habilitações de processo.', '◉', 'colaboradores', false); ?>
            <?php pcp_apontamento_management_card('Processos', 'Catálogo global de processos disponíveis para o apontamento.', '▤', 'processos', false); ?>
            <?php pcp_apontamento_management_card('Motivos de pausa', 'Motivos usados ao pausar uma participação.', 'Ⅱ', 'pausas', false); ?>
        </div></section><?php pcp_apontamento_footer(); return;
    }
    if ($section === 'processos') { pcp_apontamento_processes_page($conn, $context); return; }
    if ($section === 'pausas') { pcp_apontamento_pauses_page($conn, $context); return; }
    if ($section === 'retrabalho') {
        header('Location: ' . pcp_apontamento_url('index.php?pagina=configuracoes'));
        return;
    }
    if ($section !== 'colaboradores') pcp_apontamento_abort('Área de configuração inválida.', 404);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        pcp_apontamento_require_csrf();
        $action = (string) ($_POST['acao'] ?? '');
        try { if (!in_array($action, ['create_employee', 'update_employee', 'toggle_employee', 'link_process', 'toggle_process'], true)) throw new DomainException('Esta configuração ainda não está disponível.'); pcp_apontamento_management_save($conn, $action); pcp_apontamento_flash('success', 'Cadastro atualizado.'); }
        catch (Throwable $e) { pcp_apontamento_flash('error', $e instanceof DomainException ? $e->getMessage() : 'Não foi possível atualizar o cadastro.'); }
        header('Location: ' . pcp_apontamento_url('index.php?pagina=configuracoes&secao=colaboradores')); exit;
    }
    $search = trim((string) ($_GET['colaborador'] ?? '')); $status = in_array(($_GET['status'] ?? ''), ['A', 'I'], true) ? (string) $_GET['status'] : '';
    $employeeSql = "SELECT PCP006.PCP006_Id AS pcp006_id,PCP006.PCP006_Matricula AS matricula,PCP006.PCP006_Status AS pcp_status,D109.D109_Id AS d109_id,D109.D109_Nome AS nome,D109.D109_Flag_Ativo AS d109_status FROM PCP006 INNER JOIN D109 ON D109.D109_Id=PCP006.PCP006_D109_Id";
    $where = []; $types = ''; $values = [];
    if ($search !== '') { $where[] = '(PCP006.PCP006_Matricula LIKE ? OR D109.D109_Nome LIKE ?)'; $types .= 'ss'; $values[] = '%' . $search . '%'; $values[] = '%' . $search . '%'; }
    if ($status !== '') { $where[] = 'PCP006.PCP006_Status=?'; $types .= 's'; $values[] = $status; }
    $employees = pcp_apontamento_all($conn, $employeeSql . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY D109.D109_Nome, PCP006.PCP006_Id', $types, $values);
    $processes = pcp_apontamento_all($conn, "SELECT SMT006_Id AS id,SMT006_Descricao AS descricao,SMT006_Valor AS valor,SMT006_Flag_Pintura AS pintura FROM SMT006 WHERE SMT006_Flag_Apontamento='S' ORDER BY SMT006_Descricao");
    $candidateSearch = trim((string) ($_GET['incluir'] ?? ''));
    $candidates = $candidateSearch === '' ? [] : pcp_apontamento_all($conn, "SELECT D109.D109_Id AS id,D109.D109_Nome AS nome,D109.D109_Flag_Ativo AS status FROM D109 LEFT JOIN PCP006 ON PCP006.PCP006_D109_Id=D109.D109_Id WHERE PCP006.PCP006_Id IS NULL AND (D109.D109_Nome LIKE ? OR CAST(D109.D109_Id AS CHAR) LIKE ?) ORDER BY D109.D109_Nome LIMIT 20", 'ss', ['%' . $candidateSearch . '%', '%' . $candidateSearch . '%']);
    pcp_apontamento_header('Colaboradores e processos', $context); ?>
    <a class="back-link" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=configuracoes')); ?>">← Configurações</a>
    <?php pcp_apontamento_employees_table($conn, $employees, $processes, $candidateSearch, $candidates, $search, $status); pcp_apontamento_footer(); }

function pcp_apontamento_employees_table(mysqli $conn, array $employees, array $processes, string $candidateSearch, array $candidates, string $search, string $status): void
{ ?>

<section class="terminal-card"><div class="section-head"><div><p class="eyebrow">Gestão do módulo</p><h2>Colaboradores e processos</h2></div><button type="button" data-pcp-open-process="include-employee">＋ Incluir colaborador</button></div><dialog class="process-dialog employee-dialog" id="include-employee"><div class="process-dialog__head"><h3>Incluir colaborador</h3></div><div class="employee-dialog__body"><form method="get" class="lookup"><input type="hidden" name="pagina" value="configuracoes"><input type="hidden" name="secao" value="colaboradores"><label>Nome ou ID do funcionário<input name="incluir" value="<?php echo pcp_apontamento_h($candidateSearch); ?>" required></label><button>Localizar</button></form><?php foreach ($candidates as $candidate): ?><form method="post" class="include-candidate"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><input type="hidden" name="d109_id" value="<?php echo (int)$candidate['id']; ?>"><strong><?php echo pcp_apontamento_h($candidate['nome']); ?></strong><input name="matricula" maxlength="250" placeholder="Matrícula PCP" required><button name="acao" value="create_employee">Incluir</button></form><?php endforeach; ?></div></dialog><form method="get" class="lookup"><input type="hidden" name="pagina" value="configuracoes"><input type="hidden" name="secao" value="colaboradores"><label>Nome ou matrícula PCP<input name="colaborador" value="<?php echo pcp_apontamento_h($search); ?>"></label><label>Status PCP<select name="status"><option value="">Todos</option><option value="A"<?php echo $status==='A'?' selected':''; ?>>Ativos</option><option value="I"<?php echo $status==='I'?' selected':''; ?>>Inativos</option></select></label><button>Filtrar</button></form><div class="process-table"><table><thead><tr><th>Colaborador</th><th>Matrícula PCP</th><th>Status PCP</th><th>Funcionário</th><th>Processos</th><th></th></tr></thead><tbody><?php foreach ($employees as $employee): $links=pcp_apontamento_all($conn,'SELECT PCP004.PCP004_Id AS id,PCP004.PCP004_Status AS status,SMT006.SMT006_Descricao AS descricao,SMT006.SMT006_Flag_Apontamento AS apontamento FROM PCP004 INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP004.PCP004_SMT006_Id WHERE PCP004.PCP004_PCP006_Id=? ORDER BY SMT006.SMT006_Descricao','i',[(int)$employee['pcp006_id']]); ?><tr><td><?php echo pcp_apontamento_h($employee['nome']); ?></td><td><?php echo pcp_apontamento_h($employee['matricula']); ?></td><td><?php echo $employee['pcp_status']==='A'?'Ativo':'Inativo'; ?></td><td><?php echo $employee['d109_status']==='S'?'Ativo':'Inativo'; ?></td><td><?php echo count($links); ?></td><td><button type="button" class="link-button" data-pcp-open-process="employee-<?php echo (int)$employee['pcp006_id']; ?>">Editar</button><dialog class="process-dialog employee-dialog" id="employee-<?php echo (int)$employee['pcp006_id']; ?>"><div class="process-dialog__head"><h3><?php echo pcp_apontamento_h($employee['nome']); ?></h3></div><div class="employee-dialog__body"><p>Matrícula PCP: <b><?php echo pcp_apontamento_h($employee['matricula']); ?></b></p><p>Status: <?php echo $employee['pcp_status']==='A'?'Ativo':'Inativo'; ?></p><h4>Processos vinculados</h4><?php foreach($links as $link): ?><form method="post" class="linked-process"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><input type="hidden" name="pcp004_id" value="<?php echo (int)$link['id']; ?>"><input type="hidden" name="status" value="<?php echo $link['status']==='A'?'I':'A'; ?>"><strong><?php echo pcp_apontamento_h($link['descricao']); ?></strong><?php if($link['status']==='A' && $link['apontamento']!=='S'): ?><span class="process-disabled-alert">Processo desabilitado no apontamento</span><?php endif; ?><button name="acao" value="toggle_process"><?php echo $link['status']==='A'?'Inativar':'Ativar'; ?></button></form><?php endforeach; ?><?php if($employee['pcp_status']==='A' && $employee['d109_status']==='S'): ?><form method="post" class="link-process-form"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><input type="hidden" name="pcp006_id" value="<?php echo (int)$employee['pcp006_id']; ?>"><select name="smt006_id" required><option value="">Vincular processo</option><?php foreach($processes as $process): ?><option value="<?php echo (int)$process['id']; ?>"><?php echo pcp_apontamento_h($process['descricao']); ?></option><?php endforeach; ?></select><button name="acao" value="link_process">Vincular</button></form><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><input type="hidden" name="pcp006_id" value="<?php echo (int)$employee['pcp006_id']; ?>"><input type="hidden" name="status" value="<?php echo $employee['pcp_status']==='A'?'I':'A'; ?>"><button class="employee-toggle-button" name="acao" value="toggle_employee"><?php echo $employee['pcp_status']==='A'?'Inativar colaborador':'Ativar colaborador'; ?></button><button type="button" class="secondary employee-dialog-close" data-pcp-close-process>Fechar</button></form></div></dialog></td></tr><?php endforeach; ?></tbody</table></div></section><?php }

function pcp_apontamento_processes_page(mysqli $conn, array $context): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        pcp_apontamento_require_csrf();
        try { pcp_apontamento_management_save($conn, 'save_process'); pcp_apontamento_flash('success', 'Processo salvo.'); }
        catch (Throwable $e) { pcp_apontamento_flash('error', $e instanceof DomainException ? $e->getMessage() : 'Não foi possível salvar o processo.'); }
        header('Location: ' . pcp_apontamento_url('index.php?pagina=configuracoes&secao=processos')); exit;
    }
    $term = trim((string) ($_GET['busca'] ?? ''));
    $availability = in_array($_GET['apontamento'] ?? '', ['S', 'N'], true) ? (string) $_GET['apontamento'] : '';
    $sql = 'SELECT SMT006_Id AS id,SMT006_Descricao AS descricao,SMT006_Valor AS valor,SMT006_Flag_Pintura AS pintura,SMT006_Flag_Apontamento AS apontamento,SMT006_SMT201_Id AS etapa FROM SMT006';
    $where = []; $types = ''; $values = [];
    if ($term !== '') { $where[] = '(SMT006_Descricao LIKE ? OR CAST(SMT006_Id AS CHAR) LIKE ?)'; $types .= 'ss'; $values[] = '%' . $term . '%'; $values[] = '%' . $term . '%'; }
    if ($availability !== '') { $where[] = 'SMT006_Flag_Apontamento=?'; $types .= 's'; $values[] = $availability; }
    $rows = pcp_apontamento_all($conn, $sql . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY SMT006_Descricao,SMT006_Id', $types, $values);
    pcp_apontamento_header('Processos', $context); ?>
    <a class="back-link" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=configuracoes')); ?>">← Configurações</a>
    <section class="terminal-card"><div class="section-head"><div><p class="eyebrow">Catálogo global</p><h2>Processos</h2><p>Campos do catálogo SMT006. Não há vínculo artificial com empresa.</p></div><details class="include-box"><summary>＋ Incluir processo</summary><?php pcp_apontamento_process_form(null); ?></details></div><form method="get" class="lookup"><input type="hidden" name="pagina" value="configuracoes"><input type="hidden" name="secao" value="processos"><label>Descrição ou ID<input name="busca" value="<?php echo pcp_apontamento_h($term); ?>"></label><label>Apontamento<select name="apontamento"><option value="">Todos</option><option value="S"<?php echo $availability === 'S' ? ' selected' : ''; ?>>Liberados no apontamento</option><option value="N"<?php echo $availability === 'N' ? ' selected' : ''; ?>>Não liberados</option></select></label><button>Filtrar</button></form><div class="process-table"><table><thead><tr><th>ID</th><th>Descrição</th><th>Valor</th><th>Pintura</th><th>Usar no apontamento</th><th>Etapa</th><th></th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td>#<?php echo (int)$row['id']; ?></td><td><?php echo pcp_apontamento_h($row['descricao']); ?></td><td><?php echo $row['valor'] === null ? '—' : pcp_apontamento_h(number_format((float)$row['valor'], 2, ',', '.')); ?></td><td><?php echo $row['pintura'] === 'S' ? 'Sim' : 'Não'; ?></td><td><?php echo $row['apontamento'] === 'S' ? 'Sim' : 'Não'; ?></td><td><?php echo $row['etapa'] ?: '—'; ?></td><td><button type="button" class="link-button" data-pcp-open-process="process-<?php echo (int)$row['id']; ?>">Alterar</button><dialog class="process-dialog" id="process-<?php echo (int)$row['id']; ?>"><div class="process-dialog__head"><p class="eyebrow">Editar processo</p><h3><?php echo pcp_apontamento_h($row['descricao']); ?></h3></div><?php pcp_apontamento_process_form($row); ?></dialog></td></tr><?php endforeach; ?></tbody></table></div></section><?php pcp_apontamento_footer();
}

function pcp_apontamento_process_form(?array $row): void
{ ?><form method="post" class="process-form"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><?php if ($row): ?><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><?php endif; ?><label>Descrição<input name="descricao" maxlength="100" value="<?php echo pcp_apontamento_h($row['descricao'] ?? ''); ?>" required></label><label>Valor<input name="valor" inputmode="decimal" value="<?php echo pcp_apontamento_h($row['valor'] ?? ''); ?>"></label><label>Etapa (ID)<input name="etapa_id" inputmode="numeric" value="<?php echo pcp_apontamento_h($row['etapa'] ?? ''); ?>"></label><label class="check"><input type="checkbox" name="pintura" value="S"<?php echo ($row['pintura'] ?? '') === 'S' ? ' checked' : ''; ?>> Processo de pintura</label><label>Usar no apontamento<select name="apontamento"><option value="N"<?php echo ($row['apontamento'] ?? 'N') === 'N' ? ' selected' : ''; ?>>Não</option><option value="S"<?php echo ($row['apontamento'] ?? '') === 'S' ? ' selected' : ''; ?>>Sim</option></select></label><?php if ($row): ?><button type="button" class="secondary" data-pcp-close-process>Cancelar</button><?php endif; ?><button name="acao" value="save_process"><?php echo $row ? 'Salvar alteração' : 'Incluir processo'; ?></button></form><?php }

function pcp_apontamento_pauses_page(mysqli $conn, array $context): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        pcp_apontamento_require_csrf();
        try { pcp_apontamento_management_save($conn, 'save_pause'); pcp_apontamento_flash('success', 'Motivo de pausa salvo.'); }
        catch (Throwable $e) { pcp_apontamento_flash('error', $e instanceof DomainException ? $e->getMessage() : 'Não foi possível salvar o motivo de pausa.'); }
        header('Location: ' . pcp_apontamento_url('index.php?pagina=configuracoes&secao=pausas')); exit;
    }
    $term = trim((string) ($_GET['busca'] ?? ''));
    $sql = 'SELECT SMT012_Id AS id,SMT012_Descricao AS descricao,SMT012_Cor AS cor FROM SMT012';
    $rows = $term === '' ? pcp_apontamento_all($conn, $sql . ' ORDER BY SMT012_Descricao,SMT012_Id') : pcp_apontamento_all($conn, $sql . ' WHERE SMT012_Descricao LIKE ? OR CAST(SMT012_Id AS CHAR) LIKE ? ORDER BY SMT012_Descricao,SMT012_Id', 'ss', ['%' . $term . '%', '%' . $term . '%']);
    pcp_apontamento_header('Motivos de pausa', $context); ?>
    <a class="back-link" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=configuracoes')); ?>">← Configurações</a>
    <section class="terminal-card"><div class="section-head"><div><p class="eyebrow">Catálogo global</p><h2>Motivos de pausa</h2><p>Motivos disponíveis ao pausar uma participação no apontamento.</p></div><details class="include-box"><summary>＋ Incluir motivo</summary><?php pcp_apontamento_pause_form(null); ?></details></div><form method="get" class="lookup"><input type="hidden" name="pagina" value="configuracoes"><input type="hidden" name="secao" value="pausas"><label>Descrição ou ID<input name="busca" value="<?php echo pcp_apontamento_h($term); ?>"></label><button>Filtrar</button></form><div class="process-table"><table><thead><tr><th>ID</th><th>Descrição</th><th>Cor</th><th></th></tr></thead><tbody><?php foreach ($rows as $row): ?><tr><td>#<?php echo (int)$row['id']; ?></td><td><?php echo pcp_apontamento_h($row['descricao']); ?></td><td><?php if ($row['cor']): ?><span class="color-chip"><i style="background-color:<?php echo pcp_apontamento_h($row['cor']); ?>"></i><?php echo pcp_apontamento_h($row['cor']); ?></span><?php else: ?>—<?php endif; ?></td><td><button type="button" class="link-button" data-pcp-open-process="pause-<?php echo (int)$row['id']; ?>">Alterar</button><dialog class="process-dialog" id="pause-<?php echo (int)$row['id']; ?>"><div class="process-dialog__head"><p class="eyebrow">Editar motivo de pausa</p><h3><?php echo pcp_apontamento_h($row['descricao']); ?></h3></div><?php pcp_apontamento_pause_form($row); ?></dialog></td></tr><?php endforeach; ?></tbody></table></div></section><?php pcp_apontamento_footer();
}

function pcp_apontamento_pause_form(?array $row): void
{ $color = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($row['cor'] ?? '')) ? (string) $row['cor'] : '#195CE0'; ?><form method="post" class="process-form"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><?php if ($row): ?><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><?php endif; ?><label>Descrição<input name="descricao" maxlength="100" value="<?php echo pcp_apontamento_h($row['descricao'] ?? ''); ?>" required></label><label>Cor de destaque<span class="color-picker"><input type="color" name="cor" value="<?php echo pcp_apontamento_h($color); ?>" data-pcp-color><output><?php echo pcp_apontamento_h(strtoupper($color)); ?></output></span></label><?php if ($row): ?><button type="button" class="secondary" data-pcp-close-process>Cancelar</button><?php endif; ?><button name="acao" value="save_pause"><?php echo $row ? 'Salvar alteração' : 'Incluir motivo'; ?></button></form><?php }

function pcp_apontamento_management_card(string $title, string $description, string $icon, string $section, bool $pending): void
{ ?><a class="settings-card" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('index.php?pagina=configuracoes&secao=' . rawurlencode($section))); ?>"><span class="settings-card__icon"><?php echo pcp_apontamento_h($icon); ?></span><span><b><?php echo pcp_apontamento_h($title); ?></b><small><?php echo pcp_apontamento_h($description); ?></small><?php if ($pending): ?><em>Em preparação</em><?php endif; ?></span><span class="settings-card__arrow">›</span></a><?php }

function pcp_apontamento_management_catalog(string $title, string $action, array $rows, array $fields): void
{ ?><section class="terminal-card catalog"><h2><?php echo pcp_apontamento_h($title); ?></h2><table><tbody><?php foreach($rows as $row): ?><tr><td><?php echo pcp_apontamento_h($row['descricao']); ?></td><td><?php echo pcp_apontamento_h(implode(' · ', array_filter([$row['status'] ?? null,$row['cor'] ?? null]))); ?></td><td><details><summary>Editar</summary><form method="post" class="inline-form"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>"><input name="descricao" maxlength="100" value="<?php echo pcp_apontamento_h($row['descricao']); ?>" required><?php if(isset($fields['valor'])): ?><input name="valor" inputmode="decimal" value="<?php echo pcp_apontamento_h($row['valor'] ?? ''); ?>"><label><input type="checkbox" name="pintura" value="S"<?php echo ($row['pintura'] ?? '')==='S'?' checked':''; ?>> Pintura</label><?php endif; ?><?php if(isset($fields['cor'])): ?><input name="cor" maxlength="10" value="<?php echo pcp_apontamento_h($row['cor'] ?? ''); ?>"><label><input type="checkbox" name="obriga_quantidade" value="S"<?php echo ($row['obriga'] ?? '')==='S'?' checked':''; ?>> Exige quantidade</label><?php endif; ?><?php if(isset($fields['status'])): ?><select name="status"><option value="A"<?php echo ($row['status'] ?? '')==='A'?' selected':''; ?>>Ativo</option><option value="I"<?php echo ($row['status'] ?? '')==='I'?' selected':''; ?>>Inativo</option></select><?php endif; ?><button name="acao" value="<?php echo pcp_apontamento_h($action); ?>">Salvar</button></form></details></td></tr><?php endforeach; ?></tbody></table><form method="post" class="start-form"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><label>Descrição<input name="descricao" maxlength="100" required></label><?php if(isset($fields['valor'])): ?><label>Valor<input name="valor" inputmode="decimal"></label><label class="check"><input type="checkbox" name="pintura" value="S"> Processo de pintura</label><?php endif; ?><?php if(isset($fields['cor'])): ?><label>Cor<input name="cor" placeholder="#FFFFFF" maxlength="10"></label><label class="check"><input type="checkbox" name="obriga_quantidade" value="S"> Exige apontar quantidade</label><?php endif; ?><?php if(isset($fields['status'])): ?><label>Status<select name="status"><option value="A">Ativo</option><option value="I">Inativo</option></select></label><?php endif; ?><button name="acao" value="<?php echo pcp_apontamento_h($action); ?>">Adicionar</button></form></section><?php }
