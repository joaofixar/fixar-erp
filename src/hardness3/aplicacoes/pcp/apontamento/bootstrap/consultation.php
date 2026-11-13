<?php
declare(strict_types=1);

/* Consulta somente-leitura da gestão. O cálculo fica aqui para poder ser
 * reutilizado pelo futuro dashboard sem acoplar-se à interface. */
function pcp_apontamento_tempo_liquido(string $inicio, ?string $fim, string $status, array $eventos, ?int $agora = null): int
{
    $inicioTs = strtotime($inicio);
    if ($inicioTs === false) return 0;
    $limite = $fim ? strtotime($fim) : ($agora ?? time());
    if ($limite === false || $limite < $inicioTs) return 0;
    $pausadoEm = null;
    $pausado = $status === 'P';
    $descontado = 0;
    foreach ($eventos as $evento) {
        $quando = strtotime((string) ($evento['data_hora'] ?? $evento['PCP003_Data_Hora'] ?? ''));
        if ($quando === false || $quando < $inicioTs || $quando > $limite) continue;
        $tipo = (string) ($evento['tipo'] ?? $evento['PCP003_Tipo'] ?? '');
        if ($tipo === 'P' && $pausadoEm === null) { $pausadoEm = $quando; $pausado = true; }
        if ($tipo === 'R' && $pausadoEm !== null) { $descontado += max(0, $quando - $pausadoEm); $pausadoEm = null; $pausado = false; }
    }
    if ($pausadoEm !== null) $descontado += max(0, $limite - $pausadoEm);
    return max(0, ($limite - $inicioTs) - $descontado);
}

function pcp_apontamento_status_grupo(array $grupo): string
{
    if (($grupo['status'] ?? '') === 'F') return 'F';
    if (($grupo['status'] ?? '') === 'I') return 'I';
    if ((int) ($grupo['ativos'] ?? 0) > 0) return 'A';
    return (int) ($grupo['pausados'] ?? 0) > 0 ? 'P' : 'A';
}

function pcp_apontamento_formata_tempo(int $segundos): string
{
    $segundos = max(0, $segundos);
    return sprintf('%02d:%02d:%02d', intdiv($segundos, 3600), intdiv($segundos % 3600, 60), $segundos % 60);
}

/** @return array<int,array{descricao:string,total:int}> */
function pcp_apontamento_produtos_das_ops(mysqli $conn, array $opIds): array
{
    static $estruturaDisponivel = null;
    $opIds = array_values(array_unique(array_filter(array_map('intval', $opIds), static fn(int $id): bool => $id > 0)));
    if (!$opIds) return [];
    if ($estruturaDisponivel === null) {
        $tabelas = pcp_apontamento_one($conn, "SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('T256','D009','D049','D001')");
        $estruturaDisponivel = (int) ($tabelas['total'] ?? 0) === 4;
    }
    if (!$estruturaDisponivel) return [];
    $marcadores = implode(',', array_fill(0, count($opIds), '?'));
    $linhas = pcp_apontamento_all($conn, "SELECT T256.T256_T255_Id AS op_id,
        MIN(CONCAT_WS(' - ', NULLIF(D001.D001_Codigo_Produto,''), NULLIF(D001.D001_Descricao_Produto,''))) AS descricao,
        COUNT(DISTINCT D001.D001_Id) AS total
        FROM T256 INNER JOIN D009 ON D009.D009_Id=T256.T256_D009_Id
        INNER JOIN D049 ON D049.D049_Id=D009.D009_D049_Id
        INNER JOIN D001 ON D001.D001_Id=D049.D049_D001_Id
        WHERE T256.T256_T255_Id IN ($marcadores)
        GROUP BY T256.T256_T255_Id", str_repeat('i', count($opIds)), $opIds);
    $produtos = [];
    foreach ($linhas as $linha) {
        $descricao = trim((string) ($linha['descricao'] ?? ''));
        if ($descricao !== '') $produtos[(int) $linha['op_id']] = ['descricao'=>$descricao, 'total'=>(int) $linha['total']];
    }
    return $produtos;
}

function pcp_apontamento_tempo_liquido_grupo(array $grupo, array $participantes, array $eventos, ?int $agora = null): int
{
    /* O grupo está em curso enquanto ao menos um participante está ativo.
     * Assim, pausa simultânea de todos não aumenta o relógio e pausas de
     * participantes diferentes não são descontadas duas vezes. */
    $inicio = strtotime((string) $grupo['inicio']);
    $fim = !empty($grupo['fim']) ? strtotime((string) $grupo['fim']) : ($agora ?? time());
    if ($inicio === false || $fim === false || $fim < $inicio) return 0;
    $estado = [];
    foreach ($participantes as $p) $estado[(int) $p['id']] = true;
    $ativo = count($estado); $cursor = $inicio; $total = 0;
    foreach ($eventos as $evento) {
        $quando = strtotime((string) $evento['data_hora']); $id = (int) ($evento['participante_id'] ?? 0);
        if ($quando === false || $quando < $inicio || $quando > $fim || !$id) continue;
        if ($ativo > 0) $total += $quando - $cursor;
        $tipo = (string) $evento['tipo'];
        if (($tipo === 'P' || $tipo === 'F') && !empty($estado[$id])) { $estado[$id] = false; --$ativo; }
        if ($tipo === 'R' && empty($estado[$id])) { $estado[$id] = true; ++$ativo; }
        $cursor = $quando;
    }
    if ($ativo > 0) $total += $fim - $cursor;
    return max(0, $total);
}

function pcp_apontamento_consulta_filtros(array $input): array
{
    $data = static function ($valor): ?string {
        $valor = trim((string) $valor);
        if ($valor === '') return null;
        $d = DateTime::createFromFormat('!Y-m-d', $valor);
        if (!$d || $d->format('Y-m-d') !== $valor) throw new DomainException('Informe datas válidas no período de início.');
        return $valor;
    };
    $inicioDe = $data($input['inicio_de'] ?? '');
    $inicioAte = $data($input['inicio_ate'] ?? '');
    if ($inicioDe && $inicioAte && ($inicioDe > $inicioAte || strtotime($inicioAte) - strtotime($inicioDe) > 366 * 86400)) throw new DomainException('O período de início deve ter no máximo 366 dias.');
    $inteiro = static function ($valor, string $nome): ?int { if ($valor === null || $valor === '') return null; $id = pcp_apontamento_positive_int($valor); if (!$id) throw new DomainException($nome . ' inválido.'); return $id; };
    // A consulta operacional abre focada no trabalho em curso; o histórico
    // permanece acessível pelos demais filtros de status.
    $status = (string) ($input['status'] ?? 'aberto');
    $retrabalho = (string) ($input['retrabalho'] ?? 'todos');
    if (!in_array($status, ['todos', 'aberto', 'pausado', 'finalizado'], true)) throw new DomainException('Status inválido.');
    if (!in_array($retrabalho, ['todos', 'sim', 'nao'], true)) throw new DomainException('Filtro de retrabalho inválido.');
    $matricula = trim((string) ($input['matricula'] ?? ''));
    if (mb_strlen($matricula, 'UTF-8') > 250) throw new DomainException('Matrícula inválida.');
    return ['inicio_de'=>$inicioDe, 'inicio_ate'=>$inicioAte, 'op_id'=>$inteiro($input['op_id'] ?? null, 'OP'), 'processo_id'=>$inteiro($input['processo_id'] ?? null, 'Processo'), 'colaborador_id'=>$inteiro($input['colaborador_id'] ?? null, 'Colaborador'), 'matricula'=>$matricula, 'status'=>$status, 'retrabalho'=>$retrabalho];
}

function pcp_apontamento_consulta_grupos(mysqli $conn, int $companyId, array $filtros): array
{
    $where = ['T255.T255_C004_Id=?'];
    $types = 'i'; $values = [$companyId];
    if ($filtros['inicio_de']) { $where[] = 'PCP001.PCP001_Data_Hora_Inicio >= ?'; $types .= 's'; $values[] = $filtros['inicio_de']; }
    if ($filtros['inicio_ate']) { $where[] = 'PCP001.PCP001_Data_Hora_Inicio < DATE_ADD(?, INTERVAL 1 DAY)'; $types .= 's'; $values[] = $filtros['inicio_ate']; }
    foreach (['op_id'=>'PCP001.PCP001_T255_Id', 'processo_id'=>'PCP001.PCP001_SMT006_Id'] as $filtro => $campo) if ($filtros[$filtro]) { $where[] = $campo . '=?'; $types .= 'i'; $values[] = $filtros[$filtro]; }
    if ($filtros['colaborador_id']) { $where[] = 'EXISTS (SELECT 1 FROM PCP002 filtro_p WHERE filtro_p.PCP002_PCP001_Id=PCP001.PCP001_Id AND filtro_p.PCP002_D109_Id=?)'; $types .= 'i'; $values[] = $filtros['colaborador_id']; }
    if ($filtros['matricula'] !== '') { $where[] = 'EXISTS (SELECT 1 FROM PCP002 filtro_m INNER JOIN PCP006 ON PCP006.PCP006_D109_Id=filtro_m.PCP002_D109_Id WHERE filtro_m.PCP002_PCP001_Id=PCP001.PCP001_Id AND PCP006.PCP006_Matricula=?)'; $types .= 's'; $values[] = $filtros['matricula']; }
    if ($filtros['retrabalho'] !== 'todos') { $where[] = "PCP001.PCP001_Flag_Retrabalho='" . ($filtros['retrabalho'] === 'sim' ? 'S' : 'N') . "'"; }
    $having = '';
    if ($filtros['status'] === 'finalizado') $having = " HAVING PCP001.PCP001_Status='F'";
    if ($filtros['status'] === 'aberto') $having = " HAVING PCP001.PCP001_Status='A' AND (SUM(PCP002.PCP002_Status='A') > 0 OR COUNT(PCP002.PCP002_Id)=0)";
    if ($filtros['status'] === 'pausado') $having = " HAVING PCP001.PCP001_Status='A' AND SUM(PCP002.PCP002_Status='A')=0 AND SUM(PCP002.PCP002_Status='P')>0";
    $sql = "SELECT PCP001.PCP001_Id AS id, PCP001.PCP001_Status AS status, PCP001.PCP001_Data_Hora_Inicio AS inicio, PCP001.PCP001_Data_Hora_Fim AS fim, PCP001.PCP001_Flag_Retrabalho AS retrabalho, PCP001.PCP001_PCP005_Id AS motivo_retrabalho_id, T255.T255_Id AS op_id, SMT006.SMT006_Descricao AS processo, PCP005.PCP005_Descricao AS motivo_retrabalho, COUNT(PCP002.PCP002_Id) AS participantes, SUM(PCP002.PCP002_Status='A') AS ativos, SUM(PCP002.PCP002_Status='P') AS pausados FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP001.PCP001_SMT006_Id LEFT JOIN PCP005 ON PCP005.PCP005_Id=PCP001.PCP001_PCP005_Id LEFT JOIN PCP002 ON PCP002.PCP002_PCP001_Id=PCP001.PCP001_Id WHERE " . implode(' AND ', $where) . ' GROUP BY PCP001.PCP001_Id, PCP001.PCP001_Status, PCP001.PCP001_Data_Hora_Inicio, PCP001.PCP001_Data_Hora_Fim, PCP001.PCP001_Flag_Retrabalho, PCP001.PCP001_PCP005_Id, T255.T255_Id, SMT006.SMT006_Descricao, PCP005.PCP005_Descricao' . $having . ' ORDER BY PCP001.PCP001_Data_Hora_Inicio DESC LIMIT 101';
    $rows = pcp_apontamento_all($conn, $sql, $types, $values); $produtos = pcp_apontamento_produtos_das_ops($conn, array_column($rows, 'op_id')); $saida = [];
    foreach ($rows as $row) { $row['produto'] = $produtos[(int) $row['op_id']] ?? null; if ($row['produto']) $row['processo'] .= "\nProduto: " . $row['produto']['descricao'] . ((int) $row['produto']['total'] > 1 ? ' (+' . ((int) $row['produto']['total'] - 1) . ')' : ''); $row['status_operacional'] = pcp_apontamento_status_grupo($row); if ($filtros['status'] === 'todos' || $row['status_operacional'] === ['aberto'=>'A', 'pausado'=>'P', 'finalizado'=>'F'][$filtros['status']]) $saida[] = $row; }
    return array_slice($saida, 0, 100);
}

function pcp_apontamento_consulta_detalhe(mysqli $conn, int $companyId, int $grupoId): ?array
{
    $grupo = pcp_apontamento_one($conn, "SELECT PCP001.PCP001_Id AS id, PCP001.PCP001_Status AS status, PCP001.PCP001_Data_Hora_Inicio AS inicio, PCP001.PCP001_Data_Hora_Fim AS fim, PCP001.PCP001_Flag_Retrabalho AS retrabalho, PCP001.PCP001_SMT006_Id AS processo_id, T255.T255_Id AS op_id, SMT006.SMT006_Descricao AS processo, PCP005.PCP005_Descricao AS motivo_retrabalho FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP001.PCP001_SMT006_Id LEFT JOIN PCP005 ON PCP005.PCP005_Id=PCP001.PCP001_PCP005_Id WHERE PCP001.PCP001_Id=? LIMIT 1", 'ii', [$companyId, $grupoId]);
    if (!$grupo) return null;
    $grupo['produto'] = pcp_apontamento_produtos_das_ops($conn, [(int) $grupo['op_id']])[(int) $grupo['op_id']] ?? null;
    $participantes = pcp_apontamento_all($conn, 'SELECT PCP002.PCP002_Id AS id, PCP002.PCP002_D109_Id AS colaborador_id, D109.D109_Nome AS nome, PCP006.PCP006_Matricula AS matricula, PCP002.PCP002_Status AS status, PCP002.PCP002_Data_Hora_Inicio AS inicio, PCP002.PCP002_Data_Hora_Fim AS fim FROM PCP002 INNER JOIN D109 ON D109.D109_Id=PCP002.PCP002_D109_Id LEFT JOIN PCP006 ON PCP006.PCP006_D109_Id=PCP002.PCP002_D109_Id WHERE PCP002.PCP002_PCP001_Id=? ORDER BY D109.D109_Nome, PCP002.PCP002_Id', 'i', [$grupoId]);
    static $temStatusEvento = null;
    if ($temStatusEvento === null) $temStatusEvento = pcp_apontamento_one($conn, "SELECT 1 AS existe FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='PCP003' AND COLUMN_NAME='PCP003_Status' LIMIT 1") !== null;
    $campoStatusEvento = $temStatusEvento ? 'PCP003.PCP003_Status AS status_evento' : "'A' AS status_evento";
    $eventos = pcp_apontamento_all($conn, "SELECT PCP003.PCP003_Id AS id, PCP003.PCP003_PCP002_Id AS participante_id, D109.D109_Nome AS participante_nome, PCP003.PCP003_Tipo AS tipo, PCP003.PCP003_Data_Hora AS data_hora, SMT012.SMT012_Descricao AS motivo_pausa, $campoStatusEvento FROM PCP003 LEFT JOIN PCP002 evento_participante ON evento_participante.PCP002_Id=PCP003.PCP003_PCP002_Id LEFT JOIN D109 ON D109.D109_Id=evento_participante.PCP002_D109_Id LEFT JOIN SMT012 ON SMT012.SMT012_Id=PCP003.PCP003_SMT012_Id WHERE PCP003.PCP003_PCP001_Id=? ORDER BY PCP003.PCP003_Data_Hora, PCP003.PCP003_Id", 'i', [$grupoId]);
    $eventosAtivos = array_values(array_filter($eventos, static fn(array $e): bool => $e['status_evento'] === 'A'));
    foreach ($participantes as &$p) { $p['tempo_liquido'] = pcp_apontamento_tempo_liquido($p['inicio'], $p['fim'], $p['status'], array_values(array_filter($eventosAtivos, static fn(array $e): bool => (int) $e['participante_id'] === (int) $p['id']))); } unset($p);
    $grupo['status_operacional'] = pcp_apontamento_status_grupo(['status'=>$grupo['status'], 'ativos'=>count(array_filter($participantes, static fn(array $p): bool => $p['status'] === 'A')), 'pausados'=>count(array_filter($participantes, static fn(array $p): bool => $p['status'] === 'P'))]);
    $grupo['tempo_liquido'] = pcp_apontamento_tempo_liquido_grupo($grupo, $participantes, $eventosAtivos);
    foreach ($eventos as &$evento) if ($evento['status_evento'] === 'I') $evento['tipo'] = 'C' . $evento['tipo']; unset($evento);
    $auditoria = pcp_apontamento_all($conn, "SELECT PCP007.PCP007_Id AS id, PCP007.PCP007_PCP002_Id AS participante_id, PCP007.PCP007_PCP003_Id AS evento_id, PCP007.PCP007_Acao AS acao, PCP007.PCP007_Justificativa AS justificativa, PCP007.PCP007_Dados_Anteriores AS dados_anteriores, PCP007.PCP007_Dados_Novos AS dados_novos, PCP007.PCP007_Data_Hora AS data_hora, COALESCE(NULLIF(C007.C007_Primeiro_Nome,''),NULLIF(C007.C007_Nome,''),NULLIF(C007.C007_Login,''),'Usuário') AS usuario FROM PCP007 LEFT JOIN C007 ON C007.C007_Id=PCP007.PCP007_C007_Id WHERE PCP007.PCP007_PCP001_Id=? ORDER BY PCP007.PCP007_Data_Hora DESC, PCP007.PCP007_Id DESC", 'i', [$grupoId]);
    $eventosPorId = [];
    foreach ($eventos as $evento) $eventosPorId[(int) $evento['id']] = $evento;
    foreach ($auditoria as &$registro) {
        $anterior = json_decode((string) ($registro['dados_anteriores'] ?? ''), true) ?: [];
        $novo = json_decode((string) ($registro['dados_novos'] ?? ''), true) ?: [];
        if (empty($registro['evento_id'])) {
            $eventoId = (int) ($anterior['evento_id'] ?? 0);
            if (!$eventoId && !empty($anterior['eventos']) && is_array($anterior['eventos'])) $eventoId = (int) ($anterior['eventos'][0] ?? 0);
            if (!$eventoId && isset($novo['tipo'], $novo['data_hora'])) {
                foreach ($eventos as $evento) {
                    if ((int) $evento['participante_id'] === (int) $registro['participante_id'] && ltrim((string) $evento['tipo'], 'C') === $novo['tipo'] && $evento['data_hora'] === $novo['data_hora']) { $eventoId = (int) $evento['id']; break; }
                }
            }
            if ($eventoId) $registro['evento_id'] = $eventoId;
        }
        $eventoRelacionado = $eventosPorId[(int) ($registro['evento_id'] ?? 0)] ?? null;
        $registro['evento_data_hora'] = $eventoRelacionado['data_hora'] ?? '';
        $registro['evento_tipo'] = ltrim((string) ($eventoRelacionado['tipo'] ?? ''), 'C');
    }
    unset($registro);
    return ['grupo'=>$grupo, 'participantes'=>$participantes, 'eventos'=>$eventos, 'auditoria'=>$auditoria];
}

function pcp_apontamento_consulta_opcoes(mysqli $conn, int $companyId): array
{
    return ['processos'=>pcp_apontamento_all($conn, 'SELECT SMT006_Id AS id, SMT006_Descricao AS descricao FROM SMT006 ORDER BY SMT006_Descricao LIMIT 300'), 'colaboradores'=>pcp_apontamento_all($conn, 'SELECT DISTINCT D109.D109_Id AS id, D109.D109_Nome AS nome FROM PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=? INNER JOIN PCP002 ON PCP002.PCP002_PCP001_Id=PCP001.PCP001_Id INNER JOIN D109 ON D109.D109_Id=PCP002.PCP002_D109_Id ORDER BY D109.D109_Nome LIMIT 300', 'i', [$companyId])];
}
