/* Gestão interna do portal; nenhum link ou token de convite é enviado ao navegador. */
(function () {
  'use strict';
  var labels = { sem_envio: 'Link não enviado', enviado: 'Aguardando confirmar e-mail', email_confirmado: 'E-mail confirmado', senha_enviada: 'Link de senha enviado', senha_expirada: 'Link de senha expirado', senha_cadastrada: 'Senha cadastrada', acessou: 'Acesso confirmado', revogado: 'Acesso revogado', cadastro: 'Revisar cadastro', falha: 'Falha no envio', nao_confirmado: 'Envio não confirmado', inativo: 'Acesso inativo' };
  function escape(value) { return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]; }); }
  function date(value) { return value ? new Date(value * 1000).toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo', day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'; }
  function search(value) { return String(value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase(); }
  function mount(root, initial, options) {
    var data = initial, query = '', status = '', page = 1, size = 25, alive = true, busy = false, trigger;
    var controller = new AbortController();
    var dialog = document.createElement('dialog'); dialog.className = 'rep-access-dialog'; dialog.setAttribute('aria-labelledby', 'repAccessDialogTitle');
    document.body.appendChild(dialog);
    function close() { if (!busy) dialog.close(); }
    dialog.addEventListener('cancel', function (e) { if (busy) e.preventDefault(); });
    dialog.addEventListener('close', function () { if (trigger && trigger.isConnected) trigger.focus(); });
    var preview = document.createElement('dialog'), previewRevision = 0, previewContext = '', previewTrigger;
    preview.className = 'rep-portal-dialog'; preview.setAttribute('aria-labelledby', 'repPortalTitle');
    preview.innerHTML = '<header><div><small>Visualização do gestor</small><h2 id="repPortalTitle">Portal do representante</h2></div><button type="button">Fechar ×</button></header><p class="rep-portal-loading" role="status">Preparando o portal…</p><iframe title="Portal do representante" referrerpolicy="no-referrer" hidden></iframe>';
    document.body.appendChild(preview);
    var frame = preview.querySelector('iframe'), previewLoading = preview.querySelector('.rep-portal-loading');
    function releasePreview(context) {
      if (context) options.action({ acao: 'fecharVisualizacao', contexto: context, csrf: data.csrf }).catch(function () {});
    }
    preview.querySelector('button').addEventListener('click', function () { preview.close(); });
    preview.addEventListener('close', function () {
      previewRevision++; releasePreview(previewContext); previewContext = ''; frame.removeAttribute('src'); frame.hidden = true;
      if (previewTrigger && previewTrigger.isConnected) previewTrigger.focus({ preventScroll: true });
    });
    frame.addEventListener('load', function () {
      if (preview.open && previewContext && frame.hasAttribute('src')) { previewLoading.hidden = true; frame.hidden = false; }
    });
    window.addEventListener('message', function (event) {
      if (event.origin !== location.origin || event.source !== frame.contentWindow || !event.data || event.data.contexto !== previewContext) return;
      if (event.data.tipo === 'hardness-portal-gestor-fechar') preview.close();
      else if (event.data.tipo === 'hardness-portal-gestor-trocar' && data.podeVisualizar && /^[1-9][0-9]{0,9}$/.test(String(event.data.representante))) {
        var row = data.linhas.find(function (r) { return String(r.id) === String(event.data.representante); });
        openPortal(row || { id: event.data.representante, nome: 'Carregando representante…' }, previewTrigger);
      }
    }, { signal: controller.signal });
    function openPortal(row, button) {
      var current = ++previewRevision;
      previewTrigger = button; previewLoading.hidden = false; previewLoading.textContent = 'Preparando o portal…'; frame.hidden = true;
      preview.querySelector('h2').textContent = row.nome; frame.title = 'Portal de ' + row.nome;
      preview.showModal();
      options.action({ acao: 'visualizar', representante: String(row.id), csrf: data.csrf }).then(function (response) {
        if (!alive || current !== previewRevision || !preview.open) { releasePreview(response.contexto); return; }
        var url = new URL(response.url, location.href), base = new URL('../portal-representante/', location.href);
        if (url.origin !== location.origin || url.pathname !== base.pathname || !/^[a-f0-9]{64}$/.test(response.contexto || '') || url.search !== '?gestor=' + response.contexto) {
          releasePreview(response.contexto); throw new Error('Não foi possível abrir a visualização do representante.');
        }
        releasePreview(previewContext);
        previewContext = response.contexto;
        if (response.representante) { preview.querySelector('h2').textContent = response.representante.nome; frame.title = 'Portal de ' + response.representante.nome; }
        frame.src = url.href;
      }).catch(function (error) {
        if (alive && current === previewRevision && preview.open) previewLoading.textContent = error.message;
      });
    }
    root.innerHTML = '<div class="rep-access-panel"><div class="rep-access-kpis"></div><article class="wide-card rep-access-list"><header><div><h3>Representantes ativos</h3><p>Disponibilize o portal e acompanhe a liberação de cada representante.</p></div><span class="rep-access-scope">Acessos da empresa atual</span></header><div class="rep-access-toolbar"><label>Buscar representante<input type="search" placeholder="Nome, código, e-mail ou cidade" maxlength="160" autocomplete="off"></label><label>Situação<select><option value="">Todas as situações</option><option value="liberados">Links liberados</option><option value="sem_link">Sem acesso liberado</option>' + Object.keys(labels).map(function (key) { return '<option value="' + key + '">' + labels[key] + '</option>'; }).join('') + '</select></label><button type="button" data-clear>Limpar</button></div><div class="rep-access-notice" role="status" hidden></div><div class="rep-access-table"></div><footer><span class="rep-access-count"></span><div><button type="button" data-page="-1">Anterior</button><span class="rep-access-page"></span><button type="button" data-page="1">Próxima</button></div></footer><p class="rep-access-footnote">O primeiro acesso exige confirmar o e-mail e cadastrar a senha. Na visualização interna, o gestor pode alternar entre representantes ativos, inclusive os que não têm link liberado. Acesso confirmado indica um login com senha concluído; não indica que o representante está conectado agora.</p></article></div>';
    function selected(row) {
      var matches = status === 'liberados' ? row.ativo : status === 'sem_link' ? !row.ativo : !status || row.status === status;
      return matches && (!query || search([row.id, row.nome, row.email, row.cidade, row.uf].join(' ')).indexOf(search(query)) !== -1);
    }
    function render() {
      var all = data.linhas, active = all.filter(function (r) { return r.ativo; }).length, entered = all.filter(function (r) { return r.status === 'acessou'; }).length;
      var cards = [{ title: 'Representantes ativos', value: all.length, note: 'Ativos no cadastro', filter: '', icon: '◉', color: 'blue' },
        { title: 'Links liberados', value: active, note: 'Convites ativos para cadastro ou login', filter: 'liberados', icon: '↗', color: 'green' },
        { title: 'Sem acesso liberado', value: all.length - active, note: 'Envio ou revisão pendente', filter: 'sem_link', icon: '◷', color: 'amber' },
        { title: 'Acessos confirmados', value: entered, note: 'Já entraram com e-mail e senha', filter: 'acessou', icon: '✓', color: 'purple' }];
      root.querySelector('.rep-access-kpis').innerHTML = cards.map(function (c) { return '<button type="button" class="rep-access-kpi ' + c.color + '" data-status="' + c.filter + '" aria-pressed="' + (status === c.filter) + '"><span>' + c.title + '<i aria-hidden="true">' + c.icon + '</i></span><strong>' + c.value + '</strong><small>' + c.note + '</small></button>'; }).join('');
      var filtered = all.filter(selected), pages = Math.max(1, Math.ceil(filtered.length / size)); page = Math.min(page, pages);
      var rows = filtered.slice((page - 1) * size, page * size);
      root.querySelector('select').value = status;
      root.querySelector('.rep-access-table').innerHTML = rows.length ? '<div class="table-scroll"><table><thead><tr><th>Representante</th><th>Contato / localização</th><th>Situação do portal</th><th>Último envio</th><th>Último acesso</th><th>Ações</th></tr></thead><tbody>' + rows.map(function (r) {
        var button = r.ativo ? 'Reenviar link' : 'Disponibilizar link';
        var action = data.podeGerenciar ? '<div class="rep-access-actions">' + (data.podeVisualizar && r.ativo ? '<button type="button" class="rep-access-open" data-action="visualizar" data-representante="' + r.id + '">Abrir portal</button>' : '') + '<button type="button" data-action="enviar" data-representante="' + r.id + '"' + (!r.podeEnviar ? ' disabled' : '') + '>' + button + '</button>' + (r.podeRevogar ? '<button type="button" class="rep-access-revoke" data-action="revogar" data-representante="' + r.id + '">Revogar</button>' : '') + '</div>' : '<span class="rep-access-readonly">Somente consulta</span>';
        return '<tr><td><strong>' + escape(r.nome) + '</strong><small>Código ' + escape(r.id) + '</small></td><td><span>' + escape(r.email || 'Sem e-mail cadastrado') + '</span><small>' + escape([r.cidade, r.uf].filter(Boolean).join(' / ') || 'Localização não informada') + '</small></td><td><span class="rep-access-badge ' + escape(r.status) + '"><i></i>' + escape(labels[r.status] || 'Acesso inativo') + '</span>' + (r.pendencia ? '<small class="rep-access-warning">' + escape(r.pendencia) + '</small>' : '') + (r.falhaReenvio ? '<small class="rep-access-warning">Último reenvio falhou; o link anterior continua liberado.</small>' : '') + '</td><td class="rep-access-date">' + escape(date(r.ultimoEnvio)) + '</td><td class="rep-access-date">' + escape(date(r.ultimoAcesso)) + (!r.ultimoAcesso && r.ultimoEnvio ? '<small>Sem login registrado</small>' : '') + '</td><td>' + action + '</td></tr>';
      }).join('') + '</tbody></table></div>' : '<div class="empty-state">' + (all.length ? 'Nenhum representante encontrado nesta seleção.' : 'Nenhum representante ativo no cadastro.') + '</div>';
      root.querySelector('.rep-access-count').textContent = filtered.length + ' de ' + all.length + ' representantes';
      root.querySelector('.rep-access-page').textContent = 'Página ' + page + ' de ' + pages;
      root.querySelector('[data-page="-1"]').disabled = page === 1;
      root.querySelector('[data-page="1"]').disabled = page === pages;
    }
    function message(title, text, pending) {
      dialog.innerHTML = '<header><h2 id="repAccessDialogTitle">' + escape(title) + '</h2></header><p role="status">' + escape(text) + '</p><footer><button type="button" data-close' + (pending ? ' disabled' : '') + '>Fechar</button></footer>';
      dialog.setAttribute('aria-busy', pending ? 'true' : 'false');
      dialog.querySelector('[data-close]').addEventListener('click', close);
      if (!dialog.open) dialog.showModal();
      if (!pending) dialog.querySelector('button').focus();
    }
    function run(row, action) {
      if (busy) return;
      busy = true;
      message(action === 'enviar' ? 'Enviando link…' : 'Revogando acesso…', action === 'enviar' ? 'Aguarde o envio para ' + row.email + '.' : 'Encerrando o acesso deste representante à empresa atual.', true);
      var result, failure;
      options.action({ acao: action, representante: String(row.id), csrf: data.csrf }).then(function (response) { result = response; }).catch(function (error) { failure = error; }).then(function () {
        if (!alive) return;
        // Reconsulta também após falha: não confunde tentativa de envio com link enviado.
        return options.reload().then(function (response) {
          if (!alive) return;
          data = response.portalRepresentantes; render(); options.updated(response.updatedAt);
        }).catch(function () {
          if (!alive) return;
          var notice = root.querySelector('.rep-access-notice'); notice.hidden = false;
          notice.textContent = 'Não foi possível atualizar a lista. Use Atualizar para conferir a situação dos acessos.';
        });
      }).then(function () {
        busy = false;
        if (!alive) return;
        trigger = root.querySelector('[data-action="' + action + '"][data-representante="' + row.id + '"]') || root.querySelector('input');
        message(failure ? 'Não foi possível confirmar a ação' : action === 'enviar' ? 'Link enviado' : 'Acesso revogado', failure ? failure.message : result.mensagem, false);
      });
    }
    root.querySelector('input').addEventListener('input', function (e) { query = e.target.value; page = 1; render(); });
    root.querySelector('select').addEventListener('change', function (e) { status = e.target.value; page = 1; render(); });
    root.addEventListener('click', function handle(event) {
      var button = event.target.closest('button'); if (!button || button.disabled || busy) return;
      if (button.hasAttribute('data-status')) { status = button.dataset.status; page = 1; render(); }
      else if (button.hasAttribute('data-page')) { page += Number(button.dataset.page); render(); }
      else if (button.hasAttribute('data-clear')) { status = ''; query = ''; page = 1; root.querySelector('input').value = ''; render(); }
      else if (button.hasAttribute('data-action') && data.podeGerenciar) {
        var row = data.linhas.find(function (r) { return String(r.id) === button.dataset.representante; }); if (!row) return;
        if (button.dataset.action === 'visualizar') { if (data.podeVisualizar && row.ativo) openPortal(row, button); return; }
        trigger = button;
        if (button.dataset.action === 'enviar') { run(row, 'enviar'); return; }
        message('Revogar acesso de ' + row.nome + '?', 'O link, os códigos, a senha e as sessões deste representante serão invalidados para a empresa atual. Uma nova liberação exigirá confirmar o e-mail e criar outra senha.', false);
        var confirm = document.createElement('button'); confirm.type = 'button'; confirm.className = 'rep-access-revoke'; confirm.textContent = 'Revogar acesso';
        confirm.addEventListener('click', function () { run(row, 'revogar'); }); dialog.querySelector('footer').appendChild(confirm);
      }
    }, { signal: controller.signal });
    render();
    return { destroy: function () { alive = false; previewRevision++; releasePreview(previewContext); previewContext = ''; controller.abort(); preview.close(); preview.remove(); dialog.close(); dialog.remove(); } };
  }
  window.DashboardPortalRepresentantes = { mount: mount };
})();
