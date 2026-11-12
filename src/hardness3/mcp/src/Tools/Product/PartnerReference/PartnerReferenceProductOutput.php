<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference;

final class PartnerReferenceProductOutput
{
    /** @param array<int,array<string,mixed>> $products @return array<string,mixed> */
    public static function fromProducts(array $products, int $partnerCode, string $role, string $referenceCode): array
    {
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'id' => (int) $product['D001_Id'],
                'code' => (string) $product['D001_Codigo_Produto'],
                'description' => (string) $product['D001_Descricao_Produto'],
                'unit' => self::nullableString($product['D037_Unidade'] ?? null),
                'source' => 'partner_reference',
            ];
        }
        return [
            'partner_code' => $partnerCode,
            'role' => $role,
            'reference_code' => $referenceCode,
            'source' => 'partner_reference',
            'items' => $items,
            'count' => count($items),
            'selection_required' => count($items) > 1,
        ];
    }

    /** @param mixed $value */
    private static function nullableString($value): ?string
    {
        return $value === null ? null : (string) $value;
    }
}
