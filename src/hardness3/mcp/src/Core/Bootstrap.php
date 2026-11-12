<?php

declare(strict_types=1);

namespace HardnessMcp\Core;

use HardnessMcp\Auth\Authenticator;
use HardnessMcp\Auth\OAuthAccessTokenAuthenticator;
use HardnessMcp\Audit\McpAuditService;
use HardnessMcp\Context\ExecutionContext;
use HardnessMcp\Database\ConnectionFactory;
use HardnessMcp\Endpoint\McpEndpointResolver;
use HardnessMcp\OAuth\OAuthDiscoveryMetadata;
use HardnessMcp\OAuth\DynamicClientRegistrationService;
use HardnessMcp\OAuth\DcrValidationException;
use HardnessMcp\OAuth\DcrDiagnostic;
use HardnessMcp\OAuth\OAuthAuthorizationRequestService;
use HardnessMcp\OAuth\OAuthTokenEndpointService;
use HardnessMcp\OAuth\OAuthTokenEndpointException;
use HardnessMcp\Protocol\JsonRpcServer;
use HardnessMcp\RateLimit\ApcuRateLimiter;
use HardnessMcp\RateLimit\RateLimitDecision;
use HardnessMcp\RateLimit\RateLimiterInterface;
use HardnessMcp\RateLimit\RateLimitPolicy;
use HardnessMcp\Scopes\ScopeCatalog;
use HardnessMcp\Tenant\TenantResolver;
use HardnessMcp\Tenant\ResolvedTenantContext;
use HardnessMcp\Tools\Catalog\CompanyToolsModule;
use HardnessMcp\Tools\Catalog\ProductToolsModule;
use HardnessMcp\Tools\Catalog\QuoteToolsModule;
use HardnessMcp\Tools\Catalog\SalesToolsModule;
use HardnessMcp\Tools\Catalog\FinanceToolsModule;
use HardnessMcp\Tools\Catalog\OrderToolsModule;
use HardnessMcp\Tools\Catalog\ItemReportToolsModule;
use HardnessMcp\Tools\Catalog\CommercialToolsModule;
use HardnessMcp\Tools\Catalog\SystemToolsModule;
use HardnessMcp\Tools\Catalog\IdeSmtToolsModule;
use HardnessMcp\Schema\ProposalCustomerCapability;
use HardnessMcp\Schema\IdeSmtOrderGenerationCapability;
use HardnessMcp\Tools\Quote\Create\LegacyQuoteCreateGateway;
use HardnessMcp\Tools\Quote\Create\UnavailableQuoteCreateGateway;
use HardnessMcp\Tools\Quote\Pdf\LegacyQuotePdfGateway;
use HardnessMcp\Tools\Quote\Pdf\UnavailableQuotePdfGateway;
use HardnessMcp\Tools\Quote\Item\LegacyQuoteItemGateway;
use HardnessMcp\Tools\Quote\Item\UnavailableQuoteItemGateway;
use HardnessMcp\Tools\Quote\Summary\Infrastructure\PdoQuoteSummaryRepository;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\LegacyIdeSmtGenerateOrderGateway;
use HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination\UnavailableIdeSmtGenerateOrderGateway;
use HardnessMcp\Legacy\LegacyExecutionContextAdapter;
use HardnessMcp\Legacy\McpFrameworkBridge;
use HardnessMcp\Legacy\PdoLegacyActorResolver;
use HardnessMcp\Tools\Catalog\ToolCatalogConsistencyValidator;
use HardnessMcp\Tools\Catalog\ToolCatalogFactory;
use HardnessMcp\Tools\ToolExecutor;
use HardnessMcp\Tools\ToolRegistry;
use Throwable;

class Bootstrap
{
    private string $rootPath;
    private ?string $oauthCanonicalResource = null;
    private ?ResolvedTenantContext $resolvedTenantContext = null;
    private ?ExecutionContext $resolvedExecutionContext = null;
    private ?\PDO $readPdo = null;

    public function __construct(string $rootPath)
    {
        $this->rootPath = rtrim($rootPath, '/');
    }

    public function run(): void
    {
        $config = (new Config($this->rootPath . '/config'))->load('app');
        $this->handle(Request::capture(), $config)->send();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function handle(Request $request, array $config): Response
    {
        $startedAt = microtime(true);
        $correlationId = bin2hex(random_bytes(16));
        OperationalTrace::begin($correlationId);
        $outcome = 'mcp_request'; $safeCode = null;
        try {
            if ($this->isDiscoveryEndpoint($request)) {
                return $this->traceHttpResponse($request, $config, $startedAt, $this->handleDiscovery($request, $config), 'oauth_discovery');
            }
            if ($request->getPath() === '/oauth/register') {
                return $this->traceHttpResponse($request, $config, $startedAt, $this->handleDcr($request, $config), 'oauth_dcr');
            }
            if ($request->getPath() === '/oauth/authorize') {
                return $this->traceHttpResponse($request, $config, $startedAt, $this->handleAuthorizeStart($request, $config), 'oauth_authorize');
            }
            if ($request->getPath() === '/oauth/token') {
                return $this->traceHttpResponse($request, $config, $startedAt, $this->handleToken($request, $config), 'oauth_token');
            }
            $this->assertEndpointPath($request, $config);
            if (!$this->isOriginAllowed($request, $config)) {
                return $this->traceHttpResponse($request, $config, $startedAt, Response::empty(403), 'mcp_origin_rejected', 'origin_rejected');
            }

            if ($request->getMethod() !== 'POST') {
                return $this->traceHttpResponse($request, $config, $startedAt, Response::empty(405, ['Allow' => 'POST']), 'mcp_method_rejected', 'method_not_allowed');
            }

            // TenantResolver canonicalizes case and strips a valid port before rate-limit keys are formed.
            $tenant = $this->resolveTenant($config, $request->getHost());
            $resolvedTenantContext = ResolvedTenantContext::fromResolvedTenant($tenant);
            // Validate the configured APCu driver before opening the tenant connection.
            $rateLimit = $this->createRateLimitPolicy($config);
            $pdo = $this->connectTenant($config, $tenant);
            $connectionSpecificEndpoint = (new McpEndpointResolver($pdo))->resolve($request->getPath());
            $readPdo = $pdo;
            if ($this->hasReadTenant($tenant)) {
                try {
                    $readPdo = $this->connectReadTenant($config, $tenant);
                } catch (McpException $exception) {
                    // Reads remain available on the principal when a replica
                    // is temporarily unavailable. No tenant credentials or
                    // database details are emitted.
                    error_log(json_encode([
                        'event' => 'hardness_mcp_read_replica_unavailable',
                        'host' => $tenant['host'],
                    ], JSON_UNESCAPED_SLASHES));
                }
            }
            $requestId = bin2hex(random_bytes(16));
            OperationalTrace::reach('pre_rate_limit');
            $preAuthenticationDecision = $rateLimit->preAuthentication($tenant['host'], $request->getClientIp());
            if (!$preAuthenticationDecision->isAllowed()) {
                $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'rate_limited_preauthentication', 429, (int) ((microtime(true) - $startedAt) * 1000), null);
                $this->throwRateLimit($preAuthenticationDecision);
            }
            $this->oauthCanonicalResource = (new OAuthDiscoveryMetadata())->canonicalBaseUrl($tenant) . $request->getPath();
            $context = $this->authenticate($config, $pdo, $request);
            if ($context === null) {
                $outcome='authentication_required'; $safeCode='missing_or_invalid_bearer';
                $authenticationFailureDecision = $rateLimit->authenticationFailure($tenant['host'], $this->authorizationPublicPart($request));
                if (!$authenticationFailureDecision->isAllowed()) {
                    $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'rate_limited_authentication', 429, (int) ((microtime(true) - $startedAt) * 1000), null);
                    $this->throwRateLimit($authenticationFailureDecision);
                }
                $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'authentication_failed', 401, (int) ((microtime(true) - $startedAt) * 1000), null);
                $metadataUrl = (new OAuthDiscoveryMetadata())->protectedResourceMetadataUrl($tenant, $request->getPath());
                throw new HttpException(401, ['WWW-Authenticate' => 'Bearer resource_metadata="' . $metadataUrl . '"']);
            }
            $context = $context->withTrustedRemoteIp($request->getClientIp());
            if ($connectionSpecificEndpoint !== null && $context->getConnectionId() !== $connectionSpecificEndpoint) {
                $outcome='authentication_failed'; $safeCode='connection_credential_mismatch';
                $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'authentication_failed', 401, (int) ((microtime(true) - $startedAt) * 1000), null, $safeCode);
                $metadataUrl = (new OAuthDiscoveryMetadata())->protectedResourceMetadataUrl($tenant, $request->getPath());
                throw new HttpException(401, ['WWW-Authenticate' => 'Bearer resource_metadata="' . $metadataUrl . '"']);
            }
            $this->assertAccept($request);
            $this->assertJsonContentType($request);
            $this->assertPayloadLimit($request, $config);
            OperationalTrace::reach('post_auth_rate_limit');
            $connectionDecision = $rateLimit->connection($tenant['host'], $context->getConnectionId());
            if (!$connectionDecision->isAllowed()) {
                $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'rate_limited_connection', 429, (int) ((microtime(true) - $startedAt) * 1000), $context);
                $this->throwRateLimit($connectionDecision);
            }
            if ($context->getAuthMethod() === 'oauth') {
                $oauthDecision = $rateLimit->oauthAccessToken($tenant['host'], $context->getConnectionId(), (int) $context->getOAuthGrantId(), (int) $context->getOAuthAccessTokenId());
                if (!$oauthDecision->isAllowed()) {
                    $this->auditRequest($pdo, $requestId, $tenant['host'], $request->getClientIp(), 'rate_limited_oauth_access_token', 429, (int) ((microtime(true) - $startedAt) * 1000), $context);
                    $this->throwRateLimit($oauthDecision);
                }
            }
            OperationalTrace::reach('jsonrpc_dispatch');
            $this->resolvedTenantContext = $resolvedTenantContext;
            $this->resolvedExecutionContext = $context;
            $this->readPdo = $readPdo;
            try {
                $jsonRpcServer = $this->createJsonRpcServer($config, $pdo);
            } finally {
                $this->resolvedTenantContext = null;
                $this->resolvedExecutionContext = null;
                $this->readPdo = null;
            }
            $result = $jsonRpcServer->handle($request->getBody(), $request->getHeader('mcp-protocol-version'), $context);

            OperationalTrace::reach('audit_start');
            $this->auditRequest(
                $pdo,
                $requestId,
                $tenant['host'],
                $request->getClientIp(),
                $result->getAuditEvent(),
                $result->getHttpStatus(),
                (int) ((microtime(true) - $startedAt) * 1000),
                $context,
                $result->getAuditSafeCode()
            );

            $response = $result->getPayload() === null
                ? Response::empty($result->getHttpStatus())
                : Response::json($result->getPayload(), $result->getHttpStatus());
            return $this->traceHttpResponse($request, $config, $startedAt, $response, $result->getAuditEvent(), $result->getAuditSafeCode());
        } catch (HttpException $exception) {
            return $this->traceHttpResponse($request, $config, $startedAt, Response::empty($exception->getHttpStatus(), $exception->getHeaders()), $outcome === 'mcp_request' ? 'http_exception' : $outcome, $safeCode ?? ('http_' . $exception->getHttpStatus()));
        } catch (McpException $exception) {
            return $this->traceHttpResponse($request, $config, $startedAt, Response::json($this->error($exception->getJsonRpcCode(), $exception->getMessage()), $exception->getHttpStatus()), 'mcp_exception', 'jsonrpc_' . $exception->getJsonRpcCode());
        } catch (Throwable $exception) {
            error_log(json_encode([
                'event' => 'hardness_mcp_operational_failure',
                'correlation_id' => OperationalTrace::correlationId(),
                'exception_class' => get_class($exception),
                'message' => $this->sanitizeOperationalMessage($exception->getMessage()),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
                'stage' => OperationalTrace::stage(),
            ], JSON_UNESCAPED_SLASHES));
            return $this->traceHttpResponse($request, $config, $startedAt, Response::empty(503), 'internal_failure', 'internal_failure');
        }
    }

    /** @param array<string,mixed> $config */
    private function traceHttpResponse(Request $request, array $config, float $startedAt, Response $response, string $outcome, ?string $safeCode = null): Response
    {
        HttpDiagnostic::record((bool) (($config['oauth']['http_diagnostics_enabled'] ?? false)), $request, $response, $startedAt, $outcome, $safeCode);
        return $response;
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, string>
     */
    protected function resolveTenant(array $config, string $host): array
    {
        return (new TenantResolver($config))->resolve($host);
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, string> $tenant
     */
    protected function connectTenant(array $config, array $tenant): \PDO
    {
        return (new ConnectionFactory($config))->create($tenant);
    }

    /** @param array<string, mixed> $config @param array<string, string> $tenant */
    protected function connectReadTenant(array $config, array $tenant): \PDO
    {
        return (new ConnectionFactory($config))->create([
            'dbDatabase' => $tenant['readDbDatabase'],
            'dbHost' => $tenant['readDbHost'],
            'dbUser' => $tenant['readDbUser'],
            'dbPass' => $tenant['readDbPass'],
        ]);
    }

    /** @param array<string, string> $tenant */
    private function hasReadTenant(array $tenant): bool
    {
        foreach (['readDbDatabase', 'readDbHost', 'readDbUser', 'readDbPass'] as $field) {
            if (!isset($tenant[$field]) || $tenant[$field] === '') {
                return false;
            }
        }
        return true;
    }

    /** @param array<string, mixed> $config */
    protected function authenticate(array $config, \PDO $pdo, Request $request): ?ExecutionContext
    {
        OperationalTrace::reach('credential_classification');
        $authorization = $request->getHeader('authorization');
        if ($authorization === null || preg_match('/\ABearer[ ]+([^\s]+)\z/', $authorization, $matches) !== 1) return null;
        $credential = $matches[1];
        if (strpos($credential, 'hrd_mcp_v1_') === 0) return (new Authenticator($config))->authenticate($pdo, $authorization, $request->getClientIp());
        if (preg_match('/\A[A-Za-z0-9_-]{16}\.[A-Za-z0-9_-]{32,64}\z/', $credential) === 1) return (new OAuthAccessTokenAuthenticator($config))->authenticate($pdo, $credential, (string) $this->oauthCanonicalResource, $request->getClientIp());
        return null;
    }

    private function sanitizeOperationalMessage(string $message): string
    {
        $message = preg_replace('/Bearer\\s+[^\\s]+/i', 'Bearer [redacted]', $message) ?? '';
        $message = preg_replace('/[A-Za-z0-9_-]{16}\\.[A-Za-z0-9_-]{32,64}/', '[redacted-token]', $message) ?? '';
        return substr($message, 0, 300);
    }

    /** @param array<string, mixed> $config */
    protected function createRateLimitPolicy(array $config): RateLimitPolicy
    {
        if (($config['rate_limit']['driver'] ?? '') !== 'apcu') { throw new \RuntimeException('Configured rate limit driver is unavailable.'); }
        return new RateLimitPolicy($this->createRateLimiter(), $config);
    }

    protected function createRateLimiter(): RateLimiterInterface
    {
        return new ApcuRateLimiter();
    }

    protected function auditRequest(\PDO $pdo, string $requestId, string $host, string $ip, string $event, int $httpStatus, int $durationMs, ?ExecutionContext $context, ?string $safeCode = null): void
    {
        (new McpAuditService($pdo))->record($requestId, $host, $ip, $event, $httpStatus, $durationMs, $context, $safeCode);
    }

    /** @param array<string, mixed> $config */
    protected function createJsonRpcServer(array $config, \PDO $pdo): JsonRpcServer
    {
        $readPdo = $this->readPdo ?? $pdo;
        $proposalCustomerAvailable = ProposalCustomerCapability::isAvailable($readPdo);
        $ideSmtCompanyId = $this->resolvedExecutionContext !== null
            && IdeSmtOrderGenerationCapability::isEnabled($pdo, $this->resolvedExecutionContext->getCompanyId())
            ? $this->resolvedExecutionContext->getCompanyId()
            : null;
        $modules = [
            CompanyToolsModule::fromPdo($readPdo, (string)($config['partner']['candidate_ref_pepper'] ?? ($config['rate_limit']['key_pepper'] ?? '')), (int)($config['partner']['candidate_ref_ttl_seconds'] ?? 600)),
            ProductToolsModule::fromPdo($readPdo, $pdo, $this->resolvedTenantContext, dirname($this->rootPath)),
            new QuoteToolsModule(new \HardnessMcp\Tools\Quote\Infrastructure\PdoQuoteRepository($readPdo, $proposalCustomerAvailable), $this->resolvedTenantContext === null
                ? new UnavailableQuoteCreateGateway()
                : new LegacyQuoteCreateGateway(new LegacyExecutionContextAdapter(new PdoLegacyActorResolver($pdo), dirname($this->rootPath)), new McpFrameworkBridge(dirname($this->rootPath)), $this->resolvedTenantContext), $this->resolvedTenantContext === null
                ? new UnavailableQuotePdfGateway()
                : new LegacyQuotePdfGateway(new LegacyExecutionContextAdapter(new PdoLegacyActorResolver($pdo), dirname($this->rootPath)), new McpFrameworkBridge(dirname($this->rootPath)), $this->resolvedTenantContext), $this->resolvedTenantContext === null ? new UnavailableQuoteItemGateway() : new LegacyQuoteItemGateway(new LegacyExecutionContextAdapter(new PdoLegacyActorResolver($pdo), dirname($this->rootPath)),new McpFrameworkBridge(dirname($this->rootPath)),$this->resolvedTenantContext),new PdoQuoteSummaryRepository($readPdo, $proposalCustomerAvailable),new \HardnessMcp\Tools\Quote\Infrastructure\PdoQuoteRepository($pdo, $proposalCustomerAvailable)),
            SalesToolsModule::fromPdo($readPdo, $proposalCustomerAvailable),
            FinanceToolsModule::fromPdo($readPdo),
            OrderToolsModule::fromPdo($readPdo, $proposalCustomerAvailable),
            ItemReportToolsModule::fromPdo($readPdo),
            CommercialToolsModule::fromPdo($readPdo),
            new IdeSmtToolsModule($this->resolvedTenantContext === null
                ? new UnavailableIdeSmtGenerateOrderGateway()
                : new LegacyIdeSmtGenerateOrderGateway(new LegacyExecutionContextAdapter(new PdoLegacyActorResolver($pdo), dirname($this->rootPath)), new McpFrameworkBridge(dirname($this->rootPath)), $this->resolvedTenantContext), $ideSmtCompanyId),
        ];
        $modules[] = $this->resolvedTenantContext === null
            ? SystemToolsModule::unavailable()
            : SystemToolsModule::fromRuntime($pdo, $this->resolvedTenantContext, dirname($this->rootPath));
        $registry = (new ToolCatalogFactory($modules))->createRegistry();
        (new ToolCatalogConsistencyValidator())->assertConsistent($registry, new ScopeCatalog());

        return new JsonRpcServer($config, new ToolExecutor($registry, null, $this->resolvedTenantContext));
    }

    private function throwRateLimit(RateLimitDecision $decision): void
    {
        if (!$decision->isAllowed()) { throw new HttpException(429, ['Retry-After' => (string) $decision->getRetryAfter()]); }
    }

    private function authorizationPublicPart(Request $request): string
    {
        $authorization = (string) $request->getHeader('authorization');
        if (preg_match('/\\A\\S+\\s+hrd_mcp_v1_([A-Z2-7]{16})_/', $authorization, $matches) === 1) return 'api:' . $matches[1];
        if (preg_match('/\\A\\S+\\s+([A-Za-z0-9_-]{16})\\./', $authorization, $matches) === 1) return 'oauth:' . $matches[1];
        return 'invalid';
    }

    /** @param array<string,mixed> $config */
    private function handleDiscovery(Request $request, array $config): Response
    {
        if ($request->getPath() === '/.well-known/openid-configuration') {
            return Response::empty(404);
        }
        if ($request->getMethod() !== 'GET') {
            return Response::empty(405, ['Allow' => 'GET']);
        }
        $tenant = $this->resolveTenant($config, $request->getHost());
        $decision = $this->createRateLimitPolicy($config)->preAuthentication($tenant['host'], $request->getClientIp());
        if (!$decision->isAllowed()) {
            return Response::empty(429, ['Retry-After' => (string) $decision->getRetryAfter()]);
        }
        $metadata = new OAuthDiscoveryMetadata();
        $protectedResourcePath = $this->protectedResourcePath($request);
        if ($protectedResourcePath !== null) {
            if ($protectedResourcePath !== '/mcp') { (new McpEndpointResolver($this->connectTenant($config, $tenant)))->resolve($protectedResourcePath); }
            return Response::json($metadata->protectedResource($tenant, $protectedResourcePath));
        }
        return Response::json($metadata->authorizationServer($tenant));
    }

    private function isDiscoveryEndpoint(Request $request): bool
    {
        return $this->protectedResourcePath($request) !== null || in_array($request->getPath(), [
            '/.well-known/oauth-authorization-server',
            '/.well-known/openid-configuration',
        ], true);
    }

    private function protectedResourcePath(Request $request): ?string
    {
        $path = $request->getPath();
        if ($path === '/.well-known/oauth-protected-resource/mcp' || $path === '/.well-known/oauth-protected-resource') { return '/mcp'; }
        $prefix = '/.well-known/oauth-protected-resource/mcp/c/';
        if (strpos($path, $prefix) === 0) { return '/mcp/c/' . substr($path, strlen($prefix)); }
        return null;
    }

    /** @param array<string,mixed> $config */
    private function handleDcr(Request $request, array $config): Response
    {
        $contentType=(string)$request->getHeader('content-type');
        $startedAt=microtime(true); $payload=null; $diagnostics=(bool)(($config['oauth']['dcr_diagnostics_enabled']??false));
        $method=$request->getMethod();
        $finish=function (Response $response,string $outcome,?string $safeCode=null) use ($diagnostics,$startedAt,&$payload,$method): Response {
            $event = array_merge([
                'correlation_id'=>OperationalTrace::correlationId(), 'route'=>'/oauth/register', 'method'=>$method,
                'http_status'=>$response->getStatus(), 'outcome'=>$outcome, 'duration_ms'=>(int)((microtime(true)-$startedAt)*1000),
            ], $safeCode===null?[]:['safe_code'=>$safeCode], DcrDiagnostic::summarize($payload));
            if ($outcome === 'registered' && $response->getStatus() === 201) {
                $event['response'] = DcrDiagnostic::summarizeSuccessfulResponse($response->getPayload());
            }
            DcrDiagnostic::record($diagnostics, $event);
            return $response;
        };
        if ($request->getMethod() !== 'POST') return $finish(Response::empty(405, ['Allow' => 'POST']),'method_rejected','method_not_allowed');
        if (strlen($request->getBody()) > 16384) return $finish($this->dcrError(),'request_rejected','request_too_large');
        if (strtolower(trim(explode(';', $contentType, 2)[0])) !== 'application/json') return $finish($this->dcrError(),'request_rejected','invalid_content_type');
        try { $payload=json_decode($request->getBody(),true,32,JSON_THROW_ON_ERROR); if(!is_array($payload)||($payload===[]&&array_keys($payload)!==[])) throw new DcrValidationException('payload','invalid_payload_shape');
            $tenant=$this->resolveTenant($config,$request->getHost()); $limit=$this->createRateLimitPolicy($config)->dcrRegistration($tenant['host'],$request->getClientIp()); if(!$limit->isAllowed()) return $finish(Response::json(['error'=>'invalid_client_metadata'],429,['Retry-After'=>(string)$limit->getRetryAfter()]),'rate_limited','rate_limited');
            $result=(new DynamicClientRegistrationService($this->connectTenant($config,$tenant),$tenant['host'],$request->getClientIp()))->register($payload); return $finish(Response::json($result,201),'registered');
        } catch (DcrValidationException $exception) { return $finish($this->dcrError(),'validation_rejected',$exception->internalCode);
        } catch (\JsonException $exception) { return $finish($this->dcrError(),'request_rejected','invalid_json');
        } catch (Throwable $exception) { return $finish($this->dcrError(),'registration_failed','internal_failure'); }
    }
    private function dcrError(): Response { return Response::json(['error'=>'invalid_client_metadata'],400); }
    /** RFC 6749 form endpoint. JSON is deliberately rejected for this MVP. */
    private function handleToken(Request $request,array $config): Response
    {
        if($request->getMethod()!=='POST')return Response::empty(405,['Allow'=>'POST']);
        if(strtolower(trim(explode(';',(string)$request->getHeader('content-type'),2)[0]))!=='application/x-www-form-urlencoded')return $this->tokenError('invalid_request');
        try {$tenant=$this->resolveTenant($config,$request->getHost());$limit=$this->createRateLimitPolicy($config)->oauthToken($tenant['host'],$request->getClientIp());if(!$limit->isAllowed())return Response::json(['error'=>'temporarily_unavailable'],429,['Retry-After'=>(string)$limit->getRetryAfter(),'Pragma'=>'no-cache']);$input=$this->form($request->getBody());$base=(new OAuthDiscoveryMetadata())->canonicalBaseUrl($tenant);$legacyResource=$base.'/mcp';if(!array_key_exists('resource',$input))$input['resource']=$legacyResource;$pdo=$this->connectTenant($config,$tenant);if($input['resource']!==$legacyResource){$prefix=$legacyResource.'/c/';if(strpos($input['resource'],$prefix)!==0)return $this->tokenError('invalid_grant');try{(new McpEndpointResolver($pdo))->resolve(substr($input['resource'],strlen($base)));}catch(HttpException $e){return $this->tokenError('invalid_grant');}}$result=(new OAuthTokenEndpointService($pdo,$config,$legacyResource))->exchange($input);return Response::json($result,200,['Pragma'=>'no-cache']);}
        catch(OAuthTokenEndpointException $e){return $this->tokenError($e->oauthError,$e->getCode()===401?401:400);}
        catch(\Throwable $e){error_log('Hardness MCP OAuth token failure.');return $this->tokenError('temporarily_unavailable',503);}
    }
    private function tokenError(string $error,int $status=400): Response { return Response::json(['error'=>$error],$status,['Pragma'=>'no-cache']); }
    /** @return array<string,string> */ private function form(string $body): array {if(strlen($body)>16384)throw new OAuthTokenEndpointException('invalid_request');$out=[];foreach($body===''?[]:explode('&',$body) as $part){$pair=explode('=',$part,2);$key=urldecode($pair[0]);if($key===''||isset($out[$key]))throw new OAuthTokenEndpointException('invalid_request');$out[$key]=urldecode($pair[1]??'');}return $out;}
    /** Starts only the server-side transaction and Framework login bridge. */
    private function handleAuthorizeStart(Request $request,array $config): Response
    {
        if($request->getMethod()!=='GET')return Response::empty(405,['Allow'=>'GET']);
        if($request->hasAmbiguousQuery())return Response::json(['error'=>'invalid_request'],400);
        try{$tenant=$this->resolveTenant($config,$request->getHost());$limit=$this->createRateLimitPolicy($config)->preAuthentication($tenant['host'],$request->getClientIp());if(!$limit->isAllowed())return Response::json(['error'=>'invalid_request'],429,['Retry-After'=>(string)$limit->getRetryAfter()]);$metadata=new OAuthDiscoveryMetadata();$issuer=$metadata->canonicalBaseUrl($tenant);$pdo=$this->connectTenant($config,$tenant);$resource=(string)($request->getQuery('resource')??$issuer.'/mcp');$connectionId=null;if($resource!==$issuer.'/mcp'){$prefix=$issuer.'/mcp/c/';if(strpos($resource,$prefix)!==0)throw new \RuntimeException('invalid_request');$connectionId=(new McpEndpointResolver($pdo))->resolve(substr($resource,strlen($issuer)));}$result=(new OAuthAuthorizationRequestService($pdo,$config))->begin(['response_type'=>$request->getQuery('response_type'),'client_id'=>$request->getQuery('client_id'),'redirect_uri'=>$request->getQuery('redirect_uri'),'code_challenge'=>$request->getQuery('code_challenge'),'code_challenge_method'=>$request->getQuery('code_challenge_method'),'resource'=>$request->getQuery('resource'),'scope'=>$request->getQuery('scope')??'','state'=>$request->getQuery('state')],$issuer,$resource,$connectionId);
            $cookieName='__Host-hrd_oauth_'.$result['selector']; setcookie($cookieName,explode('.',$result['cookie'],2)[1],['expires'=>time()+300,'path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']);return Response::redirect('/hardness3/outros/login/index.php?mcp_oauth_tx='.rawurlencode($result['selector']));
        }catch(\Throwable $e){return Response::json(['error'=>'invalid_request'],400);}
    }

    /**
     * @param array<string, mixed> $config
     */
    private function assertEndpointPath(Request $request, array $config): void
    {
        $legacyPath = (string) ($config['endpoint_path'] ?? '/mcp');
        if ($request->getPath() !== $legacyPath && strpos($request->getPath(), $legacyPath . '/c/') !== 0) {
            throw new McpException('Endpoint not found.', -32601);
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function isOriginAllowed(Request $request, array $config): bool
    {
        $origin = $request->getHeader('origin');
        if ($origin === null) {
            return true;
        }

        $allowedOrigins = (array) (($config['transport']['allowed_origins'] ?? []));
        return in_array($this->normalizeOrigin($origin), $allowedOrigins, true);
    }

    private function normalizeOrigin(string $origin): string
    {
        $parts = parse_url($origin);
        if (
            $parts === false
            || !isset($parts['scheme'], $parts['host'])
            || !in_array(strtolower((string) $parts['scheme']), ['http', 'https'], true)
            || isset($parts['user'], $parts['pass'], $parts['path'], $parts['query'], $parts['fragment'])
        ) {
            return '';
        }

        $normalized = strtolower((string) $parts['scheme']) . '://' . strtolower((string) $parts['host']);
        if (isset($parts['port'])) {
            $normalized .= ':' . (int) $parts['port'];
        }

        return $normalized;
    }

    private function assertAccept(Request $request): void
    {
        $accept = $request->getHeader('accept');
        if ($accept === null || !$this->acceptsMediaType($accept, 'application/json') || !$this->acceptsMediaType($accept, 'text/event-stream')) {
            throw new McpException('Required Accept header is missing.', -32600);
        }
    }

    private function assertJsonContentType(Request $request): void
    {
        $contentType = $request->getHeader('content-type');
        if ($contentType === null) {
            throw new HttpException(415);
        }

        $mediaType = strtolower(trim(explode(';', $contentType, 2)[0]));
        if ($mediaType !== 'application/json') {
            throw new HttpException(415);
        }
    }

    private function acceptsMediaType(string $accept, string $required): bool
    {
        foreach (explode(',', $accept) as $entry) {
            $parts = explode(';', trim($entry));
            $mediaType = strtolower(trim($parts[0]));
            $quality = 1.0;
            foreach (array_slice($parts, 1) as $parameter) {
                if (preg_match('/^\s*q\s*=\s*([01](?:\.\d+)?)\s*$/i', $parameter, $matches) === 1) {
                    $quality = (float) $matches[1];
                }
            }
            if ($mediaType === $required && $quality > 0.0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function assertPayloadLimit(Request $request, array $config): void
    {
        $maxPayloadBytes = (int) (($config['limits']['max_payload_bytes'] ?? 0));
        if ($maxPayloadBytes > 0 && strlen($request->getBody()) > $maxPayloadBytes) {
            throw new McpException('Request payload is too large.', -32600);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function error(int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => $code, 'message' => $message]];
    }
}
