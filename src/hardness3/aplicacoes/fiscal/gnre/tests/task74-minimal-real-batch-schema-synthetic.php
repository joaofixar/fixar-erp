<?php
declare(strict_types=1);
require_once __DIR__.'/../src/GnreRealBatchCohort.php';

function t74(bool $condition,string $message):void {if(!$condition)throw new RuntimeException('Task 74: '.$message);}

$schema=(string)file_get_contents(__DIR__.'/../../../../scripts/atualizacaoBase.php');
$service=(string)file_get_contents(__DIR__.'/../src/GnreRealBatchCohort.php');
$documentation=(string)file_get_contents(__DIR__.'/../docs/tasks/74-schema-minimo-lote-real.md');

foreach([
    "GNRE010_Situacao enum('PREPARADO','ENVIANDO','RECEBIDO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO','CANCELADO')",
    'GNRE010_Chave_Idempotencia varchar(64) DEFAULT NULL',
    'GNRE010_XML_SHA256 char(64) DEFAULT NULL',
    'GNRE010_XML_Fisico mediumtext DEFAULT NULL',
    'GNRE010_Data_Hora_Envio datetime DEFAULT NULL',
    'GNRE010_C007_Id_Solicitante_Envio int DEFAULT NULL',
    'GNRE010_HTTP_Recepcao_Status smallint unsigned DEFAULT NULL',
    'GNRE010_SOAP_Recepcao mediumtext DEFAULT NULL',
    'GNRE010_Mensagem_Tecnica text DEFAULT NULL',
    'GNRE010_Recibo varchar(20) DEFAULT NULL',
    'GNRE010_Data_Hora_Recibo varchar(40) DEFAULT NULL',
    'GNRE010_Proxima_Tentativa_Consulta datetime DEFAULT NULL',
    'GNRE010_Consulta_Lock_Token char(64) DEFAULT NULL',
    'GNRE010_Consulta_Lock_Ate datetime DEFAULT NULL',
    'GNRE010_Ultima_Consulta_SOAP mediumtext DEFAULT NULL',
    'GNRE010_Data_Hora_Ultima_Consulta datetime DEFAULT NULL',
    'UNIQUE KEY UQ_GNRE010_Empresa_Idempotencia (GNRE010_C004_Id,GNRE010_Chave_Idempotencia)',
    'GNRE011_Ordinal_Envio smallint unsigned DEFAULT NULL',
    "GNRE011_Situacao enum('PREPARADO','PENDENTE','FINALIZADO','INCONCLUSIVO','REJEITADO') NOT NULL DEFAULT 'PREPARADO'",
    'GNRE011_Situacao_Guia char(1) DEFAULT NULL',
    'GNRE011_Resultado_Individual mediumtext DEFAULT NULL',
    'GNRE011_Motivos_Rejeicao mediumtext DEFAULT NULL',
    'GNRE011_Data_Hora_Decisao datetime DEFAULT NULL',
    'KEY IDX_GNRE011_Lote_Situacao (GNRE011_GNRE010_Id,GNRE011_Situacao)',
    'UNIQUE KEY UQ_GNRE011_Coorte_Guia (GNRE011_GNRE010_Id,GNRE011_GNRE001_Id)'
] as $needle)t74(strpos($schema,$needle)!==false,'schema consolidado ausente: '.$needle);

t74(strpos($schema,'GNRE010_PDF')===false&&strpos($schema,'GNRE011_PDF')===false,'schema não pode introduzir PDF de lote ou participante');
t74(strpos($schema,'GNRE001_GNRE010')===false,'schema não pode introduzir vínculo mutável na guia');
t74(strpos($service,"c.GNRE010_Situacao NOT IN ('FINALIZADO','REJEITADO','CANCELADO')")!==false,'serviço deve bloquear guia em todo lote ativo');
t74(strpos($service,'Ordinal_Envio')===false,'serviço preparatório não pode usar ordinal para correlação');
foreach(['Criar_Campo(\'GNRE010_Chave_Idempotencia\'','Alterar_Tipo_Campo(\'GNRE010_Situacao\'','Criar_Indice(\'GNRE010_C004_Id,,GNRE010_Chave_Idempotencia\', \'UNIQUE\')','Criar_Campo(\'GNRE011_Ordinal_Envio\'','Criar_Indice(\'GNRE011_GNRE010_Id,,GNRE011_Situacao\')'] as $needle)t74(strpos($documentation,$needle)!==false,'rotina manual idempotente ausente: '.$needle);

echo "Task 74 minimal real batch schema synthetic checks OK\n";
