<?php
declare(strict_types=1);
if (!class_exists('DOMDocument')) { echo "task15 config/ui synthetic: SKIP (DOM ausente)\n"; exit; }
require_once __DIR__.'/../src/ConfigUfRules.php';
function t15ui(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
$xml='<TConfigUf xmlns="http://www.gnre.pe.gov.br"><uf>PR</uf><situacaoConsulta><codigo>450</codigo></situacaoConsulta><versoesXml><versao>2.00</versao></versoesXml><tiposGnreDaUF><tipoGnre>0</tipoGnre></tiposGnreDaUF><receitas><receita codigo="100001" descricao="Completa"><exigeDetalhamentoReceita>S</exigeDetalhamentoReceita><detalhamentosReceita><x><codigo>D1</codigo><descricao>Detalhe</descricao></x></detalhamentosReceita><exigeProduto>S</exigeProduto><produtos><x><codigo>P1</codigo><descricao>Produto</descricao></x></produtos><exigePeriodoApuracao>S</exigePeriodoApuracao><periodosApuracao><x><codigo>1</codigo><descricao>Mensal</descricao></x></periodosApuracao><exigeDocumentoOrigem>S</exigeDocumentoOrigem><tiposDocumentosOrigem><x><codigo>01</codigo><descricao>NF</descricao></x></tiposDocumentosOrigem><valorExigido>P</valorExigido><tiposGnre><tipoGnre>0</tipoGnre></tiposGnre></receita><receita codigo="100002" descricao="Incompleta"><exigePeriodoApuracao>S</exigePeriodoApuracao><valorExigido>P</valorExigido><tiposGnre><tipoGnre>0</tipoGnre></tiposGnre></receita></receitas></TConfigUf>';
$rules=GnreConfigUfRules::fromXml($xml);
$complete=$rules['receipts']['100001'];$incomplete=$rules['receipts']['100002'];
t15ui($complete['fields']['period']==='S'&&$complete['periods'][0]['code']==='1','periodosApuracao não foi interpretado.');
t15ui($complete['details'][0]['code']==='D1'&&$complete['products'][0]['code']==='P1'&&$complete['document_types'][0]['code']==='01','listas oficiais não foram interpretadas.');
t15ui($incomplete['fields']['period']==='S'&&$incomplete['periods']===[],'fixture incompleta inválida.');
$ui=(string)file_get_contents(__DIR__.'/../public/task15-ui.js');
t15ui(strpos($ui,"selectOptions('period',r.periods)")!==false&&strpos($ui,'incomplete(')!==false,'UI não preenche/bloqueia período.');
echo "task15 config/ui synthetic: OK\n";
