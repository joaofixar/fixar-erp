<?php
declare(strict_types=1);
namespace Hardness\Observability\Security;
use Hardness\Observability\Auth\ClientAccessPolicyLoader;
use Hardness\Observability\Context\ObservationContext;
use Hardness\Observability\Context\ObservationContextProvider;

final class ObservationSession implements ObservationContextProvider
{
    private $hosts;
    private $access;
    public function __construct(TrustedHostResolver $hosts, ClientAccessPolicyLoader $access) { $this->hosts=$hosts; $this->access=$access; }
    public function start(): void { session_name('HARDNESS_OBSERVABILITY'); ini_set('session.use_strict_mode','1'); ini_set('session.use_only_cookies','1'); session_set_cookie_params(array('lifetime'=>0,'path'=>$this->basePath(),'secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'httponly'=>true,'samesite'=>'Strict')); session_start(); }
    public function csrf(): string { if (empty($_SESSION['obs_csrf'])) { $_SESSION['obs_csrf']=bin2hex(random_bytes(32)); } return $_SESSION['obs_csrf']; }
    public function verifyCsrf(string $token): bool { return isset($_SESSION['obs_csrf']) && hash_equals($_SESSION['obs_csrf'], $token); }
    public function login(array $identity): void { session_regenerate_id(true); $_SESSION['obs_identity']=$identity; $_SESSION['obs_created']=time(); $_SESSION['obs_seen']=time(); $_SESSION['obs_csrf']=bin2hex(random_bytes(32)); }
    public function logout(): void { $_SESSION=array(); if (session_status()===PHP_SESSION_ACTIVE) { session_destroy(); } }
    public function current(): ObservationContext
    {
        $i = isset($_SESSION['obs_identity']) ? $_SESSION['obs_identity'] : null; if (!is_array($i) || !isset($i['role'],$i['scope_label'],$i['capabilities'])) { throw new \RuntimeException('unauthenticated'); }
        $now=time(); $idle=$i['role']==='hardness_internal'?900:3600; $absolute=$i['role']==='hardness_internal'?3600:28800;
        if (empty($_SESSION['obs_seen']) || empty($_SESSION['obs_created']) || $now-(int)$_SESSION['obs_seen']>$idle || $now-(int)$_SESSION['obs_created']>$absolute) { $this->logout(); throw new \RuntimeException('session_expired'); }
        if ($i['role']==='client') {
            try { $resolved=$this->hosts->resolve($_SERVER); } catch (\Throwable $exception) { $this->logout(); throw new \RuntimeException('invalid_host'); }
            if (empty($i['request_host']) || empty($i['tenant_key']) || !hash_equals((string)$i['request_host'],(string)$resolved['host']) || !hash_equals((string)$i['tenant_key'],(string)$resolved['tenant_key'])) { $this->logout(); throw new \RuntimeException('invalid_host'); }
            $caps=$this->access->load()->capabilitiesFor((string)$i['tenant_key'],(int)$i['c007_id'],false);
            if ($caps===null) { $this->logout(); throw new \RuntimeException('client_access_revoked'); }
            $_SESSION['obs_identity']['capabilities']=$caps; $i['capabilities']=$caps;
        }
        $_SESSION['obs_seen']=$now; return new ObservationContext($i['role'],$i['scope_label'],$i['capabilities'],$i['role']==='client'?array($i['scope_label']):array('Ambiente A','Ambiente B','Ambiente C'));
    }
    public function publicState(): array
    {
        $context=$this->current(); $data=$context->toArray();
        return array('authenticated'=>true,'role'=>$data['role'],'capabilities'=>$data['capabilities'],'context'=>array('scope_label'=>$data['scopeLabel']),'expires_in_seconds'=>max(0,($data['role']==='hardness_internal'?900:3600)-(time()-(int)$_SESSION['obs_seen'])));
    }
    private function basePath(): string { $path=(string)($_SERVER['REQUEST_URI']??''); return preg_match('#^(.*?/observability)(?:/|$)#',$path,$m)===1?$m[1]:'/observability'; }
}
