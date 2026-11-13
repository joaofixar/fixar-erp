<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/QueueResultPresentation.php';

function task71_result_soap(string $guides): string {return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><situacaoProcess><codigo>100</codigo><descricao>Finalizado</descricao></situacaoProcess><resultado>'.$guides.'</resultado></TResultLote_GNRE></soap:Body></soap:Envelope>';}
function task71_guide(string $code, string $reasons=''): string {return '<guia><situacaoGuia>'.$code.'</situacaoGuia>'.$reasons.'</guia>';}
function task71_same($actual,$expected,string $message):void {if($actual!==$expected)throw new RuntimeException($message.'; recebido: '.var_export($actual,true));}

$authorized=GnreQueueResultPresentation::fromPersistedSoap(task71_result_soap(task71_guide('0')),true);
task71_same($authorized['state'],'authorized','situacaoGuia=0 deve autorizar');
task71_same($authorized['message'],'Guia autorizada pelo Portal.','autorização deve ter mensagem pública clara');
$reasons='<motivosRejeicao><motivo><codigo>226</codigo><descricao>Data de pagamento inválida</descricao><campo>dataPagamento</campo></motivo></motivosRejeicao>';
foreach(['1','2'] as $code){$rejected=GnreQueueResultPresentation::fromPersistedSoap(task71_result_soap(task71_guide($code,$reasons)),true);task71_same($rejected['state'],'rejected','situacaoGuia='.$code.' deve rejeitar');if(strpos($rejected['message'],'226 — Data de pagamento inválida. Campo: dataPagamento')===false)throw new RuntimeException('motivo persistido não foi apresentado');}
foreach(['','<soap>',task71_result_soap(task71_guide('9')),task71_result_soap(task71_guide('0').task71_guide('0'))] as $soap){$inconclusive=GnreQueueResultPresentation::fromPersistedSoap($soap,true);task71_same($inconclusive['state'],'inconclusive','resultado final sem evidência individual inequívoca deve ser inconclusivo');}
$notFinal=GnreQueueResultPresentation::fromPersistedSoap(task71_result_soap(task71_guide('4')),false);task71_same($notFinal['state'],'','pendência não pode ser classificada como final');
$js=(string)file_get_contents(__DIR__.'/../public/task63-batch.js');
if(strpos($js,"if(result.state==='authorized')")===false||strpos($js,"post(id,'pdf')")===false||strpos($js,"if(result.state==='rejected')")===false||strpos($js,"if(result.state==='inconclusive')")===false)throw new RuntimeException('o ciclo não condiciona PDF e classificação à evidência persistida');
if(strpos($js,"finish(item,'blocked'")===false)throw new RuntimeException('bloqueio real deve continuar separado');
foreach(['Autorizadas','Rejeitadas','Pendentes','Resultado inconclusivo','Bloqueadas','Falhas técnicas','Não iniciadas'] as $label)if(strpos($js,$label)===false)throw new RuntimeException('resumo final deve separar '.$label);
if(strpos($js,"item.status='done';item.outcome=state;item.message=text;")===false)throw new RuntimeException('cada guia deve manter somente seu estado final consolidado');
if(strpos($js,"pdf.ok&&pdf.status==='stored'&&Number(pdf.document_id)>0")===false||strpos($js,"window.open(printUrl(storedPdfs),'_blank','noopener')")===false)throw new RuntimeException('impressão consolidada deve exigir PDF persistido pela Fila no ciclo');
if(strpos($js,'Abrir guia')!==false||strpos($js,'Imprimir PDF')!==false)throw new RuntimeException('resumo final não deve oferecer ações individuais por guia');
echo "OK Task 71 sequential queue behavioral checks\n";
