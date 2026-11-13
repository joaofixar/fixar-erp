<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$handoff = balanco_qr_consume_handoff((string) ($_GET['codigo'] ?? ''));
$authorized = false;
if (is_array($handoff)) {
    balanco_start_authenticated_session([
        'C007_Id' => (int) $handoff['user_id'],
        '_database' => (string) $handoff['database'],
    ], 'qr_handoff');

    $context = balanco_selection_context();
    if ($context !== null && (int) $context['user_id'] === (int) $handoff['user_id']) {
        global $g;
        $userId = (int) $handoff['user_id'];
        $user = mysqli_query($g['conexaoBanco'], "SELECT C007_Id FROM C007 WHERE C007_Id='{$userId}' AND C007_Ativo='S' LIMIT 1");
        $authorized = $user && mysqli_fetch_assoc($user);
    }
}

if ($authorized) {
    header('Location: ' . balanco_url('select-company.php'));
    exit;
}

$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
http_response_code(403);
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Não foi possível abrir o Balanço</title><link rel="stylesheet" href="<?php echo balanco_h(balanco_url('assets/css/app.css')); ?>"></head><body class="balanco-login-page"><main class="page"><section class="login-hero"><div class="login-card"><div class="alert alert--error">Este QR Code é inválido, expirou, já foi utilizado ou o usuário não possui mais acesso ao Balanço.</div><a class="button button--primary button--block" href="<?php echo balanco_h(balanco_url('login.php')); ?>">Entrar no Balanço</a></div></section></main></body></html>
