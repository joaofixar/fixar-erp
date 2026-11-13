<?php
declare(strict_types=1);
namespace HardnessMcp\Tools\Inecel\Certificate;
use HardnessMcp\Context\ExecutionContext;
final class CertificateDownloadLinkService {
    public const DOWNLOAD_TTL_SECONDS = 900;
    public const DOWNLOAD_TTL_MINUTES = 15;
    private string $rootUrl; private string $secret;
    public function __construct(string $rootUrl,string $secret){$this->rootUrl=rtrim($rootUrl,'/');$this->secret=$secret;}
    public function issue(int $documentId,string $fileName,ExecutionContext $context,?int $documentCompanyId=null): ?string {
        if($this->rootUrl===''||$this->secret===''||$context->getOAuthGrantId()===null)return null;
        $companyId=$documentCompanyId??$context->getCompanyId(); if($companyId<1)return null;
        $data=['d'=>$documentId,'c'=>$context->getCompanyId(),'s'=>$companyId,'g'=>$context->getOAuthGrantId(),'e'=>time()+self::DOWNLOAD_TTL_SECONDS,'n'=>$fileName];
        $body=rtrim(strtr(base64_encode(json_encode($data,JSON_UNESCAPED_SLASHES)),'+/','-_'),'=');
        $mac=rtrim(strtr(base64_encode(hash_hmac('sha256','inecel-certificate-download:v1:'.$body,$this->secret,true)),'+/','-_'),'=');
        return $this->rootUrl.'/mcp?certificate_download=1&token='.$body.'.'.$mac;
    }
    /** @return array{d:int,c:int,s:int,g:int,e:int,n:string}|null */
    public function verify(string $token): ?array {
        $p=explode('.',$token,2);if(count($p)!==2||preg_match('/\A[A-Za-z0-9_-]{20,600}\z/',$p[0])!==1||preg_match('/\A[A-Za-z0-9_-]{20,100}\z/',$p[1])!==1)return null;
        $expected=rtrim(strtr(base64_encode(hash_hmac('sha256','inecel-certificate-download:v1:'.$p[0],$this->secret,true)),'+/','-_'),'=');if(!hash_equals($expected,$p[1]))return null;
        $raw=base64_decode(strtr($p[0],'-_','+/').str_repeat('=',(4-strlen($p[0])%4)%4),true);$v=is_string($raw)?json_decode($raw,true):null;
        if(!is_array($v)||!is_int($v['d']??null)||!is_int($v['c']??null)||!is_int($v['s']??null)||!is_int($v['g']??null)||!is_int($v['e']??null)||!is_string($v['n']??null)||$v['d']<1||$v['c']<1||$v['s']<1||$v['g']<1||$v['e']<time()||$v['e']>time()+self::DOWNLOAD_TTL_SECONDS+1)return null;return $v;
    }
}
