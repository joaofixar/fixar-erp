(function () {
  'use strict';

  var root = document.querySelector('.mcp-admin');
  var content = document.getElementById('mcp-admin-content');
  if (!root || !content || root.getAttribute('data-mcp-admin-bound') === '1') return;
  root.setAttribute('data-mcp-admin-bound', '1');

  var initialContent = content.innerHTML;
  var selectedConnectionFilter = 'active';
  var selectedConnectionSearch = '';
  var selectedDetailTab = 'overview';
  var pendingSuccessMessage = '';
  var activePermissionScopes = {};

  function selectNavigation(companyId) {
    Array.prototype.forEach.call(document.querySelectorAll('[data-company-id]'), function (item) {
      if (String(item.getAttribute('data-company-id')) === String(companyId)) item.setAttribute('aria-current', 'page');
      else item.removeAttribute('aria-current');
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-mcp-admin-home]'), function (item) {
      if (companyId === null) item.setAttribute('aria-current', 'page');
      else item.removeAttribute('aria-current');
    });
  }

  function showHome() {
    content.innerHTML = initialContent;
    selectNavigation(null);
    bindCompanyCards();
    bindHomeButtons();
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>'"]/g, function (character) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character];
    });
  }

  function usageLabel(value) { return value ? escapeHtml(value) : 'Nunca'; }
  function statusLabel(value) { return String(value).toLowerCase() === 'active' || String(value).toUpperCase() === 'S' ? 'Ativa' : 'Inativa'; }
  function statusClass(value) { return String(value).toLowerCase() === 'active' || String(value).toUpperCase() === 'S' ? 'mcp-admin-status--active' : 'mcp-admin-status--inactive'; }
  function userLabel(user) { return String((user && user.name) || 'Usuário') + ' (Id ' + Number((user && user.id) || 0) + ')'; }

  function requestCompany(companyId) {
    var url = root.getAttribute('data-company-detail-url');
    content.innerHTML = '<div class="mcp-admin-loading" role="status">Carregando conexões MCP…</div>';
    return fetch(url + '?ajax=true&C004_Id=' + encodeURIComponent(companyId), {
      credentials: 'same-origin',
      cache: 'no-store',
      headers: { 'Accept': 'application/json' }
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (data) {
        if (!response.ok) throw new Error(data.error || 'Não foi possível carregar a empresa.');
        return data;
      });
    });
  }

  function mutate(url, fields) {
    fields.csrf = root.getAttribute('data-csrf-token');
    var body = Object.keys(fields).map(function (key) { return encodeURIComponent(key) + '=' + encodeURIComponent(fields[key]); }).join('&');
    return fetch(url + '?ajax=true', { method: 'POST', credentials: 'same-origin', cache: 'no-store', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' }, body: body })
      .then(function (response) { return response.json().catch(function () { return {}; }).then(function (data) { if (!response.ok || !data.ok) throw new Error(data.error || 'Não foi possível concluir a operação.'); pendingSuccessMessage = 'Alteração salva com sucesso.'; return data; }); });
  }

  function renderSuccessNotice() {
    if (!pendingSuccessMessage) return;
    var message = pendingSuccessMessage; pendingSuccessMessage = '';
    var dialog = document.createElement('div');
    dialog.className = 'mcp-admin-success-dialog';
    dialog.innerHTML = '<div class="mcp-admin-success-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="mcp-admin-success-title"><div class="mcp-admin-success-dialog__icon" aria-hidden="true">✓</div><h2 id="mcp-admin-success-title">Operação concluída</h2><p></p><button type="button" class="mcp-admin-button mcp-admin-button--primary">Entendi</button></div>';
    dialog.querySelector('p').textContent = message;
    dialog.querySelector('button').addEventListener('click', function () { dialog.remove(); });
    document.body.appendChild(dialog);
    dialog.querySelector('button').focus();
  }

  function showOperationDialog(title, message, variant) {
    var dialog = document.createElement('div');
    dialog.className = 'mcp-admin-operation-dialog';
    dialog.innerHTML = '<div class="mcp-admin-operation-dialog__panel mcp-admin-operation-dialog__panel--' + (variant || 'error') + '" role="alertdialog" aria-modal="true" aria-labelledby="mcp-admin-operation-title"><div class="mcp-admin-operation-dialog__icon" aria-hidden="true"></div><div><h2 id="mcp-admin-operation-title"></h2><p></p></div><div class="mcp-admin-operation-dialog__actions"><button type="button" class="mcp-admin-button mcp-admin-button--primary">Entendi</button></div></div>';
    dialog.querySelector('h2').textContent = title;
    dialog.querySelector('p').textContent = message;
    dialog.querySelector('button').addEventListener('click', function () { dialog.remove(); });
    document.body.appendChild(dialog);
    dialog.querySelector('button').focus();
  }

  function confirmUnusedConnection(name, onConfirm) {
    var dialog = document.createElement('div');
    dialog.className = 'mcp-admin-operation-dialog';
    dialog.innerHTML = '<div class="mcp-admin-operation-dialog__panel mcp-admin-operation-dialog__panel--danger" role="dialog" aria-modal="true" aria-labelledby="mcp-admin-delete-title"><div class="mcp-admin-operation-dialog__icon" aria-hidden="true"></div><div><h2 id="mcp-admin-delete-title">Excluir conexão?</h2><p></p><small>Alterações administrativas, como criar ou ativar, não impedem a exclusão.</small></div><div class="mcp-admin-operation-dialog__actions"><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-cancel>Cancelar</button><button type="button" class="mcp-admin-button mcp-admin-button--danger" data-confirm>Excluir conexão</button></div></div>';
    dialog.querySelector('p').textContent = 'A conexão “' + name + '” será removida permanentemente. A exclusão só é permitida se não houver credencial, autorização OAuth ou uso MCP.';
    function close() { dialog.remove(); }
    dialog.querySelector('[data-cancel]').addEventListener('click', close);
    dialog.querySelector('[data-confirm]').addEventListener('click', function () { close(); onConfirm(); });
    document.body.appendChild(dialog);
    dialog.querySelector('[data-cancel]').focus();
  }

  function showOneTimeKey(key) {
    var modal = document.createElement('div');
    modal.className = 'mcp-admin-key-modal';
    modal.innerHTML = '<div class="mcp-admin-key-modal__panel" role="dialog" aria-modal="true" aria-label="Nova API key"><h2>Guarde esta API key agora</h2><p>Ela só será exibida uma vez e não poderá ser recuperada depois.</p><code></code><div><button type="button" data-copy>Copiar</button><button type="button" data-close>Fechar</button></div></div>';
    modal.querySelector('code').textContent = key;
    function close() { modal.remove(); }
    modal.querySelector('[data-close]').addEventListener('click', close);
    modal.querySelector('[data-copy]').addEventListener('click', function () { if (navigator.clipboard) navigator.clipboard.writeText(key); this.textContent = 'Copiada'; });
    document.body.appendChild(modal);
  }

  function showEntityPicker(title, items, emptyMessage, onSelect, label) {
    var modal = document.createElement('div');
    modal.className = 'mcp-admin-key-modal';
    modal.innerHTML = '<div class="mcp-admin-key-modal__panel mcp-admin-picker" role="dialog" aria-modal="true"><header><h2></h2><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-close>Fechar</button></header><input type="search" placeholder="Pesquisar por nome, login ou identificação" aria-label="Pesquisar"><div class="mcp-admin-picker__results"></div></div>';
    modal.querySelector('h2').textContent = title;
    var input = modal.querySelector('input'); var results = modal.querySelector('.mcp-admin-picker__results');
    function close() { modal.remove(); }
    function render(query) {
      var needle = String(query || '').toLocaleLowerCase();
      var filtered = items.filter(function (item) { return [item.name, item.login, item.id].join(' ').toLocaleLowerCase().indexOf(needle) !== -1; });
      results.innerHTML = filtered.length ? filtered.map(function (item) { return '<button type="button" data-id="' + Number(item.id) + '"><strong>' + escapeHtml(label ? label(item) : item.name) + '</strong><small>' + escapeHtml(item.login || ('Identificação #' + Number(item.id))) + '</small></button>'; }).join('') : '<p>' + escapeHtml(emptyMessage) + '</p>';
      Array.prototype.forEach.call(results.querySelectorAll('[data-id]'), function (button) { button.addEventListener('click', function () { onSelect(Number(button.getAttribute('data-id'))); close(); }); });
    }
    modal.querySelector('[data-close]').addEventListener('click', close); input.addEventListener('input', function () { render(input.value); }); render(''); document.body.appendChild(modal); input.focus();
  }

  function showProfileMembers(profile, members) {
    var modal = document.createElement('div'); modal.className = 'mcp-admin-key-modal';
    modal.innerHTML = '<div class="mcp-admin-key-modal__panel mcp-admin-picker" role="dialog" aria-modal="true"><header><h2></h2><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-close>Fechar</button></header><p>Os membros são administrados no cadastro de perfis do ERP.</p><div class="mcp-admin-picker__results"></div></div>';
    modal.querySelector('h2').textContent = profile.name;
    modal.querySelector('[data-close]').addEventListener('click', function () { modal.remove(); });
    modal.querySelector('.mcp-admin-picker__results').innerHTML = members.length ? members.map(function (member) { return '<div><strong>' + escapeHtml(userLabel(member)) + '</strong><small>' + escapeHtml(member.login || ('Identificação #' + Number(member.id))) + '</small></div>'; }).join('') : '<p>Nenhum membro ativo neste perfil.</p>';
    document.body.appendChild(modal);
  }

  function renderConnections(connections, companyId, credentialMap, scopeCatalog) {
    if (!connections.length) {
      return '<div class="mcp-admin-empty"><strong>Nenhuma conexão MCP cadastrada.</strong><p>As conexões desta empresa aparecerão aqui quando forem criadas.</p></div>';
    }
    return '<div class="mcp-admin-connection-list">' + connections.map(function (connection) {
      return '<article class="mcp-admin-connection-card" data-connection-filter-status="' + (connection.status === 'active' ? 'active' : 'inactive') + '"><div><h3>' + escapeHtml(connection.name) + '</h3><p class="mcp-admin-connection-card__scopes">Permissões configuradas: ' + Number((connection.scopes || []).length) + '</p></div><div class="mcp-admin-connection-card__actions"><span class="mcp-admin-status ' + statusClass(connection.status) + '">' + escapeHtml(statusLabel(connection.status)) + '</span><div class="mcp-admin-action-group"><button type="button" class="mcp-admin-button mcp-admin-button--manage" data-connection-manage="' + Number(connection.id) + '"><span>Gerenciar</span><b aria-hidden="true">→</b></button><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-connection-id="' + Number(connection.id) + '" data-connection-status="' + (connection.status === 'active' ? 'disabled' : 'active') + '">' + (connection.status === 'active' ? 'Desativar' : 'Ativar') + '</button></div></div><dl><div><dt>Último uso</dt><dd>' + usageLabel(connection.last_usage_at) + '</dd></div><div><dt>Permissões</dt><dd>' + Number((connection.scopes || []).length) + '</dd></div></dl></article>';
      /* Legacy expanded rendering intentionally removed from the main list. */
      /*
      var scopes = connection.scopes && connection.scopes.length ? connection.scopes.map(escapeHtml).join(', ') : 'Sem scopes';
      var credentials = credentialMap[String(connection.id)] || [];
      var permissionRows = (scopeCatalog || []).map(function (scope) {
        var checked = (connection.scopes || []).indexOf(scope.id) !== -1 ? ' checked' : '';
        return '<label class="mcp-admin-scope"><input type="checkbox" data-connection-scope="' + Number(connection.id) + '" value="' + escapeHtml(scope.id) + '"' + checked + '><span><strong>' + escapeHtml(scope.name) + '</strong><small>' + escapeHtml(scope.description) + ' · ' + escapeHtml((scope.tools || []).join(', ')) + '</small></span></label>';
      }).join('');
      var active = credentials.filter(function (credential) { return credential.status === 'active'; });
      var rotationPending = credentials.some(function (credential) { return credential.rotation_pending; });
      var credentialRows = credentials.map(function (credential) { return '<li><strong>' + escapeHtml(credential.public_id) + '</strong> · ' + escapeHtml(credential.status) + (credential.rotation_pending ? ' · rotação pendente até ' + escapeHtml(credential.grace_expires_at || credential.expires_at || '') : '') + (!credential.rotation_pending && credential.expires_at ? ' · expira ' + escapeHtml(credential.expires_at) : '') + (credential.status === 'active' ? ' <button type="button" data-credential-revoke="' + Number(credential.id) + '" data-connection-id="' + Number(connection.id) + '">Revogar</button>' : '') + '</li>'; }).join('');
      var credentialAction = connection.status !== 'active' ? '<span class="mcp-admin-credential-pending">Ative a conexão para gerar credenciais.</span>' : (active.length === 0 ? '<button type="button" data-credential-generate="' + Number(connection.id) + '">Gerar credencial</button>' : (rotationPending ? '<span class="mcp-admin-credential-pending">Rotação em transição</span>' : '<button type="button" data-credential-rotate="' + Number(connection.id) + '">Rotacionar</button>'));
      return '<article class="mcp-admin-connection-card" data-connection-filter-status="' + (connection.status === 'active' ? 'active' : 'inactive') + '">'
        + '<div><h3>' + escapeHtml(connection.name) + '</h3><p class="mcp-admin-connection-card__scopes">' + scopes + '</p></div>'
        + '<div class="mcp-admin-connection-card__actions"><span class="mcp-admin-status ' + statusClass(connection.status) + '">' + escapeHtml(statusLabel(connection.status)) + '</span><button type="button" class="mcp-admin-status-action" data-connection-id="' + Number(connection.id) + '" data-connection-status="' + (connection.status === 'active' ? 'disabled' : 'active') + '">' + (connection.status === 'active' ? 'Desativar' : 'Ativar') + '</button></div>'
        + '<dl><div><dt>Credenciais</dt><dd>' + Number(connection.credential_count || 0) + '</dd></div>'
        + '<div><dt>Ativas</dt><dd>' + Number(connection.active_credential_count || 0) + '</dd></div>'
        + '<div><dt>Último uso</dt><dd>' + usageLabel(connection.last_usage_at) + '</dd></div></dl><section class="mcp-admin-permissions"><header><strong>Permissões</strong><button type="button" data-connection-scopes-save="' + Number(connection.id) + '">Salvar permissões</button></header>' + permissionRows + '</section><section class="mcp-admin-credentials"><header><strong>Credenciais</strong>' + credentialAction + '</header><ul>' + (credentialRows || '<li>Nenhuma credencial gerada.</li>') + '</ul></section>'
        + '</article>'; */
    }).join('') + '</div>';
  }

  function renderCompany(data, companyId) {
    if (!data || !data.company || typeof data.company !== 'object' || !Array.isArray(data.connections)) {
      throw new Error('A resposta da empresa não pôde ser carregada.');
    }
    var company = data.company;
    var name = company.short_name || company.legal_name;
    content.innerHTML = '<button type="button" class="mcp-admin-back" id="mcp-admin-back">← Voltar para empresas</button>'
      + '<section class="mcp-admin-company-detail"><header><div><p class="mcp-admin__eyebrow">EMPRESA SELECIONADA</p><h2>' + escapeHtml(name) + '</h2><p>' + escapeHtml(company.legal_name) + '</p></div><span class="mcp-admin-status ' + statusClass(company.status) + '">' + escapeHtml(statusLabel(company.status)) + '</span></header>'
      + '<dl class="mcp-admin-summary"><div><dt>Documento</dt><dd>' + escapeHtml(company.document || 'Não informado') + '</dd></div><div><dt>Conexões</dt><dd>' + Number(company.connection_count || 0) + '</dd></div><div><dt>Conexões ativas</dt><dd>' + Number(company.active_connection_count || 0) + '</dd></div><div><dt>Último uso MCP</dt><dd>' + usageLabel(company.last_mcp_usage) + '</dd></div></dl></section>'
      /* MVP: Credenciais e Auditoria permanecem disponíveis no código e nas rotas,
         mas não são exibidas nesta visão enquanto não houver fluxo de usuário. */
      + '<section class="mcp-admin-connections"><header><div><h2>Conexões</h2><p>Administre permissões e credenciais de cada conexão.</p></div><button type="button" class="mcp-admin-create" id="mcp-admin-create">Nova conexão</button></header><div class="mcp-admin-connection-toolbar"><div class="mcp-admin-filters" role="group" aria-label="Filtrar conexões"><button type="button" data-connection-filter="all">Todas</button><button type="button" class="active" data-connection-filter="active">Ativas</button><button type="button" data-connection-filter="inactive">Inativas</button></div><label class="mcp-admin-filter-search"><span>Buscar conexão</span><input type="search" id="mcp-admin-connection-search" value="' + escapeHtml(selectedConnectionSearch) + '" placeholder="Digite o nome" autocomplete="off"></label></div>' + renderConnections(data.connections || [], companyId, data.credentials || {}, data.scope_catalog || []) + '</section>';
    renderSuccessNotice();
    selectNavigation(companyId);
    document.getElementById('mcp-admin-back').addEventListener('click', showHome);
    document.getElementById('mcp-admin-create').addEventListener('click', function () {
      var name = window.prompt('Nome da nova conexão MCP:');
      if (name === null) return;
      mutate(root.getAttribute('data-connection-create-url'), { C004_Id: companyId, nome: name }).then(function () { return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(renderError);
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-id]'), function (button) {
      button.addEventListener('click', function () {
        mutate(root.getAttribute('data-connection-status-url'), { C004_Id: companyId, MCP001_Id: button.getAttribute('data-connection-id'), status: button.getAttribute('data-connection-status') }).then(function () { return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(renderError);
      });
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-manage]'), function (button) {
      button.addEventListener('click', function () { selectedDetailTab = 'overview'; requestConnection(companyId, button.getAttribute('data-connection-manage')).then(renderConnectionDetail).catch(renderError); });
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-filter]'), function (button) {
      button.addEventListener('click', function () {
        var filter = button.getAttribute('data-connection-filter');
        applyConnectionFilter(filter);
      });
    });
    document.getElementById('mcp-admin-connection-search').addEventListener('input', function () {
      selectedConnectionSearch = this.value;
      applyConnectionFilter(selectedConnectionFilter);
    });
    applyConnectionFilter(selectedConnectionFilter);
    Array.prototype.forEach.call(content.querySelectorAll('[data-credential-generate], [data-credential-rotate]'), function (button) {
      button.addEventListener('click', function () {
        var rotating = button.hasAttribute('data-credential-rotate');
        if (rotating && !window.confirm('Rotacionar a credencial? A anterior ficará válida apenas durante o período de transição.')) return;
        var id = button.getAttribute(rotating ? 'data-credential-rotate' : 'data-credential-generate');
        mutate(root.getAttribute(rotating ? 'data-credential-rotate-url' : 'data-credential-generate-url'), { C004_Id: companyId, MCP001_Id: id }).then(function (result) { showOneTimeKey(result.api_key); return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(renderOperationError);
      });
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-credential-revoke]'), function (button) {
      button.addEventListener('click', function () { var reason = window.prompt('Motivo da revogação (obrigatório):'); if (!reason || !window.confirm('Revogar esta credencial imediatamente?')) return; mutate(root.getAttribute('data-credential-revoke-url'), { C004_Id: companyId, MCP001_Id: button.getAttribute('data-connection-id'), MCP002_Id: button.getAttribute('data-credential-revoke'), reason: reason }).then(function () { return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(renderOperationError); });
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-scopes-save]'), function (button) {
      button.addEventListener('click', function () {
        var id = button.getAttribute('data-connection-scopes-save');
        var scopes = Array.prototype.map.call(content.querySelectorAll('[data-connection-scope="' + id + '"]:checked'), function (input) { return input.value; });
        mutate(root.getAttribute('data-connection-scopes-url'), { C004_Id: companyId, MCP001_Id: id, scopes: JSON.stringify(scopes) }).then(function () { return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(renderOperationError);
      });
    });
  }

  function requestConnection(companyId, connectionId, auditOffset) {
    var url = root.getAttribute('data-connection-detail-url') + '?ajax=true&C004_Id=' + encodeURIComponent(companyId) + '&MCP001_Id=' + encodeURIComponent(connectionId);
    if (typeof auditOffset === 'number' && auditOffset > 0) url += '&audit_offset=' + encodeURIComponent(auditOffset);
    return fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } }).then(function (response) { return response.json().then(function (data) { if (!response.ok || !data.ok) throw new Error('connection unavailable'); return data; }); });
  }

  function auditEventLabel(event) {
    var labels = { tools_list: 'Ferramentas consultadas', tools_call_success: 'Ferramenta executada', tools_list_invalid_params: 'Consulta de ferramentas inválida', tools_call_not_found: 'Ferramenta não encontrada', tools_call_invalid_params: 'Solicitação inválida', request: 'Notificação recebida', connection_enabled: 'Conexão ativada', connection_disabled: 'Conexão desativada', connection_revoked: 'Conexão revogada', mcp_eligibility_user_added: 'Usuário liberado', mcp_eligibility_user_revoked: 'Acesso de usuário removido', mcp_eligibility_profile_added: 'Perfil liberado', mcp_eligibility_profile_revoked: 'Acesso de perfil removido', mcp_eligibility_all_users_enabled: 'Todos os usuários liberados', mcp_eligibility_all_users_disabled: 'Acesso geral removido' };
    return labels[event] || String(event || '').replace(/_/g, ' ');
  }

  function auditKind(event) {
    if (String(event).indexOf('tools_') === 0) return 'tool';
    if (String(event).indexOf('oauth_') === 0) return 'oauth';
    if (event === 'request') return 'notification';
    return 'administrative';
  }

  function renderAuditRows(items) {
    return items.map(function (item) {
      var kind = auditKind(item.event); var status = Number(item.http_status); var statusType = status >= 400 ? 'error' : 'success';
      var actor = item.actor_name || (item.actor_user_id ? 'Usuário #' + Number(item.actor_user_id) : 'Sistema');
      var actorLogin = item.actor_login ? '<small>' + escapeHtml(item.actor_login) + '</small>' : '';
      var client = item.client_name ? '<div><span>Aplicativo</span><strong>' + escapeHtml(item.client_name) + '</strong></div>' : '';
      var tool = item.safe_code ? '<div><span>Ferramenta</span><strong>' + escapeHtml(item.safe_code) + '</strong></div>' : '';
      return '<li class="mcp-admin-audit-row" data-audit-kind="' + kind + '" data-audit-status="' + statusType + '" data-audit-http="' + status + '" data-audit-date="' + escapeHtml(item.created_at) + '" data-audit-actor="' + escapeHtml(actor) + '" data-audit-tool="' + escapeHtml(item.safe_code || '') + '"><div class="mcp-admin-audit-row__header"><div><strong>' + escapeHtml(auditEventLabel(item.event)) + '</strong><time datetime="' + escapeHtml(item.created_at) + '">' + escapeHtml(item.created_at) + '</time></div><span class="mcp-admin-audit-status mcp-admin-audit-status--' + statusType + '">HTTP ' + status + '</span></div><div class="mcp-admin-audit-row__details"><div><span>Usuário</span><strong>' + escapeHtml(actor) + '</strong>' + actorLogin + '</div>' + client + tool + '<div><span>Duração</span><strong>' + Number(item.duration_ms) + ' ms</strong></div></div><details><summary>Detalhes técnicos</summary><span>Identificador da requisição: ' + escapeHtml(item.request_id) + '</span></details></li>';
    }).join('');
  }

  function renderAudit(items, pagination) {
    if (!items.length) return '<p class="mcp-admin-empty-row">Nenhuma atividade registrada nesta conexão.</p>';
    var loadMore = pagination && pagination.has_more ? '<button type="button" class="mcp-admin-button mcp-admin-button--secondary mcp-admin-audit-more" id="mcp-admin-audit-more" data-audit-offset="' + Number(pagination.next_offset || items.length) + '">Carregar 50 atividades anteriores</button>' : '';
    return '<p class="mcp-admin-audit-limit">São exibidas 50 atividades por vez. Carregue as anteriores quando precisar consultar o histórico completo.</p><div class="mcp-admin-audit-toolbar"><div class="mcp-admin-filters" role="group" aria-label="Filtros rápidos da auditoria"><button type="button" class="active" data-audit-quick="all">Todos</button><button type="button" data-audit-quick="success">Sucesso</button><button type="button" data-audit-quick="error">Erros</button><button type="button" data-audit-quick="oauth">OAuth</button><button type="button" data-audit-quick="tool">Tools</button><button type="button" data-audit-quick="administrative">Administração</button></div><label class="mcp-admin-filter-search"><span>Buscar evento, usuário, tool ou request ID</span><input type="search" id="mcp-admin-audit-search" placeholder="Digite para buscar" autocomplete="off"></label><label><span>Período</span><select id="mcp-admin-audit-period"><option value="all">Todo o período</option><option value="today">Hoje</option><option value="7">Últimos 7 dias</option><option value="30">Últimos 30 dias</option></select></label><label><span>Status HTTP</span><select id="mcp-admin-audit-http"><option value="">Todos</option></select></label><label><span>Usuário</span><select id="mcp-admin-audit-actor"><option value="">Todos</option></select></label><label><span>Tool</span><select id="mcp-admin-audit-tool"><option value="">Todas</option></select></label><button type="button" class="mcp-admin-filter-clear" id="mcp-admin-audit-clear">Limpar</button><output id="mcp-admin-audit-count"></output></div><ul class="mcp-admin-audit-list">' + renderAuditRows(items) + '</ul>' + loadMore;
  }

  function bindAuditFilters() {
    var search = document.getElementById('mcp-admin-audit-search'); var period = document.getElementById('mcp-admin-audit-period'); var http = document.getElementById('mcp-admin-audit-http'); var actor = document.getElementById('mcp-admin-audit-actor'); var tool = document.getElementById('mcp-admin-audit-tool'); var count = document.getElementById('mcp-admin-audit-count'); var clear = document.getElementById('mcp-admin-audit-clear'); var quick = 'all';
    if (!search || !period || !http || !actor || !tool || !count) return;
    function addOptions(select, attribute) { var values = {}; Array.prototype.forEach.call(content.querySelectorAll('.mcp-admin-audit-row'), function (row) { var value = row.getAttribute(attribute) || ''; if (value) values[value] = true; }); Object.keys(values).sort().forEach(function (value) { select.insertAdjacentHTML('beforeend', '<option value="' + escapeHtml(value) + '">' + escapeHtml(value) + '</option>'); }); }
    addOptions(http, 'data-audit-http'); addOptions(actor, 'data-audit-actor'); addOptions(tool, 'data-audit-tool');
    function inPeriod(value) { if (period.value === 'all') return true; var date = new Date(String(value).replace(' ', 'T')); if (isNaN(date.getTime())) return true; var now = new Date(); if (period.value === 'today') return date.toDateString() === now.toDateString(); return date.getTime() >= now.getTime() - Number(period.value) * 86400000; }
    function apply() { var visible = 0; var needle = search.value.toLocaleLowerCase(); Array.prototype.forEach.call(content.querySelectorAll('.mcp-admin-audit-row'), function (row) { var type = row.getAttribute('data-audit-kind'); var status = row.getAttribute('data-audit-status'); var quickMatches = quick === 'all' || (quick === 'success' && status === 'success') || (quick === 'error' && status === 'error') || type === quick; var show = quickMatches && (!needle || row.textContent.toLocaleLowerCase().indexOf(needle) !== -1) && (!http.value || row.getAttribute('data-audit-http') === http.value) && (!actor.value || row.getAttribute('data-audit-actor') === actor.value) && (!tool.value || row.getAttribute('data-audit-tool') === tool.value) && inPeriod(row.getAttribute('data-audit-date')); row.hidden = !show; if (show) visible += 1; }); count.textContent = visible + (visible === 1 ? ' evento exibido' : ' eventos exibidos'); }
    Array.prototype.forEach.call(content.querySelectorAll('[data-audit-quick]'), function (button) { button.setAttribute('aria-pressed', button.getAttribute('data-audit-quick') === 'all' ? 'true' : 'false'); button.addEventListener('click', function () { quick = button.getAttribute('data-audit-quick'); Array.prototype.forEach.call(content.querySelectorAll('[data-audit-quick]'), function (item) { var selected = item === button; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); }); });
    search.addEventListener('input', apply); period.addEventListener('change', apply); http.addEventListener('change', apply); actor.addEventListener('change', apply); tool.addEventListener('change', apply);
    if (clear) clear.addEventListener('click', function () { quick = 'all'; search.value = ''; period.value = 'all'; http.value = ''; actor.value = ''; tool.value = ''; Array.prototype.forEach.call(content.querySelectorAll('[data-audit-quick]'), function (item) { var selected = item.getAttribute('data-audit-quick') === 'all'; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); });
    apply();
  }

  function renderListFilter(id, label, placeholder) {
    return '<div class="mcp-admin-list-toolbar"><label class="mcp-admin-filter-search"><span>' + escapeHtml(label) + '</span><input type="search" id="' + escapeHtml(id) + '" placeholder="' + escapeHtml(placeholder) + '" autocomplete="off"></label><button type="button" class="mcp-admin-filter-clear" data-list-filter-clear="' + escapeHtml(id) + '">Limpar</button><output data-list-filter-count="' + escapeHtml(id) + '"></output></div>';
  }

  function bindListFilter(inputId, list) {
    var input = document.getElementById(inputId); var clear = content.querySelector('[data-list-filter-clear="' + inputId + '"]'); var count = content.querySelector('[data-list-filter-count="' + inputId + '"]');
    if (!input || !list || !count) return;
    function apply() { var visible = 0; var needle = input.value.toLocaleLowerCase(); Array.prototype.forEach.call(list.querySelectorAll('li:not(.mcp-admin-empty-row)'), function (item) { var show = !needle || item.textContent.toLocaleLowerCase().indexOf(needle) !== -1; item.hidden = !show; if (show) visible += 1; }); count.textContent = visible + (visible === 1 ? ' item exibido' : ' itens exibidos'); }
    input.addEventListener('input', apply); if (clear) clear.addEventListener('click', function () { input.value = ''; apply(); input.focus(); }); apply();
  }

  function bindAccessFilters() {
    var profileList = content.querySelector('[data-access-panel="profiles"] .mcp-admin-access-list');
    var userList = content.querySelector('[data-access-panel="users"] .mcp-admin-access-list');
    if (profileList) { profileList.insertAdjacentHTML('beforebegin', renderListFilter('mcp-admin-profile-search', 'Buscar perfil', 'Nome do perfil')); bindListFilter('mcp-admin-profile-search', profileList); }
    if (userList) { userList.insertAdjacentHTML('beforebegin', renderListFilter('mcp-admin-access-user-search', 'Buscar usuário', 'Nome ou login')); bindListFilter('mcp-admin-access-user-search', userList); }
  }

  function bindOAuthFilters() {
    var list = content.querySelector('.mcp-admin-oauth-grants'); if (!list || list.querySelector('.mcp-admin-empty-row')) return;
    list.insertAdjacentHTML('beforebegin', '<div class="mcp-admin-oauth-toolbar"><div class="mcp-admin-filters" role="group" aria-label="Filtrar aplicativos autorizados"><button type="button" data-oauth-filter="all">Todos</button><button type="button" class="active" data-oauth-filter="active">Ativos</button><button type="button" data-oauth-filter="revoked">Revogados</button></div>' + renderListFilter('mcp-admin-oauth-search', 'Buscar aplicativo ou usuário', 'Aplicativo ou usuário') + '</div>');
    Array.prototype.forEach.call(list.querySelectorAll('.mcp-admin-oauth-grant'), function (row) { var status = row.querySelector('.mcp-admin-oauth-grant__details div:last-child strong'); row.setAttribute('data-oauth-status', status && status.textContent.toLocaleLowerCase() === 'ativa' ? 'active' : 'revoked'); });
    var active = 'active'; var search = document.getElementById('mcp-admin-oauth-search'); var clear = content.querySelector('[data-list-filter-clear="mcp-admin-oauth-search"]'); var count = content.querySelector('[data-list-filter-count="mcp-admin-oauth-search"]');
    function apply() { var visible = 0; var needle = search.value.toLocaleLowerCase(); Array.prototype.forEach.call(list.querySelectorAll('.mcp-admin-oauth-grant'), function (row) { var show = (active === 'all' || row.getAttribute('data-oauth-status') === active) && (!needle || row.textContent.toLocaleLowerCase().indexOf(needle) !== -1); row.hidden = !show; if (show) visible += 1; }); count.textContent = visible + (visible === 1 ? ' aplicativo exibido' : ' aplicativos exibidos'); }
    Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-filter]'), function (button) { button.setAttribute('aria-pressed', button.getAttribute('data-oauth-filter') === 'active' ? 'true' : 'false'); button.addEventListener('click', function () { active = button.getAttribute('data-oauth-filter'); Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-filter]'), function (item) { var selected = item === button; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); }); });
    search.addEventListener('input', apply); if (clear) clear.addEventListener('click', function () { search.value = ''; active = 'active'; Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-filter]'), function (item) { var selected = item.getAttribute('data-oauth-filter') === 'active'; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); search.focus(); }); apply();
  }

  function bindFilterDelegation() {
    function filterAccess(input, list, count) { var needle = input.value.toLocaleLowerCase(); var visible = 0; Array.prototype.forEach.call(list.querySelectorAll('li:not(.mcp-admin-empty-row)'), function (row) { var show = !needle || row.textContent.toLocaleLowerCase().indexOf(needle) !== -1; row.hidden = !show; if (show) visible += 1; }); if (count) count.textContent = visible + (visible === 1 ? ' item exibido' : ' itens exibidos'); }
    function filterOAuth() { var list = content.querySelector('.mcp-admin-oauth-grants'); var search = document.getElementById('mcp-admin-oauth-search'); if (!list || !search) return; var active = (content.querySelector('[data-oauth-filter].active') || {}).getAttribute('data-oauth-filter') || 'active'; var needle = search.value.toLocaleLowerCase(); var visible = 0; Array.prototype.forEach.call(list.querySelectorAll('.mcp-admin-oauth-grant'), function (row) { var show = (active === 'all' || row.getAttribute('data-oauth-status') === active) && (!needle || row.textContent.toLocaleLowerCase().indexOf(needle) !== -1); row.hidden = !show; if (show) visible += 1; }); var count = content.querySelector('[data-list-filter-count="mcp-admin-oauth-search"]'); if (count) count.textContent = visible + (visible === 1 ? ' aplicativo exibido' : ' aplicativos exibidos'); }
    function filterAudit() { var search = document.getElementById('mcp-admin-audit-search'); var period = document.getElementById('mcp-admin-audit-period'); var http = document.getElementById('mcp-admin-audit-http'); var actor = document.getElementById('mcp-admin-audit-actor'); var tool = document.getElementById('mcp-admin-audit-tool'); var count = document.getElementById('mcp-admin-audit-count'); if (!search || !period || !http || !actor || !tool || !count) return; var quick = (content.querySelector('[data-audit-quick].active') || {}).getAttribute('data-audit-quick') || 'all'; var needle = search.value.toLocaleLowerCase(); var now = new Date(); var visible = 0; Array.prototype.forEach.call(content.querySelectorAll('.mcp-admin-audit-row'), function (row) { var kind = row.getAttribute('data-audit-kind'); var status = row.getAttribute('data-audit-status'); var date = new Date(String(row.getAttribute('data-audit-date') || '').replace(' ', 'T')); var periodMatches = period.value === 'all' || isNaN(date.getTime()) || (period.value === 'today' ? date.toDateString() === now.toDateString() : date.getTime() >= now.getTime() - Number(period.value) * 86400000); var quickMatches = quick === 'all' || (quick === 'success' && status === 'success') || (quick === 'error' && status === 'error') || kind === quick; var show = quickMatches && periodMatches && (!needle || row.textContent.toLocaleLowerCase().indexOf(needle) !== -1) && (!http.value || row.getAttribute('data-audit-http') === http.value) && (!actor.value || row.getAttribute('data-audit-actor') === actor.value) && (!tool.value || row.getAttribute('data-audit-tool') === tool.value); row.hidden = !show; if (show) visible += 1; }); count.textContent = visible + (visible === 1 ? ' evento exibido' : ' eventos exibidos'); }
    content.addEventListener('input', function (event) { var target = event.target; if (target.id === 'mcp-admin-profile-search' || target.id === 'mcp-admin-access-user-search') { var list = target.closest('.mcp-admin-access-card').querySelector('.mcp-admin-access-list'); filterAccess(target, list, content.querySelector('[data-list-filter-count="' + target.id + '"]')); } else if (target.id === 'mcp-admin-oauth-search') filterOAuth(); else if (target.id === 'mcp-admin-audit-search') filterAudit(); });
    content.addEventListener('change', function (event) { if (event.target.closest('.mcp-admin-audit-toolbar')) filterAudit(); });
    content.addEventListener('click', function (event) { var oauth = event.target.closest('[data-oauth-filter]'); var audit = event.target.closest('[data-audit-quick]'); if (oauth) { Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-filter]'), function (item) { var selected = item === oauth; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); filterOAuth(); } if (audit) { Array.prototype.forEach.call(content.querySelectorAll('[data-audit-quick]'), function (item) { var selected = item === audit; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); filterAudit(); } });
  }

  function permissionPresentation(scope) {
    var tools = (scope.tools || []).join(' ').toLocaleLowerCase();
    if (tools.indexOf('delete') !== -1) return { label: 'Exclusão', className: 'danger', icon: 'trash' };
    if (tools.indexOf('.update') !== -1) return { label: 'Alteração', className: 'write', icon: 'edit' };
    if (tools.indexOf('.create') !== -1) return { label: 'Criação', className: 'write', icon: 'plus' };
    if (tools.indexOf('.add') !== -1) return { label: 'Inclusão', className: 'write', icon: 'plus' };
    return { label: 'Consulta', className: 'read', icon: 'search' };
  }

  function permissionCategoryIcon(group) {
    return ({ 'Empresa': 'building', 'Clientes e fornecedores': 'users', 'Produtos': 'box', 'Orçamentos': 'file' })[group] || 'key';
  }

  function showPermissionDetails(scope) {
    var presentation = permissionPresentation(scope); var admin = scope.admin_presentation || {};
    var modal = document.createElement('div'); var previouslyFocused = document.activeElement;
    modal.className = 'mcp-admin-key-modal mcp-admin-permission-modal';
    modal.innerHTML = '<div class="mcp-admin-key-modal__panel mcp-admin-permission-modal__panel" role="dialog" aria-modal="true" aria-labelledby="mcp-admin-permission-details-title"><header><div><span class="mcp-admin-permission-icon mcp-admin-permission-icon--' + presentation.icon + '" aria-hidden="true"></span><div><h2 id="mcp-admin-permission-details-title"></h2><p class="mcp-admin-permission-modal__description"></p></div></div><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-close aria-label="Fechar detalhes">Fechar <span aria-hidden="true">×</span></button></header><section class="mcp-admin-permission-modal__explanation"><h3>O que esta permissão permite</h3><p data-permission-allows></p></section><section class="mcp-admin-permission-modal__limitations" data-permission-limitations hidden><h3>Limitações</h3><ul></ul></section><p class="mcp-admin-permission-modal__recommended" data-permission-recommended hidden></p><div class="mcp-admin-permission-modal__badges"><span class="mcp-admin-scope__badge mcp-admin-scope__badge--' + presentation.className + '">' + escapeHtml(presentation.label) + '</span><span class="mcp-admin-scope__badge mcp-admin-scope__badge--actor">' + (scope.requires_erp_actor ? 'Requer usuário ERP' : 'Não requer usuário ERP') + '</span></div><details class="mcp-admin-permission-modal__technical"><summary>Informações técnicas</summary><dl><div><dt>Categoria</dt><dd data-permission-category></dd></div><div><dt>Scope técnico</dt><dd><code data-permission-scope></code></dd></div></dl><section><h3>Tools relacionadas</h3><ul data-permission-tools></ul></section></details></div>';
    modal.querySelector('h2').textContent = scope.name || 'Permissão';
    modal.querySelector('.mcp-admin-permission-modal__description').textContent = admin.summary || scope.description || 'Sem descrição disponível.';
    modal.querySelector('[data-permission-allows]').textContent = admin.allows || scope.description || 'Sem explicação administrativa disponível.';
    var limitations = admin.limitations || []; var limitationsSection = modal.querySelector('[data-permission-limitations]'); if (limitations.length) { limitationsSection.hidden = false; limitationsSection.querySelector('ul').innerHTML = limitations.map(function (item) { return '<li>' + escapeHtml(item) + '</li>'; }).join(''); }
    if (admin.recommended_use) { var recommended = modal.querySelector('[data-permission-recommended]'); recommended.hidden = false; recommended.textContent = admin.recommended_use; }
    if (admin.usage) modal.querySelector('.mcp-admin-permission-modal__badges').insertAdjacentHTML('beforeend', '<span class="mcp-admin-scope__badge mcp-admin-scope__badge--usage">' + (admin.usage === 'internal' ? 'Uso interno' : 'Integração externa') + '</span>');
    modal.querySelector('[data-permission-category]').textContent = [scope.group, scope.subgroup].filter(Boolean).join(' · ') || 'Outras permissões'; modal.querySelector('[data-permission-scope]').textContent = scope.id || '';
    modal.querySelector('[data-permission-tools]').innerHTML = (scope.tools || []).map(function (tool) { return '<li><code>' + escapeHtml(tool) + '</code></li>'; }).join('') || '<li>Nenhuma Tool relacionada.</li>';
    function close() { modal.remove(); if (previouslyFocused && previouslyFocused.focus) previouslyFocused.focus(); }
    modal.querySelector('[data-close]').addEventListener('click', close);
    modal.addEventListener('click', function (event) { if (event.target === modal) close(); });
    modal.addEventListener('keydown', function (event) { if (event.key === 'Escape') close(); });
    document.body.appendChild(modal); modal.querySelector('[data-close]').focus();
  }

  function renderPermissionGroups(scopeCatalog, selectedScopes) {
    var groups = {};
    activePermissionScopes = {};
    (scopeCatalog || []).forEach(function (scope) {
      activePermissionScopes[scope.id] = scope;
      var name = scope.group || 'Outras permissões';
      if (!groups[name]) groups[name] = { name: name, order: Number(scope.order || 9999), scopes: [] };
      groups[name].order = Math.min(groups[name].order, Number(scope.order || 9999));
      groups[name].scopes.push(scope);
    });
    var orderedGroups = Object.keys(groups).map(function (name) { return groups[name]; }).sort(function (a, b) { return a.order - b.order || a.name.localeCompare(b.name); });
    var categories = orderedGroups.map(function (group) { return '<option value="' + escapeHtml(group.name) + '">' + escapeHtml(group.name) + '</option>'; }).join('');
    var items = orderedGroups.map(function (group) {
      group.scopes.sort(function (a, b) { return Number(a.order || 9999) - Number(b.order || 9999) || String(a.name).localeCompare(String(b.name)); });
      var groupId = 'mcp-admin-permission-group-' + group.name.replace(/[^a-z0-9]+/gi, '-').toLowerCase();
      return '<section class="mcp-admin-permission-group" data-permission-group="' + escapeHtml(group.name) + '" aria-labelledby="' + escapeHtml(groupId) + '"><h4 id="' + escapeHtml(groupId) + '"><span class="mcp-admin-permission-icon mcp-admin-permission-icon--' + permissionCategoryIcon(group.name) + '" aria-hidden="true"></span>' + escapeHtml(group.name) + '</h4><div class="mcp-admin-permission-group__items">' + group.scopes.map(function (scope) { var p = permissionPresentation(scope); var inputId = 'mcp-admin-scope-' + String(scope.id).replace(/[^a-z0-9]+/gi, '-'); var summary = ((scope.admin_presentation || {}).summary) || scope.description || ''; return '<article class="mcp-admin-scope" data-permission-card><input id="' + escapeHtml(inputId) + '" type="checkbox" aria-label="' + escapeHtml(scope.name) + '" data-detail-scope value="' + escapeHtml(scope.id) + '"' + ((selectedScopes || []).indexOf(scope.id) !== -1 ? ' checked' : '') + '><div class="mcp-admin-scope__body"><div class="mcp-admin-scope__summary"><span class="mcp-admin-permission-icon mcp-admin-permission-icon--' + p.icon + '" aria-hidden="true"></span><span><strong>' + escapeHtml(scope.name) + '</strong><small>' + escapeHtml(summary) + '</small></span></div><div class="mcp-admin-scope__footer"><span class="mcp-admin-scope__badges"><small class="mcp-admin-scope__badge mcp-admin-scope__badge--' + p.className + '">' + p.label + '</small>' + (scope.requires_erp_actor ? '<small class="mcp-admin-scope__badge mcp-admin-scope__badge--actor">Requer usuário ERP</small>' : '') + '</span><button type="button" class="mcp-admin-scope__details" data-permission-details="' + escapeHtml(scope.id) + '">Ver detalhes</button></div></div></article>'; }).join('') + '</div></section>';
    }).join('');
    return '<div class="mcp-admin-permission-toolbar"><div class="mcp-admin-filters" role="group" aria-label="Filtrar permissões"><button type="button" class="active" data-permission-filter="all">Todas</button><button type="button" data-permission-filter="selected">Configuradas</button><button type="button" data-permission-filter="unselected">Não configuradas</button></div><label class="mcp-admin-filter-search"><span>Buscar permissão, descrição ou Tool</span><input type="search" id="mcp-admin-permission-search" placeholder="Digite para buscar" autocomplete="off"></label><label class="mcp-admin-filter-search"><span>Categoria</span><select id="mcp-admin-permission-category"><option value="">Todas as categorias</option>' + categories + '</select></label><button type="button" class="mcp-admin-filter-clear" id="mcp-admin-permission-clear">Limpar</button><output id="mcp-admin-permission-count"></output></div><div class="mcp-admin-permission-groups">' + items + '</div>';
  }

  function bindPermissionFilters() {
    var search = document.getElementById('mcp-admin-permission-search'); var category = document.getElementById('mcp-admin-permission-category'); var clear = document.getElementById('mcp-admin-permission-clear'); var count = document.getElementById('mcp-admin-permission-count'); var state = 'all';
    if (!search || !category || !count) return;
    function apply() { var visible = 0; var enabled = 0; var total = content.querySelectorAll('[data-detail-scope]').length; var needle = search.value.toLocaleLowerCase(); Array.prototype.forEach.call(content.querySelectorAll('.mcp-admin-permission-group'), function (group) { var groupMatches = !category.value || group.getAttribute('data-permission-group') === category.value; var groupVisible = 0; Array.prototype.forEach.call(group.querySelectorAll('.mcp-admin-scope'), function (scope) { var checkbox = scope.querySelector('[data-detail-scope]'); scope.classList.toggle('is-selected', checkbox.checked); if (checkbox.checked) enabled += 1; var stateMatches = state === 'all' || (state === 'selected' && checkbox.checked) || (state === 'unselected' && !checkbox.checked); var show = groupMatches && stateMatches && (!needle || scope.textContent.toLocaleLowerCase().indexOf(needle) !== -1); scope.hidden = !show; if (show) { groupVisible += 1; visible += 1; } }); group.hidden = groupVisible === 0; }); count.textContent = enabled + ' de ' + total + ' permissões habilitadas · ' + visible + (visible === 1 ? ' exibida' : ' exibidas'); }
    Array.prototype.forEach.call(content.querySelectorAll('[data-permission-filter]'), function (button) { button.setAttribute('aria-pressed', button.getAttribute('data-permission-filter') === 'all' ? 'true' : 'false'); button.addEventListener('click', function () { state = button.getAttribute('data-permission-filter'); Array.prototype.forEach.call(content.querySelectorAll('[data-permission-filter]'), function (item) { var selected = item === button; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); }); });
    search.addEventListener('input', apply); category.addEventListener('change', apply); Array.prototype.forEach.call(content.querySelectorAll('[data-detail-scope]'), function (checkbox) { checkbox.addEventListener('change', apply); }); Array.prototype.forEach.call(content.querySelectorAll('[data-permission-details]'), function (button) { button.addEventListener('click', function (event) { event.preventDefault(); event.stopPropagation(); var id = button.getAttribute('data-permission-details'); var scope = activePermissionScopes[id]; if (scope) showPermissionDetails(scope); }); }); if (clear) clear.addEventListener('click', function () { state = 'all'; search.value = ''; category.value = ''; Array.prototype.forEach.call(content.querySelectorAll('[data-permission-filter]'), function (item) { var selected = item.getAttribute('data-permission-filter') === 'all'; item.classList.toggle('active', selected); item.setAttribute('aria-pressed', selected ? 'true' : 'false'); }); apply(); search.focus(); }); apply();
  }

  function renderConnectionDetail(data) {
    var c = data.connection; var companyId = data.company.id; var permissions = renderPermissionGroups(data.scope_catalog || [], c.scopes || []);
    var audit = renderAudit(data.audit || [], data.audit_pagination || {});
    var eligibility = data.eligibility || {}; var directUsers = eligibility.direct_users || []; var connectionProfiles = eligibility.connection_profiles || []; var allProfiles = eligibility.profiles || []; var profileMembers = eligibility.profile_members || []; var companyUsers = data.oauth_users || []; var allUsersAllowed = eligibility.all_users === true;
    var linkedProfileMembers = profileMembers.filter(function (m) { return connectionProfiles.some(function (p) { return Number(p.id) === Number(m.profile_id); }); });
    var accessWarning = !allUsersAllowed && directUsers.length === 0 && linkedProfileMembers.length === 0 ? '<div class="mcp-admin-access-warning"><strong>Ninguém está elegível</strong><p>OAuth ficará bloqueado até que um usuário, perfil ou a política Todos seja liberada.</p></div>' : '';
    var directRows = directUsers.map(function (u) { return '<li><div><strong>' + escapeHtml(userLabel(u)) + '</strong><small>' + escapeHtml(u.login || ('Identificação #' + Number(u.id))) + ' · <span class="mcp-admin-origin">Direto</span></small></div><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-eligibility-direct-revoke="' + Number(u.id) + '">Remover</button></li>'; }).join('') || '<li class="mcp-admin-empty-row">Nenhum usuário autorizado diretamente.</li>';
    var profileRows = connectionProfiles.map(function (p) { return '<li><div><strong>' + escapeHtml(p.name) + '</strong><small>' + Number(p.member_count || 0) + ' usuário(s) ativo(s) · <span class="mcp-admin-origin">Perfil</span></small></div><div class="mcp-admin-row-actions"><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-profile-members="' + Number(p.id) + '">Ver membros</button><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-eligibility-profile-revoke="' + Number(p.id) + '">Remover</button></div></li>'; }).join('') || '<li class="mcp-admin-empty-row">Nenhum perfil autorizado.</li>';
    var access = '<div hidden data-detail-panel="access"><header><h3>Acesso</h3><p>Define quem pode autorizar esta conexão. Não concede scopes.</p></header>' + accessWarning + '<nav class="mcp-admin-access-tabs" aria-label="Tipos de acesso"><button type="button" class="active" data-access-tab="all">Todos</button><button type="button" data-access-tab="profiles">Perfis <span>' + Number(connectionProfiles.length) + '</span></button><button type="button" data-access-tab="users">Usuários <span>' + Number(directUsers.length) + '</span></button></nav><section data-access-panel="all" class="mcp-admin-access-card"><h4>Todos os usuários ativos</h4><p>Libera esta conexão para todos os usuários ativos disponíveis nesta instalação.</p><label class="mcp-admin-policy"><input type="checkbox" id="mcp-admin-eligibility-all-users"' + (allUsersAllowed ? ' checked' : '') + '><span><strong>Liberar esta conexão para todos os usuários ativos</strong><small>Origem da elegibilidade: Todos</small></span></label></section><section hidden data-access-panel="profiles" class="mcp-admin-access-card"><header><div><h4>Perfis autorizados</h4><p>Perfis e membros são os já cadastrados no ERP.</p></div><button type="button" class="mcp-admin-button mcp-admin-button--primary" id="mcp-admin-eligibility-profile-add">Adicionar perfil</button></header><ul class="mcp-admin-access-list">' + profileRows + '</ul></section><section hidden data-access-panel="users" class="mcp-admin-access-card"><header><div><h4>Usuários autorizados diretamente</h4><p>Somente usuários ativos podem ser selecionados.</p></div><button type="button" class="mcp-admin-button mcp-admin-button--primary" id="mcp-admin-eligibility-direct-add">Adicionar usuário</button></header><ul class="mcp-admin-access-list">' + directRows + '</ul></section></div>';
    var grants = data.oauth_grants || [];
    var grantsNeedingReconnect = grants.filter(function (grant) { return grant.requires_scope_reauthorization === true || grant.requires_scope_reauthorization === 1 || grant.requires_scope_reauthorization === '1'; });
    var usersById = {}; (data.oauth_users || []).forEach(function (user) { usersById[Number(user.id)] = user; });
    var grantList = grants.map(function (grant) { var user = usersById[Number(grant.user_id)] || { id: grant.user_id }; var userName = userLabel(user); var userLogin = user.login ? '<small>' + escapeHtml(user.login) + '</small>' : ''; var reconnect = (grant.requires_scope_reauthorization === true || grant.requires_scope_reauthorization === 1 || grant.requires_scope_reauthorization === '1') ? '<small class="mcp-admin-oauth-grant__upgrade">Atualização necessária para usar permissões adicionais</small>' : ''; return '<li class="mcp-admin-oauth-grant"><div class="mcp-admin-oauth-grant__details"><div><span>Usuário que autorizou</span><strong>' + escapeHtml(userName) + '</strong>' + userLogin + '</div><div><span>Aplicativo</span><strong>' + escapeHtml(grant.client_name) + '</strong></div><div><span>Permissões</span><small>' + escapeHtml(grant.scopes || 'Nenhuma permissão registrada') + '</small>' + reconnect + '</div><div><span>Status</span><strong>' + escapeHtml(statusLabel(grant.status)) + '</strong></div></div>' + (grant.status === 'active' ? '<button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-oauth-grant-revoke="' + Number(grant.id) + '">Revogar acesso</button>' : '') + '</li>'; }).join('') || '<li class="mcp-admin-empty-row">Nenhum aplicativo autorizado nesta conexão.</li>';
    var mcpUrl = data.mcp_url || '';
    var publicUrlAction = !c.public_id ? '<button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-mcp-url-enable">Gerar URL exclusiva</button><p>Gerar esta URL não altera API Keys, autorizações OAuth ou o acesso atual em /mcp.</p>' : '';
    var oauthUpgradeNotice = grantsNeedingReconnect.length ? '<div class="mcp-admin-oauth-upgrade-notice"><strong>Atualização de permissões disponível</strong><p>As API Keys usam as permissões atuais imediatamente. Para OAuth, você pode revogar as autorizações desatualizadas abaixo ou desconectar e conectar o aplicativo novamente. Ao concluir a reconexão, as autorizações anteriores correspondentes são revogadas automaticamente.</p><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-oauth-grants-revoke-outdated>Revogar ' + Number(grantsNeedingReconnect.length) + (grantsNeedingReconnect.length === 1 ? ' autorização desatualizada' : ' autorizações desatualizadas') + '</button></div>' : '';
    var overviewOAuthUpgradeNotice = grantsNeedingReconnect.length ? '<div class="mcp-admin-oauth-upgrade-notice"><strong>Reconexão de aplicativo necessária</strong><p>Há ' + Number(grantsNeedingReconnect.length) + (grantsNeedingReconnect.length === 1 ? ' autorização OAuth' : ' autorizações OAuth') + ' que não inclui as permissões adicionadas. Você pode revogá-la pela tela OAuth ou desconectar e conectar o aplicativo novamente; a reconexão revoga automaticamente as autorizações anteriores correspondentes.</p><button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-open-oauth-upgrade>Ver autorizações OAuth</button></div>' : '';
    var oauth = '<div hidden data-detail-panel="oauth"><header><h3>OAuth</h3></header>' + oauthUpgradeNotice + '<section><h4>URL pública do MCP</h4>' + (mcpUrl ? '<code id="mcp-admin-mcp-url">' + escapeHtml(mcpUrl) + '</code> <button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-mcp-url-copy">Copiar</button><p>Adicione esta URL no Claude, ChatGPT ou outro cliente MCP compatível para iniciar a autenticação.</p>' + publicUrlAction : '<p class="mcp-admin-credential-pending">A URL pública HTTPS da instalação não está disponível.</p>') + '</section><section><h4>Aplicativos autorizados</h4><p class="mcp-admin-oauth-help">Cada item mostra o usuário que autorizou o aplicativo. Ao revogar, somente o acesso daquele usuário neste aplicativo será removido.</p><ul class="mcp-admin-oauth-grants">' + grantList + '</ul>' + (grants.length === 0 ? '<p>Nenhum aplicativo autorizado. Adicione a URL acima no Claude, ChatGPT ou outro cliente MCP compatível para iniciar a autenticação.</p>' : '') + '</section></div>';
    var apiUsers = data.oauth_users || []; var responsibleId = c.api_key_responsible_user_id || ''; var responsibleUser = apiUsers.filter(function (user) { return Number(user.id) === Number(responsibleId); })[0]; var responsibleLabel = responsibleUser ? escapeHtml(userLabel(responsibleUser)) : 'Nenhum usuário responsável configurado'; var responsibleLogin = responsibleUser && responsibleUser.login ? '<small>' + escapeHtml(responsibleUser.login) + '</small>' : '';
    var apiCredentialRows = data.credentials || []; var apiActiveCredentials = apiCredentialRows.filter(function (credential) { return credential.status === 'active'; }); var apiRotationPending = apiCredentialRows.some(function (credential) { return credential.rotation_pending; });
    var apiCredentials = apiCredentialRows.map(function (credential) { return '<li><strong>' + escapeHtml(credential.public_id) + '</strong> · ' + escapeHtml(credential.status) + (credential.status === 'active' ? ' <button type="button" class="mcp-admin-button mcp-admin-button--secondary" data-api-key-credential-revoke="' + Number(credential.id) + '">Revogar</button>' : '') + '</li>'; }).join('') || '<li>Nenhuma credencial gerada.</li>';
    var apiCredentialAction = c.status !== 'active' ? '<p class="mcp-admin-credential-pending">Ative a conexão para gerar credenciais.</p>' : (apiActiveCredentials.length === 0 ? '<button type="button" class="mcp-admin-button mcp-admin-button--primary" id="mcp-admin-api-key-generate">Gerar credencial</button>' : (apiRotationPending ? '<p class="mcp-admin-credential-pending">Rotação em transição.</p>' : '<button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-api-key-rotate">Rotacionar credencial</button>'));
    var apiKey = '<div hidden data-detail-panel="api-key"><header><h3>Autenticação / API Key</h3><p>Use uma credencial exclusiva por automação. Segredos existentes nunca são exibidos novamente.</p></header><section><h4>Usuário responsável</h4><div class="mcp-admin-api-key-actor"><div><strong>' + responsibleLabel + '</strong>' + responsibleLogin + '</div><button type="button" class="mcp-admin-button mcp-admin-button--primary" id="mcp-admin-api-key-responsible-select">' + (responsibleUser ? 'Alterar usuário' : 'Selecionar usuário') + '</button>' + (responsibleUser ? '<button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-api-key-responsible-clear">Remover</button>' : '') + '</div><p>As operações realizadas por esta integração serão executadas no ERP utilizando este usuário.</p></section><section><h4>Credenciais</h4>' + apiCredentialAction + '<ul>' + apiCredentials + '</ul></section><section><h4>Permissões</h4><p>Configure as permissões na aba Permissões. Se uma permissão exigir ator ERP, um usuário responsável válido é obrigatório.</p></section></div>';
    content.innerHTML = '<button class="mcp-admin-back" id="mcp-admin-connection-back">← Voltar para conexões</button><section class="mcp-admin-company-detail"><header><div><h2>' + escapeHtml(c.name) + '</h2><p>' + escapeHtml(data.company.legal_name) + '</p></div><div class="mcp-admin-action-group"><button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-rename">Alterar nome</button><button type="button" class="mcp-admin-button mcp-admin-button--secondary" id="mcp-admin-delete-unused">Excluir</button></div></header><nav class="mcp-admin-tabs"><button class="active" data-detail-tab="overview">Visão geral</button><button data-detail-tab="permissions">Permissões</button><button data-detail-tab="api-key">API Key</button><button data-detail-tab="access">Acesso</button><button data-detail-tab="oauth">OAuth</button><button data-detail-tab="audit">Auditoria</button></nav><div data-detail-panel="overview"><dl class="mcp-admin-summary"><div><dt>Status</dt><dd>' + escapeHtml(statusLabel(c.status)) + '</dd></div><div><dt>Scopes</dt><dd>' + Number((c.scopes || []).length) + '</dd></div><div><dt>Último uso</dt><dd>' + usageLabel(c.last_usage_at) + '</dd></div></dl>' + overviewOAuthUpgradeNotice + '</div><div hidden data-detail-panel="permissions"><h3>Permissões</h3>' + permissions + '<button type="button" class="mcp-admin-button mcp-admin-button--primary" id="mcp-admin-save-detail-scopes">Salvar permissões</button></div>' + apiKey + access + oauth + '<div hidden data-detail-panel="audit"><h3>Auditoria</h3>' + audit + '</div></section>';
    renderSuccessNotice();
    var saveScopes = document.getElementById('mcp-admin-save-detail-scopes');
    if (saveScopes) { var permissionFooter = document.createElement('div'); permissionFooter.className = 'mcp-admin-permissions-footer'; saveScopes.parentNode.insertBefore(permissionFooter, saveScopes); permissionFooter.appendChild(saveScopes); }
    function selectDetailTab(tab) { selectedDetailTab = tab; Array.prototype.forEach.call(content.querySelectorAll('[data-detail-tab]'), function (button) { button.classList.toggle('active', button.getAttribute('data-detail-tab') === tab); }); Array.prototype.forEach.call(content.querySelectorAll('[data-detail-panel]'), function (panel) { panel.hidden = panel.getAttribute('data-detail-panel') !== tab; }); }
    selectDetailTab(selectedDetailTab);
    document.getElementById('mcp-admin-connection-back').addEventListener('click', function () { requestCompany(companyId).then(function (next) { renderCompany(next, companyId); }); });
    Array.prototype.forEach.call(content.querySelectorAll('[data-detail-tab]'), function (button) { button.addEventListener('click', function () { selectDetailTab(button.getAttribute('data-detail-tab')); }); });
    Array.prototype.forEach.call(content.querySelectorAll('[data-open-oauth-upgrade]'), function (button) { button.addEventListener('click', function () { selectDetailTab('oauth'); }); });
    bindAccessFilters();
    bindOAuthFilters();
    bindAuditFilters();
    bindPermissionFilters();
    var loadMoreAudit = document.getElementById('mcp-admin-audit-more'); if (loadMoreAudit) loadMoreAudit.addEventListener('click', function () { var button = this; button.disabled = true; button.textContent = 'Carregando…'; requestConnection(companyId, c.id, Number(button.getAttribute('data-audit-offset'))).then(function (next) { var list = content.querySelector('.mcp-admin-audit-list'); if (!list) return; list.insertAdjacentHTML('beforeend', renderAuditRows(next.audit || [])); var pagination = next.audit_pagination || {}; if (pagination.has_more) { button.disabled = false; button.textContent = 'Carregar 50 atividades anteriores'; button.setAttribute('data-audit-offset', String(Number(pagination.next_offset || 0))); } else { button.remove(); } var filter = document.getElementById('mcp-admin-audit-search'); if (filter && typeof Event === 'function') filter.dispatchEvent(new Event('input')); }).catch(function () { button.disabled = false; button.textContent = 'Não foi possível carregar. Tentar novamente'; }); });
    document.getElementById('mcp-admin-save-detail-scopes').addEventListener('click', function () { var scopes = Array.prototype.map.call(content.querySelectorAll('[data-detail-scope]:checked'), function (i) { return i.value; }); var addedScopes = scopes.filter(function (scope) { return (c.scopes || []).indexOf(scope) === -1; }); var hasActiveOAuthGrant = grants.some(function (grant) { return grant.status === 'active'; }); mutate(root.getAttribute('data-connection-scopes-url'), { C004_Id: companyId, MCP001_Id: c.id, scopes: JSON.stringify(scopes) }).then(function () { if (addedScopes.length && hasActiveOAuthGrant) pendingSuccessMessage = 'Permissões atualizadas. As API Keys já usam a alteração. Aplicativos OAuth mantêm as permissões autorizadas anteriormente; revogue e reconecte o aplicativo caso ele precise usar permissões adicionais.'; return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); });
    document.getElementById('mcp-admin-api-key-responsible-select').addEventListener('click', function () { showEntityPicker('Selecionar usuário responsável', apiUsers, 'Nenhum usuário válido está disponível para esta empresa.', function (id) { mutate(root.getAttribute('data-api-key-responsible-url'), { C004_Id: companyId, MCP001_Id: c.id, C007_Id: id }).then(function () { return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); }, userLabel); });
    var apiClearResponsible = document.getElementById('mcp-admin-api-key-responsible-clear'); if (apiClearResponsible) apiClearResponsible.addEventListener('click', function () { if (!window.confirm('Remover o usuário responsável desta API Key?')) return; mutate(root.getAttribute('data-api-key-responsible-url'), { C004_Id: companyId, MCP001_Id: c.id, C007_Id: '' }).then(function () { return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); });
    var apiGenerate = document.getElementById('mcp-admin-api-key-generate'); if (apiGenerate) apiGenerate.addEventListener('click', function () { mutate(root.getAttribute('data-credential-generate-url'), { C004_Id: companyId, MCP001_Id: c.id }).then(function (result) { showOneTimeKey(result.api_key); return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); });
    var apiRotate = document.getElementById('mcp-admin-api-key-rotate'); if (apiRotate) apiRotate.addEventListener('click', function () { mutate(root.getAttribute('data-credential-rotate-url'), { C004_Id: companyId, MCP001_Id: c.id }).then(function (result) { showOneTimeKey(result.api_key); return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); });
    Array.prototype.forEach.call(content.querySelectorAll('[data-api-key-credential-revoke]'), function (button) { button.addEventListener('click', function () { var reason = window.prompt('Motivo da revogação (obrigatório):'); if (!reason || !window.confirm('Revogar esta credencial imediatamente?')) return; mutate(root.getAttribute('data-credential-revoke-url'), { C004_Id: companyId, MCP001_Id: c.id, MCP002_Id: button.getAttribute('data-api-key-credential-revoke'), reason: reason }).then(function () { return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); }); });
    document.getElementById('mcp-admin-rename').addEventListener('click', function () { var name = window.prompt('Novo nome da conexão:', c.name); if (name === null) return; mutate(root.getAttribute('data-connection-rename-url'), { C004_Id: companyId, MCP001_Id: c.id, nome: name }).then(function () { return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(renderOperationError); });
    var deleteUnused = document.getElementById('mcp-admin-delete-unused'); if (deleteUnused) deleteUnused.addEventListener('click', function () { confirmUnusedConnection(c.name, function () { mutate(root.getAttribute('data-connection-delete-url'), { C004_Id: companyId, MCP001_Id: c.id }).then(function () { pendingSuccessMessage = 'A conexão foi excluída.'; return requestCompany(companyId); }).then(function (next) { renderCompany(next, companyId); }).catch(function (error) { showOperationDialog('Não foi possível excluir a conexão', error && error.message ? error.message : 'Tente novamente. Se o problema persistir, contate o suporte.'); }); }); });
    var oauthRefresh = function () { return requestConnection(companyId, c.id).then(renderConnectionDetail); };
    var enableMcpUrl = document.getElementById("mcp-admin-mcp-url-enable"); if (enableMcpUrl) enableMcpUrl.addEventListener("click", function () { if (!window.confirm("Gerar uma URL exclusiva para esta conexão? O acesso atual em /mcp continuará funcionando.")) return; enableMcpUrl.disabled = true; mutate(root.getAttribute("data-connection-public-id-url"), { C004_Id: companyId, MCP001_Id: c.id }).then(function () { pendingSuccessMessage = "URL exclusiva gerada. O acesso atual em /mcp foi preservado."; return requestConnection(companyId, c.id); }).then(renderConnectionDetail).catch(function (error) { enableMcpUrl.disabled = false; renderOperationError(error); }); });
    var copyMcpUrl = document.getElementById('mcp-admin-mcp-url-copy'); if (copyMcpUrl) copyMcpUrl.addEventListener('click', function () { var url = (document.getElementById('mcp-admin-mcp-url') || {}).textContent || ''; if (navigator.clipboard) { navigator.clipboard.writeText(url); copyMcpUrl.textContent = 'Copiada'; } else { window.prompt('Copie a URL:', url); } });
    Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-grant-revoke]'), function (button) { button.addEventListener('click', function () { var reason = window.prompt('Motivo da revogação:'); if (!reason || !window.confirm('Revogar este grant?')) return; mutate(root.getAttribute('data-oauth-grant-revoke-url'), { C004_Id: companyId, MCP001_Id: c.id, MCP005_Id: button.getAttribute('data-oauth-grant-revoke'), reason: reason }).then(oauthRefresh).catch(renderOperationError); }); });
    Array.prototype.forEach.call(content.querySelectorAll('[data-oauth-grants-revoke-outdated]'), function (button) { button.addEventListener('click', function () { var count = grantsNeedingReconnect.length; var label = count === 1 ? 'autorização' : 'autorizações'; if (!window.confirm('Revogar ' + count + ' ' + label + ' OAuth desatualizada(s)? As autorizações já atualizadas serão preservadas. Os aplicativos revogados precisarão ser reconectados caso ainda precisem acessar esta conexão.')) return; button.disabled = true; mutate(root.getAttribute('data-oauth-grants-revoke-outdated-url'), { C004_Id: companyId, MCP001_Id: c.id }).then(function (result) { var revoked = Number(result.revoked_count || 0); pendingSuccessMessage = revoked === 1 ? '1 autorização OAuth desatualizada foi revogada.' : revoked + ' autorizações OAuth desatualizadas foram revogadas.'; return oauthRefresh(); }).catch(function (error) { button.disabled = false; renderOperationError(error); }); }); });
    var accessRefresh = oauthRefresh;
    Array.prototype.forEach.call(content.querySelectorAll('[data-access-tab]'), function (button) { button.addEventListener('click', function () { Array.prototype.forEach.call(content.querySelectorAll('[data-access-tab]'), function (tab) { tab.classList.toggle('active', tab === button); }); Array.prototype.forEach.call(content.querySelectorAll('[data-access-panel]'), function (panel) { panel.hidden = panel.getAttribute('data-access-panel') !== button.getAttribute('data-access-tab'); }); }); });
    var allUsers = document.getElementById('mcp-admin-eligibility-all-users'); if (allUsers) allUsers.addEventListener('change', function () { var enabled = allUsers.checked; if (enabled && !window.confirm('Liberar esta conexão para todos os usuários ativos? Esta é uma autorização ampla.')) { allUsers.checked = false; return; } mutate(root.getAttribute('data-eligibility-all-users-url'), { C004_Id: companyId, MCP001_Id: c.id, enabled: enabled ? '1' : '0' }).then(accessRefresh).catch(function () { allUsers.checked = !enabled; renderOperationError(); }); });
    var directAdd=document.getElementById('mcp-admin-eligibility-direct-add'); if(directAdd)directAdd.addEventListener('click',function(){showEntityPicker('Adicionar usuário',companyUsers.filter(function (user) { return !directUsers.some(function (linked) { return Number(linked.id) === Number(user.id); }); }),'Todos os usuários ativos já estão vinculados diretamente.',function(id){mutate(root.getAttribute('data-eligibility-direct-add-url'),{C004_Id:companyId,MCP001_Id:c.id,C007_Id:id}).then(accessRefresh).catch(renderOperationError);},userLabel);});
    Array.prototype.forEach.call(content.querySelectorAll('[data-eligibility-direct-revoke]'),function(b){b.addEventListener('click',function(){if(!window.confirm('Remover este usuário direto da conexão?'))return;mutate(root.getAttribute('data-eligibility-direct-revoke-url'),{C004_Id:companyId,MCP001_Id:c.id,C007_Id:b.getAttribute('data-eligibility-direct-revoke')}).then(accessRefresh).catch(renderOperationError);});});
    var profileAdd=document.getElementById('mcp-admin-eligibility-profile-add');if(profileAdd)profileAdd.addEventListener('click',function(){showEntityPicker('Adicionar perfil',allProfiles.filter(function (profile) { return !connectionProfiles.some(function (linked) { return Number(linked.id) === Number(profile.id); }); }),'Todos os perfis elegíveis já estão vinculados.',function(id){mutate(root.getAttribute('data-eligibility-profile-add-url'),{C004_Id:companyId,MCP001_Id:c.id,C021_Id:id}).then(accessRefresh).catch(renderOperationError);});});
    Array.prototype.forEach.call(content.querySelectorAll('[data-eligibility-profile-revoke]'),function(b){b.addEventListener('click',function(){if(!window.confirm('Remover este perfil da conexão?'))return;mutate(root.getAttribute('data-eligibility-profile-revoke-url'),{C004_Id:companyId,MCP001_Id:c.id,C021_Id:b.getAttribute('data-eligibility-profile-revoke')}).then(accessRefresh).catch(renderOperationError);});});
    Array.prototype.forEach.call(content.querySelectorAll('[data-profile-members]'),function(b){b.addEventListener('click',function(){var profile=connectionProfiles.filter(function(p){return Number(p.id)===Number(b.getAttribute('data-profile-members'));})[0];if(profile)showProfileMembers(profile,profileMembers.filter(function(m){return Number(m.profile_id)===Number(profile.id);}));});});
  }

  function applyConnectionFilter(filter) {
    selectedConnectionFilter = filter === 'active' || filter === 'inactive' ? filter : 'all';
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-filter]'), function (item) {
      item.classList.toggle('active', item.getAttribute('data-connection-filter') === selectedConnectionFilter);
    });
    Array.prototype.forEach.call(content.querySelectorAll('[data-connection-filter-status]'), function (item) {
      var name = ((item.querySelector('h3') || {}).textContent || '').toLocaleLowerCase();
      var matchesStatus = selectedConnectionFilter === 'all' || item.getAttribute('data-connection-filter-status') === selectedConnectionFilter;
      var matchesSearch = name.indexOf(selectedConnectionSearch.toLocaleLowerCase()) !== -1;
      item.hidden = !matchesStatus || !matchesSearch;
    });
  }

  function renderError(error) {
    content.innerHTML = '<div class="mcp-admin-error"><strong>Não foi possível carregar a empresa.</strong><p>Tente novamente. Se o problema persistir, contate o suporte.</p><button type="button" class="mcp-admin-back" id="mcp-admin-back">Voltar para empresas</button></div>';
    document.getElementById('mcp-admin-back').addEventListener('click', showHome);
  }

  function renderOperationError(error) {
    var message = error && error.message ? error.message : 'Não foi possível concluir a operação. Tente novamente.';
    showOperationDialog('Não foi possível concluir a operação', message);
  }

  function bindCompanyCards() {
    Array.prototype.forEach.call(document.querySelectorAll('[data-company-id]'), function (card) {
      card.addEventListener('click', function () {
        var companyId = card.getAttribute('data-company-id');
        requestCompany(companyId).then(function (data) { renderCompany(data, companyId); }).catch(renderError);
      });
    });
  }

  function bindHomeButtons() {
    Array.prototype.forEach.call(document.querySelectorAll('[data-mcp-admin-home]'), function (button) {
      button.addEventListener('click', showHome);
    });
  }

  bindCompanyCards();
  bindHomeButtons();
  bindFilterDelegation();
}());
