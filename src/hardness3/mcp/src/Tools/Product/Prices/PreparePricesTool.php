<?php

declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\{ToolDefinition, ToolExecutionResult, ToolInterface};
use HardnessMcp\Tools\Presentation\ToolResultPresenter;

final class PreparePricesTool implements ToolInterface
{
    private ?PreparePricesRepository $repository;
    public function __construct(?PreparePricesRepository $repository = null) { $this->repository = $repository; }

    public function getDefinition(): ToolDefinition
    {
        $prices = [];
        for ($n=1; $n<=5; $n++) $prices['price_'.$n] = ['type'=>'string','minLength'=>1,'maxLength'=>21];
        return new ToolDefinition('hardness.product.prices.prepare',
            'Prepares and persists a price preview, never updates product prices. Accepts 1 to '.PriceBatchLimits::maxItems().' explicitly selected product codes in one company. Ask which prices (1 to 5) to change when unspecified; never infer all prices. For spreadsheets preserve leading zeros in codes, convert amounts to decimal strings with dot and no thousands separator, and omit empty cells. Each item must select at least one price. Show all rows, old/new selected prices, warnings and blocked rows to the user. Zero requires explicit approval in a later apply step. Use hardness_product_prices_apply only after explicit approval of this preview. Reuse request_key only for the identical request; changed data or expired preview requires a new key. Never imply that prices were updated.',
            ['type'=>'object','properties'=>[
                'request_key'=>['type'=>'string','minLength'=>1,'maxLength'=>128],
                'origin'=>['type'=>'string','enum'=>['manual','spreadsheet']],
                'items'=>['type'=>'array','minItems'=>1,'maxItems'=>PriceBatchLimits::maxItems(),'items'=>[
                    'type'=>'object','properties'=>[
                        'line'=>['type'=>'integer','minimum'=>1,'maximum'=>4294967295],
                        'code'=>['type'=>'string','minLength'=>1,'maxLength'=>255],
                        'prices'=>['type'=>'object','properties'=>$prices,'additionalProperties'=>false],
                    ],'required'=>['line','code','prices'],'additionalProperties'=>false,
                ]],
            ],'required'=>['request_key','origin','items'],'additionalProperties'=>false],
            ['product:prices:prepare'], true, new \HardnessMcp\Tools\Validation\ToolArgumentsValidator(PriceBatchLimits::maxItems(), PriceBatchLimits::argumentBytes()));
    }

    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult
    {
        $presenter = new ToolResultPresenter();
        try {
            if (!$context->hasErpActor() || !$context->getResponsibleUserId()) throw new \RuntimeException('actor_required');
            if ($this->repository === null) throw new \RuntimeException('preview_unavailable');
            $result = $this->repository->prepare(PreparePricesInput::normalize($arguments), $context);
            return $presenter->detailWithItems($result, 'Prévia preparada; nenhum preço foi alterado',
                [['path'=>'batch_id','label'=>'Lote'],['path'=>'preview_hash','label'=>'Código de verificação da prévia'],['path'=>'expires_at','label'=>'Validade UTC'],['path'=>'status','label'=>'Situação']],
                'items', [['path'=>'line','label'=>'Linha'],['path'=>'code','label'=>'Código'],['path'=>'status','label'=>'Situação'],['path'=>'changes_text','label'=>'Preços'],['path'=>'error','label'=>'Pendência']],
                'tools_call_product_prices_prepare');
        } catch (\Throwable $e) {
            $messages = [
                'invalid_request_key'=>'Use uma chave de solicitação com letras, números, hífen ou sublinhado.',
                'duplicate_line'=>'As linhas da entrada devem ter identificadores distintos.',
                'invalid_product_code'=>'Informe o código de cada produto.',
                'idempotency_conflict'=>'A chave já foi usada para outra solicitação ou responsável. Use uma nova chave.',
                'preview_expired'=>'A prévia expirou. Prepare outra com uma nova chave.',
                'actor_required'=>'É necessário um usuário ERP responsável válido.',
            ];
            $code = isset($messages[$e->getMessage()]) ? $e->getMessage() : 'preview_unavailable';
            return $presenter->functionalError($code, $messages[$code] ?? 'Não foi possível preparar a prévia. Nenhum preço foi alterado.', 'tools_call_product_prices_prepare_failed', [], $code);
        }
    }
}
