<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Finance;

use InvalidArgumentException;

final class FinanceTitleCursor
{
    /** @param array<string,mixed> $row */
    public static function encode(array $row): string
    {
        $payload = json_encode(['d' => (string) ($row['due_date'] ?? ''), 'i' => (int) ($row['title_id'] ?? 0)]);
        if (!is_string($payload)) throw new InvalidArgumentException('Cursor inválido.');
        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    /** @return array{due_date:string,title_id:int} */
    public static function decode(string $cursor): array
    {
        $raw = base64_decode(strtr($cursor, '-_', '+/') . str_repeat('=', (4 - strlen($cursor) % 4) % 4), true);
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data) || !is_string($data['d'] ?? null) || preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $data['d']) !== 1 || !is_int($data['i'] ?? null) || $data['i'] < 1) throw new InvalidArgumentException('Cursor inválido.');
        return ['due_date' => $data['d'], 'title_id' => $data['i']];
    }
}
