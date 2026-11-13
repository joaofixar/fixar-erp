<?php
declare(strict_types=1);

/**
 * Interpreta somente o XML de retorno já recebido do GnreResultadoLote.
 * Não abre conexão, não consulta o Portal e não altera persistência.
 */
final class GnreResultInterpreter
{
    /** @return array{valid:bool,error:string,lot:array{code:string,description:string},guides:list<array<string,mixed>>} */
    public static function interpret(string $soap): array
    {
        $empty = ['valid'=>false, 'error'=>'SOAP de resultado ausente ou inválido.', 'lot'=>['code'=>'', 'description'=>''], 'guides'=>[]];
        if (trim($soap) === '') return $empty;
        if (!class_exists('DOMDocument')) return self::interpretWithoutDom($soap, $empty);

        $old = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $loaded = $dom->loadXML($soap, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($old);
        if (!$loaded) return $empty;

        $xp = new DOMXPath($dom);
        if ($xp->query('//*[local-name()="Fault"]')->length) {
            $empty['error'] = 'SOAP de resultado contém Fault.';
            return $empty;
        }
        $result = $xp->query('//*[local-name()="TResultLote_GNRE"]')->item(0);
        if (!$result instanceof DOMElement) {
            $empty['error'] = 'SOAP de resultado não contém TResultLote_GNRE.';
            return $empty;
        }
        $text = static function (string $query, DOMNode $node) use ($xp): string {
            return trim((string) $xp->evaluate('string('.$query.')', $node));
        };
        $out = ['valid'=>true, 'error'=>'', 'lot'=>[
            'code'=>$text('./*[local-name()="situacaoProcess"]/*[local-name()="codigo"]', $result),
            'description'=>$text('./*[local-name()="situacaoProcess"]/*[local-name()="descricao"]', $result),
        ], 'guides'=>[]];
        $nodes = $xp->query('./*[local-name()="resultado"]/*[local-name()="guia"]', $result);
        if (!$nodes) return $out;
        foreach ($nodes as $node) {
            $code = $text('./*[local-name()="situacaoGuia"]', $node);
            $guide = self::guide($code);
            /* numeroControle é devolvido nos dados oficiais da própria guia;
               numeroControleFecp não é o controle operacional da GNRE. */
            $guide['control_number'] = $text('.//*[local-name()="numeroControle"][1]', $node);
            $guide['rejections'] = [];
            $reasons = $xp->query('./*[local-name()="motivosRejeicao"]/*[local-name()="motivo"]', $node);
            if ($reasons) foreach ($reasons as $reason) $guide['rejections'][] = [
                'code'=>$text('./*[local-name()="codigo"]', $reason),
                'description'=>$text('./*[local-name()="descricao"]', $reason),
                'field'=>$text('./*[local-name()="campo"]', $reason),
            ];
            $out['guides'][] = $guide;
        }
        return $out;
    }

    /** Fallback para instalações PHP do ERP sem ext-dom; não expande entidades. */
    private static function interpretWithoutDom(string $soap, array $empty): array
    {
        if (stripos($soap, '<!DOCTYPE') !== false || !preg_match('~<(?:[A-Za-z_][\\w.-]*:)?TResultLote_GNRE\\b[^>]*>(.*)</(?:[A-Za-z_][\\w.-]*:)?TResultLote_GNRE\\s*>~si', $soap, $root)) return $empty;
        $result = $root[1];
        $lot = self::tag($result, 'situacaoProcess');
        if ($lot === null) return $empty;
        $out = ['valid'=>true, 'error'=>'', 'lot'=>['code'=>self::text($lot, 'codigo'), 'description'=>self::text($lot, 'descricao')], 'guides'=>[]];
        $container = self::tag($result, 'resultado');
        if ($container === null) return $out;
        if (!preg_match_all('~<(?:[A-Za-z_][\\w.-]*:)?guia\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?guia\\s*>~si', $container, $guides)) return $out;
        foreach ($guides[1] as $xml) {
            $guide = self::guide(self::text($xml, 'situacaoGuia'));
            $guide['control_number'] = self::text($xml, 'numeroControle');
            $guide['rejections'] = [];
            $rejections = self::tag($xml, 'motivosRejeicao');
            if ($rejections !== null && preg_match_all('~<(?:[A-Za-z_][\\w.-]*:)?motivo\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?motivo\\s*>~si', $rejections, $reasons)) foreach ($reasons[1] as $reason) $guide['rejections'][] = ['code'=>self::text($reason, 'codigo'), 'description'=>self::text($reason, 'descricao'), 'field'=>self::text($reason, 'campo')];
            $out['guides'][] = $guide;
        }
        return $out;
    }

    private static function tag(string $xml, string $name): ?string
    {
        if (!preg_match('~<(?:[A-Za-z_][\\w.-]*:)?'.preg_quote($name, '~').'\\b[^>]*>(.*?)</(?:[A-Za-z_][\\w.-]*:)?'.preg_quote($name, '~').'\\s*>~si', $xml, $match)) return null;
        return $match[1];
    }

    private static function text(string $xml, string $name): string
    {
        $value = self::tag($xml, $name);
        return $value === null ? '' : trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_XML1, 'UTF-8'));
    }

    /** @return array{code:string,description:string,outcome:string,message:string} */
    private static function guide(string $code): array
    {
        $map = [
            '0'=>['Processada com sucesso', 'issued', 'Guia emitida/aprovada pelo Portal.'],
            '1'=>['Invalidada pelo Portal', 'rejected', 'Guia não foi emitida/autorizada: invalidada pelo Portal.'],
            '2'=>['Invalidada pela UF', 'rejected', 'Guia não foi emitida/autorizada: invalidada pela UF.'],
            '3'=>['Erro de comunicação', 'error', 'Guia não foi emitida/autorizada: erro de comunicação.'],
            '4'=>['Pendência de processamento', 'processing', 'Guia ainda está em processamento; não está emitida/autorizada.'],
        ];
        $value = $map[$code] ?? ['Situação individual ausente/desconhecida', 'unknown', 'Não foi possível confirmar emissão/autorização da guia.'];
        return ['code'=>$code, 'description'=>$value[0], 'outcome'=>$value[1], 'message'=>$value[2]];
    }
}
