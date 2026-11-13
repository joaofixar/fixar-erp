<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if (PHP_SAPI !== 'cli') exit("Somente CLI.\n");

function gnre_reparo_valor(DOMXPath $xpath, DOMElement $root, string $path): string {
    return trim((string) $xpath->evaluate('string(' . $path . ')', $root));
}

$db = gnre_connect();
if (!$db) exit("Base indisponível.\n");
$result = mysqli_query($db, "SELECT GNRE004_Id,GNRE004_Resposta_SOAP FROM GNRE004 WHERE GNRE004_Resposta_SOAP LIKE '%--- CONSULTA GNRE RESULTADO %'");
if (!$result) { mysqli_close($db); exit("Não foi possível localizar reparos pendentes.\n"); }
$repaired = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $marker = strpos($row['GNRE004_Resposta_SOAP'], '--- CONSULTA GNRE RESULTADO ');
    if ($marker === false) continue;
    $receptionSoap = rtrim(substr($row['GNRE004_Resposta_SOAP'], 0, $marker));
    $legacy = substr($row['GNRE004_Resposta_SOAP'], $marker);
    if (!preg_match('/HTTP\s+(\d+).*?---\s*\n(.*)$/s', $legacy, $parts)) continue;
    $consultHttp = (int) $parts[1]; $consultSoap = $parts[2];

    $reception = new DOMDocument(); $consult = new DOMDocument();
    if (!@$reception->loadXML($receptionSoap, LIBXML_NONET) || !@$consult->loadXML($consultSoap, LIBXML_NONET)) continue;
    $rx = new DOMXPath($reception); $cx = new DOMXPath($consult);
    $ret = $rx->query('//*[local-name()="TRetLote_GNRE"]')->item(0);
    $resultLote = $cx->query('//*[local-name()="TResultLote_GNRE"]')->item(0);
    if (!$ret instanceof DOMElement || !$resultLote instanceof DOMElement) continue;
    $receptionCode = gnre_reparo_valor($rx, $ret, './*[local-name()="situacaoRecepcao"]/*[local-name()="codigo"]');
    $receptionDescription = gnre_reparo_valor($rx, $ret, './*[local-name()="situacaoRecepcao"]/*[local-name()="descricao"]');
    $consultCode = gnre_reparo_valor($cx, $resultLote, './*[local-name()="situacaoProcess"]/*[local-name()="codigo"]');
    $consultDescription = gnre_reparo_valor($cx, $resultLote, './*[local-name()="situacaoProcess"]/*[local-name()="descricao"]');

    if (!mysqli_begin_transaction($db)) continue;
    try {
        $s = mysqli_prepare($db, 'UPDATE GNRE004 SET GNRE004_Codigo_Retorno=?,GNRE004_Descricao_Retorno=?,GNRE004_Resposta_SOAP=? WHERE GNRE004_Id=? AND GNRE004_Resposta_SOAP LIKE ?');
        if (!$s) throw new RuntimeException('Prepare da recepção falhou.');
        $like = '%--- CONSULTA GNRE RESULTADO %'; mysqli_stmt_bind_param($s, 'sssis', $receptionCode, $receptionDescription, $receptionSoap, $row['GNRE004_Id'], $like);
        if (!mysqli_stmt_execute($s) || mysqli_stmt_affected_rows($s) !== 1) { mysqli_stmt_close($s); throw new RuntimeException('Restauração da recepção falhou.'); }
        mysqli_stmt_close($s);
        $s = mysqli_prepare($db, "INSERT IGNORE INTO GNRE005 (GNRE005_GNRE004_Id,GNRE005_Recibo,GNRE005_Situacao,GNRE005_Proxima_Tentativa,GNRE005_Data_Hora_Processamento,GNRE005_Ultima_Consulta_HTTP,GNRE005_Ultima_Consulta_Codigo,GNRE005_Ultima_Consulta_Descricao,GNRE005_Ultima_Consulta_SOAP,GNRE005_Data_Hora_Ultima_Consulta,GNRE005_Data_Hora_Criacao) SELECT GNRE004_Id,GNRE004_Recibo,'FINALIZADA',NOW(),NOW(),?,?,?,?,NOW(),NOW() FROM GNRE004 WHERE GNRE004_Id=? AND GNRE004_Recibo IS NOT NULL AND GNRE004_Recibo<>''");
        if (!$s) throw new RuntimeException('Prepare da fila mínima falhou.');
        mysqli_stmt_bind_param($s, 'isssi', $consultHttp, $consultCode, $consultDescription, $consultSoap, $row['GNRE004_Id']);
        if (!mysqli_stmt_execute($s)) { mysqli_stmt_close($s); throw new RuntimeException('Criação da fila mínima falhou.'); }
        mysqli_stmt_close($s);
        $s = mysqli_prepare($db, "UPDATE GNRE005 SET GNRE005_Ultima_Consulta_HTTP=?,GNRE005_Ultima_Consulta_Codigo=?,GNRE005_Ultima_Consulta_Descricao=?,GNRE005_Ultima_Consulta_SOAP=?,GNRE005_Data_Hora_Ultima_Consulta=NOW() WHERE GNRE005_GNRE004_Id=? AND (GNRE005_Ultima_Consulta_SOAP IS NULL OR GNRE005_Ultima_Consulta_SOAP='')");
        if (!$s) throw new RuntimeException('Prepare da consulta falhou.');
        mysqli_stmt_bind_param($s, 'isssi', $consultHttp, $consultCode, $consultDescription, $consultSoap, $row['GNRE004_Id']);
        if (!mysqli_stmt_execute($s)) { mysqli_stmt_close($s); throw new RuntimeException('Gravação da consulta falhou.'); }
        mysqli_stmt_close($s);
        mysqli_commit($db); $repaired++;
    } catch (Throwable $e) { mysqli_rollback($db); fwrite(STDERR, 'GNRE004 #' . $row['GNRE004_Id'] . ': ' . $e->getMessage() . "\n"); }
}
mysqli_close($db);
echo "Reparados: $repaired\n";
