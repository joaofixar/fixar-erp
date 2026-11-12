<?php
namespace hardness;

class PdfParserUtils
{
    public static function normalizeLayoutLine($line)
    {
        $line = (string) $line;
        $line = str_replace("\t", ' ', $line);
        $line = preg_replace('/\x{00a0}/u', ' ', $line);
        return rtrim($line);
    }

    public static function normalizeLine($line)
    {
        $line = self::normalizeLayoutLine($line);
        $line = trim((string) $line);
        $line = preg_replace('/\s+/u', ' ', $line);
        $line = preg_replace('/\s*([A-Za-zÀ-ÿ0-9])\s*•\s*([A-Za-zÀ-ÿ0-9])/u', '$1 / $2', $line);
        return trim($line);
    }

    public static function splitColumns($line)
    {
        $line = self::normalizeLayoutLine($line);
        $line = trim((string) $line);
        if ($line === '') {
            return array();
        }

        return preg_split('/\s{2,}/u', $line, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function containsAny($value, array $needles)
    {
        $value = strtolower((string) $value);
        foreach ($needles as $needle) {
            $needle = strtolower((string) $needle);
            if ($needle !== '' && strpos($value, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function isEmailLine($line)
    {
        return (bool) preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $line);
    }

    public static function isPhoneLine($line)
    {
        return (bool) preg_match('/\b\d{2}\s?\d{4,5}\s?\d{4}\b/', $line);
    }

    public static function isTotalsLine($line)
    {
        $lower = strtolower($line);
        return self::containsAny($lower, array(
            'total',
            'totais',
            'descontos',
            'despesas',
            'frete',
            'ipi',
            'substituição tributária',
            'substituicao tributaria',
            'pagamentos',
            'prazo',
            'venc.',
            'valor',
            'portador',
            'informação adicionais contribuinte',
            'informacao adicionais contribuinte',
        ));
    }

    public static function isStandaloneNumberLine($line)
    {
        $line = trim((string) $line);
        return (bool) preg_match('/^\d+$/', $line);
    }

    public static function isProductHeaderLine($line)
    {
        $lower = strtolower(self::normalizeLine($line));
        $hits = 0;
        $aliases = array_values(array_unique(array_merge(
            self::headerAliasesFor('description'),
            self::headerAliasesFor('quantity'),
            self::headerAliasesFor('unit'),
            self::headerAliasesFor('code'),
            array('ncm', 'preço unit.', 'preco unit.', 'total', 'total r$', 'valor unit')
        )));

        foreach ($aliases as $alias) {
            if ($alias !== '' && strpos($lower, $alias) !== false) {
                $hits++;
            }
        }

        return $hits >= 3;
    }

    public static function isProductRowCandidate($line)
    {
        $line = self::normalizeLine($line);
        if ($line === '' || self::isEmailLine($line) || self::isPhoneLine($line) || self::isTotalsLine($line)) {
            return false;
        }

        if (!preg_match('/^\d{1,4}\s+/u', $line)) {
            return false;
        }

        return (bool) preg_match('/\d+(?:[.,]\d+)?$/u', $line) || (bool) preg_match('/\b(un|und|unid|ct|pc|kg|m|mm|cm)\b/i', $line);
    }

    public static function isHeaderOrClientLine($line)
    {
        $lower = strtolower($line);
        return self::containsAny($lower, array(
            'empresa',
            'cliente',
            'local',
            'data',
            'vendedor',
            'emissão',
            'emissao',
            'condições',
            'condicoes',
            'logística',
            'logistica',
            'endereço',
            'endereco',
            'cobrança',
            'cobranca',
            'telefone',
            'celular',
            'e-mail',
            'email',
            'contato',
            'documento',
            'razão social',
            'razao social',
            'nome fantasia',
        ));
    }

    public static function isSectionLine($line)
    {
        $line = self::normalizeLine($line);
        if ($line === '' || preg_match('/[:@]/', $line) || self::isEmailLine($line) || self::isPhoneLine($line) || self::isTotalsLine($line) || self::isProductHeaderLine($line) || self::isProductRowCandidate($line)) {
            return false;
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $line, $m1);
        $upper = preg_match_all('/[A-ZÀ-Ý]/u', $line, $m2);
        return strlen($line) > 8 && $letters > 0 && $upper >= max(4, (int) floor($letters * 0.75));
    }

    public static function isQuantityToken($value)
    {
        $value = trim((string) $value);
        return (bool) preg_match('/^\d+(?:[.,]\d+)?$/', $value);
    }

    public static function normalizeQuantity($value)
    {
        $value = trim((string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float) $value : null;
    }

    public static function isUnitToken($value)
    {
        $value = strtolower(trim((string) $value));
        return in_array($value, array('un', 'und', 'unid', 'unidade', 'um', 'kg', 'g', 'm', 'mm', 'cm', 'cx', 'kit', 'pct', 'par', 'jg', 'ct', 'pc'), true);
    }

    public static function isNcmToken($value)
    {
        $value = trim((string) $value);
        return (bool) preg_match('/^\d{6,8}$/', $value);
    }

    public static function normalizeUnit($value)
    {
        $value = strtolower(trim((string) $value));

        $map = array(
            'un' => 'UN',
            'und' => 'UN',
            'unid' => 'UN',
            'unidade' => 'UN',
            'um' => 'UN',
            'pc' => 'PC',
            'ct' => 'CT',
            'kg' => 'KG',
            'g' => 'G',
            'm' => 'M',
            'mm' => 'MM',
            'cm' => 'CM',
            'cx' => 'CX',
            'kit' => 'KIT',
            'pct' => 'PCT',
            'par' => 'PAR',
            'jg' => 'JG',
        );

        return isset($map[$value]) ? $map[$value] : strtoupper($value);
    }

    public static function looksLikeCode($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        return (bool) preg_match('/^\d{2,20}$/', $value)
            || (bool) preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z0-9.\-\/_]{2,20}$/', $value);
    }

    public static function isProductContinuationLine($line)
    {
        $line = self::normalizeLine($line);
        if ($line === '' || preg_match('/^\d{1,4}\s+/u', $line)) {
            return false;
        }

        if (self::isHeaderOrClientLine($line) || self::isEmailLine($line) || self::isPhoneLine($line) || self::isTotalsLine($line) || self::isProductHeaderLine($line) || self::isProductRowCandidate($line)) {
            return false;
        }

        if (strlen($line) < 12) {
            return false;
        }

        $letters = preg_match_all('/[A-Za-zÀ-ÿ]/u', $line, $mLetters);
        $digits = (bool) preg_match('/\d/u', $line);
        $hyphen = strpos($line, '-') !== false;

        return $letters >= 6 && ($digits || $hyphen);
    }

    public static function headerAliases()
    {
        return array(
            'description' => array(
                'descricao', 'descrição', 'descricao material', 'descrição material', 'desc',
                'produto', 'material', 'item/descricao', 'item/descrição', 'especificacao', 'especificação',
                'item descricao', 'item descrição',
            ),
            'quantity' => array(
                'qtd', 'qtd.', 'qtde', 'qte', 'quant', 'quant.', 'quantidade', 'qdade', 'qtdade', 'qt.',
            ),
            'unit' => array(
                'un', 'un.', 'und', 'und.', 'unid', 'unid.', 'unidade', 'um',
            ),
            'code' => array(
                'codigo', 'código', 'cod', 'cód', '#', 'ref', 'referencia', 'referência',
            ),
            'ncm' => array(
                'ncm',
            ),
        );
    }

    public static function headerAliasesFor($field)
    {
        $all = self::headerAliases();
        return isset($all[$field]) ? $all[$field] : array();
    }

    public static function detectHeaderAnchors($line)
    {
        $line = self::normalizeLayoutLine($line);
        if (trim($line) === '') {
            return array();
        }

        $lower = mb_strtolower($line, 'UTF-8');
        $anchors = array();

        foreach (self::headerAliases() as $field => $aliases) {
            $best = null;
            foreach ($aliases as $alias) {
                $alias = trim((string) $alias);
                if ($alias === '') {
                    continue;
                }

                $offset = mb_stripos($lower, mb_strtolower($alias, 'UTF-8'), 0, 'UTF-8');
                if ($offset === false) {
                    continue;
                }

                $startByte = strlen(mb_substr($line, 0, (int) $offset, 'UTF-8'));
                $aliasLen = mb_strlen($alias, 'UTF-8');
                $endByte = strlen(mb_substr($line, 0, (int) $offset + $aliasLen, 'UTF-8'));
                $candidate = array(
                    'field' => $field,
                    'label' => $alias,
                    'start' => $startByte,
                    'end' => $endByte,
                );

                if ($best === null || strlen($alias) > strlen($best['label'])) {
                    $best = $candidate;
                }
            }

            if ($best !== null) {
                $anchors[$field] = $best;
            }
        }

        uasort($anchors, function ($a, $b) {
            if ($a['start'] === $b['start']) {
                return strcmp($a['field'], $b['field']);
            }
            return $a['start'] < $b['start'] ? -1 : 1;
        });

        return $anchors;
    }

    public static function extractSliceByRange($line, $start, $end = null)
    {
        $line = self::normalizeLayoutLine($line);
        $length = strlen($line);
        $start = max(0, (int) $start);
        if ($start >= $length) {
            return '';
        }

        if ($end === null) {
            $slice = substr($line, $start);
        } else {
            $end = max($start, (int) $end);
            $slice = substr($line, $start, ($end - $start) + 1);
        }

        return trim((string) $slice);
    }
}
