<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote;

final class QuoteOutput
{
    /** @param array<string,mixed> $quote @return array<string,mixed> */
    public static function header(array $quote): array
    {
        $name = self::nullableString($quote['D024_Nome_Fantasia'] ?? null);
        if ($name === null || $name === '') { $name = self::nullableString($quote['D024_Nome_Empresa'] ?? null); }
        $result = ['id' => (int) $quote['T003_Id'], 'issued_at' => self::nullableString($quote['T003_Data_Emissao'] ?? null), 'status' => self::nullableString($quote['status_label'] ?? null), 'customer' => isset($quote['D024_Id']) && $quote['D024_Id'] !== null ? ['id' => (int) $quote['D024_Id'], 'name' => $name] : null, 'total' => (string) $quote['T003_Valor_Total']];
        if (!empty($quote['customer_group_name'])) { $result['customer_group_name'] = (string) $quote['customer_group_name']; }
        $proposalName = self::nullableString($quote['proposal_customer_name_fantasy'] ?? null) ?: self::nullableString($quote['proposal_customer_name'] ?? null);
        if ($proposalName !== null && $proposalName !== '') { $result['proposal_customer_name'] = $proposalName; }
        return $result;
    }
    /** @param array<int,array<string,mixed>> $quotes @return array<string,mixed> */
    public static function search(array $quotes, bool $hasMore = false): array { $items = []; foreach ($quotes as $quote) { $items[] = self::header($quote); } return ['items' => $items, 'count' => count($items), 'has_more' => $hasMore]; }
    /** @param array<string,mixed> $item @return array<string,mixed> */
    public static function item(array $item): array { return ['id' => (int) ($item['T004_Id'] ?? 0), 'line_number' => (int) $item['T004_Item'], 'product' => isset($item['D001_Id']) && $item['D001_Id'] !== null ? ['id' => (int) $item['D001_Id'], 'code' => (string) $item['D001_Codigo_Produto']] : null, 'description' => self::nullableString($item['T004_Descricao_Produto'] ?? null), 'unit' => self::nullableString($item['D037_Unidade'] ?? null), 'location_code' => self::nullableString($item['T004_Codigo_Local'] ?? null), 'quantity' => (string) $item['T004_Quantidade'], 'unit_price' => (string) $item['T004_Valor_Preco_Unitario'], 'total' => (string) $item['T004_Valor_Total_Preco']]; }
    /** @param mixed $value */ private static function nullableString($value): ?string { return $value === null ? null : (string) $value; }
}
