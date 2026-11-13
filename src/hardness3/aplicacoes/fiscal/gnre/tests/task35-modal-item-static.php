<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/guide-draft-flow.php');
$ui=(string)file_get_contents($root.'/public/gnre-ui.js');
$modalUi=(string)file_get_contents($root.'/public/guide-item-modal.js');
$css=(string)file_get_contents($root.'/public/gnre.css');
$presentation=(string)file_get_contents($root.'/public/task10-presentation.php');
$readonly=(string)file_get_contents($root.'/public/guide-readonly-items.php');
$controller=(string)file_get_contents($root.'/src/GuideDraftController.php');
function t35(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
t35(strpos($flow,'data-guide-item-modal')!==false&&strpos($flow,'data-guide-item-modal-open')!==false,'Abertura de item novo e edição persistida devem oferecer modal.');
t35(strpos($css,'position:fixed;top:50%;left:50%')!==false&&strpos($css,'transform:translate(-50%,-50%)')!==false&&strpos($css,'width:min(1120px,94vw)')!==false&&strpos($css,'@media(max-width:560px)')!==false,'Modal deve ficar centralizado, amplo em desktop e responsivo em mobile pelo CSS do módulo.');
t35(strpos($modalUi,"document.createElement('style')")===false,'Controlador do modal não deve injetar CSS.');
t35(strpos($flow,'guide-item-hydration.js')!==false&&strpos($flow,"'saved'=>\$editingItem")!==false,'Modal deve manter a hidratação dos dados persistidos.');
t35(strpos($flow,'data-guide-item-modal-cancel')!==false&&strpos($flow,'method="post"')!==false,'Cancelar deve ser navegação sem POST e o formulário mantém o POST existente.');
t35(strpos($modalUi,"searchParams.delete('item')")!==false&&strpos($modalUi,"searchParams.delete('new_item')")!==false,'Fechar o modal deve sair do estado de edição/inclusão.');
t35(strpos($modalUi,"searchParams.delete('readonly_item')")!==false,'Fechar o modal de consulta deve sair do estado de visualização.');
t35(strpos($readonly,'Visualizar item')!==false&&strpos($readonly,'data-guide-draft-readonly')!==false,'Guia emitida deve abrir item em modal somente leitura.');
t35(strpos($readonly,'class="button" data-guide-item-modal')!==false&&strpos($readonly,'grid-template-columns:repeat(3,minmax(0,1fr))')!==false,'Consulta deve usar a grade integral do modal e botões no padrão do projeto.');
t35(strpos($ui,'[data-guide-item-modal]')===false&&strpos($ui,'itemModalUrl')===false&&strpos($ui,"searchParams.delete('item_modal')")===false,'gnre-ui não pode controlar o modal de item.');
t35(strpos($modalUi,'[data-guide-item-modal]')!==false&&strpos($modalUi,'dialog.showModal()')!==false&&strpos($modalUi,"searchParams.delete('item_modal')")!==false,'guide-item-modal deve ser o controlador único.');
t35(substr_count($presentation,'guide-item-modal.js')===1&&strpos($flow,'src="guide-item-modal.js"')===false&&strpos($readonly,'src="guide-item-modal.js"')===false,'Controlador do modal deve carregar uma vez na página normal, edição e consulta.');
t35(strpos($controller,"['temporary']['guide_draft_form']")!==false&&strpos($controller,"'&item_modal=1'")!==false,'Falha de validação deve preservar valores e reabrir o modal.');
t35(strpos($flow,'href=')!==false&&strpos($modalUi,'item_modal')!==false&&strpos($modalUi,'window.open')===false,'Links devem continuar como fallback sem JavaScript, sem popup.');
t35(strpos($flow,'código XML')===false&&strpos($flow,'(D020:')===false&&strpos($flow,'city_erp_code')===false,'Código XML/D020 não pode ser exposto visualmente.');
t35(strpos($flow,"['city_name']")!==false&&strpos($flow,"['cnpj']")!==false,'Dados técnicos do emitente devem continuar usados para a apresentação simplificada.');
t35(strpos($ui,"toLocaleString('pt-BR'")!==false&&strpos($ui,"formatDate(value)")!==false,'Resumo deve formatar valor e data visualmente em pt-BR, sem alterar inputs.');
echo "Task 35 modal item static: OK\n";
