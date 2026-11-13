<?php
declare(strict_types=1);
/** Reparo pontual/idempotente de guia finalizada antes do enum aceitar F. */
require_once __DIR__ . '/../bootstrap/app.php';
if (PHP_SAPI !== 'cli') exit("Somente CLI.\n");

$db = gnre_connect();
if (!$db) exit("Base indisponível.\n");
$sql = "SELECT DISTINCT g.GNRE001_Id,g.GNRE001_C004_Id
        FROM GNRE001 g
        JOIN GNRE004 t ON t.GNRE004_GNRE001_Id=g.GNRE001_Id AND t.GNRE004_Estado='RECEBIDA'
        JOIN GNRE005 q ON q.GNRE005_GNRE004_Id=t.GNRE004_Id AND q.GNRE005_Recibo=t.GNRE004_Recibo
        WHERE g.GNRE001_Status='' AND q.GNRE005_Situacao='FINALIZADA'";
$result = mysqli_query($db, $sql);
if (!$result) { mysqli_close($db); exit("Não foi possível localizar guias para reparo.\n"); }
$repaired = 0;
while ($row = mysqli_fetch_assoc($result)) {
    if (!mysqli_begin_transaction($db)) { fwrite(STDERR, "Guia #{$row['GNRE001_Id']}: transação indisponível.\n"); continue; }
    try {
        $s = mysqli_prepare($db, "UPDATE GNRE001 SET GNRE001_Status='F' WHERE GNRE001_Id=? AND GNRE001_C004_Id=? AND GNRE001_Status=''");
        if (!$s) throw new RuntimeException('Prepare falhou.');
        mysqli_stmt_bind_param($s, 'ii', $row['GNRE001_Id'], $row['GNRE001_C004_Id']);
        $ok = mysqli_stmt_execute($s); $changed = mysqli_stmt_affected_rows($s); mysqli_stmt_close($s);
        if (!$ok || $changed !== 1) throw new RuntimeException('A guia não estava no estado vazio esperado.');
        if (!mysqli_commit($db)) throw new RuntimeException('Commit falhou.');
        $repaired++;
    } catch (Throwable $e) { mysqli_rollback($db); fwrite(STDERR, "Guia #{$row['GNRE001_Id']}: {$e->getMessage()}\n"); }
}
mysqli_close($db);
echo "Guias reparadas: $repaired\n";
