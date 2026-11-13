<?php
declare(strict_types=1);

namespace Hardness\Observability\Collectors\Mysql;

/**
 * Recupera uma conexão perdida sem repetir consultas que podem ter sido
 * interrompidas durante a execução. No gravador, o erro 2006 repete uma única
 * vez a leitura fixa e somente leitura; o erro 2013 prepara a próxima leitura.
 */
final class RecoveringRecorderQuerySource implements RecorderQuerySource
{
    private $factory;
    private $source;
    private $recoveries = 0;

    public function __construct(callable $factory)
    {
        $this->factory = $factory;
        $this->source = $this->create();
    }

    public function rows(string $sql, array $parameters = array()): array
    {
        try {
            return $this->source->rows($sql, $parameters);
        } catch (\RuntimeException $exception) {
            $errorNumber = (int) $exception->getCode();
            if (!in_array($errorNumber, array(2006, 2013), true)) { throw $exception; }

            $this->replace();
            $this->recoveries++;
            if ($errorNumber !== 2006) { throw $exception; }

            return $this->source->rows($sql, $parameters);
        }
    }

    public function recoveryCount(): int { return $this->recoveries; }

    public function close(): void
    {
        $this->source->close();
    }

    private function replace(): void
    {
        try { $this->source->close(); } catch (\Throwable $ignored) {}
        $this->source = $this->create();
    }

    private function create(): RecorderQuerySource
    {
        $source = ($this->factory)();
        if (!$source instanceof RecorderQuerySource) { throw new \RuntimeException('mysql_recorder_invalid_query_source'); }
        return $source;
    }
}
