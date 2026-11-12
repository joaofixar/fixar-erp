<?php
namespace hardness;

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../bibliotecas/confUsuario.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/access.php';
require_once __DIR__ . '/custom-indicator-engine.php';
require_once __DIR__ . '/../hardness/fpdf/fpdf.php';

function dashboardExportarErro($codigo, $mensagem)
{
    http_response_code($codigo);
    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo $mensagem;
    exit;
}

function dashboardExportarTexto($valor)
{
    $texto = trim(preg_replace('/\s+/', ' ', (string) $valor));
    return $texto === '' ? '-' : $texto;
}

function dashboardExportarNomeArquivo($titulo, $extensao)
{
    $nome = dashboardIndicadorNormalizarNome($titulo);
    return ($nome !== '' ? $nome : 'indicador') . '-' . date('Ymd-His') . '.' . $extensao;
}

function dashboardExportarFormatar($valor, $formato)
{
    if ($formato === 'moeda') return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    if ($formato === 'percentual') return number_format((float) $valor, 2, ',', '.') . '%';
    if ($formato === 'numero') return number_format((float) $valor, 2, ',', '.');
    if ($formato === 'data' && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $valor)) return implode('/', array_reverse(explode('-', (string) $valor)));
    return dashboardExportarTexto($valor);
}

function dashboardExportarFiltros($indicador)
{
    $itens = array();
    foreach ((array) (isset($indicador['filtros']) ? $indicador['filtros'] : array()) as $filtro) {
        if (!is_array($filtro) || !isset($filtro['valor']) || $filtro['valor'] === '') continue;
        $rotulo = isset($filtro['titulo']) ? $filtro['titulo'] : (isset($filtro['id']) ? $filtro['id'] : 'Filtro');
        $valor = $filtro['valor'];
        if (isset($filtro['opcoes']) && is_array($filtro['opcoes'])) {
            foreach ($filtro['opcoes'] as $opcao) {
                if (is_array($opcao) && isset($opcao['valor']) && (string) $opcao['valor'] === (string) $valor) {
                    $valor = isset($opcao['titulo']) ? $opcao['titulo'] : $valor;
                    break;
                }
            }
        }
        $itens[] = dashboardExportarTexto($rotulo) . ': ' . dashboardExportarFormatar($valor, isset($filtro['tipo']) && $filtro['tipo'] === 'data' ? 'data' : 'texto');
    }
    if (!empty($indicador['listagem']['observacaoRodape'])) $itens[] = dashboardExportarTexto($indicador['listagem']['observacaoRodape']);
    return $itens;
}

function dashboardExportarTabela($resultado)
{
    $indicador = $resultado['indicador'];
    $comparativo = isset($resultado['comparativoRepresentantes']) ? $resultado['comparativoRepresentantes'] : array();
    if (!empty($comparativo['linhas'])) {
        if (isset($comparativo['modo']) && $comparativo['modo'] === 'mensal') {
            $anoAtual = isset($comparativo['anoAtual']) ? $comparativo['anoAtual'] : date('Y');
            $meses = isset($comparativo['meses']) && is_array($comparativo['meses']) ? $comparativo['meses'] : array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
            $cabecalho = array('Representante');
            foreach ($meses as $mes) $cabecalho[] = $mes . '/' . $anoAtual;
            $cabecalho[] = 'Total ' . $anoAtual;
            $linhas = array();
            foreach ($comparativo['linhas'] as $linha) {
                $valores = array(dashboardExportarTexto(isset($linha['representante']) ? $linha['representante'] : ''));
                foreach ($meses as $mes) {
                    $valor = isset($linha['meses'][$mes]) ? $linha['meses'][$mes] : 0;
                    $valores[] = dashboardExportarFormatar($valor, 'moeda');
                }
                $valores[] = dashboardExportarFormatar(isset($linha['total']) ? $linha['total'] : 0, 'moeda');
                $linhas[] = $valores;
            }
            $total = array('Total');
            foreach ($meses as $mes) $total[] = dashboardExportarFormatar(isset($comparativo['totaisMeses'][$mes]) ? $comparativo['totaisMeses'][$mes] : 0, 'moeda');
            $total[] = dashboardExportarFormatar(isset($comparativo['totalAtual']) ? $comparativo['totalAtual'] : 0, 'moeda');
            $linhas[] = $total;
            return array($cabecalho, $linhas);
        }
        $anoBase = isset($comparativo['anoBase']) ? $comparativo['anoBase'] : 'Base';
        $anoAtual = isset($comparativo['anoAtual']) ? $comparativo['anoAtual'] : 'Atual';
        $linhas = array();
        foreach ($comparativo['linhas'] as $linha) {
            $linhas[] = array(
                dashboardExportarTexto(isset($linha['representante']) ? $linha['representante'] : ''),
                dashboardExportarFormatar(isset($linha['valorBase']) ? $linha['valorBase'] : 0, 'moeda'),
                dashboardExportarFormatar(isset($linha['valorAtual']) ? $linha['valorAtual'] : 0, 'moeda'),
                isset($linha['variacao']) && $linha['variacao'] !== null ? dashboardExportarFormatar($linha['variacao'], 'percentual') : 'Não aplicável',
            );
        }
        $linhas[] = array(
            'Total',
            dashboardExportarFormatar(isset($comparativo['totalBase']) ? $comparativo['totalBase'] : 0, 'moeda'),
            dashboardExportarFormatar(isset($comparativo['totalAtual']) ? $comparativo['totalAtual'] : 0, 'moeda'),
            isset($comparativo['variacaoTotal']) && $comparativo['variacaoTotal'] !== null ? dashboardExportarFormatar($comparativo['variacaoTotal'], 'percentual') : 'Não aplicável',
        );
        return array(array('Representante', (string) $anoBase, (string) $anoAtual, 'Variação'), $linhas);
    }

    $colunas = isset($indicador['listagem']['colunas']) && is_array($indicador['listagem']['colunas']) ? $indicador['listagem']['colunas'] : array();
    $cabecalho = array();
    foreach ($colunas as $coluna) $cabecalho[] = isset($coluna['titulo']) ? $coluna['titulo'] : (isset($coluna['campo']) ? $coluna['campo'] : '');
    $linhas = array();
    foreach ((array) (isset($resultado['listagem']) ? $resultado['listagem'] : array()) as $linha) {
        $valores = array();
        foreach ($colunas as $coluna) {
            $campo = isset($coluna['campo']) ? $coluna['campo'] : '';
            $valores[] = dashboardExportarFormatar(isset($linha[$campo]) ? $linha[$campo] : '', isset($coluna['formato']) ? $coluna['formato'] : 'texto');
        }
        $linhas[] = $valores;
    }
    if (empty($cabecalho)) {
        $cabecalho = array('Métrica', 'Valor');
        $linhas[] = array(isset($indicador['card']['titulo']) ? $indicador['card']['titulo'] : 'Resultado', dashboardExportarFormatar($resultado['card']['valor'], $resultado['card']['formato']));
        $linhas[] = array('Quantidade de registros', dashboardExportarFormatar($resultado['card']['quantidade'], 'numero'));
    }
    return array($cabecalho, $linhas);
}

function dashboardExportarPrepararDetalhamento($resultado, $grupo)
{
    $grupo = trim((string) $grupo);
    if ($grupo === '') dashboardExportarErro(422, 'Selecione um registro para exportar o detalhamento.');

    $indicador = isset($resultado['indicador']) && is_array($resultado['indicador']) ? $resultado['indicador'] : array();
    $listagem = isset($indicador['listagem']) && is_array($indicador['listagem']) ? $indicador['listagem'] : array();
    $detalhamento = isset($listagem['detalhamento']) && is_array($listagem['detalhamento']) ? $listagem['detalhamento'] : array();
    $colunas = isset($detalhamento['colunas']) && is_array($detalhamento['colunas']) ? $detalhamento['colunas'] : array();
    if (empty($detalhamento) || (isset($detalhamento['habilitado']) && !$detalhamento['habilitado']) || empty($colunas)) {
        dashboardExportarErro(422, 'O detalhamento deste indicador não está disponível para exportação.');
    }

    $campoGrupo = dashboardIndicadorIdentificador(isset($detalhamento['campoGrupo']) ? $detalhamento['campoGrupo'] : '', '');
    if ($campoGrupo === '') dashboardExportarErro(422, 'O agrupamento do detalhamento não está configurado.');

    $linhas = array();
    foreach ((array) (isset($resultado['listagemDetalhes']) ? $resultado['listagemDetalhes'] : array()) as $chave => $linhasGrupo) {
        if ((string) $chave === $grupo) {
            $linhas = is_array($linhasGrupo) ? $linhasGrupo : array();
            break;
        }
    }

    $rotuloGrupo = $grupo;
    $candidatos = !empty($linhas) ? array($linhas[0]) : array();
    foreach ((array) (isset($resultado['listagem']) ? $resultado['listagem'] : array()) as $linhaLista) {
        if (isset($linhaLista[$campoGrupo]) && (string) $linhaLista[$campoGrupo] === $grupo) {
            $candidatos[] = $linhaLista;
            break;
        }
    }
    foreach ($candidatos as $candidato) {
        foreach (array('descricao', 'representante', 'nome') as $campoRotulo) {
            if (isset($candidato[$campoRotulo]) && trim((string) $candidato[$campoRotulo]) !== '') {
                $rotuloGrupo = trim((string) $candidato[$campoRotulo]);
                break 2;
            }
        }
    }

    $titulo = isset($detalhamento['titulo']) && trim((string) $detalhamento['titulo']) !== '' ? trim((string) $detalhamento['titulo']) : 'Detalhamento do indicador';
    $indicador['titulo'] = strpos($titulo, '{grupo}') !== false ? str_replace('{grupo}', $rotuloGrupo, $titulo) : $titulo . ' — ' . $rotuloGrupo;
    $indicador['listagem']['colunas'] = $colunas;
    if (!isset($indicador['filtros']) || !is_array($indicador['filtros'])) $indicador['filtros'] = array();
    $indicador['filtros'][] = array('id' => 'detalheGrupo', 'titulo' => 'Representante', 'tipo' => 'texto', 'valor' => $rotuloGrupo);

    $resultado['indicador'] = $indicador;
    $resultado['listagem'] = $linhas;
    $resultado['listagemDetalhes'] = array();
    $resultado['comparativoRepresentantes'] = array();
    $resultado['_detalhamentoExportado'] = array('grupo' => $grupo, 'rotulo' => $rotuloGrupo, 'quantidade' => count($linhas));
    return $resultado;
}

function dashboardExportarCsv($resultado)
{
    $indicador = $resultado['indicador'];
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . dashboardExportarNomeArquivo($indicador['titulo'], 'csv') . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo "\xEF\xBB\xBF";
    $saida = fopen('php://output', 'w');
    $escreverLinha = function ($linha) use ($saida) {
        $linha = array_map(function ($valor) {
            $valor = (string) $valor;
            return preg_match('/^[=+\-@]/', $valor) ? "'" . $valor : $valor;
        }, $linha);
        fputcsv($saida, $linha, ';');
    };
    $escreverLinha(array($indicador['titulo']));
    $escreverLinha(array('Gerado em', date('d/m/Y H:i')));
    foreach (dashboardExportarFiltros($indicador) as $filtro) $escreverLinha(array($filtro));
    $escreverLinha(array());
    list($cabecalho, $linhas) = dashboardExportarTabela($resultado);
    $escreverLinha($cabecalho);
    foreach ($linhas as $linha) $escreverLinha($linha);
    fclose($saida);
    exit;
}

function dashboardExportarPdfTexto($texto)
{
    $texto = dashboardExportarTexto($texto);
    if (function_exists('iconv')) {
        $convertido = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $texto);
        if ($convertido !== false) return $convertido;
    }
    return utf8_decode($texto);
}

class DashboardIndicadorPdf extends \FPDF
{
    public $tituloRelatorio = '';
    public $subtituloRelatorio = '';
    public $filtrosRelatorio = array();

    function __construct($orientacao, $formato = 'A4')
    {
        parent::FPDF($orientacao, 'mm', $formato);
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 12);
    }

    function Header()
    {
        $this->SetTextColor(7, 34, 77);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 7, dashboardExportarPdfTexto($this->tituloRelatorio), 0, 1);
        if ($this->subtituloRelatorio !== '') {
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(87, 107, 141);
            $this->Cell(0, 5, dashboardExportarPdfTexto($this->subtituloRelatorio), 0, 1);
        }
        $this->SetFont('Arial', '', 8);
        $this->Cell(0, 5, dashboardExportarPdfTexto('Gerado em ' . date('d/m/Y H:i')), 0, 1);
        if (!empty($this->filtrosRelatorio)) {
            $this->SetTextColor(87, 107, 141);
            $this->MultiCell(0, 4, dashboardExportarPdfTexto('Filtros: ' . implode(' | ', $this->filtrosRelatorio)), 0, 'L');
        }
        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-9);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(87, 107, 141);
        $this->Cell(0, 4, 'Hardness - Painel de Indicadores', 0, 0, 'L');
        $this->Cell(0, 4, 'Pagina ' . $this->PageNo(), 0, 0, 'R');
    }
}

function dashboardExportarPdfCelula($pdf, $largura, $altura, $texto, $borda, $alinhamento, $negrito, $preenchida)
{
    $pdf->SetFont('Arial', $negrito ? 'B' : '', $negrito ? 8 : 7.5);
    $texto = dashboardExportarPdfTexto($texto);
    $textoOriginal = $texto;
    while ($texto !== '' && $pdf->GetStringWidth($texto . '...') > $largura - 2) $texto = substr($texto, 0, -1);
    if ($texto !== '' && $texto !== $textoOriginal) $texto .= '...';
    $pdf->Cell($largura, $altura, $texto, $borda, 0, $alinhamento, $preenchida);
}

function dashboardExportarPdfLarguras($pdf, $cabecalho, $linhas)
{
    $larguras = array();
    foreach ($cabecalho as $indice => $titulo) {
        $pdf->SetFont('Arial', 'B', 8);
        $maior = $pdf->GetStringWidth(dashboardExportarPdfTexto($titulo));
        $pdf->SetFont('Arial', '', 7.5);
        foreach ($linhas as $linha) {
            $texto = isset($linha[$indice]) ? dashboardExportarPdfTexto($linha[$indice]) : '';
            $maior = max($maior, $pdf->GetStringWidth($texto));
        }
        $minima = $indice === 0 ? 45 : 25;
        $maxima = $indice === 0 ? 105 : 60;
        $larguras[$indice] = min($maxima, max($minima, $maior + 4));
    }
    return $larguras;
}

function dashboardExportarPdfGruposColunas($larguras, $larguraDisponivel)
{
    if (count($larguras) < 2) return array(array(0));
    $larguraFixa = min($larguras[0], $larguraDisponivel - 25);
    $grupos = array();
    $grupo = array(0);
    $usada = $larguraFixa;
    foreach ($larguras as $indice => $largura) {
        if ($indice === 0) continue;
        $largura = min($largura, $larguraDisponivel - $larguraFixa);
        if (count($grupo) > 1 && $usada + $largura > $larguraDisponivel) {
            $grupos[] = $grupo;
            $grupo = array(0);
            $usada = $larguraFixa;
        }
        $grupo[] = $indice;
        $usada += $largura;
    }
    $grupos[] = $grupo;
    return $grupos;
}

function dashboardExportarPdfAjustarLarguras($larguras, $larguraDisponivel)
{
    $larguraTotal = array_sum($larguras);
    if ($larguraTotal <= 0 || $larguraTotal <= $larguraDisponivel) return $larguras;

    $fator = ($larguraDisponivel - 0.5) / $larguraTotal;
    foreach ($larguras as $indice => $largura) $larguras[$indice] = $largura * $fator;
    return $larguras;
}

function dashboardExportarPdfImprimirTabela($pdf, $cabecalho, $linhas, $larguras, $grupo, $numeroGrupo, $totalGrupos)
{
    $imprimirCabecalho = function () use ($pdf, $cabecalho, $larguras, $grupo, $numeroGrupo, $totalGrupos) {
        if ($totalGrupos > 1) {
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetTextColor(87, 107, 141);
            $pdf->Cell(0, 4, 'Colunas ' . $numeroGrupo . ' de ' . $totalGrupos, 0, 1, 'R');
        }
        $pdf->SetFillColor(232, 240, 255);
        $pdf->SetTextColor(7, 34, 77);
        foreach ($grupo as $indice) dashboardExportarPdfCelula($pdf, $larguras[$indice], 7, $cabecalho[$indice], 1, $indice === 0 ? 'L' : 'R', true, true);
        $pdf->Ln();
    };
    $imprimirCabecalho();
    foreach ($linhas as $indiceLinha => $linha) {
        if ($pdf->GetY() + 7 > $pdf->h - $pdf->bMargin) {
            $pdf->AddPage();
            $imprimirCabecalho();
        }
        $pdf->SetFillColor($indiceLinha % 2 ? 248 : 255, $indiceLinha % 2 ? 251 : 255, 255);
        $pdf->SetTextColor(7, 34, 77);
        foreach ($grupo as $indice) dashboardExportarPdfCelula($pdf, $larguras[$indice], 7, isset($linha[$indice]) ? $linha[$indice] : '', 1, $indice === 0 ? 'L' : 'R', false, $indiceLinha % 2 === 1);
        $pdf->Ln();
    }
}

function dashboardExportarPdf($resultado)
{
    $indicador = $resultado['indicador'];
    list($cabecalho, $linhas) = dashboardExportarTabela($resultado);
    $detalhamentoExportado = !empty($resultado['_detalhamentoExportado']);
    $orientacao = count($cabecalho) > 2 ? 'L' : 'P';
    $pdf = new DashboardIndicadorPdf($orientacao, $detalhamentoExportado ? 'A3' : 'A4');
    $pdf->tituloRelatorio = isset($indicador['titulo']) ? $indicador['titulo'] : 'Indicador';
    $pdf->subtituloRelatorio = isset($indicador['subtitulo']) ? $indicador['subtitulo'] : '';
    $pdf->filtrosRelatorio = dashboardExportarFiltros($indicador);
    $pdf->SetTitle(dashboardExportarPdfTexto($pdf->tituloRelatorio));
    $pdf->AddPage();
    $pdf->SetTextColor(7, 34, 77);
    $pdf->SetFont('Arial', 'B', 10);
    if ($detalhamentoExportado) {
        $quantidadeNotas = isset($resultado['_detalhamentoExportado']['quantidade']) ? (int) $resultado['_detalhamentoExportado']['quantidade'] : count($linhas);
        $pdf->Cell(0, 6, dashboardExportarPdfTexto('Quantidade de NF-e: ' . number_format($quantidadeNotas, 0, ',', '.')), 0, 1);
    } else {
        $pdf->Cell(0, 6, dashboardExportarPdfTexto((isset($indicador['card']['titulo']) ? $indicador['card']['titulo'] : 'Resultado') . ': ' . dashboardExportarFormatar($resultado['card']['valor'], $resultado['card']['formato'])), 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 5, dashboardExportarPdfTexto('Quantidade de registros: ' . dashboardExportarFormatar($resultado['card']['quantidade'], 'numero')), 0, 1);
    }
    $pdf->Ln(3);

    $larguraDisponivel = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $larguras = dashboardExportarPdfLarguras($pdf, $cabecalho, $linhas);
    if ($detalhamentoExportado) {
        $larguras = dashboardExportarPdfAjustarLarguras($larguras, $larguraDisponivel);
        $grupos = array(array_keys($larguras));
    } else {
        $grupos = dashboardExportarPdfGruposColunas($larguras, $larguraDisponivel);
    }
    foreach ($grupos as $indiceGrupo => $grupo) {
        if ($indiceGrupo > 0) $pdf->AddPage();
        dashboardExportarPdfImprimirTabela($pdf, $cabecalho, $linhas, $larguras, $grupo, $indiceGrupo + 1, count($grupos));
    }
    $pdf->Output(dashboardExportarNomeArquivo($pdf->tituloRelatorio, 'pdf'), 'D');
    exit;
}

$autorizacao = dashboardPersonalizadoLerAutorizacao($confUsuario);
if ($autorizacao === false) dashboardExportarErro(401, 'Autorização do dashboard não encontrada ou expirada.');
$empresa = $autorizacao['empresa'];
$errosConfiguracao = array();
$configuracao = dashboardPersonalizadoConfiguracao($empresa, $errosConfiguracao);
if ($configuracao === false) dashboardExportarErro(403, implode(' ', $errosConfiguracao));

$idsIndicadores = array();
foreach ($configuracao['indicadores'] as $indicadorConfigurado) $idsIndicadores[$indicadorConfigurado['id']] = true;
$acessos = dashboardPersonalizadoLerAcessos($empresa, $idsIndicadores);
$indicadoresPermitidos = dashboardPersonalizadoIndicadoresPermitidos($autorizacao, $configuracao, $acessos);
$indicadorId = isset($_GET['id']) ? (string) $_GET['id'] : '';
$indicadorSelecionado = null;
foreach ($indicadoresPermitidos as $indicador) {
    if (isset($indicador['id']) && $indicador['id'] === $indicadorId) {
        $indicadorSelecionado = $indicador;
        break;
    }
}
if ($indicadorSelecionado === null) dashboardExportarErro(404, 'Indicador não encontrado na personalização da empresa.');
$restricaoUsuario = dashboardPersonalizadoRestricaoUsuarioIndicador($autorizacao, $acessos, $indicadorId);
$entradaIndicador = $_GET;
if (!empty($indicadorSelecionado['abas']) && is_array($indicadorSelecionado['abas'])) {
    $abaId = isset($_GET['tab']) ? (string) $_GET['tab'] : '';
    if ($restricaoUsuario !== false) {
        $abaPermitida = dashboardPersonalizadoAbaVendedores($indicadorSelecionado);
        if ($abaPermitida === '' || ($abaId !== '' && $abaId !== $abaPermitida)) {
            dashboardExportarErro(403, 'Esta aba não está liberada para o seu usuário.');
        }
        $abaId = $abaPermitida;
        $entradaIndicador['tab'] = $abaId;
    }
    $abaEncontrada = false;
    foreach ($indicadorSelecionado['abas'] as $aba) {
        if ($abaId === '' || (isset($aba['id']) && $aba['id'] === $abaId)) {
            $indicadorSelecionado = array_merge($indicadorSelecionado, $aba);
            $abaEncontrada = true;
            break;
        }
    }
    if (!$abaEncontrada) dashboardExportarErro(404, 'Aba do indicador não encontrada.');
}
$indicadorSelecionado = dashboardPersonalizadoAplicarRestricaoUsuario($indicadorSelecionado, $restricaoUsuario, $entradaIndicador, $erroRestricao);
if ($indicadorSelecionado === false) dashboardExportarErro(403, $erroRestricao);

$conexao = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'], $confUsuario['dbDatabase']);
if (!$conexao) dashboardExportarErro(503, 'Não foi possível consultar os dados do dashboard.');
mysqli_set_charset($conexao, 'utf8');
@mysqli_query($conexao, 'SET SESSION MAX_EXECUTION_TIME=5000');
@mysqli_query($conexao, 'START TRANSACTION READ ONLY');
$erro = '';
$resultado = dashboardIndicadorExecutar($conexao, $indicadorSelecionado, $empresa, $entradaIndicador, $erro);
@mysqli_rollback($conexao);
mysqli_close($conexao);
if ($resultado === false) dashboardExportarErro(422, $erro);

$escopo = isset($_GET['escopo']) ? strtolower(trim((string) $_GET['escopo'])) : '';
if ($escopo !== '' && $escopo !== 'detalhamento') dashboardExportarErro(400, 'Escopo de exportação inválido.');
if ($escopo === 'detalhamento') {
    $resultado = dashboardExportarPrepararDetalhamento($resultado, isset($_GET['detalheGrupo']) ? $_GET['detalheGrupo'] : '');
}

$formato = isset($_GET['formato']) ? strtolower((string) $_GET['formato']) : '';
if ($formato === 'csv') dashboardExportarCsv($resultado);
if ($formato === 'pdf') dashboardExportarPdf($resultado);
dashboardExportarErro(404, 'Formato de exportação não disponível.');
