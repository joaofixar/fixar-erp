<?php
declare(strict_types=1);
namespace Hardness\Observability\Auth;
use Hardness\Observability\Legacy\LegacyCredentialValidator;
use Hardness\Observability\Security\TrustedHostResolver;

final class AuthenticationService
{
    private $master; private $hosts; private $credentials; private $access;
    public function __construct(MasterPasswordProvider $master, TrustedHostResolver $hosts, LegacyCredentialValidator $credentials, ClientAccessPolicyLoader $access) { $this->master=$master; $this->hosts=$hosts; $this->credentials=$credentials; $this->access=$access; }
    public function authenticate(string $login, string $password, array $server): ?array
    {
        $login = strtolower(trim($login));
        if ($login === 'root') {
            try { $masterHash = $this->master->hash(); } catch (\Throwable $exception) { return null; }
            if (!hash_equals($masterHash, md5($password))) { return null; }
            return array('authentication_method'=>'master_password','role'=>'hardness_internal','scope_label'=>'Servidor atual','host'=>null,'tenant'=>null,'c007_id'=>null,'capabilities'=>array('view'=>true,'full_sql'=>true,'connection_identity'=>true));
        }
        if ($login === '' || !preg_match('/^[a-z0-9._-]+$/', $login)) { return null; }
        $resolved = $this->hosts->resolve($server); $user = $this->credentials->authenticate($resolved['tenant'], $login, $password);
        if ($user === null) { return null; }
        $capabilities = $this->access->load()->capabilitiesFor($resolved['tenant_key'], $user['id'], false);
        if ($capabilities === null) { return null; }
        return array('authentication_method'=>'user_password','role'=>'client','scope_label'=>(string) $resolved['tenant']['tenant_label'],'request_host'=>$resolved['host'],'tenant_key'=>$resolved['tenant_key'],'c007_id'=>$user['id'],'capabilities'=>$capabilities);
    }
}
