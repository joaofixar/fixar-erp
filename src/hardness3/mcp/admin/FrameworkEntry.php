<?php

declare(strict_types=1);

namespace HardnessMcp\AdminUi;

use HardnessMcp\Admin\McpConnectionAdminQueryService;
use HardnessMcp\Admin\McpConnectionManagementService;
use HardnessMcp\Admin\McpConnectionAdminService;
use HardnessMcp\Admin\OAuthAdminService;
use HardnessMcp\Admin\McpConnectionEligibilityAdminService;
use HardnessMcp\AdminUi\Security\CsrfGuard;
use HardnessMcp\AdminUi\Security\AdministrativeRequestGuard;
use HardnessMcp\AdminUi\Authorization\AdminAuthorizationException;
use HardnessMcp\AdminUi\Context\ErpAdminContextFactory;
use HardnessMcp\AdminUi\Http\McpAdminController;
use PDOException;
use RuntimeException;
use Throwable;

/** Framework-only entrypoint. It is never loaded by mcp/public/index.php. */
final class FrameworkEntry
{
    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function createController(array $frameworkContext, array $connectionConfiguration): McpAdminController
    {
        $factory = new ErpAdminContextFactory();
        $context = $factory->createTenantContext($frameworkContext, $connectionConfiguration);
        $pdo = $factory->createPdo($connectionConfiguration);
        return new McpAdminController($context, new McpConnectionAdminQueryService($pdo), self::publicMcpUrl($connectionConfiguration));
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    private static function createManagementService(array $frameworkContext, array $connectionConfiguration): McpConnectionManagementService
    {
        $factory = new ErpAdminContextFactory();
        $context = $factory->createTenantContext($frameworkContext, $connectionConfiguration);
        return new McpConnectionManagementService($factory->createPdo($connectionConfiguration), $context->getInstallationHost(), $context->getClientIp(), $context->getUserId());
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    private static function createCredentialService(array $frameworkContext, array $connectionConfiguration): McpConnectionAdminService
    {
        $factory = new ErpAdminContextFactory();
        $context = $factory->createTenantContext($frameworkContext, $connectionConfiguration);
        $config = require dirname(__DIR__) . '/config/app.php';
        return new McpConnectionAdminService($factory->createPdo($connectionConfiguration), $config, $context->getInstallationHost(), $context->getClientIp(), $context->getUserId());
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    private static function createOAuthAdminService(array $frameworkContext, array $connectionConfiguration): OAuthAdminService
    {
        $factory = new ErpAdminContextFactory();
        $context = $factory->createTenantContext($frameworkContext, $connectionConfiguration);
        return new OAuthAdminService($factory->createPdo($connectionConfiguration), $context->getInstallationHost(), $context->getClientIp(), $context->getUserId());
    }
    private static function createEligibilityService(array $frameworkContext, array $connectionConfiguration): McpConnectionEligibilityAdminService
    { $factory=new ErpAdminContextFactory();$context=$factory->createTenantContext($frameworkContext,$connectionConfiguration);return new McpConnectionEligibilityAdminService($factory->createPdo($connectionConfiguration),$context->getInstallationHost(),$context->getClientIp(),$context->getUserId()); }

    public static function csrfToken(): string { return (new CsrfGuard())->token(); }
    /** OAuth authorization UI, reached only after the ERP's official login bridge. */
    public static function oauthAuthorizationResume(array $frameworkContext,array $connectionConfiguration): void
    {
        $tx=(string)($_REQUEST['tx']??'');$cookieName='__Host-hrd_oauth_'.$tx;$cookie=(string)($_COOKIE[$cookieName]??'');$user=(int)($frameworkContext['usuarioAtual']??0);
        if(preg_match('/^[A-Za-z0-9_-]{16,48}$/',$tx)!==1||$cookie===''||$user<1){self::oauthBridgeError($cookieName);return;}
        try {
            $factory=new ErpAdminContextFactory();$config=require dirname(__DIR__).'/config/app.php';$service=new \HardnessMcp\OAuth\OAuthAuthorizationConsentService($factory->createPdo($connectionConfiguration),$config);$fullCookie=$tx.'.'.$cookie;
            $action=(string)($_POST['action']??'');
            if ($action !== '') { self::assertAdministrativeMutationRequest(); }
            if($action==='cancel') { $result=$service->cancel($fullCookie,$user);self::clearOAuthCookie($cookieName);self::oauthRedirect($result['redirect_uri'],'access_denied',$result['state']);return; }
            if($action==='approve') { $result=$service->consent($fullCookie,$user,self::positiveId($_POST['connection_id']??null));self::clearOAuthCookie($cookieName);self::oauthCodeRedirect($result['redirect_uri'],$result['code'],$result['state']);return; }
            $connections=$service->eligibleConnections($fullCookie,$user);
            if(count($connections)===0) { $result=$service->cancel($fullCookie,$user);self::clearOAuthCookie($cookieName);self::oauthRedirect($result['redirect_uri'],'access_denied',$result['state']);return; }
            $selected=0;
            if(count($connections)===1) $selected=(int)$connections[0]['id'];
            if($action==='select') $selected=self::positiveId($_POST['connection_id']??null);
            if($selected>0) { $preview=$service->preview($fullCookie,$user,$selected);self::renderOAuthConsent($tx,$preview); }
            else self::renderOAuthSelection($tx,$connections);
        }
        catch(\Throwable $e){
            // Keep the browser response opaque, while retaining enough
            // server-side context to diagnose a failed OAuth resume.
            error_log(json_encode([
                'event'=>'hardness_mcp_oauth_resume_failure',
                'exception_class'=>get_class($e),
                'message'=>$e->getMessage(),
                'file'=>basename($e->getFile()),
                'line'=>$e->getLine(),
            ],JSON_UNESCAPED_SLASHES));
            self::oauthBridgeError($cookieName);
        }
        exit;
    }
    private static function clearOAuthCookie(string $cookieName): void { setcookie($cookieName,'',['expires'=>time()-3600,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']); }
    /** @param ?string $state */ private static function oauthRedirect(string $uri,string $error,$state): void { $query=['error'=>$error];if($state!==null)$query['state']=$state;header('Location: '.self::appendQuery($uri,$query),true,302); }
    /** @param ?string $state */ private static function oauthCodeRedirect(string $uri,string $code,$state): void { $query=['code'=>$code];if($state!==null)$query['state']=$state;header('Location: '.self::appendQuery($uri,$query),true,302); }
    /** @param array<string,string> $query */ private static function appendQuery(string $uri,array $query): string { return $uri.(strpos($uri,'?')===false?'?':'&').http_build_query($query,'','&',PHP_QUERY_RFC3986); }
    /** @param array<int,array<string,mixed>> $connections */ private static function renderOAuthSelection(string $tx,array $connections): void
    {
        $groups=[];
        foreach($connections as $c) {
            $companyId=(int)($c['company_id']??0);
            if($companyId<1) continue;
            if(!isset($groups[$companyId])) $groups[$companyId]=['name'=>(string)($c['company_name']??''),'connections'=>[]];
            $groups[$companyId]['connections'][]=$c;
        }
        $options='';
        foreach($groups as $group) {
            $company=(string)$group['name'];
            $companyConnections=$group['connections'];
            $groupOptions='';
            foreach($companyConnections as $c) {
                $groupOptions.='<label class="mcp-oauth-selection__option"><input required type="radio" name="connection_id" value="'.(int)$c['id'].'"><span class="mcp-oauth-selection__radio" aria-hidden="true"></span><span class="mcp-oauth-selection__option-copy"><strong>'.self::oauthEscape((string)$c['name']).'</strong></span><span class="mcp-oauth-selection__arrow" aria-hidden="true">›</span></label>';
            }
            $options.='<section class="mcp-oauth-selection__group"><h2>'.self::oauthEscape($company).'</h2><div class="mcp-oauth-selection__group-options">'.$groupOptions.'</div></section>';
        }

        $body='<main class="mcp-oauth-selection"><section class="mcp-oauth-selection__card" aria-labelledby="mcp-oauth-selection-title"><header class="mcp-oauth-selection__header"><img src="/hardness3/static/img/logoHardnessBranca.svg" alt="Hardness"><span>MCP</span></header><div class="mcp-oauth-selection__content"><p class="mcp-oauth-selection__eyebrow">Autorização de acesso</p><h1 id="mcp-oauth-selection-title">Escolha a conexão MCP</h1><p class="mcp-oauth-selection__description">Selecione a empresa e a conexão que este aplicativo poderá acessar.</p><form class="mcp-oauth-selection__form" method="post"><fieldset><legend>Conexões disponíveis</legend><div class="mcp-oauth-selection__groups">'.$options.'</div></fieldset>'.self::oauthCsrfField().'<input type="hidden" name="action" value="select"><div class="mcp-oauth-selection__actions"><button class="mcp-oauth-selection__continue" type="submit">Continuar</button></div></form><div class="mcp-oauth-selection__cancel">'.self::oauthCancelForm().'</div></div></section></main>';
        self::renderOAuthSelectionPage('Escolha a conexão MCP',$body);
    }
    /** @param array<string,mixed> $preview */ private static function renderOAuthConsent(string $tx,array $preview): void
    {
        $connection=$preview['connection'];
        $granted=array_fill_keys($preview['effective'],true);
        $catalog=[];
        foreach((new \HardnessMcp\Scopes\ScopeCatalog())->all() as $scope) $catalog[$scope['id']]=$scope;
        $scopes='';
        foreach($preview['requested'] as $scopeId) {
            $scope=$catalog[$scopeId]??['name'=>$scopeId,'description'=>'Permissão solicitada pelo aplicativo.'];
            $isGranted=isset($granted[$scopeId]);
            $state=$isGranted?'granted':'unavailable';
            $scopes.='<li class="mcp-oauth-consent__scope mcp-oauth-consent__scope--'.$state.'"><span class="mcp-oauth-consent__scope-icon" aria-hidden="true">'.($isGranted?'✓':'!').'</span><span><strong>'.self::oauthEscape((string)$scope['name']).'</strong><small>'.self::oauthEscape((string)$scope['description']).'</small></span><em>'.($isGranted?'Concedido':'Não disponível nesta conexão').'</em></li>';
        }
        $companyName = trim((string) ($connection["C004_Nome_Abreviado"] ?? ""));
        if ($companyName === "") { $companyName = (string) $connection["C004_Nome_Empresa"]; }
        $companyContext = "<div class=mcp-oauth-consent__company><span>Empresa conectada</span><strong>" . self::oauthEscape($companyName) . "</strong><small>Conexão: " . self::oauthEscape((string) $connection["MCP001_Nome"]) . "</small></div>";

        $details='<dl class="mcp-oauth-consent__details"><div><dt>Aplicativo</dt><dd>'.self::oauthEscape((string)($preview['client_name']??'')).'</dd></div><div><dt>Usuário</dt><dd>'.self::oauthEscape((string)$preview['user_name']).'</dd></div><div><dt>Empresa</dt><dd>'.self::oauthEscape((string)$connection['C004_Nome_Empresa']).'</dd></div><div><dt>Conexão</dt><dd>'.self::oauthEscape((string)$connection['MCP001_Nome']).'</dd></div></dl>';
        $body='<main class="mcp-oauth-selection mcp-oauth-consent"><section class="mcp-oauth-selection__card" aria-labelledby="mcp-oauth-consent-title"><header class="mcp-oauth-selection__header"><img src="/hardness3/static/img/logoHardnessBranca.svg" alt="Hardness"><span>MCP</span></header><div class="mcp-oauth-selection__content"><p class="mcp-oauth-selection__eyebrow">Autorização de acesso</p><h1 id="mcp-oauth-consent-title">Autorizar aplicativo MCP</h1><p class="mcp-oauth-selection__description">Você está autorizando este aplicativo a acessar informações do ERP utilizando a conexão selecionada.</p>'.$companyContext.'<div class="mcp-oauth-consent__body">'.$details.'<section class="mcp-oauth-consent__permissions" aria-labelledby="mcp-oauth-consent-permissions"><div><h2 id="mcp-oauth-consent-permissions">Permissões solicitadas</h2><p>As permissões indisponíveis não serão concedidas.</p></div><ul>'.$scopes.'</ul></section></div><form class="mcp-oauth-consent__approve" method="post">'.self::oauthCsrfField().'<input type="hidden" name="action" value="approve"><input type="hidden" name="connection_id" value="'.(int)$connection['MCP001_Id'].'"><button type="submit">Autorizar</button></form><div class="mcp-oauth-consent__cancel">'.self::oauthCancelForm().'</div></div></section></main>';
        self::renderOAuthSelectionPage('Autorizar aplicativo MCP',$body,self::oauthConsentCss());
    }
    private static function oauthCancelForm(): string { return '<form method="post">'.self::oauthCsrfField().'<input type="hidden" name="action" value="cancel"><button type="submit">Cancelar</button></form>'; }
    private static function oauthCsrfField(): string { return '<input type="hidden" name="csrf" value="'.self::oauthEscape((new CsrfGuard())->token()).'">'; }
    private static function renderOAuthPage(string $title,string $body): void { header('Content-Type: text/html; charset=utf-8');header('Cache-Control: no-store');echo '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.self::oauthEscape($title).'</title><style>body{font:16px system-ui;margin:3rem auto;max-width:42rem;color:#18212f}button{margin:1rem .5rem 0 0;padding:.6rem 1rem}label{display:block;margin:.7rem 0}dt{font-weight:700;margin-top:1rem}dd{margin:.25rem 0}</style><main><h1>'.self::oauthEscape($title).'</h1>'.$body.'</main></html>'; }
    private static function renderOAuthSelectionPage(string $title,string $body,string $extraCss=''): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
        echo '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.self::oauthEscape($title).'</title><style>
*{box-sizing:border-box}html,body{min-height:100%;margin:0}body{min-width:320px;color:#172033;background:#f5f7fb;font:15px/1.45 Arial,sans-serif}.mcp-oauth-selection{display:grid;min-height:100vh;place-items:center;padding:32px 20px;background:radial-gradient(circle at 12% 8%,#e3ebff 0,rgba(227,235,255,0) 31%),radial-gradient(circle at 88% 90%,#e8f0ff 0,rgba(232,240,255,0) 32%),#f5f7fb}.mcp-oauth-selection__card{display:flex;flex-direction:column;width:min(100%,840px);max-height:min(820px,calc(100vh - 64px));overflow:hidden;border:1px solid #dce5f3;border-radius:18px;background:#fff;box-shadow:0 20px 52px rgba(24,55,111,.14)}.mcp-oauth-selection__header{display:flex;flex:0 0 auto;align-items:center;justify-content:space-between;height:78px;padding:17px 26px;color:#fff;background:linear-gradient(125deg,#173dc4,#2860dc)}.mcp-oauth-selection__header img{display:block;width:124px;height:42px;object-fit:contain;object-position:left center}.mcp-oauth-selection__header span{padding:5px 9px;border:1px solid rgba(255,255,255,.3);border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.08em}.mcp-oauth-selection__content{display:flex;flex:1 1 auto;flex-direction:column;min-height:0;padding:32px 36px}.mcp-oauth-selection__eyebrow{margin:0 0 7px;color:#2860dc;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase}.mcp-oauth-selection h1{margin:0;color:#142a61;font-size:30px;letter-spacing:-.03em;line-height:1.15}.mcp-oauth-selection__description{flex:0 0 auto;margin:11px 0 24px;color:#617495;font-size:15px}.mcp-oauth-selection__form{display:flex;flex:1 1 auto;flex-direction:column;min-height:0}.mcp-oauth-selection__form fieldset{display:flex;flex:1 1 auto;flex-direction:column;min-height:0;margin:0;padding:0;border:0}.mcp-oauth-selection__form legend{flex:0 0 auto;margin:0;color:#526078;font-size:12px;font-weight:700}.mcp-oauth-selection__groups{display:grid;flex:1 1 auto;align-content:start;gap:24px;min-height:0;margin-top:18px;padding:1px 4px 1px 1px;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:#b7c8e6 transparent}.mcp-oauth-selection__groups::-webkit-scrollbar{width:7px}.mcp-oauth-selection__groups::-webkit-scrollbar-thumb{border-radius:999px;background:#b7c8e6}.mcp-oauth-selection__group{margin:0;padding:0;border:0}.mcp-oauth-selection__group h2{margin:0 0 9px;color:#617495;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.mcp-oauth-selection__group-options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.mcp-oauth-selection__option{display:grid;grid-template-columns:20px minmax(0,1fr) 16px;align-items:center;gap:12px;min-width:0;min-height:64px;padding:13px 16px;border:1px solid #dce5f3;border-radius:12px;color:#172033;background:#fff;cursor:pointer;transition:border-color .16s ease,background .16s ease,box-shadow .16s ease,transform .16s ease}.mcp-oauth-selection__option:hover{border-color:#b9cdf5;background:#f8fbff;box-shadow:0 5px 14px rgba(23,61,196,.08);transform:translateY(-1px)}.mcp-oauth-selection__option:focus-within{border-color:#2860dc;box-shadow:0 0 0 3px rgba(40,96,220,.14)}.mcp-oauth-selection__option input{position:absolute;inline-size:1px;block-size:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}.mcp-oauth-selection__radio{display:grid;width:20px;height:20px;place-items:center;border:2px solid #aab8cc;border-radius:50%;background:#fff}.mcp-oauth-selection__radio::after{width:8px;height:8px;border-radius:50%;background:#2860dc;content:"";transform:scale(0);transition:transform .15s ease}.mcp-oauth-selection__option input:checked+.mcp-oauth-selection__radio{border-color:#2860dc}.mcp-oauth-selection__option input:checked+.mcp-oauth-selection__radio::after{transform:scale(1)}.mcp-oauth-selection__option:has(input:checked){border-color:#8faff0;background:#f3f7ff}.mcp-oauth-selection__option-copy{min-width:0}.mcp-oauth-selection__option-copy strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.mcp-oauth-selection__option-copy strong{color:#172f6d;font-size:15px}.mcp-oauth-selection__arrow{color:#2860dc;font-size:25px;font-weight:300;line-height:1}.mcp-oauth-selection__actions{flex:0 0 auto;margin-top:22px}.mcp-oauth-selection__continue,.mcp-oauth-selection__cancel button{width:100%;min-height:44px;border-radius:10px;font:700 14px Arial,sans-serif;cursor:pointer;transition:background .16s ease,border-color .16s ease,box-shadow .16s ease,transform .16s ease}.mcp-oauth-selection__continue{border:1px solid #1e4ec8;color:#fff;background:#1e4ec8;box-shadow:0 5px 12px rgba(30,78,200,.2)}.mcp-oauth-selection__continue:hover{background:#173db0;box-shadow:0 7px 16px rgba(30,78,200,.26);transform:translateY(-1px)}.mcp-oauth-selection__continue:focus-visible,.mcp-oauth-selection__cancel button:focus-visible{outline:3px solid rgba(40,96,220,.26);outline-offset:2px}.mcp-oauth-selection__cancel{flex:0 0 auto;margin-top:11px}.mcp-oauth-selection__cancel form{margin:0}.mcp-oauth-selection__cancel button{border:1px solid transparent;color:#2860dc;background:transparent}.mcp-oauth-selection__cancel button:hover{border-color:#d4e0f5;background:#edf3ff}@media(max-width:700px){.mcp-oauth-selection__group-options{grid-template-columns:1fr}}@media(max-width:520px){.mcp-oauth-selection{place-items:start;padding:16px}.mcp-oauth-selection__card{max-height:calc(100vh - 32px);border-radius:14px}.mcp-oauth-selection__header{height:70px;padding:14px 20px}.mcp-oauth-selection__content{padding:26px 20px}.mcp-oauth-selection h1{font-size:26px}}
'.$extraCss.'</style></head><body>'.$body.'</body></html>';
    }
    private static function oauthConsentCss(): string { return '.mcp-oauth-consent__company{display:grid;gap:3px;margin:16px 0;padding:13px 16px;border:1px solid #b9cdf5;border-radius:12px;background:#f3f7ff}.mcp-oauth-consent__company span{color:#617495;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.mcp-oauth-consent__company strong{color:#172f6d;font-size:17px}.mcp-oauth-consent__company small{color:#526078;font-size:12px}.mcp-oauth-consent__body{display:grid;flex:1 1 auto;align-content:start;gap:18px;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:1px 4px 1px 1px;scrollbar-width:thin;scrollbar-color:#b7c8e6 transparent}.mcp-oauth-consent__body::-webkit-scrollbar{width:7px}.mcp-oauth-consent__body::-webkit-scrollbar-thumb{border-radius:999px;background:#b7c8e6}.mcp-oauth-consent__details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1px;margin:0;overflow:hidden;border:1px solid #dce5f3;border-radius:12px;background:#dce5f3}.mcp-oauth-consent__details div{min-width:0;padding:14px 16px;background:#fff}.mcp-oauth-consent__details dt{margin:0;color:#617495;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.mcp-oauth-consent__details dd{margin:5px 0 0;overflow:hidden;color:#172f6d;font-size:15px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}.mcp-oauth-consent__permissions{padding:18px;border:1px solid #dce5f3;border-radius:12px;background:#f8fbff}.mcp-oauth-consent__permissions h2{margin:0;color:#172f6d;font-size:15px}.mcp-oauth-consent__permissions>div>p{margin:4px 0 0;color:#617495;font-size:12px}.mcp-oauth-consent__permissions ul{display:grid;gap:8px;margin:15px 0 0;padding:0;list-style:none}.mcp-oauth-consent__scope{display:grid;grid-template-columns:24px minmax(0,1fr) auto;align-items:center;gap:10px;padding:10px 11px;border-radius:9px;background:#fff}.mcp-oauth-consent__scope-icon{display:grid;width:24px;height:24px;place-items:center;border-radius:50%;font-size:14px;font-weight:800}.mcp-oauth-consent__scope strong,.mcp-oauth-consent__scope small{display:block}.mcp-oauth-consent__scope strong{color:#172033;font-size:13px}.mcp-oauth-consent__scope small{margin-top:2px;color:#617495;font-size:11px}.mcp-oauth-consent__scope em{padding:4px 7px;border-radius:999px;font-size:10px;font-style:normal;font-weight:700;white-space:nowrap}.mcp-oauth-consent__scope--granted .mcp-oauth-consent__scope-icon,.mcp-oauth-consent__scope--granted em{color:#12633a;background:#e1f6e9}.mcp-oauth-consent__scope--unavailable{border:1px solid #f0c885;background:#fff9ec}.mcp-oauth-consent__scope--unavailable .mcp-oauth-consent__scope-icon,.mcp-oauth-consent__scope--unavailable em{color:#8a5a00;background:#fff0c9}.mcp-oauth-consent__approve{flex:0 0 auto;margin-top:22px}.mcp-oauth-consent__approve button,.mcp-oauth-consent__cancel button{width:100%;min-height:44px;border-radius:10px;font:700 14px Arial,sans-serif;cursor:pointer}.mcp-oauth-consent__approve button{border:1px solid #1e4ec8;color:#fff;background:#1e4ec8;box-shadow:0 5px 12px rgba(30,78,200,.2)}.mcp-oauth-consent__approve button:hover{background:#173db0}.mcp-oauth-consent__approve button:focus-visible,.mcp-oauth-consent__cancel button:focus-visible{outline:3px solid rgba(40,96,220,.26);outline-offset:2px}.mcp-oauth-consent__cancel{flex:0 0 auto;margin-top:11px}.mcp-oauth-consent__cancel form{margin:0}.mcp-oauth-consent__cancel button{border:1px solid transparent;color:#2860dc;background:transparent}.mcp-oauth-consent__cancel button:hover{border-color:#d4e0f5;background:#edf3ff}@media(max-width:620px){.mcp-oauth-consent__details{grid-template-columns:1fr}.mcp-oauth-consent__scope{grid-template-columns:24px minmax(0,1fr)}.mcp-oauth-consent__scope em{grid-column:2;justify-self:start}}'; }
    private static function oauthEscape(string $value): string { return htmlspecialchars($value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
    private static function oauthBridgeError(string $cookieName='hrd_oauth_authorization'): void { setcookie($cookieName,'',['expires'=>time()-3600,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']);http_response_code(400);header('Content-Type: text/html; charset=utf-8');echo '<!doctype html><title>Hardness MCP</title><p>Não foi possível continuar a autorização OAuth. Retorne ao aplicativo e tente novamente.</p>';exit; }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function credentialMutation(array $frameworkContext, array $connectionConfiguration, string $action, array $request): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $action, $request): array {
            self::assertAdministrativeMutationRequest();
            $company = self::positiveId($request['C004_Id'] ?? null); $connection = self::positiveId($request['MCP001_Id'] ?? null);
            $service = self::createCredentialService($frameworkContext, $connectionConfiguration);
            if ($action === 'generate') { return ['api_key' => $service->generateFirstCredential($connection, $company)]; }
            if ($action === 'rotate') { return ['api_key' => $service->rotateCredentialForCompany($connection, $company)]; }
            if ($action === 'revoke') { $service->revokeCredentialForCompany(self::positiveId($request['MCP002_Id'] ?? null), $connection, $company, (string) ($request['reason'] ?? 'Revogada pelo administrador.')); return []; }
            throw new RuntimeException('Invalid administrative request.');
        });
    }

    /** Internal ERP administration only; this creates no OAuth protocol route. */
    public static function oauthMutation(array $frameworkContext, array $connectionConfiguration, string $action, array $request): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $action, $request): array {
            self::assertAdministrativeMutationRequest();
            $service = self::createOAuthAdminService($frameworkContext, $connectionConfiguration);
            if ($action === 'client_create') {
                $redirect = $request['redirect_uri'] ?? null;
                if (!is_string($redirect)) { throw new RuntimeException('Invalid administrative request.'); }
                return ['client' => $service->createClient((string) ($request['client_id'] ?? ''), (string) ($request['name'] ?? ''), [$redirect])];
            }
            if ($action === 'client_update') { $service->renameClient(self::positiveId($request['MCP004_Id'] ?? null), (string) ($request['name'] ?? '')); return []; }
            if ($action === 'client_status') { $service->setClientStatus(self::positiveId($request['MCP004_Id'] ?? null), (string) ($request['status'] ?? '')); return []; }
            if ($action === 'redirect_add') { return ['redirect_uri_id' => $service->addRedirectUri(self::positiveId($request['MCP004_Id'] ?? null), (string) ($request['redirect_uri'] ?? ''))]; }
            if ($action === 'redirect_revoke') { $service->revokeRedirectUri(self::positiveId($request['MCP009_Id'] ?? null)); return []; }
            if ($action === 'grant_create') {
                $scopes = $request['scopes'] ?? null;
                if (!is_string($scopes)) { throw new RuntimeException('Invalid administrative request.'); }
                try { $scopes = json_decode($scopes, true, 32, JSON_THROW_ON_ERROR); } catch (\JsonException $exception) { throw new RuntimeException('Invalid administrative request.'); }
                return ['grant_id' => $service->createGrant(self::positiveId($request['C004_Id'] ?? null), self::positiveId($request['MCP001_Id'] ?? null), self::positiveId($request['MCP004_Id'] ?? null), self::positiveId($request['C007_Id'] ?? null), $scopes)];
            }
            if ($action === 'grant_revoke') { $service->revokeGrant(self::positiveId($request['C004_Id'] ?? null), self::positiveId($request['MCP001_Id'] ?? null), self::positiveId($request['MCP005_Id'] ?? null), (string) ($request['reason'] ?? 'Revogado pelo administrador.')); return []; }
            if ($action === 'grants_revoke_outdated') { return ['revoked_count' => $service->revokeOutdatedGrants(self::positiveId($request['C004_Id'] ?? null), self::positiveId($request['MCP001_Id'] ?? null))]; }
            throw new RuntimeException('Invalid administrative request.');
        });
    }
    public static function eligibilityMutation(array $frameworkContext, array $connectionConfiguration, string $action, array $request): void
    {
        self::mutationResponse(static function () use ($frameworkContext,$connectionConfiguration,$action,$request): array {
            self::assertAdministrativeMutationRequest();$service=self::createEligibilityService($frameworkContext,$connectionConfiguration);$company=self::positiveId($request['C004_Id']??null);
            if($action==='direct_add'){$service->addDirectUser($company,self::positiveId($request['MCP001_Id']??null),self::positiveId($request['C007_Id']??null));return [];}
            if($action==='direct_revoke'){$service->revokeDirectUser($company,self::positiveId($request['MCP001_Id']??null),self::positiveId($request['C007_Id']??null));return [];}
            if($action==='profile_add'){$service->addProfileToConnection($company,self::positiveId($request['MCP001_Id']??null),self::positiveId($request['C021_Id']??null));return [];}
            if($action==='profile_revoke'){$service->revokeProfileFromConnection($company,self::positiveId($request['MCP001_Id']??null),self::positiveId($request['C021_Id']??null));return [];}
            if($action==='all_users'){$enabled=filter_var($request['enabled']??null,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);if($enabled===null)throw new RuntimeException('Invalid administrative request.');$service->setAllUsers($company,self::positiveId($request['MCP001_Id']??null),$enabled);return [];}
            throw new RuntimeException('Invalid administrative request.');
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function updateConnectionScopes(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId, $rawScopes, $csrf): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId, $rawScopes, $csrf): array {
            self::assertAdministrativeMutationRequest();
            if (!is_string($rawScopes)) { throw new RuntimeException('Invalid administrative request.'); }
            try { $scopes = json_decode($rawScopes, true, 32, JSON_THROW_ON_ERROR); } catch (\JsonException $exception) { throw new RuntimeException('Invalid administrative request.'); }
            return ['scopes' => self::createCredentialService($frameworkContext, $connectionConfiguration)->updateScopesForCompany(self::positiveId($connectionId), self::positiveId($companyId), $scopes)];
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function renameConnection(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId, $name, $csrf): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId, $name, $csrf): array {
            self::assertAdministrativeMutationRequest();
            self::createCredentialService($frameworkContext, $connectionConfiguration)->renameForCompany(self::positiveId($connectionId), self::positiveId($companyId), is_string($name) ? $name : '');
            return [];
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function updateApiKeyResponsible(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId, $responsibleUserId): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId, $responsibleUserId): array {
            self::assertAdministrativeMutationRequest();
            $responsible = ($responsibleUserId === null || $responsibleUserId === '') ? null : self::positiveId($responsibleUserId);
            self::createCredentialService($frameworkContext, $connectionConfiguration)->updateApiKeyResponsibleForCompany(self::positiveId($connectionId), self::positiveId($companyId), $responsible);
            return [];
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function createConnection(array $frameworkContext, array $connectionConfiguration, $companyId, $name): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $name): array {
            self::assertAdministrativeMutationRequest();
            $company = self::positiveId($companyId);
            return ['connection' => self::createManagementService($frameworkContext, $connectionConfiguration)->create($company, (string) $name)];
        });
    }
    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function ensureConnectionPublicId(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId): array {
            self::assertAdministrativeMutationRequest();
            return ["public_id" => self::createManagementService($frameworkContext, $connectionConfiguration)->ensurePublicId(self::positiveId($companyId), self::positiveId($connectionId))];
        });
    }


    public static function changeConnectionStatus(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId, $status): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId, $status): array {
            self::assertAdministrativeMutationRequest();
            self::createManagementService($frameworkContext, $connectionConfiguration)->setStatus(self::positiveId($companyId), self::positiveId($connectionId), (string) $status);
            return [];
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function deleteUnusedConnection(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId): void
    {
        self::mutationResponse(static function () use ($frameworkContext, $connectionConfiguration, $companyId, $connectionId): array {
            self::assertAdministrativeMutationRequest();
            self::createManagementService($frameworkContext, $connectionConfiguration)->deleteUnused(self::positiveId($companyId), self::positiveId($connectionId));
            return [];
        });
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function render(array $frameworkContext, array $connectionConfiguration): void
    {
        try {
            echo self::createController($frameworkContext, $connectionConfiguration)->renderShell();
        } catch (AdminAuthorizationException $exception) {
            http_response_code(403);
            echo '<div class="mcp-admin-error">Acesso não autorizado.</div>';
        } catch (Throwable $exception) {
            error_log('Hardness MCP administration failure.');
            echo '<div class="mcp-admin-error">Não foi possível carregar a administração MCP.</div>';
        }
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function renderStandalone(array $frameworkContext, array $connectionConfiguration): void
    {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store');
        try {
            echo self::createController($frameworkContext, $connectionConfiguration)->renderStandalonePage((new CsrfGuard())->token());
        } catch (AdminAuthorizationException $exception) {
            http_response_code(403);
            echo self::standaloneErrorPage('Acesso não autorizado.');
        } catch (Throwable $exception) {
            error_log('Hardness MCP administration failure.');
            http_response_code(503);
            echo self::standaloneErrorPage('Não foi possível carregar a administração MCP.');
        }
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function validateCompany(array $frameworkContext, array $connectionConfiguration, $companyId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        try {
            $controller = self::createController($frameworkContext, $connectionConfiguration);
            if (filter_var($companyId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                http_response_code(400);
                echo json_encode(['error' => 'Empresa inválida.']);
                return;
            }
            $company = $controller->validateSelectedCompany((int) $companyId);
            echo json_encode(['company' => $company], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (AdminAuthorizationException $exception) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso não autorizado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (PDOException $exception) {
            error_log('Hardness MCP administration query failed.');
            http_response_code(503);
            echo json_encode(['error' => 'Não foi possível carregar a administração MCP.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $exception) {
            error_log('Hardness MCP administration company validation failed.');
            http_response_code(404);
            echo json_encode(['error' => 'A empresa não está disponível para administração.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function companyDetail(array $frameworkContext, array $connectionConfiguration, $companyId): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        try {
            $controller = self::createController($frameworkContext, $connectionConfiguration);
            if (filter_var($companyId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                http_response_code(400);
                echo json_encode(['error' => 'Empresa inválida.']);
                return;
            }
            $detail = $controller->companyDetail((int) $companyId);
            echo json_encode([
                'ok' => true,
                'company' => $detail['company'],
                'connections' => $detail['connections'],
                'credentials' => $detail['credentials'],
                'scope_catalog' => $detail['scope_catalog'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (AdminAuthorizationException $exception) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso não autorizado.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (PDOException $exception) {
            error_log('Hardness MCP administration query failed.');
            http_response_code(503);
            echo json_encode(['error' => 'Não foi possível carregar a administração MCP.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (RuntimeException $exception) {
            http_response_code(404);
            echo json_encode(['error' => 'A empresa não está disponível para administração.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $exception) {
            error_log('Hardness MCP administration company detail failed.');
            http_response_code(503);
            echo json_encode(['error' => 'Não foi possível carregar a administração MCP.'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    /** @param array<string,mixed> $frameworkContext @param array<string,mixed> $connectionConfiguration */
    public static function connectionDetail(array $frameworkContext, array $connectionConfiguration, $companyId, $connectionId, $auditOffset = 0): void
    {
        header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
        try { $detail = self::createController($frameworkContext, $connectionConfiguration)->connectionDetail(self::positiveId($companyId), self::positiveId($connectionId), max(0, (int) $auditOffset)); echo json_encode(['ok' => true] + $detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); }
        catch (AdminAuthorizationException $exception) { http_response_code(403); echo json_encode(['error' => 'Acesso não autorizado.']); }
        catch (RuntimeException $exception) { http_response_code(404); echo json_encode(['error' => 'Conexão não disponível.']); }
        catch (Throwable $exception) { error_log('Hardness MCP administration connection detail failed.'); http_response_code(503); echo json_encode(['error' => 'Não foi possível carregar a conexão.']); }
    }

    private static function standaloneErrorPage(string $message): string
    {
        return '<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Conexões MCP</title><link rel="stylesheet" href="/hardness3/mcp/admin/assets/mcp-admin.css?v=6"></head>'
            . '<body class="mcp-admin-standalone"><main class="mcp-admin-error">' . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</main></body></html>';
    }

    private static function positiveId($value): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) { throw new RuntimeException('Invalid administrative request.'); }
        return (int) $value;
    }

    /** @param array<string,mixed> $connectionConfiguration */
    private static function publicMcpUrl(array $connectionConfiguration): string
    {
        $base = trim((string) ($connectionConfiguration['urlRaiz'] ?? ''));
        $parts = parse_url($base);
        if (!is_array($parts) || ($parts['scheme'] ?? null) !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) { return ''; }
        $authority = strtolower((string) $parts['host']) . (isset($parts['port']) && (int) $parts['port'] !== 443 ? ':' . (int) $parts['port'] : '');
        return 'https://' . $authority . '/mcp';
    }

    /** @param callable():array<string,mixed> $operation */
    private static function mutationResponse(callable $operation): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        try {
            echo json_encode(['ok' => true] + $operation(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (AdminAuthorizationException $exception) {
            http_response_code(403); echo json_encode(['error' => 'Acesso não autorizado.']);
        } catch (RuntimeException $exception) {
            http_response_code(400); echo json_encode(['error' => self::safeMutationErrorMessage($exception)]);
        } catch (Throwable $exception) {
            error_log('Hardness MCP administration mutation failed.');
            http_response_code(503); echo json_encode(['error' => 'Não foi possível concluir a operação.']);
        }
    }

    private static function safeMutationErrorMessage(RuntimeException $exception): string
    {
        $messages = [
            'A responsible user is required for API key permissions.' => 'Para usar permissões de escrita com API Key, selecione um usuário responsável na aba API Key.',
            'Connection must be active to issue credentials.' => 'Ative a conexão antes de gerar uma credencial de API Key.',
            'Credential already exists.' => 'Esta conexão já possui uma credencial de API Key ativa.',
            'Maximum active credentials reached.' => 'Esta conexão já atingiu o limite de credenciais de API Key ativas.',
            'Unknown MCP scope.' => 'Uma das permissões selecionadas não está disponível.',
            'Invalid MCP scopes.' => 'As permissões informadas são inválidas.',
            'Connection not found.' => 'A conexão selecionada não foi encontrada para esta empresa.',
            'Only a connection that has never been used can be deleted.' => 'Esta conexão não pode ser excluída porque já possui credencial, autorização OAuth ou uso registrado. Para interromper o acesso, desative ou revogue a conexão.',
            'Responsible user not found.' => 'O usuário responsável precisa estar ativo e vinculado à empresa.',
        ];
        return $messages[$exception->getMessage()] ?? 'Não foi possível concluir a operação. Tente novamente.';
    }

    private static function assertAdministrativeMutationRequest(): void
    {
        (new AdministrativeRequestGuard())->assertMutationRequest();
    }
}
