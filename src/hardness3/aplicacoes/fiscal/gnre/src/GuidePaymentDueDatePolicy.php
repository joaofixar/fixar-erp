<?php
declare(strict_types=1);

/** Keeps the one guide payment date compatible with every item due date. */
final class GuidePaymentDueDatePolicy
{
    public const PAYMENT_AFTER_DUE_MESSAGE = 'A data de pagamento não pode ser posterior ao menor vencimento dos itens.';

    /** @param array<int,array<string,mixed>|string|null> $items @return array<int,string> */
    public function validate(?string $paymentDate, array $items): array
    {
        $paymentDate = trim((string) $paymentDate);
        if ($paymentDate === '') return [];
        if (!$this->date($paymentDate)) return ['Data de pagamento inválida.'];
        $earliest = null;
        foreach ($items as $item) {
            $dueDate = trim((string) (is_array($item) ? ($item['GNRE003_Data_Vencimento'] ?? '') : $item));
            if ($dueDate === '') continue;
            if (!$this->date($dueDate)) return ['Data de vencimento do item inválida.'];
            if ($earliest === null || $dueDate < $earliest) $earliest = $dueDate;
        }
        return $earliest !== null && $paymentDate > $earliest ? [self::PAYMENT_AFTER_DUE_MESSAGE] : [];
    }

    /** @param array<int,array<string,mixed>|string|null> $items */
    public function assertValid(?string $paymentDate, array $items): void
    {
        $errors = $this->validate($paymentDate, $items);
        if ($errors) throw new RuntimeException($errors[0]);
    }

    private function date(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        return $date instanceof DateTimeImmutable && ($errors === false || (!$errors['warning_count'] && !$errors['error_count'])) && $date->format('Y-m-d') === $value;
    }
}
