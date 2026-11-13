<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/** Fonte mínima para consultas fixas e somente leitura do gravador MySQL. */
interface RecorderQuerySource
{
    public function rows(string $sql, array $parameters = array()): array;

    public function close(): void;
}
