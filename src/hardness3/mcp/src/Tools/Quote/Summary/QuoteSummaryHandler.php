<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary;

use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Quote\Summary\Domain\QuoteSummaryRepositoryInterface;

final class QuoteSummaryHandler
{
    private QuoteSummaryRepositoryInterface $repository;
    public function __construct(QuoteSummaryRepositoryInterface $repository){$this->repository=$repository;}
    /** @return array<string,mixed> */
    public function handle(QuoteSummaryInput $input,ExecutionContext $context): array
    {
        $companyId=$context->getCompanyId(); $summary=$this->repository->summary($companyId,$input->getDateFrom(),$input->getDateTo(),$input->getCustomerId(),$input->getSellerInternalId(),$input->getSupervisorId(),$input->getStatus(),$input->getOrigin()); $rows=$this->repository->grouped($companyId,$input->getDateFrom(),$input->getDateTo(),$input->getGroupBy(),$input->getCustomerId(),$input->getSellerInternalId(),$input->getSupervisorId(),$input->getStatus(),$input->getOrigin(),$input->getLimit()+1); $hasMore=in_array($input->getGroupBy(),['customer','seller_internal','supervisor','document'],true)&&count($rows)>$input->getLimit(); if($hasMore)$rows=array_slice($rows,0,$input->getLimit());
        return ['report'=>'quote_summary','definition'=>'Orçamentos emitidos, agrupados pela data de emissão.','period'=>['from'=>$input->getDateFrom(),'to'=>$input->getDateTo()],'filters_applied'=>array_filter(['customer_id'=>$input->getCustomerId(),'seller_internal_id'=>$input->getSellerInternalId(),'supervisor_id'=>$input->getSupervisorId(),'status'=>$input->getStatus(),'origin'=>$input->getOrigin()],static function($value):bool{return $value!==null;}),'group_by'=>$input->getGroupBy(),'summary'=>QuoteSummaryOutput::summary($summary),'rows'=>QuoteSummaryOutput::rows($rows,$input->getGroupBy()),'has_more'=>$hasMore];
    }
}
