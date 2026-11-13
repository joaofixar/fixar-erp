(function () {
  'use strict';
  var enabled = false, reports = [], bytes = 0, openings = [], currentOpening = '', modal = null, returnFocus = null;
  var button = document.getElementById('sqlAnalysis');
  if (!button) return;

  function configure(value) {
    enabled = value === true;
    button.hidden = !enabled;
    if (!enabled) { reports = []; openings = []; currentOpening = ''; bytes = 0; if (modal) modal.hidden = true; }
  }
  function hasError(report) {
    return report.httpStatus >= 400 || !!report.erro || !!report.ultimaConsultaComErro;
  }
  function isSlow(report) { return report.tempoServidorMs >= 1000 || report.tempoClienteMs >= 1000 || report.tempoTotalMs >= 1000; }
  function findOpening(id) { return openings.filter(function (o) { return o.id === id; })[0]; }
  function begin(context) {
    if (!enabled) return '';
    var id = Array.from(crypto.getRandomValues(new Uint8Array(8))).map(function (v) { return ('0' + v.toString(16)).slice(-2); }).join('');
    var input = {};
    if (context.params) new URLSearchParams(context.params).forEach(function (value, key) {
      if (/^f_/.test(key) && !/senha|password|secret|token|cookie/i.test(key)) input[key] = value.slice(0, 500);
    });
    openings.push({ id: id, indicador: context.id || '', aba: context.tab || '', titulo: context.title || context.id || '',
      data: new Date().toISOString(), inicioRelativoMs: performance.now(), estado: 'carregando', fase: 'consultando servidor',
      entrada: input, requisicoes: 0, tempoSqlMs: 0, tempoServidorSomadoMs: 0, tempoRequisicoesMs: 0, tempoMontagemMs: 0, erros: [], avisos: [] });
    currentOpening = id; refresh(); return id;
  }
  function phase(id, name) { var o = findOpening(id); if (o) { o.fase = name; if (name === 'montando tela') o.montagemInicioMs = performance.now(); refresh(); } }
  function errorInfo(error) {
    return { tipo: String(error.name || 'Error').slice(0, 100), mensagem: String(error.message || error).slice(0, 2048),
      pilha: String(error.stack || '').replace(/https?:\/\/[^\s)]+/g, function (url) { try { return new URL(url).pathname.split('/').pop().split('?')[0]; } catch (ignore) { return '[arquivo]'; } }).slice(0, 4096) };
  }
  function complete(id, error, renderMs, cancelled) {
    var o = findOpening(id); if (!o) return;
    if (cancelled && o.estado !== 'carregando') return;
    var elapsed = Math.round(performance.now() - o.inicioRelativoMs);
    if (o.estado === 'carregando') o.tempoTotalMs = elapsed;
    else if (error) o.tempoDesdeInicioAteErroMs = elapsed;
    o.tempoMontagemMs += Math.round(renderMs || (o.montagemInicioMs ? performance.now() - o.montagemInicioMs : 0));
    delete o.montagemInicioMs;
    o.tempoForaRequisicoesMs = Math.max(0, o.tempoTotalMs - o.tempoRequisicoesMs);
    o.estado = cancelled ? 'cancelado' : error || o.erros.length ? 'erro' : o.avisos.length ? 'aviso' : 'concluido';
    o.fase = cancelled ? 'cancelado pelo navegador' : error ? 'falhou' : 'tela montada';
    if (error) {
      o.erroFinal = errorInfo(error);
      o.erroFinal.origem = error.dashboardSqlRecorded ? 'requisicao' : 'navegador';
      if (!o.erros.some(function (e) { return e.mensagem === o.erroFinal.mensagem; })) o.erros.push(o.erroFinal);
      o.erros = o.erros.slice(-10);
      error.dashboardSqlRecorded = true;
    }
    refresh();
  }
  function receive(data, url, status, started, metadata) {
    // O servidor só fornece analiseSql após validar a autorização ROOT.
    if (data && data.analiseSql) {
      configure(true);
      var report = data.analiseSql;
      report.tempoClienteMs = Math.round(performance.now() - started);
      report.httpStatus = status;
      if (metadata) report.http = metadata;
      if (!report.aberturaId) report.aberturaId = new URL(url, location.href).searchParams.get('diagnosticoCarga') || '';
      var o = findOpening(report.aberturaId);
      if (o) {
        o.requisicoes++; o.tempoSqlMs += Number(report.tempoSqlMs || 0); o.tempoServidorSomadoMs += Number(report.tempoServidorMs || 0); o.tempoRequisicoesMs += report.tempoClienteMs;
        if (report.erro) o.erros = o.erros.concat([{ mensagem: report.erro, httpStatus: status }]).slice(-10);
        var slowest = report.consultaMaisLenta || (report.consultas || []).reduce(function (a, q) { return !a || q.tempoMs > a.tempoMs ? q : a; }, null);
        if (slowest && (!o.consultaMaisLenta || slowest.tempoMs > o.consultaMaisLenta.tempoMs)) o.consultaMaisLenta = slowest;
        o.avisos = o.avisos.concat(report.avisos || []).slice(-10);
        if (report.ultimaConsultaComErro && !report.erro) o.avisos = o.avisos.concat([{ mensagem: 'Houve falha SQL nesta etapa. Consulte a última consulta com erro e as etapas seguintes.' }]).slice(-10);
        if (status === 202) o.fase = 'consultando etapa ' + o.requisicoes;
      }
      keep(report);
      return true;
    }
    return false;
  }
  function keep(report) {
    var size = JSON.stringify(report).length * 2;
    if (size > 1048576) return;
    reports.push({ data: report, size: size }); bytes += size;
    refresh();
  }
  function refresh() {
    while (openings.length > 20) openings.shift();
    var openingBytes = JSON.stringify(openings).length * 2;
    while (reports.length && (reports.length > 20 || bytes + openingBytes > 1048576)) bytes -= reports.shift().size;
    while (openings.length && bytes + JSON.stringify(openings).length * 2 > 1048576) openings.shift();
    var errors = Math.max(reports.filter(function (r) { return hasError(r.data); }).length, openings.filter(function (o) { return o.estado === 'erro'; }).length);
    button.textContent = 'Análise SQL' + (errors ? ' (' + errors + ' erro' + (errors > 1 ? 's' : '') + ')' : '');
    button.classList.toggle('sql-has-error', errors > 0);
    if (modal && !modal.hidden) render();
  }
  function failure(error, url, started, metadata) {
    if (!enabled || !/data\.php(?:\?|$)/.test(url) || error.name === 'AbortError') return;
    var params = new URL(url, location.href).searchParams;
    if (['indicator', 'commercialClientHistory', 'configuration'].indexOf(params.get('action')) < 0) return;
    var opening = findOpening(params.get('diagnosticoCarga'));
    if (opening) {
      opening.requisicoes++; opening.tempoRequisicoesMs += Math.round(performance.now() - started);
      opening.erros = opening.erros.concat([{ mensagem: String(error.message).slice(0, 2048), httpStatus: metadata ? metadata.status : 0 }]).slice(-10);
    }
    keep({ data: new Date().toISOString(), indicador: params.get('id') || '', aba: params.get('tab') || '', aberturaId: params.get('diagnosticoCarga') || '',
      erro: String(error.message).slice(0, 2048), httpStatus: metadata ? metadata.status : 0, http: metadata || {},
      tempoClienteMs: Math.round(performance.now() - started), consultas: [],
      observacao: 'Falha de rede ou resposta inválida. O navegador não recebeu o diagnóstico SQL do servidor.' });
    error.dashboardSqlRecorded = true;
  }
  function clientError(error, context) {
    if (!enabled || error.dashboardSqlRecorded) return;
    var id = context.aberturaId || currentOpening, o = findOpening(id);
    if (o && (!context.id || o.indicador === context.id)) { complete(id, error); return; }
    keep({ data: new Date().toISOString(), indicador: context.id || '', aba: context.tab || '',
      erro: String(error.message).slice(0, 2048), httpStatus: 0, consultas: [], observacao: 'Erro na apresentação do indicador no navegador.' });
  }
  function read(response, url, started) {
    var headersAt = performance.now(), metadata = { status: response.status,
      tipoConteudo: (response.headers.get('Content-Type') || '').slice(0, 100), tempoAteCabecalhosMs: Math.round(headersAt - started) };
    return response.json().catch(function () {
      var error = new Error('O servidor retornou uma resposta inválida para o indicador (HTTP ' + response.status + ').');
      failure(error, url, started, metadata); throw error;
    }).then(function (data) {
      metadata.tempoCorpoJsonMs = Math.round(performance.now() - headersAt);
      if (!data || typeof data !== 'object' || Array.isArray(data)) {
        var invalid = new Error('O servidor retornou um JSON incompatível com o indicador (HTTP ' + response.status + ').');
        failure(invalid, url, started, metadata); throw invalid;
      }
      if (enabled && response.url && performance.getEntriesByName) {
        var entries = performance.getEntriesByName(response.url), entry = entries[entries.length - 1];
        if (entry) metadata.tamanhoRespostaBytes = entry.encodedBodySize;
      }
      var recorded = receive(data, url, response.status, started, metadata);
      if (!response.ok || data.error) {
        var error = new Error(data.error || 'Não foi possível carregar o indicador (HTTP ' + response.status + ').');
        error.dashboardSqlRecorded = recorded;
        if (!recorded) failure(error, url, started, metadata);
        throw error;
      }
      return data;
    });
  }
  window.addEventListener('error', function (event) {
    if (!enabled || !event.error || !currentOpening) return;
    if (event.filename) {
      var path = new URL(event.filename, location.href).pathname;
      if (path.indexOf(location.pathname.slice(0, location.pathname.lastIndexOf('/') + 1)) !== 0 || /analise-sql\.js$/.test(path)) return;
    }
    clientError(event.error, { aberturaId: currentOpening });
  });
  window.addEventListener('unhandledrejection', function (event) {
    if (enabled && currentOpening && event.reason instanceof Error) clientError(event.reason, { aberturaId: currentOpening });
  });
  function createModal() {
    modal = document.createElement('div');
    modal.id = 'sqlAnalysisModal'; modal.className = 'sql-analysis-modal'; modal.hidden = true;
    modal.setAttribute('role', 'dialog'); modal.setAttribute('aria-modal', 'true'); modal.setAttribute('aria-labelledby', 'sqlAnalysisTitle');
    modal.innerHTML = '<section class="sql-analysis-dialog"><header><div><p class="eyebrow">SOMENTE ROOT</p><h2 id="sqlAnalysisTitle">Análise SQL</h2></div><button type="button" class="modal-close" id="closeSqlAnalysis" aria-label="Fechar análise SQL">×</button></header>' +
      '<p>Acompanha a abertura completa: espera, SQL, etapas e montagem da tela. Registra falhas mesmo depois de o indicador abrir. Até 20 aberturas, 20 requisições e 1 MB nesta aba; recarregar apaga o histórico.</p>' +
      '<div class="sql-analysis-tools"><label>Exibir <select id="sqlAnalysisFilter"><option value="all">Todos os registros</option><option value="errors">Com erro</option><option value="warnings">Com aviso</option><option value="slow">Lentos (≥ 1 segundo)</option></select></label><button type="button" id="copySqlAnalysis">Copiar relatório</button><button type="button" id="clearSqlAnalysis">Limpar histórico</button></div>' +
      '<p id="sqlAnalysisStatus" role="status" aria-live="polite"></p><div id="sqlAnalysisOpenings"></div><div id="sqlAnalysisReports"></div><label for="sqlAnalysisText">Relatório JSON para copiar e enviar</label><textarea id="sqlAnalysisText" readonly spellcheck="false"></textarea></section>';
    document.body.appendChild(modal);
    modal.querySelector('#closeSqlAnalysis').addEventListener('click', close);
    modal.querySelector('#sqlAnalysisFilter').addEventListener('change', render);
    modal.querySelector('#clearSqlAnalysis').addEventListener('click', function () {
      reports = []; openings = []; currentOpening = ''; bytes = 0; refresh();
    });
    modal.querySelector('#copySqlAnalysis').addEventListener('click', copy);
    modal.addEventListener('click', function (event) { if (event.target === modal) close(); });
    modal.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') { event.stopPropagation(); close(); }
      if (event.key !== 'Tab') return;
      var controls = modal.querySelectorAll('button,select,textarea'), first = controls[0], last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });
  }
  function close() { if (modal) modal.hidden = true; if (returnFocus) returnFocus.focus(); }
  function ms(value) { return Number(value || 0).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) + ' ms'; }
  function render() {
    var filter = modal.querySelector('#sqlAnalysisFilter').value;
    function matches(r) { return filter === 'errors' ? hasError(r) || r.estado === 'erro' : filter === 'warnings' ? !!(r.avisos && r.avisos.length) : filter === 'slow' ? isSlow(r) : true; }
    var selected = reports.map(function (r) { return r.data; }).filter(function (r) {
      return matches(r);
    });
    var loads = openings.map(function (o) {
      var copy = Object.assign({}, o);
      if (o.estado === 'carregando') copy.tempoTotalMs = Math.round(performance.now() - o.inicioRelativoMs);
      return copy;
    }).filter(matches);
    var loadContainer = modal.querySelector('#sqlAnalysisOpenings'); loadContainer.textContent = '';
    loads.slice().reverse().forEach(function (o) {
      var details = document.createElement('details'), summary = document.createElement('summary');
      summary.textContent = 'ABERTURA · ' + o.estado.toUpperCase() + ' · ' + o.titulo + ' · total ' + ms(o.tempoTotalMs) + ' · montagem ' + ms(o.tempoMontagemMs) + ' · ' + o.requisicoes + ' requisição(ões)';
      details.appendChild(summary);
      var pre = document.createElement('pre'); pre.textContent = JSON.stringify(o, null, 2); details.appendChild(pre); loadContainer.appendChild(details);
    });
    var container = modal.querySelector('#sqlAnalysisReports'); container.textContent = '';
    modal.querySelector('#sqlAnalysisStatus').textContent = loads.length + ' abertura(s), ' + selected.length + ' requisição(ões). Lento: a partir de 1.000 ms. Valores zerados, sozinhos, não comprovam erro.';
    selected.slice().reverse().forEach(function (report) {
      var details = document.createElement('details'), summary = document.createElement('summary');
      summary.textContent = (hasError(report) ? 'ERRO · ' : report.avisos && report.avisos.length ? 'AVISO · ' : report.httpStatus === 202 ? 'EM ETAPAS · ' : isSlow(report) ? 'LENTO · ' : 'OK · ') + (report.titulo || report.indicador || 'Configuração') + (report.aba ? ' / ' + report.aba : '') + ' · servidor ' + ms(report.tempoServidorMs) + ' · SQL ' + ms(report.tempoSqlMs) + ' · navegador ' + ms(report.tempoClienteMs);
      details.appendChild(summary);
      var info = document.createElement('p');
      info.textContent = (report.erro || 'Consulta concluída') + ' · ' + Number(report.quantidadeConsultas || 0) + ' SQL(s) · fora do SQL ' + ms(report.tempoForaSqlMs) + (report.conexao ? ' · conexão ' + report.conexao : '') + (report.consultasOmitidas ? ' · ' + report.consultasOmitidas + ' consultas omitidas pelo limite; totais preservados' : '');
      details.appendChild(info);
      if ((report.avisos && report.avisos.length) || report.erroPhp) {
        var warnings = document.createElement('pre'); warnings.textContent = JSON.stringify({ avisos: report.avisos || [], erroPhp: report.erroPhp || null }, null, 2); details.appendChild(warnings);
      }
      var queries = (report.consultas || []).slice();
      [report.ultimaConsultaComErro, report.consultaMaisLenta].forEach(function (extra) {
        if (extra && !queries.some(function (q) { return q.ordem === extra.ordem; })) queries.push(extra);
      });
      queries.sort(function (a, b) { return b.tempoMs - a.tempoMs; }).forEach(function (query) {
        var pre = document.createElement('pre');
        pre.textContent = 'SQL #' + query.ordem + ' · ' + ms(query.tempoMs) + ' · linhas retornadas: ' + query.linhasRetornadas + (query.erro ? '\nERRO: ' + query.erro : '') + (query.faseDaFalha ? '\nFase da falha: ' + query.faseDaFalha : '') + (query.mysql ? '\nMySQL: ' + JSON.stringify(query.mysql) : '') + (query.etapasMs ? '\nEtapas (ms): ' + JSON.stringify(query.etapasMs) : '') + '\n' + query.sql + '\nParâmetros: ' + JSON.stringify(query.parametros);
        details.appendChild(pre);
      });
      container.appendChild(details);
    });
    modal.querySelector('#sqlAnalysisText').value = JSON.stringify({ formato: 'hardness-dashboard-analise-sql-v2',
      ambienteNavegador: { agente: navigator.userAgent.slice(0, 300), largura: innerWidth, altura: innerHeight, online: navigator.onLine }, aberturas: loads, registros: selected }, null, 2);
  }
  function copy() {
    render();
    var textarea = modal.querySelector('#sqlAnalysisText'), status = modal.querySelector('#sqlAnalysisStatus');
    function fallback() {
      textarea.focus(); textarea.select();
      var ok = false; try { ok = document.execCommand('copy'); } catch (ignore) {}
      status.textContent = ok ? 'Relatório copiado.' : 'Relatório selecionado. Pressione Ctrl+C para copiar.';
    }
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(textarea.value).then(function () { status.textContent = 'Relatório copiado.'; }, fallback);
    else fallback();
  }
  button.addEventListener('click', function () {
    if (!enabled) return;
    returnFocus = button;
    if (!modal) createModal();
    modal.hidden = false; render(); modal.querySelector('#closeSqlAnalysis').focus();
  });
  window.DashboardSqlDiagnostics = { configure: configure, receive: receive, read: read, failure: failure, clientError: clientError, begin: begin, phase: phase, complete: complete };
}());
