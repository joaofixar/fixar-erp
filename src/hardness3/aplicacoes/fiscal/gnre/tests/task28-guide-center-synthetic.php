<?php
declare(strict_types=1);
require_once __DIR__.'/../src/ResultInterpreter.php';
require_once __DIR__.'/../src/GuideCenter.php';
function t28(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
function s28(string $status='R'):array{return ['GNRE001_Status'=>$status];}
function r28(string $code):array{return ['valid'=>true,'guides'=>[['code'=>$code]]];}

t28(GnreGuideCenter::classify(s28('R'))['key']==='RASCUNHO','rascunho é classificado e abre dados.');
t28(GnreGuideCenter::classify(s28('P'),['attempts'=>[['GNRE004_Estado'=>'RECEBIDA']]])['key']==='AGUARDANDO','pendente recebido aguarda resultado.');
$issued=GnreGuideCenter::classify(s28('F'),['result'=>r28('0')]);t28($issued['key']==='EMITIDA'&&$issued['action']==='Abrir','somente resultado individual 0 emite.');
t28(GnreGuideCenter::classify(s28('F'))['key']!=='EMITIDA','F isolado nunca aparece como emitida.');
$rejected=GnreGuideCenter::classify(s28('F'),['result'=>r28('1')]);t28($rejected['key']==='REJEITADA'&&$rejected['action']==='Ver resultado','rejeitada apresenta ação segura de resultado.');
$inconclusive=GnreGuideCenter::classify(s28('F'),['result'=>r28('3')]);t28($inconclusive['key']==='INCONCLUSIVA'&&$inconclusive['action']==='Abrir','inconclusiva abre histórico sem reenvio.');
t28(GnreGuideCenter::classify(s28('R'),['attempts'=>[['GNRE004_Estado'=>'REJEITADA','GNRE004_Recibo'=>'']]])['key']==='RECEPCAO_REJEITADA','rejeição de recepção sem recibo é distinta.');
$filters=GnreGuideCenter::filters(['status'=>'emitida','uf'=>'ba','revenue'=>'1000','due_from'=>'2026-11-01','due_to'=>'2026-11-30','guide'=>'12','receipt'=>'26000000045967','environment'=>'2']);
t28($filters['status']==='EMITIDA'&&$filters['uf']==='BA'&&$filters['revenue']==='1000'&&$filters['guide']===12&&$filters['environment']==='2','filtros válidos são normalizados para consulta parametrizada.');
t28(GnreGuideCenter::filters(['uf'=>'BA; DROP','receipt'=>'abc','due_from'=>'2026-13-10'])['uf']===''&&GnreGuideCenter::filters(['uf'=>'BA; DROP','receipt'=>'abc','due_from'=>'2026-13-10'])['receipt']==='','filtros inválidos são descartados no servidor.');
$query=http_build_query(array_filter(array_merge(['view'=>'list'],$filters),static function($v){return $v!==''&&$v!==0;}));t28(strpos($query,'status=EMITIDA')!==false&&strpos($query,'uf=BA')!==false&&strpos($query,'receipt=26000000045967')!==false,'query string preserva filtros combinados.');
$sql=(string)file_get_contents(__DIR__.'/../src/GuideCenter.php');t28(strpos($sql,'g.GNRE001_C004_Id=?')!==false&&strpos($sql,'GNRE008_C004_Id=')!==false,'consulta aplica escopo da empresa às guias e documentos.');
$index=(string)file_get_contents(__DIR__.'/../public/index.php');t28(strpos($index,"GnreResultInterpreter::interpret((string)(\$queue['GNRE005_Ultima_Consulta_SOAP']??''))")!==false,'página individual interpreta novamente o SOAP persistido da fila antes do badge.');
function gnre_h($value):string{return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');}
function gnre_url(string $path=''):string{return '/gnre/'.$path;}
function gnre_guide_url(int $id,string $tab='dados'):string{return '/gnre/?edit='.$id.'&tab='.$tab;}
require_once __DIR__.'/../public/task28-guide-center.php';
$viewFilters=['status'=>'','uf'=>'','revenue'=>'','due_from'=>'','due_to'=>'','guide'=>0,'receipt'=>'','environment'=>''];
$baseRow=['GNRE001_Id'=>1,'GNRE001_Status'=>'R','GNRE001_UF_Favorecida'=>'BA','GNRE001_Codigo_Receita'=>'1000','GNRE001_Data_Vencimento'=>'0000-00-00','GNRE001_Valor'=>'10.00','GNRE001_Data_Hora_Criacao'=>'2026-11-02 10:00:00','GNRE001_Data_Hora_Alteracao'=>null,'_queue'=>null,'_attempts'=>[],'_pdf_id'=>0,'_state'=>GnreGuideCenter::classify(s28())];
$one=$baseRow;$one['_item_due_dates']=['2026-11-05'];$html=task28_render_guide_center($viewFilters,[$one],[$one],['RASCUNHO'=>1,'AGUARDANDO'=>0,'EMITIDA'=>0,'REJEITADA'=>0,'INCONCLUSIVA'=>0],true);t28(strpos($html,'05/11/2026')!==false,'vencimento do item substitui cabeçalho zerado quando é único.');
$many=$baseRow;$many['_item_due_dates']=['2026-11-05','2026-11-10'];$html=task28_render_guide_center($viewFilters,[$many],[$many],['RASCUNHO'=>1,'AGUARDANDO'=>0,'EMITIDA'=>0,'REJEITADA'=>0,'INCONCLUSIVA'=>0],true);t28(strpos($html,'Múltiplos vencimentos')!==false,'datas distintas dos itens não escolhem vencimento arbitrário.');
t28(strpos($html,'<th>Controle</th>')===false&&strpos($html,'GNRE001_Numero_Controle')===false,'Central não renderiza a coluna Controle.');
t28(substr_count($html,'<th>')===10&&strpos($html,'Data de criação')!==false&&strpos($html,'02/11/2026')!==false&&strpos($html,'min-width:960px')!==false,'tabela inclui a data de criação com dez colunas.');
t28(strpos($html,'R$ 10,00')!==false&&strpos($html,'Rascunho')!==false&&strpos($html,'#1')!==false&&strpos($html,'Abrir')!==false,'remoção preserva situação, valor e ação existentes.');
$center=(string)file_get_contents(__DIR__.'/../public/task28-guide-center.php');
t28(strpos($center,'GNRE005_Ultima_Consulta_SOAP')===false&&strpos($center,'GnreResultInterpreter')===false&&strpos($center,'PortalClient')===false,'GET da Central permanece sem interpretar SOAP ou chamar o Portal.');
echo "OK Task 28: central de guias sintética passou.\n";
