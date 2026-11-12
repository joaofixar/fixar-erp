<?php
namespace hardness;

require_once dirname(__DIR__) . '/base_conhecimento/BaseConhecimentoRepository.php';
require_once __DIR__ . '/OpenAiItemEnricher.php';

class ImportEnrichmentService
{
    const MAX_ITEMS = 300;
    const MAX_REQUEST_BYTES = 204800;
    const TIMEOUT_SECONDS = 120;
    protected $repository;
    protected $enricher;

    public function __construct(BaseConhecimentoRepository $repository = null, OpenAiItemEnricher $enricher = null)
    { $this->repository = $repository ?: new BaseConhecimentoRepository(); $this->enricher = $enricher ?: new OpenAiItemEnricher(); }

    public function enrich($previewPath, $workDir, &$result = array())
    {
        $startedAt = microtime(true);
        $result = array('status' => 'failed', 'message' => '');
        $enrichedPath = rtrim($workDir, '/') . '/import_preview_enriched.json';
        $diagPath = rtrim($workDir, '/') . '/enrichment_diag.json';
        @unlink($enrichedPath); @unlink($diagPath);
        $raw = @file_get_contents($previewPath); $preview = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($preview) || !isset($preview['items']) || !is_array($preview['items'])) return $this->fail($enrichedPath, $diagPath, '', array(), 'preview_invalid', 'Preview de importação inválido.', $result, '', $startedAt);
        $previewHash = hash('sha256', $raw); $items = $preview['items'];
        $knowledgeError = ''; $snapshot = $this->repository->getActiveSnapshot($knowledgeError);
        if (empty($snapshot)) { $result = array('status' => 'not_configured', 'message' => $knowledgeError); return false; }
        if (count($items) > self::MAX_ITEMS) return $this->fail($enrichedPath, $diagPath, $previewHash, $snapshot, 'limit_exceeded', 'Quantidade de itens excede o limite do enriquecimento.', $result, '', $startedAt, count($items));
        $input = array(); foreach ($items as $index => $item) { $input[] = array('source_index' => $index, 'sequence' => isset($item['sequence']) ? $item['sequence'] : null, 'page' => isset($item['page']) ? $item['page'] : null, 'rawText' => isset($item['rawText']) ? $item['rawText'] : '', 'codeClient' => isset($item['codeClient']) ? $item['codeClient'] : '', 'description' => isset($item['description']) ? $item['description'] : '', 'quantity' => isset($item['quantity']) ? $item['quantity'] : null, 'unit' => isset($item['unit']) ? $item['unit'] : ''); }
        if (strlen(json_encode(array('items'=>$input,'knowledge'=>$snapshot['content']))) > self::MAX_REQUEST_BYTES) return $this->fail($enrichedPath, $diagPath, $previewHash, $snapshot, 'limit_exceeded', 'Conteúdo excede o limite operacional da chamada de enriquecimento.', $result, '', $startedAt, count($items));
        global $g; $key = isset($g['C031']['openAIApiKey']) ? trim((string)$g['C031']['openAIApiKey']) : '';
        if ($key === '') return $this->fail($enrichedPath, $diagPath, $previewHash, $snapshot, 'authentication', 'Chave da IA não configurada.', $result, '', $startedAt, count($items));
        $model = getenv('IMPORTADOR_ENRICHMENT_IA_MODEL'); if (!$model) $model = getenv('IMPORTADOR_IA_MODEL'); if (!$model) $model = 'gpt-4.1-mini';
        $error = ''; $response = $this->enricher->enrich($input, $snapshot, $key, $model, self::TIMEOUT_SECONDS, $error);
        $received = is_array($response) && isset($response['items']) && is_array($response['items']) ? count($response['items']) : null;
        $validated = $response !== null ? $this->validate($response, count($input), $error) : null;
        if ($validated === null) return $this->fail($enrichedPath, $diagPath, $previewHash, $snapshot, 'response_invalid', $error, $result, $model, $startedAt, count($items), $received);
        $payload = array('schema_version'=>1,'status'=>$this->overallStatus($validated),'preview_sha256'=>$previewHash,'knowledge_document_sha256'=>$snapshot['document_sha256'],'knowledge_snapshot_schema_version'=>$snapshot['snapshot_schema_version'],'model'=>$model,'created_at'=>date('c'),'items'=>$validated);
        $diag = array('status'=>$payload['status'],'model'=>$model,'preview_sha256'=>$previewHash,'knowledge_document_sha256'=>$snapshot['document_sha256'],'created_at'=>date('c'),'duration_ms'=>(int)round((microtime(true)-$startedAt)*1000),'error_type'=>'','message'=>'','expected_items'=>count($items),'received_items'=>$received);
        if (!$this->writeJson($enrichedPath, $payload, $error) || !$this->writeJson($diagPath, $diag, $error)) { $result=array('status'=>'failed','message'=>'Não foi possível gravar os artefatos de enriquecimento.'); return false; }
        $result = array('status'=>$payload['status'],'message'=>'Enriquecimento concluído.','path'=>$enrichedPath); return true;
    }
    public function validateSubset($response, array $expectedIndexes, $contextItemCount, &$error = '') { return $this->validate($response, count($expectedIndexes), $error, $expectedIndexes, $contextItemCount); }
    protected function validate($response, $count, &$error, $expectedIndexes = null, $contextItemCount = null) {
        $error=''; if (!isset($response['items']) || !is_array($response['items']) || count($response['items'])!==$count) { $error='Resposta de enriquecimento com quantidade de itens inválida.'; return null; }
        $out=array();
        foreach ($response['items'] as $expected=>$item) {
            $expectedIndex = is_array($expectedIndexes) && array_key_exists($expected, $expectedIndexes) ? (int) $expectedIndexes[$expected] : $expected;
            $inheritanceLimit = $contextItemCount === null ? $count : (int) $contextItemCount;
            if (!is_array($item) || !isset($item['source_index']) || !is_int($item['source_index']) || $item['source_index']!==$expectedIndex || !isset($item['search_description']) || !is_string($item['search_description']) || !isset($item['attributes']) || !is_array($item['attributes']) || !isset($item['status']) || !in_array($item['status'],array('enriched','attention'),true)) { $error='Resposta de enriquecimento com vínculo, ordem ou conteúdo inválidos.'; return null; }
            foreach($item as $field=>$value) if($this->isForbiddenCatalogField($field)) { $error='Resposta de enriquecimento contém decisão de catálogo inválida.'; return null; }
            foreach(array('aliases','relevant_terms','pending_confirmations','contradictions') as $field) { if(!isset($item[$field]) || !$this->isStringArray($item[$field])) { $error='Resposta de enriquecimento com listas inválidas.'; return null; } }
            if (!isset($item['evidence']) || !$this->normalizeEvidence($item['evidence'])) { $error='Resposta de enriquecimento com evidence inválido.'; return null; }
            if (!isset($item['confidence']) || !$this->isConfidence($item['confidence'])) { $error='Resposta de enriquecimento com confiança inválida.'; return null; }
            foreach($item['attributes'] as $attributeName=>&$attribute) {
                if ($this->isForbiddenCatalogField($attributeName)) { $error=$this->attributeError($item['source_index'],$attributeName,'nome','atributo permitido','campo de catálogo'); return null; }
                if (!is_array($attribute)) { $error=$this->attributeError($item['source_index'],$attributeName,'atributo','objeto',$attribute); return null; }
                if (!array_key_exists('value',$attribute)) { $error=$this->attributeError($item['source_index'],$attributeName,'value','campo obrigatório','ausente'); return null; }
                if (!isset($attribute['origin']) || !in_array($attribute['origin'],array('markdown','calculated','inherited','assumed','ai_inferred'),true)) { $error=$this->attributeError($item['source_index'],$attributeName,'origin','markdown|calculated|inherited|assumed|ai_inferred',isset($attribute['origin'])?$attribute['origin']:'ausente'); return null; }
                if (!isset($attribute['confidence']) || !$this->isConfidence($attribute['confidence'])) { $error=$this->attributeError($item['source_index'],$attributeName,'confidence','número entre 0 e 1',isset($attribute['confidence'])?$attribute['confidence']:'ausente'); return null; }
                if (!isset($attribute['requires_confirmation']) || !is_bool($attribute['requires_confirmation'])) { $error=$this->attributeError($item['source_index'],$attributeName,'requires_confirmation','booleano',isset($attribute['requires_confirmation'])?$attribute['requires_confirmation']:'ausente'); return null; }
                if (!isset($attribute['evidence']) || !$this->normalizeEvidence($attribute['evidence'])) { $error=$this->attributeError($item['source_index'],$attributeName,'evidence','string ou array de strings',isset($attribute['evidence'])?$attribute['evidence']:'ausente'); return null; }
                if($attribute['origin']==='inherited' && (!isset($attribute['source_item_index']) || !is_int($attribute['source_item_index']) || $attribute['source_item_index']<0 || $attribute['source_item_index']>=$inheritanceLimit)) { $error=$this->attributeError($item['source_index'],$attributeName,'source_item_index','inteiro de item existente',isset($attribute['source_item_index'])?$attribute['source_item_index']:'ausente'); return null; }
            }
            unset($attribute);
            $out[]=$item;
        } return $out; }
    protected function overallStatus($items) { foreach($items as $item) if($item['status']==='attention') return 'partial'; return 'success'; }
    protected function fail($path,$diagPath,$previewHash,$snapshot,$errorType,$message,&$result,$model='',$startedAt=null,$expected=null,$received=null) { $payload=array('schema_version'=>1,'status'=>'failed','preview_sha256'=>$previewHash,'knowledge_document_sha256'=>isset($snapshot['document_sha256'])?$snapshot['document_sha256']:'','knowledge_snapshot_schema_version'=>isset($snapshot['snapshot_schema_version'])?$snapshot['snapshot_schema_version']:1,'model'=>$model,'created_at'=>date('c'),'items'=>array()); $artifactError=''; $artifactWritten=$this->writeJson($path,$payload,$artifactError); $diagError=''; $diagWritten=$this->writeJson($diagPath,array('status'=>'failed','model'=>$model,'preview_sha256'=>$previewHash,'knowledge_document_sha256'=>isset($snapshot['document_sha256'])?$snapshot['document_sha256']:'','created_at'=>date('c'),'duration_ms'=>$startedAt===null?0:(int)round((microtime(true)-$startedAt)*1000),'error_type'=>$errorType,'message'=>$message,'expected_items'=>$expected,'received_items'=>$received),$diagError); $result=array('status'=>'failed','message'=>$artifactWritten&&$diagWritten?$message:'Não foi possível gravar os artefatos de diagnóstico do enriquecimento.'); return false; }
    protected function writeJson($path,array $data,&$error='') { $error=''; $dir=dirname($path); $tmp=@tempnam($dir,'.tmp-enrichment-'); if($tmp===false){$error='Falha ao preparar gravação.';return false;} $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); if(!is_string($json)||@file_put_contents($tmp,$json,LOCK_EX)===false){@unlink($tmp);$error='Falha ao gravar artefato.';return false;} if(!@rename($tmp,$path)){@unlink($tmp);$error='Falha ao publicar artefato.';return false;} return true; }
    protected function isStringArray($value) { if(!is_array($value)) return false; foreach($value as $v) if(!is_string($v)) return false; return true; }
    protected function normalizeEvidence(&$value) { if(is_string($value)){ $value=array($value); return true; } return $this->isStringArray($value); }
    protected function isConfidence($value) { return is_numeric($value) && is_finite((float)$value) && (float)$value>=0 && (float)$value<=1; }
    protected function isForbiddenCatalogField($field) { return in_array(strtolower((string)$field),array('product','produto','produto_sugerido','suggested_product','product_suggestion','d001_id','codigo_produto','product_code','d001_codigo_produto','catalog_product','catalog_decision','final_catalog_decision','decisao_catalogo','decisao_final_catalogo'),true); }
    protected function attributeError($sourceIndex,$attributeName,$field,$expected,$received) { return 'Item ' . ((int)$sourceIndex + 1) . ' (source_index ' . (int)$sourceIndex . '), atributo “' . (string)$attributeName . '”, campo “' . (string)$field . '” inválido: esperado ' . (string)$expected . ', recebido ' . $this->valueType($received) . '.'; }
    protected function valueType($value) { if($value==='ausente') return 'ausente'; if(is_array($value)) return 'array'; if(is_bool($value)) return 'booleano'; if(is_int($value)) return 'inteiro'; if(is_float($value)) return 'número'; if(is_null($value)) return 'null'; return 'string'; }
}
