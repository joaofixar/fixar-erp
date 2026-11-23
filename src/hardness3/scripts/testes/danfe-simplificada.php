<?php
/** XML fictício e stubs de banco: não carrega bootstrap nem acessa base de cliente. */
namespace hardness;
if (PHP_SAPI !== 'cli') { exit; }
error_reporting(E_ALL & ~E_DEPRECATED);
require_once __DIR__ . '/../../bibliotecas/classes/DanfeSimplificadaDocumento.php';
require_once __DIR__ . '/../../prog/ven/ven003/grid_func-callback.php';
set_error_handler(function ($nivel, $mensagem, $arquivo, $linha) {
    if (error_reporting() & $nivel) { throw new \RuntimeException("$mensagem em $arquivo:$linha"); }
});
$checks = 0;
function conferir($ok, $mensagem) {
    global $checks;
    $checks++;
    if (!$ok) { throw new \RuntimeException($mensagem); }
}
function rejeitar($acao, $mensagem) {
    try { $acao(); } catch (\InvalidArgumentException $e) { conferir(true, $mensagem); return; }
    conferir(false, $mensagem);
}
$destino = $argv[1] ?? sys_get_temp_dir() . '/danfe-simplificada-testes';
if (!is_dir($destino)) { mkdir($destino, 0777, true); }
$xml = file_get_contents(__DIR__ . '/fixtures/danfe-rtc.xml');
$xml = str_replace('</infNFe>', '<pag><detPag><tPag>01</tPag><vPag>300.00</vPag></detPag><detPag><tPag>17</tPag><vPag>230.00</vPag></detPag><vTroco>14.88</vTroco></pag></infNFe>', $xml);
$xml = str_replace('</infNFeSupl>', '<urlChave>https://example.com/consulta</urlChave></infNFeSupl>', $xml);
$hash = hash('sha256', $xml);
$danfe = new DanfeSimplificada($xml);
$dados = $danfe->dados();
conferir($dados['protocolo'] === '141260000000123', 'Protocolo de autorização incorreto');
conferir(strpos($dados['qrCode'], 'https://example.com/nfe?') === 0, 'Conteúdo do QR modificado');
file_put_contents($destino . '/normal.pdf', $danfe->gerarPDF());
conferir(hash('sha256', $xml) === $hash && strpos($xml, '<tpImp>1</tpImp>') !== false, 'XML da emissão normal alterado');
file_put_contents($destino . '/cancelada.pdf', (new DanfeSimplificada($xml, true))->gerarPDF());
$legado = preg_replace('~<infNFeSupl>.*?</infNFeSupl>~s', '', $xml);
$legado = preg_replace('~<IBSCBSTot>.*?</IBSCBSTot>|<ISTot>.*?</ISTot>|<pag>.*?</pag>|<vNFTot>.*?</vNFTot>~s', '', $legado);
$semQr = new DanfeSimplificada($legado);
conferir($semQr->dados()['qrCode'] === 'http://www.fazenda.pr.gov.br/nfce/qrcode?p=' . $dados['chave'] . '|3|2', 'QR online v3 de homologação do PR incorreto');
conferir($semQr->dados()['urlChave'] === 'http://www.fazenda.pr.gov.br/nfce/consulta', 'Consulta por chave do PR incorreta');
file_put_contents($destino . '/sem-qr.pdf', $semQr->gerarPDF());
$sc = str_replace(array('412610', '<cUF>41</cUF>', '<UF>PR</UF>'), array('422610', '<cUF>42</cUF>', '<UF>SC</UF>'), preg_replace('~<infNFeSupl>.*?</infNFeSupl>~s', '', $xml));
foreach (array('2' => 'https://hom.sat.sef.sc.gov.br', '1' => 'https://sat.sef.sc.gov.br') as $ambiente => $base) {
    $conteudo = str_replace('<tpAmb>2</tpAmb>', '<tpAmb>' . $ambiente . '</tpAmb>', $sc);
    $impressao = new DanfeSimplificada($conteudo);
    conferir($impressao->dados()['qrCode'] === $base . '/nfce/consulta?p=' . str_replace('412610', '422610', $dados['chave']) . '|3|' . $ambiente, 'QR de SC usa UF/ambiente incorretos');
    conferir($impressao->dados()['urlChave'] === $base . '/nfce/consulta', 'Consulta por chave de SC incorreta');
    file_put_contents($destino . '/sc-' . $ambiente . '.pdf', $impressao->gerarPDF());
}
$semAjustes = str_replace(array('<vFrete>10.00</vFrete>', '<vDesc>5.00</vDesc>', '<vNF>515.12</vNF>', '<vTroco>14.88</vTroco>', '<vIS>4.00</vIS>'),
    array('<vFrete>0.00</vFrete>', '<vDesc>0.00</vDesc>', '<vNF>500.12</vNF>', '', '<vIS>0.00</vIS>'), $xml);
$semAjustes = str_replace('<dest>', '<entrega><xLgr>Rua da Entrega</xLgr><nro>42</nro><xMun>Curitiba</xMun><UF>PR</UF><CEP>80000000</CEP></entrega><dest>', $semAjustes);
file_put_contents($destino . '/sem-ajustes.pdf', (new DanfeSimplificada($semAjustes))->gerarPDF());
// Uma URL já autorizada é preservada, inclusive na contingência offline.
$offline = str_replace('<tpEmis>1</tpEmis>', '<tpEmis>9</tpEmis>', $xml);
conferir((new DanfeSimplificada($offline))->dados()['qrCode'] === $dados['qrCode'], 'QR original de contingência substituído');
// Confere a leitura das tabelas locais de todas as UFs, sem consultar nenhum portal.
foreach (array('11'=>'RO','12'=>'AC','13'=>'AM','14'=>'RR','15'=>'PA','16'=>'AP','17'=>'TO',
    '21'=>'MA','22'=>'PI','23'=>'CE','24'=>'RN','25'=>'PB','26'=>'PE','27'=>'AL','28'=>'SE','29'=>'BA',
    '31'=>'MG','32'=>'ES','33'=>'RJ','35'=>'SP','41'=>'PR','42'=>'SC','43'=>'RS','50'=>'MS','51'=>'MT','52'=>'GO','53'=>'DF') as $codigo => $uf) {
    $conteudo = str_replace(array('412610', '<cUF>41</cUF>', '<UF>PR</UF>'), array($codigo . '2610', '<cUF>' . $codigo . '</cUF>', '<UF>' . $uf . '</UF>'), $legado);
    foreach (array('1', '2') as $ambiente) {
        $impressao = new DanfeSimplificada(str_replace('<tpAmb>2</tpAmb>', '<tpAmb>' . $ambiente . '</tpAmb>', $conteudo));
        conferir(substr($impressao->dados()['qrCode'], -4) === '|3|' . $ambiente, 'UF sem QR v3: ' . $uf);
    }
}

foreach (array('<invalido>', '<!DOCTYPE nfeProc SYSTEM "file:///etc/passwd">' . $xml,
    str_replace('<mod>55</mod>', '<mod>65</mod>', $xml),
    str_replace('<cStat>100</cStat>', '<cStat>301</cStat>', $xml),
    str_replace('<tpAmb>2</tpAmb>', '<tpAmb>3</tpAmb>', $xml),
    str_replace('<infProt><tpAmb>2</tpAmb>', '<infProt><tpAmb>1</tpAmb>', $xml),
    str_replace('<tpEmis>1</tpEmis>', '<tpEmis>9</tpEmis>', $legado),
    str_replace('<UF>PR</UF>', '<UF>SC</UF>', $legado),
    str_replace('<chNFe>412610', '<chNFe>422610', $xml),
    preg_replace('~<protNFe.*?</protNFe>~s', '', $xml),
    preg_replace('~<det nItem=.*?</det>~s', '', $xml)) as $invalido) {
    rejeitar(function () use ($invalido) { new DanfeSimplificada($invalido); }, 'XML inválido/sem autorização aceito');
}
$doc = new \DOMDocument(); $doc->loadXML($xml);
$inf = $doc->getElementsByTagName('infNFe')->item(0);
$primeiro = $doc->getElementsByTagName('det')->item(0);
for ($i = 3; $i <= 80; $i++) {
    $item = $primeiro->cloneNode(true);
    $item->setAttribute('nItem', $i);
    $item->getElementsByTagName('cProd')->item(0)->nodeValue = sprintf('MULTI%03d', $i);
    $inf->insertBefore($item, $doc->getElementsByTagName('total')->item(0));
}
file_put_contents($destino . '/multipagina.pdf', (new DanfeSimplificada($doc->saveXML()))->gerarPDF());
$longo = str_replace('Informação adicional do primeiro item.', str_repeat('Texto longo sem truncamento. ', 350) . ' FIM-ITEM-LONGO', $xml);
$longo = str_replace('DOCUMENTO FICTÍCIO DE TESTE.', str_repeat('Observação longa deve continuar na próxima folha. ', 400) . ' FIM-OBSERVACAO-LONGA', $longo);
file_put_contents($destino . '/textos-longos.pdf', (new DanfeSimplificada($longo))->gerarPDF());

// Stubs exclusivos deste teste, para verificar escopo por empresa e seleção do arquivo.
$queries = array();
$notaStub = array('T007_Chave_Acesso_Nfe' => $dados['chave'], 'T007_Numero_Protocolo_Nfe' => $dados['protocolo'],
    'T007_Data_Emissao' => '2026-10-13', 'T007_Flag_Cancelada' => 'N');
function mysql_query($sql) { global $queries; $queries[] = $sql; return true; }
function mysql_fetch_assoc($sql) { global $notaStub; return $notaStub; }
$pasta = $destino . '/dados-ficticios';
mkdir($pasta . '/nfe/xml/autorizado/2026-10', 0777, true);
$arquivo = $pasta . '/nfe/xml/autorizado/2026-10/' . $dados['protocolo'] . '.xml';
file_put_contents($arquivo, $xml);
$resultado = DanfeSimplificadaDocumento::gerar(123, 7, $pasta);
conferir(substr($resultado['conteudo'], 0, 5) === '%PDF-', 'PDF por protocolo não gerado');
conferir(count($queries) === 1 && strpos($queries[0], "T007_C004_Id = '7'") !== false && strpos($queries[0], 'LIMIT 1') !== false, 'Consulta sem limite ou filtro de empresa');
conferir(hash_file('sha256', $arquivo) === $hash, 'XML autorizado gravado ou alterado');
$legadoArquivo = $pasta . '/nfe/xml/autorizado/' . $dados['protocolo'] . '.xml';
rename($arquivo, $legadoArquivo);
conferir(substr(DanfeSimplificadaDocumento::gerar(123, 7, $pasta)['conteudo'], 0, 5) === '%PDF-', 'Pasta legada não encontrada');
$queries = array();
rejeitar(function () use ($pasta) { DanfeSimplificadaDocumento::gerar('1 OR 1=1', 7, $pasta); }, 'Id inválido aceito');
rejeitar(function () use ($pasta) { DanfeSimplificadaDocumento::gerar(123, 0, $pasta); }, 'Empresa vazia aceita');
conferir(!$queries, 'Consulta executada com parâmetros inválidos');
$notaGuardada = $notaStub; $notaStub = false;
rejeitar(function () use ($pasta) { DanfeSimplificadaDocumento::gerar(123, 8, $pasta); }, 'Nota de outra empresa aceita');
$notaStub = $notaGuardada; $notaStub['T007_Chave_Acesso_Nfe'] = str_repeat('9', 44);
rejeitar(function () use ($pasta) { DanfeSimplificadaDocumento::gerar(123, 7, $pasta); }, 'XML de outra NF-e aceito');
$notaStub = $notaGuardada; $notaStub['T007_Numero_Protocolo_Nfe'] = '../../arquivo';
rejeitar(function () use ($pasta) { DanfeSimplificadaDocumento::gerar(123, 7, $pasta); }, 'Protocolo inválido aceito como caminho');

$linha = array('T007_Id' => 123, 'T007_Flag_ACP' => '6', 'T007_Versao_Nfe' => '4.00', 'T007_Numero_Protocolo_Nfe' => $dados['protocolo']);
conferir(strpos(ValidaCampoDanfeSimplificada(array('todosCampos' => $linha)), 'T007_Id=123') !== false, 'Coluna não abre a nota autorizada');
foreach (array(array('T007_Numero_Protocolo_Nfe' => ''), array('T007_Flag_ACP' => '8'), array('T007_Versao_Nfe' => '3.10')) as $alteracao) {
    conferir(ValidaCampoDanfeSimplificada(array('todosCampos' => array_merge($linha, $alteracao))) === '', 'Link exibido para documento sem suporte');
}
unlink($legadoArquivo);
rmdir($pasta . '/nfe/xml/autorizado/2026-10'); rmdir($pasta . '/nfe/xml/autorizado');
rmdir($pasta . '/nfe/xml'); rmdir($pasta . '/nfe'); rmdir($pasta);
echo "OK: $checks verificações; PDFs fictícios em $destino\n";
