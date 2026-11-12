<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference;

final class PartnerReferenceProductInput
{
    private int $partnerCode;
    private string $role;
    private string $referenceCode;

    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        $input = new self();
        $input->partnerCode = (int) $arguments['partner_code'];
        $input->role = (string) $arguments['role'];
        $input->referenceCode = trim((string) $arguments['reference_code']);
        return $input;
    }

    public function getPartnerCode(): int { return $this->partnerCode; }
    public function getRole(): string { return $this->role; }
    public function getReferenceCode(): string { return $this->referenceCode; }
}
