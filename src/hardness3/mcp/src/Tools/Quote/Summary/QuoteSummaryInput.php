<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary;

use DateTimeImmutable;
use InvalidArgumentException;

final class QuoteSummaryInput
{
    private string $dateFrom; private string $dateTo; private string $groupBy; private ?int $customerId; private ?int $sellerInternalId; private ?int $supervisorId; private ?string $status; private ?string $origin; private int $limit;
    /** @param array<string,mixed> $arguments */
    public static function fromArguments(array $arguments): self
    {
        $input = new self(); $input->dateFrom=(string)$arguments['date_from']; $input->dateTo=(string)$arguments['date_to']; $input->groupBy=(string)$arguments['group_by']; $input->customerId=isset($arguments['customer_id'])?(int)$arguments['customer_id']:null; $input->sellerInternalId=isset($arguments['seller_internal_id'])?(int)$arguments['seller_internal_id']:null; $input->supervisorId=isset($arguments['supervisor_id'])?(int)$arguments['supervisor_id']:null; $input->status=isset($arguments['status'])?(string)$arguments['status']:null; $input->origin=isset($arguments['origin'])?(string)$arguments['origin']:null; $input->limit=isset($arguments['limit'])?(int)$arguments['limit']:20; $input->assertPeriod(); return $input;
    }
    public function getDateFrom(): string{return $this->dateFrom;} public function getDateTo(): string{return $this->dateTo;} public function getGroupBy(): string{return $this->groupBy;} public function getCustomerId(): ?int{return $this->customerId;} public function getSellerInternalId(): ?int{return $this->sellerInternalId;} public function getSupervisorId(): ?int{return $this->supervisorId;} public function getStatus(): ?string{return $this->status;} public function getOrigin(): ?string{return $this->origin;} public function getLimit(): int{return $this->limit;}
    private function assertPeriod(): void
    {
        $from=self::date($this->dateFrom); $to=self::date($this->dateTo); if($from>$to)throw new InvalidArgumentException('A data inicial não pode ser posterior à data final.'); $days=(int)$from->diff($to)->format('%a')+1;
        if($this->groupBy==='day'&&$days>90)throw new InvalidArgumentException('O agrupamento diário aceita no máximo 90 dias. Não divida um período maior em consultas sequenciais; peça ao usuário um único recorte de até 90 dias.');
        $historical=in_array($this->groupBy,['month','customer','seller_internal','supervisor','status'],true);
        if($historical&&$to>$from->modify('+5 years'))throw new InvalidArgumentException('Resumos históricos aceitam no máximo 5 anos. Não divida um período maior em consultas sequenciais; peça ao usuário um único recorte de até 5 anos.');
        if(!$historical&&$to>$from->modify('+1 year'))throw new InvalidArgumentException('Agrupamentos semanais e listagens de documentos aceitam no máximo 12 meses. Não divida um período maior em consultas sequenciais; peça ao usuário um único recorte de até 12 meses.');
    }
    private static function date(string $value): DateTimeImmutable
    {
        $date=DateTimeImmutable::createFromFormat('!Y-m-d',$value); $errors=DateTimeImmutable::getLastErrors(); if($date===false||(is_array($errors)&&($errors['warning_count']>0||$errors['error_count']>0)))throw new InvalidArgumentException('Informe datas válidas no formato AAAA-MM-DD.'); return $date;
    }
}
