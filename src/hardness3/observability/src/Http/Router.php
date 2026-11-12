<?php
declare(strict_types=1);

namespace Hardness\Observability\Http;

final class Router
{
    private $controller;
    private $auth;
    private $session;

    public function __construct(PrototypeController $controller, ?AuthController $auth = null, $session = null)
    {
        $this->controller = $controller;
        $this->auth=$auth; $this->session=$session;
    }

    public function dispatch(Request $request): Response
    {
        $path = $this->relativePath($request->path());
        $method = $request->method() === 'HEAD' ? 'GET' : $request->method();
        if ($path === '/login' && $method==='GET' && $this->auth) { return $this->auth->loginPage(); }
        if ($path === '/auth/login' && $request->method()==='POST' && $this->auth) { return $this->auth->login($request); }
        if (($path === '/auth/logout' || $path === '/logout') && $request->method()==='POST' && $this->auth) { return $this->auth->logout($request); }
        if ($this->session) { try { $this->session->start(); $this->session->current(); } catch (\Throwable $e) { return strpos($path, '/api/')===0 ? Response::json(array('authenticated'=>false),401) : Response::redirect($this->basePath($request->path()) . '/login'); } }

        if (in_array($path,array('/captures','/error-logs'),true) && $this->session && $this->session->current()->toArray()['role'] !== 'hardness_internal') {
            return Response::redirect($this->basePath($request->path()) . '/');
        }

        if ($path === '/api/v1/captures' && $method === 'POST') {
            if (!$this->session || !$this->session->verifyCsrf($request->post('csrf'))) { return Response::json(array('error' => 'Solicitação inválida.'), 403); }
            return $this->controller->capture($request->post('note'));
        }
        if (preg_match('#^/api/v1/captures/(\d{8}-\d{6}-[a-f0-9]{8})/delete$#', $path, $match) === 1 && $method === 'POST') {
            if (!$this->session || !$this->session->verifyCsrf($request->post('csrf'))) { return Response::json(array('error' => 'Solicitação inválida.'), 403); }
            return $this->controller->deleteCapture($match[1]);
        }
        if ($method !== 'GET') { return Response::json(array('error' => 'Método não permitido.'), 405); }

        if ($path === '/api/v1/session' && $this->session) { return Response::json($this->session->publicState()); }

        if (in_array($path, array('/', '/server', '/database', '/processes', '/mysql-history', '/diagnostics', '/network', '/captures', '/error-logs'), true)) {
            return $this->controller->page($request->path());
        }

        if ($path === '/api/v1/snapshot') {
            return $this->controller->snapshot();
        }

        if ($path === '/api/v1/network-probe') {
            return $this->controller->networkProbe();
        }
        if ($path === '/api/v1/mysql-history') { return $this->controller->mysqlHistory($request); }
        if ($path === '/api/v1/error-logs') { return $this->controller->errorLogs(); }
        if ($path === '/api/v1/captures') { return $this->controller->captures(); }
        if (preg_match('#^/api/v1/captures/(\d{8}-\d{6}-[a-f0-9]{8})/download$#', $path, $match) === 1) { return $this->controller->downloadCapture($match[1]); }
        if (preg_match('#^/api/v1/captures/(\d{8}-\d{6}-[a-f0-9]{8})$#', $path, $match) === 1) { return $this->controller->captureById($match[1]); }

        return Response::json(array('error' => 'Recurso não encontrado.'), 404);
    }

    private function relativePath(string $path): string
    {
        if (preg_match('#^(?:.*?/)?observability(?:/|$)#', $path, $match) === 1) { return substr($path, strlen(rtrim($match[0], '/'))) ?: '/'; }
        return $path;
    }
    private function basePath(string $path): string { return preg_match('#^(.*?/observability)(?:/|$)#', $path, $match) === 1 ? $match[1] : '/observability'; }
}
