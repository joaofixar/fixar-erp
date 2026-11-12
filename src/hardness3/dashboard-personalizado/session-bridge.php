<?php
namespace hardness;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('error' => 'Método não permitido.'));
    exit;
}

require_once __DIR__ . '/../bibliotecas/confUsuario.php';

$contexto = dashboardPersonalizadoConsumirTokenSessao(isset($_POST['token']) ? $_POST['token'] : '');
if ($contexto === false) {
    if (dashboardPersonalizadoLerAutorizacao($confUsuario) !== false) {
        echo json_encode(array('ok' => true));
        exit;
    }

    http_response_code(401);
    echo json_encode(array('error' => 'Acesso ao dashboard inválido ou expirado.'));
    exit;
}

if (!dashboardPersonalizadoGravarAutorizacao($contexto, $confUsuario)) {
    http_response_code(500);
    echo json_encode(array('error' => 'Não foi possível autorizar o dashboard.'));
    exit;
}

echo json_encode(array('ok' => true));
