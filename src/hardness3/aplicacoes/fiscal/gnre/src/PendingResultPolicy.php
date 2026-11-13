<?php
declare(strict_types=1);

/**
 * Agenda a releitura de um recibo que o Portal confirmou, mas ainda processa.
 * Não classifica resultados e não trata falhas técnicas.
 */
final class GnrePendingResultPolicy
{
    private const MIN_ESTIMATED_SECONDS = 60;
    private const MAX_SECONDS = 3600;
    private const NO_ESTIMATE_SECONDS = 3;

    public static function nextDelaySeconds($estimatedMilliseconds, int $attempts): int
    {
        $estimate = is_numeric($estimatedMilliseconds) ? (int)$estimatedMilliseconds : 0;
        if ($estimate >= 1000 && $estimate <= self::MAX_SECONDS * 1000) {
            return max(self::MIN_ESTIMATED_SECONDS, min(self::MAX_SECONDS, (int)ceil($estimate / 1000)));
        }
        return self::NO_ESTIMATE_SECONDS;
    }

    public static function publicMessage(): string
    {
        return 'O Portal recebeu a guia e ainda está processando o resultado. Não retransmita esta guia.';
    }
}
