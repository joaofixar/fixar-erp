<?php
declare(strict_types=1);
require_once __DIR__ . '/PendingResultPolicy.php';

/** Presentation rules for the manual result consultation.  No I/O by design. */
final class GnreManualConsultFeedback
{
    public static function brazilianDateTime(string $value): string
    {
        try { return (new DateTimeImmutable($value, new DateTimeZone('America/Sao_Paulo')))->format('d/m/Y \\à\\s H:i:s'); }
        catch (Throwable $e) { return $value; }
    }

    public static function message(string $outcome): string
    {
        $messages=['pending'=>GnrePendingResultPolicy::publicMessage(),'final'=>'A consulta foi concluída e o resultado final foi gravado. A emissão depende exclusivamente do resultado individual exibido abaixo.','retry'=>'A consulta teve uma falha técnica e foi reagendada. Aguarde a próxima tentativa registrada.','processing'=>'Já existe uma consulta em andamento para esta guia. Aguarde a conclusão do processamento.','rescheduled'=>GnrePendingResultPolicy::publicMessage(),'no_receipt'=>'Esta guia não possui recibo confirmado para consulta.','blocked'=>'A consulta está bloqueada pelo estado técnico atual da guia.','not_found'=>'A guia não foi encontrada para a empresa ativa.','exception'=>'Não foi possível concluir a consulta agora. Nenhuma retransmissão foi realizada; verifique o estado técnico registrado e tente novamente no horário permitido.'];
        return $messages[$outcome]??'A consulta foi recebida. Verifique o estado técnico registrado abaixo.';
    }

    /** O flash nunca pode contradizer a releitura persistida após o redirect. */
    public static function persistedOutcome(string $outcome, ?array $guide, ?array $queue, int $now): string
    {
        if ($outcome === 'exception') return 'exception';
        if (!$guide) return 'not_found';
        if (!$queue) return 'no_receipt';
        $state=(string)($queue['GNRE005_Situacao']??'');
        if ($state === 'FINALIZADA' && (string)($guide['GNRE001_Status']??'') === 'F') return 'final';
        if ($state === 'BLOQUEADA') return 'blocked';
        if ($state === 'PROCESSANDO') return 'processing';
        if ($state === 'PENDENTE') {
            if ($outcome === 'retry') return 'retry';
            $next=strtotime((string)($queue['GNRE005_Proxima_Tentativa']??''));
            return $next !== false && $next > $now ? 'rescheduled' : 'pending';
        }
        return 'blocked';
    }

    /** @return array{available:bool,reason:string} */
    public static function action(array $guide, ?array $queue, int $now): array
    {
        if ((string)($guide['GNRE001_Status'] ?? '') !== 'P') {
            return ['available'=>false, 'reason'=>(string)($guide['GNRE001_Status'] ?? '') === 'F' ? 'Resultado final já registrado.' : 'A consulta não é permitida no estado técnico atual.'];
        }
        if (!$queue) return ['available'=>false, 'reason'=>'Disponível após o recebimento do protocolo.'];
        $state=(string)($queue['GNRE005_Situacao'] ?? '');
        if ($state === 'BLOQUEADA') return ['available'=>false, 'reason'=>'A consulta está bloqueada pela fila técnica.'];
        if ($state === 'PROCESSANDO') return ['available'=>false, 'reason'=>'Consulta já em andamento para esta guia.'];
        if ($state === 'FINALIZADA') return ['available'=>false, 'reason'=>'Resultado final já registrado.'];
        if ($state !== 'PENDENTE') return ['available'=>false, 'reason'=>'A consulta não está disponível no estado técnico atual.'];
        $next=strtotime((string)($queue['GNRE005_Proxima_Tentativa'] ?? ''));
        if ($next !== false && $next > $now) return ['available'=>false, 'reason'=>'Próxima consulta permitida em '.self::brazilianDateTime((string)$queue['GNRE005_Proxima_Tentativa']).'.'];
        return ['available'=>true, 'reason'=>''];
    }
}
