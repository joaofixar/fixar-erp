<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\ItemReport;

use DateTimeImmutable;
use InvalidArgumentException;

final class ItemReportInput
{
    private string $from; private string $to; private ?int $customer; private ?int $seller; private ?string $productCode; private string $groupBy; private int $limit;
    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        $input = new self();
        $input->from = (string) $arguments['date_from']; $input->to = (string) $arguments['date_to'];
        $input->customer = isset($arguments['customer_id']) ? (int) $arguments['customer_id'] : null;
        $input->seller = isset($arguments['seller_internal_id']) ? (int) $arguments['seller_internal_id'] : null;
        $input->productCode = isset($arguments['product_code']) ? trim((string) $arguments['product_code']) : null;
        $input->groupBy = isset($arguments['group_by']) ? (string) $arguments['group_by'] : 'product';
        $input->limit = isset($arguments['limit']) ? (int) $arguments['limit'] : 10;
        $from = self::date($input->from); $to = self::date($input->to);
        if ($from > $to) throw new InvalidArgumentException('A data inicial não pode ser posterior à data final.');
        if ((int) $from->diff($to)->format('%a') + 1 > 366) throw new InvalidArgumentException('Relatórios interativos aceitam no máximo 12 meses. Não divida um período maior em consultas sequenciais; peça ao usuário um único recorte de até 12 meses.');
        return $input;
    }
    public function from(): string { return $this->from; } public function to(): string { return $this->to; }
    public function customer(): ?int { return $this->customer; } public function seller(): ?int { return $this->seller; }
    public function productCode(): ?string { return $this->productCode; } public function groupBy(): string { return $this->groupBy; } public function limit(): int { return $this->limit; }
    private static function date(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value); $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) throw new InvalidArgumentException('Informe datas válidas no formato AAAA-MM-DD.');
        return $date;
    }
}
