<?php
declare(strict_types=1);

/** Small, injectable transaction boundary for the item persistence stages. */
final class GnreGuideItemTransaction
{
    public function begin(mysqli $connection): void
    {
        if (!mysqli_begin_transaction($connection)) throw new RuntimeException('Não foi possível iniciar a gravação atômica do item.');
    }

    public function commit(mysqli $connection): void
    {
        if (!mysqli_commit($connection)) throw new RuntimeException('Não foi possível confirmar a gravação do item.');
    }

    public function rollback(mysqli $connection): void
    {
        @mysqli_rollback($connection);
    }
    /** @param object $connection beginTransaction/commit/rollback; @param array<string,callable> $stages */
    public function run(object $connection, array $stages): void
    {
        $connection->beginTransaction();
        try {
            foreach (['document', 'item', 'values', 'total'] as $stage) {
                if (isset($stages[$stage])) $stages[$stage]();
            }
            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollback();
            throw $e;
        }
    }
}
