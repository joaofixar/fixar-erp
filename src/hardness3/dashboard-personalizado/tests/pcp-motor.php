<?php
// Validação local do suporte ao PCP; não abre conexão com banco.
namespace hardness;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/custom-indicator-engine.php';

if (dashboardIndicadorValorPadrao(array('padrao' => 'fim_mes')) !== date('Y-m-t')) {
    throw new \RuntimeException('O padrão fim_mes deve retornar o último dia do mês atual.');
}

$consultas = array(
    array(true, 'SELECT op.T255_Id FROM T255 AS op WHERE op.T255_C004_Id = :empresa AND EXISTS (SELECT 1 FROM SMT134 AS capa INNER JOIN SMT130 AS plano ON plano.SMT130_SMT134_Id = capa.SMT134_Id WHERE capa.SMT134_T255_Id = op.T255_Id)'),
    array(true, 'SELECT plano.SMT130_Id FROM `smt130` AS plano INNER JOIN D009 ON D009_C004_Id = :empresa'),
    array(false, 'SELECT SMT130_Id FROM SMT130'),
    array(false, 'SELECT SMT131_Id FROM SMT131 INNER JOIN D009 ON D009_C004_Id = :empresa'),
    array(false, 'DELETE FROM SMT130 WHERE SMT130_Id = :empresa'),
    array(true, 'SELECT T255_Id FROM T255 WHERE T255_C004_Id = :empresa AND EXISTS (SELECT 1 FROM SMT134 WHERE SMT134_T255_Id = T255_Id)'),
    array(true, 'SELECT SMT029_Id FROM SMT029 INNER JOIN D001A ON D001A_SMT029_Id = SMT029_Id INNER JOIN D009 ON D009_D001_Id = D001A_D001_Id WHERE D009_C004_Id = :empresa'),
    array(true, 'SELECT SMT029_Id FROM smt029 INNER JOIN D009 ON D009_C004_Id = :empresa'),
    array(false, 'SELECT SMT030_Id FROM SMT030 INNER JOIN D009 ON D009_C004_Id = :empresa'),
    array(false, 'SELECT SMT029_Id FROM SMT029'),
    array(false, 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES INNER JOIN D009 ON D009_C004_Id = :empresa'),
    array(false, 'SELECT SMT029_Id FROM SMT029 INNER JOIN D009 ON D009_C004_Id = :empresa; DELETE FROM SMT029'),
);

foreach ($consultas as $caso) {
    $erro = '';
    if (dashboardIndicadorValidarSql($caso[1], $erro) !== $caso[0]) {
        throw new \RuntimeException('Resultado inesperado na validação SQL: ' . $erro);
    }
}

echo "OK: fim_mes, leitura de SMT029/SMT130 e bloqueios de empresa, tabelas e comandos.\n";
