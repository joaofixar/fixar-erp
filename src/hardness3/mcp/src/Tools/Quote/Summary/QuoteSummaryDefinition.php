<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Quote\Summary;

use HardnessMcp\Tools\ToolDefinition;

final class QuoteSummaryDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        $groups=['day','week','month','customer','seller_internal','supervisor','status','document'];
        return new ToolDefinition('hardness.quote.summary', 'Returns quote reports. Use document to list individual quotes with issue date, customer, customer group, proposal customer when available, status, and total; other groupings return totals, trends, and rankings. A supervisor is resolved from the internal seller hierarchy. Set supervisor_id together with group_by seller_internal to list that supervisor’s sellers. Set origin to assistant_ia to report only quotes created by the Assistant IA through MCP. Historical summaries grouped by month, customer, internal seller, supervisor, or status accept up to 5 years. Weekly grouping and document listings accept up to 12 months; daily grouping accepts up to 90 days. Rankings and document listings return at most 50 rows. It never returns product analysis. Never split a period longer than these limits into sequential calls: explain the applicable interactive limit and request one allowed period instead.', ['type'=>'object','properties'=>['date_from'=>['type'=>'string','minLength'=>10,'maxLength'=>10],'date_to'=>['type'=>'string','minLength'=>10,'maxLength'=>10],'group_by'=>['type'=>'string','enum'=>$groups],'customer_id'=>['type'=>'integer','minimum'=>1],'seller_internal_id'=>['type'=>'integer','minimum'=>1],'supervisor_id'=>['type'=>'integer','minimum'=>1],'status'=>['type'=>'string','enum'=>['P','F','C','V','A','N','L']],'origin'=>['type'=>'string','enum'=>['assistant_ia']],'limit'=>['type'=>'integer','minimum'=>1,'maximum'=>50]],'required'=>['date_from','date_to','group_by'],'additionalProperties'=>false],['quote:report:read']);
    }
}
