<?php
declare(strict_types=1);
namespace Hardness\Observability\Http;
use Hardness\Observability\Auth\AuthenticationService;
use Hardness\Observability\Security\LoginRateLimiter;
use Hardness\Observability\Security\ObservationSession;
use Hardness\Observability\Security\SecurityLogger;

final class AuthController
{
    private $auth,$session,$rate,$log;
    public function __construct(AuthenticationService $auth, ObservationSession $session, LoginRateLimiter $rate, SecurityLogger $log){$this->auth=$auth;$this->session=$session;$this->rate=$rate;$this->log=$log;}
    public function loginPage():Response { $this->session->start(); return $this->loginView(''); }
    public function login(Request $request):Response { $this->session->start(); $login=strtolower(trim($request->post('login'))); $ip=(string)($_SERVER['REMOTE_ADDR']??''); if(!$this->session->verifyCsrf($request->post('csrf'))){$this->log->event('csrf_failure',array('ip'=>$ip,'code'=>'login'));return $this->deny();} if($this->rate->blocked($ip,$login)){$this->log->event('login_rate_limited',array('ip'=>$ip,'login'=>hash('sha256',$login)));return $this->rateLimited();} try{$identity=$this->auth->authenticate($login,$request->post('password'),$_SERVER);}catch(\Throwable $e){$identity=null;} if($identity===null){$this->rate->failure($ip,$login);$this->log->event('login_failure',array('ip'=>$ip,'login'=>hash('sha256',$login)));return $this->deny();}$this->rate->success($ip,$login);$this->session->login($identity);$this->log->event('login_success',array('ip'=>$ip,'role'=>$identity['role'],'c007_id'=>$identity['c007_id']));return Response::redirect($this->basePath() . '/'); }
    public function logout(Request $request):Response{$this->session->start();if(!$this->session->verifyCsrf($request->post('csrf'))){return Response::json(array('error'=>'Acesso negado.'),403);}$this->session->logout();$this->log->event('logout');return Response::redirect($this->basePath() . '/login');}
    private function deny():Response{return $this->loginView('Login ou senha inválidos, ou acesso não autorizado.',403);}
    private function rateLimited():Response{return $this->loginView('Muitas tentativas de acesso. Por segurança, aguarde 15 minutos antes de tentar novamente.',429);}
    private function loginView(string $error, int $status=200): Response { $csrf=htmlspecialchars($this->session->csrf(),ENT_QUOTES,'UTF-8'); $base=$this->basePath(); $alert=$error!==''?'<div class="login-alert" role="alert">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>':''; return Response::html('<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="'.$base.'/assets/css/observability.css"><title>Observability — Login</title></head><body><main class="login-page"><section class="login-hero"><div class="login-brand"><img class="login-logo" src="'.$base.'/assets/img/logoHardnessH.svg" alt="Hardness"></div><form class="login-card" method="post" action="'.$base.'/auth/login"><div><p class="eyebrow">ACESSO PROTEGIDO</p><h1>Entrar</h1><p>Use suas credenciais autorizadas.</p></div>'.$alert.'<input type="hidden" name="csrf" value="'.$csrf.'"><label class="login-field"><span>Login</span><input name="login" placeholder="Usuário" autocomplete="username" required autofocus></label><label class="login-field"><span>Senha</span><input name="password" type="password" placeholder="Senha" autocomplete="current-password" required></label><button class="button login-submit" type="submit">Entrar</button></form></section></main></body></html>',$status); }
    private function basePath(): string { $path=(string)($_SERVER['REQUEST_URI']??''); return preg_match('#^(.*?/observability)(?:/|$)#',$path,$m)===1?$m[1]:'/observability'; }
}
