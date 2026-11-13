<?php
declare(strict_types=1);
if(!class_exists('DOMDocument')){echo "task26.5 persisted transaction: SKIP (DOM ausente)\n";exit;}
require_once __DIR__.'/../src/GuideItemTransaction.php';
function t265tx(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
final class T265Store {
    public $state; private $before; public $commits=0; public $rollbacks=0;
    public function __construct(){ $this->state=['guide'=>['payment'=>'2026-11-03'],'documents'=>[41=>['id'=>41,'number'=>'246583','description'=>'origem']],'items'=>[9=>['id'=>9,'document_id'=>41,'detail'=>'01','product'=>'9','period'=>'0','installment'=>'002','due'=>'2026-11-30','agreement'=>'CONV','recipient'=>'12345678901','extras'=>['additional_71'=>'A','additional_72'=>'B']]],'values'=>[9=>['11'=>'100.00','12'=>'2.00']]]; }
    public function beginTransaction():void{$this->before=unserialize(serialize($this->state));}
    public function commit():void{$this->commits++;}
    public function rollback():void{$this->state=$this->before;$this->rollbacks++;}
}
function t265_save(T265Store $db,array $input,bool $incompatible=false,bool $fail=false):void{
    if($incompatible)throw new RuntimeException('snapshot incompatível: salvar bloqueado');
    (new GnreGuideItemTransaction())->run($db,[
      'document'=>function()use($db,$input){$id=$db->state['items'][9]['document_id'];t265tx(isset($db->state['documents'][$id]),'documento vinculado deve ser atualizado in-place');$db->state['documents'][$id]=array_replace($db->state['documents'][$id],$input['document']??[]);},
      'item'=>function()use($db,$input){$db->state['guide']=array_replace($db->state['guide'],$input['guide']??[]);$db->state['items'][9]=array_replace($db->state['items'][9],$input['item']??[]);},
      'values'=>function()use($db,$input){$db->state['values'][9]=array_replace($db->state['values'][9],$input['values']??[]);},
      'total'=>function()use($fail){if($fail)throw new RuntimeException('falha simulada');}
    ]);
}
$db=new T265Store();$before=unserialize(serialize($db->state));t265_save($db,[]);t265tx($db->state===$before,'salvar sem alterações deve preservar integralmente guia/item/documento/valores/adicionais');t265tx(count($db->state['documents'])===1&&count($db->state['items'])===1&&count($db->state['values'][9])===2,'reabertura/salvamento não pode criar ou duplicar registros');t265tx($db->commits===1,'commit ausente');
$reopened=unserialize(serialize($db->state));t265tx($reopened===$before,'reabrir deve devolver exatamente os valores persistidos');
t265_save($db,['item'=>['agreement'=>'NOVO']]);t265tx($db->state['items'][9]['agreement']==='NOVO'&&$db->state['items'][9]['extras']===$before['items'][9]['extras']&&$db->state['values'][9]===$before['values'][9],'alteração isolada não preservou os demais dados');
$stable=unserialize(serialize($db->state));try{t265_save($db,[],true);throw new RuntimeException('incompatibilidade deveria bloquear');}catch(RuntimeException $e){t265tx($db->state===$stable,'bloqueio incompatível não pode alterar dados');}
try{t265_save($db,['item'=>['agreement'=>'FALHA']],false,true);throw new RuntimeException('falha deveria propagar');}catch(RuntimeException $e){t265tx($db->state===$stable&&$db->rollbacks===1,'rollback deve restaurar o estado completo');}
echo "task26.5 persisted transaction: OK\n";
