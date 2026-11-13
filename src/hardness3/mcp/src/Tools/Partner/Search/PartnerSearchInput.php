<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use InvalidArgumentException;

final class PartnerSearchInput
{
    private string $criterion; private string $query; private string $role;
    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        $input = new self();
        $input->criterion = (string) $arguments['criterion'];
        $query = preg_replace('/\s+/u', ' ', trim((string) $arguments['query'])) ?? '';
        if ($input->criterion === 'document') {
            $query = preg_replace('/\D/', '', $query) ?? '';
            if (strlen($query) !== 11 && strlen($query) !== 14) throw new InvalidArgumentException('A CPF or CNPJ document is required.');
        } elseif ($input->criterion === 'name') {
            if (mb_strlen($query, 'UTF-8') < 3 || self::isTooGeneric($query)) throw new InvalidArgumentException('Refine the partner name search.');
        } else { throw new InvalidArgumentException('Invalid partner search criterion.'); }
        $input->query = $query;
        $input->role = (string) ($arguments['role'] ?? 'both');
        return $input;
    }
    public function getCriterion(): string { return $this->criterion; }
    public function getQuery(): string { return $this->query; }
    public function getRole(): string { return $this->role; }
    private static function isTooGeneric(string $value): bool { $normalized=mb_strtolower(trim($value),'UTF-8'); return count(preg_split('/\s+/u', $normalized) ?: []) < 2 && (mb_strlen($normalized, 'UTF-8') < 5 || in_array($normalized,['joão','joao','silva','comercial','transportes'],true)); }
}
