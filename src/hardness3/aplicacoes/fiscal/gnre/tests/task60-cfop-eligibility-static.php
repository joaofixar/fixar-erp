<?php
declare(strict_types=1);

function t60(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Task 60: '.$message);
    }
}

$root = dirname(__DIR__, 4);
$schema = (string) file_get_contents($root.'/scripts/atualizacaoBase.php');
$grid = (string) file_get_contents($root.'/prog/fis/fis001/grid_cfop.php');
$form = (string) file_get_contents($root.'/prog/fis/fis001/form.php');
$config = (string) file_get_contents($root.'/configuracoes/D006.php');
$restrictions = (string) file_get_contents($root.'/configuracoes/_colunaEditarRestricoes.php');

foreach (array('D006_Flag_Gera_GNRE_ST', 'D006_Flag_Gera_GNRE_DIFAL', 'D006_Flag_Gera_GNRE_FCP') as $field) {
    t60(strpos($schema, 'Criar_Campo("'.$field.'", "char(1) NOT NULL DEFAULT \'N\'");') !== false,
        $field.' deve estar no schema distribuído, com padrão seguro N.');
    t60(strpos($grid, '"'.$field.'"') !== false,
        $field.' deve ser apresentado no cadastro de CFOP.');
    t60(strpos($config, "['".$field."']") !== false && strpos($config, "in_array(\$valor, array('S', 'N'), true)") !== false,
        $field.' deve aceitar somente S ou N na gravação.');
    t60(strpos($restrictions, "'".$field."'") !== false,
        $field.' deve permanecer protegido contra edição genérica fora do cadastro de CFOP.');
}

foreach (array('ST', 'DIFAL', 'FCP') as $kind) {
    t60(strpos($grid, 'Gera GNRE<br>'.$kind) !== false || strpos($form, 'Gera GNRE '.$kind) !== false,
        'A tela de CFOP deve identificar claramente a elegibilidade GNRE de '.$kind.'.');
}
t60(strpos($grid, "array('title' => 'Não', 'value' => 'N'") !== false || strpos($form, "array('title' => 'NÃO', 'value' => 'N'") !== false,
    'A tela de CFOP deve oferecer Não como escolha segura.');
t60(strpos($grid, "array('title' => 'Sim', 'value' => 'S')") !== false || strpos($form, "array('title' => 'SIM', 'value' => 'S')") !== false,
    'A tela de CFOP deve oferecer Sim como escolha explícita.');

t60(strpos($schema, 'GNRE002_T007_Id int unsigned DEFAULT NULL') !== false
    && strpos($schema, 'GNRE002_Tributo_Origem varchar(10) DEFAULT NULL') !== false
    && strpos($schema, 'UNIQUE KEY UQ_GNRE002_NF_TRIBUTO (GNRE002_T007_Id,GNRE002_Tributo_Origem)') !== false,
    'Escopo 3 deve distribuir o vínculo único composto NF + tributo em GNRE002.');
t60(strpos($grid, 'GNRE001') === false && strpos($grid, 'GNRE002') === false,
    'Escopo 1 não pode acoplar o cadastro de CFOP ao módulo GNRE.');

echo "Task 60 CFOP eligibility static: OK\n";
