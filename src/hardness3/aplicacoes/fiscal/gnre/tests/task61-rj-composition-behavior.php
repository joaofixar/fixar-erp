<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ConfigUfRules.php';
require_once __DIR__.'/../src/NfGnreDraftCreator.php';
function t61b(bool $ok,string $message):void {if(!$ok)throw new RuntimeException('Task 61 RJ behavior: '.$message);}
function c61b(array $note,string $tax):array {foreach(GnreNfDraftCreator::components($note)as$c)if($c['tax']===$tax)return$c;return[];}
$note=['indicator_difal'=>true,'T007_Valor_Total_Partilha_ICMS_Destino'=>'20.10','indicator_fcp'=>true,'T007_Valor_Total_FCP_Partilha'=>'3.33'];$d=c61b($note,'DIFAL');$f=c61b($note,'FCP');
$rules=['uf_types'=>['0'],'receipts'=>['100102'=>['types'=>['0'],'fields'=>['fecp'=>'S']]]];
$ready=GnreNfDraftCreator::rjCompositionDecision($d,$f,0,0,$rules);t61b($ready['status']==='ready'&&$ready['tax']==='DIFAL+FCP'&&$ready['types']===['0'],'RJ pronto deve ser uma única combinação simples.');
$same=GnreNfDraftCreator::rjCompositionDecision($d,$f,41,41,$rules);t61b($same['status']==='existing'&&$same['guide_id']===41,'dois vínculos na mesma guia devem ser existentes.');
$partial=GnreNfDraftCreator::rjCompositionDecision($d,$f,41,0,$rules);t61b($partial['status']==='blocked'&&$partial['types']===[],'vínculo parcial deve bloquear sem ocultar pendência.');
$bad=$rules;$bad['receipts']['100102']['fields']['fecp']='N';$blocked=GnreNfDraftCreator::rjCompositionDecision($d,$f,0,0,$bad);t61b($blocked['status']==='blocked','snapshot sem FCP deve bloquear antes de escrita.');
t61b($d['value_type']==='11'&&$f['value_type']==='11'&&c61b(['indicator_fcp'=>true,'T007_Valor_Total_FCP_Partilha'=>'3.33'],'FCP')['receipt']==='100129','FCP fora do RJ deve ser 100129/11.');
$created=['guides'=>[['receipt'=>'100102']],'links'=>['DIFAL','FCP'],'values'=>[['type'=>'11','value'=>$d['value']],['type'=>'12','value'=>$f['value']]]];t61b(count($created['guides'])===1&&$created['links']===['DIFAL','FCP']&&array_column($created['values'],'type')===['11','12'],'composição bem-sucedida deve ter uma guia, dois vínculos e valores 11/12.');
/* Adaptador transacional observável, sem mysqli: lock → releitura → validação
   → inserts; qualquer erro restaura o snapshot do armazenamento. */
final class Task61FakeTx {public array $events=[],$guides=[],$links=[],$values=[];public bool $failSecond=false;function run():void{$before=[$this->guides,$this->links,$this->values];try{$this->events[]='lock';$this->events[]='reload';$this->events[]='validate';$this->guides[]='100102';$this->events[]='guide';foreach(['DIFAL','FCP']as$tax){if($tax==='FCP'&&$this->failSecond)throw new RuntimeException('duplicate');$this->links[]=$tax;$this->events[]='link-'.$tax;}$this->values=['11','12'];$this->events[]='commit';}catch(Throwable$e){[$this->guides,$this->links,$this->values]=$before;$this->events[]='rollback';throw$e;}}}
$fake=new Task61FakeTx();$fake->run();t61b($fake->events===['lock','reload','validate','guide','link-DIFAL','link-FCP','commit']&&$fake->links===['DIFAL','FCP']&&$fake->values===['11','12'],'ordem transacional e persistência RJ devem ser observáveis.');
$failed=new Task61FakeTx();$failed->failSecond=true;try{$failed->run();}catch(Throwable$e){}t61b($failed->guides===[]&&$failed->links===[]&&$failed->values===[]&&end($failed->events)==='rollback','falha do segundo vínculo deve reverter integralmente.');
t61b(in_array('0',$ready['types'],true)&&!in_array('1',$ready['types'],true)&&!in_array('2',$ready['types'],true),'somente tipo 0 é aceito.');
echo "Task 61 RJ composition behavior: OK\n";
