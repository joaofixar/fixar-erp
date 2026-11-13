<?php
namespace hardness;

// Subconsulta escalar: extensões do cadastro nunca multiplicam documentos.
function dashboardAnaliseSegmentoSql($cliente)
{
    return "COALESCE((SELECT NULLIF(TRIM(distrito.SMT078_Nome),'')
        FROM D024A cadastroDistrito INNER JOIN SMT078 distrito ON distrito.SMT078_Id=cadastroDistrito.D024A_SMT078_Id
        WHERE cadastroDistrito.D024A_D024_Id=$cliente ORDER BY cadastroDistrito.D024A_Id LIMIT 1),'Não classificado')";
}

function dashboardAnaliseCancelamentoSql($alias)
{
    return "((COALESCE($alias.T003_Flag_Status_Orcamento,'')='C' OR COALESCE($alias.T003_Flag_Perdido,'N')='S')
        AND COALESCE($alias.T003_D047_Id,0)>0)";
}

function dashboardAnaliseMotivoSql($alias)
{
    return "COALESCE((SELECT NULLIF(TRIM(motivo.D047_Descricao_Motivo),'') FROM D047 motivo
        WHERE motivo.D047_Id=$alias.T003_D047_Id),'Motivo não informado')";
}

function dashboardAnaliseAgrupar($linhas, $campo)
{
    $grupos = array();
    foreach ($linhas as $linha) {
        $nome = isset($linha[$campo]) ? trim((string) $linha[$campo]) : '';
        if ($nome === '') $nome = $campo === 'segmento' ? 'Não classificado' : 'Motivo não informado';
        if (!isset($grupos[$nome])) $grupos[$nome] = array($campo => $nome, 'quantidade' => 0, 'valor' => 0.0);
        $grupos[$nome]['quantidade'] += (int) $linha['quantidade'];
        $grupos[$nome]['valor'] += (float) $linha['valor'];
    }
    $grupos = array_values($grupos);
    usort($grupos, function ($a, $b) use ($campo) {
        return $a['quantidade'] === $b['quantidade'] ? strcmp($a[$campo], $b[$campo]) : ($a['quantidade'] > $b['quantidade'] ? -1 : 1);
    });
    return $grupos;
}
