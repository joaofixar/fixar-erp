<?php

declare(strict_types=1);

use HardnessMcp\Schema\IdeSmtOrderGenerationCapability;

final class IdeSmtCapabilityStatement extends PDOStatement
{
    /** @var array<string,mixed>|false */
    private $row;
    private bool $executeResult;
    /** @var array<string,mixed>|null */
    public ?array $executedWith = null;

    /** @param array<string,mixed>|false $row */
    public function __construct($row, bool $executeResult = true)
    {
        $this->row = $row;
        $this->executeResult = $executeResult;
    }

    public function execute($parameters = null)
    {
        $this->executedWith = is_array($parameters) ? $parameters : [];
        return $this->executeResult;
    }

    public function fetch($mode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        return $this->row;
    }
}

final class IdeSmtCapabilityPdo extends PDO
{
    /** @var array<string,mixed>|false */
    private $row;
    private bool $prepareResult;
    private bool $executeResult;
    public ?IdeSmtCapabilityStatement $statement = null;
    public string $preparedSql = '';

    /** @param array<string,mixed>|false $row */
    public function __construct($row, bool $prepareResult = true, bool $executeResult = true)
    {
        $this->row = $row;
        $this->prepareResult = $prepareResult;
        $this->executeResult = $executeResult;
    }

    public function prepare($statement, $options = [])
    {
        $this->preparedSql = (string) $statement;
        if (!$this->prepareResult) {
            return false;
        }
        $this->statement = new IdeSmtCapabilityStatement($this->row, $this->executeResult);
        return $this->statement;
    }
}

$enabledPdo = new IdeSmtCapabilityPdo(['C031_Valor' => 'S']);
mcpAssert(IdeSmtOrderGenerationCapability::isEnabled($enabledPdo, 17), 'IDE SMT order capability must be enabled only by the S setting');
mcpAssert(strpos($enabledPdo->preparedSql, 'C031_C004_Id=:company_id') !== false, 'IDE SMT capability must be isolated by company');
mcpAssert(($enabledPdo->statement->executedWith ?? null) === [
    'company_id' => 17,
    'setting' => 'SMT_habilitaMudarGerarPedido',
], 'IDE SMT capability must query only the fixed global for the authenticated company');

foreach ([
    ['C031_Valor' => 'N'],
    ['C031_Valor' => 's'],
    ['C031_Valor' => ''],
    ['C031_Valor' => null],
    false,
] as $disabledRow) {
    mcpAssert(!IdeSmtOrderGenerationCapability::isEnabled(new IdeSmtCapabilityPdo($disabledRow), 17), 'IDE SMT order capability must fail closed unless the value is exactly S');
}

mcpAssert(!IdeSmtOrderGenerationCapability::isEnabled(new IdeSmtCapabilityPdo(['C031_Valor' => 'S']), 0), 'IDE SMT capability must reject an invalid company before querying');
mcpAssert(!IdeSmtOrderGenerationCapability::isEnabled(new IdeSmtCapabilityPdo(['C031_Valor' => 'S'], false), 17), 'IDE SMT capability must fail closed when the setting cannot be prepared');
mcpAssert(!IdeSmtOrderGenerationCapability::isEnabled(new IdeSmtCapabilityPdo(['C031_Valor' => 'S'], true, false), 17), 'IDE SMT capability must fail closed when the setting cannot be read');
