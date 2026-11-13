<?php
declare(strict_types=1);
function task11_assert(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
$page=(string)file_get_contents(__DIR__.'/../public/index.php');
$presentation=(string)file_get_contents(__DIR__.'/../public/task10-presentation.php');
$ui=(string)file_get_contents(__DIR__.'/../public/gnre-ui.js');
$manual=(string)file_get_contents(__DIR__.'/../public/task10-manual-flow.php');
task11_assert(strpos($page, "action==='start_new_guide'")!==false, 'Continue must be explicit.');
task11_assert(strpos($page, 'gnre_rules($context,$uf)')!==false && strpos($page, 'GnreConfigUfClient())->consult')!==false, 'Continue must reuse session before client.');
task11_assert(strpos($page, 'não está habilitado no Portal GNRE de homologação')!==false, 'Return 102 needs message.');
task11_assert(strpos($page, 'gnre_set_new_guide_state')!==false && strpos($page, 'gnre_clear_new_guide_state')!==false, 'State must be company-local.');
task11_assert(strpos($presentation, '1. UF')!==false && strpos($presentation, '2. Receita e dados da guia')!==false, 'Step indicator missing.');
task11_assert(strpos($presentation, 'data-gnre-start-guide')!==false && strpos($presentation, 'Continuar')!==false, 'Initial continuation missing.');
task11_assert(strpos($presentation, 'Receita padrão')!==false && strpos($presentation, 'Gravar rascunho')!==false, 'Step two missing.');
task11_assert(strpos($presentation, 'Requisitos da UF')===false, 'Normal flow duplicates requirements.');
task11_assert(strpos($page, '$formError')!==false && strpos($presentation, 'role="alert"')!==false, 'Form errors must use GNRE layout.');
task11_assert(strpos($ui, "flag === 'N'")!==false && strpos($ui, 'detail.value')!==false, 'Revenue change must clear dependent values.');
task11_assert(strpos($presentation, 'configuração oficial não carregada nesta sessão')!==false, 'Persisted guide receipt must remain visible without session configuration.');
task11_assert(strpos($presentation, 'Carregar/recarregar requisitos da UF')!==false && strpos($page, "action==='reload_guide_config'")!==false, 'Configuration reload must be explicit.');
task11_assert(strpos($page, "action==='update'")!==false && strpos($page, 'Carregue a configuração oficial desta UF antes de gravar a guia.')!==false, 'Server must block guide changes without rules.');
task11_assert(strpos($page, 'item_config_missing')!==false && strpos($ui, 'input[name="action"][value="save_item"]')!==false, 'Existing items must be visible but protected when rules are absent.');
foreach(['add_document','save_item','generate_xml'] as $action) task11_assert(strpos($manual,$action)!==false, 'Manual flow missing '.$action.'.');
echo "Task 11 guided manual flow synthetic checks OK\n";
