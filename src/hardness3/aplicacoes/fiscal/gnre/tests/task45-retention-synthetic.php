<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ResultInterpreter.php';require_once __DIR__.'/../src/GuideRetention.php';
function t45(bool $ok,string $m):void{if(!$ok)throw new RuntimeException($m);}function p45(array $g,array $a=[],array $q=[]):string{return GnreGuideRetention::project(array_merge(['GNRE001_Status'=>'R','GNRE001_Arquivada'=>'N'],$g),GnreGuideRetention::facts($a,$q,[]))['action'];}function a45(string $state,int $seq,string $receipt=''):array{return ['GNRE004_Id'=>$seq,'GNRE004_Estado'=>$state,'GNRE004_Sequencia'=>$seq,'GNRE004_Recibo'=>$receipt];}function q45(int $attempt,string $state,string $soap=''):array{return ['GNRE005_GNRE004_Id'=>$attempt,'GNRE005_Situacao'=>$state,'GNRE005_Ultima_Consulta_SOAP'=>$soap];}
t45(p45([])==='delete','rascunho sem histórico exclui');
t45(p45([], [a45('REJEITADA',1)])==='archive','rejeição técnica terminal arquiva');
t45(p45([], [a45('REJEITADA',1),a45('RECEBIDA',2)])==='none','tentativa recebida posterior bloqueia');
t45(p45([], [a45('REJEITADA',1)],[q45(1,'PENDENTE')])==='none','fila pendente bloqueia');t45(p45([], [a45('REJEITADA',1)],[q45(1,'PROCESSANDO')])==='none','fila processando bloqueia');
t45(p45([], [a45('REJEITADA',1),a45('INCONCLUSIVA',2)])==='none','inconclusiva bloqueia');
$soap=static fn(string $code):string=>'<soap:Envelope xmlns:soap="urn:test" xmlns="http://www.gnre.pe.gov.br"><soap:Body><TResultLote_GNRE><ambiente>2</ambiente><situacaoProcess><codigo>403</codigo><descricao>Lote</descricao></situacaoProcess><resultado><guia><situacaoGuia>'.$code.'</situacaoGuia></guia></resultado></TResultLote_GNRE></soap:Body></soap:Envelope>';$approved=$soap('0');$rejected=$soap('1');
t45(p45(['GNRE001_Status'=>'F'],[a45('RECEBIDA',1,'R')],[q45(1,'FINALIZADA',$approved)])==='none','autorizada bloqueia');
t45(p45(['GNRE001_Status'=>'F'],[a45('RECEBIDA',1,'R')],[q45(1,'FINALIZADA',$rejected)])==='archive','rejeição individual final arquiva');
t45(p45(['GNRE001_Status'=>'F'],[a45('RECEBIDA',1,'R'),a45('RECEBIDA',2,'R')],[q45(1,'FINALIZADA',$rejected)])==='none','resultado anterior não libera tentativa recente sem resultado');
t45(p45(['GNRE001_Arquivada'=>'S'],[a45('REJEITADA',1)])==='unarchive','arquivada somente desarquiva');
$source=(string)file_get_contents(__DIR__.'/../src/GuideRetention.php');t45(strpos($source,'FOR UPDATE')!==false&&strpos($source,'mysqli_commit')!==false,'execução bloqueada e commit confirmados');t45(strpos($source,'GNRE004_Sequencia')!==false,'terminal considera sequência');echo "Task 45 retention synthetic: OK\n";
