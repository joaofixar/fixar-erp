<?php
declare(strict_types=1);
require_once __DIR__ . '/ResultInterpreter.php';

/** Seleção pura e conservadora para o reparo manual da regressão Task 74. */
final class GnrePendingResultRegressionRepair
{
    /** @param array<string,mixed> $row */
    public static function affected(array $row): bool
    {
        if (($row['GNRE004_Estado']??'')!=='RECEBIDA'||($row['GNRE001_Status']??'')!=='F'||($row['GNRE005_Situacao']??'')!=='FINALIZADA') return false;
        $parsed=GnreResultInterpreter::interpret((string)($row['GNRE005_Ultima_Consulta_SOAP']??''));
        if (empty($parsed['valid'])||!in_array((string)($parsed['lot']['code']??''),['400','401'],true)) return false;
        foreach((array)($parsed['guides']??[]) as $guide) if(in_array((string)($guide['code']??''),['0','1','2'],true)) return false;
        return true;
    }

    /** Estado a aplicar somente após inspeção humana da seleção SQL documentada. */
    public static function restoredState(): array
    {
        return ['GNRE001_Status'=>'P','GNRE005_Situacao'=>'PENDENTE'];
    }
}
