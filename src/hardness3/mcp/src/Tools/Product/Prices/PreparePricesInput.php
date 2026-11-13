<?php

declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

final class PreparePricesInput
{
    public static function normalize(array $a): array
    {
        if (!preg_match('/\A[A-Za-z0-9_-]{1,128}\z/', $a['request_key'] ?? '')) {
            throw new \InvalidArgumentException('invalid_request_key');
        }
        $seen = [];
        foreach ($a['items'] as &$item) {
            if (isset($seen[$item['line']])) throw new \InvalidArgumentException('duplicate_line');
            $seen[$item['line']] = true;
            $item['code'] = trim($item['code']);
            if ($item['code'] === '') throw new \InvalidArgumentException('invalid_product_code');
            ksort($item['prices']);
            $item = ['line'=>$item['line'], 'code'=>$item['code'], 'prices'=>$item['prices']];
        }
        unset($item);
        usort($a['items'], static function ($x, $y) { return $x['line'] <=> $y['line']; });
        return ['request_key'=>$a['request_key'], 'origin'=>$a['origin'], 'items'=>$a['items']];
    }

    // Canonical decimal strings only; never cast prices to float or silently round.
    public static function decimal(?string $value): ?string
    {
        if ($value === null) return null;
        if (!preg_match('/\A([0-9]{1,10})(?:\.([0-9]{1,10}))?\z/', $value, $m)) {
            throw new \InvalidArgumentException('invalid_price');
        }
        return (ltrim($m[1], '0') ?: '0') . '.' . str_pad($m[2] ?? '', 10, '0');
    }
}
