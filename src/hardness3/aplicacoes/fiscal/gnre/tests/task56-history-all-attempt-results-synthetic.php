<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ResultInterpreter.php';
require_once __DIR__.'/../src/HistoryResultProjection.php';
function t56(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
function t56soap(string $reason):string{return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><situacaoProcess><codigo>403</codigo><descricao>Lote</descricao></situacaoProcess><resultado><guia><situacaoGuia>1</situacaoGuia><motivosRejeicao><motivo><codigo>226</codigo><descricao>'.$reason.'</descricao><campo>dataPagamento</campo></motivo></motivosRejeicao></guia></resultado></TResultLote_GNRE></soap:Body></soap:Envelope>';}
function t56authorized():string{return '<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><situacaoProcess><codigo>402</codigo><descricao>Lote Processado com sucesso</descricao></situacaoProcess><resultado><guia><situacaoGuia>0</situacaoGuia></guia></resultado></TResultLote_GNRE></soap:Body></soap:Envelope>';}
$queues=[
 ['GNRE005_GNRE004_Id'=>101,'GNRE005_Recibo'=>'26000000046772','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>t56soap('Motivo da tentativa 1')],
 ['GNRE005_GNRE004_Id'=>102,'GNRE005_Recibo'=>'26000000046772','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>t56soap('Motivo da tentativa 2')],
 ['GNRE005_GNRE004_Id'=>103,'GNRE005_Recibo'=>'26000000046775','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>t56soap('Motivo da tentativa 3')],
 ['GNRE005_GNRE004_Id'=>104,'GNRE005_Recibo'=>'26000000046776','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>''],
 ['GNRE005_GNRE004_Id'=>105,'GNRE005_Recibo'=>'26000000046769','GNRE005_Situacao'=>'FINALIZADA','GNRE005_Ultima_Consulta_SOAP'=>t56authorized()],
];
$projected=GnreHistoryResultProjection::byAttempt($queues);
t56(array_keys($projected)===[101,102,103,105],'as GNRE005 finalizadas são projetadas pela própria GNRE004, mesmo com recibo repetido.');
foreach([101=>1,102=>2,103=>3] as $attempt=>$number){$result=$projected[$attempt];t56(($result['guides'][0]['code']??'')==='1','tentativa '.$number.' preserva seu resultado individual rejeitado.');$reason=$result['guides'][0]['rejections'][0]??[];t56(($reason['code']??'')==='226'&&($reason['description']??'')==='Motivo da tentativa '.$number&&($reason['field']??'')==='dataPagamento','tentativa '.$number.' preserva o motivo da própria fila.');}
t56(!isset($projected[104]),'tentativa sem SOAP final válido não recebe resultado ou motivo inventado.');
t56(($projected[105]['guides'][0]['code']??'')==='0'&&($projected[105]['guides'][0]['description']??'')==='Processada com sucesso','situacaoGuia 0 persistida projeta autorização individual, sem inferi-la do lote 402.');
$page=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
t56(strpos($page,'GnreHistoryResultProjection::byAttempt($resultQueues)')!==false,'Histórico não usa a coleção completa resultQueues.');
t56(strpos($page,'$attemptResultsById[(int)($attempt[\'GNRE004_Id\']??0)]')!==false,'Histórico não relaciona resultado por GNRE005_GNRE004_Id.');
echo "Task 56 history all attempt results synthetic: OK\n";
