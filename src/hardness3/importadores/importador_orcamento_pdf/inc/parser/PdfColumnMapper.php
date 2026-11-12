<?php
namespace hardness;

class PdfColumnMapper
{
    public function map(PdfBlock $block, PdfTableHeaderDetectionResult $headerResult = null)
    {
        $map = new PdfColumnMap();

        if (!$headerResult || !$headerResult->exists) {
            $map->mode = 'heuristic';
            $map->hasHeader = false;
            $map->score = 0;
            return $map;
        }

        $map->mode = 'header';
        $map->hasHeader = true;
        $map->sourceLineNumber = $headerResult->lineNumber;
        $map->sourceLineText = $headerResult->lineText;
        $map->score = $headerResult->score;

        if (isset($headerResult->columnAnchors) && is_array($headerResult->columnAnchors) && !empty($headerResult->columnAnchors)) {
            $map->spans = $this->buildSpansFromAnchors($headerResult->columnAnchors);

            if (isset($headerResult->columnAnchors['code'])) {
                $map->columns['codeClient'] = 0;
            }
            if (isset($headerResult->columnAnchors['description'])) {
                $map->columns['description'] = 1;
            }
            if (isset($headerResult->columnAnchors['unit'])) {
                $map->columns['unit'] = 2;
            }
            if (isset($headerResult->columnAnchors['quantity'])) {
                $map->columns['quantity'] = 3;
            }
            if (isset($headerResult->columnAnchors['ncm'])) {
                $map->columns['notes'] = 4;
            }
        }

        foreach ($headerResult->columns as $index => $columnText) {
            $lower = strtolower($columnText);
            if ($this->matchesAny($lower, array('codigo', 'código', 'cod', 'cód', '#'))) {
                $map->columns['codeClient'] = $index;
            } elseif ($this->matchesAny($lower, array('descricao', 'descrição', 'desc', 'produto', 'nome', 'item'))) {
                $map->columns['description'] = $index;
            } elseif ($this->matchesAny($lower, array('un', 'und', 'unid', 'unidade'))) {
                $map->columns['unit'] = $index;
            } elseif ($this->matchesAny($lower, array('qtd', 'qtde', 'quantidade', 'quant'))) {
                $map->columns['quantity'] = $index;
            } elseif ($this->matchesAny($lower, array('ncm', 'obs', 'observação', 'observacao'))) {
                $map->columns['notes'] = $index;
            }
        }

        if ($map->columns['description'] === null && count($headerResult->columns) > 1) {
            $map->columns['description'] = 1;
        }

        if ($map->columns['quantity'] === null && count($headerResult->columns) > 0) {
            $map->columns['quantity'] = count($headerResult->columns) - 1;
        }

        return $map;
    }

    protected function buildSpansFromAnchors(array $anchors)
    {
        $normalized = array();
        foreach ($anchors as $field => $anchor) {
            if (!isset($anchor['start'])) {
                continue;
            }

            $targetField = $this->anchorFieldToMapField($field);
            if ($targetField === '') {
                continue;
            }

            $normalized[] = array(
                'field' => $targetField,
                'start' => (int) $anchor['start'],
            );
        }

        usort($normalized, function ($a, $b) {
            if ($a['start'] === $b['start']) {
                return strcmp($a['field'], $b['field']);
            }
            return $a['start'] < $b['start'] ? -1 : 1;
        });

        $spans = array(
            'codeClient' => null,
            'description' => null,
            'unit' => null,
            'quantity' => null,
            'notes' => null,
        );

        $count = count($normalized);
        for ($i = 0; $i < $count; $i++) {
            $current = $normalized[$i];
            $next = isset($normalized[$i + 1]) ? $normalized[$i + 1] : null;
            $start = (int) $current['start'];
            $end = $next ? max($start, ((int) $next['start']) - 1) : null;

            if (!isset($spans[$current['field']]) || $spans[$current['field']] === null) {
                $spans[$current['field']] = array(
                    'start' => $start,
                    'end' => $end,
                );
            }
        }

        return $spans;
    }

    protected function anchorFieldToMapField($field)
    {
        switch ((string) $field) {
            case 'code':
                return 'codeClient';
            case 'description':
                return 'description';
            case 'unit':
                return 'unit';
            case 'quantity':
                return 'quantity';
            case 'ncm':
                return 'notes';
            default:
                return '';
        }
    }

    protected function matchesAny($value, array $needles)
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && strpos($value, strtolower($needle)) !== false) {
                return true;
            }
        }

        return false;
    }
}
