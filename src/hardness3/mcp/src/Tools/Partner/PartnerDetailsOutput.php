<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner;

final class PartnerDetailsOutput
{
    /** @param array<string,mixed> $partner @return array<string,mixed> */
    public static function fromPartner(array $partner): array
    {
        $document = trim((string) ($partner['D024_Cnpj'] ?? ''));
        $documentType = 'cnpj';
        if ($document === '') {
            $document = trim((string) ($partner['D024_Cpf'] ?? ''));
            $documentType = 'cpf';
        }

        return [
            // "Código" is the public API concept. Its value is the ERP's
            // D024_Id, which quote.create continues to validate independently.
            'code' => (int) ($partner['D024_Id'] ?? 0),
            'legal_name' => self::value($partner['D024_Nome_Empresa'] ?? null),
            'trading_name' => self::value($partner['D024_Nome_Fantasia'] ?? null),
            'person_type' => self::value($partner['D024_Flag_Pessoa_Fisica_Juridica'] ?? null),
            'document' => $document === '' ? null : $document,
            'document_type' => $document === '' ? null : $documentType,
            'status' => ($partner['D024_Flag_Ativo'] ?? '') === 'S' ? 'active' : 'inactive',
            'roles' => [
                'customer' => ($partner['D024_Flag_Cliente'] ?? '') === 'S',
                'supplier' => ($partner['D024_Flag_Fornecedor'] ?? '') === 'S',
            ],
            'address' => [
                'street' => self::value($partner['D024_Endereco'] ?? null),
                'number' => self::value($partner['D024_Numero'] ?? null),
                'complement' => self::value($partner['D024_Complemento'] ?? null),
                'neighborhood' => self::value($partner['D024_Bairro'] ?? null),
                'city' => self::value($partner['D024_Cidade'] ?? null),
                'state' => self::value($partner['D024_Estado'] ?? null),
                'postal_code' => self::value($partner['D024_Cep'] ?? null),
            ],
        ];
    }

    /** @param mixed $value */
    private static function value($value): ?string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';
        return $value === '' ? null : $value;
    }
}
