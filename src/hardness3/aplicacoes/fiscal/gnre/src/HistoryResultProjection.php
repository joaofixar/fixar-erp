<?php
declare(strict_types=1);

/** Projects only persisted final SOAP results onto their owning attempt. */
final class GnreHistoryResultProjection
{
    /** @return array<int,array<string,mixed>> keyed by GNRE004_Id */
    public static function byAttempt(array $queues): array
    {
        $out=[];
        foreach ($queues as $queue) {
            if ((string)($queue['GNRE005_Situacao'] ?? '') !== 'FINALIZADA') continue;
            $attemptId=(int)($queue['GNRE005_GNRE004_Id'] ?? 0);
            if (!$attemptId || isset($out[$attemptId])) continue;
            $parsed=GnreResultInterpreter::interpret((string)($queue['GNRE005_Ultima_Consulta_SOAP'] ?? ''));
            if ($parsed['valid'] && !empty($parsed['guides'])) $out[$attemptId]=$parsed;
        }
        return $out;
    }
}
