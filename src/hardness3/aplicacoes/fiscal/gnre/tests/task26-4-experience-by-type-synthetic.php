<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GuideItemEditPolicy.php';
function t264(bool $ok,string $message):void { if(!$ok) throw new RuntimeException($message); }
$flow=(string)file_get_contents(__DIR__.'/../public/guide-draft-flow.php');
$controller=(string)file_get_contents(__DIR__.'/../src/GuideDraftController.php');
t264(GnreGuideItemEditPolicy::mode('0',0,false,false)==='new','tipo 0 sem item deve abrir somente inclusão');
t264(GnreGuideItemEditPolicy::mode('0',1,false,false)==='summary','tipo 0 salvo deve iniciar no resumo');
t264(GnreGuideItemEditPolicy::mode('0',1,true,false)==='edit','tipo 0 deve editar apenas mediante seleção explícita');
t264(GnreGuideItemEditPolicy::mode('0',2,false,false)==='invalid','tipo 0 não pode aceitar segundo item');
t264(GnreGuideItemEditPolicy::mode('1',1,false,false)==='list'&&GnreGuideItemEditPolicy::mode('1',1,false,true)==='new','tipo 1 deve listar e incluir explicitamente');
t264(GnreGuideItemEditPolicy::mode('2',1,false,false)==='list'&&GnreGuideItemEditPolicy::mode('2',1,false,true)==='new','tipo 2 deve listar e incluir explicitamente');
t264(strpos($flow,'data-guide-draft-item-summary')!==false&&strpos($flow,'Editar item')!==false&&strpos($flow,'Cancelar edição')!==false&&strpos($flow,'Salvar alterações')!==false,'resumo e ações de edição do tipo 0 ausentes');
t264(strpos($flow,'$type!==\'0\'')!==false&&strpos($flow,'Adicionar documento/item')!==false&&strpos($flow,'Adicionar receita')!==false,'ações explícitas dos tipos múltiplos ausentes');
t264(strpos($flow,"&&\$mode==='list'")!==false,'lista não deve acompanhar formulário sem retorno explícito');
t264(strpos($flow,'if($showForm&&$pending)')!==false&&strpos($flow,'guide_draft_discard_pending_item')!==false,'descarte deve depender do POST pendente do formulário ativo');
t264(strpos($controller,"'&new_item=1'")!==false,'POST pendente de novo item deve retornar ao item correspondente');
echo "task26.4 experience by type synthetic: OK\n";
