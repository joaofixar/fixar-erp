<?php
/** Testes isolados: somente XML fictício, sem bootstrap nem acesso ao banco. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL & ~E_DEPRECATED);
require_once __DIR__ . '/../../hardness/class/DanfeModelo.php';
require_once __DIR__ . '/../../hardness/class/danfe_rt400.php';
set_error_handler(function ($nivel, $mensagem, $arquivo, $linha) {
    if (error_reporting() & $nivel) { throw new \RuntimeException("$mensagem em $arquivo:$linha"); }
});
$checks = 0;
function verificar($ok, $mensagem) {
    global $checks;
    $checks++;
    if (!$ok) { throw new \RuntimeException($mensagem); }
}
$nota = array('T007_Flag_ACP' => '6', 'T007_Versao_NFe' => '4.00');
verificar(array_column(DanfeModelo::opcoes(), 'value') === array('V', 'H'), 'Seletor da nota deve conter somente as duas orientações RTC');
verificar(array_column(DanfeModelo::opcoes(), 'title') === array('Retrato', 'Paisagem'), 'Rótulos das orientações RTC incorretos');
verificar(array_column(DanfeModelo::opcoes('H'), 'selected') === array(false, true), 'Horizontal não aparece selecionada');
verificar(DanfeModelo::resolver($nota + array('C004_Flag_Usar_DANFE_RTC' => 'N', 'C004_Tipo_DANFE' => 'H')) === 'A', 'Empresa no modelo atual não deve usar a orientação RTC');
verificar(array_column(DanfeModelo::opcoes(DanfeModelo::resolver($nota + array('C004_Flag_Usar_DANFE_RTC' => 'S', 'C004_Tipo_DANFE' => 'H'))), 'selected') === array(false, true), 'Empresa RTC não mostra a orientação configurada');
foreach (array('N', 'S') as $usarRTC) {
    foreach (array('', 'A', 'V', 'H', 'invalido') as $empresa) {
        foreach (array('', 'A', 'V', 'H', 'invalido') as $individual) {
            $esperado = $individual === 'A'
                ? 'A'
                : ($usarRTC === 'S' && in_array($individual, array('V', 'H'))
                    ? $individual
                    : ($usarRTC === 'S' && in_array($empresa, array('V', 'H')) ? $empresa : 'A'));
            verificar(DanfeModelo::resolver($nota + array('C004_Flag_Usar_DANFE_RTC' => $usarRTC, 'C004_Tipo_DANFE' => $empresa, 'T007A_Tipo_DANFE' => $individual)) === $esperado, 'Precedência empresa/nota incorreta');
        }
    }
}
verificar(DanfeModelo::resolver(array('T007_Flag_ACP' => '8', 'T007_Versao_NFe' => '4.00', 'C004_Tipo_DANFE' => 'H')) === 'A', 'NFC-e alterada');
verificar(DanfeModelo::resolver(array('T007_Flag_ACP' => '6', 'T007_Versao_NFe' => '3.10', 'C004_Tipo_DANFE' => 'H')) === 'A', 'NF-e 3.10 alterada');
$xml = file_get_contents(__DIR__ . '/fixtures/danfe-rtc.xml');
$danfe = new danfe_rt400();
$danfe->xml = $xml;
$dados = $danfe->lerXML();
verificar($dados['total'] === '522,97', 'Total vNFTot ignorado');
verificar($dados['itens'][0]['bases']['IBS/CBS'] === '176,00', 'Base confundida com outro grupo fiscal');
verificar($dados['itens'][0]['aliquotas']['CBS'] === '0,3600%', 'CBS efetiva não aplicada');
verificar($dados['itens'][0]['aliquotas']['IBS UF'] === '0,0400%', 'IBS UF efetiva não aplicada');
verificar($dados['itens'][0]['aliquotas']['IBS MUN'] === '0,0000%', 'Alíquota efetiva zero ignorada');
verificar($dados['itens'][0]['aliquotas']['IS'] === '2,0000%', 'IS deve usar pIS');
verificar($dados['itens'][0]['tributos']['CBS'] === '0,63', 'Valor CBS de grupo incorreto');
verificar($dados['itens'][1]['aliquotas']['CBS'] === '0,9000%', 'CBS nominal incorreta');
verificar($dados['itens'][1]['aliquotas']['IBS UF'] === '0,1000%', 'IBS nominal incorreto');
verificar($dados['itens'][1]['bases']['IS'] === '' && $dados['itens'][1]['tributos']['IS'] === '', 'IS ausente foi inventado');
verificar(strpos($dados['itens'][1]['descricao'], 'cClassTrib: 000001') !== false, 'Zeros da classificação perdidos');
verificar($dados['itens'][0]['cst'] === '000' && $dados['itens'][1]['cst'] === '0102', 'CST/CSOSN incorretos');
verificar($dados['itens'][0]['total'] === '214,70' && $dados['itens'][1]['total'] === '300,12', 'Total do item incorreto');
$danfe->xml = preg_replace('/<vNFTot>.*?<\/vNFTot>/', '', $xml);
verificar($danfe->lerXML()['total'] === '515,12', 'Fallback vNF incorreto');
$danfe->xml = str_replace('<pAliqEfet>0.3600</pAliqEfet>', '', $xml);
verificar($danfe->lerXML()['itens'][0]['aliquotas']['CBS'] === '', 'gRed sem pAliqEfet não deve inventar valor');
foreach (array('<invalido>', str_replace('<mod>55</mod>', '<mod>65</mod>', $xml), '<!DOCTYPE NFe SYSTEM "file:///etc/passwd">' . $xml) as $invalido) {
    $danfe->xml = $invalido;
    try { $danfe->lerXML(); verificar(false, 'XML inválido aceito'); } catch (\InvalidArgumentException $e) { verificar(true, 'XML rejeitado'); }
}
// Stubs exclusivos do processo de testes, não abrem conexão.
$queries = array(); $notaExiste = true; $falhar = false;
function mysql_query($sql) { global $queries, $falhar; $queries[] = $sql; return !$falhar; }
function mysql_fetch_assoc($sql) { global $notaExiste; return $notaExiste ? array('T007_Id' => 123) : false; }
verificar(DanfeModelo::salvarPorNota(123, 1, 'H') === true, 'Preferência não gravada');
verificar(count($queries) === 2 && strpos($queries[1], 'INSERT INTO T007A') === 0, 'Gravação não isolada em T007A');
verificar(strpos($queries[0], "T007_C004_Id = '1'") !== false, 'Consulta sem filtro de empresa');
verificar(strpos($queries[1], 'ON DUPLICATE KEY UPDATE T007A_Tipo_DANFE') !== false, 'Registro auxiliar ausente não tratado');
$queries = array(); $notaExiste = false;
verificar(DanfeModelo::salvarPorNota(123, 2, 'V') !== true && count($queries) === 1, 'Nota de outra empresa gravada');
$queries = array();
verificar(DanfeModelo::salvarPorNota(123, 1, "' OR 1=1") !== true && !$queries, 'Tipo inválido aceito');
$notaExiste = true; $falhar = true;
verificar(DanfeModelo::salvarPorNota(123, 1, 'H') !== true, 'Falha de persistência ocultada');
$falhar = false;
$queries = array();
verificar(DanfeModelo::salvarPorNota(123, 1, '') !== true && !$queries, 'Opção vazia não deve ser gravada pelo seletor');
$queries = array();
verificar(DanfeModelo::salvarPorNota(123, 1, 'A') !== true && !$queries, 'Modelo atual não deve ser gravado pelo seletor');

$destino = $argv[1] ?? sys_get_temp_dir() . '/danfe-rtc-testes';
if (!is_dir($destino)) { mkdir($destino, 0777, true); }
foreach (array('P', 'L') as $orientacao) {
    $danfe = new danfe_rt400($orientacao);
    $danfe->xml = $xml;
    $pdf = $danfe->gerarPDF();
    verificar(substr($pdf, 0, 5) === '%PDF-', 'PDF inválido');
    $medida = $orientacao === 'L' ? '/MediaBox [0 0 841.89 595.28]' : '/MediaBox [0 0 595.28 841.89]';
    verificar(strpos($pdf, $medida) !== false, 'Tamanho/orientação A4 incorreta');
    $temValorUtil = new \ReflectionMethod(danfe_rt400::class, 'temValorUtil');
    $temValorUtil->setAccessible(true);
    verificar(!$temValorUtil->invoke($danfe, array('total/ICMSTot/vFCP', 'total/ISSQNtot/vISS')), 'Quadros tributários facultativos vazios foram considerados preenchidos');
    file_put_contents($destino . '/rtc-' . $orientacao . '.pdf', $pdf);
    $doc = new \DOMDocument(); $doc->loadXML($xml);
    $inf = $doc->getElementsByTagName('infNFe')->item(0);
    $original = $doc->getElementsByTagName('det')->item(0);
    for ($i = 3; $i <= 45; $i++) {
        $det = $original->cloneNode(true);
        $det->setAttribute('nItem', $i);
        $det->getElementsByTagName('cProd')->item(0)->nodeValue = sprintf('MULTI%03d', $i);
        $inf->insertBefore($det, $doc->getElementsByTagName('total')->item(0));
    }
    $danfe->xml = $doc->saveXML();
    $pdf = $danfe->gerarPDF();
    verificar(preg_match('/\/Count (\d+)/', $pdf, $m) && (int) $m[1] > 1, 'Paginação não executada');
    file_put_contents($destino . '/rtc-multipagina-' . $orientacao . '.pdf', $pdf);
    $danfe->xml = str_replace('Informação adicional do primeiro item.', str_repeat('Texto longo de teste para continuidade sem truncamento. ', 900) . ' FIM-ITEM-LONGO', $xml);
    file_put_contents($destino . '/rtc-item-longo-' . $orientacao . '.pdf', $danfe->gerarPDF());
    $danfe->xml = str_replace('DOCUMENTO FICTÍCIO DE TESTE.', str_repeat('Observação longa deve continuar sem perder conteúdo. ', 1200) . ' FIM-OBSERVACAO-LONGA', $xml);
    file_put_contents($destino . '/rtc-observacao-longa-' . $orientacao . '.pdf', $danfe->gerarPDF());
}
// XML legado sem grupos opcionais: nenhum tributo/QR/regime reservado é inferido.
$doc = new \DOMDocument(); $doc->loadXML($xml);
foreach (array('IBSCBS', 'IBSCBSTot', 'IS', 'ISTot', 'cobr', 'infNFeSupl', 'vNFTot', 'transporta', 'veicTransp', 'vol') as $tag) {
    while ($no = $doc->getElementsByTagName($tag)->item(0)) { $no->parentNode->removeChild($no); }
}
$danfe = new danfe_rt400(); $danfe->xml = $doc->saveXML();
$legado = $danfe->lerXML();
verificar($legado['qrCode'] === '' && $legado['itens'][0]['bases']['IBS/CBS'] === '', 'Valores RTC criados em XML legado');
verificar($legado['total'] === '515,12', 'XML legado sem vNFTot não usa vNF');
file_put_contents($destino . '/rtc-sem-opcionais.pdf', $danfe->gerarPDF());
$danfe->cancelada = true; $danfe->visualizacao = 'S';
file_put_contents($destino . '/rtc-cancelada-preview.pdf', $danfe->gerarPDF());
$danfe->canhoto = false; $danfe->cancelada = false; $danfe->visualizacao = '';
file_put_contents($destino . '/rtc-sem-canhoto.pdf', $danfe->gerarPDF());
// Todos os campos opcionais e totais de monofasia em um mesmo documento.
$doc = new \DOMDocument(); $doc->loadXML($xml);
$tot = $doc->getElementsByTagName('ICMSTot')->item(0);
foreach (array('vFCP','vFCPST','vICMSUFDest','vFCPUFDest','qBCMono','vICMSMono','qBCMonoReten','vICMSMonoReten') as $i => $tag) {
    $tot->appendChild($doc->createElementNS('http://www.portalfiscal.inf.br/nfe', $tag, ($i + 1) . '.12'));
}
$iss = $doc->createElementNS('http://www.portalfiscal.inf.br/nfe', 'ISSQNtot');
foreach (array('vServ' => '42.00', 'vBC' => '41.00', 'vISS' => '2.05') as $tag => $valor) {
    $iss->appendChild($doc->createElementNS('http://www.portalfiscal.inf.br/nfe', $tag, $valor));
}
$doc->getElementsByTagName('total')->item(0)->appendChild($iss);
foreach (array('P', 'L') as $orientacao) {
    $danfe = new danfe_rt400($orientacao); $danfe->xml = $doc->saveXML();
    $pdf = $danfe->gerarPDF();
    file_put_contents($destino . '/rtc-opcionais-' . $orientacao . '.pdf', $pdf);
    verificar($temValorUtil->invoke($danfe, array('total/ICMSTot/vFCP', 'total/ISSQNtot/vISS')), 'Quadros tributários facultativos preenchidos foram considerados vazios');
}
$arquivo = $destino . '/cache.pdf';
file_put_contents($arquivo, '%PDF-ficticio');
if (is_file($arquivo . '.modelo')) { unlink($arquivo . '.modelo'); }
verificar(DanfeModelo::cacheValido($arquivo, 'A'), 'Cache legado rejeitado');
verificar(!DanfeModelo::cacheValido($arquivo, 'H'), 'Cache legado reutilizado como RTC');
file_put_contents($arquivo . '.modelo', 'H');
verificar(!DanfeModelo::cacheValido($arquivo, 'A') && !DanfeModelo::cacheValido($arquivo, 'H'), 'Layout RTC anterior reutilizado');
file_put_contents($arquivo . '.layout-rtc', DanfeModelo::REVISAO_LAYOUT_RTC);
verificar(DanfeModelo::cacheValido($arquivo, 'H'), 'Layout RTC revisado não reutilizado');
DanfeModelo::gravarRTC($nota + array('C004_Flag_Usar_DANFE_RTC' => 'S', 'C004_Tipo_DANFE' => 'V'), __DIR__ . '/fixtures/danfe-rtc.xml', '', $arquivo);
verificar(DanfeModelo::cacheValido($arquivo, 'V'), 'Arquivo/cache RTC não gravado');
DanfeModelo::prepararGeracaoAtual($arquivo);
verificar(!is_file($arquivo) && !is_file($arquivo . '.modelo'), 'Falha do conversor pode reutilizar PDF RTC como atual');
echo "OK: $checks verificações; PDFs fictícios em $destino\n";
