<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

$handoff = tablet_separacao_qr_consume_handoff((string) ($_GET['codigo'] ?? ''));
if (is_array($handoff) && tablet_separacao_qr_start_session($handoff)) {
    header('Location: ' . app_base_path('pedidos.php'));
    exit;
}

http_response_code(403);
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Não foi possível abrir a Separação</title></head><body style="font-family:Arial,sans-serif;color:#1f2937;padding:24px;"><p>Este QR Code é inválido, expirou, já foi utilizado ou o usuário não possui mais acesso à Separação.</p><p><a href="<?php echo h(app_base_path('login.php')); ?>">Entrar na Separação</a></p></body></html>
