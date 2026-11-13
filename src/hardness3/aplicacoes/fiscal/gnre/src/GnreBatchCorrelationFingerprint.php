<?php
declare(strict_types=1);

/**
 * Contrato puro para correlacionar uma participante de lote com TDadosGNRE
 * devolvido pelo Portal. Não lê XML, não conhece banco, NF-e, recibo ou ambiente.
 *
 * A entrada usa os nomes oficiais v2.00 já materializados em array. A futura
 * camada de XML deve preservar a distinção entre tag ausente e tag vazia antes
 * de chamar este contrato.
 */
final class GnreBatchCorrelationFingerprint
{
    private const VERSION = 'GNRE-BATCH-CORRELATION-v1';

    /** @return array{fragment:string,fragment_sha256:string,fingerprint:string,complete:bool} */
    public static function describe(array $dados): array
    {
        $fragment = self::fragment($dados);
        return [
            'fragment' => self::json($fragment),
            'fragment_sha256' => hash('sha256', self::json($fragment)),
            'fingerprint' => hash('sha256', self::VERSION."\n".self::json(self::business($fragment))),
            'complete' => self::sufficient($dados),
        ];
    }

    /**
     * @param list<array<string,mixed>> $participants Cada participante contém
     *        id e tdados (o fragmento congelado antes do envio).
     * @return array{decision:string,participant_ids:list<string>,fingerprint:string}
     */
    public static function correlate(array $participants, ?array $returnedTdados): array
    {
        if ($returnedTdados === null || !self::hasPartialAnchors($returnedTdados)) {
            return ['decision'=>'INSUFFICIENT_EVIDENCE', 'participant_ids'=>[], 'fingerprint'=>''];
        }
        $returned = self::describe($returnedTdados);
        $matches = [];
        foreach ($participants as $participant) {
            if (!isset($participant['tdados']) || !is_array($participant['tdados'])) continue;
            $candidate = self::describe($participant['tdados']);
            if (!$candidate['complete']) continue;
            if (self::matchesReturnedEvidence(self::fragment($participant['tdados']), self::fragment($returnedTdados))) {
                $matches[] = (string)($participant['id'] ?? '');
            }
        }
        $decision = count($matches) === 1 ? 'MATCHED' : (count($matches) === 0 ? 'UNMATCHED' : 'AMBIGUOUS');
        return ['decision'=>$decision, 'participant_ids'=>$matches, 'fingerprint'=>$returned['fingerprint']];
    }

    /** Correlacionar nunca equivale a autorizar: somente código individual 0 autoriza. */
    public static function isAuthorizedSituation($situacaoGuia): bool
    {
        return (string)$situacaoGuia === '0';
    }

    /** Public diagnostic with field paths only; it never returns fiscal values. */
    public static function returnedEvidenceDifferences(array $candidateTdados,array $returnedTdados): array
    {
        $candidate=self::fragment($candidateTdados);$returned=self::fragment($returnedTdados);$out=[];
        foreach(['ufFavorecida','tipoGnre','valorGNRE','dataPagamento'] as $field)if(self::present($returned[$field]??null)&&$candidate[$field]!==$returned[$field])$out[]=$field;
        if(self::presentIdentity($returned['contribuinteEmitente']??[])&&$candidate['contribuinteEmitente']!==$returned['contribuinteEmitente'])$out[]='contribuinteEmitente.identificacao';
        foreach((array)$returned['itensGNRE'] as $returnedItem){if(!is_array($returnedItem))continue;$candidateItem=null;foreach((array)$candidate['itensGNRE'] as $item)if(is_array($item)&&(!self::present($returnedItem['receita']??null)||$item['receita']===$returnedItem['receita'])){$candidateItem=$item;break;}if(!is_array($candidateItem)){$out[]='itensGNRE.item';continue;}foreach(['receita','detalhamentoReceita','produto','dataVencimento','convenio'] as $field)if(self::present($returnedItem[$field]??null)&&$candidateItem[$field]!==$returnedItem[$field])$out[]='itensGNRE.item.'.$field;foreach(['periodo','mes','ano','parcela'] as $field)if(self::present($returnedItem['referencia'][$field]??null)&&$candidateItem['referencia'][$field]!==$returnedItem['referencia'][$field])$out[]='itensGNRE.item.referencia.'.$field;}
        return array_values(array_unique($out));
    }

    /** @return array<string,mixed> */
    private static function fragment(array $d): array
    {
        $items = self::list($d['itensGNRE']['item'] ?? ($d['itensGNRE'] ?? []));
        $normalized = [];
        foreach ($items as $item) if (is_array($item)) $normalized[] = self::item($item);
        usort($normalized, static function (array $a, array $b): int { return strcmp(self::json($a), self::json($b)); });
        return [
            'ufFavorecida'=>self::text($d['ufFavorecida'] ?? ''),
            'tipoGnre'=>self::code($d['tipoGnre'] ?? ''),
            'contribuinteEmitente'=>self::identity($d['contribuinteEmitente']['identificacao'] ?? ($d['contribuinteEmitente'] ?? [])),
            'itensGNRE'=>$normalized,
            'valorGNRE'=>self::money($d['valorGNRE'] ?? ''),
            'dataPagamento'=>self::date($d['dataPagamento'] ?? ''),
            'identificadorGuia'=>self::code($d['identificadorGuia'] ?? ''),
        ];
    }

    /** @return array<string,mixed> */
    private static function item(array $item): array
    {
        $values=[]; foreach (self::list($item['valor'] ?? []) as $value) {
            if (!is_array($value)) continue;
            $values[]=['tipo'=>self::code($value['tipo'] ?? ''),'valor'=>self::money($value['valor'] ?? ($value['value'] ?? ''))];
        }
        usort($values, static function (array $a, array $b): int { return strcmp(self::json($a), self::json($b)); });
        $extras=[]; foreach (self::list($item['camposExtras']['campoExtra'] ?? ($item['camposExtras'] ?? [])) as $extra) {
            if (!is_array($extra)) continue;
            $extras[]=['codigo'=>self::code($extra['codigo'] ?? ''),'valor'=>self::text($extra['valor'] ?? '')];
        }
        usort($extras, static function (array $a, array $b): int { return strcmp(self::json($a), self::json($b)); });
        $doc=$item['documentoOrigem'] ?? [];
        if (!is_array($doc)) $doc=['valor'=>$doc];
        return [
            'receita'=>self::code($item['receita'] ?? ''),
            'detalhamentoReceita'=>self::code($item['detalhamentoReceita'] ?? ''),
            'documentoOrigem'=>['tipo'=>self::code($doc['tipo'] ?? ''),'valor'=>self::text($doc['valor'] ?? ($doc['value'] ?? ''))],
            'produto'=>self::code($item['produto'] ?? ''),
            'referencia'=>['periodo'=>self::code($item['referencia']['periodo'] ?? ''),'mes'=>self::code($item['referencia']['mes'] ?? ''),'ano'=>self::code($item['referencia']['ano'] ?? ''),'parcela'=>self::code($item['referencia']['parcela'] ?? '')],
            'dataVencimento'=>self::date($item['dataVencimento'] ?? ''),
            'valor'=>$values,
            'convenio'=>self::text($item['convenio'] ?? ''),
            'contribuinteDestinatario'=>self::identity($item['contribuinteDestinatario']['identificacao'] ?? ($item['contribuinteDestinatario'] ?? [])),
            'camposExtras'=>$extras,
        ];
    }

    /** Minimum evidence deliberately excludes controls and technical state. */
    private static function sufficient(array $d): bool
    {
        /* Some official UF layouts use only the item values and omit the
           optional aggregate valorGNRE.  A validated TDadosGNRE remains a
           usable correlation fact in that case. */
        if (!array_key_exists('ufFavorecida', $d) || !array_key_exists('tipoGnre', $d)) return false;
        $items = self::list($d['itensGNRE']['item'] ?? ($d['itensGNRE'] ?? []));
        if (!$items) return false;
        foreach ($items as $item) {
            /* Receitas configuradas com valor N legitimately have no <valor>.
               Receita + the immutable item structure still identify the XML. */
            if (!is_array($item) || !array_key_exists('receita', $item)) return false;
        }
        return self::text($d['ufFavorecida']) !== '' && self::code($d['tipoGnre']) !== '';
    }

    /** A result is a partial observation, not a second copy of TDadosGNRE. */
    private static function hasPartialAnchors(array $d): bool
    {
        return self::text($d['ufFavorecida'] ?? '')!==''
            && self::code($d['tipoGnre'] ?? '')!==''
            && self::money($d['valorGNRE'] ?? '')!=='';
    }

    /**
     * Every business value actually returned must equal the frozen value.
     * Omitted optional fields impose no constraint; document/NF-e identifiers
     * and generated controls never participate in matching.
     */
    private static function matchesReturnedEvidence(array $candidate,array $returned): bool
    {
        foreach(['ufFavorecida','tipoGnre','valorGNRE','dataPagamento'] as $field) {
            if(self::present($returned[$field] ?? null) && $candidate[$field]!==$returned[$field]) return false;
        }
        if(self::presentIdentity($returned['contribuinteEmitente'] ?? [])) {
            if(($candidate['contribuinteEmitente'] ?? [])!==$returned['contribuinteEmitente']) return false;
        }
        foreach((array)($returned['itensGNRE'] ?? []) as $returnedItem) {
            if(!is_array($returnedItem)) continue;
            $found=false;foreach((array)($candidate['itensGNRE'] ?? []) as $candidateItem) {
                if(is_array($candidateItem) && self::itemMatchesReturnedEvidence($candidateItem,$returnedItem)) {$found=true;break;}
            }
            if(!$found)return false;
        }
        return true;
    }
    private static function itemMatchesReturnedEvidence(array $candidate,array $returned): bool
    {
        foreach(['receita','detalhamentoReceita','produto','dataVencimento','convenio'] as $field)if(self::present($returned[$field] ?? null)&&($candidate[$field]??null)!==$returned[$field])return false;
        if(self::presentIdentity($returned['contribuinteDestinatario'] ?? []))if(($candidate['contribuinteDestinatario']??[])!==$returned['contribuinteDestinatario'])return false;
        foreach(['periodo','mes','ano','parcela'] as $field)if(self::present($returned['referencia'][$field]??null)&&($candidate['referencia'][$field]??null)!==$returned['referencia'][$field])return false;
        foreach((array)($returned['valor']??[]) as $returnedValue){if(!is_array($returnedValue))continue;$found=false;foreach((array)($candidate['valor']??[]) as $candidateValue)if(is_array($candidateValue)&&$candidateValue===$returnedValue){$found=true;break;}if(!$found)return false;}
        foreach((array)($returned['camposExtras']??[]) as $returnedExtra){if(!is_array($returnedExtra))continue;$found=false;foreach((array)($candidate['camposExtras']??[]) as $candidateExtra)if(is_array($candidateExtra)&&$candidateExtra===$returnedExtra){$found=true;break;}if(!$found)return false;}
        return true;
    }
    private static function present($value): bool {return is_array($value)?(bool)$value:trim((string)$value)!=='';}
    private static function presentIdentity($value): bool {return is_array($value)&&self::present($value['valor']??'');}

    /** @param array<string,mixed> $fragment @return array<string,mixed> */
    private static function business(array $fragment): array
    {
        /* documentoOrigem (inclusive tipo) e camposExtras podem carregar
           NF-e. identificadorGuia consta no XSD de envio/retorno, mas o
           contrato local não lhe atribui semântica pública que prove não ser
           identificador interno; por cautela fica somente na auditoria. */
        $items=[];
        foreach ($fragment['itensGNRE'] as $item) $items[]=[
            'receita'=>$item['receita'],
            'detalhamentoReceita'=>$item['detalhamentoReceita'],
            'produto'=>$item['produto'],
            'referencia'=>$item['referencia'],
            'dataVencimento'=>$item['dataVencimento'],
            'valor'=>$item['valor'],
            'convenio'=>$item['convenio'],
            'contribuinteDestinatario'=>$item['contribuinteDestinatario'],
        ];
        return [
            'ufFavorecida'=>$fragment['ufFavorecida'],
            'tipoGnre'=>$fragment['tipoGnre'],
            'contribuinteEmitente'=>$fragment['contribuinteEmitente'],
            'itensGNRE'=>$items,
            'valorGNRE'=>$fragment['valorGNRE'],
            'dataPagamento'=>$fragment['dataPagamento'],
        ];
    }

    private static function identity($value): array
    {
        if (!is_array($value)) return ['tipo'=>'','valor'=>''];
        foreach (['CNPJ','CPF','IE'] as $type) if (array_key_exists($type, $value)) return ['tipo'=>$type,'valor'=>self::code($value[$type])];
        return ['tipo'=>self::text($value['tipo'] ?? ''),'valor'=>self::code($value['valor'] ?? ($value['value'] ?? ''))];
    }
    private static function list($value): array { if (!is_array($value) || !$value) return []; return array_keys($value) === range(0, count($value)-1) ? $value : [$value]; }
    private static function text($value): string { $value=trim(preg_replace('/\s+/u',' ',(string)$value) ?? ''); return function_exists('mb_strtoupper') ? mb_strtoupper($value,'UTF-8') : strtoupper($value); }
    private static function code($value): string { return preg_replace('/\s+/', '', (string)$value) ?? ''; }
    private static function money($value): string { $v=str_replace(' ','',trim((string)$value));if(strpos($v,',')!==false)$v=str_replace(['.',','],['','.'],$v);if ($v==='' || !preg_match('/^[+-]?\d+(?:\.\d+)?$/',$v)) return ''; $negative=$v[0]==='-'; if($negative)$v=substr($v,1); [$whole,$decimal]=array_pad(explode('.',$v,2),2,''); $whole=ltrim($whole,'0');if($whole==='')$whole='0';$decimal=rtrim($decimal,'0');return ($negative?'-':'').$whole.($decimal===''?'':'.'.$decimal); }
    private static function date($value): string { $v=trim((string)$value); foreach(['Y-m-d','d/m/Y','Y/m/d','d-m-Y'] as $format){$date=DateTime::createFromFormat('!'.$format,$v);if($date instanceof DateTime&&$date->format($format)===$v)return $date->format('Y-m-d');}return self::text($v); }
    private static function json(array $value): string { return (string)json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
}
