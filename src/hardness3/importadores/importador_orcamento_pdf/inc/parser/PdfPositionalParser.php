<?php
namespace hardness;

require_once __DIR__ . '/PdfParserUtils.php';
require_once __DIR__ . '/PdfItemParserInterface.php';
require_once __DIR__ . '/ParsedItem.php';
require_once __DIR__ . '/PdfFieldInferenceResult.php';
require_once __DIR__ . '/PdfConsistencyValidator.php';

class PdfPositionalParser implements PdfItemParserInterface
{
    protected $validator;
    protected $diagnostics = array();
    protected $binary;
    protected $timeoutSeconds;

    public function __construct(PdfConsistencyValidator $validator = null, $binary = 'pdftotext', $timeoutSeconds = 60)
    {
        $this->validator = $validator ?: new PdfConsistencyValidator();
        $this->binary = (string) $binary;
        $this->timeoutSeconds = (int) $timeoutSeconds;
    }

    public function parse(PdfExtractionResult $result, array $context = array())
    {
        $this->diagnostics = array(
            'parser' => 'PdfPositionalParser',
            'command' => '',
            'status' => 'not_started',
            'fallback_parser_used' => 'bbox',
            'fallback_input_file' => '',
            'fallback_input_format' => 'pdftotext-bbox-layout-html',
            'columns' => array(),
            'records' => array(),
            'errors' => array(),
        );

        $bboxPath = rtrim($result->workDir, '/') . '/parser_input_bbox_layout.html';
        $this->diagnostics['fallback_input_file'] = basename($bboxPath);
        $bboxText = $this->extractBbox($result->pdfPath, $bboxPath);
        if ($bboxText === '') {
            return array();
        }

        $bands = $this->parseBands($bboxText);
        if (empty($bands)) {
            $this->diagnostics['status'] = 'empty_layout';
            return array();
        }

        $headerInfo = $this->detectHeaderWindow($bands);
        $columns = $this->detectColumns($bands, $headerInfo);
        $this->diagnostics['columns'] = $columns;

        $records = $this->buildRecords($bands, $columns, $headerInfo);
        $this->diagnostics['records'] = array_map(function ($record) {
            return array(
                'page' => $record['page'],
                'rawText' => $record['rawText'],
                'description' => $record['description'],
                'quantity' => $record['quantity'],
                'unit' => $record['unit'],
                'codeClient' => $record['codeClient'],
            );
        }, $records);
        $this->diagnostics['status'] = 'ok';

        $items = array();
        foreach ($records as $index => $record) {
            $item = new ParsedItem();
            $item->sequence = $record['sequence'] !== null ? $record['sequence'] : ($index + 1);
            $item->page = $record['page'];
            $item->rawText = $record['rawText'];
            $item->description = $record['description'];
            $item->quantity = $record['quantity'];
            $item->unit = $record['unit'];
            $item->codeClient = $record['codeClient'];
            $item->alternativeCodes = $record['alternativeCodes'];
            $item->confidence = 72;
            $item->sourceBlock = 'positional_table';
            $item->sourceMethod = 'positional_bbox';
            $item->sourceLineStart = $record['lineStart'];
            $item->sourceLineEnd = $record['lineEnd'];

            $inference = new PdfFieldInferenceResult();
            $inference->item = $item;
            $inference->method = 'positional_bbox';
            $inference->score = $this->baseScoreForRecord($record);
            $inference->reason = 'Item reconstruido por layout posicional.';

            $validation = $this->validator->validate($inference, $context);
            if ($validation->ignored) {
                if (!$this->shouldKeepPositionalRecordDespiteValidation($record)) {
                    continue;
                }
                $validation->score = max(60, (int) $validation->score);
                $validation->reason = 'Linha mantida por consistencia posicional do bbox.';
            }

            $item->validationScore = $validation->score;
            $item->validationReason = $validation->reason;
            $items[] = $item;
        }

        return $items;
    }

    public function getDiagnostics()
    {
        return $this->diagnostics;
    }

    protected function extractBbox($pdfPath, $outputPath)
    {
        $command = sprintf(
            '%s -bbox-layout -enc UTF-8 %s %s',
            escapeshellcmd($this->binary),
            escapeshellarg($pdfPath),
            escapeshellarg($outputPath)
        );
        $this->diagnostics['command'] = $command;

        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );

        $process = @proc_open($command, $descriptorSpec, $pipes, importador_orcamento_pdf_fs_root_dir());
        if (!is_resource($process)) {
            $this->diagnostics['errors'][] = 'Falha ao iniciar pdftotext -bbox-layout.';
            return '';
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout = '';
        $stderr = '';
        $start = microtime(true);
        $timedOut = false;

        while (true) {
            $status = proc_get_status($process);
            $stdout .= (string) stream_get_contents($pipes[1]);
            $stderr .= (string) stream_get_contents($pipes[2]);

            if (!$status['running']) {
                break;
            }

            if ((microtime(true) - $start) > $this->timeoutSeconds) {
                $timedOut = true;
                proc_terminate($process);
                break;
            }

            usleep(100000);
        }

        $stdout .= (string) stream_get_contents($pipes[1]);
        $stderr .= (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if ($timedOut) {
            $this->diagnostics['errors'][] = 'Timeout ao gerar bbox layout.';
            return '';
        }

        if (!is_file($outputPath)) {
            $this->diagnostics['errors'][] = trim($stderr) !== '' ? trim($stderr) : 'Arquivo bbox nao foi gerado.';
            return '';
        }

        return (string) file_get_contents($outputPath);
    }

    protected function parseBands($bboxText)
    {
        if (!preg_match_all('/<page\b[^>]*>(.*?)<\/page>/is', $bboxText, $pageMatches, PREG_SET_ORDER)) {
            $this->diagnostics['errors'][] = 'Nao foi possivel localizar paginas no HTML bbox.';
            return array();
        }

        $bands = array();
        foreach ($pageMatches as $pageIndex => $pageMatch) {
            $pageHtml = isset($pageMatch[1]) ? $pageMatch[1] : '';
            $pageNumber = $pageIndex + 1;
            if (!preg_match_all('/<line\b([^>]*)>(.*?)<\/line>/is', $pageHtml, $lineMatches, PREG_SET_ORDER)) {
                continue;
            }

            $fragments = array();
            foreach ($lineMatches as $lineMatch) {
                $lineAttributes = $this->parseHtmlAttributes(isset($lineMatch[1]) ? $lineMatch[1] : '');
                $lineInnerHtml = isset($lineMatch[2]) ? $lineMatch[2] : '';

                if (!preg_match_all('/<word\b[^>]*>(.*?)<\/word>/is', $lineInnerHtml, $wordMatches, PREG_SET_ORDER)) {
                    continue;
                }

                $parts = array();
                foreach ($wordMatches as $wordMatch) {
                    $text = PdfParserUtils::normalizeLine(html_entity_decode(strip_tags(isset($wordMatch[1]) ? $wordMatch[1] : ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    if ($text !== '') {
                        $parts[] = $text;
                    }
                }

                $text = trim(implode(' ', $parts));
                if ($text === '') {
                    continue;
                }

                $fragments[] = array(
                    'page' => $pageNumber,
                    'xMin' => isset($lineAttributes['xMin']) ? (float) $lineAttributes['xMin'] : 0.0,
                    'xMax' => isset($lineAttributes['xMax']) ? (float) $lineAttributes['xMax'] : 0.0,
                    'yMin' => isset($lineAttributes['yMin']) ? (float) $lineAttributes['yMin'] : 0.0,
                    'yMax' => isset($lineAttributes['yMax']) ? (float) $lineAttributes['yMax'] : 0.0,
                    'text' => $text,
                );
            }

            usort($fragments, function ($a, $b) {
                if (abs($a['yMin'] - $b['yMin']) > 2.0) {
                    return $a['yMin'] < $b['yMin'] ? -1 : 1;
                }

                return $a['xMin'] < $b['xMin'] ? -1 : 1;
            });

            foreach ($this->groupFragmentsIntoBands($fragments) as $band) {
                $bands[] = $band;
            }
        }

        return $bands;
    }

    protected function parseHtmlAttributes($attributeText)
    {
        $attributes = array();
        if (!preg_match_all('/([A-Za-z_:][A-Za-z0-9_.:-]*)="([^"]*)"/', (string) $attributeText, $matches, PREG_SET_ORDER)) {
            return $attributes;
        }

        foreach ($matches as $match) {
            $attributes[$match[1]] = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $attributes;
    }

    protected function groupFragmentsIntoBands(array $fragments)
    {
        $bands = array();
        $current = null;

        foreach ($fragments as $fragment) {
            if ($current === null
                || $current['page'] !== $fragment['page']
                || abs($current['yMin'] - $fragment['yMin']) > 3.2
            ) {
                if ($current !== null) {
                    $bands[] = $this->finalizeBand($current);
                }

                $current = array(
                    'page' => $fragment['page'],
                    'yMin' => $fragment['yMin'],
                    'yMax' => $fragment['yMax'],
                    'fragments' => array($fragment),
                );
                continue;
            }

            $current['fragments'][] = $fragment;
            $current['yMax'] = max($current['yMax'], $fragment['yMax']);
        }

        if ($current !== null) {
            $bands[] = $this->finalizeBand($current);
        }

        return $bands;
    }

    protected function finalizeBand(array $band)
    {
        usort($band['fragments'], function ($a, $b) {
            return $a['xMin'] < $b['xMin'] ? -1 : 1;
        });

        $band['text'] = trim(implode(' ', array_map(function ($fragment) {
            return $fragment['text'];
        }, $band['fragments'])));

        return $band;
    }

    protected function detectHeaderWindow(array $bands)
    {
        $best = array(
            'start' => 0,
            'end' => -1,
            'hits' => 0,
            'yMax' => 0.0,
        );

        $count = count($bands);
        for ($i = 0; $i < $count; $i++) {
            for ($window = 1; $window <= 4 && ($i + $window - 1) < $count; $window++) {
                $slice = array_slice($bands, $i, $window);
                if (count(array_unique(array_map(function ($band) {
                    return $band['page'];
                }, $slice))) !== 1) {
                    break;
                }

                $text = implode(' ', array_map(function ($band) {
                    return $band['text'];
                }, $slice));
                $hits = $this->countHeaderHits($text);
                if ($hits > $best['hits']) {
                    $best = array(
                        'start' => $i,
                        'end' => $i + $window - 1,
                        'hits' => $hits,
                        'yMax' => max(array_map(function ($band) {
                            return $band['yMax'];
                        }, $slice)),
                    );
                }
            }
        }

        return $best;
    }

    protected function countHeaderHits($text)
    {
        $lower = mb_strtolower(PdfParserUtils::normalizeLine($text), 'UTF-8');
        $hits = 0;

        foreach (array('description', 'quantity', 'unit', 'code', 'ncm') as $field) {
            foreach (PdfParserUtils::headerAliasesFor($field) as $alias) {
                if ($alias !== '' && mb_strpos($lower, mb_strtolower($alias, 'UTF-8')) !== false) {
                    $hits++;
                    break;
                }
            }
        }

        if (strpos($lower, 'total') !== false) {
            $hits++;
        }

        return $hits;
    }

    protected function detectColumns(array $bands, array $headerInfo)
    {
        $clusters = array();
        foreach ($bands as $index => $band) {
            if ($index <= $headerInfo['end']) {
                continue;
            }

            if ($band['page'] !== $bands[$headerInfo['start']]['page'] && $headerInfo['end'] >= 0) {
                // Mantem analise multipagina, mas evita misturar o bloco de cabecalho com a pagina seguinte.
            }

            if ($this->isTerminalBand($band['text'])) {
                continue;
            }

            foreach ($band['fragments'] as $fragment) {
                $this->pushCluster($clusters, $fragment);
            }
        }

        usort($clusters, function ($a, $b) {
            return $a['xMin'] < $b['xMin'] ? -1 : 1;
        });

        $columns = array();
        foreach ($clusters as $clusterIndex => $cluster) {
            $samples = $cluster['samples'];
            $texts = array_map(function ($sample) {
                return $sample['text'];
            }, $samples);

            $numericLike = 0;
            $dateLike = 0;
            $unitLike = 0;
            $codeLike = 0;
            $decimalLike = 0;
            $shortDigitLike = 0;
            $longDigitLike = 0;
            $multiTokenNumericLike = 0;
            $totalLength = 0;
            $totalWords = 0;
            foreach ($texts as $text) {
                $normalized = PdfParserUtils::normalizeLine($text);
                $totalLength += strlen($normalized);
                $words = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY);
                $totalWords += $words ? count($words) : 0;

                if (preg_match('/^\d+(?:[.,]\d+)?$/u', $text)) {
                    $numericLike++;
                }
                if (preg_match('/^\d+,\d+$/u', $text)) {
                    $decimalLike++;
                }
                if (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/u', $text)) {
                    $dateLike++;
                }
                if (PdfParserUtils::isUnitToken($text)) {
                    $unitLike++;
                }
                if (PdfParserUtils::looksLikeCode($text)) {
                    $codeLike++;
                }
                if (preg_match('/^\d{1,4}$/u', $normalized)) {
                    $shortDigitLike++;
                }
                if (preg_match('/^\d{5,}$/u', $normalized)) {
                    $longDigitLike++;
                }
                if (preg_match('/^\d{2,}\s+\d{2,}$/u', $normalized)) {
                    $multiTokenNumericLike++;
                }
            }

            $sampleCount = count($samples);

            $columns[] = array(
                'index' => $clusterIndex,
                'xMin' => $cluster['xMin'],
                'xMax' => $cluster['xMax'],
                'sample_count' => $sampleCount,
                'numeric_ratio' => $sampleCount > 0 ? ($numericLike / $sampleCount) : 0,
                'decimal_ratio' => $sampleCount > 0 ? ($decimalLike / $sampleCount) : 0,
                'date_ratio' => $sampleCount > 0 ? ($dateLike / $sampleCount) : 0,
                'unit_ratio' => $sampleCount > 0 ? ($unitLike / $sampleCount) : 0,
                'code_ratio' => $sampleCount > 0 ? ($codeLike / $sampleCount) : 0,
                'short_digit_ratio' => $sampleCount > 0 ? ($shortDigitLike / $sampleCount) : 0,
                'long_digit_ratio' => $sampleCount > 0 ? ($longDigitLike / $sampleCount) : 0,
                'multi_number_ratio' => $sampleCount > 0 ? ($multiTokenNumericLike / $sampleCount) : 0,
                'avg_length' => $sampleCount > 0 ? ($totalLength / $sampleCount) : 0,
                'avg_words' => $sampleCount > 0 ? ($totalWords / $sampleCount) : 0,
                'samples' => array_slice($texts, 0, 5),
            );
        }

        return $columns;
    }

    protected function pushCluster(array &$clusters, array $fragment)
    {
        foreach ($clusters as &$cluster) {
            if (abs($cluster['xMin'] - $fragment['xMin']) <= 18.0) {
                $cluster['samples'][] = $fragment;
                $cluster['xMin'] = ($cluster['xMin'] + $fragment['xMin']) / 2;
                $cluster['xMax'] = max($cluster['xMax'], $fragment['xMax']);
                return;
            }
        }

        $clusters[] = array(
            'xMin' => $fragment['xMin'],
            'xMax' => $fragment['xMax'],
            'samples' => array($fragment),
        );
    }

    protected function buildRecords(array $bands, array $columns, array $headerInfo)
    {
        $roles = $this->classifyColumnRoles($columns);
        $records = array();
        $current = null;
        $lineNumber = 1;

        foreach ($bands as $index => $band) {
            if ($index <= $headerInfo['end']) {
                continue;
            }

            $text = PdfParserUtils::normalizeLine($band['text']);
            if ($text === '' || $this->isTerminalBand($text)) {
                if ($current !== null) {
                    $records[] = $this->finalizeRecord($current);
                    $current = null;
                }
                continue;
            }

            $cells = $this->bandToCells($band, $columns);
            $bandInfo = $this->analyzeBand($cells, $roles);

            if ($bandInfo['isMeta']) {
                if ($current !== null) {
                    $records[] = $this->finalizeRecord($current);
                    $current = null;
                }
                continue;
            }

            if ($bandInfo['startsRecord']) {
                if ($current !== null) {
                    $records[] = $this->finalizeRecord($current);
                }
                $current = $this->newRecord($band, $bandInfo, $lineNumber);
                $lineNumber++;
                continue;
            }

            if ($current !== null && $bandInfo['isContinuation']) {
                $this->appendBandToRecord($current, $band, $bandInfo);
            }
        }

        if ($current !== null) {
            $records[] = $this->finalizeRecord($current);
        }

        return $this->mergeAdjacentRecords($records);
    }

    protected function classifyColumnRoles(array $columns)
    {
        $roles = array(
            'description' => array(),
            'complement' => array(),
            'item_code' => array(),
            'quantity' => null,
            'unit' => null,
            'date' => array(),
            'date_tail' => array(),
            'code' => array(),
            'request_code' => array(),
            'reference' => array(),
        );

        if (empty($columns)) {
            return $roles;
        }

        $ordered = $columns;
        usort($ordered, function ($a, $b) {
            return $a['xMin'] < $b['xMin'] ? -1 : 1;
        });

        foreach ($ordered as $column) {
            if ($column['unit_ratio'] >= 0.2 && $roles['unit'] === null) {
                $roles['unit'] = $column['index'];
            }
        }

        $quantityBestScore = null;
        $quantityPool = array_values(array_filter($ordered, function ($column) {
            return $column['decimal_ratio'] > 0.0 && $column['code_ratio'] < 0.7;
        }));
        if (empty($quantityPool)) {
            $quantityPool = $ordered;
        }
        foreach ($quantityPool as $position => $column) {
            $score = ($column['decimal_ratio'] * 7.0)
                + ($column['numeric_ratio'] * 2.8)
                - ($column['code_ratio'] * 2.4)
                - ($column['long_digit_ratio'] * 2.0);

            if ($column['date_ratio'] > 0.5 && $column['decimal_ratio'] === 0.0) {
                $score -= 1.5;
            }

            if ($roles['unit'] !== null) {
                $unitPosition = $this->findColumnPosition($ordered, $roles['unit']);
                if ($unitPosition !== null && abs($position - $unitPosition) <= 1) {
                    $score += 1.8;
                }
            }

            if ($column['xMin'] >= $ordered[(int) floor(count($ordered) / 2)]['xMin']) {
                $score += 0.4;
            }

            if ($quantityBestScore === null || $score > $quantityBestScore) {
                $quantityBestScore = $score;
                $roles['quantity'] = $column['index'];
            }
        }

        $descriptionAnchor = null;
        foreach ($ordered as $column) {
            if ($column['avg_words'] >= 1.5 && $column['numeric_ratio'] < 0.45 && $column['unit_ratio'] < 0.2) {
                $descriptionAnchor = $column;
                break;
            }
        }
        if ($descriptionAnchor === null) {
            $descriptionAnchor = $ordered[min(1, count($ordered) - 1)];
        }

        $bestItemCode = null;
        $bestItemCodeScore = null;
        foreach ($ordered as $position => $column) {
            if ($roles['quantity'] !== null && $column['index'] === $roles['quantity']) {
                continue;
            }
            if ($roles['unit'] !== null && $column['index'] === $roles['unit']) {
                continue;
            }

            $isDateColumn = $column['date_ratio'] >= 0.2;
            if ($isDateColumn) {
                $roles['date'][] = $column['index'];
                continue;
            }

            if ($roles['quantity'] !== null) {
                $qtyPos = $this->findColumnPosition($ordered, $roles['quantity']);
                if ($qtyPos !== null
                    && $position > $qtyPos
                    && $column['short_digit_ratio'] >= 0.6
                    && $column['avg_length'] <= 4
                ) {
                    $roles['date_tail'][] = $column['index'];
                    continue;
                }
            }

            if ($column['xMin'] < $descriptionAnchor['xMin']
                && $column['sample_count'] >= 3
                && $column['avg_length'] <= 18
                && $column['avg_words'] <= 3.5
                && ($column['short_digit_ratio'] >= 0.25 || $column['multi_number_ratio'] >= 0.2 || $column['code_ratio'] >= 0.4)
            ) {
                if (!($column['avg_length'] <= 2 && $column['short_digit_ratio'] >= 0.8)) {
                    $score = ($column['multi_number_ratio'] * 3.2)
                        + ($column['short_digit_ratio'] * 2.4)
                        + ($column['code_ratio'] * 1.2)
                        - ($column['avg_words'] * 0.6);
                    if ($bestItemCodeScore === null || $score > $bestItemCodeScore) {
                        $bestItemCodeScore = $score;
                        $bestItemCode = $column['index'];
                    }
                }
            }

            if ($column['xMin'] > $descriptionAnchor['xMax']
                && ($column['long_digit_ratio'] >= 0.35 || ($column['numeric_ratio'] >= 0.4 && $column['avg_length'] >= 6 && $column['xMin'] < $this->columnXMin($ordered, $roles['quantity'])))
            ) {
                $roles['request_code'][] = $column['index'];
                continue;
            }

            if ($column['code_ratio'] >= 0.5
                && $column['xMin'] >= $descriptionAnchor['xMin']
                && ($roles['quantity'] === null || $column['xMin'] < $this->columnXMin($ordered, $roles['quantity']))
            ) {
                $roles['reference'][] = $column['index'];
                continue;
            }

            if ($column['avg_words'] >= 1.3 || $column['avg_length'] >= 10) {
                if (empty($roles['description'])) {
                    $roles['description'][] = $column['index'];
                } else {
                    $roles['complement'][] = $column['index'];
                }
            }
        }

        if ($bestItemCode !== null) {
            $roles['item_code'][] = $bestItemCode;
        }

        $roles['description'] = array_values(array_unique($roles['description']));
        $roles['complement'] = array_values(array_unique($roles['complement']));
        $roles['item_code'] = array_values(array_unique($roles['item_code']));
        $roles['date'] = array_values(array_unique($roles['date']));
        $roles['date_tail'] = array_values(array_unique($roles['date_tail']));
        $roles['request_code'] = array_values(array_unique($roles['request_code']));
        $roles['reference'] = array_values(array_unique($roles['reference']));

        sort($roles['description']);
        sort($roles['complement']);
        sort($roles['item_code']);
        sort($roles['date']);
        sort($roles['date_tail']);
        sort($roles['request_code']);
        sort($roles['reference']);

        if (empty($roles['description'])) {
            $roles['description'][] = $descriptionAnchor['index'];
        }

        return $roles;
    }

    protected function bandToCells(array $band, array $columns)
    {
        $cells = array();
        foreach ($columns as $column) {
            $cells[$column['index']] = array();
        }

        foreach ($band['fragments'] as $fragment) {
            $target = null;
            $bestDistance = null;
            foreach ($columns as $column) {
                $distance = abs($column['xMin'] - $fragment['xMin']);
                if ($bestDistance === null || $distance < $bestDistance) {
                    $bestDistance = $distance;
                    $target = $column['index'];
                }
            }

            if ($target !== null) {
                $cells[$target][] = $fragment['text'];
            }
        }

        return array_map(function ($parts) {
            return PdfParserUtils::normalizeLine(implode(' ', $parts));
        }, $cells);
    }

    protected function analyzeBand(array $cells, array $roles)
    {
        $descParts = $this->collectRoleTexts($cells, $roles['description']);
        $complementParts = $this->collectRoleTexts($cells, $roles['complement']);
        $referenceParts = $this->collectRoleTexts($cells, $roles['reference']);
        $requestParts = $this->collectRoleTexts($cells, $roles['request_code']);
        $itemCodeParts = $this->collectRoleTexts($cells, $roles['item_code']);
        if (empty($itemCodeParts)) {
            $itemCodeParts = $this->deriveItemCodePartsFromLeadingCells($cells, $roles);
        }
        $quantityText = $roles['quantity'] !== null && isset($cells[$roles['quantity']]) ? $cells[$roles['quantity']] : '';
        $unitText = $roles['unit'] !== null && isset($cells[$roles['unit']]) ? $cells[$roles['unit']] : '';
        $dateText = implode(' ', array_merge(
            $this->collectRoleTexts($cells, $roles['date']),
            $this->collectRoleTexts($cells, $roles['date_tail'])
        ));

        $hasDescription = !empty($descParts) || !empty($complementParts);
        $hasLeadCode = !empty($itemCodeParts) && !$this->isWeakCodeAnchor($itemCodeParts);
        $quantityValue = $this->extractQuantityFromColumns($quantityText, $dateText, $referenceParts, $requestParts);
        $isMeta = $this->looksLikeMetaBand(implode(' ', array_merge($descParts, $complementParts, $referenceParts, $requestParts, $itemCodeParts)));
        $hasDescriptiveMass = $this->descriptionMass($descParts, $complementParts) >= 8;
        $startsRecord = !$isMeta && ($hasLeadCode || ($quantityValue !== null && $hasDescriptiveMass));

        return array(
            'descriptionParts' => $descParts,
            'complementParts' => $complementParts,
            'referenceParts' => $referenceParts,
            'requestParts' => $requestParts,
            'itemCodeParts' => $itemCodeParts,
            'quantity' => $quantityValue,
            'quantityText' => $quantityText,
            'unit' => PdfParserUtils::isUnitToken($unitText) ? PdfParserUtils::normalizeUnit($unitText) : '',
            'dateText' => $dateText,
            'isMeta' => $isMeta,
            'startsRecord' => $startsRecord,
            'isContinuation' => !$isMeta && ($hasDescription || !empty($referenceParts) || !empty($requestParts)) && !$startsRecord,
        );
    }

    protected function collectRoleTexts(array $cells, array $indexes)
    {
        $parts = array();
        foreach ($indexes as $index) {
            if (!isset($cells[$index])) {
                continue;
            }

            $text = trim((string) $cells[$index]);
            if ($text !== '') {
                $parts[] = $text;
            }
        }

        return $this->dedupeTextParts($parts);
    }

    protected function containsRecordAnchor(array $parts)
    {
        foreach ($parts as $part) {
            if (preg_match('/\d{3,}/u', $part) || PdfParserUtils::looksLikeCode($part)) {
                return true;
            }
        }

        return false;
    }

    protected function looksLikeMetaBand($text)
    {
        $text = mb_strtolower(PdfParserUtils::normalizeLine($text), 'UTF-8');
        if ($text === '') {
            return false;
        }

        if (strpos($text, 'gef') !== false || strpos($text, 'solicita') !== false) {
            return true;
        }

        if (preg_match('/\b(?:comprador|email|contato|cidade|cnpj|pagamento|frete)\b/u', $text)) {
            return true;
        }

        return substr_count($text, ':') >= 2;
    }

    protected function newRecord(array $band, array $bandInfo, $lineNumber)
    {
        $descriptionParts = array_merge($bandInfo['descriptionParts'], $bandInfo['complementParts']);
        $description = implode(' ', $descriptionParts);
        $rawLines = array();
        $firstLine = trim(implode(' ', array_filter(array(
            implode(' ', $bandInfo['itemCodeParts']),
            $description,
            implode(' ', $bandInfo['requestParts']),
            implode(' ', $bandInfo['referenceParts']),
            $bandInfo['quantityText'],
            $bandInfo['unit'],
            $bandInfo['dateText'],
        ))));
        if ($firstLine !== '') {
            $rawLines[] = $firstLine;
        }

        return array(
            'sequence' => $lineNumber,
            'page' => $band['page'],
            'codeClient' => $this->pickPrimaryCode($bandInfo),
            'alternativeCodes' => $this->pickAlternativeCodes($bandInfo),
            'descriptionParts' => $descriptionParts,
            'referenceParts' => $bandInfo['referenceParts'],
            'requestParts' => $bandInfo['requestParts'],
            'itemCodeParts' => $bandInfo['itemCodeParts'],
            'dateTexts' => $bandInfo['dateText'] !== '' ? array($bandInfo['dateText']) : array(),
            'quantity' => $bandInfo['quantity'],
            'unit' => $bandInfo['unit'],
            'rawLines' => $rawLines,
            'lineStart' => $lineNumber,
            'lineEnd' => $lineNumber,
        );
    }

    protected function appendBandToRecord(array &$record, array $band, array $bandInfo)
    {
        $description = implode(' ', array_merge($bandInfo['descriptionParts'], $bandInfo['complementParts']));
        if ($description !== '') {
            $record['descriptionParts'] = $this->dedupeTextParts(array_merge($record['descriptionParts'], array($description)));
            $record['rawLines'][] = $description;
        }

        if (!empty($bandInfo['referenceParts'])) {
            $record['referenceParts'] = $this->dedupeTextParts(array_merge($record['referenceParts'], $bandInfo['referenceParts']));
        }

        if (!empty($bandInfo['requestParts'])) {
            $record['requestParts'] = $this->dedupeTextParts(array_merge($record['requestParts'], $bandInfo['requestParts']));
        }

        if (!empty($bandInfo['itemCodeParts']) && $record['codeClient'] === '') {
            $record['itemCodeParts'] = $this->dedupeTextParts(array_merge($record['itemCodeParts'], $bandInfo['itemCodeParts']));
            $record['codeClient'] = $this->pickPrimaryCode(array(
                'itemCodeParts' => $record['itemCodeParts'],
                'referenceParts' => $record['referenceParts'],
                'requestParts' => $record['requestParts'],
            ));
        }

        if ($record['quantity'] === null && $bandInfo['quantity'] !== null) {
            $record['quantity'] = $bandInfo['quantity'];
        }

        if ($record['unit'] === '' && $bandInfo['unit'] !== '') {
            $record['unit'] = $bandInfo['unit'];
        }

        if ($bandInfo['dateText'] !== '') {
            $record['dateTexts'] = $this->dedupeTextParts(array_merge($record['dateTexts'], array($bandInfo['dateText'])));
        }

        $record['lineEnd']++;
    }

    protected function finalizeRecord(array $record)
    {
        $record = $this->normalizeRecordByRawPattern($record);
        $record['descriptionParts'] = $this->dedupeTextParts($record['descriptionParts']);
        $record['description'] = $this->cleanDescriptionParts(
            $record['descriptionParts'],
            isset($record['codeClient']) ? $record['codeClient'] : '',
            isset($record['referenceParts']) ? $record['referenceParts'] : array(),
            isset($record['requestParts']) ? $record['requestParts'] : array(),
            isset($record['dateTexts']) ? $record['dateTexts'] : array(),
            isset($record['quantity']) ? $record['quantity'] : null,
            isset($record['unit']) ? $record['unit'] : ''
        );
        $record['rawText'] = trim(implode("\n", array_values(array_filter($record['rawLines'], function ($line) {
            return trim((string) $line) !== '';
        }))));

        if ($record['rawText'] === '' && $record['description'] !== '') {
            $record['rawText'] = $record['description'];
        }

        return $record;
    }

    protected function mergeAdjacentRecords(array $records)
    {
        $merged = array();

        foreach ($records as $record) {
            $lastIndex = count($merged) - 1;
            if ($lastIndex >= 0 && $this->shouldMergeRecords($merged[$lastIndex], $record)) {
                $merged[$lastIndex] = $this->mergeRecordPair($merged[$lastIndex], $record);
                continue;
            }

            $merged[] = $record;
        }

        return $merged;
    }

    protected function shouldMergeRecords(array $previous, array $current)
    {
        if ($previous['page'] !== $current['page']) {
            return false;
        }

        if ($this->shouldMergeShortHeaderWithContinuation($previous, $current)) {
            return true;
        }

        if (trim((string) $previous['codeClient']) === '' || trim((string) $current['codeClient']) !== '') {
            return false;
        }

        $previousWords = preg_split('/\s+/u', trim((string) $previous['description']), -1, PREG_SPLIT_NO_EMPTY);
        $currentWords = preg_split('/\s+/u', trim((string) $current['description']), -1, PREG_SPLIT_NO_EMPTY);

        if ($previous['quantity'] === null && $current['quantity'] !== null) {
            return count($previousWords) <= 6 && count($currentWords) >= 4;
        }

        $previousLines = preg_split('/\R/u', trim((string) $previous['rawText']), -1, PREG_SPLIT_NO_EMPTY);
        return count($previousLines) <= 2 && count($previousWords) <= 5 && count($currentWords) >= 4;
    }

    protected function mergeRecordPair(array $previous, array $current)
    {
        $current['codeClient'] = $previous['codeClient'];
        if ($current['quantity'] === null) {
            $current['quantity'] = $previous['quantity'];
        }
        if ($current['unit'] === '') {
            $current['unit'] = $previous['unit'];
        }
        $current['alternativeCodes'] = array_values(array_unique(array_merge(
            isset($previous['alternativeCodes']) ? $previous['alternativeCodes'] : array(),
            isset($current['alternativeCodes']) ? $current['alternativeCodes'] : array()
        )));
        $current['referenceParts'] = $this->dedupeTextParts(array_merge(
            isset($previous['referenceParts']) ? $previous['referenceParts'] : array(),
            isset($current['referenceParts']) ? $current['referenceParts'] : array()
        ));
        $current['requestParts'] = $this->dedupeTextParts(array_merge(
            isset($previous['requestParts']) ? $previous['requestParts'] : array(),
            isset($current['requestParts']) ? $current['requestParts'] : array()
        ));
        $current['dateTexts'] = $this->dedupeTextParts(array_merge(
            isset($previous['dateTexts']) ? $previous['dateTexts'] : array(),
            isset($current['dateTexts']) ? $current['dateTexts'] : array()
        ));
        $current['itemCodeParts'] = $this->dedupeTextParts(array_merge(
            isset($previous['itemCodeParts']) ? $previous['itemCodeParts'] : array(),
            isset($current['itemCodeParts']) ? $current['itemCodeParts'] : array()
        ));

        $combinedDescription = $this->dedupeTextParts(array_merge(
            preg_split('/\R/u', (string) $previous['description']),
            preg_split('/\R/u', (string) $current['description'])
        ));
        $current['description'] = trim(implode(' ', $combinedDescription));

        $combinedRawLines = array_values(array_filter(array_merge(
            preg_split('/\R/u', (string) $previous['rawText']),
            preg_split('/\R/u', (string) $current['rawText'])
        ), function ($line) {
            return trim((string) $line) !== '';
        }));
        $current['rawText'] = trim(implode("\n", $this->dedupeTextParts($combinedRawLines)));
        $current['lineStart'] = min($previous['lineStart'], $current['lineStart']);
        $preferredQuantity = $this->extractPreferredQuantityFromRaw($current['rawText']);
        if ($preferredQuantity !== null) {
            $current['quantity'] = $preferredQuantity;
        }

        return $current;
    }

    protected function shouldMergeShortHeaderWithContinuation(array $previous, array $current)
    {
        $previousCode = trim((string) $previous['codeClient']);
        if ($previousCode === '') {
            return false;
        }

        $previousDesc = PdfParserUtils::normalizeLine(isset($previous['description']) ? $previous['description'] : '');
        $currentDesc = PdfParserUtils::normalizeLine(isset($current['description']) ? $current['description'] : '');

        if ($previousDesc === '' || $currentDesc === '') {
            return false;
        }

        $previousWords = preg_split('/\s+/u', $previousDesc, -1, PREG_SPLIT_NO_EMPTY);
        $currentWords = preg_split('/\s+/u', $currentDesc, -1, PREG_SPLIT_NO_EMPTY);
        $currentCode = trim((string) $current['codeClient']);

        return count($previousWords) <= 3
            && count($currentWords) >= 4
            && ($currentCode === '' || $this->isWeakCodeAnchor(array($currentCode)))
            && $this->extractPreferredQuantityFromRaw(isset($previous['rawText']) ? $previous['rawText'] : '') !== null;
    }

    protected function pickPrimaryCode(array $bandInfo)
    {
        $itemCodeParts = isset($bandInfo['itemCodeParts']) ? $bandInfo['itemCodeParts'] : array();
        foreach ($itemCodeParts as $part) {
            $part = PdfParserUtils::normalizeLine($part);
            if ($part === '') {
                continue;
            }

            if (preg_match('/^\d{1,6}$/u', $part) || preg_match('/^\d{2,}\s+\d{2,}$/u', $part) || PdfParserUtils::looksLikeCode($part)) {
                return $part;
            }
        }

        return '';
    }

    protected function pickAlternativeCodes(array $bandInfo)
    {
        $codes = array();
        foreach (array_merge(
            isset($bandInfo['referenceParts']) ? $bandInfo['referenceParts'] : array(),
            isset($bandInfo['requestParts']) ? $bandInfo['requestParts'] : array()
        ) as $part) {
            if (PdfParserUtils::looksLikeCode($part)) {
                $codes[] = $part;
            }
        }

        return array_values(array_unique(array_filter($codes, function ($value) use ($bandInfo) {
            return $value !== $this->pickPrimaryCode($bandInfo);
        })));
    }

    protected function dedupeTextParts(array $parts)
    {
        $deduped = array();
        foreach ($parts as $part) {
            $part = PdfParserUtils::normalizeLine($part);
            if ($part === '') {
                continue;
            }

            $skip = false;
            foreach ($deduped as $index => $existing) {
                $lowerPart = mb_strtolower($part, 'UTF-8');
                $lowerExisting = mb_strtolower($existing, 'UTF-8');

                if ($lowerPart === $lowerExisting || strpos($lowerExisting, $lowerPart) !== false) {
                    $skip = true;
                    break;
                }

                if (strpos($lowerPart, $lowerExisting) !== false) {
                    $deduped[$index] = $part;
                    $skip = true;
                    break;
                }
            }

            if (!$skip) {
                $deduped[] = $part;
            }
        }

        return array_values($deduped);
    }

    protected function isTerminalBand($text)
    {
        return PdfParserUtils::isTotalsLine($text)
            || PdfParserUtils::isEmailLine($text)
            || PdfParserUtils::isPhoneLine($text)
            || preg_match('/\b(?:condi[cç][oõ]es de pagamento|data m[aá]xima|total de itens)\b/iu', $text);
    }

    protected function baseScoreForRecord(array $record)
    {
        $score = 52;
        if ($record['description'] !== '') {
            $score += 12;
        }
        if ($record['quantity'] !== null) {
            $score += 10;
        }
        if ($record['unit'] !== '') {
            $score += 6;
        }
        if ($record['codeClient'] !== '') {
            $score += 6;
        }

        return min(92, $score);
    }

    protected function extractQuantityFromColumns($quantityText, $dateText, array $referenceParts = array(), array $requestParts = array())
    {
        $text = PdfParserUtils::normalizeLine($quantityText);
        if ($text === '') {
            return null;
        }

        if (preg_match('/\b(\d+,\d+)\b/u', $text, $matches)) {
            $candidate = PdfParserUtils::normalizeQuantity($matches[1]);
            if ($candidate !== null && $candidate > 0) {
                return $candidate;
            }
        }

        $tokens = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $excluded = array();
        foreach (array_merge($referenceParts, $requestParts, array($dateText)) as $part) {
            foreach (preg_split('/\s+/u', PdfParserUtils::normalizeLine($part), -1, PREG_SPLIT_NO_EMPTY) as $token) {
                $excluded[mb_strtolower($token, 'UTF-8')] = true;
            }
        }

        foreach ($tokens as $token) {
            $normalized = PdfParserUtils::normalizeLine($token);
            if ($normalized === '' || isset($excluded[mb_strtolower($normalized, 'UTF-8')])) {
                continue;
            }
            if (!preg_match('/^\d+(?:[.,]\d+)?$/u', $normalized)) {
                continue;
            }

            if (preg_match('/^\d{1,2}$/u', $normalized) && preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/u', $dateText)) {
                continue;
            }

            $candidate = PdfParserUtils::normalizeQuantity($normalized);
            if ($candidate !== null && $candidate > 0) {
                return $candidate;
            }
        }

        return null;
    }

    protected function normalizeRecordByRawPattern(array $record)
    {
        $rawText = isset($record['rawText']) ? (string) $record['rawText'] : '';
        if ($rawText === '' && isset($record['rawLines']) && is_array($record['rawLines'])) {
            $rawText = trim(implode("\n", array_values(array_filter($record['rawLines'], function ($line) {
                return trim((string) $line) !== '';
            }))));
        }
        $firstLine = '';
        if ($rawText !== '') {
            $lines = preg_split('/\R/u', $rawText, -1, PREG_SPLIT_NO_EMPTY);
            $firstLine = isset($lines[0]) ? PdfParserUtils::normalizeLine($lines[0]) : '';
        }

        if ($firstLine !== '' && preg_match('/^(\d{5,8})\s+(\d{1,5})\s+(.+)$/u', $firstLine, $matches)) {
            $requestCode = $matches[1];
            $itemCode = $matches[2];
            if (strlen($requestCode) > strlen($itemCode)
                && trim((string) $record['codeClient']) !== PdfParserUtils::normalizeLine($requestCode . ' ' . $itemCode)
            ) {
                $record['codeClient'] = $itemCode;
            }
            $record['requestParts'] = $this->dedupeTextParts(array_merge(
                isset($record['requestParts']) ? $record['requestParts'] : array(),
                array($requestCode)
            ));

            if (!empty($record['descriptionParts'])) {
                foreach ($record['descriptionParts'] as $index => $part) {
                    $part = preg_replace('/^\s*' . preg_quote($itemCode, '/') . '\s+/u', '', $part);
                    $record['descriptionParts'][$index] = PdfParserUtils::normalizeLine($part);
                }
            }
        }

        $preferredQuantity = $this->extractPreferredQuantityFromRaw($rawText);
        if ($preferredQuantity !== null) {
            $record['quantity'] = $preferredQuantity;
        }

        return $record;
    }

    protected function extractPreferredQuantityFromRaw($rawText)
    {
        $rawText = PdfParserUtils::normalizeLine($rawText);
        if ($rawText === '') {
            return null;
        }

        if (preg_match('/\b(\d+,\d+)\s+\d{1,2}\/\d{1,2}\/\d{2,4}\b/u', $rawText, $matches)) {
            $candidate = PdfParserUtils::normalizeQuantity($matches[1]);
            if ($candidate !== null && $candidate > 0) {
                return $candidate;
            }
        }

        return null;
    }

    protected function shouldKeepPositionalRecordDespiteValidation(array $record)
    {
        $description = trim((string) (isset($record['description']) ? $record['description'] : ''));
        $quantity = isset($record['quantity']) ? $record['quantity'] : null;
        $unit = trim((string) (isset($record['unit']) ? $record['unit'] : ''));

        return $description !== '' && ($quantity !== null || $unit !== '');
    }

    protected function cleanDescriptionParts(array $parts, $codeClient, array $referenceParts, array $requestParts, array $dateTexts, $quantity, $unit)
    {
        $description = implode(' ', $this->dedupeTextParts($parts));
        $description = PdfParserUtils::normalizeLine($description);

        if ($description === '') {
            return '';
        }

        $codeClient = PdfParserUtils::normalizeLine($codeClient);
        if ($codeClient !== '' && strpos($description, $codeClient) === 0) {
            $description = ltrim(substr($description, strlen($codeClient)));
        }

        foreach ($requestParts as $part) {
            $part = PdfParserUtils::normalizeLine($part);
            if ($part !== '') {
                $description = preg_replace('/\b' . preg_quote($part, '/') . '\b/u', ' ', $description);
            }
        }

        foreach ($dateTexts as $dateText) {
            $dateText = PdfParserUtils::normalizeLine($dateText);
            if ($dateText !== '') {
                $description = preg_replace('/\b' . preg_quote($dateText, '/') . '\b/u', ' ', $description);
            }
        }

        foreach ($referenceParts as $part) {
            $part = PdfParserUtils::normalizeLine($part);
            if ($part !== '') {
                $description = preg_replace('/\b' . preg_quote($part, '/') . '\b/u', ' ', $description);
            }
        }

        $description = preg_replace('/\b\d{1,2}\/\d{1,2}\/\d{2,4}\b/u', ' ', $description);
        $description = preg_replace('/\b\d{5,}\b/u', ' ', $description);
        $description = preg_replace('/(?:^|\s)\d{2,4}(?=\s*$)/u', ' ', $description);
        $description = preg_replace('/(?:^|\s)\d{1,2}\s+0(?=\s|$)/u', ' ', $description);

        if ($quantity !== null) {
            $quantityToken = str_replace('.', ',', (string) $quantity);
            if (strpos($quantityToken, ',') !== false) {
                $description = preg_replace('/\b' . preg_quote($quantityToken, '/') . '\b/u', ' ', $description);
            }
        }

        if ($unit !== '') {
            $description = preg_replace('/\b' . preg_quote($unit, '/') . '\b/u', ' ', $description);
        }

        $description = preg_replace('/\s+/u', ' ', trim((string) $description));
        return $description;
    }

    protected function deriveItemCodePartsFromLeadingCells(array $cells, array $roles)
    {
        $reserved = array_flip(array_merge(
            isset($roles['description']) ? $roles['description'] : array(),
            isset($roles['complement']) ? $roles['complement'] : array(),
            isset($roles['reference']) ? $roles['reference'] : array(),
            isset($roles['request_code']) ? $roles['request_code'] : array(),
            isset($roles['date']) ? $roles['date'] : array(),
            isset($roles['date_tail']) ? $roles['date_tail'] : array(),
            isset($roles['quantity']) && $roles['quantity'] !== null ? array($roles['quantity']) : array(),
            isset($roles['unit']) && $roles['unit'] !== null ? array($roles['unit']) : array()
        ));

        $candidates = array();
        foreach ($cells as $index => $text) {
            if (isset($reserved[$index])) {
                continue;
            }

            $text = PdfParserUtils::normalizeLine($text);
            if ($text === '' || $this->isWeakCodeAnchor(array($text))) {
                continue;
            }

            if (preg_match('/^\d{1,6}$/u', $text) || preg_match('/^\d{2,}\s+\d{2,}$/u', $text) || PdfParserUtils::looksLikeCode($text)) {
                $candidates[] = $text;
            }
        }

        return $candidates;
    }

    protected function isWeakCodeAnchor(array $parts)
    {
        if (empty($parts)) {
            return true;
        }

        $text = PdfParserUtils::normalizeLine(implode(' ', $parts));
        return (bool) preg_match('/^\d{1,2}$/u', $text);
    }

    protected function descriptionMass(array $descParts, array $complementParts)
    {
        return strlen(PdfParserUtils::normalizeLine(implode(' ', array_merge($descParts, $complementParts))));
    }

    protected function findColumnPosition(array $columns, $index)
    {
        foreach ($columns as $position => $column) {
            if ((int) $column['index'] === (int) $index) {
                return $position;
            }
        }

        return null;
    }

    protected function columnXMin(array $columns, $index)
    {
        foreach ($columns as $column) {
            if ((int) $column['index'] === (int) $index) {
                return (float) $column['xMin'];
            }
        }

        return INF;
    }
}
