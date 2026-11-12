<?php

declare(strict_types=1);

namespace RestApi\Controller;

use RestApi\Core\ApiException;
use RestApi\Core\Request;
use RestApi\Core\Response;

class AuthController
{
    /**
     * @return array{status:int,payload:array<string,mixed>}
     */
    public function me(Request $request): array
    {
        $request->setAttribute('audit_operation', 'auth_me');
        $request->setAttribute('audit_affected_rows', 1);

        $tenantContext = $request->getAttribute('tenant');
        $authContext = $request->getAttribute('auth');

        if (!is_array($tenantContext) || !is_array($authContext)) {
            throw new ApiException('AUTH_CONTEXT_MISSING', 'Contexto de autenticação inválido.', 500);
        }

        return Response::success([
            'authenticated' => true,
            'token' => [
                'id' => $authContext['A001_Id'],
                'name' => $authContext['A001_Nome'],
                'prefix' => $authContext['A001_Token_Prefixo'],
                'status' => $authContext['A001_Status'],
            ],
            'tenant' => [
                'database' => $tenantContext['dbDatabase'],
                'host' => $tenantContext['host'],
            ],
        ], [
            'request_id' => (string) $request->getAttribute('request_id', ''),
        ]);
    }
}
