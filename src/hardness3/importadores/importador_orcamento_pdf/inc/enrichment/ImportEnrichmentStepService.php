<?php
namespace hardness;
require_once __DIR__ . '/ImportEnrichmentService.php';

class ImportEnrichmentStepService
{
    // Mantido em dois itens enquanto o contrato de enriquecimento é validado
    // contra os resultados já aprovados pelo cliente.
    const BATCH_SIZE = 2;
    const PARALLEL_CALLS = 3;
    const TIMEOUT_SECONDS = 45;
    protected $repository; protected $enricher; protected $validator;
    public function __construct(BaseConhecimentoRepository $repository = null, OpenAiItemEnricher $enricher = null) { $this->repository=$repository?:new BaseConhecimentoRepository();$this->enricher=$enricher?:new OpenAiItemEnricher();$this->validator=new ImportEnrichmentService($this->repository,$this->enricher); }
    public function start($previewPath,$workDir,&$result=array()) {
        $preview=$this->loadPreview($previewPath,$raw,$error); if(empty($preview)) return $this->failStart($error,$result);
        $snapshotError='';$snapshot=$this->repository->getActiveSnapshot($snapshotError);if(empty($snapshot))return $this->failStart($snapshotError?:'Não existe Base de Conhecimento ativa.',$result);
        @unlink($this->enrichedPath($workDir));@unlink($this->diagPath($workDir));
        $now=date('c');$state=array('status'=>'processing','preview_sha256'=>hash('sha256',$raw),'knowledge_document_sha256'=>$snapshot['document_sha256'],'knowledge_snapshot_schema_version'=>$snapshot['snapshot_schema_version']??1,'total_items'=>count($preview['items']),'next_index'=>0,'batch_size'=>self::BATCH_SIZE,'parallel_calls'=>$this->parallelCalls(),'items'=>array(),'valid_count'=>0,'attention_count'=>0,'fallback_indices'=>array(),'errors'=>array(),'calls'=>0,'call_metrics'=>array(),'model'=>'','started_at'=>$now,'updated_at'=>$now,'duration_ms'=>0,'message'=>'Aplicando Base de Conhecimento.');
        if(!$this->writeJson($this->statePath($workDir),$state,$error))return $this->failStart($error,$result);$result=array('ok'=>true,'state'=>$state);return true;
    }
    public function process($previewPath,$workDir,$apiKey,&$result=array()) {
        $state=$this->readJson($this->statePath($workDir));if(empty($state)||($state['status']??'')!=='processing'){ $result=array('ok'=>false,'done'=>true,'erro'=>'Estado do enriquecimento inválido.','message'=>'Estado do enriquecimento inválido.');return false; }
        $preview=$this->loadPreview($previewPath,$raw,$error);if(empty($preview)||!hash_equals((string)$state['preview_sha256'],hash('sha256',$raw)))return $this->finishGlobal($workDir,$state,$preview,'O preview atual não confere com o processamento iniciado.',$result);
        $snapshotError='';$snapshot=$this->repository->getActiveSnapshot($snapshotError);if(empty($snapshot)||!hash_equals((string)$state['knowledge_document_sha256'],(string)($snapshot['document_sha256']??'')))return $this->finishGlobal($workDir,$state,$preview,'A Base de Conhecimento ativa foi alterada durante o processamento.',$result);
        if(trim((string)$apiKey)==='')return $this->finishGlobal($workDir,$state,$preview,'Chave da IA não configurada.',$result);
        $context=$this->buildContext($preview['items']);$from=(int)$state['next_index'];$batchSize=max(1,(int)($state['batch_size']??self::BATCH_SIZE));$groups=array();
        for($slot=0;$slot<$this->parallelCalls();$slot++){ $indexes=array_slice(range(0,count($context)-1),$from+($slot*$batchSize),$batchSize);if(empty($indexes))break;$groups[]=$indexes; }
        if(empty($groups))return $this->finish($workDir,$state,$preview,$result);
        $model=getenv('IMPORTADOR_ENRICHMENT_IA_MODEL')?:getenv('IMPORTADOR_IA_MODEL');$state['model']=$model?:'gpt-4.1-mini';$started=microtime(true);$batches=$this->enricher->enrichSubsetsParallel($context,$groups,$snapshot,$apiKey,$state['model'],self::TIMEOUT_SECONDS);$state['duration_ms']+=(int)round((microtime(true)-$started)*1000);$state['updated_at']=date('c');
        foreach($groups as $groupIndex=>$indexes){$batch=isset($batches[$groupIndex])&&is_array($batches[$groupIndex])?$batches[$groupIndex]:array('response'=>null,'metrics'=>array(),'error'=>'Falha técnica ao processar o lote.','duration_ms'=>0);$this->applyEnrichmentBatch($state,$indexes,$batch,$preview['items'],$context);}
        $state['next_index']=$from+array_sum(array_map('count',$groups));$state['message']='Lotes concluídos.';
        if($state['next_index']>=count($context))return $this->finish($workDir,$state,$preview,$result);
        if(!$this->writeJson($this->statePath($workDir),$state,$error))return $this->finishGlobal($workDir,$state,$preview,$error,$result);
        $result=array_merge(array('ok'=>true,'done'=>false),$this->progress($state));return true;
    }
    protected function parallelCalls(){ $value=(int)getenv('IMPORTADOR_ENRICHMENT_PARALLEL_CALLS');if($value<=0)$value=self::PARALLEL_CALLS;return max(1,min(3,$value)); }
    protected function applyEnrichmentBatch(array &$state,array $indexes,array $batch,array $previewItems,array $context){
        $response=$batch['response']??null;$metrics=isset($batch['metrics'])&&is_array($batch['metrics'])?$batch['metrics']:array();$callError=(string)($batch['error']??'');$state['calls']++;if(!isset($state['call_metrics'])||!is_array($state['call_metrics']))$state['call_metrics']=array();$state['call_metrics'][]=array('call'=>(int)$state['calls'],'source_indexes'=>array_values($indexes),'duration_ms'=>(int)($batch['duration_ms']??0),'prompt_bytes'=>(int)($metrics['prompt_bytes']??0),'response_bytes'=>(int)($metrics['response_bytes']??0),'http'=>isset($metrics['http'])&&is_array($metrics['http'])?$metrics['http']:array(),'usage'=>isset($metrics['usage'])&&is_array($metrics['usage'])?$metrics['usage']:array(),'success'=>$response!==null,'error'=>$response===null?($callError!==''?$callError:'Falha técnica ou resposta inválida no lote.'):'' );
        if($response===null||!isset($response['items'])||!is_array($response['items'])||count($response['items'])!==count($indexes)){ $this->appendFallbacks($state,$previewItems,$indexes,$callError?:'Falha técnica ou resposta inválida no lote.');return; }
        foreach($indexes as $position=>$index){$repairMeta=array();$itemResponse=$this->repairItemResponse($response['items'][$position],$index,$context,$repairMeta);if(!empty($repairMeta)){$metricIndex=count($state['call_metrics'])-1;if($metricIndex>=0){if(!isset($state['call_metrics'][$metricIndex]['repairs'])||!is_array($state['call_metrics'][$metricIndex]['repairs']))$state['call_metrics'][$metricIndex]['repairs']=array();$state['call_metrics'][$metricIndex]['repairs'][]=array('source_index'=>(int)$index,'discarded_fields'=>array_values($repairMeta));}}$itemError='';$valid=$this->validator->validateSubset(array('items'=>array($itemResponse)),array($index),count($context),$itemError);if($valid===null)$this->appendFallbacks($state,$previewItems,array($index),$itemError?:'Item inválido no lote.');else{$state['items'][]=$valid[0];$state['valid_count']++;if(($valid[0]['status']??'')==='attention')$state['attention_count']++;}}
    }
    protected function repairItemResponse($itemResponse,$sourceIndex,array $context,array &$repairMeta=array())
    {
        $repairMeta=array();
        if (!is_array($itemResponse)) {
            return $itemResponse;
        }
        $itemResponse = $this->normalizeItemResponse($itemResponse);
        // source_item_index é metadado de um atributo inherited, nunca um
        // atributo técnico por si só. A IA às vezes o devolve indevidamente
        // dentro de attributes; descartar apenas esse escalar preserva a
        // validação rigorosa dos atributos reais.
        if (isset($itemResponse['attributes']) && is_array($itemResponse['attributes'])
            && array_key_exists('source_item_index',$itemResponse['attributes'])
            && !is_array($itemResponse['attributes']['source_item_index'])) {
            unset($itemResponse['attributes']['source_item_index']);
            $repairMeta[]='attributes.source_item_index';
        }
        $sourceIndex = (int) $sourceIndex;
        $anchorIndex = $this->findAnchorIndex($sourceIndex, $context);
        if (isset($itemResponse['attributes']) && is_array($itemResponse['attributes'])) {
            foreach ($itemResponse['attributes'] as &$attribute) {
                if (!is_array($attribute) || ($attribute['origin'] ?? '') !== 'inherited') {
                    continue;
                }
                if (!isset($attribute['source_item_index']) || !is_int($attribute['source_item_index'])) {
                    $attribute['source_item_index'] = $anchorIndex;
                }
            }
            unset($attribute);
        }

        $itemDimensions = $this->crossSection(isset($itemResponse['attributes']['dimensions']['value']) ? $itemResponse['attributes']['dimensions']['value'] : '');
        $anchorDimensions = $this->crossSection(isset($context[$anchorIndex]['description']) ? $context[$anchorIndex]['description'] : '');
        if ($itemDimensions !== '' && $itemDimensions === $anchorDimensions) {
            $itemResponse['contradictions'] = $this->removeRepeatedDimensionAlerts($itemResponse['contradictions'] ?? array());
            $itemResponse['pending_confirmations'] = $this->removeMeasureConfirmationAlerts($itemResponse['pending_confirmations'] ?? array());
        }

        $thickness = isset($itemResponse['attributes']['thickness']['value']) ? trim((string) $itemResponse['attributes']['thickness']['value']) : '';
        $originalDescription = isset($context[$sourceIndex]['description']) ? (string) $context[$sourceIndex]['description'] : '';
        if ($thickness !== '' && stripos($originalDescription, $thickness) !== false) {
            $itemResponse['pending_confirmations'] = $this->removeThicknessConfirmationAlerts($itemResponse['pending_confirmations'] ?? array());
        }
        $this->clearExplicitAttributeConfirmations($itemResponse, $originalDescription);
        $itemResponse['search_description'] = $this->completeSearchDescription(
            isset($itemResponse['search_description']) ? $itemResponse['search_description'] : '',
            isset($itemResponse['attributes']) && is_array($itemResponse['attributes']) ? $itemResponse['attributes'] : array()
        );
        $itemResponse['search_description'] = $this->preserveExplicitTechnicalTokens(
            $itemResponse['search_description'],
            $originalDescription
        );
        if (!empty($itemResponse['pending_confirmations']) || !empty($itemResponse['contradictions'])) {
            $itemResponse['status'] = 'attention';
        }
        if (($itemResponse['status'] ?? '') === 'attention'
            && empty($itemResponse['pending_confirmations'])
            && empty($itemResponse['contradictions'])
            && empty($itemResponse['enrichment_error'])) {
            $itemResponse['status'] = 'enriched';
        }
        return $itemResponse;
    }

    protected function completeSearchDescription($description, array $attributes)
    {
        $parts = array();
        $seen = array();
        $descriptionNormalized = $this->normalizeDescriptionPart($description);
        $description = trim((string) $description);
        if ($description !== '') {
            $parts[] = $description;
            $seen[$descriptionNormalized] = true;
        }
        foreach ($attributes as $attributeName => $attribute) {
            if (!$this->isSearchAttribute($attributeName, $attribute)) {
                continue;
            }
            $part = (string) $attribute['value'];
            $part = trim((string) $part);
            $normalized = $this->normalizeDescriptionPart($part);
            if ($normalized === '' || isset($seen[$normalized])
                || ($descriptionNormalized !== '' && $this->containsDescriptionPart($descriptionNormalized, $normalized))) {
                continue;
            }
            $seen[$normalized] = true;
            $parts[] = $part;
        }
        return trim(implode(' - ', $parts));
    }

    protected function containsDescriptionPart($description, $part)
    {
        if (preg_match('/^\d+$/', (string) $part)) {
            return (bool) preg_match('/(?<!\d)' . preg_quote((string) $part, '/') . '(?!\d)/u', (string) $description);
        }
        return (bool) preg_match('/(?:^|\s)' . preg_quote((string) $part, '/') . '(?:\s|$)/u', (string) $description);
    }

    protected function clearExplicitAttributeConfirmations(array &$itemResponse, $sourceDescription)
    {
        if (!isset($itemResponse['attributes']) || !is_array($itemResponse['attributes'])) {
            return;
        }
        $pending = isset($itemResponse['pending_confirmations']) && is_array($itemResponse['pending_confirmations'])
            ? $itemResponse['pending_confirmations'] : array();
        foreach ($itemResponse['attributes'] as $name => &$attribute) {
            if (!is_array($attribute) || empty($attribute['requires_confirmation']) || !isset($attribute['value'])
                || !(is_string($attribute['value']) || is_int($attribute['value']) || is_float($attribute['value']))) {
                continue;
            }
            $value = trim((string) $attribute['value']);
            if ($value === '' || !$this->attributeValueAppearsInSource($sourceDescription, $value)) {
                continue;
            }
            $attribute['requires_confirmation'] = false;
            $pending = array_values(array_filter($pending, function ($pendingItem) use ($name, $value) {
                $pendingNormalized = $this->normalizeDescriptionPart($pendingItem);
                $nameNormalized = $this->normalizeDescriptionPart($name);
                $valueNormalized = $this->normalizeDescriptionPart($value);
                return ($nameNormalized === '' || strpos($pendingNormalized, $nameNormalized) === false)
                    && ($valueNormalized === '' || strpos($pendingNormalized, $valueNormalized) === false);
            }));
        }
        unset($attribute);
        $itemResponse['pending_confirmations'] = $pending;
    }

    protected function attributeValueAppearsInSource($sourceDescription, $value)
    {
        $sourceNormalized = $this->normalizeDescriptionPart($sourceDescription);
        $valueNormalized = $this->normalizeDescriptionPart($value);
        if ($sourceNormalized === '' || $valueNormalized === '') {
            return false;
        }
        if (preg_match('/^\d+$/', $valueNormalized)) {
            return (bool) preg_match('/(?<!\d)' . preg_quote($valueNormalized, '/') . '(?!\d)/u', $sourceNormalized);
        }
        return $this->containsDescriptionPart($sourceNormalized, $valueNormalized);
    }

    protected function isSearchAttribute($name, $attribute)
    {
        if (!is_array($attribute) || !isset($attribute['value'])
            || !(is_string($attribute['value']) || is_int($attribute['value']) || is_float($attribute['value']))) {
            return false;
        }
        return !preg_match('/(?:^|_)(?:quantity|qty|unit|code|index|sequence|line)(?:$|_)/i', (string) $name);
    }

    protected function normalizeItemResponse(array $item)
    {
        foreach (array('aliases', 'relevant_terms', 'pending_confirmations', 'contradictions', 'evidence') as $field) {
            if (array_key_exists($field, $item)) {
                $item[$field] = $this->normalizeTextList($item[$field]);
            }
        }
        if (isset($item['attributes']) && is_array($item['attributes'])) {
            foreach ($item['attributes'] as &$attribute) {
                if (!is_array($attribute)) {
                    continue;
                }
                if (!isset($attribute['origin']) || !in_array($attribute['origin'], array('markdown', 'calculated', 'inherited', 'assumed', 'ai_inferred'), true)) {
                    $attribute['origin'] = 'ai_inferred';
                }
                if (isset($attribute['evidence'])) {
                    $attribute['evidence'] = $this->normalizeTextList($attribute['evidence']);
                }
                if (($attribute['origin'] ?? '') === 'inherited' && isset($attribute['source_item_index']) && is_numeric($attribute['source_item_index'])) {
                    $attribute['source_item_index'] = (int) $attribute['source_item_index'];
                }
            }
            unset($attribute);
        }
        return $item;
    }

    protected function normalizeTextList($value)
    {
        if (is_string($value)) {
            $value = array($value);
        }
        if (!is_array($value)) {
            return array();
        }
        $out = array();
        $walk = function ($entry) use (&$walk, &$out) {
            if (is_string($entry) || is_numeric($entry)) {
                $entry = trim((string) $entry);
                if ($entry !== '') {
                    $out[] = $entry;
                }
                return;
            }
            if (is_array($entry)) {
                foreach ($entry as $nested) {
                    $walk($nested);
                }
            }
        };
        $walk($value);
        return array_values(array_unique($out));
    }

    protected function normalizeDescriptionPart($value)
    {
        $value = strtoupper(trim((string) $value));
        $value = preg_replace('/[^[:alnum:]]+/u', ' ', $value);
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    protected function preserveExplicitTechnicalTokens($searchDescription, $sourceDescription)
    {
        $searchDescription = trim((string) $searchDescription);
        $tokens = $this->extractExplicitTechnicalTokens($sourceDescription);
        foreach ($tokens as $token) {
            if (!$this->technicalTokenIsPresent($searchDescription, $token)) {
                $searchDescription .= ($searchDescription === '' ? '' : ' - ') . $token;
            }
        }
        return $searchDescription;
    }

    protected function extractExplicitTechnicalTokens($description)
    {
        $patterns = array(
            '/#\s*\d+(?:\s*\/\s*\d+)?/u',
            '/\b\d+(?:[.,]\d+)?\s*[x×]\s*\d+(?:[.,]\d+)?(?:\s*[x×]\s*\d+(?:[.,]\d+)?)?\b/iu',
            '/\b\d+(?:[.,]\d+)?\s*(?:°|º|%)/u',
            '/\b\d+(?:[.,]\d+)?\s*(?:mm|cm|km|m2|m³|m3|kg|g|mg|kv|kw|kva|va|volts?|v|amp(?:eres?)?|a|hz|rpm|pol(?:egadas?)?|in)\b/iu',
            '/\b(?:ip|nema|m)\s*\d{1,4}\b/iu',
        );
        $tokens = array();
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, (string) $description, $matches)) {
                foreach ($matches[0] as $match) {
                    $match = trim((string) $match);
                    $key = $this->normalizeDescriptionPart($match);
                    if ($key !== '') {
                        $tokens[$key] = $match;
                    }
                }
            }
        }
        return array_values($tokens);
    }

    protected function technicalTokenIsPresent($description, $token)
    {
        $descriptionNormalized = $this->normalizeDescriptionPart($description);
        $tokenNormalized = $this->normalizeDescriptionPart($token);
        if ($descriptionNormalized === '' || $tokenNormalized === '') {
            return false;
        }
        if (preg_match('/^\d+(?:\s+\d+)*$/', $tokenNormalized)) {
            return (bool) preg_match('/(?<!\d)' . preg_quote(str_replace(' ', '\\s+', $tokenNormalized), '/') . '(?!\d)/u', $descriptionNormalized);
        }
        return $this->containsDescriptionPart($descriptionNormalized, $tokenNormalized);
    }

    protected function findAnchorIndex($sourceIndex, array $context)
    {
        for ($index = (int) $sourceIndex - 1; $index >= 0; $index--) {
            if (!$this->isAccessoryDescription(isset($context[$index]['description']) ? $context[$index]['description'] : '')) {
                return $index;
            }
        }
        foreach ($context as $index => $item) {
            if (!$this->isAccessoryDescription(isset($item['description']) ? $item['description'] : '')) {
                return (int) $index;
            }
        }
        return max(0, (int) $sourceIndex);
    }

    protected function isAccessoryDescription($description)
    {
        $description = trim((string) $description);
        if ($description === '') {
            return true;
        }
        return (bool) preg_match('/\b(curva|t[eê]|cruzeta|redu[cç][aã]o|emenda|sa[ií]da|terminal|suporte|fixador)\b/iu', $description);
    }

    protected function crossSection($value)
    {
        if (!preg_match('/(\d{1,4})\s*[xX]\s*(\d{1,4})/', (string) $value, $matches)) {
            return '';
        }
        return (int) $matches[1] . 'X' . (int) $matches[2];
    }

    protected function removeRepeatedDimensionAlerts($alerts)
    {
        if (!is_array($alerts)) {
            return array();
        }
        return array_values(array_filter($alerts, function ($alert) {
            preg_match_all('/\d{1,4}\s*[xX]\s*\d{1,4}/', (string) $alert, $matches);
            $dimensions = array_unique(array_map(function ($value) { return preg_replace('/\s+/', '', strtoupper($value)); }, $matches[0]));
            return count($matches[0]) < 2 || count($dimensions) > 1;
        }));
    }

    protected function removeMeasureConfirmationAlerts($alerts)
    {
        if (!is_array($alerts)) {
            return array();
        }
        return array_values(array_filter($alerts, function ($alert) {
            return !preg_match('/confirmar.*medida|medida.*compatib/i', (string) $alert);
        }));
    }

    protected function removeThicknessConfirmationAlerts($alerts)
    {
        if (!is_array($alerts)) {
            return array();
        }
        return array_values(array_filter($alerts, function ($alert) {
            return stripos((string) $alert, 'espessura') === false
                && stripos((string) $alert, 'thickness') === false;
        }));
    }

    protected function appendFallbacks(&$state,array $originalItems,array $indexes,$message){foreach($indexes as $index){$state['items'][]=$this->fallback($index,$originalItems[$index]??array(),$message);$state['fallback_indices'][]=$index;$state['errors'][]=array('indexes'=>array($index),'message'=>$message);} }
    protected function fallback($index,array $original,$message){return array('source_index'=>(int)$index,'search_description'=>isset($original['description'])?(string)$original['description']:'','attributes'=>array(),'aliases'=>array(),'relevant_terms'=>array(),'pending_confirmations'=>array('Enriquecimento não disponível; usar os dados originais.'),'contradictions'=>array(),'evidence'=>array(),'confidence'=>0,'status'=>'attention','enrichment_error'=>(string)$message);}
    protected function finishGlobal($workDir,array $state,array $preview,$message,&$result){$remaining=array();for($i=(int)$state['next_index'];$i<(int)$state['total_items'];$i++)$remaining[]=$i;$this->appendFallbacks($state,$preview['items']??array(),$remaining,$message);$state['next_index']=$state['total_items'];return $this->finish($workDir,$state,$preview,$result,$message);}
    protected function finish($workDir,array $state,array $preview,&$result,$globalMessage=''){
        usort($state['items'],function($a,$b){return (int)$a['source_index']<=>(int)$b['source_index'];});$status=(int)$state['valid_count']===0?'failed':((!empty($state['fallback_indices'])||(int)$state['attention_count']>0)?'partial':'success');$now=date('c');$payload=array('schema_version'=>1,'status'=>$status,'preview_sha256'=>$state['preview_sha256'],'knowledge_document_sha256'=>$state['knowledge_document_sha256'],'knowledge_snapshot_schema_version'=>$state['knowledge_snapshot_schema_version']??1,'model'=>$state['model'],'created_at'=>$now,'items'=>$state['items']);$diag=array('status'=>$status,'model'=>$state['model'],'preview_sha256'=>$state['preview_sha256'],'knowledge_document_sha256'=>$state['knowledge_document_sha256'],'created_at'=>$now,'duration_ms'=>(int)$state['duration_ms'],'total_items'=>(int)$state['total_items'],'enriched_items'=>(int)$state['valid_count'],'fallback_items'=>count($state['fallback_indices']),'fallback_indexes'=>array_values($state['fallback_indices']),'errors'=>$state['errors'],'calls'=>(int)$state['calls'],'batch_size'=>(int)($state['batch_size']??self::BATCH_SIZE),'parallel_calls'=>(int)($state['parallel_calls']??self::PARALLEL_CALLS),'call_metrics'=>isset($state['call_metrics'])&&is_array($state['call_metrics'])?$state['call_metrics']:array());$error='';
        if(!$this->writeJson($this->enrichedPath($workDir),$payload,$error)||!$this->writeJson($this->diagPath($workDir),$diag,$error)){ $result=array('ok'=>false,'done'=>true,'erro'=>'Não foi possível gravar os artefatos de enriquecimento.','message'=>'Não foi possível gravar os artefatos de enriquecimento.');return false; }
        @unlink($this->statePath($workDir));$result=array_merge(array('ok'=>$status!=='failed','done'=>true,'status'=>$status),$this->progress($state));if($globalMessage!==''){$result['erro']=$globalMessage;$result['message']=$globalMessage;}return $status!=='failed';
    }
    protected function progress(array $state){return array('total'=>(int)$state['total_items'],'processados'=>count($state['items']),'calls'=>(int)$state['calls'],'attention_count'=>(int)$state['attention_count'],'fallback_count'=>count($state['fallback_indices']),'duration_ms'=>(int)$state['duration_ms']);}
    protected function failStart($message,&$result){$result=array('ok'=>false,'done'=>true,'erro'=>$message,'message'=>$message);return false;}
    protected function loadPreview($path,&$raw,&$error){$raw=@file_get_contents($path);$p=is_string($raw)?json_decode($raw,true):null;$error=!is_array($p)||!isset($p['items'])||!is_array($p['items'])?'Preview de importação inválido.':'';return $error===''?$p:array();}
    protected function buildContext(array $items){$out=array();foreach($items as $i=>$item)$out[]=array('source_index'=>$i,'sequence'=>$item['sequence']??null,'page'=>$item['page']??null,'rawText'=>$item['rawText']??'','codeClient'=>$item['codeClient']??'','description'=>$item['description']??'','quantity'=>$item['quantity']??null,'unit'=>$item['unit']??'');return $out;}
    protected function statePath($dir){return rtrim($dir,'/').'/enrichment_state.json';}protected function enrichedPath($dir){return rtrim($dir,'/').'/import_preview_enriched.json';}protected function diagPath($dir){return rtrim($dir,'/').'/enrichment_diag.json';}
    protected function readJson($path){$raw=@file_get_contents($path);$v=is_string($raw)?json_decode($raw,true):null;return is_array($v)?$v:array();}
    protected function writeJson($path,array $data,&$error=''){ $error='';$tmp=@tempnam(dirname($path),'.tmp-enrichment-state-');$json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);if($tmp===false||!is_string($json)||@file_put_contents($tmp,$json,LOCK_EX)===false||!@rename($tmp,$path)){if($tmp!==false)@unlink($tmp);$error='Não foi possível gravar o estado do enriquecimento.';return false;}return true; }
}
