<?php

declare(strict_types=1);

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\Tools\Catalog\IdeSmtToolsModule;
use HardnessMcp\Tools\Catalog\ToolCatalogFactory;
use HardnessMcp\Tools\ToolExecutor;

$ideSmtGateway = new TestIdeSmtGenerateOrderGateway();
$ideSmtDisabledRegistry = (new ToolCatalogFactory([new IdeSmtToolsModule($ideSmtGateway, null)]))->createRegistry();
$ideSmtDisabledServer = new JsonRpcServer($config, new ToolExecutor($ideSmtDisabledRegistry));
$ideSmtAuthorizedContext = new ExecutionContext(7, 8, 3, 9, ['ide_smt:quote:order:generate'], 'api_key', null, null, null, '203.0.113.10', true);
$ideSmtCall = '{"jsonrpc":"2.0","id":301,"method":"tools/call","params":{"name":"hardness_ide_smt_quote_generate_order_by_destination","arguments":{"quote_id":123,"destination":"shipping"}}}';

$ideSmtDisabledList = $ideSmtDisabledServer->handle('{"jsonrpc":"2.0","id":300,"method":"tools/list"}', '2025-11-25', $ideSmtAuthorizedContext);
mcpAssert(($ideSmtDisabledList->getPayload()['result']['tools'] ?? []) === [], 'IDE SMT Tool must not appear when the company global is disabled');
$ideSmtDisabledCall = $ideSmtDisabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtAuthorizedContext);
mcpAssert(($ideSmtDisabledCall->getPayload()['error']['code'] ?? null) === -32601 && $ideSmtGateway->quoteId === null, 'a direct IDE SMT Tool call must look nonexistent and must not reach the gateway when the global is disabled');

$ideSmtEnabledRegistry = (new ToolCatalogFactory([new IdeSmtToolsModule($ideSmtGateway, 3)]))->createRegistry();
$ideSmtEnabledServer = new JsonRpcServer($config, new ToolExecutor($ideSmtEnabledRegistry));
$ideSmtEnabledList = $ideSmtEnabledServer->handle('{"jsonrpc":"2.0","id":302,"method":"tools/list"}', '2025-11-25', $ideSmtAuthorizedContext);
$ideSmtDefinitions = $ideSmtEnabledList->getPayload()['result']['tools'] ?? [];
mcpAssert(array_column($ideSmtDefinitions, 'name') === ['hardness_ide_smt_quote_generate_order_by_destination'], 'IDE SMT Tool must appear under its normalized public name when global, scope, actor, and company match');
mcpAssert(($ideSmtDefinitions[0]['inputSchema']['properties']['destination']['enum'] ?? null) === ['point_of_sale', 'shipping', 'counter_book', 'shipping_book'], 'IDE SMT Tool must publish only the four supported destinations');
$ideSmtDescription = (string) ($ideSmtDefinitions[0]['description'] ?? '');
mcpAssert(preg_match('/\b(?:IDE|SMT)\b/i', $ideSmtDescription) !== 1
    && strpos($ideSmtDescription, 'ask directly which destination') !== false
    && strpos($ideSmtDescription, 'Enviar PDV') !== false,
    'the public Tool guidance must list Enviar PDV and ask for the destination without exposing internal implementation names');

$ideSmtWrongCompany = new ExecutionContext(7, 8, 4, 9, ['ide_smt:quote:order:generate'], 'api_key', null, null, null, '203.0.113.10', true);
$ideSmtWrongCompanyList = $ideSmtEnabledServer->handle('{"jsonrpc":"2.0","id":303,"method":"tools/list"}', '2025-11-25', $ideSmtWrongCompany);
mcpAssert(($ideSmtWrongCompanyList->getPayload()['result']['tools'] ?? []) === [], 'IDE SMT capability must not leak from one company to another');

$ideSmtNoScope = new ExecutionContext(7, 8, 3, 9, ['quote:read'], 'api_key', null, null, null, '203.0.113.10', true);
$ideSmtNoScopeList = $ideSmtEnabledServer->handle('{"jsonrpc":"2.0","id":304,"method":"tools/list"}', '2025-11-25', $ideSmtNoScope);
mcpAssert(($ideSmtNoScopeList->getPayload()['result']['tools'] ?? []) === [], 'IDE SMT Tool must be hidden without its dedicated scope');
$ideSmtNoScopeCall = $ideSmtEnabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtNoScope);
mcpAssert(($ideSmtNoScopeCall->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'scope_denied', 'IDE SMT Tool must reject direct calls without its dedicated scope');

$ideSmtNoActor = new ExecutionContext(7, 8, 3, null, ['ide_smt:quote:order:generate'], 'api_key', null, null, null, '203.0.113.10', false);
$ideSmtNoActorList = $ideSmtEnabledServer->handle('{"jsonrpc":"2.0","id":305,"method":"tools/list"}', '2025-11-25', $ideSmtNoActor);
mcpAssert(($ideSmtNoActorList->getPayload()['result']['tools'] ?? []) === [], 'IDE SMT Tool must be hidden without a valid ERP actor');
$ideSmtNoActorCall = $ideSmtEnabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtNoActor);
mcpAssert(($ideSmtNoActorCall->getPayload()['result']['structuredContent']['error']['code'] ?? null) === 'erp_actor_required', 'IDE SMT Tool must reject direct calls without a valid ERP actor');

$ideSmtInvalidDestination = $ideSmtEnabledServer->handle('{"jsonrpc":"2.0","id":306,"method":"tools/call","params":{"name":"hardness_ide_smt_quote_generate_order_by_destination","arguments":{"quote_id":123,"destination":"standard"}}}', '2025-11-25', $ideSmtAuthorizedContext);
mcpAssert(($ideSmtInvalidDestination->getPayload()['error']['code'] ?? null) === -32602, 'IDE SMT Tool schema must reject destinations outside the fixed allowlist');

$ideSmtSuccess = $ideSmtEnabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtAuthorizedContext);
$ideSmtSuccessPayload = $ideSmtSuccess->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($ideSmtSuccessPayload['success'] ?? false) === true
    && ($ideSmtSuccessPayload['quote_id'] ?? null) === 123
    && ($ideSmtSuccessPayload['order_id'] ?? null) === 9001
    && ($ideSmtSuccessPayload['destination'] ?? null) === 'shipping'
    && ($ideSmtSuccessPayload['credit_review'] ?? null) === true
    && ($ideSmtSuccessPayload['post_processing_complete'] ?? null) === true
    && $ideSmtGateway->quoteId === 123
    && $ideSmtGateway->destination === 'shipping'
    && $ideSmtGateway->context === $ideSmtAuthorizedContext,
    'authorized IDE SMT Tool calls must pass only validated arguments and return the confirmed order result');

$ideSmtPosCall = '{"jsonrpc":"2.0","id":307,"method":"tools/call","params":{"name":"hardness_ide_smt_quote_generate_order_by_destination","arguments":{"quote_id":124,"destination":"point_of_sale"}}}';
$ideSmtPosSuccess = $ideSmtEnabledServer->handle($ideSmtPosCall, '2025-11-25', $ideSmtAuthorizedContext);
$ideSmtPosPayload = $ideSmtPosSuccess->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($ideSmtPosPayload['success'] ?? false) === true
    && ($ideSmtPosPayload['quote_id'] ?? null) === 124
    && ($ideSmtPosPayload['destination'] ?? null) === 'point_of_sale'
    && $ideSmtGateway->destination === 'point_of_sale',
    'Enviar PDV must be accepted by the Tool and reach the gateway as its explicit allow-listed destination');

$ideSmtGateway->postProcessingComplete = false;
$ideSmtPartialSuccess = $ideSmtEnabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtAuthorizedContext);
$ideSmtPartialPayload = $ideSmtPartialSuccess->getPayload()['result']['structuredContent'] ?? [];
mcpAssert(($ideSmtPartialPayload['success'] ?? false) === true
    && ($ideSmtPartialPayload['order_id'] ?? null) === 9001
    && ($ideSmtPartialPayload['post_processing_complete'] ?? true) === false
    && strpos((string) ($ideSmtPartialPayload['warning'] ?? ''), 'não tente gerá-lo novamente') !== false
    && $ideSmtPartialSuccess->getAuditEvent() === 'tools_call_ide_smt_order_created_with_warning',
    'an order confirmed before a post-processing failure must be returned with a non-retry warning');
$ideSmtGateway->postProcessingComplete = true;

$ideSmtGateway->failure = new LegacyBridgeException('ide_smt_order_precondition_failed', ['functional_message' => 'O campo TRANSPORTADORA é obrigatório para esse tipo de frete']);
$ideSmtFunctionalFailure = $ideSmtEnabledServer->handle($ideSmtCall, '2025-11-25', $ideSmtAuthorizedContext);
$ideSmtFunctionalPayload = $ideSmtFunctionalFailure->getPayload()['result']['structuredContent']['error'] ?? [];
mcpAssert(($ideSmtFunctionalPayload['code'] ?? null) === 'ide_smt_order_precondition_failed'
    && strpos((string) ($ideSmtFunctionalPayload['message'] ?? ''), 'TRANSPORTADORA') !== false
    && $ideSmtFunctionalFailure->getAuditSafeCode() === 'ide_smt_order_precondition_failed',
    'safe functional messages returned by the IDE methods must reach the AI with an allow-listed audit code');
