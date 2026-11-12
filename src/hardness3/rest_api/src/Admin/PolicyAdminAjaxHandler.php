<?php

declare(strict_types=1);

namespace RestApi\Admin;

class PolicyAdminAjaxHandler
{
    /**
     * @param object $policyAdmin
     * @param array<string, mixed> $request
     * @return array{success:bool,message:string,data:array<string,mixed>,status_code:int}
     */
    public function handle(string $action, $policyAdmin, array $request): array
    {
        try {
            $policyAdmin->assertAuthorized();

            if ($action === 'API001PolicyAccessStatus') {
                return $this->response(true, '', $policyAdmin->getRestApiAccessStatus());
            }

            if ($action === 'API001PolicyUnlock') {
                $password = isset($request['password']) ? (string) $request['password'] : '';
                return $this->response(true, 'Área liberada com sucesso.', $policyAdmin->unlockRestApi($password));
            }

            $policyAdmin->assertRestApiUnlocked();

            if ($action === 'API001PolicyStatus') {
                $status = $policyAdmin->getPolicyStatus();
                $status['status_label'] = $status['status'] === 'valid'
                    ? 'Válida'
                    : ($status['status'] === 'missing' ? 'Ausente' : 'Inválida');

                return $this->response(true, '', $status);
            }

            if ($action === 'API001PolicyLoadTable') {
                $table = isset($request['table']) ? (string) $request['table'] : '';
                return $this->response(true, '', $policyAdmin->loadTable($table));
            }

            if ($action === 'API001PolicyPreview') {
                $table = isset($request['table']) ? (string) $request['table'] : '';
                $mode = isset($request['mode']) ? (string) $request['mode'] : '';
                $fields = isset($request['fields']) ? (array) $request['fields'] : [];

                return $this->response(true, '', [
                    'preview' => $policyAdmin->previewTable($table, $mode, $fields),
                ]);
            }

            if ($action === 'API001PolicySave') {
                $table = isset($request['table']) ? (string) $request['table'] : '';
                $mode = isset($request['mode']) ? (string) $request['mode'] : '';
                $fields = isset($request['fields']) ? (array) $request['fields'] : [];

                return $this->response(true, 'Policy salva com sucesso.', $policyAdmin->saveTable($table, $mode, $fields));
            }

            if ($action === 'API001PolicyRemoveTable') {
                $table = isset($request['table']) ? (string) $request['table'] : '';
                return $this->response(true, 'Tabela removida da policy.', $policyAdmin->removeTable($table));
            }

            if ($action === 'API001TokenStatus') {
                return $this->response(true, '', $policyAdmin->getTokenStatus());
            }

            if ($action === 'API001TokenCreate') {
                $name = isset($request['name']) ? (string) $request['name'] : '';
                $observation = isset($request['observation']) ? (string) $request['observation'] : '';
                return $this->response(true, 'Token gerado com sucesso.', $policyAdmin->createToken($name, $observation));
            }

            if ($action === 'API001TokenSetStatus') {
                $tokenId = isset($request['token_id']) ? (int) $request['token_id'] : 0;
                $status = isset($request['status']) ? (string) $request['status'] : '';
                return $this->response(true, 'Status do token atualizado.', $policyAdmin->updateTokenStatus($tokenId, $status));
            }

            return $this->response(false, 'Ação administrativa inválida.', [], 404);
        } catch (\Throwable $throwable) {
            return $this->response(false, $throwable->getMessage(), []);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array{success:bool,message:string,data:array<string,mixed>,status_code:int}
     */
    private function response(bool $success, string $message = '', array $data = [], int $statusCode = 200): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ];
    }
}
