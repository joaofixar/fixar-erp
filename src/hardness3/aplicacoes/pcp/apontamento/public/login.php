<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if (pcp_apontamento_context()) { header('Location: ' . pcp_apontamento_url('index.php?pagina=visao-geral')); exit; }
if (!empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'])) { header('Location: ' . pcp_apontamento_url('select-company.php')); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    pcp_apontamento_require_csrf();
    $user = pcp_apontamento_authenticate((string) ($_POST['login'] ?? ''), (string) ($_POST['password'] ?? ''));
    if (!$user) $error = 'Login ou senha inválidos, ou sem acesso ao Apontamento PCP.';
    else { pcp_apontamento_start_session($user); $companies = $_SESSION[PCP_APONTAMENTO_SESSION]['companies']; if (count($companies) === 1) pcp_apontamento_select_company((int) array_key_first($companies)); header('Location: ' . pcp_apontamento_url(count($companies) === 1 ? 'index.php?pagina=visao-geral' : 'select-company.php')); exit; }
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex"><title>Entrar | Apontamento PCP</title><link rel="stylesheet" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('assets/css/app.css') . '?v=' . (string) @filemtime(PCP_APONTAMENTO_MODULE . '/public/assets/css/app.css')); ?>"></head><body class="pcp-login-page"><main class="pcp-login"><section class="pcp-login-card"><img src="/hardness3/static/img/logo/logoHardnessH.svg" alt="Hardness Sistemas"><p class="eyebrow">Terminal de produção</p><h1>Apontamento PCP</h1><p>Entre com seu usuário Hardness para iniciar o terminal.</p><?php if ($error): ?><div class="alert alert--error"><?php echo pcp_apontamento_h($error); ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><label>Login<input name="login" autocomplete="username" autofocus required></label><label>Senha<input type="password" name="password" autocomplete="current-password" required></label><button type="submit">Entrar no terminal</button></form></section></main></body></html>
