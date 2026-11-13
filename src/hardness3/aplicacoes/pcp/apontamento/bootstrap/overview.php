<?php
declare(strict_types=1);

/* Visão operacional do instante atual. Todas as consultas começam em PCP001
 * e chegam à empresa somente por T255, nunca por parâmetro de tela. */
function pcp_apontamento_visao_geral_atalhos(bool $canManage): array
{
    $atalhos = [
        ['pagina' => 'individual', 'titulo' => 'Iniciar apontamento', 'descricao' => 'Inicie ou acompanhe o apontamento por matrícula.', 'classe' => 'quick-action--individual'],
    ];
    if ($canManage) {
        $atalhos[] = ['pagina' => 'gerenciar', 'titulo' => 'Gerenciar apontamentos', 'descricao' => 'Consulte os apontamentos da empresa ativa.', 'classe' => 'quick-action--admin'];
        $atalhos[] = ['pagina' => 'configuracoes', 'titulo' => 'Configurações', 'descricao' => 'Cadastros e permissões do módulo.', 'classe' => 'quick-action--admin'];
    }
    return $atalhos;
}

function pcp_apontamento_visao_geral_resumo(mysqli $conn, int $companyId): array
{
    $empresa = 'PCP001 INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=?';
    $ultimoEvento = "(SELECT ultimo.PCP003_Tipo FROM PCP003 ultimo WHERE ultimo.PCP003_PCP002_Id=PCP002.PCP002_Id AND ultimo.PCP003_Status='A' ORDER BY ultimo.PCP003_Data_Hora DESC, ultimo.PCP003_Id DESC LIMIT 1)";
    $abertos = pcp_apontamento_one($conn, "SELECT COUNT(*) AS total FROM $empresa WHERE PCP001.PCP001_Status='A'", 'i', [$companyId]);
    $participantes = pcp_apontamento_one($conn, "SELECT
        SUM(PCP002.PCP002_Status='A' AND $ultimoEvento <> 'P') AS atividade,
        SUM((PCP002.PCP002_Status='P') OR (PCP002.PCP002_Status='A' AND $ultimoEvento = 'P')) AS pausados
        FROM $empresa INNER JOIN PCP002 ON PCP002.PCP002_PCP001_Id=PCP001.PCP001_Id
        WHERE PCP001.PCP001_Status='A' AND PCP002.PCP002_Status<>'I'", 'i', [$companyId]);
    $hoje = pcp_apontamento_one($conn, "SELECT COUNT(*) AS total FROM $empresa
        WHERE DATE(PCP001.PCP001_Data_Hora_Inicio)=CURDATE()", 'i', [$companyId]);
    return [
        'grupos_abertos' => (int) ($abertos['total'] ?? 0), // chave preservada para a tela legada
        'participantes_atividade' => (int) ($participantes['atividade'] ?? 0),
        'participantes_pausados' => (int) ($participantes['pausados'] ?? 0),
        'iniciados_hoje' => (int) ($hoje['total'] ?? 0),
    ];
}

function pcp_apontamento_visao_geral_grupos_abertos(mysqli $conn, int $companyId): array
{
    $ultimoEvento = "(SELECT ultimo.PCP003_Tipo FROM PCP003 ultimo WHERE ultimo.PCP003_PCP002_Id=PCP002.PCP002_Id AND ultimo.PCP003_Status='A' ORDER BY ultimo.PCP003_Data_Hora DESC, ultimo.PCP003_Id DESC LIMIT 1)";
    $grupos = pcp_apontamento_all($conn, "SELECT PCP001.PCP001_Id AS id,
        PCP001.PCP001_Data_Hora_Inicio AS inicio, PCP001.PCP001_Flag_Retrabalho AS retrabalho,
        T255.T255_Id AS op_id, SMT006.SMT006_Descricao AS processo,
        COUNT(PCP002.PCP002_Id) AS participantes,
        SUM((PCP002.PCP002_Status='P') OR (PCP002.PCP002_Status='A' AND $ultimoEvento = 'P')) AS pausados
        FROM PCP001
        INNER JOIN T255 ON T255.T255_Id=PCP001.PCP001_T255_Id AND T255.T255_C004_Id=?
        INNER JOIN SMT006 ON SMT006.SMT006_Id=PCP001.PCP001_SMT006_Id
        LEFT JOIN PCP002 ON PCP002.PCP002_PCP001_Id=PCP001.PCP001_Id AND PCP002.PCP002_Status<>'I'
        WHERE PCP001.PCP001_Status='A'
        GROUP BY PCP001.PCP001_Id, PCP001.PCP001_Data_Hora_Inicio, PCP001.PCP001_Flag_Retrabalho, T255.T255_Id, SMT006.SMT006_Descricao
        ORDER BY PCP001.PCP001_Data_Hora_Inicio DESC, PCP001.PCP001_Id DESC LIMIT 10", 'i', [$companyId]);
    foreach ($grupos as &$grupo) {
        $detalhe = pcp_apontamento_consulta_detalhe($conn, $companyId, (int) $grupo['id']);
        $grupo['tempo_liquido'] = $detalhe ? (int) $detalhe['grupo']['tempo_liquido'] : 0;
        $grupo['produto'] = $detalhe['grupo']['produto'] ?? null;
        $grupo['modalidade'] = (int) $grupo['participantes'] >= 2 ? 'Compartilhado' : 'Individual';
        $grupo['em_execucao'] = (int) $grupo['participantes'] > (int) $grupo['pausados'];
        $grupo['motivo_pausa'] = null;
        if (!$grupo['em_execucao'] && (int) $grupo['pausados'] > 0) {
            $grupo['motivo_pausa'] = pcp_apontamento_one($conn, "SELECT SMT012.SMT012_Descricao AS descricao, SMT012.SMT012_Cor AS cor
                FROM PCP003
                INNER JOIN SMT012 ON SMT012.SMT012_Id=PCP003.PCP003_SMT012_Id
                WHERE PCP003.PCP003_PCP001_Id=? AND PCP003.PCP003_Tipo='P' AND PCP003.PCP003_Status='A' AND PCP003.PCP003_PCP002_Id IS NOT NULL
                  AND PCP003.PCP003_Id=(SELECT ultimo.PCP003_Id FROM PCP003 ultimo WHERE ultimo.PCP003_PCP002_Id=PCP003.PCP003_PCP002_Id AND ultimo.PCP003_Status='A' ORDER BY ultimo.PCP003_Data_Hora DESC, ultimo.PCP003_Id DESC LIMIT 1)
                ORDER BY PCP003.PCP003_Data_Hora DESC, PCP003.PCP003_Id DESC LIMIT 1", 'i', [(int) $grupo['id']]);
        }
    }
    unset($grupo);
    return $grupos;
}
