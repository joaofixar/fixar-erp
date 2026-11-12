<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Order\Domain;
interface OrderBacklogRepositoryInterface { public function summary(int $companyId,string $from,string $to,?int $customerId,?int $sellerId,?int $status): array; public function grouped(int $companyId,string $from,string $to,string $groupBy,?int $customerId,?int $sellerId,?int $status,int $limit): array; }
