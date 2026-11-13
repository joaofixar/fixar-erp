<?php
declare(strict_types=1);
if(!defined('GNRE_SESSION'))define('GNRE_SESSION','gnre_task26_test');
require_once __DIR__.'/../src/GuideDraftController.php';
function t263flash(bool $ok,string $message):void {if(!$ok)throw new RuntimeException($message);}
$context=['company_id'=>7];$_SESSION=[];
$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');t263flash(strpos($controller,"\$redirect=gnre_url('?edit='.\$guideId.'&tab=dados'.(\$itemId?'&item='.\$itemId:'&new_item=1'))")!==false,'falha de edição deve redirecionar para a própria guia e item');
$key=gnre_guide_draft_store_flash('7','guide',21,34,'guide_draft_save_item','Não foi possível gravar o item agora. Seus dados foram mantidos para correção.',true);
t263flash($key==='guide:21:34','chave completa de flash deve conter rota, guia e item');
t263flash(isset($_SESSION[GNRE_SESSION]['guide_draft_flash']['7']['guide:21:34']),'flash de falha deve ficar na empresa/guia/item corretos');
$_SESSION[GNRE_SESSION]['temporary']['guide_draft_form']['7'][21][34]=['receipt'=>'100102','source_document_number'=>'NF preservada'];
gnre_guide_draft_enter_new($context);
$new=gnre_guide_draft_take_flash($context,'new',0,0,['guide_draft_choose_uf','guide_draft_consult','guide_draft_choose_snapshot','guide_draft_restart','guide_draft_create']);
t263flash($new===null,'view=new nunca pode consumir flash de guide_draft_save_item');
t263flash(!isset($_SESSION[GNRE_SESSION]['guide_draft_flash']['7']['guide:21:34']),'entrar em Nova guia deve dispensar somente flash de edição da empresa atual');
t263flash(($_SESSION[GNRE_SESSION]['temporary']['guide_draft_form']['7'][21][34]['source_document_number']??'')==='NF preservada','POST pendente da guia deve sobreviver à navegação para Nova guia');
gnre_guide_draft_store_flash('8','guide',22,35,'guide_draft_save_item','Falha outra empresa',true);t263flash(isset($_SESSION[GNRE_SESSION]['guide_draft_flash']['8']['guide:22:35']),'empresa atual não pode limpar flash de outra empresa');
gnre_guide_draft_store_flash('7','new',0,0,'guide_draft_create','Erro ao criar nova guia.',true);gnre_guide_draft_enter_new($context);$new=gnre_guide_draft_take_flash($context,'new',0,0,['guide_draft_choose_uf','guide_draft_consult','guide_draft_choose_snapshot','guide_draft_restart','guide_draft_create']);t263flash(($new['message']??'')==='Erro ao criar nova guia.','erro de criação deve aparecer exclusivamente em view=new');
$_SESSION[GNRE_SESSION]['guide_draft_flash']=['message'=>'Não foi possível gravar o item agora. Seus dados foram mantidos para correção.','error'=>true];gnre_guide_draft_enter_new($context);t263flash(!isset($_SESSION[GNRE_SESSION]['guide_draft_flash']),'flash legado de edição deve ser descartado com segurança ao entrar em Nova guia');
echo "task26.3 flash scope synthetic: OK\n";
