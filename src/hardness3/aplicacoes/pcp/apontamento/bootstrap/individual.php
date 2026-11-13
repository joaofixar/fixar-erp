<?php
declare(strict_types=1);

/* Consultas e mutações do fluxo individual. IDs vindos do navegador nunca
 * bastam: cada escrita recompõe colaborador, OP, processo e empresa no banco. */
function pcp_apontamento_stmt(mysqli $conn, string $sql, string $types = '', array $values = []): mysqli_stmt
{
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) throw new RuntimeException('Não foi possível preparar a operação.');
    if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$values);
    if (!mysqli_stmt_execute($stmt)) throw new RuntimeException('Não foi possível concluir a operação: ' . mysqli_stmt_error($stmt));
    return $stmt;
}

function pcp_apontamento_one(mysqli $conn, string $sql, string $types = '', array $values = []): ?array
{
    $stmt = pcp_apontamento_stmt($conn, $sql, $types, $values);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);
    return $row ?: null;
}

function pcp_apontamento_all(mysqli $conn, string $sql, string $types = '', array $values = []): array
{
    $stmt = pcp_apontamento_stmt($conn, $sql, $types, $values);
    $result = mysqli_stmt_get_result($stmt);
    $rows = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    mysqli_stmt_close($stmt);
    return $rows;
}

function pcp_apontamento_colaborador(mysqli $conn, string $matricula, bool $lock = false, bool $exigirPcpAtivo = true): ?array
{
    $suffix = $lock ? ' FOR UPDATE' : '';
    $status = $exigirPcpAtivo ? " AND PCP006.PCP006_Status='A'" : '';
    return pcp_apontamento_one($conn,
        "SELECT D109.D109_Id AS id, D109.D109_Nome AS nome, PCP006.PCP006_Matricula AS matricula
         FROM PCP006 INNER JOIN D109 ON D109.D109_Id=PCP006.PCP006_D109_Id
         WHERE PCP006.PCP006_Matricula=? AND D109.D109_Flag_Ativo='S'" . $status . "
         LIMIT 1" . $suffix, 's', [$matricula]);
}

function pcp_apontamento_aberto(mysqli $conn, int $companyId, int $d109Id, bool $lock = false): ?array
{
    $suffix = $lock ? ' FOR UPDATE' : '';
    return pcp_apontamento_one($conn,
        "SELECT PCP002.PCP002_Id AS id, PCP002.PCP002_PCP001_Id AS grupo_id, PCP002.PCP002_Status AS status,
                PCP002.PCP002_Data_Hora_Inicio AS inicio, PCP001.PCP001_Flag_Retrabalho AS retrabalho,
                T255.T255_Id AS op_id, SMT006.SMT006_Descricao AS processo,
                (SELECT COUNT(*) FROM PCP002 participante
                 WHERE participante.PCP002_PCP001_Id=PCP001.PCP001_Id
                   AND participante.PCP002_Status IN ('A','P')) AS participantes_abertos,
                GREATEST(0, TIMESTAMPDIFF(SECOND, PCP002.PCP002_Data_Hora_Inicio, NOW()) - COALESCE((
                    SELECT SUM(TIMESTAMPDIFF(SECOND, pausa.PCP003_Data_Hora, COALESCE((
                        SELECT MIN(retorno.PCP003_Data_Hora) FROM PCP003 retorno
                        WHERE retorno.PCP003_PCP002_Id=pausa.PCP003_PCP002_Id
                          AND retorno.PCP003_Tipo='R' AND retorno.PCP003_Status='A' AND retorno.PCP003_Data_Hora > pausa.PCP003_Data_Hora
                    ), NOW()))) FROM PCP003 pausa
                    WHERE pausa.PCP003_PCP002_Id=PCP002.PCP002_Id AND pausa.PCP003_Tipo='P' AND pausa.PCP003_Status='A'
                ), 0)) AS segundos
         FROM PCP002
         INNER JOIN PCP001 ON PCP001.PCP001_Id=PCP002.PCP002_PCP001_Id
         INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=?
         INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP001.PCP001_SMT006_Id
         WHERE PCP002.PCP002_D109_Id=? AND PCP002.PCP002_Status IN ('A','P') AND PCP001.PCP001_Status='A'
         ORDER BY PCP002.PCP002_Id DESC LIMIT 1" . $suffix, 'ii', [$companyId, $d109Id]);
}

function pcp_apontamento_opcoes_inicio(mysqli $conn, int $companyId, int $d109Id): array
{
    $processos = pcp_apontamento_all($conn,
        "SELECT SMT006.SMT006_Id AS smt006_id, SMT006.SMT006_Descricao AS descricao
         FROM PCP006 INNER JOIN PCP004 ON PCP004.PCP004_PCP006_Id=PCP006.PCP006_Id
         INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP004.PCP004_SMT006_Id
         WHERE PCP006.PCP006_D109_Id=? AND PCP006.PCP006_Status='A' AND PCP004.PCP004_Status='A'
           AND SMT006.SMT006_Flag_Apontamento='S' ORDER BY SMT006.SMT006_Descricao", 'i', [$d109Id]);
    return ['processos' => $processos, 'retrabalho' => pcp_apontamento_all($conn, "SELECT PCP005_Id AS id, PCP005_Descricao AS descricao FROM PCP005 WHERE PCP005_Status='A' ORDER BY PCP005_Descricao")];
}

/* Dados de conferência exibidos antes de iniciar o apontamento. A OP é
 * validada novamente dentro da transação de início. */
function pcp_apontamento_resumo_op(mysqli $conn, int $companyId, int $opId, bool $incluirFinalizada = false): ?array
{
    static $temClienteDiretoNaOp = null;
    if ($temClienteDiretoNaOp === null) {
        $coluna = pcp_apontamento_one($conn, "SELECT 1 AS existe FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='T255A' AND COLUMN_NAME='T255A_D024_Id' LIMIT 1");
        $temClienteDiretoNaOp = $coluna !== null;
    }
    $joinClienteDireto = $temClienteDiretoNaOp
        ? ' LEFT JOIN T255A ON T255A.T255A_T255_Id=T255.T255_Id LEFT JOIN D024 cliente_op ON cliente_op.D024_Id=T255A.T255A_D024_Id'
        : '';
    $cliente = $temClienteDiretoNaOp
        ? "COALESCE(MIN(NULLIF(D024.D024_Nome_Empresa,'')), MIN(NULLIF(cliente_op.D024_Nome_Empresa,''))) AS cliente"
        : "MIN(NULLIF(D024.D024_Nome_Empresa,'')) AS cliente";
    $status = $incluirFinalizada ? '' : " AND COALESCE(T255.T255_Flag_Status,'0') != '99'";
    return pcp_apontamento_one($conn,
        "SELECT T255.T255_Id AS id,
                COALESCE(T255.T255_Quantidade, 0) AS quantidade_prevista,
                MIN(CONCAT_WS(' - ', NULLIF(D001.D001_Codigo_Produto,''), NULLIF(D001.D001_Descricao_Produto,''))) AS produto,
                COUNT(DISTINCT T256.T256_Id) AS total_itens,
                $cliente
         FROM T255
         LEFT JOIN T256 ON T256.T256_T255_Id=T255.T255_Id
         LEFT JOIN D009 ON D009.D009_Id=T256.T256_D009_Id
         LEFT JOIN D049 ON D049.D049_Id=D009.D009_D049_Id
         LEFT JOIN D001 ON D001.D001_Id=D049.D049_D001_Id
         LEFT JOIN T006 ON T006.T006_Id=T256.T256_T006_Id
         LEFT JOIN T005 ON T005.T005_Id=T006.T006_T005_Id
         LEFT JOIN D024 ON D024.D024_Id=T005.T005_D024_Id
         $joinClienteDireto
         WHERE T255.T255_Id=? AND T255.T255_C004_Id=? $status
         GROUP BY T255.T255_Id, T255.T255_Quantidade
         LIMIT 1",
        'ii', [$opId, $companyId]);
}

function pcp_apontamento_motivos_pausa(mysqli $conn): array
{
    return pcp_apontamento_all($conn, 'SELECT SMT012_Id AS id, SMT012_Descricao AS descricao FROM SMT012 ORDER BY SMT012_Descricao');
}

/* O bloqueio da OP serializa novas entradas da mesma empresa/OP. Como a
 * empresa é derivada de T255, não há índice nem coluna artificial de empresa
 * em PCP001. Após obter esse lock, a busca abaixo torna a combinação
 * OP/processo/retrabalho/motivo determinística, inclusive quando não existe
 * ainda uma linha PCP001 para bloquear. */
function pcp_apontamento_compartilhado_aberto(mysqli $conn, int $opId, int $smt006Id, bool $retrabalho, ?int $motivoRetrabalho): ?array
{
    return pcp_apontamento_one($conn,
        "SELECT PCP001_Id AS id FROM PCP001
         WHERE PCP001_T255_Id=? AND PCP001_SMT006_Id=? AND PCP001_Status='A'
           AND PCP001_Flag_Retrabalho=? AND PCP001_PCP005_Id <=> ?
         ORDER BY PCP001_Id ASC LIMIT 1 FOR UPDATE",
        'iisi', [$opId, $smt006Id, $retrabalho ? 'S' : 'N', $motivoRetrabalho]);
}

function pcp_apontamento_inicio(mysqli $conn, array $context, string $matricula, int $opId, int $smt006Id, bool $retrabalho, ?int $motivoRetrabalho): array
{
    if (!$retrabalho) $motivoRetrabalho = null;
    mysqli_begin_transaction($conn);
    try {
        $companyId = (int) $context['company_id']; $userId = (int) $context['user_id'];
        $d109 = pcp_apontamento_colaborador($conn, $matricula, true);
        if (!$d109) throw new DomainException('Colaborador ativo não encontrado para esta empresa.');
        if (pcp_apontamento_aberto($conn, $companyId, (int) $d109['id'], true)) throw new DomainException('Este colaborador já possui um apontamento ativo ou pausado.');
        $operacao = pcp_apontamento_one($conn, "SELECT T255_Id AS id FROM T255 WHERE T255_Id=? AND T255_C004_Id=? AND COALESCE(T255_Flag_Status,'0') != '99' LIMIT 1 FOR UPDATE", 'ii', [$opId, $companyId]);
        $processo = pcp_apontamento_one($conn, "SELECT SMT006.SMT006_Descricao AS descricao FROM PCP006 INNER JOIN PCP004 ON PCP004.PCP004_PCP006_Id=PCP006.PCP006_Id INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP004.PCP004_SMT006_Id WHERE PCP006.PCP006_D109_Id=? AND PCP006.PCP006_Status='A' AND PCP004.PCP004_SMT006_Id=? AND PCP004.PCP004_Status='A' AND SMT006.SMT006_Flag_Apontamento='S' LIMIT 1 FOR UPDATE", 'ii', [(int) $d109['id'], $smt006Id]);
        if (!$operacao || !$processo) throw new DomainException('A OP ou processo não é permitido para este colaborador.');
        if ($retrabalho && (!$motivoRetrabalho || !pcp_apontamento_one($conn, "SELECT PCP005_Id FROM PCP005 WHERE PCP005_Id=? AND PCP005_Status='A' LIMIT 1", 'i', [$motivoRetrabalho]))) throw new DomainException('Selecione um motivo ativo para o retrabalho.');
        $existente = pcp_apontamento_compartilhado_aberto($conn, $opId, $smt006Id, $retrabalho, $motivoRetrabalho);
        $entrouEmExistente = $existente !== null;
        if ($existente) {
            $grupoId = (int) $existente['id'];
        } else {
            $flag = $retrabalho ? 'S' : 'N';
            pcp_apontamento_stmt($conn, 'INSERT INTO PCP001 (PCP001_T255_Id,PCP001_SMT006_Id,PCP001_Status,PCP001_Data_Hora_Inicio,PCP001_Flag_Retrabalho,PCP001_PCP005_Id,PCP001_C007_Id) VALUES (?,?,\'A\',NOW(),?,?,?)', 'iisii', [$opId, $smt006Id, $flag, $motivoRetrabalho, $userId]);
            $grupoId = mysqli_insert_id($conn);
        }
        pcp_apontamento_stmt($conn, 'INSERT INTO PCP002 (PCP002_PCP001_Id,PCP002_D109_Id,PCP002_Status,PCP002_Data_Hora_Inicio) VALUES (?,?,\'A\',NOW())', 'ii', [$grupoId, (int) $d109['id']]);
        $participanteId = mysqli_insert_id($conn);
        pcp_apontamento_stmt($conn, 'INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_C007_Id) VALUES (?,?,\'I\',NOW(),?)', 'iii', [$grupoId, $participanteId, $userId]);
        $participantes = pcp_apontamento_one($conn, "SELECT COUNT(*) AS total FROM PCP002 WHERE PCP002_PCP001_Id=? AND PCP002_Status IN ('A','P')", 'i', [$grupoId]);
        mysqli_commit($conn);
        return ['pcp001_id' => $grupoId, 'entrou_em_existente' => $entrouEmExistente, 'participantes' => (int) ($participantes['total'] ?? 1), 'processo' => (string) $processo['descricao']];
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}

function pcp_apontamento_acao(mysqli $conn, array $context, string $matricula, string $acao, ?int $motivoPausa): void
{
    mysqli_begin_transaction($conn);
    try {
        $companyId = (int) $context['company_id']; $userId = (int) $context['user_id'];
        // Uma inativação posterior não apaga o histórico nem impede finalizar
        // uma participação já existente.
        $d109 = pcp_apontamento_colaborador($conn, $matricula, true, false);
        if (!$d109) throw new DomainException('Colaborador ativo não encontrado para esta empresa.');
        $aberto = pcp_apontamento_aberto($conn, $companyId, (int) $d109['id'], true);
        if (!$aberto) throw new DomainException('Não há apontamento aberto para este colaborador.');
        $status = (string) $aberto['status'];
        if ($acao === 'pause' && $status !== 'A') throw new DomainException('Somente um apontamento ativo pode ser pausado.');
        if ($acao === 'resume' && $status !== 'P') throw new DomainException('Somente um apontamento pausado pode ser retomado.');
        if ($acao === 'pause' && (!$motivoPausa || !pcp_apontamento_one($conn, 'SELECT SMT012_Id FROM SMT012 WHERE SMT012_Id=? LIMIT 1', 'i', [$motivoPausa]))) throw new DomainException('Selecione um motivo de pausa válido.');
        $id = (int) $aberto['id']; $grupoId = (int) $aberto['grupo_id'];
        if ($acao === 'pause') {
            pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='P' WHERE PCP002_Id=?", 'i', [$id]);
            pcp_apontamento_stmt($conn, "INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_SMT012_Id,PCP003_C007_Id) VALUES (?,?,'P',NOW(),?,?)", 'iiii', [$grupoId, $id, $motivoPausa, $userId]);
        } elseif ($acao === 'resume') {
            pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='A' WHERE PCP002_Id=?", 'i', [$id]);
            pcp_apontamento_stmt($conn, "INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_C007_Id) VALUES (?,?,'R',NOW(),?)", 'iii', [$grupoId, $id, $userId]);
        } elseif ($acao === 'finish') {
            // Serializa a última finalização contra uma nova entrada no mesmo
            // PCP001; assim não se adiciona participante a cabeçalho já fechado.
            pcp_apontamento_one($conn, "SELECT PCP001_Id FROM PCP001 WHERE PCP001_Id=? AND PCP001_Status='A' FOR UPDATE", 'i', [$grupoId]);
            pcp_apontamento_stmt($conn, "UPDATE PCP002 SET PCP002_Status='F', PCP002_Data_Hora_Fim=NOW() WHERE PCP002_Id=?", 'i', [$id]);
            pcp_apontamento_stmt($conn, "INSERT INTO PCP003 (PCP003_PCP001_Id,PCP003_PCP002_Id,PCP003_Tipo,PCP003_Data_Hora,PCP003_C007_Id) VALUES (?,?,'F',NOW(),?)", 'iii', [$grupoId, $id, $userId]);
            pcp_apontamento_stmt($conn, "UPDATE PCP001 SET PCP001_Status='F', PCP001_Data_Hora_Fim=NOW() WHERE PCP001_Id=? AND NOT EXISTS (SELECT 1 FROM PCP002 WHERE PCP002_PCP001_Id=? AND PCP002_Status IN ('A','P'))", 'ii', [$grupoId, $grupoId]);
        } else throw new DomainException('Ação inválida.');
        mysqli_commit($conn);
    } catch (Throwable $e) { mysqli_rollback($conn); throw $e; }
}
