<?php
declare(strict_types=1);

function task30_assert(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$home = (string) file_get_contents(__DIR__ . '/../../../../prog/etc/etc011/outros.php');
$bootstrap = (string) file_get_contents(__DIR__ . '/../bootstrap/app.php');
$module = (string) file_get_contents(__DIR__ . '/../config/module.php');

task30_assert(strpos($home, '$exibirAtalhoGnre = !empty($g[\'c029Ids\'][310]);') !== false, 'atalho deve usar a permissão C029 310 já carregada no ERP.');
task30_assert(strpos($home, 'atalho-gnre') !== false && strpos($home, 'Guias estaduais') !== false, 'card GNRE deve usar o padrão de atalhos da tela inicial.');
task30_assert(strpos($home, '?view=list&amp;gnre_company=') !== false, 'atalho deve abrir a Central de Guias com a empresa ativa do ERP como sugestão inicial.');
task30_assert(strpos($bootstrap, "filter_input(INPUT_GET, 'gnre_company'") !== false, 'bootstrap deve receber a sugestão de empresa no servidor.');
task30_assert(strpos($bootstrap, "foreach (\$companies as \$company) if (\$company['company_id'] === \$selected)") !== false, 'empresa sugerida deve ser comparada à lista autorizada pelo servidor.');
task30_assert(strpos($bootstrap, "gnre_abort('A empresa informada não está disponível para GNRE.', 403)") !== false, 'empresa não autorizada na URL deve ser negada.');
task30_assert(strpos($bootstrap, 'C030.C030_C029_Id=? AND C030.C030_Acesso_Liberado=\'S\'') !== false, 'acesso direto deve continuar revalidando C030.');
task30_assert(strpos($module, "'permission_access' => 310") !== false, 'módulo deve reutilizar a permissão GNRE C029 310.');

echo "task30 home access: OK\n";
