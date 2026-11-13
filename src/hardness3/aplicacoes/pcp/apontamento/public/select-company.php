<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap/app.php';
if (pcp_apontamento_context()) { header('Location: ' . pcp_apontamento_url('index.php?pagina=visao-geral')); exit; }
if (empty($_SESSION[PCP_APONTAMENTO_SESSION]['authenticated'])) { header('Location: ' . pcp_apontamento_url('login.php')); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    pcp_apontamento_require_csrf();
    if ((string) ($_POST['action'] ?? '') === 'logout') { pcp_apontamento_logout(); header('Location: ' . pcp_apontamento_url('login.php')); exit; }
    if (pcp_apontamento_select_company((int) ($_POST['company_id'] ?? 0))) { header('Location: ' . pcp_apontamento_url('index.php?pagina=visao-geral')); exit; }
    $error = 'A empresa escolhida não está autorizada para este usuário.';
}
$companies = (array) ($_SESSION[PCP_APONTAMENTO_SESSION]['companies'] ?? []);
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex"><title>Empresa | Apontamento PCP</title><link rel="stylesheet" href="<?php echo pcp_apontamento_h(pcp_apontamento_url('assets/css/app.css') . '?v=' . (string) @filemtime(PCP_APONTAMENTO_MODULE . '/public/assets/css/app.css')); ?>"></head><body class="pcp-login-page"><main class="pcp-login"><section class="pcp-login-card"><p class="eyebrow">Terminal de produção</p><h1>Selecionar empresa</h1><p>Os apontamentos e consultas desta sessão usarão somente a empresa escolhida.</p><?php if ($error): ?><div class="alert alert--error"><?php echo pcp_apontamento_h($error); ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><label>Empresa<select name="company_id" required><option value="">Selecione a empresa</option><?php foreach ($companies as $company): ?><option value="<?php echo (int) $company['id']; ?>"><?php echo pcp_apontamento_h($company['name']); ?></option><?php endforeach; ?></select></label><button type="submit">Usar esta empresa</button></form><form method="post" class="pcp-login-cancel"><input type="hidden" name="csrf" value="<?php echo pcp_apontamento_h(pcp_apontamento_csrf_token()); ?>"><button class="secondary" name="action" value="logout">Cancelar</button></form></section></main></body></html>
