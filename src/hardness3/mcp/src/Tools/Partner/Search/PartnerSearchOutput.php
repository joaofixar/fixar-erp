<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use HardnessMcp\Context\ExecutionContext;

final class PartnerSearchOutput
{
    /** @param array<int,array<string,mixed>> $partners @return array<string,mixed> */
    public static function fromPartners(array $partners, ExecutionContext $context, PartnerCandidateReference $references, bool $documentMatch = false): array
    {
        if (count($partners) > 5) return ['items' => [], 'count' => 0, 'refine_required' => true, 'selection_required' => false, 'document_match' => $documentMatch];
        $items = [];
        foreach ($partners as $partner) {
            $items[] = ['candidate_ref' => $references->issue((int) $partner['D024_Id'], $context), 'legal_name' => self::nullable($partner['D024_Nome_Empresa'] ?? null), 'trading_name' => self::nullable($partner['D024_Nome_Fantasia'] ?? null), 'city' => self::nullable($partner['D024_Cidade'] ?? null), 'state' => self::nullable($partner['D024_Estado'] ?? null)];
        }
        return ['items' => $items, 'count' => count($items), 'refine_required' => false, 'selection_required' => count($items) > 1, 'document_match' => $documentMatch];
    }
    /** @param mixed $value */ private static function nullable($value): ?string { $value = is_scalar($value) ? trim((string) $value) : ''; return $value === '' ? null : $value; }
}
