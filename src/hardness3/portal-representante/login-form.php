<?php
namespace hardness;
if (realpath(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '') === __FILE__) { http_response_code(404); exit; }
$titulosLogin = array('email' => 'Confirme seu e-mail', 'senha' => 'Cadastre sua senha', 'entrar' => 'Acesse seu portal', 'indisponivel' => 'Acesso indisponível');
?>
<div id="portalLogin" data-etapa="<?= portalRepresentanteHtml($loginEtapa) ?>">
  <?php if (in_array($loginEtapa, array('email', 'senha'), true)): ?>
  <ol class="login-steps" aria-label="Primeiro acesso"><li class="<?= $loginEtapa === 'email' ? 'current' : 'done' ?>">1. Confirmar e-mail</li><li class="<?= $loginEtapa !== 'email' ? 'current' : '' ?>">2. Criar senha</li></ol>
  <?php endif; ?>
  <h2 id="loginTitle"><?= $titulosLogin[$loginEtapa] ?></h2>
  <?php if ($loginNome !== ''): ?><p class="login-identity"><?= portalRepresentanteHtml($loginNome) ?></p><?php endif; ?>
  <?php if ($loginEtapa === 'email'): ?>
    <p id="loginDescription">Informe o e-mail cadastrado na empresa. Enviaremos um código para confirmar que ele é seu.</p>
    <form id="emailForm"><label for="email">E-mail cadastrado</label><input id="email" name="email" type="email" autocomplete="email" required maxlength="254" placeholder="seu@email.com"><button type="submit" class="primary login-submit">Receber código de confirmação</button></form>
    <form id="codeForm" hidden><label for="codigo">Código recebido por e-mail</label><input id="codigo" name="codigo" class="otp-input" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required placeholder="000000"><small>Válido por 5 minutos e para um único uso.</small><button type="submit" class="primary login-submit">Confirmar e-mail</button><button type="button" class="secondary resend" id="resendCode" disabled>Reenviar código</button><button type="button" class="text-button" id="changeEmail">Corrigir e-mail</button></form>
  <?php elseif ($loginEtapa === 'senha'): ?>
    <p>E-mail confirmado. Escolha a senha que usará para acompanhar suas vendas.</p>
    <form id="passwordForm">
      <label for="senha">Nova senha</label>
      <div class="password-field"><input id="senha" name="senha" type="password" autocomplete="new-password" required minlength="10" maxlength="72" aria-describedby="passwordHelp"><button type="button" class="password-toggle" data-password-toggle="senha" data-password-label="nova senha" aria-controls="senha" aria-pressed="false" aria-label="Mostrar nova senha">Mostrar</button></div>
      <small id="passwordHelp">Use pelo menos 10 caracteres. Prefira uma frase fácil de lembrar.</small>
      <label for="confirmacao">Confirme a senha</label>
      <div class="password-field"><input id="confirmacao" name="confirmacao" type="password" autocomplete="new-password" required minlength="10" maxlength="72"><button type="button" class="password-toggle" data-password-toggle="confirmacao" data-password-label="confirmação da senha" aria-controls="confirmacao" aria-pressed="false" aria-label="Mostrar confirmação da senha">Mostrar</button></div>
      <button type="submit" class="primary login-submit">Salvar senha</button>
    </form>
  <?php elseif ($loginEtapa === 'entrar'): ?>
    <p>Entre com seu e-mail e a senha cadastrada.</p>
    <form id="loginForm">
      <label for="email">E-mail</label><input id="email" name="email" type="email" autocomplete="username" required maxlength="254" placeholder="seu@email.com" value="<?= portalRepresentanteHtml($loginEmail) ?>">
      <label for="senha">Senha</label>
      <div class="password-field"><input id="senha" name="senha" type="password" autocomplete="current-password" required maxlength="72"<?= $loginEmail !== '' ? ' autofocus' : '' ?>><button type="button" class="password-toggle" data-password-toggle="senha" data-password-label="senha" aria-controls="senha" aria-pressed="false" aria-label="Mostrar senha">Mostrar</button></div>
      <button type="submit" class="primary login-submit">Entrar</button>
    </form>
    <small>Primeiro acesso? Abra o link enviado pela empresa. Esqueceu sua senha? Solicite ao responsável uma nova liberação.</small>
  <?php endif; ?>
  <p id="loginStatus" class="form-status" role="status" aria-live="polite"><?= portalRepresentanteHtml($loginMensagem) ?></p>
  <?php if ($loginEtapa === 'email'): ?><small>Seu e-mail mudou? Solicite à empresa a atualização do cadastro e uma nova liberação.</small><?php endif; ?>
</div>
