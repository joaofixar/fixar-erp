<?php

declare(strict_types=1);

use HardnessWebhooks\Admin\Authorization\ErpWebhookAdminAuthorization;
use HardnessWebhooks\Admin\Authorization\WebhookAdminAuthorizationException;
use HardnessWebhooks\Admin\Context\ErpWebhookAdminContextFactory;
use HardnessWebhooks\Admin\FrameworkEntry;
use HardnessWebhooks\Admin\Security\AdministrativeRequestException;
use HardnessWebhooks\Admin\Security\AdministrativeRequestGuard;
use HardnessWebhooks\Admin\Security\CsrfGuard;

require_once dirname(__DIR__) . '/autoload.php';

function task16Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

/** @return array<string,mixed> */
function task16Context(): array
{
    return array(
        'usuarioAtual' => 91,
        'perfilUsuario' => 12,
        'empresaAtual' => 45,
        'nivelAlcada' => 2,
        'c029Ids' => array(ErpWebhookAdminAuthorization::C029_ID => true),
        'C007' => array('C007_Id' => 91),
        'C021' => array('C021_Id' => 12),
        'C004' => array('C004_Id' => 45),
    );
}

/** @param callable():void $operation */
function task16ExpectRequestFailure(callable $operation, string $message): void
{
    try {
        $operation();
        task16Assert(false, $message);
    } catch (AdministrativeRequestException $exception) {
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_save_path(sys_get_temp_dir());
    session_start();
}
$_SESSION = array();

$authorization = new ErpWebhookAdminAuthorization();
$factory = new ErpWebhookAdminContextFactory($authorization);
$authorized = task16Context();
$context = $factory->create($authorized);
task16Assert($context->getUserId() === 91 && $context->getProfileId() === 12 && $context->getCompanyId() === 45, 'valid ERP context creates immutable administrative context');
task16Assert($authorization->isAuthorized($authorized), 'C029 307 authorizes administration');
task16Assert($authorization->isAuthorized(task16Context()), 'access level below 4 does not block C029 307');

foreach (array('usuarioAtual', 'perfilUsuario', 'empresaAtual', 'c029Ids') as $field) {
    $invalid = task16Context();
    unset($invalid[$field]);
    try {
        $factory->create($invalid);
        task16Assert(false, 'missing ' . $field . ' fails closed');
    } catch (WebhookAdminAuthorizationException $exception) {
    }
}
$invalidCompany = task16Context();
$invalidCompany['empresaAtual'] = 0;
try { $factory->create($invalidCompany); task16Assert(false, 'invalid company fails closed'); } catch (WebhookAdminAuthorizationException $exception) {}

$forgedRequest = array('C004_Id' => 999, 'empresaAtual' => 999);
task16Assert($context->getCompanyId() === 45 && $forgedRequest['C004_Id'] === 999, 'browser company input cannot replace ERP company context');

$guard = new AdministrativeRequestGuard();
$guard->assertReadRequest('GET', array());
task16ExpectRequestFailure(static function () use ($guard): void { $guard->assertReadRequest('GET', array('action' => 'create')); }, 'GET mutation is rejected');
task16ExpectRequestFailure(static function () use ($guard): void { $guard->assertReadRequest('DELETE', array()); }, 'unsupported method is rejected');
task16ExpectRequestFailure(static function () use ($guard): void { $guard->assertMutationRequest('POST', array()); }, 'POST without CSRF is rejected');
task16ExpectRequestFailure(static function () use ($guard): void { $guard->assertMutationRequest('POST', array('csrf' => 'invalid')); }, 'POST with invalid CSRF is rejected');
$csrf = (new CsrfGuard())->token();
$guard->assertMutationRequest('POST', array('csrf' => $csrf));

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = array();
ob_start();
FrameworkEntry::read($authorized);
$readResponse = (string) ob_get_clean();
task16Assert(strpos($readResponse, 'boundary_ready') !== false, 'GET read passes the boundary');

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('csrf' => $csrf);
ob_start();
FrameworkEntry::mutation($authorized);
$postResponse = (string) ob_get_clean();
task16Assert(strpos($postResponse, 'boundary_ready') !== false, 'valid CSRF POST passes the boundary');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = array('action' => 'create');
ob_start();
FrameworkEntry::read($authorized);
$invalidResponse = (string) ob_get_clean();
task16Assert(strpos($invalidResponse, 'Requisição administrativa inválida.') !== false, 'GET mutation response is opaque');

$denied = task16Context();
unset($denied['c029Ids']);
$_GET = array();
ob_start();
FrameworkEntry::read($denied);
$deniedResponse = (string) ob_get_clean();
task16Assert(strpos($deniedResponse, 'Acesso não autorizado.') !== false, 'denied response is safe');
foreach (array('WEBHOOKS_MASTER_KEY', 'csrf', 'stack trace', '/root/', 'SQL') as $sensitive) {
    task16Assert(stripos($deniedResponse, $sensitive) === false, 'error response does not leak ' . $sensitive);
}

echo "Task 16 administrative boundary tests passed.\n";


/* ERP authorization regression coverage. */

function task15Assert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Assertion failed: ' . $message);
    }
}

/** @return array<string,mixed> */
function task15AuthorizedContext(): array
{
    return array(
        'usuarioAtual' => 91,
        'perfilUsuario' => 12,
        'empresaAtual' => 45,
        'nivelAlcada' => 4,
        'c029Ids' => array(ErpWebhookAdminAuthorization::C029_ID => true),
        'C007' => array('C007_Id' => 91),
        'C021' => array('C021_Id' => 12),
        'C004' => array('C004_Id' => 45),
    );
}

$authorization = new ErpWebhookAdminAuthorization();
$authorized = task15AuthorizedContext();
task15Assert($authorization->isAuthorized($authorized), 'complete ERP context with C029 307 is authorized');
$authorization->assertAuthorized($authorized);

$nonDirector = task15AuthorizedContext();
$nonDirector['nivelAlcada'] = 2;
task15Assert($authorization->isAuthorized($nonDirector), 'access level does not restrict the Webhooks permission');

$cases = array(
    'missing permission' => static function (array $context): array { unset($context['c029Ids'][ErpWebhookAdminAuthorization::C029_ID]); return $context; },
    'missing company' => static function (array $context): array { $context['empresaAtual'] = 0; return $context; },
    'invalid resolved company' => static function (array $context): array { $context['C004']['C004_Id'] = 46; return $context; },
    'missing user' => static function (array $context): array { unset($context['usuarioAtual']); return $context; },
    'invalid resolved user' => static function (array $context): array { $context['C007']['C007_Id'] = 92; return $context; },
    'missing profile' => static function (array $context): array { unset($context['perfilUsuario']); return $context; },
    'permissions not loaded' => static function (array $context): array { unset($context['c029Ids']); return $context; },
    'incomplete context' => static function (array $context): array { unset($context['C021']); return $context; },
);

foreach ($cases as $name => $mutate) {
    $context = $mutate(task15AuthorizedContext());
    task15Assert(!$authorization->isAuthorized($context), $name . ' fails closed');
    try {
        $authorization->assertAuthorized($context);
        task15Assert(false, $name . ' throws');
    } catch (WebhookAdminAuthorizationException $exception) {
    }
}

// The class has no request input: a forged company parameter cannot replace empresaAtual.
$request = array('C004_Id' => 999, 'empresaAtual' => 999);
task15Assert($authorization->isAuthorized($authorized), 'request values do not alter the trusted ERP context');
task15Assert($authorized['empresaAtual'] === 45 && $request['C004_Id'] === 999, 'company authority remains ERP-derived');

$updater = file_get_contents(dirname(__DIR__, 2) . '/scripts/atualizacaoBase.php');
task15Assert($updater !== false && strpos($updater, "values (307,'Administrar Webhooks','Integrações','M','0');") !== false, 'official C029 registry reserves stable Webhooks ID 307');
task15Assert(strpos((string) $updater, 'MAX(C029_Id)') === false, 'updater does not allocate the Webhooks ID dynamically');
task15Assert(strpos((string) $updater, 'C030_C029_Id = 307') === false, 'updater does not create a C030 grant for Webhooks');

echo "Task 15 authorization tests passed.\n";

