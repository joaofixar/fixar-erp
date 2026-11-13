<?php
declare(strict_types=1);

function task10_assert(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }

$bootstrap = (string) file_get_contents(__DIR__ . '/../bootstrap/app.php');
$page = (string) file_get_contents(__DIR__ . '/../public/index.php');
$presentation = (string) file_get_contents(__DIR__ . '/../public/task10-presentation.php');
$manual = (string) file_get_contents(__DIR__ . '/../public/task10-manual-flow.php');
$ui = (string) file_get_contents(__DIR__ . '/../public/gnre-ui.js');

task10_assert(strpos($bootstrap, "['Empresa_Atual']") !== false, 'ERP company must remain only an initial source.');
task10_assert(strpos($bootstrap, "['company_id']") !== false && strpos($bootstrap, 'gnre_select_company') !== false, 'GNRE must own a company context and explicit selector.');
task10_assert(strpos($bootstrap, 'gnre_authorized_companies') !== false && strpos($bootstrap, "C030_Acesso_Liberado='S'") !== false, 'Company list must be permission-scoped.');
task10_assert(strpos($bootstrap, 'gnre_clear_company_state') !== false && strpos($bootstrap, "'config_uf'") !== false, 'Company switch must clear UF configuration state.');
task10_assert(strpos($presentation, 'select_gnre_company') !== false && strpos($presentation, 'Empresa selecionada no GNRE') !== false, 'Page must expose GNRE-only company switch.');
task10_assert(strpos($page, "'EMITIDA'") !== false && strpos($page, 'GnreResultInterpreter::interpret') !== false, 'Business status must use persisted individual SOAP interpretation.');
task10_assert(strpos($ui, 'edição de rascunho não salva') !== false, 'Switch must warn about unsaved drafts.');
task10_assert(strpos($page, 'register_shutdown_function') === false, 'Technical result cannot be deferred to shutdown.');
task10_assert(strpos($presentation, 'name="due" value="<?=gnre_h($dueFilter)?>"') !== false, 'Due date filter must close its HTML attribute.');
task10_assert(strpos($presentation, 'Resultado individual persistido') !== false && strpos($presentation, 'Motivos de rejeição') !== false, 'Individual result and rejection details must remain in main flow.');
task10_assert(strpos($presentation, 'Configuração oficial de') !== false && strpos($presentation, 'Receita padrão') !== false, 'Guide form must use an official UF configuration.');
task10_assert(strpos($page, 'não está habilitado no Portal GNRE de homologação') !== false, 'Return 102 requires a comprehensible message.');
task10_assert(strpos($presentation, 'task10-manual-flow.php') !== false, 'Presentation must include the complete manual flow.');
foreach (['add_document','delete_document','save_item','delete_item','generate_xml'] as $action) task10_assert(strpos($manual, $action) !== false, 'Manual flow missing '.$action.'.');
foreach (['task09-progressive.js','gnreTask09Rules','gnreTask09Emitter'] as $feature) task10_assert(strpos($presentation, $feature) !== false, 'Task 09 integration missing '.$feature.'.');
task10_assert(strpos($page, '$form=') < strpos($page, "require __DIR__ . '/task10-presentation.php'") && strpos($page, '$item=') < strpos($page, "require __DIR__ . '/task10-presentation.php'"), 'Endpoint must prepare form and item before rendering.');
task10_assert(strpos($presentation, 'method="get"') !== false && strpos($presentation, '?view=list&amp;status=RASCUNHO') === false, 'Listing navigation must remain GET links, not a create submit.');
task10_assert(strpos($page, "elseif(\$action==='create'||\$action==='update')") !== false, 'Only explicit create/update may validate guide fields.');
task10_assert(strpos($page, 'Ação inválida.') !== false, 'Unknown POST action must be rejected explicitly.');
echo "Task 10 company context synthetic checks OK\n";
