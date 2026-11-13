<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$error = null;
$handoff = null;
// O framework do ERP carrega conteúdos de janelas por POST/AJAX, mesmo sem
// enviar o formulário. Só a presença das credenciais caracteriza o envio real.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['balanco_qr_login']) || isset($_POST['balanco_qr_password']))) {
    balanco_require_csrf();
    $authenticated = balanco_authenticate((string) ($_POST['balanco_qr_login'] ?? ''), (string) ($_POST['balanco_qr_password'] ?? ''));
    if (!is_array($authenticated)) {
        $error = $authenticated;
    } else {
        try {
            // O QR representa o usuário cujas credenciais foram validadas,
            // sem alterar a sessão do ERP que o está emitindo.
            $handoff = balanco_qr_issue_handoff([
                'user_id' => (int) $authenticated['C007_Id'],
                'database' => (string) $authenticated['_database'],
            ]);
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$qrUrl = $handoff ? balanco_qr_handoff_url((string) $handoff['code']) : '';
$qrImage = $handoff ? balanco_qr_svg_data_uri($qrUrl) : '';
$ajax = !empty($_REQUEST['ajax']);
if (!$ajax): ?><!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Abrir Balanço no celular</title><link rel="stylesheet" href="<?php echo balanco_h(balanco_url('assets/css/app.css')); ?>"></head>
<body class="balanco-login-page"><main class="page"><section class="login-hero"><?php endif; ?>
<div class="login-card" id="balanco-qr-login-card" style="box-sizing:border-box;max-width:430px;padding:16px 18px;color:var(--letra-form,#1f2937);font-family:Arial,sans-serif;">
<?php if ($handoff): ?>
    <h2 style="margin:0 0 10px;font-size:18px;line-height:1.25;">Leia o QR Code no celular</h2>
    <p style="margin:0 0 16px;font-size:13px;line-height:1.45;">No celular, selecione e confirme a empresa antes de iniciar o Balanço. Este código expira em 2 minutos e pode ser usado uma única vez.</p>
    <p style="text-align:center"><img src="<?php echo balanco_h($qrImage); ?>" width="260" height="260" alt="QR Code para abrir o Balanço no celular"></p>
<?php else: ?>
    <h2 style="margin:0 0 10px;font-size:18px;line-height:1.25;">Abrir Balanço no celular</h2>
    <p style="margin:0 0 18px;font-size:13px;line-height:1.45;">Confirme suas credenciais para gerar o QR Code.</p>
    <?php if ($error): ?><div class="alert alert--error" style="margin:0 0 14px;padding:10px 12px;border-radius:4px;background:var(--celula-vermelho,#fdecec);color:var(--BGVermelho,#a61919);font-size:13px;"><?php echo balanco_h($error); ?></div><?php endif; ?>
    <form method="post" action="<?php echo balanco_h(balanco_url('qr-login.php')); ?>" class="stack-lg" autocomplete="off" style="display:grid;gap:14px;">
        <input type="hidden" name="csrf" value="<?php echo balanco_h(balanco_csrf_token()); ?>">
        <label class="field" style="display:grid;gap:6px;font-size:13px;font-weight:600;"><span>Login</span><input type="text" name="balanco_qr_login" autocomplete="off" required autofocus style="box-sizing:border-box;width:100%;height:36px;padding:7px 9px;border:1px solid var(--borda-campo-form,#b8c3d1);border-radius:4px;background:var(--fundo-campo-form,#fff);color:var(--letra-form,#1f2937);font-size:14px;"></label>
        <label class="field" style="display:grid;gap:6px;font-size:13px;font-weight:600;"><span>Senha</span><input type="password" name="balanco_qr_password" autocomplete="off" required style="box-sizing:border-box;width:100%;height:36px;padding:7px 9px;border:1px solid var(--borda-campo-form,#b8c3d1);border-radius:4px;background:var(--fundo-campo-form,#fff);color:var(--letra-form,#1f2937);font-size:14px;"></label>
        <button class="button button--primary button--block" type="submit" style="justify-self:start;min-width:132px;height:36px;padding:0 16px;border:0;border-radius:5px;background:#0866cf;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Gerar QR Code</button>
    </form>
    <p style="margin:16px 0 0;font-size:13px;"><a href="<?php echo balanco_h(balanco_url()); ?>" target="_blank" rel="noopener" style="color:var(--BGAzul,#1267c4);">Abrir Balanço diretamente neste computador</a></p>
<?php endif; ?>
</div>
<?php if (!$ajax): ?></section></main></body></html><?php endif; ?>
<script>(function(){var form=document.querySelector('#balanco-qr-login-card form');if(!form||!window.jQuery){return;}form.addEventListener('submit',function(event){event.preventDefault();var data=window.jQuery(form).serialize()+'&ajax=true';window.jQuery.post(form.action,data).done(function(response){window.jQuery('#balanco-qr-login-card').replaceWith(response);}).fail(function(xhr){window.jQuery('#balanco-qr-login-card').prepend('<div class="alert alert--error">'+(xhr.responseText||'Não foi possível gerar o QR Code.')+'</div>');});});})();</script>
