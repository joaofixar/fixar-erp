<?php
declare(strict_types=1);
require_once __DIR__ . '/TlsPolicy.php';
require_once __DIR__ . '/EnvironmentResolver.php';
require_once __DIR__ . '/ResultInterpreter.php';

/** Consulta de resultado no ambiente imutável do snapshot; não inclui código de NF-e. */
final class GnreResultClient
{
    private const NS = 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote';
    private const ACTION = 'http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar';

    /** @return array{kind:string,http:int,code:string,description:string,soap:string,error:string,retry_after:int} */
    public function consult(int $companyId, string $receipt, string $environment): array
    {
        return $this->consultWithPdf($companyId, $receipt, false, $environment);
    }

    /** Physical batch pilot only. The individual MVP path above stays strict
     * about a single guide; this variant accepts many individual results but
     * still requires every one to be 0, 1 or 2. */
    public function consultPhysicalBatch(int $companyId, string $receipt, string $environment): array
    {
        $result=$this->consultWithPdf($companyId, $receipt, false, $environment);
        if($result['kind']!=='pending' || trim((string)$result['soap'])==='')return $result;
        $parsed=GnreResultInterpreter::interpret((string)$result['soap']);
        $codes=array_map(static fn(array $guide):string=>(string)($guide['code']??''),(array)($parsed['guides']??[]));
        if($parsed['valid']&&$codes&&!array_diff($codes,['0','1','2']))$result['kind']='final';
        return $result;
    }

    /** Consulta explícita do documento oficial; nunca é usada pela fila normal. */
    public function consultOfficialPdf(int $companyId, string $receipt, string $environment): array
    {
        return $this->consultWithPdf($companyId, $receipt, true, $environment);
    }

    private function consultWithPdf(int $companyId, string $receipt, bool $includePdf, string $environment): array
    {
        $environment = GnreEnvironmentResolver::assertOperationalAllowed($environment);
        $service = GnreEnvironmentResolver::service($environment, 'result');
        if (!preg_match('/^\d{10}(\d{4})?$/', $receipt)) return $this->retry('Recibo GNRE inválido para consulta.');
        if (!function_exists('curl_init') || !function_exists('openssl_pkcs12_read') || !class_exists('DOMDocument')) return $this->retry('Recursos PHP para consulta GNRE não disponíveis.');
        try { $cert = $this->companyCertificate($companyId); $pem = $this->pemFromPfx($cert['path'], $cert['password']); }
        catch (RuntimeException $e) { return $this->retry($e->getMessage()); }
        try { return $this->send($this->envelope($receipt, $includePdf, $environment), $pem, $includePdf, $service); } finally { @unlink($pem); }
    }

    private function companyCertificate(int $companyId): array
    {
        $db = gnre_connect(); if (!$db) throw new RuntimeException('Base ERP indisponível para localizar o certificado A1.');
        try {
            $s = mysqli_prepare($db, "SELECT C004_Localizacao_Certificados,C004_Senha_Certificados FROM C004 WHERE C004_Id=? AND C004_Flag_Ativo='S' LIMIT 1");
            if (!$s) throw new RuntimeException('Não foi possível localizar o certificado A1 da empresa.');
            mysqli_stmt_bind_param($s, 'i', $companyId); mysqli_stmt_execute($s); $r=mysqli_stmt_get_result($s); $row=$r?mysqli_fetch_assoc($r):null; mysqli_stmt_close($s);
        } finally { mysqli_close($db); }
        $path=is_array($row)?rtrim((string)$row['C004_Localizacao_Certificados'],'/\\').'/'.$companyId.'.pfx':'';
        if ($path==='' || !is_readable($path)) throw new RuntimeException('Certificado A1 da empresa não está disponível para consulta GNRE.');
        return ['path'=>$path,'password'=>(string)$row['C004_Senha_Certificados']];
    }
    private function pemFromPfx(string $path, string $password): string
    {
        $raw=@file_get_contents($path); $data=[];
        if ($raw===false || !openssl_pkcs12_read($raw,$data,$password) || empty($data['pkey']) || empty($data['cert'])) throw new RuntimeException('Não foi possível abrir o certificado A1 da empresa.');
        $pem=tempnam(sys_get_temp_dir(),'gnre-tls-'); $chain=empty($data['extracerts'])?'':"\n".implode("\n",$data['extracerts']);
        if ($pem===false || @file_put_contents($pem,$data['pkey']."\n".$data['cert'].$chain)===false || !@chmod($pem,0600)) { if($pem)@unlink($pem); throw new RuntimeException('Não foi possível preparar o certificado A1 para consulta GNRE.'); }
        return $pem;
    }
    private function envelope(string $receipt, bool $includePdf, string $environment): string
    {
        $data='<TConsLote_GNRE xmlns="http://www.gnre.pe.gov.br"><ambiente>'.$environment.'</ambiente><numeroRecibo>'.$receipt.'</numeroRecibo><incluirPDFGuias>'.($includePdf ? 'S' : 'N').'</incluirPDFGuias><incluirArquivoPagamento>N</incluirArquivoPagamento></TConsLote_GNRE>';
        return '<?xml version="1.0" encoding="UTF-8"?><soap12:Envelope xmlns:soap12="http://www.w3.org/2003/05/soap-envelope"><soap12:Header><gnreCabecMsg xmlns="'.self::NS.'"><versaoDados>2.00</versaoDados></gnreCabecMsg></soap12:Header><soap12:Body><gnreDadosMsg xmlns="'.self::NS.'">'.$data.'</gnreDadosMsg></soap12:Body></soap12:Envelope>';
    }
    private function send(string $request, string $pem, bool $includePdf, array $service): array
    {
        $curl=curl_init($service['endpoint']); if($curl===false)return $this->retry('Não foi possível iniciar a consulta GNRE.');
        curl_setopt_array($curl,GnreTlsPolicy::curlOptions()+[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$request,CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true,CURLOPT_CONNECTTIMEOUT=>15,CURLOPT_TIMEOUT=>45,CURLOPT_SSLCERT=>$pem,CURLOPT_SSLCERTTYPE=>'PEM',CURLOPT_HTTPHEADER=>['Content-Type: application/soap+xml; charset=utf-8; action="'.self::ACTION.'"']]);
        $raw=curl_exec($curl); $error=curl_error($curl); $http=(int)curl_getinfo($curl,CURLINFO_HTTP_CODE); $headerSize=(int)curl_getinfo($curl,CURLINFO_HEADER_SIZE); curl_close($curl);
        if(!is_string($raw))return $this->retry(GnreTlsPolicy::connectionFailure('consulta de resultado',$error)->getMessage(),$http);
        $headers=substr($raw,0,$headerSize); $soap=substr($raw,$headerSize); $limit=$includePdf ? 14*1024*1024 : 2*1024*1024; if(strlen($soap)>$limit)return $this->retry('Resposta SOAP excede o limite permitido.',$http,$soap);
        if($http===429)return $this->retry('Portal GNRE limitou temporariamente as consultas (HTTP 429).',$http,$soap,$this->retryAfter($headers));
        /* With a persisted receipt, the national service uses 400/401 while
           processing. Keep the lot pending; neither status is a rejection. */
        if($http===400||$http===401)return $this->processing($http,$soap);
        if($http<200||$http>=300)return $this->retry('HTTP '.$http.' na consulta GNRE'.($error===''?'.':': '.$error),$http,$soap);
        return $this->parse($soap,$http);
    }
    private function parse(string $soap,int $http): array
    {
        if(!class_exists('DOMDocument')){
            $parsed=GnreResultInterpreter::interpret($soap);$guides=(array)($parsed['guides']??[]);$lot=(array)($parsed['lot']??[]);
            if(!$parsed['valid'])return $this->retry('Resposta SOAP inválida na consulta GNRE.',$http,$soap);
            if(count($guides)!==1)return ['kind'=>'pending','http'=>$http,'code'=>(string)($lot['code']??''),'description'=>(string)($lot['description']??''),'soap'=>$soap,'error'=>'','retry_after'=>0];
            $guideCode=(string)($guides[0]['code']??'');return ['kind'=>in_array($guideCode,['0','1','2'],true)?'final':'pending','http'=>$http,'code'=>(string)($lot['code']??''),'description'=>(string)($lot['description']??''),'soap'=>$soap,'error'=>'','retry_after'=>0];
        }
        $old=libxml_use_internal_errors(true);$dom=new DOMDocument();$ok=$dom->loadXML($soap,LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($old);
        if(!$ok)return $this->retry('Resposta SOAP inválida na consulta GNRE.',$http,$soap); $xp=new DOMXPath($dom);
        if($xp->query('//*[local-name()="Fault"]')->length)return $this->retry('SOAP Fault na consulta GNRE.',$http,$soap);
        $result=$xp->query('//*[local-name()="TResultLote_GNRE"]')->item(0); if(!$result instanceof DOMElement)return $this->retry('Resposta SOAP sem resultado de lote.',$http,$soap);
        $v=static function(string $q)use($xp,$result):string{return trim((string)$xp->evaluate('string('.$q.')',$result));};
        $code=$v('./*[local-name()="situacaoProcess"]/*[local-name()="codigo"]');$description=$v('./*[local-name()="situacaoProcess"]/*[local-name()="descricao"]');
        /* Uma guia só pode finalizar com uma evidência individual, única e
           conclusiva. Lote 400/401/403, ausência, multiplicidade, situação 4
           ou código desconhecido seguem pendentes: recibo existente não é emissão. */
        $guides=$xp->query('./*[local-name()="resultado"]/*[local-name()="guia"]',$result);
        if(!$guides || $guides->length!==1)return ['kind'=>'pending','http'=>$http,'code'=>$code,'description'=>$description,'soap'=>$soap,'error'=>'','retry_after'=>0];
        $guide=$guides->item(0);$guideCode=$guide instanceof DOMElement?trim((string)$xp->evaluate('string(./*[local-name()="situacaoGuia"])',$guide)):'';
        return ['kind'=>in_array($guideCode,['0','1','2'],true)?'final':'pending','http'=>$http,'code'=>$code,'description'=>$description,'soap'=>$soap,'error'=>'','retry_after'=>0];
    }
    private function retryAfter(string $headers): int { return preg_match('/^Retry-After:\s*(\d+)/mi',$headers,$m)?max(1,min(86400,(int)$m[1])):60; }
    /** HTTP 400/401 after a confirmed receipt are Portal processing signals. */
    private function processing(int $http,string $soap): array
    {
        $parsed=GnreResultInterpreter::interpret($soap);
        $lot=(array)($parsed['lot']??[]);
        return ['kind'=>'pending','http'=>$http,'code'=>(string)($lot['code']??''),'description'=>(string)($lot['description']??''),'soap'=>$soap,'error'=>'','retry_after'=>30];
    }
    private function retry(string $error,int $http=0,string $soap='',int $after=0): array { return ['kind'=>'retry','http'=>$http,'code'=>'','description'=>'','soap'=>$soap,'error'=>$error,'retry_after'=>$after]; }
}
