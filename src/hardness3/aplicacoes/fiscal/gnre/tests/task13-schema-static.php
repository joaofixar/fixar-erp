<?php
/* Validação estática do contrato DDL da Task 13; não acessa banco. */
$root = dirname(__DIR__, 4);
$schema = file_get_contents($root . '/scripts/atualizacaoBase.php');
if ($schema === false) {
    fwrite(STDERR, "Não foi possível ler scripts/atualizacaoBase.php.\n");
    exit(1);
}

function task13_table($schema, $table) {
    $pattern = '/CREATE TABLE IF NOT EXISTS ' . preg_quote($table, '/') . ' \\((.*?)\\) ENGINE=InnoDB/s';
    if (!preg_match($pattern, $schema, $matches)) {
        fwrite(STDERR, "CREATE TABLE de {$table} não encontrado.\n");
        exit(1);
    }
    return $matches[1];
}

function task13_require($condition, $message) {
    if (!$condition) {
        fwrite(STDERR, "Falha: {$message}\n");
        exit(1);
    }
}

$gnre001 = task13_table($schema, 'GNRE001');
$gnre003 = task13_table($schema, 'GNRE003');
$gnre006 = task13_table($schema, 'GNRE006');

task13_require((bool) preg_match('/GNRE001_Tipo_GNRE char\\(1\\) DEFAULT NULL/', $gnre001), 'GNRE001_Tipo_GNRE nullable ausente ou fora do contrato.');
task13_require((bool) preg_match('/GNRE001_Data_Pagamento date DEFAULT NULL/', $gnre001), 'GNRE001_Data_Pagamento nullable ausente ou fora do contrato.');
task13_require((bool) preg_match('/GNRE003_Data_Vencimento date DEFAULT NULL/', $gnre003), 'GNRE003_Data_Vencimento nullable ausente ou fora do contrato.');
task13_require((bool) preg_match('/GNRE003_Convenio varchar\\(30\\) DEFAULT NULL/', $gnre003), 'GNRE003_Convenio deve respeitar o máximo 30 do XSD.');

foreach (array('GNRE006_Id', 'GNRE006_GNRE003_Id int unsigned NOT NULL', 'GNRE006_Tipo_Valor char(2) NOT NULL', 'GNRE006_Valor decimal(15,2) NOT NULL', 'IDX_GNRE006_GNRE003_Id') as $required) {
    task13_require(strpos($gnre006, $required) !== false, "GNRE006 não contém {$required}.");
}

foreach (array('GNRE001_Codigo_Receita', 'GNRE001_Detalhamento_Receita', 'GNRE001_Data_Vencimento', 'GNRE001_Valor', 'GNRE001_Referencia') as $legacy) {
    task13_require(strpos($gnre001, $legacy) !== false, "Campo legado {$legacy} não foi preservado em GNRE001.");
}

$gnreStart = strpos($schema, '/* INÍCIO PROJETO GNRE');
$gnreEnd = strpos($schema, '/* FIM PROJETO GNRE */');
task13_require($gnreStart !== false && $gnreEnd !== false && $gnreEnd > $gnreStart, 'Bloco GNRE não localizado.');
$gnreBlock = substr($schema, $gnreStart, $gnreEnd - $gnreStart);
task13_require(!preg_match('/\\b(?:Criar_Campo|ALTER\\s+TABLE|INSERT\\s+INTO|UPDATE\\s+GNRE|backfill)\\b/i', $gnreBlock), 'O bloco GNRE não pode introduzir migration operacional, ALTER, INSERT ou backfill.');

echo "Task 13 schema static validation: OK\n";
