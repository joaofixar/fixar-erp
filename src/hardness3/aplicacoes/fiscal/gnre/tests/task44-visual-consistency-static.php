<?php
declare(strict_types=1);
$root=dirname(__DIR__);
$flow=(string)file_get_contents($root.'/public/guide-draft-flow.php');
$modal=(string)file_get_contents($root.'/public/guide-item-modal.js');
$css=(string)file_get_contents($root.'/public/gnre.css');
$readonly=(string)file_get_contents($root.'/public/guide-readonly-items.php');
$controller=(string)file_get_contents($root.'/src/GuideDraftController.php');
function t44(bool $ok,string $message):void {if(!$ok)throw new RuntimeException('Task 44: '.$message);}

t44(strpos($flow,'<th>Vencimento</th>')!==false,'A lista de itens múltiplos deve conter a coluna Vencimento.');
t44(strpos($flow,"GNRE003_Data_Vencimento']??''")!==false&&strpos($flow,'dd/mm')===false,'A tela deve obter o vencimento persistido sem alterar o seu valor técnico.');
t44(strpos($flow,"number_format((float)\$value,2,',','.')")!==false&&strpos($flow,"'R$ '")!==false&&strpos($flow,"formatDueDate")!==false&&strpos($flow,"format('d/m/Y')")!==false,'Valor e vencimento devem ser apenas formatados visualmente em pt-BR.');
t44(strpos($flow,"return '—'")!==false,'Vencimento vazio deve usar travessão.');
t44(strpos($flow,'name="due_date"')===false&&strpos($flow,'name="item_amount"')===false,'A lista não deve introduzir inputs ou POST para valor e vencimento.');
t44(strpos($controller,'GNRE003_Valor')!==false&&strpos($controller,'GNRE003_Data_Vencimento')!==false,'Persistência existente de valor e vencimento deve permanecer no controlador.');

t44(strpos($flow,"\$singleItemEmpty=\$type==='0'&&!\$items&&!isset(\$_GET['new_item'])")!==false,'Tipo único vazio deve separar estado vazio da abertura do formulário.');
t44(strpos($flow,'data-guide-draft-item-empty')!==false&&strpos($flow,'>Adicionar item</a>')!==false,'Tipo único vazio deve apresentar a ação Adicionar item.');
t44(strpos($css,'.guide-draft-item-list .form-actions .button,.guide-draft-item-empty .form-actions .button')!==false&&strpos($css,'background:var(--blue)!important')!==false,'Ações de inclusão devem seguir o padrão visual de botão.');
t44(strpos($flow,"'&new_item=1'")!==false&&strpos($flow,'data-guide-item-modal')!==false,'Ação de inclusão deve ter link normal e usar o mesmo controlador de modal quando há JavaScript.');
t44(strpos($modal,'modalUrl')!==false&&strpos($modal,"searchParams.set('item_modal','1')")!==false&&strpos($modal,'window.open')===false,'Modal deve ser aberto pelo controlador existente, sem popup.');
t44(strpos($flow,"\$showForm=(\$mode==='new'||\$mode==='edit')&&!\$singleItemEmpty")!==false&&strpos($flow,'guide-item-hydration.js')!==false,'Inclusão solicitada deve reutilizar formulário e hidratação existentes.');
t44(substr_count($flow,'data-guide-item-modal')>=3&&strpos($modal,"'[data-guide-item-modal]'")!==false,'Edições de item único e múltiplo devem usar o mesmo controlador.');

t44(strpos($flow,'<dl class="guide-draft-emitter">')!==false&&strpos($readonly,'<dl class="guide-draft-emitter">')!==false,'Emitente deve ter a mesma estrutura no modal/formulário normal e na consulta fallback.');
t44(strpos($css,'.guide-draft-emitter{display:grid;grid-template-columns:max-content minmax(0,1fr)')!==false&&strpos($css,'.guide-draft-emitter dd{min-width:0;margin:0;overflow-wrap:anywhere}')!==false,'Emitente deve usar grade de rótulo e valor no CSS próprio do módulo.');
t44(strpos($css,'.guide-draft-grid [data-guide-draft-emitter]{order:-1}')!==false,'Emitente deve aparecer antes dos grupos editáveis do formulário.');
t44(strpos($flow,'código XML')===false&&strpos($flow,'D020')===false&&strpos($flow,'city_erp_code')===false,'Apresentação do emitente não pode expor dados técnicos.');
t44(strpos($modal,"document.createElement('style')")===false&&strpos($flow,'ob_start')===false&&strpos($flow,'preg_replace')===false,'Ajuste visual não deve injetar CSS, usar ob_start ou transformar HTML por regex.');
echo "Task 44 visual consistency: OK\n";
