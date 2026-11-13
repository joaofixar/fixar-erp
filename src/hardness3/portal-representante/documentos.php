<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }

require_once __DIR__ . '/dados.php';

function portalRepresentanteDocumentoDisponibilidade($conexao, $conta, $id)
{
    if (empty($conta['painel']['impressoes']['disponibilidadePedido']))
        throw new RepresentantePortalErro('Impressão de disponibilidade não configurada pela empresa.', 404);
    $base = portalRepresentanteBaseConfigurada('pedidos', $conta, portalRepresentanteFiltros(array()), null, array('id'));
    $params = $base['params']; $params['documento'] = (int) $id;
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT d.T005_Id AS id FROM T005 d
        INNER JOIN (' . $base['sql'] . ') portal ON portal.id=d.T005_Id
        WHERE d.T005_Id=:documento AND d.T005_C004_Id=:empresa AND d.T005_D021_Id=:representante LIMIT 1', $params);
    if (!$linhas) throw new RepresentantePortalErro('Impressão não disponível para este pedido.', 404);
    return $linhas[0];
}

function portalRepresentanteDocumentoOrcamento($conexao, $conta, $id)
{
    $base = portalRepresentanteBaseConfigurada('orcamentos', $conta, portalRepresentanteFiltros(array()), null, array('id'));
    $params = $base['params']; $params['documento'] = (int) $id;
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT d.T003_Id AS id FROM T003 d
        INNER JOIN (' . $base['sql'] . ') portal ON portal.id=d.T003_Id
        WHERE d.T003_Id=:documento AND d.T003_C004_Id=:empresa AND d.T003_D021_Id=:representante LIMIT 1', $params);
    if (!$linhas) throw new RepresentantePortalErro('Impressão não disponível para este orçamento.', 404);
    return $linhas[0];
}

// As fontes personalizadas delimitam o que pode ser consultado. A impressão usa
// os identificadores reais do ERP, sem aceitar URLs ou caminhos do navegador.
function portalRepresentanteDocumentoNota($conexao, $conta, $id)
{
    $base = portalRepresentanteBaseConfigurada('notas', $conta, portalRepresentanteFiltros(array()), null, array('id','excluido'));
    $params = $base['params']; $params['documento'] = (int) $id;
    $sql = 'SELECT n.T007_Id AS id,n.T007_D024_Id AS cliente,n.T007_Numero_Protocolo_Nfe AS protocolo
        FROM T007 n INNER JOIN (' . $base['sql'] . ') portal ON portal.id=n.T007_Id AND portal.excluido=0
        WHERE n.T007_Id=:documento AND n.T007_C004_Id=:empresa AND n.T007_D021_Id=:representante AND (' . portalRepresentanteNotaValida('n') . ') LIMIT 1';
    $linhas = portalRepresentanteConsultar($conexao, $sql, $params);
    if (!$linhas) throw new RepresentantePortalErro('DANFE não disponível para esta nota.', 404);
    return $linhas[0];
}

function portalRepresentanteDocumentoBoletosSql($conta)
{
    $base = portalRepresentanteBaseConfigurada('cobrancas', $conta, portalRepresentanteFiltros(array()), null, array('id','notaId','excluido'));
    // Um boleto pode agrupar títulos. Nenhum deles pode revelar documentos de
    // outra empresa ou representante, mesmo que um título pertença à seleção.
    $base['sql'] = ' FROM T002 t
        INNER JOIN (' . $base['sql'] . ') portal ON portal.id=t.T002_Id AND portal.notaId=t.T002_T007_Id AND portal.excluido=0
        INNER JOIN T007 n ON n.T007_Id=t.T002_T007_Id AND n.T007_C004_Id=t.T002_C004_Id
        INNER JOIN T117 b ON b.T117_Id=t.T002_T117_Id AND b.T117_C004_Id=t.T002_C004_Id
        INNER JOIN D007 conta ON conta.D007_Id=b.T117_D007_Id
        INNER JOIN D062 banco ON banco.D062_Id=conta.D007_D062_Id
        WHERE t.T002_C004_Id=:empresa AND n.T007_D021_Id=:representante
        AND (' . portalRepresentanteNotaValida('n') . ')
        AND COALESCE(t.T002_Flag_Cancelada,\'N\')<>\'S\' AND COALESCE(t.T002_T002_Id_Agrupado,0)=0
        AND COALESCE(b.T117_Flag_Cancelado,\'N\')<>\'S\'
        AND COALESCE(b.T117_Data_Pagamento,\'0000-00-00\') IN (\'\',\'0000-00-00\')
        AND GREATEST(COALESCE(t.T002_Valor_Saldo,t.T002_Valor_Total-COALESCE(t.T002_Valor_Recebido,0)),0)>0
        AND COALESCE(conta.D007_Flag_Ativo,\'S\')=\'S\' AND COALESCE(banco.D062_Numero_Banco,\'\')<>\'\'
        AND NOT EXISTS (SELECT 1 FROM T002 outro LEFT JOIN T007 outraNota ON outraNota.T007_Id=outro.T002_T007_Id
            AND outraNota.T007_C004_Id=outro.T002_C004_Id
            WHERE outro.T002_T117_Id=b.T117_Id AND (outro.T002_C004_Id<>:empresa
                OR outraNota.T007_Id IS NULL OR COALESCE(outraNota.T007_D021_Id,0)<>:representante))';
    return $base;
}

function portalRepresentanteDocumentoBoleto($conexao, $conta, $id)
{
    $base = portalRepresentanteDocumentoBoletosSql($conta);
    $base['params']['documento'] = (int) $id;
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT b.T117_Id AS id,t.T002_T007_Id AS nota' . $base['sql'] . ' AND t.T002_Id=:documento LIMIT 1', $base['params']);
    if (!$linhas) throw new RepresentantePortalErro('Boleto não disponível para esta cobrança.', 404);
    return $linhas[0];
}

function portalRepresentanteDocumentosMarcar($conexao, $conta, $tipo, $linhas)
{
    if (!$linhas || !in_array($tipo, array('notas','boletos','cobrancas'), true)) return $linhas;
    $config = isset($conta['painel']) ? $conta['painel'] : portalRepresentanteLerConfiguracao($conta['empresa']);
    if ($tipo === 'boletos' && portalRepresentanteComissaoPorNota($config)) {
        foreach ($linhas as &$linha) $linha['boletoDisponivel'] = false;
        unset($linha);
        return $linhas;
    }
    $base = portalRepresentanteDocumentoBoletosSql($conta);
    $chave = $tipo === 'notas' ? 't.T002_T007_Id' : 't.T002_Id'; $ids = array();
    foreach ($linhas as $i => $linha) { $nome = 'documento' . $i; $ids[] = ':' . $nome; $base['params'][$nome] = (int) $linha['id']; }
    $disponiveis = portalRepresentanteConsultar($conexao, 'SELECT DISTINCT ' . $chave . ' AS id' . $base['sql'] . ' AND ' . $chave . ' IN (' . implode(',', $ids) . ') LIMIT 101', $base['params']);
    $disponiveis = array_fill_keys(array_column($disponiveis, 'id'), true);
    foreach ($linhas as &$linha) $linha['boletoDisponivel'] = isset($disponiveis[$linha['id']]);
    unset($linha);
    return $linhas;
}

function portalRepresentanteDocumentoUrl($conf, $caminho, $params, $rotaErp = false)
{
    $raiz = isset($conf['urlRaiz']) ? rtrim($conf['urlRaiz'], '/') : '';
    if (!filter_var($raiz, FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($raiz, PHP_URL_SCHEME)), array('http','https'), true)
        || parse_url($raiz, PHP_URL_USER) !== null || parse_url($raiz, PHP_URL_QUERY) !== null || parse_url($raiz, PHP_URL_FRAGMENT) !== null)
        throw new RepresentantePortalErro('Endereço de impressão não configurado. Contate a empresa.', 503);
    return $raiz . ($rotaErp ? '/' : '/hardness3/hardness/') . $caminho . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

function portalRepresentanteDocumentoPdfValido($pdf)
{
    return is_string($pdf) && strlen($pdf) >= 8 && strlen($pdf) <= 33554432 && substr($pdf, 0, 5) === '%PDF-';
}

function portalRepresentanteDocumentoDanfe($conf, $nota)
{
    // Aproveita a DANFE já gerada no envio da NF, inclusive o layout da empresa.
    $raiz = rtrim(isset($conf['pathRaiz']) ? $conf['pathRaiz'] : dirname(__DIR__), '/\\');
    if (isset($conf['dbDatabase']) && preg_match('/^[a-zA-Z0-9_-]+$/D', $conf['dbDatabase']) && preg_match('/^[0-9]{1,20}$/D', (string) $nota['protocolo'])) {
        foreach (array_unique(array($raiz, str_replace('hardness3', 'hardness', $raiz))) as $projeto) {
            $dados = realpath($projeto . '/dados_usuarios/' . $conf['dbDatabase']);
            $arquivo = $dados ? realpath($dados . '/nfe/danfe/danfe_' . $nota['protocolo'] . '.pdf') : false;
            if ($arquivo && strpos($arquivo, $dados . DIRECTORY_SEPARATOR) === 0 && is_readable($arquivo) && filesize($arquivo) <= 33554432) {
                $pdf = file_get_contents($arquivo);
                if (portalRepresentanteDocumentoPdfValido($pdf)) return $pdf;
            }
        }
    }
    if (!function_exists('curl_init')) throw new RepresentantePortalErro('Impressão da DANFE indisponível. Contate a empresa.', 503);
    // O id já passou pela autorização do portal. A impressão do ERP aceita a
    // chave direta, evitando varrer T007 pelo MD5 de cliente e documento.
    $url = portalRepresentanteDocumentoUrl($conf, 'danfe/imprimir_danfe.php', array('T007_Id' => (int) $nota['id']));
    $limite = microtime(true) + 55;
    for ($tentativa = 0; $tentativa < 2; $tentativa++) {
        $restante = (int) ceil($limite - microtime(true));
        if ($restante < 1) break;
        $curl = curl_init($url); $pdf = '';
        try {
            curl_setopt_array($curl, array(CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => min(5, $restante), CURLOPT_TIMEOUT => $restante,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_WRITEFUNCTION => function ($c, $parte) use (&$pdf) { if (strlen($pdf) + strlen($parte) > 33554432) return 0; $pdf .= $parte; return strlen($parte); }));
            $ok = curl_exec($curl); $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($ok && $status === 200 && portalRepresentanteDocumentoPdfValido($pdf)) return $pdf;
            // Repete somente falhas transitórias, dentro do mesmo limite total.
            if ($ok && !in_array($status, array(408,429,502,503,504), true)) break;
        } finally { curl_close($curl); }
    }
    throw new RepresentantePortalErro('Não foi possível preparar a DANFE agora. Tente novamente.', 503);
}

function portalRepresentanteDocumentoBoletoPdf($conf, $conta, $boleto)
{
    if (!function_exists('shell_exec')) throw new RepresentantePortalErro('Impressão de boleto indisponível. Contate a empresa.', 503);
    // Mesma impressão bancária usada pelo FIN005. Os parâmetros são exclusivamente
    // da sessão e da consulta autorizada, nunca de uma URL recebida do navegador.
    $url = portalRepresentanteDocumentoUrl($conf, 'boleto/gerar_boleto.php', array('T117_Id' => (int) $boleto['id'],
        'noEmp' => 'true', 'p' => (int) $conta['empresa'], 'u' => 0, 'db' => $conf['dbDatabase']));
    return portalRepresentanteDocumentoHtmlPdf($url, 'boleto');
}

function portalRepresentanteDocumentoOrcamentoPdf($conf, $conta, $orcamento)
{
    // Usa o mesmo HTML e a personalização de impressão do VEN001, em reais.
    $url = portalRepresentanteDocumentoUrl($conf, 'ven/ven001/form_func-ajax/ven001ImprimirOrcamento/', array(
        'ajax' => 'true', 'gerarHtml' => 1, 'T003_Id' => (int) $orcamento['id'], 'moeda' => 1,
        'noEmp' => 'true', 'p' => (int) $conta['empresa'], 'u' => 0, 'db' => $conf['dbDatabase']), true);
    return portalRepresentanteDocumentoHtmlPdf($url, 'orcamento');
}

function portalRepresentanteDocumentoDisponibilidadePdf($conf, $conta, $pedido)
{
    $tela = isset($conta['painel']['impressoes']['disponibilidadePedido']) ? $conta['painel']['impressoes']['disponibilidadePedido'] : '';
    if (!is_string($tela) || !preg_match('/^[a-f0-9]{32}$/D', $tela))
        throw new RepresentantePortalErro('Impressão de disponibilidade não configurada pela empresa.', 404);
    if (!function_exists('curl_init')) throw new RepresentantePortalErro('Impressão indisponível. Contate a empresa.', 503);
    // O gerador original preserva dimensões, JavaScript e código de barras da
    // tela personalizada. O navegador recebe apenas o PDF do pedido autorizado.
    $link = '/sistema/personalizacao/tela/' . $tela . '/?ajax=true&T005_Id=' . (int) $pedido['id'];
    $url = portalRepresentanteDocumentoUrl($conf, 'sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/', array(
        'ajax' => 'true', 'acaoId' => $tela, 'link' => $link, 'inline' => 'true',
        'noEmp' => 'true', 'p' => (int) $conta['empresa'], 'u' => 0, 'db' => $conf['dbDatabase']), true);
    $curl = curl_init($url); $pdf = '';
    try {
        curl_setopt_array($curl, array(CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 65,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_WRITEFUNCTION => function ($c, $parte) use (&$pdf) { if (strlen($pdf) + strlen($parte) > 33554432) return 0; $pdf .= $parte; return strlen($parte); }));
        $ok = curl_exec($curl); $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($ok && $status === 200 && portalRepresentanteDocumentoPdfValido($pdf)) return $pdf;
    } finally { curl_close($curl); }
    throw new RepresentantePortalErro('Não foi possível preparar a impressão de disponibilidade agora. Tente novamente.', 503);
}

function portalRepresentanteDocumentoHtmlPdf($url, $tipo)
{
    if (!function_exists('shell_exec')) throw new RepresentantePortalErro('Impressão indisponível. Contate a empresa.', 503);
    $arquivo = tempnam(sys_get_temp_dir(), 'portal-pdf-');
    if (!$arquivo) throw new RepresentantePortalErro('Impressão indisponível.', 503);
    chmod($arquivo, 0600);
    try {
        $comando = 'timeout 60s xvfb-run --auto-servernum -s ' . escapeshellarg('-screen 0 640x480x16')
            . ' wkhtmltopdf --quiet --disable-javascript --disable-local-file-access '
            . ($tipo === 'orcamento' ? '--minimum-font-size 13 --margin-left 1 --margin-right 1 --margin-top 1 --margin-bottom 1 ' : '--lowquality --margin-bottom 0 ')
            . escapeshellarg($url) . ' ' . escapeshellarg($arquivo) . ' 2>/dev/null';
        shell_exec($comando);
        clearstatcache(true, $arquivo);
        $pdf = filesize($arquivo) <= 33554432 ? file_get_contents($arquivo) : '';
        if (!portalRepresentanteDocumentoPdfValido($pdf)) throw new RepresentantePortalErro('Não foi possível imprimir o documento. Contate a empresa para verificar a impressão.', 503);
        return $pdf;
    } finally { @unlink($arquivo); }
}

function portalRepresentanteDocumentoBoletosNota($conexao, $conta, $id)
{
    $base = portalRepresentanteDocumentoBoletosSql($conta);
    $base['params']['documento'] = (int) $id;
    $linhas = portalRepresentanteConsultar($conexao, 'SELECT DISTINCT b.T117_Id AS id,t.T002_T007_Id AS nota'
        . $base['sql'] . ' AND t.T002_T007_Id=:documento ORDER BY b.T117_Id LIMIT 101', $base['params']);
    if (!$linhas) throw new RepresentantePortalErro('Nenhum boleto disponível para esta nota.', 404);
    if (count($linhas) > 100) throw new RepresentantePortalErro('Mais de 100 boletos nesta nota. Solicite o detalhamento à empresa.');
    return $linhas;
}

function portalRepresentanteDocumentoBoletosZip($conf, $conta, $boletos)
{
    if (!class_exists('ZipArchive')) throw new RepresentantePortalErro('Download de boletos em conjunto indisponível. Contate a empresa.', 503);
    $arquivo = tempnam(sys_get_temp_dir(), 'portal-boletos-');
    if (!$arquivo) throw new RepresentantePortalErro('Não foi possível preparar os boletos.', 503);
    chmod($arquivo, 0600); $zip = new \ZipArchive(); $aberto = false;
    try {
        if ($zip->open($arquivo, \ZipArchive::OVERWRITE) !== true) throw new RepresentantePortalErro('Não foi possível preparar os boletos.', 503);
        $aberto = true; $total = 0;
        foreach ($boletos as $boleto) {
            set_time_limit(90);
            $pdf = portalRepresentanteDocumentoBoletoPdf($conf, $conta, $boleto);
            $total += strlen($pdf);
            if ($total > 67108864 || !$zip->addFromString('boleto-' . (int) $boleto['id'] . '.pdf', $pdf))
                throw new RepresentantePortalErro('Não foi possível reunir todos os boletos. Baixe as parcelas individualmente.', 503);
        }
        if (!$zip->close()) throw new RepresentantePortalErro('Não foi possível finalizar os boletos.', 503);
        $aberto = false;
        return file_get_contents($arquivo);
    } finally { if ($aberto) $zip->close(); @unlink($arquivo); }
}

function portalRepresentanteDocumentoResponder($tipo, $id, $pdf)
{
    $zip = $tipo === 'boletosNota';
    header('Content-Type: ' . ($zip ? 'application/zip' : 'application/pdf'));
    header('Content-Disposition: ' . (in_array($tipo, array('danfe','orcamento','disponibilidade'), true) ? 'inline' : 'attachment') . '; filename="' . $tipo . '-' . (int) $id . ($zip ? '.zip' : '.pdf') . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}
