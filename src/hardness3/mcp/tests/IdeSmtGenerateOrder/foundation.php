<?php

declare(strict_types=1);

use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Legacy\LegacyExecutionContext;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Tenant\ResolvedTenantContext;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\IdeSmtOrderDestination;

$functionalFailure = new LegacyBridgeException('ide_smt_order_precondition_failed', ['functional_message' => 'Transportadora obrigatória']);
mcpAssert(($functionalFailure->getPublicDetails()['functional_message'] ?? null) === 'Transportadora obrigatória', 'safe IDE SMT functional messages must remain available to the future Tool presenter');

mcpAssert(IdeSmtOrderDestination::all() === ['point_of_sale', 'shipping', 'counter_book', 'shipping_book'], 'IDE SMT order destinations must remain an explicit allowlist');
mcpAssert(IdeSmtOrderDestination::typeFor('point_of_sale') === 1, 'point of sale must map to legacy type 1');
mcpAssert(IdeSmtOrderDestination::typeFor('shipping') === 2, 'shipping must map to legacy type 2');
mcpAssert(IdeSmtOrderDestination::typeFor('counter_book') === 3, 'counter book must map to legacy type 3');
mcpAssert(IdeSmtOrderDestination::typeFor('shipping_book') === 4, 'shipping book must map to legacy type 4');

try {
    IdeSmtOrderDestination::typeFor('standard');
    throw new RuntimeException('unknown IDE SMT destination should have failed');
} catch (InvalidArgumentException $exception) {
    mcpAssert($exception->getMessage() === 'Invalid IDE SMT order destination.', 'unknown IDE SMT destinations must be rejected before legacy execution');
}

$ideSmtRoot = dirname(__DIR__, 3);
$ideSmtTenant = ResolvedTenantContext::fromResolvedTenant([
    'host' => 'tenant.example.test',
    'conf_file' => $ideSmtRoot . '/test-conf.php',
    'dbDatabase' => 'test_tenant',
    'dbHost' => '127.0.0.1',
    'dbUser' => 'test',
    'dbPass' => 'test',
]);
$ideSmtLegacyContext = new LegacyExecutionContext(
    $ideSmtTenant,
    $ideSmtRoot . '/',
    $ideSmtRoot . '/dados_usuarios/test_tenant/',
    1,
    2,
    3,
    1,
    '127.0.0.1',
    'America/Sao_Paulo'
);
$ideSmtBridge = new McpFrameworkBridge($ideSmtRoot);
$ideSmtBridgeSource = file_get_contents($ideSmtRoot . '/mcp/src/Legacy/McpFrameworkBridge.php');
mcpAssert($ideSmtBridgeSource !== false
    && strpos($ideSmtBridgeSource, 'acoesAntesGerarPedido($quoteId, $type)') !== false
    && strpos($ideSmtBridgeSource, '$blockedCustomerId') === false,
    'the bridge must delegate destination-specific validation to VEN001 instead of applying the Caderneta customer restriction to Enviar PDV');
foreach ([[0, 2], [10, 0], [10, 5]] as $invalidCall) {
    try {
        $ideSmtBridge->executeVen001IdeSmtGenerateOrder($ideSmtLegacyContext, $invalidCall[0], $invalidCall[1]);
        throw new RuntimeException('invalid IDE SMT bridge arguments should have failed');
    } catch (LegacyBridgeException $exception) {
        mcpAssert($exception->getMessage() === 'ide_smt_order_invalid', 'IDE SMT bridge must reject quote and type values outside its fixed contract before opening a connection');
    }
}
