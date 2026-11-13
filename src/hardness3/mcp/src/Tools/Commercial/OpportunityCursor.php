<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Commercial;

use InvalidArgumentException;

final class OpportunityCursor
{
    /** @param array<string,mixed> $row */
    public static function encode(array $row): string
    {
        $priority = ['high' => 1, 'medium' => 2, 'low' => 3][(string) ($row['priority'] ?? 'low')] ?? 3;
        $payload = json_encode(['p' => $priority, 'v' => (string) ($row['pending_total'] ?? '0'), 'd' => (string) ($row['issued_at'] ?? ''), 'i' => (int) ($row['document_id'] ?? 0), 't' => (string) ($row['opportunity_type'] ?? '')]);
        if (!is_string($payload)) throw new InvalidArgumentException('Cursor inválido.');
        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }
    /** @return array{priority:int,pending_total:string,issued_at:string,document_id:int,opportunity_type:string} */
    public static function decode(string $cursor): array
    {
        $raw = base64_decode(strtr($cursor, '-_', '+/') . str_repeat('=', (4 - strlen($cursor) % 4) % 4), true); $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data) || !in_array($data['p'] ?? null, [1, 2, 3], true) || !is_numeric($data['v'] ?? null) || !is_string($data['d'] ?? null) || preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $data['d']) !== 1 || !is_int($data['i'] ?? null) || !is_string($data['t'] ?? null) || !in_array($data['t'], ['quote_pending_order', 'order_pending_invoice'], true)) throw new InvalidArgumentException('Cursor inválido.');
        return ['priority' => $data['p'], 'pending_total' => (string) $data['v'], 'issued_at' => $data['d'], 'document_id' => $data['i'], 'opportunity_type' => $data['t']];
    }
}
