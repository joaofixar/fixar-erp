<?php
namespace hardness;

require_once __DIR__ . '/PdfParserUtils.php';
require_once __DIR__ . '/PdfItemParserInterface.php';
require_once __DIR__ . '/ParsedItem.php';
require_once __DIR__ . '/PdfFieldInferenceResult.php';
require_once __DIR__ . '/PdfConsistencyValidator.php';
require_once dirname(__DIR__) . '/app.php';

class OpenAiPdfParser implements PdfItemParserInterface
{
    protected $validator;
    protected $model;
    protected $timeoutSeconds;
    protected $currentTimeoutSeconds;
    protected $diagnostics = array();

    public function __construct(PdfConsistencyValidator $validator = null, $model = '', $timeoutSeconds = 180)
    {
        $this->validator = $validator ?: new PdfConsistencyValidator();
        $this->model = trim((string) $model);
        $this->timeoutSeconds = (int) $timeoutSeconds;
        $this->currentTimeoutSeconds = (int) $timeoutSeconds;
    }

    public function parse(PdfExtractionResult $result, array $context = array())
    {
        $startedAt = microtime(true);
        $this->currentTimeoutSeconds = $this->resolveTimeoutSeconds($context);
        $this->diagnostics = array(
            'parser' => 'OpenAiPdfParser',
            'api' => 'responses',
            'status' => 'not_started',
            'ai_input_mode' => $this->resolveInputMode($context),
            'model' => $this->resolveModel($context),
            'response_id' => '',
            'uploaded_file_id' => '',
            'elapsed_ms' => 0,
            'upload_elapsed_ms' => 0,
            'response_elapsed_ms' => 0,
            'usage' => array(),
            'item_count' => 0,
            'table_found' => false,
            'confidence' => 0,
            'notes' => array(),
            'errors' => array(),
        );

        $apiKey = $this->resolveApiKey();
        if ($apiKey === '') {
            $this->diagnostics['status'] = 'missing_api_key';
            $this->diagnostics['errors'][] = 'Chave openAIApiKey nao configurada.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        if (!function_exists('curl_init')) {
            $this->diagnostics['status'] = 'curl_unavailable';
            $this->diagnostics['errors'][] = 'Extensao cURL indisponivel no PHP.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        if (!is_file($result->pdfPath) || !is_readable($result->pdfPath)) {
            $this->diagnostics['status'] = 'pdf_unreadable';
            $this->diagnostics['errors'][] = 'PDF nao encontrado ou sem permissao de leitura.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $inputMode = $this->resolveInputMode($context);
        $payload = null;
        if ($inputMode === 'raw_text') {
            if (trim((string) $result->rawText) === '') {
                $this->diagnostics['status'] = 'raw_text_unavailable';
                $this->diagnostics['errors'][] = 'Texto extraido indisponivel para ai_input_mode=raw_text.';
                $this->finalizeDiagnostics($startedAt);
                return array();
            }

            $payload = $this->buildRawTextRequest($result, $context);
        } else {
            $fileId = $this->uploadFile($apiKey, $result->pdfPath);
            if ($fileId === '') {
                $this->diagnostics['status'] = 'file_upload_error';
                if (empty($this->diagnostics['errors'])) {
                    $this->diagnostics['errors'][] = 'Nao foi possivel enviar o PDF para a API.';
                }
                $this->finalizeDiagnostics($startedAt);
                return array();
            }
            $this->diagnostics['uploaded_file_id'] = $fileId;
            $payload = $this->buildPdfRequest($result, $context, $fileId);
        }

        $response = $this->sendRequest($apiKey, $payload);
        if (!is_array($response) || isset($response['_request_error'])) {
            $this->diagnostics['status'] = 'request_error';
            $this->diagnostics['errors'][] = isset($response['_request_error']) ? $response['_request_error'] : 'Falha ao chamar a API.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $this->diagnostics['response_id'] = isset($response['id']) ? (string) $response['id'] : '';
        $this->diagnostics['usage'] = isset($response['usage']) && is_array($response['usage']) ? $response['usage'] : array();

        if (isset($response['error']) && is_array($response['error'])) {
            $this->diagnostics['status'] = 'api_error';
            $this->diagnostics['errors'][] = isset($response['error']['message']) ? (string) $response['error']['message'] : 'Erro retornado pela API.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $outputText = $this->extractOutputText($response);
        if ($outputText === '') {
            $this->diagnostics['status'] = 'empty_output';
            $this->diagnostics['errors'][] = 'A IA nao retornou texto estruturado.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $decoded = json_decode($outputText, true);
        if (!is_array($decoded)) {
            $decoded = json_decode($this->extractJsonObject($outputText), true);
        }

        if (!is_array($decoded)) {
            $this->diagnostics['status'] = 'invalid_json';
            $this->diagnostics['errors'][] = 'A resposta da IA nao veio em JSON valido.';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $itemsData = isset($decoded['items']) && is_array($decoded['items']) ? $decoded['items'] : array();
        $this->diagnostics['table_found'] = !empty($decoded['table_found']);
        $this->diagnostics['confidence'] = isset($decoded['confidence']) ? max(0, min(100, (int) $decoded['confidence'])) : 0;
        $this->diagnostics['notes'] = isset($decoded['notes']) && is_array($decoded['notes']) ? $decoded['notes'] : array();
        $this->diagnostics['item_count'] = count($itemsData);

        if (empty($itemsData)) {
            $this->diagnostics['status'] = $this->diagnostics['table_found'] ? 'no_items' : 'table_not_found';
            $this->finalizeDiagnostics($startedAt);
            return array();
        }

        $items = array();
        foreach ($itemsData as $index => $itemData) {
            if (!is_array($itemData)) {
                continue;
            }

            $item = $this->buildItem($itemData, $index);
            $inference = new PdfFieldInferenceResult();
            $inference->item = $item;
            $inference->method = 'openai_pdf';
            $inference->score = max(60, min(100, (int) $item->confidence));
            $inference->reason = 'Item extraido por analise multimodal do PDF.';

            $validation = $this->validator->validate($inference, $context);
            if ($validation->ignored) {
                if (!$this->shouldKeepItemDespiteValidation($item)) {
                    continue;
                }
                $validation->score = max(60, (int) $validation->score);
                $validation->reason = 'Item da IA mantido por conter sinais suficientes de produto.';
            }

            $item->validationScore = $validation->score;
            $item->validationReason = $validation->reason;
            $items[] = $item;
        }

        $this->diagnostics['item_count'] = count($items);
        $this->diagnostics['status'] = !empty($items) ? 'ok' : 'filtered_empty';
        $this->finalizeDiagnostics($startedAt);
        return $items;
    }

    public function getDiagnostics()
    {
        return $this->diagnostics;
    }

    protected function buildPdfRequest(PdfExtractionResult $result, array $context, $fileId)
    {
        $instructions = implode("\n", array(
            'Extraia todos os itens da tabela principal deste PDF de orcamento.',
            'Objetivo principal: retornar todos os registros visuais distintos da tabela principal, do primeiro ao ultimo, com cobertura maxima e sem resumir, agrupar ou omitir itens.',
            'Prioridade: e melhor retornar um item separado com algum campo incerto do que unir dois itens diferentes ou deixar um item de fora.',
            'Processo obrigatorio:',
            '1. Identifique primeiro a tabela principal de itens do orcamento.',
            '2. Leia a tabela pagina por pagina, de cima para baixo e da esquerda para a direita.',
            '3. Identifique como cada item e separado visualmente, usando sinais como numero sequencial, codigo, referencia, marcador de linha, alinhamento por colunas e repeticao estrutural entre descricao, unidade e quantidade.',
            '4. Trate como item distinto cada linha ou bloco visual que represente um registro proprio da tabela.',
            '5. Cada item visual distinto deve virar exatamente um item no JSON.',
            '6. Se um item ocupar varias linhas visuais, una apenas as linhas pertencentes ao mesmo item.',
            '7. Se houver duvida entre unir ou separar registros, prefira separar.',
            'Regras obrigatorias:',
            'Nao omita itens.',
            'Nao duplique itens.',
            'Nao invente itens.',
            'Nao una dois registros diferentes apenas porque as descricoes sao parecidas.',
            'Nao transfira descricao, quantidade, unidade ou codigo entre linhas vizinhas.',
            'quantity e unit devem pertencer ao mesmo registro visual da description correspondente.',
            'Em blocos com itens muito semelhantes, mantenha cada registro visual como item proprio.',
            'Revise inicio e fim de pagina, quebras de pagina e mudancas de secao.',
            'Se existir enumeracao ou identificadores ordenados, use isso para conferir se nenhum item ficou faltando.',
            'Se algum campo estiver incerto, mantenha o item e deixe apenas esse campo como vazio ou null.',
            'Nao descarte um registro apenas porque a description parece incompleta; preserve o registro e deixe os campos incertos vazios.',
            'Todo registro visual identificado na tabela principal deve ser convertido em um item do JSON.',
            'Use em description o texto principal do produto, material, servico ou especificacao do registro, mesmo que a coluna tenha outro nome.',
            'Use quantity como numero quando identificavel; senao null.',
            'Use unit quando identificavel; senao string vazia.',
            'Use code_client quando existir codigo, marcador, indice, referencia ou identificador do registro; senao string vazia.',
            'Preserve medidas, dimensoes, diametros, modelos, bitolas, tensoes, codigos e especificacoes tecnicas presentes no registro.',
            'Ignore cabecalhos, rodapes, totais, observacoes administrativas e textos fora da tabela principal.',
            'Antes de responder, confira se todos os registros identificados na tabela principal foram convertidos em itens no JSON.',
            'Confira especialmente registros proximos a quebras de pagina, inicio de pagina e fim de pagina.',
            'Se existir enumeracao sequencial de itens, valide que nao houve salto ou perda de registros.',
            'Retorne somente o JSON no schema solicitado.',
        ));

        return array(
            'model' => $this->resolveModel($context),
            'input' => array(
                array(
                    'role' => 'user',
                    'content' => array(
                        array(
                            'type' => 'input_file',
                            'file_id' => $fileId,
                        ),
                        array(
                            'type' => 'input_text',
                            'text' => $instructions,
                        ),
                    ),
                ),
            ),
            'text' => array(
                'format' => array(
                    'type' => 'json_schema',
                    'name' => 'orcamento_pdf_items',
                    'strict' => true,
                    'schema' => $this->responseSchema(),
                ),
            ),
            'max_output_tokens' => 8000,
        );
    }

    protected function buildRawTextRequest(PdfExtractionResult $result, array $context)
    {
        $coverageSnapshot = isset($context['coverage_snapshot']) && is_array($context['coverage_snapshot'])
            ? $context['coverage_snapshot']
            : array();
        $rawText = trim((string) $result->rawText);

        $instructions = implode("\n", array(
            'Extraia todos os itens da tabela principal deste texto de orcamento.',
            'O texto foi extraido de um PDF e pode ter quebras de linha, campos colados, desalinhamento e ruido.',
            'Objetivo principal: retornar todos os registros visuais distintos da tabela principal, do primeiro ao ultimo, com cobertura maxima e sem resumir, agrupar ou omitir itens.',
            'Prioridade: e melhor retornar um item separado com algum campo incerto do que unir dois itens diferentes ou deixar um item de fora.',
            'Processo obrigatorio:',
            '1. Identifique primeiro a tabela principal de itens do orcamento.',
            '2. Leia a tabela pagina por pagina, de cima para baixo e da esquerda para a direita.',
            '3. Identifique como cada item e separado visualmente, usando sinais como numero sequencial, codigo, referencia, marcador de linha, alinhamento por colunas e repeticao estrutural entre descricao, unidade e quantidade.',
            '4. Trate como item distinto cada linha ou bloco visual que represente um registro proprio da tabela.',
            '5. Cada item visual distinto deve virar exatamente um item no JSON.',
            '6. Se um item ocupar varias linhas visuais, una apenas as linhas pertencentes ao mesmo item.',
            '7. Se houver duvida entre unir ou separar registros, prefira separar.',
            'Regras obrigatorias:',
            'Nao omita itens.',
            'Nao duplique itens.',
            'Nao invente itens.',
            'Nao una dois registros diferentes apenas porque as descricoes sao parecidas.',
            'Nao transfira descricao, quantidade, unidade ou codigo entre linhas vizinhas.',
            'quantity e unit devem pertencer ao mesmo registro visual da description correspondente.',
            'Em blocos com itens muito semelhantes, mantenha cada registro visual como item proprio.',
            'Revise inicio e fim de pagina, quebras de pagina e mudancas de secao.',
            'Se existir enumeracao ou identificadores ordenados, use isso para conferir se nenhum item ficou faltando.',
            'Se algum campo estiver incerto, mantenha o item e deixe apenas esse campo como vazio ou null.',
            'Nao descarte um registro apenas porque a description parece incompleta; preserve o registro e deixe os campos incertos vazios.',
            'Todo registro visual identificado na tabela principal deve ser convertido em um item do JSON.',
            'Use em description o texto principal do produto, material, servico ou especificacao.',
            'Use quantity como numero quando identificavel; senao null.',
            'Use unit quando identificavel; senao string vazia.',
            'Use code_client quando existir codigo, marcador, indice, referencia ou identificador do registro; senao string vazia.',
            'Preserve medidas, dimensoes, diametros, modelos, bitolas, tensoes, codigos e especificacoes tecnicas presentes no registro.',
            'Ignore cabecalhos, rodapes, totais e observacoes administrativas.',
            'Antes de responder, confira se todos os registros identificados na tabela principal foram convertidos em itens no JSON.',
            'Confira especialmente registros proximos a quebras de pagina, inicio de pagina e fim de pagina.',
            'Se existir enumeracao sequencial de itens, valide que nao houve salto ou perda de registros.',
            'Resumo estrutural:',
            'candidate_lines_estimate=' . (isset($coverageSnapshot['candidate_lines_estimate']) ? (string) $coverageSnapshot['candidate_lines_estimate'] : 'null'),
            'enumeration_estimate=' . (isset($coverageSnapshot['enumeration_estimate']) ? (string) $coverageSnapshot['enumeration_estimate'] : 'null'),
            'enumeration_pattern=' . (isset($coverageSnapshot['enumeration_pattern']) ? (string) $coverageSnapshot['enumeration_pattern'] : 'none'),
            'Retorne somente o JSON no schema solicitado.',
        ));

        return array(
            'model' => $this->resolveModel($context),
            'input' => array(
                array(
                    'role' => 'user',
                    'content' => array(
                        array(
                            'type' => 'input_text',
                            'text' => $instructions . "\n\nTEXTO EXTRAIDO:\n" . $rawText,
                        ),
                    ),
                ),
            ),
            'text' => array(
                'format' => array(
                    'type' => 'json_schema',
                    'name' => 'orcamento_pdf_items',
                    'strict' => true,
                    'schema' => $this->responseSchema(),
                ),
            ),
            'max_output_tokens' => 8000,
        );
    }

    protected function responseSchema()
    {
        return array(
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => array(
                'table_found' => array('type' => 'boolean'),
                'confidence' => array('type' => 'integer', 'minimum' => 0, 'maximum' => 100),
                'notes' => array(
                    'type' => 'array',
                    'items' => array('type' => 'string'),
                ),
                'items' => array(
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => array(
                            'sequence' => array(
                                'anyOf' => array(
                                    array('type' => 'integer'),
                                    array('type' => 'null'),
                                ),
                            ),
                            'description' => array('type' => 'string'),
                            'quantity' => array(
                                'anyOf' => array(
                                    array('type' => 'number'),
                                    array('type' => 'null'),
                                ),
                            ),
                            'unit' => array('type' => 'string'),
                            'code_client' => array('type' => 'string'),
                        ),
                        'required' => array(
                            'sequence',
                            'description',
                            'quantity',
                            'unit',
                            'code_client',
                        ),
                    ),
                ),
            ),
            'required' => array('table_found', 'confidence', 'notes', 'items'),
        );
    }

    protected function sendRequest($apiKey, array $payload)
    {
        $startedAt = microtime(true);
        $ch = curl_init('https://api.openai.com/v1/responses');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, max(30, (int) $this->currentTimeoutSeconds));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ));

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $raw = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $curlErr !== '') {
            return array('_request_error' => 'Falha ao comunicar com a API: ' . trim((string) $curlErr));
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return array('_request_error' => 'Resposta da API em JSON invalido. HTTP ' . $httpCode . '.');
        }

        $this->diagnostics['response_elapsed_ms'] = (int) round((microtime(true) - $startedAt) * 1000);

        if ($httpCode >= 400) {
            $message = isset($decoded['error']['message']) ? (string) $decoded['error']['message'] : 'Erro HTTP ' . $httpCode . '.';
            return array('_request_error' => $message);
        }

        return $decoded;
    }

    protected function uploadFile($apiKey, $pdfPath)
    {
        if (!is_file($pdfPath) || !is_readable($pdfPath)) {
            $this->diagnostics['errors'][] = 'PDF nao encontrado ou sem permissao para upload.';
            return '';
        }

        $startedAt = microtime(true);
        $ch = curl_init('https://api.openai.com/v1/files');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, max(30, (int) $this->currentTimeoutSeconds));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $apiKey,
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'purpose' => 'user_data',
            'file' => new \CURLFile($pdfPath, 'application/pdf', basename($pdfPath)),
        ));

        $raw = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $curlErr !== '') {
            $this->diagnostics['errors'][] = 'Falha ao enviar arquivo para a API: ' . trim((string) $curlErr);
            return '';
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            $this->diagnostics['errors'][] = 'Resposta invalida ao enviar arquivo. HTTP ' . $httpCode . '.';
            return '';
        }

        $this->diagnostics['upload_elapsed_ms'] = (int) round((microtime(true) - $startedAt) * 1000);

        if ($httpCode >= 400) {
            $this->diagnostics['errors'][] = isset($decoded['error']['message'])
                ? (string) $decoded['error']['message']
                : 'Erro HTTP ao enviar arquivo: ' . $httpCode . '.';
            return '';
        }

        return isset($decoded['id']) ? (string) $decoded['id'] : '';
    }

    protected function extractOutputText(array $response)
    {
        $parts = array();
        $output = isset($response['output']) && is_array($response['output']) ? $response['output'] : array();
        foreach ($output as $item) {
            if (!is_array($item) || !isset($item['content']) || !is_array($item['content'])) {
                continue;
            }

            foreach ($item['content'] as $content) {
                if (!is_array($content)) {
                    continue;
                }

                if (isset($content['type']) && $content['type'] === 'output_text' && isset($content['text'])) {
                    $parts[] = (string) $content['text'];
                }
            }
        }

        return trim(implode("\n", $parts));
    }

    protected function extractJsonObject($text)
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $text, $matches)) {
            return trim((string) $matches[1]);
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return '';
        }

        return trim((string) substr($text, $start, $end - $start + 1));
    }

    protected function buildItem(array $itemData, $index)
    {
        $item = new ParsedItem();
        $sequence = isset($itemData['sequence']) && $itemData['sequence'] !== null ? (int) $itemData['sequence'] : ($index + 1);
        $description = PdfParserUtils::normalizeLine(isset($itemData['description']) ? $itemData['description'] : '');
        $quantity = isset($itemData['quantity']) && $itemData['quantity'] !== null ? (float) $itemData['quantity'] : null;
        $unit = trim((string) (isset($itemData['unit']) ? $itemData['unit'] : ''));
        $codeClient = PdfParserUtils::normalizeLine(isset($itemData['code_client']) ? $itemData['code_client'] : '');
        $confidence = isset($itemData['confidence']) ? max(0, min(100, (int) $itemData['confidence'])) : 85;

        $item->sequence = $sequence > 0 ? $sequence : ($index + 1);
        $item->page = 1;
        $item->rawText = $description;
        $item->description = $description;
        $item->quantity = $quantity;
        $item->unit = $unit !== '' ? PdfParserUtils::normalizeUnit($unit) : '';
        $item->codeClient = $codeClient;
        $item->alternativeCodes = array();
        $item->confidence = $confidence;
        $item->sourceBlock = 'ai_table';
        $item->sourceMethod = 'openai_pdf';
        $item->sourceLineStart = null;
        $item->sourceLineEnd = null;

        return $item;
    }

    protected function shouldKeepItemDespiteValidation(ParsedItem $item)
    {
        return trim((string) $item->description) !== ''
            && ($item->quantity !== null || trim((string) $item->unit) !== '' || trim((string) $item->codeClient) !== '');
    }

    protected function resolveApiKey()
    {
        importador_orcamento_pdf_bootstrap();

        global $g;
        $fromConfig = isset($g['C031']['openAIApiKey']) ? trim((string) $g['C031']['openAIApiKey']) : '';
        if ($fromConfig !== '') {
            return $fromConfig;
        }

        return trim((string) getenv('OPENAI_API_KEY'));
    }

    protected function resolveModel(array $context = array())
    {
        if (isset($context['ai_pdf_model']) && trim((string) $context['ai_pdf_model']) !== '') {
            return trim((string) $context['ai_pdf_model']);
        }

        if ($this->model !== '') {
            return $this->model;
        }

        $envModel = trim((string) getenv('IMPORTADOR_PDF_AI_MODEL'));
        if ($envModel !== '') {
            return $envModel;
        }

        return 'gpt-4.1';
    }

    protected function resolveInputMode(array $context = array())
    {
        if (isset($context['ai_input_mode']) && in_array((string) $context['ai_input_mode'], array('pdf', 'raw_text'), true)) {
            return (string) $context['ai_input_mode'];
        }

        return 'pdf';
    }

    protected function resolveTimeoutSeconds(array $context = array())
    {
        if (isset($context['ai_timeout_seconds']) && (int) $context['ai_timeout_seconds'] > 0) {
            return max(30, (int) $context['ai_timeout_seconds']);
        }

        return max(30, $this->timeoutSeconds);
    }

    protected function finalizeDiagnostics($startedAt)
    {
        $this->diagnostics['elapsed_ms'] = (int) round((microtime(true) - $startedAt) * 1000);
    }
}
