<?php
declare(strict_types=1);

namespace Hardness\Observability;

use Hardness\Observability\Auth\AuthenticationService;
use Hardness\Observability\Auth\ClientAccessPolicyLoader;
use Hardness\Observability\Config\SiteTenantProvider;
use Hardness\Observability\Auth\MasterPasswordProvider;
use Hardness\Observability\Context\MockObservationContextProvider;
use Hardness\Observability\Http\AuthController;
use Hardness\Observability\Legacy\C007CredentialValidator;
use Hardness\Observability\Http\PrototypeController;
use Hardness\Observability\Http\Request;
use Hardness\Observability\Http\Response;
use Hardness\Observability\Http\Router;
use Hardness\Observability\Mock\MockObservationDataProvider;
use Hardness\Observability\Collectors\RealObservationDataProvider;
use Hardness\Observability\Security\SecurityHeaders;
use Hardness\Observability\Security\ObservationSession;
use Hardness\Observability\Security\TrustedHostResolver;
use Hardness\Observability\Security\LoginRateLimiter;
use Hardness\Observability\Security\SecurityLogger;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderHistoryService;
use Hardness\Observability\Collectors\Mysql\MysqlRecorderScopeResolver;

final class Application
{
    private $router;

    private function __construct(Router $router)
    {
        $this->router = $router;
    }

    public static function createPrototype(): self
    {
        $contexts = new MockObservationContextProvider();
        $data = new MockObservationDataProvider($contexts);
        $controller = new PrototypeController($contexts, $data);
        $router = new Router($controller);

        return new self($router);
    }

    public static function createProduction(): self
    {
        $tenants=new SiteTenantProvider(); $hosts=new TrustedHostResolver($tenants); $access=new ClientAccessPolicyLoader(); $session=new ObservationSession($hosts,$access); $data=new RealObservationDataProvider($session,$tenants);
        $auth=new AuthenticationService(new MasterPasswordProvider(),$hosts,new C007CredentialValidator(),$access);
        $controller=new PrototypeController($session,$data,null,new MysqlRecorderHistoryService(),new MysqlRecorderScopeResolver($session,$tenants)); $authController=new AuthController($auth,$session,new LoginRateLimiter(),new SecurityLogger());
        return new self(new Router($controller,$authController,$session));
    }

    public function run(): void
    {
        SecurityHeaders::apply();

        try {
            $response = $this->router->dispatch(Request::fromGlobals());
        } catch (\Throwable $exception) {
            $response = Response::json(array(
                'error' => 'O protótipo não pôde concluir esta solicitação.',
            ), 500);
        }

        $response->send();
    }
}
