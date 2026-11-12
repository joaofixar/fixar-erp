<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary;

use InvalidArgumentException;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;

final class QuoteSummaryTool implements ToolInterface
{
    private QuoteSummaryHandler $handler; private ToolResultPresenter $presenter;
    public function __construct(QuoteSummaryHandler $handler,?ToolResultPresenter $presenter=null){$this->handler=$handler;$this->presenter=$presenter??new ToolResultPresenter(20);}
    public function getDefinition(): ToolDefinition{return (new QuoteSummaryDefinition())->toToolDefinition();}
    /** @param array<string,mixed> $arguments */
    public function execute(array $arguments,ExecutionContext $context): ToolExecutionResult
    {
        try{$result=$this->handler->handle(QuoteSummaryInput::fromArguments($arguments),$context);}catch(InvalidArgumentException $exception){return $this->presenter->functionalError('quote_summary_invalid_period',$exception->getMessage(),'tools_call_quote_summary_invalid_period');}catch(\Throwable $exception){return $this->presenter->functionalError('quote_summary_unavailable','Não foi possível consultar o resumo de orçamentos agora. Tente novamente.','tools_call_quote_summary_unavailable');}
        return $this->presenter->report($result,'Resumo de orçamentos',[['path'=>'summary.quoted_total','label'=>'Total orçado','format'=>'money'],['path'=>'summary.quote_count','label'=>'Orçamentos']],'rows',$this->rowFields((string)$result['group_by']));
    }
    /** @return array<int,array<string,mixed>> */
    private function rowFields(string $groupBy): array
    {
        $fields=['day'=>[['path'=>'date','label'=>'Data','format'=>'date']],'week'=>[['path'=>'week_start','label'=>'Semana de','format'=>'date']],'month'=>[['path'=>'month','label'=>'Mês','format'=>'date']],'customer'=>[['path'=>'customer_id','label'=>'Cliente'],['path'=>'customer_name','label'=>'Nome']],'seller_internal'=>[['path'=>'seller_internal_id','label'=>'Vendedor interno'],['path'=>'seller_internal_name','label'=>'Nome'],['path'=>'supervisor_id','label'=>'Supervisor'],['path'=>'supervisor_name','label'=>'Nome do supervisor']],'supervisor'=>[['path'=>'supervisor_id','label'=>'Supervisor'],['path'=>'supervisor_name','label'=>'Nome']],'status'=>[['path'=>'status_label','label'=>'Status']],'document'=>[['path'=>'document_id','label'=>'Orçamento'],['path'=>'issued_at','label'=>'Emissão','format'=>'date'],['path'=>'customer_name','label'=>'Cliente'],['path'=>'customer_group_name','label'=>'Grupo'],['path'=>'proposal_customer_name','label'=>'Cliente da proposta'],['path'=>'status_label','label'=>'Status']]];
        return array_merge($fields[$groupBy]??[],[['path'=>'quoted_total','label'=>'Orçado','format'=>'money'],['path'=>'quote_count','label'=>'Orçamentos']]);
    }
}
