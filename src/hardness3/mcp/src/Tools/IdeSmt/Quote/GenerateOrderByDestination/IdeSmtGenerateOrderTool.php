<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Legacy\LegacyBridgeException;
use HardnessMcp\Tools\ConditionallyAvailableToolInterface;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
use Throwable;

final class IdeSmtGenerateOrderTool implements ToolInterface, ConditionallyAvailableToolInterface
{
    public const NAME = 'hardness.ide_smt.quote.generate_order_by_destination';
    public const SCOPE = 'ide_smt:quote:order:generate';

    private IdeSmtGenerateOrderGatewayInterface $gateway;
    private ?int $enabledCompanyId;
    private ToolResultPresenter $presenter;

    public function __construct(IdeSmtGenerateOrderGatewayInterface $gateway, ?int $enabledCompanyId, ?ToolResultPresenter $presenter = null)
    {
        $this->gateway = $gateway;
        $this->enabledCompanyId = $enabledCompanyId;
        $this->presenter = $presenter ?? new ToolResultPresenter();
    }

    public function isAvailable(ExecutionContext $executionContext): bool
    {
        return $this->enabledCompanyId !== null && $this->enabledCompanyId === $executionContext->getCompanyId();
    }

    public function getDefinition(): ToolDefinition
    {
        return new ToolDefinition(self::NAME, 'Generates an order from an existing quote after the user chooses a destination. Treat requests to faturar, processar, or gerar pedido from a quote as an intent to start this choice; when no destination was chosen, ask directly which destination the user wants and list Enviar PDV, Enviar Expedicao, Caderneta Balcao, and Caderneta Expedicao. Do not mention internal tool names, customization sources, or implementation details. Do not call the tool until the user explicitly confirms the quote and one destination. Map point_of_sale to Enviar PDV, shipping to Enviar Expedicao, counter_book to Caderneta Balcao, and shipping_book to Caderneta Expedicao.', [
            'type' => 'object',
            'properties' => [
                'quote_id' => ['type' => 'integer', 'minimum' => 1],
                'destination' => ['type' => 'string', 'enum' => IdeSmtOrderDestination::all()],
            ],
            'required' => ['quote_id', 'destination'],
            'additionalProperties' => false,
        ], [self::SCOPE], true);
    }

    public function execute(array $arguments, ExecutionContext $executionContext): ToolExecutionResult
    {
        try {
            $result = $this->gateway->generate((int) $arguments['quote_id'], (string) $arguments['destination'], $executionContext);
        } catch (Throwable $exception) {
            return $this->failure($exception);
        }

        $safe = [
            'success' => $result['success'] ?? false,
            'quote_id' => (int) ($result['quote_id'] ?? 0),
            'order_id' => (int) ($result['order_id'] ?? 0),
            'destination' => (string) ($result['destination'] ?? ''),
            'order_status' => (int) ($result['order_status'] ?? 0),
            'order_status_name' => (string) ($result['order_status_name'] ?? ''),
            'credit_review' => ($result['credit_review'] ?? false) === true,
            'post_processing_complete' => ($result['post_processing_complete'] ?? false) === true,
        ];
        if ($safe['success'] !== true
            || $safe['quote_id'] !== (int) $arguments['quote_id']
            || $safe['order_id'] <= 0
            || $safe['destination'] !== (string) $arguments['destination']
            || !isset($result['type']) || !is_int($result['type']) || $result['type'] !== IdeSmtOrderDestination::typeFor($safe['destination'])
            || !isset($result['order_status']) || !is_int($result['order_status']) || $result['order_status'] <= 0
            || !isset($result['order_status_name']) || !is_string($result['order_status_name'])
            || !isset($result['credit_review']) || !is_bool($result['credit_review'])
            || !isset($result['post_processing_complete']) || !is_bool($result['post_processing_complete'])) {
            return $this->presenter->functionalError('ide_smt_order_result_invalid', 'O pedido não pôde ser confirmado após o processamento.', 'tools_call_ide_smt_order_failed', [], 'ide_smt_order_result_invalid');
        }

        if (!$safe['post_processing_complete']) {
            $safe['warning'] = 'O pedido foi criado, mas uma etapa posterior de finalização não foi concluída. Verifique o pedido no ERP e não tente gerá-lo novamente.';
        }
        return $this->presenter->record($safe, $safe['post_processing_complete'] ? 'Pedido gerado por destino' : 'Pedido criado com pendência de finalização', [
            ['path' => 'quote_id', 'label' => 'Orçamento'],
            ['path' => 'order_id', 'label' => 'Pedido'],
            ['path' => 'destination', 'label' => 'Destino'],
            ['path' => 'order_status', 'label' => 'Situação'],
            ['path' => 'order_status_name', 'label' => 'Descrição'],
            ['path' => 'warning', 'label' => 'Atenção'],
        ], $safe['post_processing_complete'] ? 'tools_call_ide_smt_order_success' : 'tools_call_ide_smt_order_created_with_warning');
    }

    private function failure(Throwable $exception): ToolExecutionResult
    {
        $code = $exception instanceof LegacyBridgeException ? $exception->getMessage() : '';
        $details = $exception instanceof LegacyBridgeException ? $exception->getPublicDetails() : [];
        $messages = [
            'ide_smt_order_disabled' => 'A geração de pedido por destino não está habilitada para esta empresa.',
            'ide_smt_quote_not_available' => 'O orçamento não está disponível para esta empresa.',
            'ide_smt_quote_extension_missing' => 'O orçamento não possui a configuração necessária para este fluxo.',
            'ide_smt_quote_customer_blocked' => 'Este cliente não pode utilizar a geração de pedido por destino.',
            'ide_smt_quote_freight_mismatch' => 'O frete dos itens está diferente do frete do orçamento. Atualize os preços antes de gerar o pedido.',
            'ide_smt_quote_in_analysis' => 'O orçamento está em análise e não pode gerar pedido neste momento.',
            'ide_smt_quote_approval_required' => 'O orçamento exige liberação de uma alçada superior.',
            'ide_smt_order_time_limit' => 'O horário permitido para geração de pedidos foi ultrapassado.',
            'ide_smt_order_precondition_failed' => 'O orçamento não atende às condições necessárias para gerar o pedido.',
            'ide_smt_order_generation_failed' => 'O pedido não pôde ser gerado a partir do orçamento.',
        ];
        $publicCode = array_key_exists($code, $messages) ? $code : 'ide_smt_order_failed';
        $functionalMessage = $details['functional_message'] ?? null;
        $message = is_string($functionalMessage) && $functionalMessage !== '' && in_array($code, ['ide_smt_order_precondition_failed', 'ide_smt_order_generation_failed'], true)
            ? $functionalMessage
            : ($messages[$code] ?? 'O pedido não pôde ser gerado pelo destino selecionado.');
        $auditCode = preg_match('/\Aide_smt_(?:order|quote)_[a-z0-9_]{1,45}\z/', $code) === 1 ? $code : 'ide_smt_order_unclassified_failure';
        if ($code === '') {
            error_log(json_encode([
                'event' => 'hardness_mcp_ide_smt_order_unclassified_failure',
                'exception_class' => get_class($exception),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
            ]));
        }
        return $this->presenter->functionalError($publicCode, $message, 'tools_call_ide_smt_order_failed', [], $auditCode);
    }
}
