<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Logistics;

use DateTimeImmutable;
use InvalidArgumentException;

final class LoadReportInput
{
    /** @return array<string,mixed> */
    public static function period(array $arguments, bool $summary): array
    {
        $from = (string)($arguments['date_from'] ?? ''); $to = (string)($arguments['date_to'] ?? '');
        $fromDate = self::date($from); $toDate = self::date($to);
        if ($fromDate > $toDate) throw new InvalidArgumentException('A data inicial não pode ser posterior à data final.');
        if ($toDate > $fromDate->modify('+12 months')) throw new InvalidArgumentException('Consultas de cargas aceitam um período de até 12 meses.');
        return ['from' => $from, 'to' => $to, 'basis' => (string)($arguments['date_basis'] ?? 'created_at'), 'group' => $summary ? (string)($arguments['group_by'] ?? '') : null, 'status' => isset($arguments['status']) ? trim((string)$arguments['status']) : null, 'limit' => isset($arguments['limit']) ? (int)$arguments['limit'] : 20];
    }

    private static function date(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value); $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) throw new InvalidArgumentException('Informe datas válidas no formato AAAA-MM-DD.');
        return $date;
    }
}
