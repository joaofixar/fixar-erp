<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/app.php';

tablet_separacao_bootstrap();

$error = null;
$handoff = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && (isset($_POST['tablet_separacao_qr_login']) || isset($_POST['tablet_separacao_qr_password']))) {
    $csrf = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals(tablet_separacao_qr_csrf_token(), $csrf)) {
        $error = 'Não foi possível validar a solicitação. Gere o QR Code novamente.';
    } else {
        $authenticated = tablet_separacao_authenticate(
            (string) ($_POST['tablet_separacao_qr_login'] ?? ''),
            (string) ($_POST['tablet_separacao_qr_password'] ?? '')
        );
        if (!is_array($authenticated)) {
            $error = $authenticated;
        } else {
            try {
                $handoff = tablet_separacao_qr_issue_handoff([
                    'user_id' => (int) $authenticated['C007_Id'],
                    'database' => (string) $authenticated['_database'],
                ]);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$qrImage = $handoff
    ? tablet_separacao_qr_svg_data_uri(tablet_separacao_qr_handoff_url((string) $handoff['code']))
    : '';
$ajax = !empty($_REQUEST['ajax']);
if (!$ajax):
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Abrir Separação no celular</title></head><body><?php endif; ?>
<div id="tablet-separacao-qr-login-card" style="box-sizing:border-box;max-width:430px;padding:16px 18px;color:#1f2937;font-family:Arial,sans-serif;">
<?php if ($handoff): ?>
    <h2 style="margin:0 0 10px;font-size:18px;line-height:1.25;">Leia o QR Code no celular</h2>
    <p style="margin:0 0 16px;font-size:13px;line-height:1.45;">O código expira em 2 minutos e pode ser usado uma única vez.</p>
    <p style="text-align:center"><img src="<?php echo h($qrImage); ?>" width="260" height="260" alt="QR Code para abrir a Separação no celular"></p>
<?php else: ?>
    <h2 style="margin:0 0 10px;font-size:18px;line-height:1.25;">Abrir Separação no celular</h2>
    <p style="margin:0 0 18px;font-size:13px;line-height:1.45;">Confirme suas credenciais para gerar o QR Code.</p>
    <?php if ($error): ?><div style="margin:0 0 14px;padding:10px 12px;border-radius:4px;background:#fdecec;color:#a61919;font-size:13px;"><?php echo h($error); ?></div><?php endif; ?>
    <form method="post" action="<?php echo h(app_base_path('qr-login.php')); ?>" autocomplete="off" style="display:grid;gap:14px;">
        <input type="hidden" name="csrf" value="<?php echo h(tablet_separacao_qr_csrf_token()); ?>">
        <label style="display:grid;gap:6px;font-size:13px;font-weight:600;"><span>Login</span><input type="text" name="tablet_separacao_qr_login" required autofocus style="box-sizing:border-box;width:100%;height:36px;padding:7px 9px;border:1px solid #b8c3d1;border-radius:4px;font-size:14px;"></label>
        <label style="display:grid;gap:6px;font-size:13px;font-weight:600;"><span>Senha</span><input type="password" name="tablet_separacao_qr_password" required style="box-sizing:border-box;width:100%;height:36px;padding:7px 9px;border:1px solid #b8c3d1;border-radius:4px;font-size:14px;"></label>
        <button type="submit" style="justify-self:start;min-width:132px;height:36px;padding:0 16px;border:0;border-radius:5px;background:#0866cf;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Gerar QR Code</button>
    </form>
    <p style="margin:16px 0 0;font-size:13px;"><a href="<?php echo h(app_base_path()); ?>" target="_blank" rel="noopener" style="color:#1267c4;">Abrir Separação diretamente neste computador</a></p>
<?php endif; ?>
</div>
<?php if (!$ajax): ?></body></html><?php endif; ?>
<script>(function(){var form=document.querySelector('#tablet-separacao-qr-login-card form');if(!form||!window.jQuery){return;}form.addEventListener('submit',function(event){event.preventDefault();var data=window.jQuery(form).serialize()+'&ajax=true';window.jQuery.post(form.action,data).done(function(response){window.jQuery('#tablet-separacao-qr-login-card').replaceWith(response);}).fail(function(xhr){window.jQuery('#tablet-separacao-qr-login-card').prepend('<div style="color:#a61919">'+(xhr.responseText||'Não foi possível gerar o QR Code.')+'</div>');});});})();</script>
