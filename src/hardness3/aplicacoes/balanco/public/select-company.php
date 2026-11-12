<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$baseContext = balanco_selection_context();
if ($baseContext === null) {
    header('Location: ' . balanco_url('login.php'));
    exit;
}

$error = null;
$candidate = null;
$companies = balanco_companies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    balanco_require_csrf();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'cancel') {
        balanco_logout();
        header('Location: ' . balanco_url('login.php'));
        exit;
    }

    $companyId = (int) ($_POST['company_id'] ?? 0);
    if ($action === 'choose') {
        if (!balanco_select_company($companyId)) {
            $error = 'A empresa escolhida não está mais autorizada para este usuário.';
        } else {
            $candidate = balanco_active_company();
        }
    } elseif ($action === 'confirm') {
        // Reconsulta C019 dentro de balanco_confirm_company() antes de abrir
        // o contexto operacional da empresa.
        if (!balanco_confirm_company($companyId)) {
            $error = 'A empresa escolhida não está mais autorizada para este usuário.';
            balanco_clear_operational_context();
        } else {
            header('Location: ' . balanco_url());
            exit;
        }
    }
}

if ($candidate === null && count($companies) === 1) {
    // Uma única empresa ainda exige a confirmação explícita; apenas evita a
    // tela intermediária de escolha.
    $candidate = reset($companies);
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Selecionar empresa | Balanço</title><link rel="stylesheet" href="<?php echo balanco_h(balanco_url('assets/css/app.css') . '?v=' . (string) @filemtime(BALANCO_MODULE . '/public/assets/css/app.css')); ?>"></head><body class="balanco-login-page"><div class="app-shell"><header class="topbar"><div><p class="eyebrow">Hardness</p><h1>Balanço</h1></div></header><main class="page"><section class="login-hero"><div class="login-card stack-lg"><?php if ($error): ?><div class="alert alert--error"><?php echo balanco_h($error); ?></div><?php endif; ?><?php if ($candidate !== null): ?><h2>Confirmar empresa</h2><p>Empresa selecionada:<br><strong><?php echo balanco_h((string) $candidate['name']); ?></strong></p><div class="company-selection-actions"><form method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="action" value="confirm"><input type="hidden" name="company_id" value="<?php echo (int) $candidate['id']; ?>"><button class="button button--primary" type="submit">Continuar</button></form><form method="post"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="action" value="cancel"><button class="button button--secondary" type="submit">Cancelar</button></form></div><?php else: ?><h2>Selecionar empresa</h2><p>Escolha a empresa para iniciar o Balanço.</p><form method="post" class="stack-lg"><input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>"><input type="hidden" name="action" value="choose"><label class="field"><span>Empresa</span><select name="company_id" required><option value="">Selecione</option><?php foreach ($companies as $company): ?><option value="<?php echo (int) $company['id']; ?>"><?php echo balanco_h((string) $company['name']); ?></option><?php endforeach; ?></select></label><div class="company-selection-actions"><button class="button button--primary" type="submit">Continuar</button><button class="button button--secondary" type="submit" name="action" value="cancel" formnovalidate>Cancelar</button></div></form><?php endif; ?></div></section></main></div></body></html>
