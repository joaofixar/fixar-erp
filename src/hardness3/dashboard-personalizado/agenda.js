(function () {
  'use strict';

  var active = null;
  var modal = document.getElementById('agendaModal');
  var content = document.getElementById('agendaNativeContent');
  var status = document.getElementById('agendaStatus');
  var closeButton = document.getElementById('closeAgendaModal');
  var saving = document.getElementById('agendaSaving');
  var savingMessage = document.getElementById('agendaSavingMessage');
  var agendaPath = '/crm/crm001/content/crm001contentEditarAgenda/';

  // Restringe somente a janela incorporada. O ERP aberto fora do dashboard
  // mantém a navegação e as etapas posteriores do cadastro original.
  function restrictNativeWindow(nativeWindow) {
    var nativeDocument = nativeWindow.document;
    var cleanups = [];
    function wrap(name, allow) {
      var original = nativeWindow[name];
      if (typeof original !== 'function') return;
      var replacement = function () {
        if (!allow.apply(this, arguments)) return false;
        return original.apply(this, arguments);
      };
      nativeWindow[name] = replacement;
      cleanups.push(function () { if (nativeWindow[name] === replacement) nativeWindow[name] = original; });
    }
    wrap('abrirJanela', function (event, root, parent, id, buttonTitle, title, href) {
      // Apenas a montagem inicial da própria janela é permitida.
      return id === 'oneWindow' && href === agendaPath;
    });
    wrap('nova_janela', function () { return false; });
    wrap('open', function () { return false; });
    wrap('dialogConfirm', function (title) {
      var normalized = String(title || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      return !(/\b(incluir|cadastrar|criar|gerar|novo|nova)\b/.test(normalized) && /\b(orcamento|tarefa|atividade)\b/.test(normalized));
    });
    var blocked = '.contentCabecalho, .contentCabecalhoLayout, .ui-dialog-titlebar, #crm001CadastroCliente, #crm001CadastroContato, #crm001OrcamentoAgenda, #crm001EnviarEmailAgenda';
    function stopNavigation(event) {
      if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') return;
      var target = event.target.nodeType === 1 ? event.target : event.target.parentElement;
      if (!target || !target.closest) return;
      var anchor = target.closest('a[href]');
      var href = anchor ? String(anchor.getAttribute('href') || '') : '';
      if (target.closest(blocked) || (href && href.charAt(0) !== '#')) {
        event.preventDefault();
        event.stopImmediatePropagation();
      }
    }
    ['click', 'dblclick', 'auxclick', 'keydown', 'contextmenu'].forEach(function (type) {
      nativeDocument.addEventListener(type, stopNavigation, true);
      cleanups.push(function () { nativeDocument.removeEventListener(type, stopNavigation, true); });
    });
    var style = nativeDocument.createElement('style');
    style.textContent = '.contentCabecalho, .contentCabecalho *, .contentCabecalhoLayout * { cursor:default !important; }'
      + '#crm001CadastroCliente, #crm001CadastroContato, #crm001OrcamentoAgenda, #crm001EnviarEmailAgenda { display:none !important; }'
      // No oneWindow, corrigeConteudo usa 100% para a aba dentro de um form sem
      // altura definida. Os campos absolutos não dão altura ao form e são cortados.
      // Divide o espaço entre cabeçalho, navegação e campos apenas neste iframe.
      + '#oneWindow > .content { display:flex; flex-direction:column; min-height:0; }'
      + '#oneWindow > .content > .contentCabecalho { flex:none; }'
      + '#oneWindow > .content > form.formFormulario { display:flex; flex:1; min-height:0; min-width:0; }'
      + '#oneWindow > .content > form.formFormulario > .tabs { display:flex; flex-direction:column; flex:1; min-height:0; min-width:0; height:auto !important; }'
      + '#oneWindow > .content > form.formFormulario > .tabs > .ui-tabs-nav { flex:none; }'
      + '#oneWindow > .content > form.formFormulario > .tabs > .ui-tabs-panel { flex:1; min-height:0; height:auto !important; }';
    nativeDocument.head.appendChild(style);
    cleanups.push(function () { style.remove(); });
    return function () { cleanups.forEach(function (cleanup) { cleanup(); }); };
  }

  function clearNativeBridge(current) {
    if (current.document) current.document.unbind('.dashboardAgenda');
    if (current.cleanup) current.cleanup();
    current.document = null;
    current.cleanup = null;
  }

  function validUrl(context) {
    var url = new URL(context.url, window.location.href);
    if (url.origin !== window.location.origin || url.pathname !== agendaPath ||
        url.searchParams.get('T002_Id') !== String(context.tituloId) || url.searchParams.get('D024_Id') !== String(context.clienteId) ||
        url.searchParams.get('oneWindow') !== 'true' ||
        Array.from(url.searchParams.keys()).some(function (key) { return ['T002_Id', 'D024_Id', 'oneWindow', 'tab'].indexOf(key) === -1; })) {
      throw new Error('Não foi possível validar o cadastro da agenda.');
    }
    return url.href;
  }

  function isAgendaSubmission(settings) {
    if (String(settings.type || settings.method || '').toUpperCase() !== 'POST') return false;
    var data = settings.data;
    if (typeof data === 'string') {
      var params = new URLSearchParams(data);
      return params.get('crm001FormAgenda[internal_sent]') === '1'
        || params.has('crm001FormAgenda[D085_Mensagem]') || params.has('D085_Mensagem');
    }
    if (!data || typeof data !== 'object') return false;
    var has = function (key) { return Object.prototype.hasOwnProperty.call(data, key); };
    return !!(has('D085_Mensagem') || has('crm001FormAgenda[D085_Mensagem]')
      || String(data['crm001FormAgenda[internal_sent]'] || '') === '1'
      || (data.crm001FormAgenda && (String(data.crm001FormAgenda.internal_sent || '') === '1'
        || Object.prototype.hasOwnProperty.call(data.crm001FormAgenda, 'D085_Mensagem'))));
  }

  function showSaving(current, message) {
    if (active !== current) return;
    current.saving = !!message;
    current.frame.inert = current.saving;
    if (saving) saving.hidden = !message;
    if (savingMessage && message) savingMessage.textContent = message;
    if (message) status.textContent = '';
    closeButton.disabled = current.pending > 0 || current.saving;
  }

  function refresh(current) {
    return current.refresh().then(function (changed) {
      if (active === current && current.submitted) {
        current.confirmed = current.confirmed || changed;
        if (current.pending > 0) return;
        if (current.confirmed) {
          close(false);
          document.getElementById('closeIndicatorDetailModal').focus();
          current.onSaved('Agenda incluída. Última ocorrência atualizada.');
        } else {
          current.submitted = false;
          showSaving(current, '');
          status.textContent = 'A inclusão ainda não foi confirmada. Confira os avisos do formulário.';
        }
      }
    }).catch(function (error) {
      var message = 'Não foi possível atualizar a última ocorrência. Reabra os títulos para conferir. ' + (error.message || '');
      if (active === current) {
        showSaving(current, '');
        status.textContent = message;
      }
      else current.onError(message);
    });
  }

  function close(update) {
    if (!active) return;
    var current = active;
    if (update !== false && (current.pending > 0 || current.saving)) return;
    active = null;
    clearTimeout(current.timer);
    clearNativeBridge(current);
    current.frame.remove();
    modal.hidden = true;
    if (saving) saving.hidden = true;
    var detail = document.getElementById('indicatorDetailModal');
    detail.inert = false;
    if (update !== false) {
      refresh(current);
      document.getElementById('closeIndicatorDetailModal').focus();
    }
  }

  function open(context, documentNumber, onRefresh, onError, onSaved) {
    var url = validUrl(context);
    close(false);
    var frame = document.createElement('iframe');
    var current = { frame: frame, refresh: onRefresh, onError: onError || function () {}, onSaved: onSaved || function () {}, document: null, cleanup: null, timer: null, pending: 0, submitted: false, confirmed: false, saving: false };
    active = current;
    document.getElementById('agendaTitle').textContent = 'Incluir agenda — título ' + documentNumber;
    status.textContent = '';
    modal.hidden = false;
    document.getElementById('indicatorDetailModal').inert = true;
    closeButton.disabled = false;
    if (saving) saving.hidden = false;
    if (savingMessage) savingMessage.textContent = 'Carregando agenda…';
    closeButton.focus();
    frame.title = 'Cadastro de agenda do título ' + documentNumber;
    frame.style.visibility = 'hidden';
    frame.addEventListener('load', function () {
      if (active !== current) return;
      try {
        var nativeWindow = frame.contentWindow;
        if (nativeWindow.location.origin !== window.location.origin || !nativeWindow.g ||
            Number(nativeWindow.g.usuarioAtual) !== Number(context.usuario) ||
            Number(nativeWindow.g.empresaAtual) !== Number(context.empresaSessao) || !nativeWindow.jQuery) {
          throw new Error('A sessão do ERP mudou ou expirou. Feche este cadastro e reabra o dashboard pelo sistema.');
        }
        // O cadastro nativo usa jQuery 1.6.2, anterior à API on/off.
        clearNativeBridge(current);
        current.cleanup = restrictNativeWindow(nativeWindow);
        current.document = nativeWindow.jQuery(nativeWindow.document);
        current.document.bind('ajaxSend.dashboardAgenda', function (event, xhr, settings) {
          if (active !== current || !settings || String(settings.url || '').indexOf('/crm/crm001/form/crm001FormEditarAgenda/') === -1) return;
          current.pending++;
          closeButton.disabled = true;
          if (isAgendaSubmission(settings)) {
            current.submitted = true;
            current.confirmed = false;
            showSaving(current, 'Salvando agenda…');
          }
        });
        current.document.bind('ajaxComplete.dashboardAgenda', function (event, xhr, settings) {
          if (active !== current || !settings || String(settings.url || '').indexOf('/crm/crm001/form/crm001FormEditarAgenda/') === -1) return;
          current.pending = Math.max(0, current.pending - 1);
          closeButton.disabled = current.pending > 0 || current.saving;
          clearTimeout(current.timer);
          if (current.pending > 0) return;
          if (current.submitted) showSaving(current, 'Atualizando os títulos do cliente…');
          // O retorno do formulário pode conter validação ou erro: só a releitura
          // do título confirma se uma nova ocorrência realmente foi gravada.
          current.timer = setTimeout(function () { if (active === current) refresh(current); }, 250);
        });
        frame.style.visibility = 'visible';
        if (!current.saving && saving) saving.hidden = true;
        status.textContent = '';
      } catch (error) {
        clearNativeBridge(current);
        frame.remove();
        current.pending = 0;
        showSaving(current, '');
        closeButton.disabled = false;
        status.textContent = error.message || 'Não foi possível abrir o cadastro original do ERP.';
      }
    });
    frame.src = url;
    content.appendChild(frame);
  }

  closeButton.addEventListener('click', function () { close(true); });
  window.dashboardAgenda = { open: open, close: close, isOpen: function () { return active !== null; } };
})();
