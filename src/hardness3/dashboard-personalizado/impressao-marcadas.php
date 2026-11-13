<?php
namespace hardness;

function dashboardImpressaoMarcadasConfiguracao($indicador, &$erro)
{
    $erro = '';
    $lista = isset($indicador['listagem']) ? $indicador['listagem'] : array();
    $config = isset($lista['impressaoMarcadas']) ? $lista['impressaoMarcadas'] : array();
    if (!is_array($config) || !isset($config['habilitada']) || $config['habilitada'] !== true) {
        $erro = 'A impressão de marcadas não está habilitada nesta listagem.';
        return false;
    }
    foreach (array('campoChave', 'campoNome', 'campoTipo') as $campo) {
        if (dashboardIndicadorIdentificador(isset($config[$campo]) ? $config[$campo] : '', '') === '') {
            $erro = 'Campo inválido na configuração de impressão.';
            return false;
        }
    }
    $colunas = isset($config['colunas']) ? $config['colunas'] : array();
    if (!is_array($colunas) || count($colunas) < 1 || count($colunas) > 9) {
        $erro = 'Configure entre uma e nove colunas na impressão.';
        return false;
    }
    $campos = array();
    foreach ($colunas as $coluna) {
        if (!is_array($coluna)) { $erro = 'Coluna inválida na impressão.'; return false; }
        $campo = isset($coluna['campo']) ? $coluna['campo'] : '';
        if (dashboardIndicadorIdentificador($campo, '') === '' || isset($campos[$campo])
            || (isset($coluna['largura']) && (!is_numeric($coluna['largura']) || $coluna['largura'] <= 0))) {
            $erro = 'Coluna inválida na impressão.';
            return false;
        }
        $campos[$campo] = true;
    }
    if (isset($config['totais']) && !is_array($config['totais'])) { $erro = 'Totais de impressão inválidos.'; return false; }
    foreach (isset($config['totais']) ? $config['totais'] : array() as $campo) {
        if (!is_string($campo) || !isset($campos[$campo])) { $erro = 'Total de impressão sem coluna correspondente.'; return false; }
    }
    $config['sqlBase'] = dashboardPersonalizadoSqlTexto(isset($config['sqlBase']) ? $config['sqlBase'] : '');
    // The printed report always belongs to the authorized session company.
    if (!dashboardIndicadorValidarSql($config['sqlBase'], $erro)) return false;
    return $config;
}

function dashboardImpressaoMarcadasIds($entrada, &$erro)
{
    $erro = '';
    if (!is_string($entrada) || strlen($entrada) > 5500) {
        $erro = 'Seleção de impressão inválida.'; return false;
    }
    $partes = explode(',', $entrada);
    if (count($partes) > 500) { $erro = 'Selecione até 500 registros por impressão.'; return false; }
    $ids = array();
    foreach ($partes as $id) {
        if (!preg_match('/^[1-9][0-9]{0,9}$/D', $id)) {
            $erro = 'Selecione registros válidos para imprimir.'; return false;
        }
        $ids[$id] = $id;
    }
    return array_values($ids);
}

function dashboardImpressaoMarcadasConsultar($conexao, $indicador, $empresa, $entrada, &$erro)
{
    $config = dashboardImpressaoMarcadasConfiguracao($indicador, $erro);
    if ($config === false) return false;
    $ids = dashboardImpressaoMarcadasIds(isset($entrada['selecionados']) ? $entrada['selecionados'] : null, $erro);
    if ($ids === false) return false;
    $filtros = dashboardIndicadorFiltros($indicador, $entrada, $errosFiltros);
    if ($errosFiltros) { $erro = implode(' ', $errosFiltros); return false; }
    $valores = $filtros['valores'];
    if (empty($valores['dataInicio']) || empty($valores['dataFim']) || $valores['dataFim'] < $valores['dataInicio']) {
        $erro = 'Informe um período válido para a impressão.'; return false;
    }
    $parametros = array_merge($valores, array('empresa' => (int) $empresa));
    $marcadores = array();
    foreach ($ids as $n => $id) {
        $nome = 'impressaoSelecionado' . $n;
        $marcadores[] = ':' . $nome;
        $parametros[$nome] = $id;
    }
    $chave = $config['campoChave']; $nome = $config['campoNome']; $tipo = $config['campoTipo'];
    $base = dashboardPersonalizadoSqlTexto(isset($indicador['sqlBase']) ? $indicador['sqlBase'] : '');
    if (!dashboardIndicadorValidarSql($base, $erro, !empty($indicador['multiempresa']), !empty($indicador['permitirUnionAll']))) return false;
    $where = '`' . $chave . '` IN (' . implode(', ', $marcadores) . ')';
    $sql = 'SELECT `' . $chave . '` AS id, `' . $nome . '` AS nome, `' . $tipo . '` AS tipo FROM (' . $base
        . ') AS dashboard_impressao_selecao WHERE ' . $where . ' ORDER BY `' . $nome . '`';
    $selecionados = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($selecionados === false) return false;
    $registros = array();
    foreach ($selecionados as $registro) {
        if (!isset($registro['id'], $registro['nome']) || !array_key_exists('tipo', $registro)) {
            $erro = 'Resposta inválida da seleção de impressão.'; return false;
        }
        $id = (string) $registro['id'];
        if (!in_array($id, $ids, true)) { $erro = 'Resposta inválida da seleção de impressão.'; return false; }
        $registros[$id] = array('id' => $id, 'nome' => $registro['nome'], 'tipo' => $registro['tipo'], 'linhas' => array());
    }
    if (count($registros) !== count($ids)) {
        $erro = 'Um dos registros selecionados não está disponível nesta visão.'; return false;
    }
    $colunas = array('`' . $chave . '`');
    foreach ($config['colunas'] as $coluna) $colunas[] = '`' . $coluna['campo'] . '`';
    $sql = 'SELECT ' . implode(', ', array_unique($colunas)) . ' FROM (' . $config['sqlBase']
        . ') AS dashboard_impressao_linhas WHERE ' . $where . ' ORDER BY `' . $chave . '` LIMIT 10001';
    $linhas = dashboardIndicadorConsultar($conexao, $sql, $parametros, $erro);
    if ($linhas === false) return false;
    if (count($linhas) > 10000) { $erro = 'O relatório excede 10.000 parcelas. Reduza o período ou a seleção.'; return false; }
    foreach ($linhas as $linha) {
        $id = isset($linha[$chave]) ? (string) $linha[$chave] : '';
        if (!isset($registros[$id])) { $erro = 'Resposta inválida das parcelas selecionadas.'; return false; }
        foreach ($config['colunas'] as $coluna) {
            if (!array_key_exists($coluna['campo'], $linha)) { $erro = 'Parcela com campo ausente na impressão.'; return false; }
            $valor = $linha[$coluna['campo']];
            if ($valor !== null && isset($coluna['formato']) && in_array($coluna['formato'], array('moeda', 'numero', 'percentual'), true) && !is_numeric($valor)) {
                $erro = 'Parcela com valor inválido na impressão.'; return false;
            }
        }
        $registros[$id]['linhas'][] = $linha;
    }
    return array('configuracao' => $config, 'registros' => array_values($registros),
        'dataInicio' => $valores['dataInicio'], 'dataFim' => $valores['dataFim']);
}

function dashboardImpressaoMarcadasPdf($relatorio)
{
    require_once __DIR__ . '/../hardness/fpdf/fpdf.php';
    $config = $relatorio['configuracao'];
    $pdf = new class extends \FPDF {
        function __construct() { parent::FPDF('P', 'mm', array(210, 297)); }
    };
    $pdf->SetMargins(10, 10, 10); $pdf->SetAutoPageBreak(false);
    $titulo = isset($config['titulo']) ? $config['titulo'] : 'Relatório para assinatura';
    $pdf->SetTitle(dashboardExportarPdfTexto($titulo));
    $larguras = array(); $soma = 0;
    foreach ($config['colunas'] as $coluna) {
        $largura = isset($coluna['largura']) ? (float) $coluna['largura'] : 1;
        $larguras[] = $largura; $soma += $largura;
    }
    foreach ($larguras as &$largura) $largura = 190 * $largura / $soma;
    unset($largura);
    $agora = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
    $celula = function ($largura, $texto, $alinhamento = 'L', $negrito = false) use ($pdf) {
        $texto = $texto === '' ? '' : dashboardExportarPdfTexto($texto); $tamanho = 7.0;
        do { $pdf->SetFont('Arial', $negrito ? 'B' : '', $tamanho); $tamanho -= .25; }
        while ($pdf->GetStringWidth($texto) > $largura - 2 && $tamanho >= 4);
        $pdf->Cell($largura, 5.5, $texto, 0, 0, $alinhamento);
    };
    foreach ($relatorio['registros'] as $registro) {
        $pagina = function ($continuacao = false) use ($pdf, $titulo, $registro, $relatorio, $config, $agora, $larguras, $celula) {
            $pdf->AddPage(); $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'BU', 12);
            $pdf->MultiCell(190, 7, dashboardExportarPdfTexto($titulo), 0, 'C'); $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->MultiCell(190, 6, dashboardExportarPdfTexto('Representante: ' . $registro['nome']), 0, 'L');
            $tipo = (string) $registro['tipo'];
            if (isset($config['rotulosTipo'][$tipo])) $tipo = $config['rotulosTipo'][$tipo];
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(190, 5, dashboardExportarPdfTexto('Tipo: ' . $tipo), 0, 1);
            $pdf->Cell(190, 5, dashboardExportarPdfTexto('Data de impressão: ' . $agora->format('d/m/Y - H:i:s')), 0, 1);
            $periodo = dashboardExportarFormatar($relatorio['dataInicio'], 'data') . ' - ' . dashboardExportarFormatar($relatorio['dataFim'], 'data');
            $pdf->Cell(190, 5, dashboardExportarPdfTexto('Período: ' . $periodo . ($continuacao ? ' (continuação)' : '')), 0, 1);
            $pdf->Ln(2); $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
            foreach ($config['colunas'] as $n => $coluna) $celula($larguras[$n], isset($coluna['titulo']) ? $coluna['titulo'] : $coluna['campo'], 'L', true);
            $pdf->Ln(6);
        };
        $pagina(); $totais = array();
        foreach (isset($config['totais']) ? $config['totais'] : array() as $campo) $totais[$campo] = 0;
        if (!$registro['linhas']) {
            $pdf->SetFont('Arial', '', 9);
            $pdf->MultiCell(190, 6, dashboardExportarPdfTexto('Nenhuma parcela quitada no período selecionado.'), 0, 'L');
        }
        foreach ($registro['linhas'] as $linha) {
            if ($pdf->GetY() + 5.5 > 215) $pagina(true);
            foreach ($config['colunas'] as $n => $coluna) {
                $campo = $coluna['campo']; $formato = isset($coluna['formato']) ? $coluna['formato'] : 'texto';
                $valor = isset($linha[$campo]) ? $linha[$campo] : '';
                if (isset($totais[$campo])) $totais[$campo] += (float) $valor;
                $celula($larguras[$n], dashboardExportarFormatar($valor, $formato), in_array($formato, array('moeda', 'numero', 'percentual'), true) ? 'R' : 'L');
            }
            $pdf->Ln(5.5);
        }
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); $pdf->Ln(1);
        foreach ($config['colunas'] as $n => $coluna) {
            $campo = $coluna['campo'];
            $texto = $n === 0 ? 'TOTAIS:' : (isset($totais[$campo]) ? dashboardExportarFormatar($totais[$campo], isset($coluna['formato']) ? $coluna['formato'] : 'numero') : '');
            $celula($larguras[$n], $texto, $n === 0 ? 'L' : 'R', true);
        }
        $pdf->Ln(8); $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(190, 5, dashboardExportarPdfTexto('TOTAL DE REGISTROS: ' . count($registro['linhas'])), 0, 1);
        $pdf->Ln(12); $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(190, 6, dashboardExportarPdfTexto(isset($config['assinatura']) ? $config['assinatura'] : 'ASSINATURA'), 0, 1, 'C');
        $pdf->Ln(18); $y = $pdf->GetY(); $pdf->Line(50, $y, 160, $y); $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(190, 5, dashboardExportarPdfTexto($registro['nome']), 0, 'C');
    }
    return $pdf->Output('', 'S');
}
