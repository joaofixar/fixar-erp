<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Partner\GetByDocument;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Tools\Partner\Domain\PartnerRepositoryInterface;
use HardnessMcp\Tools\Partner\PartnerDetailsOutput;
use HardnessMcp\Tools\Partner\Search\PartnerSearchOutput;
use HardnessMcp\Tools\Partner\Search\PartnerCandidateReference;
use HardnessMcp\Tools\Presentation\ToolResultPresenter;
use HardnessMcp\Tools\ToolDefinition;
use HardnessMcp\Tools\ToolExecutionResult;
use HardnessMcp\Tools\ToolInterface;
final class PartnerGetByDocumentTool implements ToolInterface
{
    private PartnerRepositoryInterface $repository; private ToolResultPresenter $presenter; private PartnerCandidateReference $references;
    public function __construct(PartnerRepositoryInterface $repository, PartnerCandidateReference $references, ?ToolResultPresenter $presenter=null) { $this->repository=$repository; $this->references=$references; $this->presenter=$presenter??new ToolResultPresenter(); }
    public function getDefinition(): ToolDefinition { return (new PartnerGetByDocumentDefinition())->toToolDefinition(); }
    public function execute(array $arguments, ExecutionContext $context): ToolExecutionResult {
        $document=preg_replace('/\D/', '', (string)($arguments['document']??''))??'';
        if (strlen($document)!==11&&strlen($document)!==14) return $this->presenter->functionalError('partner_document_invalid','Informe um CPF com 11 dígitos ou um CNPJ com 14 dígitos para consultar o cadastro.','tools_call_partner_document_invalid');
        $role=(string)($arguments['role']??'both');
        $candidateRef=(string)($arguments['candidate_ref']??'');
        if ($candidateRef !== '') return $this->selectedPartner($document,$role,$candidateRef,$context);
        try {$partners=$this->repository->searchByDocument($context->getCompanyId(),$document,$role,6);} catch (\Throwable $e) { return $this->presenter->functionalError('partner_lookup_unavailable','Não foi possível localizar o parceiro agora. Tente novamente.','tools_call_partner_lookup_unavailable'); }
        if (count($partners)===1) return $this->partnerDetails((int)($partners[0]['D024_Id']??0),$document,$role,$context);
        if (count($partners)>5) return $this->presenter->functionalError('partner_document_ambiguous','Há mais de um cadastro ativo para este documento. Identifique o cliente antes de criar um orçamento.','tools_call_partner_document_ambiguous');
        $result=PartnerSearchOutput::fromPartners($partners,$context,$this->references,true);
        return $this->presenter->collection($result,'items','cadastro','cadastros',[['path'=>'candidate_ref','label'=>'Referência para selecionar'],['path'=>'legal_name','label'=>'Razão social'],['path'=>'trading_name','label'=>'Fantasia'],['path'=>'city','label'=>'Cidade'],['path'=>'state','label'=>'UF']],'tools_call_partner_document_ambiguous');
    }

    private function selectedPartner(string $document,string $role,string $candidateRef,ExecutionContext $context): ToolExecutionResult {
        $partnerId=$this->references->resolve($candidateRef,$context);
        if ($partnerId===null) return $this->presenter->functionalError('partner_candidate_invalid','A referência do parceiro é inválida, expirou ou pertence a outro contexto. Consulte o documento novamente.','tools_call_partner_candidate_invalid');
        try {$partner=$this->repository->findVisibleById($context->getCompanyId(),$partnerId);} catch (\Throwable $e) { return $this->presenter->functionalError('partner_lookup_unavailable','Não foi possível localizar o parceiro agora. Tente novamente.','tools_call_partner_lookup_unavailable'); }
        if ($partner===null || !$this->matchesDocument($partner,$document) || !$this->matchesRole($partner,$role) || ($partner['D024_Flag_Ativo']??'')!=='S') return $this->presenter->functionalError('partner_not_found','O parceiro selecionado não está disponível para este documento neste contexto.','tools_call_partner_not_found');
        return $this->details($partner);
    }

    private function partnerDetails(int $partnerId,string $document,string $role,ExecutionContext $context): ToolExecutionResult {
        if ($partnerId<=0) return $this->presenter->functionalError('partner_not_found','O parceiro não está disponível neste contexto.','tools_call_partner_not_found');
        try {$partner=$this->repository->findVisibleById($context->getCompanyId(),$partnerId);} catch (\Throwable $e) { return $this->presenter->functionalError('partner_lookup_unavailable','Não foi possível consultar o cadastro agora.','tools_call_partner_lookup_unavailable'); }
        if ($partner===null || !$this->matchesDocument($partner,$document) || !$this->matchesRole($partner,$role) || ($partner['D024_Flag_Ativo']??'')!=='S') return $this->presenter->functionalError('partner_not_found','O parceiro não está disponível neste contexto.','tools_call_partner_not_found');
        return $this->details($partner);
    }

    /** @param array<string,mixed> $partner */
    private function details(array $partner): ToolExecutionResult {
        $out=PartnerDetailsOutput::fromPartner($partner);
        return $this->presenter->record($out,'Dados cadastrais do parceiro',[['path'=>'legal_name','label'=>'Razão social'],['path'=>'document','label'=>'Documento'],['path'=>'code','label'=>'Código'],['path'=>'address.street','label'=>'Endereço'],['path'=>'address.number','label'=>'Número'],['path'=>'address.complement','label'=>'Complemento'],['path'=>'address.neighborhood','label'=>'Bairro'],['path'=>'address.city','label'=>'Cidade'],['path'=>'address.state','label'=>'UF'],['path'=>'address.postal_code','label'=>'CEP']]);
    }

    /** @param array<string,mixed> $partner */
    private function matchesDocument(array $partner,string $document): bool {
        $cpf=preg_replace('/\D/','',(string)($partner['D024_Cpf']??''))??'';
        $cnpj=preg_replace('/\D/','',(string)($partner['D024_Cnpj']??''))??'';
        return $cpf===$document || $cnpj===$document;
    }

    /** @param array<string,mixed> $partner */
    private function matchesRole(array $partner,string $role): bool {
        return $role==='both' || ($role==='customer' && ($partner['D024_Flag_Cliente']??'')==='S') || ($role==='supplier' && ($partner['D024_Flag_Fornecedor']??'')==='S');
    }
}
