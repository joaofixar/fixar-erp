<?php
declare(strict_types=1);

/** Immutable, company-scoped TConfigUf snapshots.  Session data is never a source of truth. */
final class GnreConfigUfSnapshot
{
    private const XSD = GNRE_MODULE . '/docs/referencias/gnre-v2.17/config_uf_v1.00.xsd';

    public static function validate(string $xml, string $expectedEnvironment): array
    {
        if (!class_exists('DOMDocument')) throw new RuntimeException('A extensão DOM é necessária para validar a configuração GNRE.');
        $old = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $loaded = $dom->loadXML($xml, LIBXML_NONET);
        $valid = $loaded && $dom->schemaValidate(self::XSD);
        libxml_clear_errors(); libxml_use_internal_errors($old);
        if (!$valid) throw new RuntimeException('A configuração oficial não passou na validação XML/XSD local.');
        $xp = new DOMXPath($dom);
        $environment = trim((string)$xp->evaluate('string(/*[local-name()="TConfigUf"]/*[local-name()="ambiente"])'));
        if (!in_array($expectedEnvironment, ['1','2'], true) || $environment !== $expectedEnvironment) throw new RuntimeException('A configuração oficial pertence a outro ambiente GNRE.');
        $rules = GnreConfigUfRules::fromXml($xml);
        $rules['environment'] = $environment;
        if (($rules['uf'] ?? '') === '') throw new RuntimeException('A configuração oficial não informa a UF.');
        return $rules;
    }

    /** Persists only a fully validated response. Same company/environment/UF/XML is reused. */
    public static function persist(mysqli $db, array $context, string $xml, array $rules): array
    {
        $uf = strtoupper((string)($rules['uf'] ?? ''));
        if (!preg_match('/^[A-Z]{2}$/', $uf)) throw new RuntimeException('UF inválida na configuração oficial.');
        $hash = hash('sha256', $xml);
        $company = (int)$context['company_id']; $environment = (string)($rules['environment'] ?? '');
        if (!in_array($environment, ['1','2'], true)) throw new RuntimeException('Ambiente inválido para o snapshot oficial.');
        $q = mysqli_prepare($db, 'SELECT * FROM GNRE007 WHERE GNRE007_C004_Id=? AND GNRE007_Ambiente=? AND GNRE007_UF=? AND GNRE007_XML_SHA256=? LIMIT 1');
        mysqli_stmt_bind_param($q, 'isss', $company, $environment, $uf, $hash); mysqli_stmt_execute($q);
        $result = mysqli_stmt_get_result($q); $row = $result ? mysqli_fetch_assoc($result) : null; mysqli_stmt_close($q);
        if ($row) return $row;
        $json = json_encode($rules, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $user = (int)($context['user_id'] ?? 0);
        $origin = 'PORTAL_MANUAL';
        $q = mysqli_prepare($db, 'INSERT INTO GNRE007 (GNRE007_C004_Id,GNRE007_Ambiente,GNRE007_UF,GNRE007_XML,GNRE007_Regras_JSON,GNRE007_XML_SHA256,GNRE007_Data_Hora_Consulta,GNRE007_C007_Id_Consulta,GNRE007_Origem) VALUES (?,?,?,?,?,?,NOW(),NULLIF(?,0),?)');
        mysqli_stmt_bind_param($q, 'isssssis', $company, $environment, $uf, $xml, $json, $hash, $user, $origin);
        $ok = mysqli_stmt_execute($q); $id = (int)mysqli_insert_id($db); mysqli_stmt_close($q);
        if (!$ok || !$id) {
            /* Concurrent identical insertion is safe to reuse. */
            $q = mysqli_prepare($db, 'SELECT * FROM GNRE007 WHERE GNRE007_C004_Id=? AND GNRE007_Ambiente=? AND GNRE007_UF=? AND GNRE007_XML_SHA256=? LIMIT 1');
            mysqli_stmt_bind_param($q, 'isss', $company, $environment, $uf, $hash); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $row=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($q);
            if (!$row) throw new RuntimeException('Não foi possível persistir a configuração oficial.');
            return $row;
        }
        $created = self::byId($db, $id, $context);
        if (!$created) throw new RuntimeException('Snapshot oficial recém-criado não foi localizado.');
        return $created;
    }

    public static function byId(mysqli $db, int $id, array $context): ?array
    {
        $company=(int)$context['company_id']; $q=mysqli_prepare($db,'SELECT * FROM GNRE007 WHERE GNRE007_Id=? AND GNRE007_C004_Id=? LIMIT 1'); mysqli_stmt_bind_param($q,'ii',$id,$company); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $row=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($q); return $row?:null;
    }

    /** Local-only candidates for the progressive draft flow. Newest first. */
    public static function availableForUf(mysqli $db, array $context, string $uf, string $environment): array
    {
        if (!in_array($environment, ['1','2'], true)) throw new RuntimeException('Ambiente inválido para o snapshot oficial.');
        $company=(int)$context['company_id']; $uf=strtoupper($uf);
        $q=mysqli_prepare($db,'SELECT GNRE007_Id,GNRE007_UF,GNRE007_Ambiente,GNRE007_XML_SHA256,GNRE007_Data_Hora_Consulta,GNRE007_Origem FROM GNRE007 WHERE GNRE007_C004_Id=? AND GNRE007_Ambiente=? AND GNRE007_UF=? ORDER BY GNRE007_Data_Hora_Consulta DESC,GNRE007_Id DESC');
        mysqli_stmt_bind_param($q,'iss',$company,$environment,$uf); mysqli_stmt_execute($q); $r=mysqli_stmt_get_result($q); $out=[];
        while($r&&($row=mysqli_fetch_assoc($r))) $out[]=$row;
        mysqli_stmt_close($q); return $out;
    }

    public static function forGuide(mysqli $db, array $guide, array $context): ?array
    {
        $id=(int)($guide['GNRE001_GNRE007_Id'] ?? 0); if (!$id) return null;
        $row=self::byId($db,$id,$context);
        if (!$row || $row['GNRE007_UF'] !== $guide['GNRE001_UF_Favorecida'] || !in_array((string)$row['GNRE007_Ambiente'], ['1','2'], true)) return null;
        try { $row['_rules']=json_decode((string)$row['GNRE007_Regras_JSON'],true,512,JSON_THROW_ON_ERROR); } catch (JsonException $e) { return null; }
        return is_array($row['_rules']) ? $row : null;
    }
}
