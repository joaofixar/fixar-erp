<?php
declare(strict_types=1);

function t51(bool $condition,string $message):void{if(!$condition)throw new RuntimeException('Task 51: '.$message);}

$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/guide-draft-flow.php');
$modal=(string)file_get_contents($root.'/public/guide-item-modal.js');

t51(strpos($flow,'$backgroundMode=$itemModal?GnreGuideItemEditPolicy::mode($type,count($items),false,false):$mode')!==false,'O estado de fundo deve ignorar a seleção do formulário modal.');
t51(strpos($flow,'$formMode=$mode;if($itemModal)$mode=$backgroundMode')!==false&&strpos($flow,'data-guide-draft-item-summary')!==false,'O resumo simples deve continuar sendo renderizado sob o modal.');
t51(strpos($flow,'$mode=$backgroundMode;if($showForm&&$pending)')!==false&&strpos($flow,'data-guide-draft-item-list')!==false,'A lista de tipos múltiplos deve continuar sendo renderizada sob o modal.');
t51(strpos($flow,'Documentos e itens gravados')!==false&&strpos($flow,'Receitas e itens gravados')!==false&&strpos($flow,'Adicionar documento/item')!==false&&strpos($flow,'Adicionar receita')!==false,'As tabelas e ações de inclusão dos tipos múltiplos devem permanecer no conteúdo de fundo.');
t51(strpos($flow,'data-guide-item-modal-open')!==false&&strpos($flow,'data-guide-draft-item id="guide-draft-item-form"')!==false,'O formulário de edição/inclusão deve continuar sendo renderizado no diálogo modal.');
t51(strpos($flow,'&new_item=1')!==false&&strpos($flow,'data-guide-item-modal')!==false,'O href de fallback sem JavaScript para inclusão múltipla deve permanecer inalterado.');

$closeStart=strpos($modal,'function close()');
$close=(string)substr($modal,$closeStart,220);
t51(strpos($close,'window.history.replaceState')!==false&&strpos($close,'window.location.assign')===false,'X e Cancelar devem fechar sem navegação e atualizar somente o histórico.');
t51(strpos($modal,"searchParams.delete('item_modal')")!==false&&strpos($modal,"searchParams.delete('item')")!==false&&strpos($modal,"searchParams.delete('new_item')")!==false&&strpos($modal,"searchParams.delete('readonly_item')")!==false,'O fechamento deve remover exclusivamente os parâmetros de estado do modal.');
t51(strpos($modal,"dialog.addEventListener('cancel',function(event){event.preventDefault();});")!==false,'Esc não deve fechar o modal silenciosamente.');
t51(strpos($modal,"event.target===dialog")===false,'Clique no backdrop não deve fechar o modal.');

echo "Task 51 modal background: OK\n";
