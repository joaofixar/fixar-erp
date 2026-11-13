<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GnreBatchCorrelationFingerprint.php';
function t65(bool $ok,string $message):void { if(!$ok)throw new RuntimeException($message); }
function t65_data(string $revenue,string $amount,string $date='2026-11-10'):array{return ['ufFavorecida'=>' PR ','tipoGnre'=>'0','contribuinteEmitente'=>['identificacao'=>['CNPJ'=>'12345678000199']],'itensGNRE'=>['item'=>[['receita'=>$revenue,'documentoOrigem'=>['tipo'=>'55','valor'=>' 000123 '],'dataVencimento'=>$date,'valor'=>[['tipo'=>'11','valor'=>$amount]]]]],'valorGNRE'=>$amount,'dataPagamento'=>'09/11/2026'];}
$a=t65_data('100001','10.00');$b=t65_data('100002','20.00');
$result=GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a],['id'=>'B','tdados'=>$b]],$b);
t65($result['decision']==='MATCHED'&&$result['participant_ids']===['B'],'duas guias distintas devem correlacionar inequivocamente.');
$equivalent=t65_data('100001','00010,000','10/11/2026');$equivalent['ufFavorecida']=' pr ';$equivalent['dataPagamento']='2026/11/09';$equivalent['itensGNRE']['item'][0]['documentoOrigem']['valor']='000123';
t65(GnreBatchCorrelationFingerprint::describe($a)['fingerprint']===GnreBatchCorrelationFingerprint::describe($equivalent)['fingerprint'],'espaços, valor com zeros/data e texto equivalentes devem normalizar deterministicamente.');
$nfA=$a;$nfA['itensGNRE']['item'][0]['documentoOrigem']=['tipo'=>'55','valor'=>'35261111111111111111550010000000011000000001'];$nfA['itensGNRE']['item'][0]['camposExtras']=['campoExtra'=>[['codigo'=>'99','valor'=>'CHAVE NFE 35261111111111111111550010000000011000000001']]];
$nfB=$a;$nfB['itensGNRE']['item'][0]['documentoOrigem']=['tipo'=>'55','valor'=>'35261111111111111111550010000000021000000002'];$nfB['itensGNRE']['item'][0]['camposExtras']=['campoExtra'=>[['codigo'=>'99','valor'=>'CHAVE NFE 35261111111111111111550010000000021000000002']]];
$nfADescription=GnreBatchCorrelationFingerprint::describe($nfA);$nfBDescription=GnreBatchCorrelationFingerprint::describe($nfB);
t65($nfADescription['fingerprint']===$nfBDescription['fingerprint'],'documento de origem e campo extra não podem integrar o fingerprint de negócio.');
t65($nfADescription['fragment_sha256']!==$nfBDescription['fragment_sha256'],'fragmento de auditoria deve registrar mudança de documento de origem ou campo extra.');
$nfDuplicate=GnreBatchCorrelationFingerprint::correlate([['id'=>'NFE-A','tdados'=>$nfA],['id'=>'NFE-B','tdados'=>$nfB]],$nfA);
t65($nfDuplicate['decision']==='AMBIGUOUS','guias diferenciadas somente por NF-e devem continuar bloqueadas por ambiguidade.');
$duplicate=GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a],['id'=>'A2','tdados'=>$a]],$a);
t65($duplicate['decision']==='AMBIGUOUS','duas participantes iguais devem bloquear por ambiguidade.');
t65(GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a]],$b)['decision']==='UNMATCHED','retorno suficientemente formado sem participante compatível deve ser UNMATCHED.');
t65(GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a]],null)['decision']==='INSUFFICIENT_EVIDENCE','ausência de TDadosGNRE deve bloquear.');
$partial=['ufFavorecida'=>'PR','tipoGnre'=>'0','itensGNRE'=>['item'=>[['receita'=>'100001']]]];
t65(GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a]],$partial)['decision']==='INSUFFICIENT_EVIDENCE','retorno parcial sem âncoras mínimas deve bloquear.');
$inverted=[GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a],['id'=>'B','tdados'=>$b]],$b),GnreBatchCorrelationFingerprint::correlate([['id'=>'A','tdados'=>$a],['id'=>'B','tdados'=>$b]],$a)];
t65($inverted[0]['participant_ids']===['B']&&$inverted[1]['participant_ids']===['A'],'ordem invertida dos resultados não pode alterar a correlação.');
$withControl=$a;$withControl['itensGNRE']['item'][0]['numeroControle']='000001';
t65(GnreBatchCorrelationFingerprint::describe($a)['fingerprint']===GnreBatchCorrelationFingerprint::describe($withControl)['fingerprint'],'numeroControle não pode participar da correlação.');
foreach(['0'=>true,'1'=>false,'2'=>false,'3'=>false,'4'=>false] as $situacao=>$authorized)t65(GnreBatchCorrelationFingerprint::isAuthorizedSituation($situacao)===$authorized,'somente situacaoGuia=0 autoriza: '.$situacao);
echo "Task 65 batch correlation fingerprint synthetic checks OK\n";
