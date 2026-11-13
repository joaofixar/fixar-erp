<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if (balanco_context()) { header('Location: ' . balanco_url('resultados.php')); exit; }
if (balanco_selection_context()) { header('Location: ' . balanco_url('select-company.php')); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    balanco_require_csrf();
    $error = balanco_login((string) ($_POST['login'] ?? ''), (string) ($_POST['password'] ?? ''));
    if ($error === null) { header('Location: ' . balanco_url('resultados.php')); exit; }
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Entrar | Balanço</title><link rel="stylesheet" href="<?php echo balanco_h(balanco_url('assets/css/app.css') . '?v=' . (string) @filemtime(BALANCO_MODULE . '/public/assets/css/app.css')); ?>"></head><body class="balanco-login-page"><div class="app-shell"><header class="topbar"><div class="topbar__brand"><div class="topbar__custom-brand"><div class="login-brand"><img class="login-brand__logo" src="/hardness3/static/img/logo/logoHardnessH.svg" alt="Hardness Sistemas"><p class="login-brand__label">Módulo Balanço</p></div></div></div></header><main class="page"><section class="login-hero"><div class="login-card"><?php if ($error): ?><div class="alert alert--error"><?php echo balanco_h($error); ?></div><?php endif; ?><form method="post" class="stack-lg"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><label class="field"><span>Login</span><input type="text" name="login" placeholder="Usuário" autocomplete="username" required></label><label class="field"><span>Senha</span><input type="password" name="password" placeholder="Senha" autocomplete="current-password" required></label><button class="button button--primary button--block" type="submit">Entrar</button></form></div></section></main></div></body></html>
