(function () {
  'use strict';
  const $ = (id) => document.getElementById(id);
  if (!$('portalLogin')) return;
  if (location.search) history.replaceState(null, '', location.pathname);
  const csrf = document.body.dataset.csrf;
  let timer;
  async function post(action, values = {}) {
    const response = await fetch('api.php?acao=' + action, { method: 'POST', credentials: 'same-origin',
      headers: { 'X-CSRF-Token': csrf }, body: new URLSearchParams(values) });
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || 'Não foi possível concluir. Tente novamente.');
    return data;
  }
  function countdown(button, seconds, label) {
    clearInterval(timer); const until = Date.now() + seconds * 1000;
    const tick = () => {
      const left = Math.max(0, Math.ceil((until - Date.now()) / 1000));
      button.disabled = left > 0; button.textContent = left ? label + ' (' + left + 's)' : label;
      if (!left) clearInterval(timer);
    };
    tick(); timer = setInterval(tick, 1000);
  }
  function submit(id, action, values) {
    if (!$(id)) return;
    $(id).addEventListener('submit', async (event) => {
      event.preventDefault(); const button = event.currentTarget.querySelector('[type=submit]');
      button.disabled = true; $('loginStatus').textContent = 'Aguarde…';
      try { await post(action, values()); location.reload(); }
      catch (error) { $('loginStatus').textContent = error.message; button.disabled = false; }
    });
  }
  if ($('emailForm')) {
    let email = '';
    async function sendCode() {
      const button = $('emailForm').querySelector('button');
      button.disabled = true; $('resendCode').disabled = true; $('loginStatus').textContent = 'Enviando código…';
      try {
        const data = await post('solicitarCodigo', { email });
        $('emailForm').hidden = true; $('codeForm').hidden = false; $('loginTitle').textContent = 'Confira seu e-mail';
        $('loginDescription').textContent = 'Digite o código recebido para confirmar seu e-mail e continuar.';
        $('loginStatus').textContent = data.mensagem; $('codigo').value = ''; $('codigo').focus();
        countdown($('resendCode'), data.reenviarEm, 'Reenviar código');
      } catch (error) { $('loginStatus').textContent = error.message; $('resendCode').disabled = false; }
      finally { button.disabled = false; }
    }
    $('emailForm').addEventListener('submit', (event) => { event.preventDefault(); email = $('email').value; sendCode(); });
    $('resendCode').addEventListener('click', sendCode);
    $('changeEmail').addEventListener('click', () => {
      $('emailForm').hidden = false; $('codeForm').hidden = true; $('loginTitle').textContent = 'Confirme seu e-mail';
      $('loginDescription').textContent = 'Informe o e-mail cadastrado na empresa.'; $('loginStatus').textContent = ''; $('email').focus();
    });
    $('codigo').addEventListener('input', () => { $('codigo').value = $('codigo').value.replace(/\D/g, '').slice(0, 6); });
  }
  submit('codeForm', 'confirmarCodigo', () => ({ codigo: $('codigo').value }));
  submit('passwordForm', 'salvarSenha', () => ({ senha: $('senha').value, confirmacao: $('confirmacao').value }));
  submit('loginForm', 'entrar', () => ({ email: $('email').value, senha: $('senha').value }));
  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const field = $(button.dataset.passwordToggle);
      const show = field.type === 'password';
      field.type = show ? 'text' : 'password';
      button.textContent = show ? 'Ocultar' : 'Mostrar';
      button.setAttribute('aria-pressed', String(show));
      button.setAttribute('aria-label', (show ? 'Ocultar ' : 'Mostrar ') + button.dataset.passwordLabel);
    });
  });
}());
