<?php
declare(strict_types=1);

/** ERP municipality lookup: D020 identifies the city and D018 owns its UF. */
final class GnreMunicipality
{
    private const META_IBGE='gnre_municipality_ibge';
    private const META_EXTRAS='gnre_campos_extras';

    public static function gnreCode($code): string
    {
        $digits = preg_replace('/\D/', '', (string)$code) ?? '';
        return preg_match('/^\d{7}$/',$digits) ? substr($digits,2) : '';
    }
    public static function isIbge($code): bool { return (bool)preg_match('/^\d{7}$/',(string)$code); }

    /** GNRE003's legacy city field is char(5); preserve identity in its existing JSON payload. */
    public static function encodeExtras(array $extras,string $ibge): string
    { return json_encode([self::META_EXTRAS=>array_values($extras),self::META_IBGE=>$ibge],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?: ''; }
    public static function storedIbge(array $item): string
    { $data=json_decode((string)($item['GNRE003_Campos_Extras_JSON']??''),true);$ibge=is_array($data)?(string)($data[self::META_IBGE]??''):'';return self::isIbge($ibge)?$ibge:''; }
    /** Accept both legacy array-only extras and the Task 21 envelope. */
    public static function extras($json): ?array
    { if($json===null||$json==='')return []; $data=json_decode((string)$json,true);if(!is_array($data))return null;$list=true;$index=0;foreach($data as $key=>$_){if($key!==$index++){$list=false;break;}}return $list?$data:((isset($data[self::META_EXTRAS])&&is_array($data[self::META_EXTRAS]))?$data[self::META_EXTRAS]:null); }

    /** Pure helper, also used by synthetic tests. */
    public static function optionsFromRows(array $rows): array
    {
        $states=[];$cities=[];
        foreach ($rows as $row) {
            $uf=strtoupper(trim((string)($row['uf']??$row['D018_UF']??'')));
            $code=preg_replace('/\D/','',(string)($row['code']??$row['D020_Codigo_IBGE']??''))??'';
            $name=trim((string)($row['name']??$row['D020_Nome_Cidade']??''));
            if (!preg_match('/^[A-Z]{2}$/',$uf)) continue;
            $states[$uf]=true;
            if (!self::isIbge($code) || $name==='') continue;
            $cities[$uf][$code]=['code'=>$code,'description'=>$name];
        }
        ksort($states); foreach($cities as &$list){ksort($list,SORT_NATURAL);$list=array_values($list);} unset($list);
        return ['states'=>array_map(static fn($uf)=>['code'=>$uf,'description'=>$uf],array_keys($states)),'cities'=>$cities];
    }

    public function options(): array
    {
        $db=gnre_connect(); if(!$db) return ['states'=>[],'cities'=>[]];
        try {$q=mysqli_query($db,'SELECT D018.D018_UF,D020.D020_Codigo_IBGE,D020.D020_Nome_Cidade FROM D020 INNER JOIN D018 ON D020.D020_D018_Id=D018.D018_Id ORDER BY D018.D018_UF,D020.D020_Nome_Cidade');$rows=[];while($q&&($row=mysqli_fetch_assoc($q)))$rows[]=$row;return self::optionsFromRows($rows);} finally {mysqli_close($db);}
    }

    public static function cityUfForCode(array $options,string $code): string
    {
        foreach((array)($options['cities']??[]) as $uf=>$cities) foreach($cities as $city) if(($city['code']??'')===$code) return (string)$uf;
        return '';
    }

    /** Resolves the browser search label without accepting an arbitrary municipality. */
    public static function codeForSearch(array $options,string $search,string $uf=''): string
    {
        $needle=mb_strtoupper(trim($search),'UTF-8'); if($needle==='') return '';
        foreach((array)($options['cities']??[]) as $state=>$cities) {
            if($uf!=='' && $state!==$uf) continue;
            foreach($cities as $city) {
                $short=mb_strtoupper((string)$city['code'].' — '.(string)$city['description'],'UTF-8');
                $label=$short.' ('.$state.')';
                if($needle===$label || $needle===$short || $needle===(string)$city['code'] || $needle===mb_strtoupper((string)$city['description'],'UTF-8')) return (string)$city['code'];
            }
        }
        return '';
    }
}
