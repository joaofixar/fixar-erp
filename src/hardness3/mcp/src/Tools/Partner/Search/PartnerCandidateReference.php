<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Partner\Search;

use HardnessMcp\Context\ExecutionContext;

/**
 * Opaque display reference. It deliberately contains no partner identifier.
 * A later confirmed-write flow must replace this ephemeral display reference
 * with a server-persisted, connection/company-bound selection proof.
 */
final class PartnerCandidateReference
{
    private string $key; private int $ttl;
    public function __construct(string $pepper, int $ttl = 600) { if ($pepper === '') throw new \RuntimeException('Candidate reference key unavailable.'); $this->key=hash('sha256',$pepper,true); $this->ttl=max(60,min($ttl,3600)); }
    public function issue(int $id, ExecutionContext $c): string { $p=json_encode(['p'=>$id,'c'=>$c->getCompanyId(),'n'=>$c->getConnectionId(),'e'=>time()+$this->ttl]);$iv=random_bytes(12);$tag='';$x=openssl_encrypt((string)$p,'aes-256-gcm',$this->key,OPENSSL_RAW_DATA,$iv,$tag);if(!is_string($x))throw new \RuntimeException();return 'pc1_'.rtrim(strtr(base64_encode($iv.$tag.$x),'+/','-_'),'='); }
    public function resolve(string $ref, ExecutionContext $c): ?int { if(strpos($ref,'pc1_')!==0)return null;$raw=base64_decode(strtr(substr($ref,4),'-_','+/'),true);if(!is_string($raw)||strlen($raw)<29)return null;$p=openssl_decrypt(substr($raw,28),'aes-256-gcm',$this->key,OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16));$d=is_string($p)?json_decode($p,true):null;if(!is_array($d)||(int)($d['c']??0)!==$c->getCompanyId()||(int)($d['n']??0)!==$c->getConnectionId()||(int)($d['e']??0)<time()||(int)($d['p']??0)<=0)return null;return (int)$d['p']; }
}
