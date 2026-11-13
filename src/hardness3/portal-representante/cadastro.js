(() => {
  'use strict';
  const version = 5;
  const previous = window.hardnessRepresentanteCadastro;
  if (previous && previous.version === version) return;
  if (previous && previous.dispose) previous.dispose();
  let dialog;
  let trigger;
  let sending = false;
  const element = (tag, className, text) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text) node.textContent = text;
    return node;
  };
  function prepareDialog(button, revoke) {
    if (!dialog) {
      dialog = element('dialog', 'cad003-portal-modal');
      dialog.setAttribute('aria-labelledby', 'cad003-portal-title');
      dialog.setAttribute('aria-describedby', 'cad003-portal-message');
      const header = element('header');
      const title = element('h2'); title.id = 'cad003-portal-title'; title.tabIndex = -1;
      header.append(title);
      const content = element('div', 'cad003-portal-result');
      content.setAttribute('role', 'status'); content.setAttribute('aria-live', 'polite'); content.setAttribute('aria-atomic', 'true');
      const icon = element('span', 'cad003-portal-icon'); icon.setAttribute('aria-hidden', 'true');
      const copy = element('div');
      const message = element('p'); message.id = 'cad003-portal-message';
      copy.append(element('strong'), message); content.append(icon, copy);
      const footer = element('footer');
      const close = element('button', 'cad003-portal-close', 'Fechar'); close.type = 'button';
      close.addEventListener('click', () => { if (!sending) dialog.close(); });
      dialog.addEventListener('keydown', (event) => {
        if (sending && event.key === 'Escape') { event.preventDefault(); event.stopPropagation(); }
      });
      dialog.addEventListener('cancel', (event) => { if (sending) event.preventDefault(); });
      dialog.addEventListener('close', () => { if (trigger && trigger.isConnected) trigger.focus(); });
      footer.append(close); dialog.append(header, content, footer); document.body.append(dialog);
    }
    trigger = button;
    sending = true;
    dialog.querySelector('h2').textContent = revoke ? 'Revogar acesso' : 'Disponibilizar link';
    dialog.dataset.kind = 'pending';
    dialog.setAttribute('aria-busy', 'true');
    dialog.querySelector('.cad003-portal-icon').textContent = '';
    dialog.querySelector('strong').textContent = revoke ? 'Revogando acesso…' : 'Enviando e-mail…';
    dialog.querySelector('p').textContent = revoke ? 'Aguarde enquanto o acesso externo é encerrado.' : 'Aguarde enquanto enviamos o link para o e-mail cadastrado do representante.';
    dialog.querySelector('button').disabled = true;
    dialog.showModal(); dialog.querySelector('h2').focus();
  }
  function finishDialog(message, success, revoke) {
    sending = false;
    dialog.dataset.kind = success ? 'success' : 'error';
    dialog.setAttribute('aria-busy', 'false');
    dialog.querySelector('.cad003-portal-icon').textContent = success ? '✓' : '!';
    dialog.querySelector('strong').textContent = success ? (revoke ? 'Acesso revogado' : 'E-mail enviado') : 'Não foi possível concluir';
    dialog.querySelector('p').textContent = message;
    dialog.querySelector('button').disabled = false;
    dialog.querySelector('button').focus();
  }
  const handleClick = async (event) => {
    const button = event.target.closest('[data-rep-acao][data-rep-id]');
    if (!button) return;
    event.preventDefault(); event.stopImmediatePropagation();
    if (button.disabled || (dialog && dialog.open)) return;
    const area = button.closest('.cad003-portal-acesso');
    const revoke = button.dataset.repAcao === 'revogar';
    const action = revoke ? 'portalRepresentanteRevogar' : 'portalRepresentanteLiberar';
    prepareDialog(button, revoke);
    const buttons = area.querySelectorAll('button'); buttons.forEach((node) => { node.disabled = true; });
    button.setAttribute('aria-busy', 'true');
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);
    try {
      // Deixa o navegador pintar a janela antes de iniciar a requisição.
      await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
      const response = await fetch(`/cad/cad003/form_func-ajax/${action}/?ajax=true`, {
        method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json' },
        signal: controller.signal,
        body: new URLSearchParams({ representante: button.dataset.repId, csrf: button.dataset.repCsrf })
      });
      const data = await response.json();
      if (!response.ok || !data.ok) throw new Error(data.error || 'Não foi possível concluir a solicitação.');
      finishDialog(data.mensagem, true, revoke);
    } catch (error) {
      finishDialog(error.name === 'AbortError' || error instanceof TypeError ? 'Não foi possível confirmar o resultado. Verifique sua conexão e aguarde antes de tentar novamente.'
        : error instanceof SyntaxError ? 'Sua sessão expirou ou o serviço está indisponível. Atualize a tela.' : error.message, false, revoke);
    } finally {
      clearTimeout(timeout);
      buttons.forEach((node) => { node.disabled = false; });
      button.removeAttribute('aria-busy');
    }
  };
  // Captura antes dos handlers do grid e das versões antigas em abas abertas.
  document.addEventListener('click', handleClick, true);
  window.hardnessRepresentanteCadastro = { version, dispose: () => {
    document.removeEventListener('click', handleClick, true);
    if (dialog && !sending) dialog.remove();
  } };
})();
