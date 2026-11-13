<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Partner\GetByDocument;
use HardnessMcp\Tools\ToolDefinition;
final class PartnerGetByDocumentDefinition
{
    public function toToolDefinition(): ToolDefinition { return new ToolDefinition('hardness.partner.get_by_document', 'Locates active customers or suppliers accessible to the connection by a known CPF or CNPJ only. It does not search by name or trading name. If the document has multiple matches, preserve the returned candidate_ref and submit it with the same document after the user selects a candidate.', ['type'=>'object','properties'=>['document'=>['type'=>'string','minLength'=>11,'maxLength'=>18],'role'=>['type'=>'string','enum'=>['customer','supplier','both']],'candidate_ref'=>['type'=>'string','minLength'=>20,'maxLength'=>512]],'required'=>['document'],'additionalProperties'=>false], ['partner:lookup']); }
}
