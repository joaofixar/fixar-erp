<?php
declare(strict_types=1);

require_once __DIR__ . '/ResultInterpreter.php';
require_once __DIR__ . '/PendingResultPolicy.php';

/** Public, read-only classification of the latest persisted individual result. */
final class GnreQueueResultPresentation
{
    /** @return array{state:string,message:string} */
    public static function fromPersistedSoap(string $soap, bool $finalized): array
    {
        $parsed=GnreResultInterpreter::interpret($soap);
        $guides=(array)($parsed['guides']??[]);
        if (!empty($parsed['valid']) && count($guides)===1) {
            $guide=$guides[0]; $code=(string)($guide['code']??'');
            if ($code==='0') return ['state'=>'authorized','message'=>'Guia autorizada pelo Portal.'];
            if ($code==='1'||$code==='2') return ['state'=>'rejected','message'=>self::rejectedMessage((array)$guide)];
        }
        if ($finalized) return ['state'=>'inconclusive','message'=>'Guia teve resultado final inconclusivo. Consulte o Histórico para detalhes.'];
        return ['state'=>'','message'=>''];
    }

    /** @return array{state:string,message:string,description:string} */
    public static function pending(string $description): array
    {
        return ['state'=>'pending','message'=>GnrePendingResultPolicy::publicMessage(),'description'=>trim($description)];
    }

    /** @param array<string,mixed> $guide */
    private static function rejectedMessage(array $guide): string
    {
        $reasons=(array)($guide['rejections']??[]);
        if (!$reasons) return 'Guia rejeitada pelo Portal.';
        $reason=(array)$reasons[0]; $parts=[];
        $code=trim((string)($reason['code']??'')); $description=trim((string)($reason['description']??'')); $field=trim((string)($reason['field']??''));
        if ($code!==''&&$description!=='') $parts[]=$code.' — '.$description;
        elseif ($description!=='') $parts[]=$description;
        elseif ($code!=='') $parts[]=$code;
        if ($field!=='') $parts[]='Campo: '.$field;
        return 'Guia rejeitada pelo Portal'.($parts?': '.implode('. ',$parts):'.');
    }
}
