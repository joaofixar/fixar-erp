<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Partner\Get;
use HardnessMcp\Context\ExecutionContext; use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface; use HardnessMcp\Tools\Partner\PartnerDetailsOutput; use HardnessMcp\Tools\Partner\Search\PartnerCandidateReference; use HardnessMcp\Tools\Presentation\ToolResultPresenter; use HardnessMcp\Tools\ToolDefinition; use HardnessMcp\Tools\ToolExecutionResult; use HardnessMcp\Tools\ToolInterface;
final class PartnerGetTool implements ToolInterface {
 private PartnerRepositoryInterface $repo; private PartnerCandidateReference $refs; private ToolResultPresenter $presenter;
 public function __construct(PartnerRepositoryInterface $repo,PartnerCandidateReference $refs){$this->repo=$repo;$this->refs=$refs;$this->presenter=new ToolResultPresenter();}
 public function getDefinition():ToolDefinition{return new ToolDefinition('hardness.partner.get','Gets basic registration data for a partner selected from a prior partner search. Use only candidate_ref returned by a partner Tool.',['type'=>'object','properties'=>['candidate_ref'=>['type'=>'string','minLength'=>20,'maxLength'=>512]],'required'=>['candidate_ref'],'additionalProperties'=>false],['partner:search']);}
 public function execute(array $a,ExecutionContext $c):ToolExecutionResult {
  $id=$this->refs->resolve((string)($a['candidate_ref']??''),$c);if($id===null)return $this->presenter->functionalError('partner_candidate_invalid','A referência do parceiro é inválida, expirou ou pertence a outro contexto. Faça uma nova busca.','tools_call_partner_candidate_invalid');
  try{$r=$this->repo->findVisibleById($c->getCompanyId(),$id);}catch(\Throwable $e){return $this->presenter->functionalError('partner_get_unavailable','Não foi possível consultar o cadastro agora.','tools_call_partner_get_unavailable');}if($r===null)return $this->presenter->functionalError('partner_not_found','O parceiro não está disponível neste contexto.','tools_call_partner_not_found');
  $out=PartnerDetailsOutput::fromPartner($r);
  return $this->presenter->record($out,'Dados cadastrais do parceiro',[['path'=>'legal_name','label'=>'Razão social'],['path'=>'document','label'=>'Documento'],['path'=>'code','label'=>'Código'],['path'=>'address.street','label'=>'Endereço'],['path'=>'address.number','label'=>'Número'],['path'=>'address.complement','label'=>'Complemento'],['path'=>'address.neighborhood','label'=>'Bairro'],['path'=>'address.city','label'=>'Cidade'],['path'=>'address.state','label'=>'UF'],['path'=>'address.postal_code','label'=>'CEP']]);
 }
}
