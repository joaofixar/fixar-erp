<?php
declare(strict_types=1);

function company_switch_assert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$index = (string) file_get_contents(__DIR__ . '/../public/index.php');
$bootstrap = (string) file_get_contents(__DIR__ . '/../bootstrap/app.php');

$start = strpos($index, "if (\$_SERVER['REQUEST_METHOD'] === 'POST' && (string)(\$_POST['action'] ?? '') === 'select_gnre_company') {");
$end = strpos($index, '/* Task 38:', $start === false ? 0 : $start);
$route = $start === false || $end === false ? '' : substr($index, $start, $end - $start);

company_switch_assert($route !== '', 'O POST select_gnre_company deve permanecer definido.');
company_switch_assert(strpos($route, 'gnre_require_csrf();') !== false, 'Troca de empresa deve manter a validação CSRF.');
company_switch_assert(strpos($route, 'gnre_select_company((int)($_POST[\'gnre_company_id\'] ?? 0));') !== false, 'Troca de empresa deve manter a revalidação da empresa autorizada.');
company_switch_assert(strpos($bootstrap, "gnre_abort('A empresa selecionada não está disponível para GNRE.', 403);") !== false, 'Empresa não autorizada deve continuar bloqueada com 403.');
company_switch_assert(strpos($bootstrap, "gnre_abort('Solicitação inválida. Atualize a página e tente novamente.', 403);") !== false, 'CSRF inválido deve continuar bloqueado com 403.');
company_switch_assert(strpos($route, "header('Location: ' . gnre_url('?view=list'), true, 303);") !== false, 'POST válido deve aplicar PRG/303 para a Central de Guias.');
company_switch_assert(strpos($route, 'gnre_url()') === false, 'Redirect da troca não pode reutilizar a URL anterior.');
foreach (['edit', 'tab', 'gnre34', 'status', 'uf', 'revenue', 'due_from', 'due_to', 'guide', 'receipt', 'environment', 'action='] as $forbidden) {
    company_switch_assert(strpos($route, $forbidden) === false, 'Redirect da troca não pode preservar o parâmetro '.$forbidden.'.');
}
foreach (['gnre_db(', 'INSERT ', 'UPDATE ', 'DELETE ', 'GnreReceptionClient', 'GnreResultClient', 'GnreQueueWorker', 'GnreOfficialPdfService'] as $sideEffect) {
    company_switch_assert(strpos($route, $sideEffect) === false, 'Troca de empresa não pode executar efeito em guia: '.$sideEffect.'.');
}

echo "Company switch to Guide Center static checks OK\n";
