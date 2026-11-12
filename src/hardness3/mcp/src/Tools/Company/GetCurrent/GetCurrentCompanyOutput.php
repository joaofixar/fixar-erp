<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Company\GetCurrent;

/** Explicit allow-list for the stable public company profile response. */
final class GetCurrentCompanyOutput
{
    /** @param array<string, mixed> $company @return array<string, mixed> */
    public static function fromCompany(array $company): array
    {
        return [
            'id' => (int) $company['C004_Id'],
            'legal_name' => self::nullableString($company['C004_Nome_Empresa'] ?? null),
            'short_name' => self::nullableString($company['C004_Nome_Abreviado'] ?? null),
            'document' => self::nullableString($company['C004_Cnpj'] ?? null),
            'city' => self::nullableString($company['C004_Cidade'] ?? null),
            'state' => self::nullableString($company['C004_Uf'] ?? null),
            'status' => self::nullableString($company['C004_Flag_Ativo'] ?? null),
        ];
    }

    /** @param mixed $value */
    private static function nullableString($value): ?string { return $value === null ? null : (string) $value; }
}
