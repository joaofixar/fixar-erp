<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\Search;

final class ProductSearchOutput
{
    /** @param array<int,array<string,mixed>> $products @return array<string,mixed> */
    public static function fromProducts(array $products, bool $hasMore = false): array
    {
        $items = [];
        foreach ($products as $product) {
            $items[] = ['id' => (int) $product['D001_Id'], 'code' => (string) $product['D001_Codigo_Produto'], 'description' => (string) $product['D001_Descricao_Produto'], 'unit' => self::nullableString($product['D037_Unidade'] ?? null)];
        }
        return ['items' => $items, 'count' => count($items), 'has_more' => $hasMore];
    }
    /** @param mixed $value */ private static function nullableString($value): ?string { return $value === null ? null : (string) $value; }
}
