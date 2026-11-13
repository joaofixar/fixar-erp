(function () {
  'use strict';

  var state = { configuration: null, selectedId: '', selectedTab: '', selectedUf: '', paretoRange: null, paretoClass: '', paretoClassTotal: 0, paretoScrollToList: false, loading: false, requestId: 0, listExpanded: false, expandedGroups: {}, expandedMonthlyRepresentatives: {}, access: null, accessMode: 'perfis', accessSubject: '', exportParams: null, detailExportParams: null, worldMapRows: [], worldMapDefinition: null, filterPreferences: {}, cashFlowGranularities: {} };
  var worldMapSourcePromise = null;
  var clientDetail = null;
  var agendaOpening = false;
  var titleEditing = null;
  var titleBulkSaving = false;
  var $ = function (selector) { return document.querySelector(selector); };

  function bindCompanyLogo() {
    var brand = $('#companyBrand');
    var logo = $('#companyLogo');
    if (!brand || !logo) return;
    var show = function () { brand.hidden = false; };
    var hide = function () { brand.hidden = true; };
    logo.addEventListener('load', show);
    logo.addEventListener('error', hide);
    if (logo.complete) {
      if (logo.naturalWidth) show();
      else hide();
    }
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>'"]/g, function (char) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char];
    });
  }

  function icon(name) {
    var icons = {
      chart: '<path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 5-6"/>',
      money: '<circle cx="12" cy="12" r="9"/><path d="M15 8.5c-.7-.5-1.6-.8-2.7-.8-1.6 0-2.7.8-2.7 2s1 1.8 2.8 2.2 2.8 1 2.8 2.2-1.1 2.1-2.9 2.1c-1.1 0-2.2-.3-3.1-1"/><path d="M12 6v12"/>',
      users: '<circle cx="9" cy="8" r="3"/><path d="M3.5 19c.8-3 2.8-4.5 5.5-4.5s4.7 1.5 5.5 4.5"/><path d="M16 8.5a2.5 2.5 0 0 1 0 5"/><path d="M18 15c1.5.7 2.4 2 2.7 4"/>',
      truck: '<path d="M3 6h10v9H3zM13 10h4l3 3v2h-7z"/><circle cx="7" cy="17" r="1.5"/><circle cx="17" cy="17" r="1.5"/>',
      wallet: '<path d="M4 7.5A2.5 2.5 0 0 1 6.5 5H19v14H6.5A2.5 2.5 0 0 1 4 16.5z"/><path d="M4 8h13v5H4"/>'
    };
    return '<svg viewBox="0 0 24 24" aria-hidden="true">' + (icons[name] || icons.chart) + '</svg>';
  }

  function localDate(date) {
    var offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().slice(0, 10);
  }

  function defaultValue(filter) {
    if (filter.valor != null && filter.valor !== '') return filter.valor;
    var today = new Date();
    if (filter.padrao === 'hoje') return localDate(today);
    if (filter.padrao === 'inicio_mes') return localDate(new Date(today.getFullYear(), today.getMonth(), 1));
    if (filter.padrao === 'inicio_ano') return localDate(new Date(today.getFullYear(), 0, 1));
    return filter.padrao == null ? '' : filter.padrao;
  }

  function filterPreferenceTabKey() {
    return state.selectedTab || '_principal';
  }

  function currentFilterPreferences() {
    var indicator = state.filterPreferences[state.selectedId];
    if (!indicator || typeof indicator !== 'object') return {};
    var values = indicator[filterPreferenceTabKey()];
    return values && typeof values === 'object' ? values : {};
  }

  function setCurrentFilterPreferences(values) {
    var tabKey = filterPreferenceTabKey();
    var hasValues = values && Object.keys(values).length > 0;
    if (!state.filterPreferences[state.selectedId]) state.filterPreferences[state.selectedId] = {};
    if (hasValues) state.filterPreferences[state.selectedId][tabKey] = values;
    else delete state.filterPreferences[state.selectedId][tabKey];
    if (!Object.keys(state.filterPreferences[state.selectedId]).length) delete state.filterPreferences[state.selectedId];
  }

  function preferredFilterValue(filter) {
    if (filter.bloqueado === true || filter.persistir === false) return defaultValue(filter);
    var values = currentFilterPreferences();
    var key = 'f_' + filter.id;
    if (!Object.prototype.hasOwnProperty.call(values, key)) return defaultValue(filter);
    return String(values[key] == null ? '' : values[key]);
  }

  function persistCurrentFilters(clear) {
    var values = {};
    var form = $('#indicatorFilters');
    var transientKeys = selectedFilters(selectedDefinition()).filter(function (filter) { return filter.persistir === false; }).map(function (filter) { return 'f_' + filter.id; });
    if (!clear && form && !form.hidden) {
      new FormData(form).forEach(function (value, key) { if (transientKeys.indexOf(key) === -1) values[key] = value; });
    }
    setCurrentFilterPreferences(values);
    return requestPost('data.php?action=saveFilters', { id: state.selectedId, tab: state.selectedTab || '', filters: values });
  }

  function format(value, type) {
    if (type === 'moeda') return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value || 0));
    if (type === 'percentual') return new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 2 }).format(Number(value || 0)) + '%';
    if (type === 'numero') return new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 2 }).format(Number(value || 0));
    if (type === 'data' && /^\d{4}-\d{2}-\d{2}$/.test(String(value || ''))) return String(value).split('-').reverse().join('/');
    return String(value == null ? '' : value);
  }

  function imageSource(value) {
    var source = String(value == null ? '' : value).trim();
    if (!source) return '';
    if (/^https?:\/\//i.test(source) || /^\/(?!\/)/.test(source) || source.indexOf('./') === 0 || source.indexOf('../') === 0) return source;
    if (!/^[a-z][a-z0-9+.-]*:/i.test(source) && source.indexOf('//') !== 0) return source;
    return '';
  }

  function channelLogoSource(channel) {
    var text = String(channel == null ? '' : channel).trim();
    if (!text) return '';
    var utf8 = unescape(encodeURIComponent(text));
    var hexadecimal = '';
    for (var index = 0; index < utf8.length; index++) {
      var parte = utf8.charCodeAt(index).toString(16);
      hexadecimal += parte.length === 1 ? '0' + parte : parte;
    }
    return 'channel-logo.php?canalHex=' + hexadecimal;
  }

  function listCell(row, column) {
    var type = column.formato || 'texto';
    if (type !== 'imagem') return '<td' + (column.destaque === true ? ' class="list-cell-highlight"' : '') + '>' + escapeHtml(format(row[column.campo], type)) + '</td>';

    var source = column.canalLogo ? channelLogoSource(row[column.campoCanal || 'canal']) : imageSource(row[column.campo]);
    var alt = column.campoAlt ? row[column.campoAlt] : (row.descricao || column.titulo || 'Imagem');
    var imageCellClass = (column.classe === 'canal-logo' || column.campo === 'logo') ? ' channel-logo-cell' : '';
    var fallback = '<span class="list-image-placeholder" aria-label="Imagem não disponível">Sem foto</span>';
    if (!source) return '<td class="table-image-cell' + imageCellClass + '"><span class="list-image-wrapper">' + fallback + '</span></td>';
    return '<td class="table-image-cell' + imageCellClass + '"><span class="list-image-wrapper"><img class="list-image" data-list-image src="' + escapeHtml(source) + '" alt="' + escapeHtml(alt) + '" loading="lazy"><span class="list-image-placeholder" hidden aria-label="Imagem não disponível">Sem foto</span></span></td>';
  }

  function showToast(message) {
    var toast = $('#toast');
    toast.textContent = message;
    toast.classList.add('show');
    clearTimeout(showToast.timer);
    showToast.timer = setTimeout(function () { toast.classList.remove('show'); }, 2800);
  }

  function request(url) {
    return fetch(url, { credentials: 'same-origin', cache: 'no-store' }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (data) {
        if (!response.ok) throw new Error(data.error || 'Não foi possível carregar o dashboard.');
        return data;
      });
    });
  }

  function requestPost(url, body) {
    return fetch(url, { method: 'POST', credentials: 'same-origin', cache: 'no-store', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (data) {
        if (!response.ok) throw new Error(data.error || 'Não foi possível salvar as informações.');
        return data;
      });
    });
  }

  var loadingProgressStates = new WeakMap();

  function stopLoadingProgress(root) {
    if (!root) return;
    var progress = root.querySelector('.indicator-loading-progress');
    if (!progress) return;
    var current = loadingProgressStates.get(progress);
    if (current && current.timer) clearInterval(current.timer);
    loadingProgressStates.delete(progress);
  }

  function startLoadingProgress(root) {
    if (!root) return;
    var progress = root.querySelector('.indicator-loading-progress');
    var bar = progress ? progress.querySelector('i') : null;
    if (!progress || !bar) return;
    stopLoadingProgress(root);
    var current = { value: 0, timer: null };
    loadingProgressStates.set(progress, current);
    bar.style.width = '0%';
    window.requestAnimationFrame(function () {
      if (loadingProgressStates.get(progress) !== current) return;
      bar.style.width = '7%';
      current.value = 7;
    });
    current.timer = window.setInterval(function () {
      current.value = Math.min(92, current.value + Math.max(.6, (92 - current.value) * .075));
      bar.style.width = current.value.toFixed(2) + '%';
      if (current.value >= 92) { clearInterval(current.timer); current.timer = null; }
    }, 320);
  }

  function completeLoadingProgress(root) {
    return new Promise(function (resolve) {
      if (!root) { resolve(); return; }
      var progress = root.querySelector('.indicator-loading-progress');
      var bar = progress ? progress.querySelector('i') : null;
      if (!progress || !bar) { resolve(); return; }
      stopLoadingProgress(root);
      bar.style.width = '100%';
      window.setTimeout(resolve, 220);
    });
  }

  function authorizeSession() {
    var match = window.location.hash.match(/(?:^#|&)session=([a-f0-9]{64})(?:&|$)/);
    if (!match) return Promise.resolve();

    window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
    return fetch('session-bridge.php', {
      method: 'POST',
      credentials: 'same-origin',
      cache: 'no-store',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body: 'token=' + encodeURIComponent(match[1])
    }).then(function (response) {
      return response.json().catch(function () { return {}; }).then(function (data) {
        if (!response.ok) throw new Error(data.error || 'Não foi possível autorizar o dashboard.');
      });
    });
  }

  function selectedDefinition() {
    var indicators = state.configuration ? state.configuration.indicadores : [];
    for (var i = 0; i < indicators.length; i++) if (indicators[i].id === state.selectedId) return indicators[i];
    return null;
  }

  function selectedFilters(indicator) {
    if (!indicator) return [];
    if (state.selectedTab && indicator.abas) {
      for (var i = 0; i < indicator.abas.length; i++) {
        if (indicator.abas[i].id === state.selectedTab && Array.isArray(indicator.abas[i].filtros)) return indicator.abas[i].filtros;
      }
    }
    return indicator.filtros || [];
  }

  function setExportAvailability(enabled) {
    Array.prototype.forEach.call(document.querySelectorAll('[data-export]'), function (button) { button.disabled = !enabled; });
  }

  function indicatorParams() {
    var params = new URLSearchParams({ id: state.selectedId });
    if (state.selectedTab) params.set('tab', state.selectedTab);
    if (state.selectedUf) params.set('ufMapa', state.selectedUf);
    if (state.paretoRange) {
      params.set('paretoInicio', state.paretoRange.inicio);
      params.set('paretoFim', state.paretoRange.fim);
    }
    if (state.paretoClass) params.set('paretoClasse', state.paretoClass);
    var form = $('#indicatorFilters');
    if (form && !form.hidden) new FormData(form).forEach(function (value, key) { params.set(key, value); });
    return params;
  }

  function renderMenu() {
    var indicators = state.configuration.indicadores || [];
    $('#indicatorCount').textContent = indicators.length + (indicators.length === 1 ? ' visão' : ' visões');
    $('#indicatorMenu').innerHTML = indicators.map(function (indicator) {
      var active = indicator.id === state.selectedId ? ' active' : '';
      var color = indicator.cor || 'blue';
      return '<button type="button" class="menu-item ' + escapeHtml(color) + active + '" data-id="' + escapeHtml(indicator.id) + '">' +
        '<span class="menu-icon">' + icon(indicator.icone) + '</span><span class="menu-copy"><b>' + escapeHtml(indicator.titulo) + '</b><small>' + escapeHtml(indicator.subtitulo || 'Indicador personalizado') + '</small></span><span class="menu-chevron">›</span></button>';
    }).join('');
    Array.prototype.forEach.call(document.querySelectorAll('[data-id]'), function (button) {
      button.addEventListener('click', function () {
        state.selectedId = button.getAttribute('data-id');
        state.selectedTab = '';
        state.selectedUf = '';
        state.paretoRange = null;
        state.paretoClass = '';
        state.paretoClassTotal = 0;
        state.paretoScrollToList = false;
        state.exportParams = null;
        setExportAvailability(false);
        renderIndicatorHeader(selectedDefinition());
        renderFilters(selectedFilters(selectedDefinition()));
        renderMenu();
        loadIndicator(false);
      });
    });
  }

  function renderIndicatorHeader(indicator) {
    $('#contextTitle').textContent = indicator.titulo;
    $('#contextSubtitle').textContent = indicator.subtitulo || '';
    $('#contextIcon').className = 'context-icon ' + (indicator.cor || 'blue');
    $('#contextIcon').innerHTML = icon(indicator.icone);
    var tabs = indicator.abas || [], target = $('#indicatorTabs'), tabsRow = $('#indicatorTabsRow');
    if (!tabs.length) { state.selectedTab = ''; tabsRow.hidden = !(indicator.card && indicator.card.compacta === true && indicator.card.habilitada !== false); target.hidden = true; target.innerHTML = ''; return; }
    if (!state.selectedTab || !tabs.some(function (tab) { return tab.id === state.selectedTab; })) state.selectedTab = tabs[0].id;
    tabsRow.hidden = false;
    target.hidden = false; target.innerHTML = tabs.map(function (tab) { return '<button type="button" data-tab="' + escapeHtml(tab.id) + '" class="' + (tab.id === state.selectedTab ? 'active' : '') + '">' + escapeHtml(tab.titulo) + '</button>'; }).join('');
    Array.prototype.forEach.call(target.querySelectorAll('[data-tab]'), function (button) { button.addEventListener('click', function () { state.selectedTab = button.getAttribute('data-tab'); state.paretoRange = null; state.paretoClass = ''; state.paretoClassTotal = 0; state.paretoScrollToList = false; state.exportParams = null; setExportAvailability(false); renderIndicatorHeader(indicator); renderFilters(selectedFilters(indicator)); loadIndicator(true); }); });
  }

  function renderFilters(filters) {
    var form = $('#indicatorFilters');
    if (!filters || !filters.length) {
      form.hidden = true;
      form.innerHTML = '';
      return;
    }
    form.hidden = false;
    var allBlocked = filters.every(function (filter) { return filter.bloqueado === true; });
    form.innerHTML = filters.map(function (filter) {
      var id = filter.id;
      var value = preferredFilterValue(filter);
      var blocked = filter.bloqueado === true ? ' disabled' : '';
      var label = '<span>' + escapeHtml(filter.titulo || id) + '</span>';
      var control;
      if (filter.tipo === 'selecao') {
        var options = (filter.obrigatorio ? [] : [{ valor: '', titulo: filter.placeholder || 'Todos' }]).concat(filter.opcoes || []);
        if (value !== '' && !options.some(function (option) { return String(option.valor) === String(value); })) options.splice(1, 0, { valor: value, titulo: value });
        control = '<select' + blocked + (filter.obrigatorio ? ' required' : '') + ' name="f_' + escapeHtml(id) + '">' + options.map(function (option) {
          var selected = String(option.valor) === String(value) ? ' selected' : '';
          return '<option value="' + escapeHtml(option.valor) + '"' + selected + '>' + escapeHtml(option.titulo) + '</option>';
        }).join('') + '</select>';
      } else {
        var type = filter.tipo === 'data' ? 'date' : (filter.tipo === 'numero' ? 'number' : 'search');
        control = '<input type="' + type + '" name="f_' + escapeHtml(id) + '" value="' + escapeHtml(value) + '"' + (filter.obrigatorio ? ' required' : '') + blocked + '>';
      }
      return '<label>' + label + control + '</label>';
    }).join('') + (allBlocked ? '' : '<div class="filter-actions"><button class="primary" type="submit">Aplicar</button><button class="secondary" type="button" id="resetFilters">Limpar</button></div>');
    filters.forEach(function (filter) {
      if (filter.recarregarAoAlterar !== true || filter.bloqueado === true) return;
      var control = form.elements.namedItem('f_' + filter.id);
      if (!control) return;
      control.addEventListener('change', function () {
        if (!form.reportValidity()) return;
        state.selectedUf = '';
        state.paretoRange = null;
        state.paretoClass = '';
        state.paretoClassTotal = 0;
        state.paretoScrollToList = false;
        closeListDetailModal();
        persistCurrentFilters(false).catch(function (error) { showToast(error.message); });
        loadIndicator(true, true);
      });
    });
    var reset = $('#resetFilters');
    if (reset) reset.addEventListener('click', function () {
      state.paretoRange = null;
      state.paretoClass = '';
      state.paretoClassTotal = 0;
      state.paretoScrollToList = false;
      setCurrentFilterPreferences({});
      renderFilters(filters.map(function (filter) {
        return filter.persistir === false ? Object.assign({}, filter, { valor: null }) : filter;
      }));
      persistCurrentFilters(true).catch(function (error) { showToast(error.message); });
      loadIndicator(false, true);
    });
  }

  function renderLoading() {
    $('#indicatorCompactSummary').hidden = true;
    $('#indicatorCompactSummary').innerHTML = '';
    $('#indicatorLoading').hidden = false;
    startLoadingProgress($('#indicatorLoading'));
    $('#indicatorSummary').innerHTML = '';
    $('#indicatorPrimaryList').innerHTML = '';
    $('#indicatorChart').innerHTML = '';
    $('#indicatorCalendar').innerHTML = '';
    $('#indicatorList').innerHTML = '';
  }

  function renderError(error) {
    stopLoadingProgress($('#indicatorLoading'));
    $('#indicatorLoading').hidden = true;
    state.exportParams = null;
    setExportAvailability(false);
    $('#indicatorCompactSummary').hidden = true;
    $('#indicatorCompactSummary').innerHTML = '';
    $('#indicatorSummary').innerHTML = '<article class="error-card"><p class="card-kicker">INDICADOR NÃO CARREGADO</p><h3>' + escapeHtml(error.message) + '</h3><p>Revise a configuração em Configuração &gt; Personalização.</p></article>';
    $('#indicatorPrimaryList').innerHTML = '';
    $('#indicatorChart').innerHTML = '';
    $('#indicatorCalendar').innerHTML = '';
    $('#indicatorList').innerHTML = '';
  }

  function chart(series, definition, valueFormat) {
    if (!definition || definition.habilitado === false) return '';
    var points = (series || []).map(function (item) { return { label: item.rotulo || item.periodo, value: Number(item.valor || 0) }; });
    var title = definition.titulo || 'Evolução no período';
    if (!points.length) return '<article class="wide-card"><p class="card-kicker">GRÁFICO</p><h3>' + escapeHtml(title) + '</h3><p class="empty-state">Não há dados para o período filtrado.</p></article>';
    var width = 820, height = 250, left = 64, right = 22, top = 20, bottom = 52;
    var max = Math.max.apply(Math, points.map(function (point) { return point.value; }).concat([1]));
    var plotWidth = width - left - right, plotHeight = height - top - bottom;
    var step = points.length > 1 ? plotWidth / (points.length - 1) : 0;
    var coords = points.map(function (point, index) {
      return { label: point.label, value: point.value, x: points.length === 1 ? left + (plotWidth / 2) : left + (step * index), y: top + plotHeight - ((point.value / max) * plotHeight) };
    });
    var path = coords.map(function (point, index) { return (index ? 'L' : 'M') + point.x.toFixed(1) + ',' + point.y.toFixed(1); }).join(' ');
    var area = path + ' L' + coords[coords.length - 1].x.toFixed(1) + ',' + (top + plotHeight) + ' L' + coords[0].x.toFixed(1) + ',' + (top + plotHeight) + ' Z';
    var grid = [0, .5, 1].map(function (level) {
      var y = top + plotHeight - plotHeight * level;
      return '<line x1="' + left + '" y1="' + y + '" x2="' + (width - right) + '" y2="' + y + '" class="chart-grid"/><text x="0" y="' + (y + 4) + '" class="chart-label">' + escapeHtml(format(max * level, valueFormat)) + '</text>';
    }).join('');
    var marks = coords.map(function (point) {
      return '<g><circle cx="' + point.x + '" cy="' + point.y + '" r="4.5"><title>' + escapeHtml(point.label + ': ' + format(point.value, valueFormat)) + '</title></circle><text x="' + point.x + '" y="' + (height - 18) + '" text-anchor="middle" class="chart-label">' + escapeHtml(point.label) + '</text></g>';
    }).join('');
    var periodLabel = /^\d{2}\/\d{2}$/.test(String(points[0].label || '')) ? 'Por dia' : 'Por mês';
    return '<article class="wide-card"><header><div><p class="card-kicker">EVOLUÇÃO</p><h3>' + escapeHtml(title) + '</h3></div><span class="chip">' + periodLabel + '</span></header><div class="chart-scroll"><svg class="chart" viewBox="0 0 ' + width + ' ' + height + '">' + grid + '<path d="' + area + '" class="chart-area"/><path d="' + path + '" class="chart-line"/>' + marks + '</svg></div></article>';
  }

  function cashFlowDate(value) {
    var parts = String(value || '').split('-');
    if (parts.length !== 3) return null;
    var date = new Date(Date.UTC(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2])));
    return isNaN(date.getTime()) ? null : date;
  }

  function cashFlowIsoDate(date) {
    return date.toISOString().slice(0, 10);
  }

  function cashFlowShortDate(date) {
    var iso = cashFlowIsoDate(date).split('-');
    return iso[2] + '/' + iso[1];
  }

  function aggregateCashFlowRows(rows, granularity, period) {
    var rangeStart = period && cashFlowDate(period.dataInicio), rangeEnd = period && cashFlowDate(period.dataFim);
    if (rangeStart && rangeEnd) rows = (rows || []).filter(function (row) {
      return row.periodo >= period.dataInicio && row.periodo <= period.dataFim;
    });
    if (granularity === 'day') return (rows || []).map(function (row) {
      return Object.assign({}, row, { dataInicio: row.periodo, dataFim: row.periodo });
    });
    var grouped = {};
    (rows || []).forEach(function (row) {
      var date = cashFlowDate(row.periodo);
      if (!date) return;
      var start = new Date(date.getTime()), end = new Date(date.getTime()), label = '';
      if (granularity === 'week') {
        var daysFromMonday = (date.getUTCDay() + 6) % 7;
        start.setUTCDate(date.getUTCDate() - daysFromMonday);
        end = new Date(start.getTime());
        end.setUTCDate(start.getUTCDate() + 6);
      } else {
        start = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), 1));
        end = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth() + 1, 0));
        label = String(start.getUTCMonth() + 1).padStart(2, '0') + '/' + start.getUTCFullYear();
      }
      if (rangeStart && rangeEnd) {
        if (start < rangeStart) start = new Date(rangeStart.getTime());
        if (end > rangeEnd) end = new Date(rangeEnd.getTime());
      }
      if (granularity === 'week') label = cashFlowShortDate(start) + '–' + cashFlowShortDate(end);
      var period = cashFlowIsoDate(start), key = period + '\n' + String(row.serie || '');
      if (!grouped[key]) grouped[key] = { periodo: period, rotulo: label, serie: row.serie, valor: 0, dataInicio: period, dataFim: cashFlowIsoDate(end) };
      grouped[key].valor += Number(row.valor || 0);
    });
    return Object.keys(grouped).map(function (key) { return grouped[key]; });
  }

  function comparativeBarChart(rows, definition, valueFormat) {
    if (!definition || definition.habilitado === false) return '';
    var granularity = state.cashFlowGranularities[state.selectedId] || 'day';
    var displayedRows = aggregateCashFlowRows(rows, granularity, definition.limitarAoPeriodoFiltrado ? definition.periodoFiltrado : null);
    var title = definition.titulo || 'Previsão diária de entradas e saídas';
    var configuredSeries = Array.isArray(definition.series) ? definition.series : [];
    var series = configuredSeries.map(function (item, index) {
      var color = /^#[0-9a-f]{6}$/i.test(String(item.cor || '')) ? item.cor : (index === 0 ? '#16a477' : '#e04b59');
      return { key: String(item.chave || ''), label: String(item.rotulo || item.chave || ''), color: color };
    }).filter(function (item) { return item.key; });
    if (!series.length) {
      var found = {};
      (rows || []).forEach(function (row) { if (row.serie != null) found[String(row.serie)] = true; });
      series = Object.keys(found).map(function (key, index) { return { key: key, label: key, color: index === 0 ? '#16a477' : '#e04b59' }; });
    }
    var periods = {}, values = {};
    displayedRows.forEach(function (row) {
      var period = String(row.periodo || ''), key = String(row.serie || '');
      if (!period || !key) return;
      periods[period] = { label: String(row.rotulo || period), start: String(row.dataInicio || period), end: String(row.dataFim || period) };
      values[period + '\n' + key] = Number(row.valor || 0);
    });
    var dates = Object.keys(periods).sort();
    if (!dates.length || !series.length) return '<article class="wide-card"><p class="card-kicker">PROJEÇÃO DE CAIXA</p><h3>' + escapeHtml(title) + '</h3><p class="empty-state">Não há títulos em aberto para o período filtrado.</p></article>';
    var width = Math.max(820, dates.length * 94 + 110), height = 326, left = 82, right = 28, top = 40, bottom = 64;
    var plotWidth = width - left - right, plotHeight = height - top - bottom, base = top + plotHeight;
    var maximum = Math.max.apply(Math, dates.reduce(function (items, date) {
      return items.concat(series.map(function (item) { return values[date + '\n' + item.key] || 0; }));
    }, [1]));
    var groupWidth = plotWidth / dates.length, availableWidth = Math.min(groupWidth * .72, 62), barWidth = availableWidth / series.length;
    var detailEnabled = definition.detalhamento && definition.detalhamento.habilitado !== false;
    var gradients = series.map(function (item, index) {
      return '<linearGradient id="cashFlowGradient' + index + '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="' + item.color + '" stop-opacity=".82"/><stop offset="100%" stop-color="' + item.color + '"/></linearGradient>';
    }).join('');
    var grid = [0, .25, .5, .75, 1].map(function (level) {
      var y = base - plotHeight * level;
      return '<line x1="' + left + '" y1="' + y + '" x2="' + (width - right) + '" y2="' + y + '" class="chart-grid"/><text x="' + (left - 10) + '" y="' + (y + 4) + '" text-anchor="end" class="chart-label">' + escapeHtml(calendarValue(maximum * level, valueFormat || 'moeda')) + '</text>';
    }).join('');
    var bars = dates.map(function (date, dateIndex) {
      var center = left + groupWidth * (dateIndex + .5);
      var zone = '<rect class="cash-flow-day-zone" x="' + (left + groupWidth * dateIndex + 4).toFixed(2) + '" y="' + top + '" width="' + Math.max(1, groupWidth - 8).toFixed(2) + '" height="' + plotHeight + '" rx="8"/>';
      var previousLabelY = null;
      var rectangles = series.map(function (item, seriesIndex) {
        var value = values[date + '\n' + item.key] || 0;
        var barHeight = value > 0 && maximum ? Math.max(2, value / maximum * plotHeight) : 0;
        var x = center - availableWidth / 2 + barWidth * seriesIndex;
        var y = base - barHeight;
        var tooltip = periods[date].label + ' · ' + item.label + ': ' + format(value, valueFormat || 'moeda');
        var interaction = detailEnabled && value > 0 ? ' role="button" tabindex="0" data-cash-flow-start="' + escapeHtml(periods[date].start) + '" data-cash-flow-end="' + escapeHtml(periods[date].end) + '" data-cash-flow-period="' + escapeHtml(periods[date].label) + '" data-cash-flow-series="' + escapeHtml(item.key) + '" aria-label="' + escapeHtml(tooltip + '. Clique para ver os títulos considerados.') + '"' : '';
        var labelY = Math.max(12, y - 7);
        if (value > 0 && previousLabelY != null && Math.abs(labelY - previousLabelY) < 14) labelY = Math.max(12, previousLabelY - 14);
        if (value > 0) previousLabelY = labelY;
        var labelX = x + barWidth / 2;
        var labelAnchor = 'middle';
        if (series.length > 1 && seriesIndex === 0) {
          labelX = x + barWidth - 2;
          labelAnchor = 'end';
        } else if (series.length > 1 && seriesIndex === series.length - 1) {
          labelX = x + 2;
          labelAnchor = 'start';
        }
        var valueLabel = value > 0 ? '<text class="cash-flow-bar-value" x="' + labelX.toFixed(2) + '" y="' + labelY.toFixed(2) + '" text-anchor="' + labelAnchor + '">' + escapeHtml(calendarValue(value, valueFormat || 'moeda')) + '</text>' : '';
        return '<g class="cash-flow-bar-group">' + valueLabel + '<rect class="cash-flow-bar' + (interaction ? ' clickable' : '') + '" x="' + (x + 2).toFixed(2) + '" y="' + y.toFixed(2) + '" width="' + Math.max(1, barWidth - 4).toFixed(2) + '" height="' + barHeight.toFixed(2) + '" rx="6" fill="url(#cashFlowGradient' + seriesIndex + ')"' + interaction + '><title>' + escapeHtml(tooltip) + '</title></rect></g>';
      }).join('');
      return '<g class="cash-flow-day-group">' + zone + rectangles + '<text x="' + center.toFixed(2) + '" y="' + (base + 24) + '" text-anchor="middle" class="chart-label cash-flow-date-label">' + escapeHtml(periods[date].label) + '</text></g>';
    }).join('');
    var legend = series.map(function (item) { return '<span><i style="background:' + item.color + '"></i>' + escapeHtml(item.label) + '</span>'; }).join('');
    var totals = series.map(function (item) {
      return { label: item.label, color: item.color, value: dates.reduce(function (sum, date) { return sum + (values[date + '\n' + item.key] || 0); }, 0) };
    });
    var summary = totals.map(function (item) { return '<div class="cash-flow-metric"><i style="background:' + item.color + '"></i><span>' + escapeHtml(item.label) + '<b>' + escapeHtml(format(item.value, valueFormat || 'moeda')) + '</b></span></div>'; }).join('');
    var showBalance = totals.length === 2 && definition.exibirSaldoProjetado !== false;
    if (showBalance) {
      var balance = totals[0].value - totals[1].value;
      summary += '<div class="cash-flow-metric balance ' + (balance < 0 ? 'negative' : 'positive') + '"><span>Saldo projetado<b>' + escapeHtml(format(balance, valueFormat || 'moeda')) + '</b></span></div>';
    }
    var help = definition.ajuda ? contextualHelp(definition.ajuda, title) : '';
    var summaryClass = totals.length === 2 && !showBalance ? ' cash-flow-summary--two' : '';
    var detailHint = detailEnabled ? '<p class="cash-flow-detail-hint">Clique em uma barra para consultar os títulos que formam o valor.</p>' : '';
    var granularityOptions = [{ key: 'day', label: 'Dia' }, { key: 'week', label: 'Semana' }, { key: 'month', label: 'Mês' }].map(function (option) {
      return '<button type="button" data-cash-flow-granularity="' + option.key + '" class="' + (granularity === option.key ? 'active' : '') + '" aria-pressed="' + (granularity === option.key ? 'true' : 'false') + '">' + option.label + '</button>';
    }).join('');
    return '<article class="wide-card cash-flow-card"><header><div><p class="card-kicker">PROJEÇÃO DE CAIXA</p><h3>' + escapeHtml(title) + help + '</h3></div><div class="cash-flow-granularity" role="group" aria-label="Agrupar projeção por">' + granularityOptions + '</div></header><div class="cash-flow-summary' + summaryClass + '">' + summary + '</div><div class="cash-flow-toolbar"><div class="cash-flow-legend">' + legend + '</div>' + detailHint + '</div><div class="chart-scroll"><svg class="chart cash-flow-chart" viewBox="0 0 ' + width + ' ' + height + '" style="min-width:' + width + 'px" role="img" aria-label="Comparativo de contas a receber, contas a pagar e créditos por ' + (granularity === 'day' ? 'dia' : granularity === 'week' ? 'semana' : 'mês') + '"><defs>' + gradients + '</defs><rect class="cash-flow-plot" x="' + left + '" y="' + top + '" width="' + plotWidth + '" height="' + plotHeight + '" rx="10"/>' + grid + '<line x1="' + left + '" y1="' + base + '" x2="' + (width - right) + '" y2="' + base + '" class="cash-flow-baseline"/>' + bars + '</svg></div></article>';
  }

  function paretoChart(series, definition, filters) {
    if (!definition || definition.habilitado === false) return '';
    var points = series || [], title = definition.titulo || 'Curva de Pareto dos produtos';
    var dates = {};
    (filters || []).forEach(function (filter) { dates[filter.id] = filter.valor; });
    var period = dates.dataInicio && dates.dataFim ? format(dates.dataInicio, 'data') + ' a ' + format(dates.dataFim, 'data') : 'Período filtrado';
    var header = '<header><div><p class="card-kicker">CONCENTRAÇÃO DAS VENDAS</p><h3>' + escapeHtml(title) + '</h3></div><span class="chip">' + escapeHtml(period) + '</span></header>';
    if (!points.length) return '<article class="wide-card pareto-card">' + header + '<p class="empty-state">Não há vendas de produtos para os filtros selecionados.</p></article>';

    var classes = ['A', 'B', 'C'], colors = { A: '#2860dc', B: '#14a7a0', C: '#8b5cf6' };
    var total = 0, count = 0, maximum = 0, largestGroup = 1;
    var classTotals = { A: { value: 0, count: 0 }, B: { value: 0, count: 0 }, C: { value: 0, count: 0 } };
    points.forEach(function (point) {
      total += Number(point.valor || 0);
      count += Number(point.produtos || 0);
      maximum = Math.max(maximum, Number(point.valor || 0));
      largestGroup = Math.max(largestGroup, Number(point.produtos || 0));
      classes.forEach(function (key) { classTotals[key].value += Number(point['valor' + key] || 0); classTotals[key].count += Number(point['produtos' + key] || 0); });
    });
    if (!(total > 0)) return '<article class="wide-card pareto-card">' + header + '<p class="empty-state">Não há valor vendido positivo para os filtros selecionados.</p></article>';
    var summary = classes.map(function (key) {
      var item = classTotals[key];
      var selectedClass = state.paretoClass === key;
      return '<button type="button" class="pareto-class' + (selectedClass ? ' selected' : '') + '" aria-pressed="' + !!selectedClass + '" data-pareto-class="' + key + '"><span><i style="background:' + colors[key] + '"></i>Classe ' + key + '</span><strong>' + escapeHtml(format(item.count, 'numero')) + ' produtos</strong><small>' + escapeHtml(format(count ? item.count / count * 100 : 0, 'percentual')) + ' dos produtos · ' + escapeHtml(format(item.value / total * 100, 'percentual')) + ' do valor vendido</small></button>';
    }).join('');
    var explanation = largestGroup > 1
      ? 'Todos os ' + format(count, 'numero') + ' produtos estão representados. Cada barra soma até ' + format(largestGroup, 'numero') + ' produtos consecutivos do ranking; as cores mostram a participação de cada classe no grupo.'
      : 'Cada barra representa um produto, do maior para o menor valor vendido. Todos os ' + format(count, 'numero') + ' produtos filtrados estão representados.';
    var width = Math.max(940, points.length * 24 + 150), height = 390, left = 110, right = 62, top = 32, bottom = 68;
    var plotWidth = width - left - right, plotHeight = height - top - bottom, step = plotWidth / points.length;
    var barWidth = Math.min(46, step * .72), base = top + plotHeight;
    var yPercent = function (value) { return base - Math.max(0, Math.min(100, value)) / 100 * plotHeight; };
    var grid = [0, .25, .5, .75, 1].map(function (level) {
      var y = base - level * plotHeight;
      return '<line x1="' + left + '" y1="' + y + '" x2="' + (width - right) + '" y2="' + y + '" class="chart-grid"/><text x="' + (left - 10) + '" y="' + (y + 4) + '" text-anchor="end" class="chart-label">' + escapeHtml(format(maximum * level, 'moeda')) + '</text>';
    }).join('');
    var percentLabels = [0, 25, 50, 80, 95, 100].map(function (value) {
      return '<text x="' + (width - right + 10) + '" y="' + (yPercent(value) + 4) + '" class="chart-label">' + value + '%</text>';
    }).join('');
    var thresholds = [80, 95].map(function (value) {
      return '<line x1="' + left + '" y1="' + yPercent(value) + '" x2="' + (width - right) + '" y2="' + yPercent(value) + '" class="pareto-threshold"/>';
    }).join('');
    var path = 'M' + left + ',' + base, labelEvery = Math.max(1, Math.ceil(points.length / 12));
    var cumulativeBefore = 0;
    var bars = points.map(function (point, index) {
      var x = left + step * (index + .5), accumulated = Number(point.acumulado || 0);
      var grouped = Number(point.produtos) > 1;
      var inicio = Number(point.posicaoInicio || 0), fim = Number(point.posicaoFim || 0);
      var selected = state.paretoRange && state.paretoRange.inicio === inicio && state.paretoRange.fim === fim;
      var label = grouped ? point.posicaoInicio + '–' + point.posicaoFim : String(point.codigo || '');
      var name = grouped ? 'Posições ' + label + ' · ' + format(point.produtos, 'numero') + ' produtos' : String(point.codigo || '') + ' · ' + String(point.descricao || '');
      var classDetail = classes.filter(function (key) { return Number(point['produtos' + key]) > 0; }).map(function (key) { return 'Classe ' + key + ': ' + format(point['produtos' + key], 'numero') + ' produto(s), ' + format(point['valor' + key], 'moeda'); }).join('; ');
      var detail = name + '\nValor vendido: ' + format(point.valor, 'moeda') + ' · Participação: ' + format(Number(point.valor) / total * 100, 'percentual') + '\nAcumulado: ' + format(accumulated, 'percentual') + '\n' + classDetail;
      [80, 95].forEach(function (cut) { if (cumulativeBefore < cut && accumulated >= cut) detail += '\n' + (grouped ? 'Este grupo' : 'Este produto') + ' atinge o corte de ' + cut + '%.'; });
      cumulativeBefore = accumulated;
      path += ' L' + x.toFixed(2) + ',' + yPercent(accumulated).toFixed(2);
      var stacked = 0;
      var rectangles = classes.map(function (key) {
        var value = Number(point['valor' + key] || 0), barHeight = value / maximum * plotHeight;
        var y = base - stacked - barHeight;
        stacked += barHeight;
        return '<rect x="' + (x - barWidth / 2).toFixed(2) + '" y="' + y.toFixed(2) + '" width="' + barWidth.toFixed(2) + '" height="' + barHeight.toFixed(2) + '" fill="' + colors[key] + '"/>';
      }).join('');
      var axisLabel = index % labelEvery === 0 || index === points.length - 1
        ? '<text x="' + x.toFixed(2) + '" y="' + (base + 22) + '" text-anchor="middle" class="chart-label">' + escapeHtml(label.length > 16 ? label.slice(0, 15) + '…' : label) + '</text>' : '';
      return '<g class="pareto-point' + (selected ? ' selected' : '') + '" tabindex="0" role="button" aria-pressed="' + !!selected + '" aria-label="' + escapeHtml(detail + '\nClique para listar estes produtos.') + '" data-pareto-detail-text="' + escapeHtml(detail) + '" data-pareto-inicio="' + inicio + '" data-pareto-fim="' + fim + '"><title>' + escapeHtml(detail + '\nClique para listar estes produtos.') + '</title><rect class="pareto-hit" x="' + (x - step / 2).toFixed(2) + '" y="' + top + '" width="' + step.toFixed(2) + '" height="' + plotHeight + '" fill="transparent"/>' + rectangles + '<circle cx="' + x.toFixed(2) + '" cy="' + yPercent(accumulated).toFixed(2) + '" r="3" fill="#b45309"/>' + axisLabel + '</g>';
    }).join('');
    path += ' L' + (width - right) + ',' + yPercent(Number(points[points.length - 1].acumulado || 0)).toFixed(2);
    var axes = '<text x="' + left + '" y="16" class="chart-label">Valor vendido (R$)</text><text x="' + (width - right) + '" y="16" text-anchor="end" class="chart-label">Acumulado (%)</text><text x="' + (left + plotWidth / 2) + '" y="' + (height - 12) + '" text-anchor="middle" class="chart-label">' + (largestGroup > 1 ? 'Posições consecutivas do ranking de produtos' : 'Código do produto · maior para menor valor vendido') + '</text>';
    return '<article class="wide-card pareto-card">' + header + '<div class="pareto-summary">' + summary + '</div><p class="pareto-explanation">' + escapeHtml(explanation) + '</p><div class="pareto-legend"><span><i class="pareto-line-key"></i>Percentual acumulado</span><span><i class="pareto-threshold-key"></i>Cortes de 80% e 95% (eixo direito)</span></div><div class="chart-scroll"><svg class="pareto-chart" viewBox="0 0 ' + width + ' ' + height + '" style="min-width:' + width + 'px" role="group" aria-label="Valores vendidos por posição no ranking e percentual acumulado">' + axes + grid + percentLabels + thresholds + bars + '<path d="' + path + '" class="pareto-line"/></svg></div><p class="pareto-detail" data-pareto-detail aria-live="polite">Passe o mouse, toque ou use Tab nas barras para consultar os detalhes. Clique em uma barra para ver seus produtos na listagem.</p></article>';
  }

  function bindParetoDetails() {
    var target = $('#indicatorChart'), detail = target.querySelector('[data-pareto-detail]');
    if (!detail) return;
    Array.prototype.forEach.call(target.querySelectorAll('[data-pareto-detail-text]'), function (point) {
      var show = function () { detail.textContent = point.getAttribute('data-pareto-detail-text'); };
      var select = function () {
        var inicio = Number(point.getAttribute('data-pareto-inicio'));
        var fim = Number(point.getAttribute('data-pareto-fim'));
        if (!(inicio > 0) || fim < inicio) return;
        state.paretoRange = { inicio: inicio, fim: fim };
        state.paretoClass = '';
        state.paretoClassTotal = 0;
        state.paretoScrollToList = true;
        loadIndicator(true);
      };
      point.addEventListener('mouseenter', show);
      point.addEventListener('focus', show);
      point.addEventListener('click', select);
      point.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); select(); } });
    });
    Array.prototype.forEach.call(target.querySelectorAll('[data-pareto-class]'), function (card) {
      card.addEventListener('click', function () {
        var selectedClass = card.getAttribute('data-pareto-class');
        if (!/^[ABC]$/.test(selectedClass)) return;
        state.paretoRange = null;
        state.paretoClass = selectedClass;
        state.paretoClassTotal = 0;
        state.paretoScrollToList = true;
        loadIndicator(true);
      });
    });
  }

  function groupedChannelCharts(groups, definition, valueFormat, filters) {
    if (!definition || definition.habilitado === false) return '';
    if (!groups || !groups.length) return '<article class="wide-card"><p class="card-kicker">VISÃO GRÁFICA</p><h3>' + escapeHtml(definition.titulo || 'Desempenho por canal') + '</h3><p class="empty-state">Não há dados para o período filtrado.</p></article>';
    var colors = ['#2c62df', '#f59e0b', '#16a477', '#7c5ce6', '#e65c49', '#0ea5a7', '#bd4ed7', '#64748b'];
    var availableDates = [];
    groups.forEach(function (group) {
      (group.serie || []).forEach(function (item) {
        var key = String(item.data || '').slice(0, 10);
        if (/^\d{4}-\d{2}-\d{2}$/.test(key)) availableDates.push(key);
      });
    });
    availableDates.sort();
    if (!availableDates.length) return '';

    var firstDate = new Date(availableDates[0] + 'T00:00:00');
    var lastDate = new Date(availableDates[availableDates.length - 1] + 'T00:00:00');
    (filters || []).forEach(function (filter) {
      var filterDate = calendarDate(defaultValue(filter));
      if (!filterDate) return;
      if (filter.id === 'dataInicio') firstDate = filterDate;
      if (filter.id === 'dataFim') lastDate = filterDate;
    });
    if (lastDate < firstDate) return '';
    var periodDays = Math.round((lastDate - firstDate) / 86400000) + 1;
    var grouping = periodDays > 180 ? 'month' : (periodDays > 62 ? 'week' : 'day');
    var groupingLabel = grouping === 'month' ? 'mês' : (grouping === 'week' ? 'semana' : 'dia');
    var groupingLabelPlural = grouping === 'month' ? 'meses' : (grouping === 'week' ? 'semanas' : 'dias');
    var pointWidth = grouping === 'month' ? 52 : (grouping === 'week' ? 46 : 30);
    var dateKey = function (date) {
      return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
    };
    var bucketDate = function (date) {
      var bucket = new Date(date.getFullYear(), date.getMonth(), date.getDate());
      if (grouping === 'month') return new Date(bucket.getFullYear(), bucket.getMonth(), 1);
      if (grouping === 'week') bucket.setDate(bucket.getDate() - ((bucket.getDay() + 6) % 7));
      return bucket;
    };
    var incrementBucket = function (date) {
      var next = new Date(date);
      if (grouping === 'month') next.setMonth(next.getMonth() + 1);
      else next.setDate(next.getDate() + (grouping === 'week' ? 7 : 1));
      return next;
    };
    var bucketLabel = function (date) {
      if (grouping === 'month') return String(date.getMonth() + 1).padStart(2, '0') + '/' + date.getFullYear();
      var shortDate = String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0');
      return grouping === 'week' ? 'Sem. ' + shortDate : shortDate;
    };
    var bucketDates = [];
    for (var bucketCursor = bucketDate(firstDate); bucketCursor <= bucketDate(lastDate); bucketCursor = incrementBucket(bucketCursor)) bucketDates.push(new Date(bucketCursor));
    var subtitle = 'Valores agrupados por ' + groupingLabel + '. As barras mostram o total e a linha mostra a média móvel dos últimos três ' + groupingLabelPlural + '.';

    var cards = groups.map(function (group, index) {
      var valuesByBucket = {};
      (group.serie || []).forEach(function (item) {
        var itemKey = String(item.data || '').slice(0, 10);
        if (!/^\d{4}-\d{2}-\d{2}$/.test(itemKey)) return;
        var itemBucket = dateKey(bucketDate(new Date(itemKey + 'T00:00:00')));
        valuesByBucket[itemBucket] = Number(valuesByBucket[itemBucket] || 0) + Number(item.valor || 0);
      });
      var points = bucketDates.map(function (date) { return { date: date, key: dateKey(date), label: bucketLabel(date), value: Number(valuesByBucket[dateKey(date)] || 0) }; });
      var running = 0;
      points.forEach(function (point, pointIndex) {
        running += point.value;
        if (pointIndex >= 3) running -= points[pointIndex - 3].value;
        point.average = running / Math.min(pointIndex + 1, 3);
      });
      var maximum = Math.max.apply(Math, points.map(function (point) { return Math.max(point.value, point.average); }).concat([1]));
      var width = Math.max(620, points.length * pointWidth + 92), height = 330, left = 74, right = 18, top = 24, bottom = 58, plotWidth = width - left - right, plotHeight = height - top - bottom;
      var step = plotWidth / points.length, color = colors[index % colors.length];
      var y = function (value) { return top + plotHeight - ((value / maximum) * plotHeight); };
      var line = points.map(function (point, pointIndex) { return (pointIndex ? 'L' : 'M') + (left + step * pointIndex + step / 2).toFixed(1) + ',' + y(point.average).toFixed(1); }).join(' ');
      var grid = [0, .25, .5, .75, 1].map(function (level) {
        var gridY = top + plotHeight - plotHeight * level;
        return '<line x1="' + left + '" y1="' + gridY + '" x2="' + (width - right) + '" y2="' + gridY + '" class="chart-grid"/><text x="' + (left - 10) + '" y="' + (gridY + 4) + '" text-anchor="end" class="chart-label">' + escapeHtml(calendarValue(maximum * level, valueFormat)) + '</text>';
      }).join('');
      var bars = points.map(function (point, pointIndex) {
        var barHeight = Math.max(0, (point.value / maximum) * plotHeight), barWidth = Math.max(7, Math.min(34, step * .62)), barX = left + step * pointIndex + (step - barWidth) / 2;
        return '<rect x="' + barX.toFixed(1) + '" y="' + (top + plotHeight - barHeight).toFixed(1) + '" width="' + barWidth.toFixed(1) + '" height="' + barHeight.toFixed(1) + '" rx="3" fill="' + color + '"><title>' + escapeHtml(point.label + ': ' + format(point.value, valueFormat)) + '</title></rect>';
      }).join('');
      var labelInterval = Math.max(1, Math.ceil(points.length / 9));
      var labels = points.map(function (point, pointIndex) {
        if (pointIndex % labelInterval !== 0 && pointIndex !== points.length - 1) return '';
        return '<text x="' + (left + step * pointIndex + step / 2).toFixed(1) + '" y="' + (height - 19) + '" text-anchor="middle" class="chart-label">' + escapeHtml(point.label) + '</text>';
      }).join('');
      return '<article class="channel-chart-card"><header><span class="channel-chart-dot" style="background:' + color + '"></span><div><h4>' + escapeHtml(group.grupo || 'Não informado') + '</h4><small>Total no período</small></div><b>' + escapeHtml(format(group.total, valueFormat)) + '</b></header><div class="chart-scroll"><svg class="channel-chart" viewBox="0 0 ' + width + ' ' + height + '" style="min-width:' + width + 'px" role="img" aria-label="Evolução de ' + escapeHtml(group.grupo || 'Não informado') + ' por ' + groupingLabel + '">' + grid + bars + '<path d="' + line + '" class="channel-average channel-average-trend"/>' + labels + '</svg></div><footer><span><i style="background:' + color + '"></i>Valor por ' + groupingLabel + '</span><span><i class="average-trend"></i>Média móvel de 3 ' + groupingLabelPlural + '</span></footer></article>';
    }).join('');
    var sectionTitle = definition.titulo || 'Evolução por situação';
    if (grouping !== 'day' && /di[aá]ria/i.test(sectionTitle)) sectionTitle = sectionTitle.replace(/di[aá]ria/i, grouping === 'month' ? 'mensal' : 'semanal');
    return '<section class="group-chart-section"><header><div><p class="card-kicker">VISÃO GRÁFICA</p><h3>' + escapeHtml(sectionTitle) + '</h3><p>' + escapeHtml(subtitle) + '</p></div><span class="chip">' + escapeHtml(periodDays) + ' dias analisados</span></header><div class="group-chart-grid">' + cards + '</div></section>';
  }

  function detailTable(rows, definition, selectedTitles) {
    var columns = definition.colunas || [];
    var selectable = definition.modal === true && definition.edicaoTitulo && definition.edicaoTitulo.habilitado === true;
    var totalColumns = columns.length + (selectable ? 1 : 0);
    selectedTitles = selectedTitles || {};
    var sectionField = definition.campoSecao || '';
    var rowHighlight = definition.destaqueLinha || {};
    var head = (selectable ? '<th class="detail-title-check"><input type="checkbox" data-title-select-all aria-label="Selecionar todos os títulos em aberto"></th>' : '')
      + columns.map(function (column) { return '<th>' + escapeHtml(column.titulo || column.campo) + '</th>'; }).join('');
    var currentSection = null;
    var body = rows.length ? rows.map(function (detailRow) {
      var section = sectionField ? String(detailRow[sectionField] == null || detailRow[sectionField] === '' ? 'Outros' : detailRow[sectionField]) : '';
      var sectionRow = sectionField && section !== currentSection ? '<tr class="group-detail-section"><td colspan="' + totalColumns + '">' + escapeHtml(section) + '</td></tr>' : '';
      var highlightValue = rowHighlight.campo ? detailRow[rowHighlight.campo] : null;
      var highlighted = rowHighlight.campo && String(highlightValue == null ? '' : highlightValue) === String(rowHighlight.valor == null ? '' : rowHighlight.valor);
      var rowClass = highlighted ? ' class="detail-row-danger"' : '';
      currentSection = section;
      var canSelect = detailRow.tituloEditavel === 'S' && /^[1-9][0-9]{0,9}$/.test(String(detailRow.tituloId || ''));
      var checkbox = selectable ? '<td class="detail-title-check">' + (canSelect ? '<input type="checkbox" data-title-select="' + escapeHtml(detailRow.tituloId)
        + '" aria-label="Selecionar título ' + escapeHtml(detailRow.documento || detailRow.tituloId) + '"' + (selectedTitles[String(detailRow.tituloId)] ? ' checked' : '') + '>' : '') + '</td>' : '';
      return sectionRow + '<tr' + rowClass + '>' + checkbox + columns.map(function (column) {
        var agenda = definition.agenda || {};
        var editable = detailRow.tituloEditavel === 'S' && /^[1-9][0-9]{0,9}$/.test(String(detailRow.tituloId || ''));
        var edit = definition.edicaoTitulo || {};
        if (definition.modal === true && edit.habilitado === true && editable
            && (column.campo === 'statusTituloDescricao' || column.campo === 'dataVencimento')) {
          var field = column.campo === 'dataVencimento' ? 'dataVencimento' : 'statusTitulo';
          var value = column.campo === 'dataVencimento' ? format(detailRow[column.campo], 'data') : detailRow[column.campo];
          return '<td><button type="button" class="detail-title-edit" data-title-edit="' + field + '" data-title-id="' + escapeHtml(detailRow.tituloId)
            + '" aria-label="Alterar ' + escapeHtml(column.titulo || column.campo) + ' do título ' + escapeHtml(detailRow.documento || detailRow.tituloId) + '">'
            + escapeHtml(value || 'Selecionar') + '<span aria-hidden="true"> ▾</span></button></td>';
        }
        if (definition.modal === true && agenda.habilitarInclusao === true && column.campo === (agenda.campoMensagem || 'agenda')) {
          var button = editable && Number(detailRow.clienteId) > 0 && Number(detailRow.empresaTituloId) > 0
            ? '<button type="button" class="detail-agenda-add" data-agenda-title="' + escapeHtml(detailRow.tituloId) + '" aria-label="Incluir agenda no título ' + escapeHtml(detailRow.documento || detailRow.tituloId) + '">Incluir</button>' : '';
          return '<td class="detail-agenda-cell"><div>' + button + '<span>' + escapeHtml(detailRow[column.campo]) + '</span></div></td>';
        }
        return listCell(detailRow, column);
      }).join('') + '</tr>';
    }).join('') : '<tr><td colspan="' + totalColumns + '" class="empty-state">Nenhuma nota encontrada.</td></tr>';
    return '<div class="table-scroll"><table><thead><tr>' + head + '</tr></thead><tbody>' + body + '</tbody></table></div>';
  }

  function detailLoading(message) {
    return '<div class="detail-loading" role="status" aria-live="polite" aria-busy="true"><div><strong>Carregando detalhamento</strong><small>' + escapeHtml(message || 'Consultando os dados. Aguarde um momento.') + '</small><span class="indicator-loading-progress" role="progressbar" aria-label="Carregamento em andamento"><i></i></span></div></div>';
  }

  function showDetailLoading(content, message) {
    content.innerHTML = detailLoading(message);
    startLoadingProgress(content);
  }

  function calendarDate(value) {
    var match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})/);
    return match ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3])) : null;
  }

  function calendarKey(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
  }

  function calendarValue(value, type) {
    var number = Number(value || 0), absolute = Math.abs(number), prefix = type === 'moeda' ? 'R$ ' : '';
    if (absolute >= 1000000) return prefix + (number / 1000000).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + ' mi';
    if (absolute >= 1000) return prefix + (number / 1000).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + ' mil';
    return type === 'moeda' ? format(number, 'moeda') : format(number, type || 'numero');
  }

  function calendarColor(color) {
    return ['red', 'yellow', 'orange', 'blue', 'green'].indexOf(color) !== -1 ? color : 'blue';
  }

  function calendarBands(rows, definition) {
    var configured = Array.isArray(definition.faixas) ? definition.faixas.filter(function (band) { return band; }) : [];
    if (configured.length) return configured.map(function (band) { return { ate: band.ate == null ? Infinity : Number(band.ate), cor: calendarColor(band.cor), rotulo: band.rotulo || (band.ate == null ? 'Maior intensidade' : ('Até ' + calendarValue(band.ate, definition.formato || 'moeda'))) }; }).sort(function (a, b) { return a.ate - b.ate; });
    var maximum = Math.max.apply(Math, rows.map(function (row) { return Number(row.valor || 0); }).concat([1]));
    return ['red', 'yellow', 'orange', 'blue', 'green'].map(function (color, index) {
      var limit = maximum * ((index + 1) / 5);
      return { ate: limit, cor: color, rotulo: index === 4 ? 'Maior intensidade' : ('Até ' + calendarValue(limit, definition.formato || 'moeda')) };
    });
  }

  function calendarRange(rows, filters, definition) {
    var start = null, end = null;
    if (definition.periodo === 'ultimos_30_dias') {
      var today = new Date();
      end = new Date(today.getFullYear(), today.getMonth(), today.getDate());
      start = new Date(end.getFullYear(), end.getMonth(), end.getDate() - 29);
      return { start: start, end: end };
    }
    if (definition.periodo === 'proximos_30_dias') {
      var current = new Date();
      start = new Date(current.getFullYear(), current.getMonth(), current.getDate());
      end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 29);
      return { start: start, end: end };
    }
    (filters || []).forEach(function (filter) { if (filter.id === 'dataInicio') start = calendarDate(filter.valor); if (filter.id === 'dataFim') end = calendarDate(filter.valor); });
    var dates = rows.map(function (row) { return calendarDate(row.data); }).filter(Boolean).sort(function (a, b) { return a - b; });
    if (!start && dates.length) start = dates[0];
    if (!end && dates.length) end = dates[dates.length - 1];
    return start && end && start <= end ? { start: start, end: end } : null;
  }

  function calendar(rows, definition, filters) {
    if (!definition || definition.habilitado === false) return '';
    var title = definition.titulo || 'Calendário de vendas', range = calendarRange(rows, filters, definition);
    if (!range) return '<article class="calendar-card"><header><div><p class="card-kicker">CALENDÁRIO</p><h3>' + escapeHtml(title) + '</h3><p>' + escapeHtml(definition.subtitulo || 'Período selecionado') + '</p></div></header><p class="empty-state">Não há dados para o período filtrado.</p></article>';
    var values = {}, bands = calendarBands(rows, definition), counts = bands.map(function () { return 0; });
    rows.forEach(function (row) { if (calendarDate(row.data)) values[String(row.data).slice(0, 10)] = Number(row.valor || 0); });
    var gridStart = new Date(range.start.getFullYear(), range.start.getMonth(), range.start.getDate() - range.start.getDay());
    var gridEnd = new Date(range.end.getFullYear(), range.end.getMonth(), range.end.getDate() + (6 - range.end.getDay()));
    var days = [], current = new Date(gridStart);
    while (current <= gridEnd) {
      var key = calendarKey(current), inside = current >= range.start && current <= range.end, hasValue = Object.prototype.hasOwnProperty.call(values, key), bandIndex = -1;
      if (hasValue) for (var i = 0; i < bands.length; i++) if (values[key] <= bands[i].ate || i === bands.length - 1) { bandIndex = i; counts[i]++; break; }
      var dayClass = !inside ? ' outside' : (!hasValue ? ' empty' : ' heat-' + bands[bandIndex].cor);
      var colorIndicator = hasValue ? '<i class="calendar-day-indicator" aria-hidden="true"></i>' : '';
      var detail = hasValue ? '<small>' + escapeHtml(calendarValue(values[key], definition.formato || 'moeda')) + '</small>' : '';
      var tooltip = hasValue ? ' title="' + escapeHtml(key + ': ' + format(values[key], definition.formato || 'moeda')) + '"' : '';
      var detailDefinition = definition.detalhamento || {};
      var clickable = hasValue && detailDefinition.habilitado !== false && Array.isArray(detailDefinition.colunas) && detailDefinition.colunas.length;
      var interaction = clickable ? ' data-calendar-id="' + escapeHtml(definition.id || '') + '" data-calendar-date="' + escapeHtml(key) + '" tabindex="0" role="button" aria-label="Ver títulos de ' + escapeHtml(format(key, 'data')) + '"' : '';
      days.push('<div class="calendar-day' + dayClass + (clickable ? ' clickable' : '') + '"' + tooltip + interaction + '><span class="calendar-day-heading"><b>' + (inside ? current.getDate() : '') + '</b>' + colorIndicator + '</span>' + detail + '</div>');
      current.setDate(current.getDate() + 1);
    }
    var legend = bands.map(function (band, index) { return '<span><i class="heat-' + band.cor + '"></i>' + escapeHtml(band.rotulo) + '<b>' + counts[index] + '</b></span>'; }).join('');
    return '<article class="calendar-card"><header><div><p class="card-kicker">CALENDÁRIO</p><h3>' + escapeHtml(title) + '</h3><p>' + escapeHtml(definition.subtitulo || 'Período selecionado') + '</p></div></header><div class="calendar-weekdays"><span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span></div><div class="calendar-days">' + days.join('') + '</div><footer class="calendar-legend"><span>Legenda:</span>' + legend + '</footer></article>';
  }

  function calendars(results, definitions, filters) {
    if (!definitions || !definitions.length) return '';
    var cards = definitions.map(function (definition, index) { return calendar((results || [])[index] || [], definition, filters); }).filter(Boolean);
    return cards.length ? '<section class="calendars-grid">' + cards.join('') + '</section>' : '';
  }

  function calendarDefinition(indicator, calendarId) {
    var definitions = indicator && Array.isArray(indicator.calendarios) ? indicator.calendarios : [];
    for (var index = 0; index < definitions.length; index++) if (String(definitions[index].id || '') === String(calendarId)) return definitions[index];
    return null;
  }

  function openCalendarDetailModal(calendarId, date, indicator) {
    clientDetail = null;
    var definition = calendarDefinition(indicator, calendarId);
    var detailDefinition = definition ? (definition.detalhamento || {}) : {};
    var modal = $('#indicatorDetailModal');
    var eyebrow = $('#indicatorDetailEyebrow');
    var title = $('#indicatorDetailTitle');
    var content = $('#indicatorDetailContent');
    var exportButton = $('#exportIndicatorDetailPdf');
    if (!definition || !modal || !title || !content || !exportButton) return;
    var formattedDate = format(date, 'data');
    if (eyebrow) eyebrow.textContent = 'CALENDÁRIO';
    title.textContent = String(detailDefinition.titulo || 'Títulos considerados em {data}').replace('{data}', formattedDate);
    showDetailLoading(content, 'Consultando os títulos deste dia. Aguarde um momento.');
    state.detailExportParams = null;
    exportButton.hidden = true;
    exportButton.disabled = true;
    modal.hidden = false;
    var params = indicatorParams();
    params.set('action', 'indicator');
    params.set('detalheCalendarioId', calendarId);
    params.set('detalheCalendarioData', date);
    request('data.php?' + params.toString()).then(function (data) {
      var rows = Array.isArray(data.calendarioDetalhes) ? data.calendarioDetalhes : [];
      var singular = detailDefinition.rotuloSingular || 'título encontrado';
      var plural = detailDefinition.rotuloPlural || 'títulos encontrados';
      var valueField = detailDefinition.campoValor || '';
      var total = valueField ? rows.reduce(function (sum, row) { return sum + Number(row[valueField] || 0); }, 0) : null;
      var totalText = total == null ? '' : ' · Total: ' + format(total, detailDefinition.formato || 'moeda');
      return completeLoadingProgress(content).then(function () {
        content.innerHTML = '<p class="detail-modal-count">' + rows.length + ' ' + escapeHtml(rows.length === 1 ? singular : plural) + escapeHtml(totalText) + '</p>' + detailTable(rows, detailDefinition);
      });
    }).catch(function (error) {
      stopLoadingProgress(content);
      content.innerHTML = '<p class="empty-state">' + escapeHtml(error.message || 'Não foi possível carregar os títulos.') + '</p>';
    });
  }

  function bindCalendarDetails(indicator) {
    Array.prototype.forEach.call(document.querySelectorAll('.calendar-day[data-calendar-date]'), function (day) {
      var open = function () { openCalendarDetailModal(day.getAttribute('data-calendar-id'), day.getAttribute('data-calendar-date'), indicator); };
      day.addEventListener('click', open);
      day.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); open(); } });
    });
  }

  function cashFlowSeriesLabel(definition, seriesKey) {
    var series = definition && Array.isArray(definition.series) ? definition.series : [];
    for (var index = 0; index < series.length; index++) {
      if (String(series[index].chave || '') === String(seriesKey)) return String(series[index].rotulo || seriesKey);
    }
    return String(seriesKey || 'Títulos');
  }

  function openCashFlowDetailModal(seriesKey, startDate, endDate, periodLabel, indicator) {
    clientDetail = null;
    var definition = indicator && indicator.grafico ? indicator.grafico : {};
    var detailDefinition = definition.detalhamento || {};
    var modal = $('#indicatorDetailModal');
    var eyebrow = $('#indicatorDetailEyebrow');
    var title = $('#indicatorDetailTitle');
    var content = $('#indicatorDetailContent');
    var exportButton = $('#exportIndicatorDetailPdf');
    if (!modal || !title || !content || !exportButton || detailDefinition.habilitado === false) return;
    var seriesLabel = cashFlowSeriesLabel(definition, seriesKey);
    var formattedPeriod = periodLabel || (startDate === endDate ? format(startDate, 'data') : format(startDate, 'data') + ' a ' + format(endDate, 'data'));
    if (eyebrow) eyebrow.textContent = 'PROJEÇÃO DE CAIXA';
    title.textContent = String(detailDefinition.titulo || '{serie} em {data}').replace('{serie}', seriesLabel).replace('{data}', formattedPeriod);
    showDetailLoading(content, 'Consultando os títulos desta barra. Aguarde um momento.');
    state.detailExportParams = null;
    exportButton.hidden = true;
    exportButton.disabled = true;
    modal.hidden = false;
    var params = indicatorParams();
    params.set('action', 'indicator');
    params.set('detalheGraficoSerie', seriesKey);
    params.set('detalheGraficoDataInicio', startDate);
    params.set('detalheGraficoDataFim', endDate);
    request('data.php?' + params.toString()).then(function (data) {
      var rows = Array.isArray(data.graficoDetalhes) ? data.graficoDetalhes : [];
      var singular = detailDefinition.rotuloSingular || 'título encontrado';
      var plural = detailDefinition.rotuloPlural || 'títulos encontrados';
      var valueField = detailDefinition.campoValor || definition.campoValor || 'valor';
      var total = rows.reduce(function (sum, row) { return sum + Number(row[valueField] || 0); }, 0);
      return completeLoadingProgress(content).then(function () {
        content.innerHTML = '<p class="detail-modal-count">' + rows.length + ' ' + escapeHtml(rows.length === 1 ? singular : plural) + ' · Total: ' + escapeHtml(format(total, detailDefinition.formato || 'moeda')) + '</p>' + detailTable(rows, detailDefinition);
      });
    }).catch(function (error) {
      stopLoadingProgress(content);
      content.innerHTML = '<p class="empty-state">' + escapeHtml(error.message || 'Não foi possível carregar os títulos.') + '</p>';
    });
  }

  function bindCashFlowDetails(indicator, rows, valueFormat) {
    Array.prototype.forEach.call(document.querySelectorAll('.cash-flow-bar[data-cash-flow-start]'), function (bar) {
      var open = function () { openCashFlowDetailModal(bar.getAttribute('data-cash-flow-series'), bar.getAttribute('data-cash-flow-start'), bar.getAttribute('data-cash-flow-end'), bar.getAttribute('data-cash-flow-period'), indicator); };
      bar.addEventListener('click', open);
      bar.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); open(); } });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-cash-flow-granularity]'), function (button) {
      button.addEventListener('click', function () {
        var granularity = button.getAttribute('data-cash-flow-granularity');
        if (['day', 'week', 'month'].indexOf(granularity) === -1 || state.cashFlowGranularities[state.selectedId] === granularity) return;
        state.cashFlowGranularities[state.selectedId] = granularity;
        $('#indicatorChart').innerHTML = comparativeBarChart(rows, indicator.grafico, valueFormat);
        bindCashFlowDetails(indicator, rows, valueFormat);
      });
    });
  }

  function list(rows, definition, details) {
    if (!definition || definition.habilitada === false) return '';
    var columns = definition.colunas || [];
    var title = definition.titulo || 'Registros considerados';
    var paretoRange = state.paretoRange;
    var paretoClass = state.paretoClass;
    if (paretoRange) title += ' — posições ' + paretoRange.inicio + ' a ' + paretoRange.fim;
    else if (paretoClass) title += ' — Classe ' + paretoClass;
    if (state.selectedUf) title += ' — ' + state.selectedUf;
    var clearPareto = (paretoRange || paretoClass) ? '<button type="button" class="pareto-clear-list" id="clearParetoList">Ver ranking completo</button>' : '';
    var titleHtml = definition.ocultarTitulo === true ? '' : '<div><p class="card-kicker">LISTAGEM</p><h3>' + escapeHtml(title) + '</h3></div>';
    var footnote = definition.observacaoRodape ? '<p class="list-footnote">' + escapeHtml(definition.observacaoRodape) + '</p>' : '';
    if (!rows || !rows.length) return '<article class="wide-card">' + (titleHtml ? '<header>' + titleHtml + clearPareto + '</header>' : '') + '<p class="empty-state">Nenhum registro encontrado.</p>' + footnote + '</article>';
    var hasSellerAction = definition.atualizarVendedor === true;
    var head = (hasSellerAction ? '<th scope="col" class="inactive-client-action">Ação</th>' : '') + columns.map(function (column) { return '<th' + (column.destaque === true ? ' class="list-cell-highlight"' : '') + '>' + escapeHtml(column.titulo || column.campo) + '</th>'; }).join('');
    var visibleRows = state.listExpanded ? rows : rows.slice(0, 10);
    var detailDefinition = definition.detalhamento || {};
    var detailColumns = detailDefinition.colunas || [];
    var groupField = detailDefinition.campoGrupo || ((definition.agrupamento || {}).campo);
    var modalDetails = detailDefinition.modal === true;
    var hasDetails = groupField && detailColumns.length;
    var body = visibleRows.map(function (row) {
      var group = String(row[groupField] == null || row[groupField] === '' ? 'Não informado' : row[groupField]);
      var groupDetails = hasDetails && details ? (details[group] || []) : [];
      var isExpanded = !!state.expandedGroups[group];
      var rowClass = hasDetails ? ' class="group-row" data-group="' + escapeHtml(group) + '" tabindex="0" role="button" aria-expanded="' + isExpanded + '"' : '';
      var actionCell = hasSellerAction ? inactiveClientActionCell(row) : '';
      var mainRow = '<tr' + rowClass + '>' + actionCell + columns.map(function (column) { return listCell(row, column); }).join('') + '</tr>';
      if (!hasDetails || modalDetails || !isExpanded) return mainRow;
      var detailTitle = String(detailDefinition.titulo || 'Itens relacionados a {grupo}').replace('{grupo}', group);
      return mainRow + '<tr class="group-detail"><td colspan="' + (columns.length + (hasSellerAction ? 1 : 0)) + '"><div><strong>' + escapeHtml(detailTitle) + '</strong>' + detailTable(groupDetails, detailDefinition) + '</div></td></tr>';
    }).join('');
    var footer = '';
    var footerDefinition = definition.rodapeTotais || {};
    if (footerDefinition.habilitado === true) {
      var totalFields = Array.isArray(footerDefinition.campos) ? footerDefinition.campos : [];
      var totals = {};
      totalFields.forEach(function (field) {
        totals[field] = rows.reduce(function (sum, row) { return sum + Number(row[field] || 0); }, 0);
      });
      footer = '<tfoot><tr>' + (hasSellerAction ? '<td class="inactive-client-action"></td>' : '') + columns.map(function (column, index) {
        if (index === 0) return '<th>' + escapeHtml(footerDefinition.titulo || 'Total geral') + '</th>';
        if (totalFields.indexOf(column.campo) === -1) return '<td></td>';
        return '<td>' + escapeHtml(format(totals[column.campo], column.formato)) + '</td>';
      }).join('') + '</tr></tfoot>';
    }
    var more = rows.length > 10 && !state.listExpanded ? '<div class="list-more"><button type="button" id="showMoreResults">Mostrar mais resultados</button></div>' : '';
    var totalSelected = paretoClass ? Number(state.paretoClassTotal || rows.length) : rows.length;
    var chipCount = paretoClass ? totalSelected : rows.length;
    var singular = definition.rotuloQuantidadeSingular || 'produto';
    var plural = definition.rotuloQuantidadePlural || 'produtos';
    var chip = '<span class="chip">' + format(chipCount, 'numero') + ' ' + escapeHtml(chipCount === 1 ? singular : plural) + '</span>';
    var limitNotice = paretoClass && totalSelected > rows.length ? '<p class="pareto-list-limit">Mostrando os primeiros ' + format(rows.length, 'numero') + ' de ' + format(totalSelected, 'numero') + ' produtos da Classe ' + escapeHtml(paretoClass) + '.</p>' : '';
    return '<article class="wide-card"><header' + (definition.ocultarTitulo === true ? ' class="list-header-compact"' : '') + '>' + titleHtml + '<div class="list-header-actions">' + clearPareto + chip + '</div></header>' + limitNotice + '<div class="table-scroll"><table><thead><tr>' + head + '</tr></thead><tbody>' + body + '</tbody>' + footer + '</table></div>' + more + footnote + '</article>';
  }

  function bindParetoList() {
    var clear = $('#clearParetoList');
    if (!clear) return;
    clear.addEventListener('click', function () {
      state.paretoRange = null;
      state.paretoClass = '';
      state.paretoClassTotal = 0;
      state.paretoScrollToList = true;
      loadIndicator(true);
    });
  }

  function inactiveClientActionCell(row) {
    var reference = row.referenciaVendedor;
    var referenceNote = reference && reference.origem === 'cadastro'
      ? 'Referência: ' + reference.nome + ' (cadastro atual). Histórico da inativação não confirmado.' : '';
    if (!row.acaoVendedor) return '<td class="inactive-client-action"><span>' + escapeHtml(row.situacao === 'Reativado' ? 'Reativado' : 'Indisponível') + '</span></td>';
    return '<td class="inactive-client-action"><button type="button" class="inactive-client-update" data-update-seller="' + escapeHtml(row.acaoVendedor) + '" data-client-name="' + escapeHtml(row.descricao) + '" data-seller-name="' + escapeHtml(row.vendedorSugerido) + '" data-reference-note="' + escapeHtml(referenceNote) + '">Atualizar vendedor</button></td>';
  }

  function bindInactiveClientActions() {
    Array.prototype.forEach.call(document.querySelectorAll('[data-update-seller]'), function (button) {
      if (button.dataset.sellerBound) return;
      button.dataset.sellerBound = '1';
      button.addEventListener('click', function () {
        var client = button.getAttribute('data-client-name');
        var seller = button.getAttribute('data-seller-name');
        var referenceNote = button.getAttribute('data-reference-note');
        if (!window.confirm('Reativar ' + client + ' e atribuir o vendedor interno ' + seller + '?' + (referenceNote ? '\n\n' + referenceNote : ''))) return;
        button.disabled = true;
        button.textContent = 'Atualizando…';
        var indicatorId = state.selectedId;
        requestPost('atualizar-vendedor.php', { ticket: button.getAttribute('data-update-seller') }).then(function (result) {
          window.alert(result.mensagem);
          if (state.selectedId === indicatorId) loadIndicator(true);
        }).catch(function (error) {
          window.alert(error.message);
          button.textContent = 'Atualize o indicador';
          // Após falha, uma nova consulta é necessária para conferir estado e sugestão.
        });
      });
    });
  }

  function worldGeographicMap(rows, definition) {
    var countries = (rows || []).filter(function (row) { return Number(row.quantidade || 0) > 0 || Number(row.valor || 0) > 0; });
    if (!countries.length) return '';
    state.worldMapRows = countries;
    state.worldMapDefinition = definition;
    var title = definition.titulo || 'Origem das D.I.s por país';
    var ranking = countries.map(function (row) {
      var quantity = Number(row.quantidade || 0);
      var suppliers = Number(row.fornecedores || 0);
      var code = /^[A-Z]{2}$/i.test(String(row.codigo || '')) ? String(row.codigo).toUpperCase() + ' · ' : '';
      return '<div class="uf-row world-country-row"><span class="uf-state"><strong>' + escapeHtml(row.pais || row.codigo) + '</strong><small>' + escapeHtml(code) + escapeHtml(format(quantity, 'numero')) + (quantity === 1 ? ' D.I.' : ' D.I.s') + ' · ' + escapeHtml(format(suppliers, 'numero')) + (suppliers === 1 ? ' fornecedor' : ' fornecedores') + '</small></span><b>' + escapeHtml(format(row.valor, definition.formato || 'moeda')) + '<small>' + escapeHtml(format(row.percentual, 'percentual')) + ' do total</small></b></div>';
    }).join('');
    var chip = '<span class="chip">' + escapeHtml(format(countries.length, 'numero')) + (countries.length === 1 ? ' país com D.I.' : ' países com D.I.') + '</span>';
    var map = '<div class="map-wrapper world-map-wrapper"><div class="world-map-host" data-world-map-host><span>Carregando mapa…</span></div><div class="map-legend world-map-legend" aria-label="Legenda do mapa"><i></i><span>País com D.I.</span></div><div class="map-tooltip" hidden></div></div>';
    return '<article class="wide-card geography-card world-geography-card"><header><div><p class="card-kicker">MAPA-MÚNDI</p><h3>' + escapeHtml(title) + '</h3></div>' + chip + '</header><div class="geography-content world-geography-content">' + map + '<div class="uf-ranking">' + ranking + '</div></div></article>';
  }

  function normalizeWorldCountryName(value) {
    var normalized = String(value || '').toUpperCase();
    if (typeof normalized.normalize === 'function') normalized = normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    return normalized.replace(/[^A-Z0-9]+/g, ' ').replace(/^\s+|\s+$/g, '').replace(/\s+/g, ' ');
  }

  function bindWorldMap() {
    var host = $('[data-world-map-host]');
    var wrapper = $('.world-map-wrapper');
    if (!host || !wrapper) return;
    var rows = state.worldMapRows || [];
    var definition = state.worldMapDefinition || {};
    if (!worldMapSourcePromise) {
      worldMapSourcePromise = fetch('world-map.min.svg?v=1', { credentials: 'same-origin', cache: 'force-cache' }).then(function (response) {
        if (!response.ok) throw new Error('Mapa mundial indisponível.');
        return response.text();
      });
    }
    worldMapSourcePromise.then(function (markup) {
      if (!host.isConnected) return;
      var documentSvg = new DOMParser().parseFromString(markup, 'image/svg+xml');
      var svg = documentSvg.documentElement;
      svg.removeAttribute('width');
      svg.removeAttribute('height');
      svg.setAttribute('class', 'world-map world-real-map');
      svg.setAttribute('role', 'img');
      svg.setAttribute('aria-label', 'Mapa mundial dos países de origem das declarações de importação');
      var countryPaths = svg.querySelectorAll('path[id], g[id]');
      var availableCodes = {}, namesByCode = {};
      var displayNames = typeof Intl.DisplayNames === 'function' ? new Intl.DisplayNames(['pt-BR'], { type: 'region' }) : null;
      Array.prototype.forEach.call(countryPaths, function (path) {
        var code = String(path.getAttribute('id') || '').toLowerCase();
        if (!/^[a-z]{2}$/.test(code)) return;
        availableCodes[code] = true;
        if (displayNames) namesByCode[normalizeWorldCountryName(displayNames.of(code.toUpperCase()))] = code;
      });
      var countryAliases = {
        'EGITO': 'eg', 'FRANCA': 'fr', 'ESTADOS UNIDOS': 'us', 'ESTADOS UNIDOS DA AMERICA': 'us',
        'REINO UNIDO': 'gb', 'CHINA REPUBLICA POPULAR': 'cn', 'RUSSIA FEDERACAO DA': 'ru',
        'COREIA DO SUL REPUBLICA DA': 'kr', 'COREIA DO NORTE REP POP DEMOCRATICA': 'kp',
        'BOLIVIA ESTADO PLURINACIONAL DA': 'bo', 'VENEZUELA': 've', 'VIETNA': 'vn',
        'IRAN REPUBLICA ISLAMICA DO': 'ir', 'SIRIA REPUBLICA ARABE DA': 'sy',
        'TANZANIA REP UNIDA DA': 'tz', 'TCHECA REPUBLICA': 'cz'
      };
      Object.keys(countryAliases).forEach(function (name) { namesByCode[normalizeWorldCountryName(name)] = countryAliases[name]; });
      var byCode = {};
      rows.forEach(function (row) {
        var directCode = String(row.codigo || '').toLowerCase();
        var code = availableCodes[directCode] ? directCode : namesByCode[normalizeWorldCountryName(row.pais)] || namesByCode[normalizeWorldCountryName(row.codigo)] || '';
        if (code) byCode[code] = row;
      });
      Array.prototype.forEach.call(countryPaths, function (path) {
        var code = String(path.getAttribute('id') || '').toLowerCase();
        var row = byCode[code];
        path.setAttribute('class', 'map-country' + (row ? ' has-data' : ''));
        if (!row) return;
        path.setAttribute('data-name', row.pais || row.codigo);
        path.setAttribute('data-code', code.toUpperCase());
        path.setAttribute('data-total', format(row.valor, definition.formato || 'moeda'));
        path.setAttribute('data-quantity', format(row.quantidade, 'numero'));
        path.setAttribute('data-suppliers', format(row.fornecedores, 'numero'));
        path.setAttribute('aria-label', (row.pais || row.codigo) + ': ' + format(row.valor, definition.formato || 'moeda'));
      });
      host.innerHTML = '';
      host.appendChild(document.importNode(svg, true));
      var tooltip = wrapper.querySelector('.map-tooltip');
      Array.prototype.forEach.call(host.querySelectorAll('.map-country.has-data'), function (path) {
        path.addEventListener('mouseenter', function () {
          tooltip.innerHTML = '<b>' + escapeHtml(path.getAttribute('data-name')) + ' <small>' + escapeHtml(path.getAttribute('data-code')) + '</small></b><span>Custo nacionalizado: ' + escapeHtml(path.getAttribute('data-total')) + '</span><span>D.I.s no período: ' + escapeHtml(path.getAttribute('data-quantity')) + '</span><span>Fornecedores: ' + escapeHtml(path.getAttribute('data-suppliers')) + '</span>';
          tooltip.hidden = false;
        });
        path.addEventListener('mousemove', function (event) {
          var bounds = wrapper.getBoundingClientRect();
          tooltip.style.left = Math.min(Math.max(event.clientX - bounds.left + 14, 8), bounds.width - tooltip.offsetWidth - 8) + 'px';
          tooltip.style.top = Math.min(Math.max(event.clientY - bounds.top + 14, 8), bounds.height - tooltip.offsetHeight - 8) + 'px';
        });
        path.addEventListener('mouseleave', function () { tooltip.hidden = true; });
      });
    }).catch(function () {
      if (host.isConnected) host.innerHTML = '<span>Mapa mundial indisponível no momento.</span>';
    });
  }

  function geographicMap(rows, definition) {
    if (!definition || definition.habilitada === false) return '';
    if (definition.tipo === 'mundo') return worldGeographicMap(rows, definition);
    var states = rows || [], title = definition.titulo || 'Distribuição por estado';
    if (!states.length) return '';
    var source = window.BRAZIL_MAP || {}, locations = source.locations || [], mapUfs = {};
    locations.forEach(function (location) { mapUfs[String(location.id || '').toLowerCase()] = true; });
    var top = states.filter(function (row) { return mapUfs[String(row.uf || '').toLowerCase()]; }).slice(0, 3), highlighted = {};
    top.forEach(function (row, index) { highlighted[String(row.uf).toLowerCase()] = index + 1; });
    var statesByUf = {};
    states.forEach(function (row) { statesByUf[String(row.uf || '').toLowerCase()] = row; });
    var salesValues = states.filter(function (row) { return mapUfs[String(row.uf || '').toLowerCase()] && Number(row.valor || 0) > 0; }).map(function (row) { return Number(row.valor); });
    var smallestSale = salesValues.length ? Math.min.apply(Math, salesValues) : 0;
    var largestSale = salesValues.length ? Math.max.apply(Math, salesValues) : 0;
    var heatColor = function (value) {
      var ratio = largestSale === smallestSale ? 1 : (Math.log(Number(value)) - Math.log(smallestSale)) / (Math.log(largestSale) - Math.log(smallestSale));
      var strength = .18 + (Math.max(0, Math.min(1, ratio)) * .82);
      var start = [250, 253, 255], end = [10, 59, 150];
      return 'rgb(' + start.map(function (channel, index) { return Math.round(channel + ((end[index] - channel) * strength)); }).join(',') + ')';
    };
    var ranking;
    if (state.selectedUf) {
      var selectedRow = statesByUf[String(state.selectedUf).toLowerCase()];
      var cities = selectedRow && Array.isArray(selectedRow.cidades) ? selectedRow.cidades : [];
      var cityRows = cities.map(function (city) {
        var cityPercentage = selectedRow && Number(selectedRow.valor || 0) > 0 ? (Number(city.valor || 0) / Number(selectedRow.valor)) * 100 : null;
        return '<div class="uf-row uf-city-row"><span>' + escapeHtml(city.cidade) + '</span><b>' + escapeHtml(format(city.valor, definition.formato || 'moeda')) + (cityPercentage != null ? '<small>' + escapeHtml(format(cityPercentage, 'percentual')) + ' da UF</small>' : '') + '</b></div>';
      }).join('');
      var selectedTotal = selectedRow ? '<b>' + escapeHtml(format(selectedRow.valor, definition.formato || 'moeda')) + '<small>' + escapeHtml(format(selectedRow.percentual, 'percentual')) + ' do total</small></b>' : '';
      ranking = '<div class="uf-selected-heading"><span>UF selecionada: <strong>' + escapeHtml(state.selectedUf) + '</strong></span>' + selectedTotal + '</div><p class="uf-cities-title">3 principais cidades com vendas</p>' + (cityRows || '<p class="empty-state">Nenhuma cidade com venda encontrada.</p>');
    } else {
      ranking = top.map(function (row) {
        var cityPercentage = Number(row.valor || 0) > 0 && row.cidadeValor != null ? (Number(row.cidadeValor) / Number(row.valor)) * 100 : null;
        var city = row.cidade ? '<div class="uf-city"><span>Cidade líder: <strong>' + escapeHtml(row.cidade) + '</strong></span><b>' + escapeHtml(format(row.cidadeValor, definition.formato || 'moeda')) + (cityPercentage != null ? '<small>' + escapeHtml(format(cityPercentage, 'percentual')) + ' da UF</small>' : '') + '</b></div>' : '';
        return '<div class="uf-row"><span class="uf-state"><strong>' + escapeHtml(row.uf) + '</strong><small>Participação do estado no total</small></span><b>' + escapeHtml(format(row.valor, definition.formato || 'moeda')) + '<small>' + escapeHtml(format(row.percentual, 'percentual')) + ' do total</small></b></div>' + city;
      }).join('');
    }
    var paths = locations.map(function (location) {
      var uf = String(location.id || '').toLowerCase(), row = statesByUf[uf], rank = highlighted[uf] || 0;
      var hasSales = row && Number(row.valor || 0) > 0;
      var selected = row && String(row.uf || '').toUpperCase() === String(state.selectedUf || '').toUpperCase();
      var stateClass = 'map-state' + (hasSales ? ' has-sales' : '') + (rank ? ' top top-' + rank : '') + (selected ? ' selected' : '');
      var data = row ? ' data-name="' + escapeHtml(location.name) + '" data-uf="' + escapeHtml(row.uf) + '" data-total="' + escapeHtml(format(row.valor, definition.formato || 'moeda')) + '" data-ticket="' + escapeHtml(format(row.ticketMedio, definition.formato || 'moeda')) + '" data-clients="' + escapeHtml(format(row.clientes, 'numero')) + '"' : '';
      var color = hasSales ? ' style="--map-fill:' + heatColor(row.valor) + '"' : '';
      return '<path class="' + stateClass + '" d="' + location.path + '"' + color + data + ' aria-label="' + escapeHtml(location.name) + (row ? ': ' + format(row.valor, definition.formato || 'moeda') : '') + '"></path>';
    }).join('');
    if (!paths) return '<article class="wide-card geography-card"><header><div><p class="card-kicker">MAPA DO BRASIL</p><h3>' + escapeHtml(title) + '</h3></div><span class="chip">Período filtrado</span></header><p class="empty-state">Mapa indisponível no momento.</p></article>';
    var legend = salesValues.length ? '<div class="map-legend" aria-label="Escala de volume de vendas"><span>Menor volume</span><i></i><span>Maior volume</span></div>' : '';
    var clearButton = state.selectedUf ? '<button type="button" class="map-clear-uf" id="clearUfFilter">Limpar UF</button>' : '';
    var map = '<div class="map-wrapper"><svg class="brazil-map brazil-real-map" viewBox="' + escapeHtml(source.viewBox || '0 0 613 639') + '" role="img" aria-label="Mapa do Brasil com escala de volume de vendas">' + paths + '</svg>' + legend + clearButton + '<div class="map-tooltip" hidden aria-live="polite"></div></div>';
    var filter = state.selectedUf ? '<span class="map-filter">Exibindo resultados de <b>' + escapeHtml(state.selectedUf) + '</b></span>' : '<span class="chip">Clique em uma UF para detalhar</span>';
    return '<article class="wide-card geography-card"><header><div><p class="card-kicker">MAPA DO BRASIL</p><h3>' + escapeHtml(title) + '</h3></div>' + filter + '</header><div class="geography-content">' + map + '<div class="uf-ranking">' + ranking + '</div></div></article>';
  }

  function bindMapTooltip() {
    if ($('[data-world-map-host]')) {
      bindWorldMap();
      return;
    }
    var wrapper = $('.map-wrapper');
    if (!wrapper) return;
    var tooltip = wrapper.querySelector('.map-tooltip');
    if (!tooltip) return;
    Array.prototype.forEach.call(wrapper.querySelectorAll('.map-state.has-sales'), function (path) {
      path.addEventListener('mouseenter', function () {
        tooltip.innerHTML = '<b>' + escapeHtml(path.getAttribute('data-name')) + ' <small>' + escapeHtml(path.getAttribute('data-uf')) + '</small></b><span>Total de vendas: ' + escapeHtml(path.getAttribute('data-total')) + '</span><span>Ticket médio: ' + escapeHtml(path.getAttribute('data-ticket')) + '</span><span>Clientes atendidos: ' + escapeHtml(path.getAttribute('data-clients')) + '</span>';
        tooltip.hidden = false;
      });
      path.addEventListener('mousemove', function (event) {
        var bounds = wrapper.getBoundingClientRect();
        tooltip.style.left = Math.max(8, bounds.width - (tooltip.offsetWidth / 2)) + 'px';
        tooltip.style.top = Math.min(Math.max(event.clientY - bounds.top - (tooltip.offsetHeight / 2), 8), bounds.height - tooltip.offsetHeight - 8) + 'px';
      });
      path.addEventListener('mouseleave', function () { tooltip.hidden = true; });
      path.addEventListener('click', function () {
        state.selectedUf = path.getAttribute('data-uf') || '';
        loadIndicator(true);
      });
    });
    var clear = $('#clearUfFilter');
    if (clear) clear.addEventListener('click', function () {
      state.selectedUf = '';
      loadIndicator(true);
    });
  }

  function contextualHelp(help, title) {
    var text = String(help || '').trim();
    if (!text) return '';
    var label = 'Ajuda sobre ' + (title || 'este bloco') + ': ' + text;
    return '<span class="contextual-help"><button type="button" class="contextual-help-trigger" aria-label="' + escapeHtml(label) + '" data-help="' + escapeHtml(text) + '"><span aria-hidden="true">?</span></button></span>';
  }

  function compositionCard(values, title, valueFormat, help) {
    var total = values.reduce(function (sum, item) { return sum + Number(item.valor || 0); }, 0);
    var offset = 0, colors = ['#2860dc', '#14a7a0', '#8b5cf6', '#f59e0b', '#ef476f'];
    var slices = values.map(function (item, index) { var pct = total ? Number(item.valor || 0) * 100 / total : 0, dash = pct.toFixed(3) + ' ' + (100 - pct).toFixed(3), out = '<circle class="pie-slice" cx="50" cy="50" r="32" pathLength="100" stroke="' + colors[index % colors.length] + '" stroke-dasharray="' + dash + '" stroke-dashoffset="-' + offset.toFixed(3) + '"/>'; offset += pct; return out; }).join('');
    var rows = values.map(function (item, index) { var pct = total ? Number(item.valor || 0) * 100 / total : 0; return '<div class="composition-row"><span><i style="background:' + colors[index % colors.length] + '"></i>' + escapeHtml(item.rotulo || item.chave) + '</span><b>' + escapeHtml(format(item.valor, valueFormat)) + '<small>' + escapeHtml(format(pct, 'percentual')) + '</small></b></div>'; }).join('');
    return '<article class="detail-card composition-card"><p class="card-kicker">COMPOSIÇÃO</p><div class="card-title-with-help"><h3>' + escapeHtml(title || 'Situação') + '</h3>' + contextualHelp(help, title) + '</div><div class="composition-content"><svg class="pie-chart" viewBox="0 0 100 100"><circle class="pie-track" cx="50" cy="50" r="32"/><g transform="rotate(-90 50 50)">' + slices + '</g><text x="50" y="48" class="pie-total">' + (total ? '100%' : '0%') + '</text><text x="50" y="57" class="pie-caption">do bloco</text></svg><div class="composition-legend">' + rows + '</div></div></article>';
  }

  function composition(items, definition, valueFormat) {
    if (!definition || definition.habilitada === false) return '';
    var values = items || [];
    if (!values.length) return '';
    var blocks = Array.isArray(definition.blocos) ? definition.blocos : [];
    if (!blocks.length) return compositionCard(values, definition.titulo || 'Situação', valueFormat, definition.ajuda);
    var byKey = {};
    values.forEach(function (item) { byKey[String(item.chave)] = item; });
    return blocks.map(function (block) {
      var blockValues = (block.itens || []).map(function (key) { return byKey[String(key)] || null; }).filter(Boolean);
      return blockValues.length ? compositionCard(blockValues, block.titulo || definition.titulo || 'Situação', valueFormat, block.ajuda || definition.ajuda) : '';
    }).join('');
  }

  function comparativeRepresentatives(data) {
    var comparison = data.comparativoRepresentantes || {};
    if (comparison.modo === 'mensal') return monthlyRepresentativeComparison(comparison);
    var baseYear = comparison.anoBase || 2025, currentYear = comparison.anoAtual || new Date().getFullYear();
    var filters = (data.indicador || {}).filtros || [];
    var representativeFilter = filters.filter(function (filter) { return filter.id === 'representante'; })[0] || {};
    var monthFilter = filters.filter(function (filter) { return filter.id === 'mesReferencia'; })[0] || {};
    var monthOption = (monthFilter.opcoes || []).filter(function (option) { return String(option.valor) === String(monthFilter.valor || ''); })[0] || {};
    var monthLabel = monthOption.titulo || '';
    var description = monthLabel ? 'Comparação de ' + monthLabel + '/' + baseYear + ' com ' + monthLabel + '/' + currentYear + ', calculada pelas NF-e.' : 'Faturamento líquido anual calculado pelas NF-e.';
    var kicker = monthLabel ? 'COMPARATIVO MENSAL' : 'COMPARATIVO ANUAL';
    var variation = function (value) {
      if (value == null || !isFinite(Number(value))) return '<span class="comparison-na">Não aplicável</span>';
      var numeric = Number(value), css = numeric > 0 ? ' positive' : (numeric < 0 ? ' negative' : ' neutral');
      return '<span class="comparison-variation' + css + '">' + escapeHtml(format(numeric, 'percentual')) + '</span>';
    };
    var lines = (comparison.linhas || []).slice();
    if (!representativeFilter.valor && !monthFilter.valor) lines.sort(function (rowA, rowB) {
      if (rowA.variacao == null && rowB.variacao == null) return String(rowA.representante).localeCompare(String(rowB.representante));
      if (rowA.variacao == null) return 1;
      if (rowB.variacao == null) return -1;
      return Number(rowB.variacao) - Number(rowA.variacao);
    });
    var rows = lines.map(function (row) {
      return '<tr><td><strong>' + escapeHtml(row.representante) + '</strong></td><td>' + escapeHtml(format(row.valorBase, 'moeda')) + '</td><td>' + escapeHtml(format(row.valorAtual, 'moeda')) + '</td><td>' + variation(row.variacao) + '</td></tr>';
    }).join('');
    if (!rows) rows = '<tr><td colspan="4" class="empty-state">Nenhum representante configurado.</td></tr>';
    return '<article class="wide-card comparison-card"><header><div><p class="card-kicker">' + kicker + '</p><h3>Faturamento líquido por representante</h3><p class="comparison-description">' + escapeHtml(description) + '</p></div><span class="chip">' + escapeHtml(lines.length) + ' representantes</span></header><div class="table-scroll"><table class="comparison-table"><thead><tr><th>Representante</th><th>' + escapeHtml(baseYear) + '</th><th>' + escapeHtml(currentYear) + '</th><th>Variação</th></tr></thead><tbody>' + rows + '</tbody><tfoot><tr><th>Total</th><th>' + escapeHtml(format(comparison.totalBase, 'moeda')) + '</th><th>' + escapeHtml(format(comparison.totalAtual, 'moeda')) + '</th><th>' + variation(comparison.variacaoTotal) + '</th></tr></tfoot></table></div></article>';
  }

  function monthlyRepresentativeComparison(comparison) {
    var baseYear = comparison.anoBase || (new Date().getFullYear() - 1), year = comparison.anoAtual || new Date().getFullYear();
    var months = comparison.meses || ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    var lines = comparison.linhas || [];
    var variation = function (value) {
      if (value == null || !isFinite(Number(value))) return '<span class="comparison-na">Não aplicável</span>';
      var numeric = Number(value), css = numeric > 0 ? ' positive' : (numeric < 0 ? ' negative' : ' neutral');
      return '<span class="comparison-variation' + css + '">' + escapeHtml(format(numeric, 'percentual')) + '</span>';
    };
    var headers = months.map(function (month) { return '<th>' + escapeHtml(month + '/' + year) + '</th>'; }).join('');
    var rows = lines.map(function (row, index) {
      var values = row.meses || {}, baseValues = row.mesesBase || {}, variations = row.variacoes || {};
      var expanded = !!state.expandedMonthlyRepresentatives[index];
      var main = '<tr class="monthly-representative-row" data-representative-index="' + index + '" tabindex="0" role="button" aria-expanded="' + expanded + '"><td><strong>' + escapeHtml(row.representante) + '</strong></td>' + months.map(function (month) { return '<td>' + escapeHtml(format(values[month], 'moeda')) + '</td>'; }).join('') + '<td><strong>' + escapeHtml(format(row.total, 'moeda')) + '</strong></td></tr>';
      if (!expanded) return main;
      var base = '<tr class="monthly-comparison-row"><td><strong>' + escapeHtml(baseYear) + '</strong></td>' + months.map(function (month) { return '<td>' + escapeHtml(format(baseValues[month], 'moeda')) + '</td>'; }).join('') + '<td><strong>' + escapeHtml(format(row.totalBase, 'moeda')) + '</strong></td></tr>';
      var comparison = '<tr class="monthly-comparison-row monthly-variation-row"><td><strong>Variação</strong></td>' + months.map(function (month) { return '<td>' + variation(variations[month]) + '</td>'; }).join('') + '<td>' + variation(row.variacaoTotal) + '</td></tr>';
      var scrollControl = '<tr class="monthly-scroll-control-row"><td colspan="' + (months.length + 2) + '"><div class="monthly-horizontal-control"><span>Deslize para ver os demais meses</span><input type="range" min="0" max="0" value="0" step="1" aria-label="Rolagem horizontal dos meses"></div></td></tr>';
      return main + base + comparison + scrollControl;
    }).join('');
    if (!rows) rows = '<tr><td colspan="' + (months.length + 2) + '" class="empty-state">Nenhum representante configurado.</td></tr>';
    var totals = comparison.totaisMeses || {};
    var totalCells = months.map(function (month) { return '<th>' + escapeHtml(format(totals[month], 'moeda')) + '</th>'; }).join('');
    return '<article class="wide-card comparison-card"><header><div><p class="card-kicker">VISÃO MENSAL</p><h3>Faturamento líquido mensal por representante</h3><p class="comparison-description">Valores de 01/' + escapeHtml(year) + ' a 12/' + escapeHtml(year) + '. Clique em um representante para comparar cada mês com ' + escapeHtml(baseYear) + '.</p></div><span class="chip">' + escapeHtml(lines.length) + ' representantes</span></header><div class="table-scroll monthly-table-scroll"><table class="comparison-table"><thead><tr><th>Representante</th>' + headers + '<th>Total ' + escapeHtml(year) + '</th></tr></thead><tbody>' + rows + '</tbody><tfoot><tr><th>Total</th>' + totalCells + '<th>' + escapeHtml(format(comparison.totalAtual, 'moeda')) + '</th></tr></tfoot></table></div></article>';
  }

  function bindMonthlyRepresentativeComparison(comparison) {
    var tableScroll = document.querySelector('.monthly-table-scroll');
    var controls = Array.prototype.slice.call(document.querySelectorAll('.monthly-horizontal-control input'));
    var updateScrollControls = function () {
      if (!tableScroll) return;
      var max = Math.max(0, tableScroll.scrollWidth - tableScroll.clientWidth);
      controls.forEach(function (control) {
        control.max = max;
        control.value = Math.min(max, tableScroll.scrollLeft);
        control.disabled = max === 0;
      });
    };
    if (tableScroll) {
      tableScroll.addEventListener('scroll', updateScrollControls);
      controls.forEach(function (control) { control.addEventListener('input', function () { tableScroll.scrollLeft = Number(control.value); }); });
      window.requestAnimationFrame(updateScrollControls);
    }
    Array.prototype.forEach.call(document.querySelectorAll('.monthly-representative-row'), function (row) {
      var toggle = function () {
        var index = row.getAttribute('data-representative-index');
        state.expandedMonthlyRepresentatives[index] = !state.expandedMonthlyRepresentatives[index];
        $('#indicatorSummary').innerHTML = monthlyRepresentativeComparison(comparison);
        bindMonthlyRepresentativeComparison(comparison);
      };
      row.addEventListener('click', toggle);
      row.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(); } });
    });
  }

  function compactSummaryCard(definition, value, color) {
    var title = definition.ocultarTitulo === true ? '' : '<span>' + escapeHtml(definition.titulo || 'RESULTADO') + '</span>';
    return '<article class="compact-summary-card ' + escapeHtml(color || 'blue') + (definition.ocultarTitulo === true ? ' compact-summary-value' : '') + '" aria-label="' + escapeHtml(definition.titulo || 'Resultado') + '">' + title + '<strong>' + escapeHtml(value) + '</strong></article>';
  }

  function renderResult(data) {
    $('#indicatorLoading').hidden = true;
    var indicator = data.indicador;
    var cardDefinition = indicator.card || {};
    var cardValue = format(data.card.valor, data.card.formato);
    renderIndicatorHeader(indicator);
    renderFilters(indicator.filtros || []);
    setExportAvailability(true);
    $('#indicatorSummary').classList.remove('composition-split');
    $('#updatedAt').textContent = 'Atualizado em ' + data.updatedAt;
    if (data.comparativoRepresentantes && data.comparativoRepresentantes.linhas) {
      state.expandedMonthlyRepresentatives = {};
      $('#indicatorSummary').innerHTML = comparativeRepresentatives(data);
      if (data.comparativoRepresentantes.modo === 'mensal') bindMonthlyRepresentativeComparison(data.comparativoRepresentantes);
      $('#indicatorChart').innerHTML = '';
      $('#indicatorPrimaryList').innerHTML = '';
      $('#indicatorCalendar').innerHTML = '';
      $('#indicatorMap').innerHTML = '';
      $('#indicatorList').innerHTML = '';
      return;
    }
    var compositionDefinition = indicator.composicao || {};
    var detail = composition(data.composicao, compositionDefinition, compositionDefinition.formato || data.card.formato);
    $('#indicatorSummary').classList.toggle('composition-split', Array.isArray(compositionDefinition.blocos) && compositionDefinition.blocos.length > 1);
    if (!detail && compositionDefinition.habilitada !== false) detail = '<article class="detail-card"><p class="card-kicker">COMPOSIÇÃO</p><h3>Registros considerados</h3><div class="detail-row"><span>Quantidade</span><b>' + escapeHtml(format(data.card.quantidade, 'numero')) + '</b></div></article>';
    var compactSummary = $('#indicatorCompactSummary');
    if (cardDefinition.habilitada === false) {
      compactSummary.hidden = true;
      compactSummary.innerHTML = '';
      $('#indicatorSummary').innerHTML = detail;
    } else if (cardDefinition.compacta === true) {
      compactSummary.hidden = false;
      compactSummary.innerHTML = compactSummaryCard(cardDefinition, cardValue, indicator.cor);
      $('#indicatorSummary').innerHTML = detail;
    } else {
      compactSummary.hidden = true;
      compactSummary.innerHTML = '';
      $('#indicatorSummary').innerHTML = '<article class="hero-card ' + escapeHtml(indicator.cor || 'blue') + '"><p class="card-kicker">' + escapeHtml(cardDefinition.titulo || 'RESULTADO') + '</p><strong>' + escapeHtml(cardValue) + '</strong><p>' + escapeHtml(cardDefinition.descricao || indicator.subtitulo || '') + '</p></article>' + detail;
    }
    var generalChart = indicator.grafico && indicator.grafico.tipo === 'pareto'
      ? paretoChart(data.grafico, indicator.grafico, indicator.filtros)
      : (indicator.grafico && indicator.grafico.tipo === 'barras_comparativas'
        ? comparativeBarChart(data.grafico, indicator.grafico, data.card.formato)
        : chart(data.grafico, indicator.grafico, data.card.formato));
    var groupedCharts = groupedChannelCharts(data.graficosPorGrupo, indicator.graficosPorGrupo, data.card.formato, indicator.filtros);
    var showGeneralWithGroups = indicator.graficosPorGrupo && indicator.graficosPorGrupo.exibirGraficoGeral === true && data.graficosPorGrupo && data.graficosPorGrupo.length;
    $('#indicatorChart').innerHTML = groupedCharts ? ((showGeneralWithGroups ? generalChart : '') + groupedCharts) : generalChart;
    bindParetoDetails();
    bindCashFlowDetails(indicator, data.grafico, data.card.formato);
    $('#indicatorCalendar').innerHTML = calendars(data.calendarios, indicator.calendarios, indicator.filtros);
    bindCalendarDetails(indicator);
    $('#indicatorMap').innerHTML = geographicMap(data.geografia, indicator.geografia);
    bindMapTooltip();
    state.paretoClassTotal = data.paretoClasse ? Number(data.paretoClasse.quantidade || 0) : 0;
    state.listExpanded = !!state.paretoRange || !!state.paretoClass;
    state.expandedGroups = {};
    var listTarget = indicator.listagem && indicator.listagem.posicao === 'apos_resumo' ? $('#indicatorPrimaryList') : $('#indicatorList');
    var alternateListTarget = listTarget === $('#indicatorPrimaryList') ? $('#indicatorList') : $('#indicatorPrimaryList');
    alternateListTarget.innerHTML = '';
    listTarget.innerHTML = list(data.listagem, indicator.listagem, data.listagemDetalhes);
    bindParetoList();
    var showMore = $('#showMoreResults');
    if (showMore) showMore.addEventListener('click', function () { state.listExpanded = true; listTarget.innerHTML = list(data.listagem, indicator.listagem, data.listagemDetalhes); bindParetoList(); bindListDetails(data, indicator); });
    bindListDetails(data, indicator);
    if (state.paretoScrollToList) {
      state.paretoScrollToList = false;
      listTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function bindListDetails(data, indicator) {
    bindInactiveClientActions();
    Array.prototype.forEach.call(document.querySelectorAll('[data-list-image]'), function (image) {
      var showFallback = function () {
        image.hidden = true;
        var fallback = image.nextElementSibling;
        if (fallback) fallback.hidden = false;
      };
      image.addEventListener('error', showFallback);
      if (image.complete && !image.naturalWidth) showFallback();
    });
    Array.prototype.forEach.call(document.querySelectorAll('.group-row'), function (row) {
      var toggle = function () {
        var group = row.getAttribute('data-group');
        var detailDefinition = (indicator.listagem || {}).detalhamento || {};
        if (detailDefinition.modal === true) {
          openListDetailModal(group, indicator);
          return;
        }
        state.expandedGroups[group] = !state.expandedGroups[group];
        var listTarget = indicator.listagem && indicator.listagem.posicao === 'apos_resumo' ? $('#indicatorPrimaryList') : $('#indicatorList');
        listTarget.innerHTML = list(data.listagem, indicator.listagem, data.listagemDetalhes);
        bindListDetails(data, indicator);
      };
      row.addEventListener('click', toggle);
      row.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(); } });
    });
  }

  function closeListDetailModal() {
    if (titleBulkSaving || (titleEditing && titleEditing.saving)) return;
    titleEditing = null;
    clientDetail = null;
    if (window.dashboardAgenda) window.dashboardAgenda.close(false);
    var modal = $('#indicatorDetailModal');
    if (modal) modal.hidden = true;
    state.detailExportParams = null;
    var exportButton = $('#exportIndicatorDetailPdf');
    if (exportButton) exportButton.disabled = true;
  }

  function openListDetailModal(group, indicator) {
    var modal = $('#indicatorDetailModal');
    var title = $('#indicatorDetailTitle');
    var content = $('#indicatorDetailContent');
    var exportButton = $('#exportIndicatorDetailPdf');
    if (!modal || !title || !content || !exportButton) return;
    var detailDefinition = (indicator.listagem || {}).detalhamento || {};
    var eyebrow = $('#indicatorDetailEyebrow');
    if (eyebrow) eyebrow.textContent = 'DETALHAMENTO';
    exportButton.hidden = false;
    title.textContent = String(detailDefinition.titulo || 'Notas consideradas — {grupo}').replace('{grupo}', group);
    showDetailLoading(content, 'Consultando os registros selecionados. Aguarde um momento.');
    state.detailExportParams = null;
    exportButton.disabled = true;
    modal.hidden = false;
    var params = new URLSearchParams(state.exportParams || indicatorParams().toString());
    params.set('action', 'indicator');
    params.set('detalheGrupo', group);
    var context = { group: group, definition: detailDefinition, params: params.toString(), requestId: 0, rows: [], selectedTitles: {} };
    clientDetail = context;
    refreshClientDetail(context, true);
  }

  function refreshClientDetail(context, loading, refreshDashboard) {
    if (clientDetail !== context || $('#indicatorDetailModal').hidden) return Promise.resolve(null);
    var requestId = ++context.requestId;
    var content = $('#indicatorDetailContent');
    return request('data.php?' + context.params).then(function (data) {
      if (clientDetail !== context || requestId !== context.requestId) return null;
      var group = context.group;
      var rows = data.listagemDetalhes && data.listagemDetalhes[group] ? data.listagemDetalhes[group] : [];
      return (loading ? completeLoadingProgress(content) : Promise.resolve()).then(function () {
        if (clientDetail !== context || requestId !== context.requestId) return null;
        context.rows = rows;
        if (refreshDashboard) renderResult(data);
        context.definition = data.indicador && data.indicador.listagem ? data.indicador.listagem.detalhamento : context.definition;
        context.selectedTitles = context.selectedTitles || {};
        Object.keys(context.selectedTitles).forEach(function (id) {
          if (!rows.some(function (row) { return String(row.tituloId) === id && row.tituloEditavel === 'S'; })) delete context.selectedTitles[id];
        });
        content.innerHTML = '<p class="detail-modal-count">' + rows.length + (rows.length === 1 ? ' nota encontrada' : ' notas encontradas') + '</p>'
          + titleBulkToolbar(context) + detailTable(rows, context.definition, context.selectedTitles);
        bindTitleBulk(context);
        var params = new URLSearchParams(context.params);
        params.delete('action');
        params.set('escopo', 'detalhamento');
        state.detailExportParams = params.toString();
        $('#exportIndicatorDetailPdf').disabled = false;
        return rows;
      });
    }).catch(function (error) {
      if (clientDetail !== context || requestId !== context.requestId) return null;
      if (loading) {
        stopLoadingProgress(content);
        content.innerHTML = '<p class="empty-state">' + escapeHtml(error.message || 'Não foi possível carregar as notas.') + '</p>';
        return null;
      }
      throw error;
    });
  }

  function titleBulkToolbar(context) {
    var edit = (context.definition || {}).edicaoTitulo || {};
    if (edit.habilitado !== true || !context.rows.some(function (row) { return row.tituloEditavel === 'S'; })) return '';
    var options = (edit.opcoesStatus || []).map(function (option) {
      return '<option value="' + escapeHtml(option.valor) + '">' + escapeHtml(option.titulo) + '</option>';
    }).join('');
    return '<form class="detail-title-bulk" data-title-bulk-form><div class="detail-title-bulk-fields">'
      + '<label>Status do título<select name="statusTitulo"><option value="">Manter status atual</option>' + options + '</select></label>'
      + '<label>Novo vencimento<input type="date" name="dataVencimento" min="1000-01-01" max="9999-12-31"></label>'
      + '<span data-title-selection-count>0 selecionados</span><button type="submit" disabled>Atualizar títulos</button></div>'
      + '<small>Marque os títulos em aberto. Campos vazios mantêm o valor atual.</small><p data-title-bulk-status role="status" aria-live="polite"></p></form>';
  }

  function bindTitleBulk(context) {
    var content = $('#indicatorDetailContent'), form = content.querySelector('[data-title-bulk-form]');
    var checkboxes = Array.prototype.slice.call(content.querySelectorAll('[data-title-select]'));
    var all = content.querySelector('[data-title-select-all]');
    context.selectedTitles = context.selectedTitles || {};
    function update() {
      var selected = Object.keys(context.selectedTitles).length;
      checkboxes.forEach(function (box) { box.checked = !!context.selectedTitles[box.getAttribute('data-title-select')]; });
      if (all) {
        all.checked = checkboxes.length > 0 && selected === checkboxes.length;
        all.indeterminate = selected > 0 && selected < checkboxes.length;
        all.disabled = titleBulkSaving || checkboxes.length === 0;
      }
      if (form) {
        form.querySelector('[data-title-selection-count]').textContent = selected + (selected === 1 ? ' selecionado' : ' selecionados');
        form.querySelector('button[type=submit]').disabled = titleBulkSaving || selected === 0
          || (!form.elements.statusTitulo.value && !form.elements.dataVencimento.value);
      }
    }
    context.updateBulkControls = update;
    checkboxes.forEach(function (box) {
      box.addEventListener('change', function () {
        var id = box.getAttribute('data-title-select');
        if (box.checked && Object.keys(context.selectedTitles).length >= 100) {
          box.checked = false; showToast('Selecione até 100 títulos por atualização.');
        } else if (box.checked) context.selectedTitles[id] = true;
        else delete context.selectedTitles[id];
        update();
      });
    });
    if (all) all.addEventListener('change', function () {
      var select = all.checked && Object.keys(context.selectedTitles).length < Math.min(100, checkboxes.length);
      context.selectedTitles = {};
      if (select) checkboxes.slice(0, 100).forEach(function (box) { context.selectedTitles[box.getAttribute('data-title-select')] = true; });
      if (select && checkboxes.length > 100) showToast('Os primeiros 100 títulos em aberto foram selecionados.');
      update();
    });
    if (!form) { update(); return; }
    form.addEventListener('change', update);
    form.addEventListener('input', update);
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (titleBulkSaving || clientDetail !== context || !form.reportValidity()) return;
      if (titleEditing || agendaOpening || (window.dashboardAgenda && window.dashboardAgenda.isOpen())) {
        showToast('Conclua ou cancele a edição individual antes de atualizar os títulos selecionados.'); return;
      }
      var ids = Object.keys(context.selectedTitles);
      var changes = {};
      if (form.elements.statusTitulo.value) changes.statusTitulo = form.elements.statusTitulo.value;
      if (form.elements.dataVencimento.value) changes.dataVencimento = form.elements.dataVencimento.value;
      if (!ids.length || !Object.keys(changes).length) return;
      var status = form.querySelector('[data-title-bulk-status]');
      var controls = Array.prototype.slice.call(content.querySelectorAll('input, select, button'));
      var previous = controls.map(function (control) { return control.disabled; });
      titleBulkSaving = true;
      controls.forEach(function (control) { control.disabled = true; });
      $('#closeIndicatorDetailModal').disabled = true;
      $('#exportIndicatorDetailPdf').disabled = true;
      status.textContent = 'Preparando atualização de ' + ids.length + ' título(s)…';
      var tickets = [], chain = Promise.resolve();
      ids.forEach(function (id) {
        chain = chain.then(function () {
          var params = new URLSearchParams(context.params);
          params.set('action', 'titleContext'); params.set('tituloId', id);
          return request('data.php?' + params.toString()).then(function (data) { tickets.push(data.ticket); });
        });
      });
      chain.then(function () {
        status.textContent = 'Atualizando títulos…';
        return requestPost('atualizar-titulo.php', { tickets: tickets, alteracoes: changes });
      }).then(function (result) {
        context.selectedTitles = {};
        return refreshClientDetail(context, false, true).then(function () { showToast(result.mensagem); }).catch(function () {
          // A gravação já foi confirmada; impede repetir o lote sobre uma lista desatualizada.
          controls.forEach(function (control) { control.disabled = true; });
          status.textContent = result.mensagem + ' Reabra o detalhamento para atualizar os dados.';
          showToast(status.textContent);
        });
      }).catch(function (error) {
        status.textContent = error.message;
        controls.forEach(function (control, index) { control.disabled = previous[index]; });
      }).then(function () {
        titleBulkSaving = false;
        $('#closeIndicatorDetailModal').disabled = false;
        $('#exportIndicatorDetailPdf').disabled = !state.detailExportParams;
        if (form.isConnected && !controls.every(function (control) { return control.disabled; })) update();
        else if (clientDetail === context && !form.isConnected && context.updateBulkControls) context.updateBulkControls();
      });
    });
    update();
  }

  function openTitleAgenda(button) {
    var context = clientDetail;
    if (!context || titleBulkSaving || titleEditing || agendaOpening || !window.dashboardAgenda || window.dashboardAgenda.isOpen()) return;
    var titleId = button.getAttribute('data-agenda-title');
    var row = context.rows.filter(function (item) { return String(item.tituloId) === titleId; })[0];
    if (!row || row.tituloEditavel !== 'S') return;
    agendaOpening = true;
    button.disabled = true;
    var params = new URLSearchParams(context.params);
    params.set('action', 'agendaContext');
    params.set('agendaTituloId', titleId);
    request('data.php?' + params.toString()).then(function (nativeContext) {
      if (clientDetail !== context || $('#indicatorDetailModal').hidden) return;
      var lastAgendaId = Number(nativeContext.agendaId || 0);
      window.dashboardAgenda.open(nativeContext, row.documento || titleId, function () {
        return refreshClientDetail(context, false).then(function (rows) {
          if (!rows) return false;
          var updated = rows.filter(function (item) { return String(item.tituloId) === titleId; })[0];
          var agendaId = Number(updated && updated.agendaId || 0);
          var changed = agendaId > lastAgendaId;
          lastAgendaId = Math.max(lastAgendaId, agendaId);
          return changed;
        });
      }, showToast, showToast);
    }).catch(function (error) { showToast(error.message); }).then(function () {
      agendaOpening = false;
      button.disabled = false;
    });
  }

  function openTitleEditor(button) {
    var context = clientDetail;
    if (!context || titleBulkSaving || titleEditing || agendaOpening || (window.dashboardAgenda && window.dashboardAgenda.isOpen())) return;
    var titleId = button.getAttribute('data-title-id'), field = button.getAttribute('data-title-edit');
    var row = context.rows.filter(function (item) { return String(item.tituloId) === titleId; })[0];
    if (!row || row.tituloEditavel !== 'S') return;
    var editor = { context: context, cell: button.parentNode, original: button.parentNode.innerHTML, saving: false };
    titleEditing = editor;
    button.disabled = true;
    var params = new URLSearchParams(context.params);
    params.set('action', 'titleContext');
    params.set('tituloId', titleId);
    function cancel() {
      if (titleEditing !== editor || editor.saving) return;
      editor.cell.innerHTML = editor.original;
      titleEditing = null;
      var original = editor.cell.querySelector('button');
      if (original) original.focus();
    }
    request('data.php?' + params.toString()).then(function (data) {
      if (clientDetail !== context || titleEditing !== editor) return;
      var current = String(data.titulo[field] == null ? '' : data.titulo[field]);
      var input;
      if (field === 'statusTitulo') {
        var options = data.opcoesStatus.slice();
        if (!options.some(function (option) { return String(option.valor) === current; })) options.unshift({ valor: '', titulo: 'Selecione um status' });
        input = '<select required aria-label="Status do título">' + options.map(function (option) {
          return '<option value="' + escapeHtml(option.valor) + '"' + (String(option.valor) === current ? ' selected' : '') + '>' + escapeHtml(option.titulo) + '</option>';
        }).join('') + '</select>';
      } else {
        input = '<input type="date" required min="1000-01-01" max="9999-12-31" aria-label="Data de vencimento" value="' + escapeHtml(current) + '">';
      }
      editor.cell.innerHTML = '<form class="detail-title-form">' + input + '<div><button type="submit">Salvar</button><button type="button" data-title-cancel>Cancelar</button></div><small role="alert"></small></form>';
      var form = editor.cell.querySelector('form'), control = form.querySelector('select, input'), errorText = form.querySelector('small');
      form.querySelector('[data-title-cancel]').addEventListener('click', cancel);
      form.addEventListener('keydown', function (event) { if (event.key === 'Escape') { event.stopPropagation(); cancel(); } });
      control.focus();
      if (control.showPicker) { try { control.showPicker(); } catch (ignore) {} }
      if (field === 'dataVencimento') control.addEventListener('click', function () { if (control.showPicker) { try { control.showPicker(); } catch (ignore) {} } });
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (editor.saving || !form.reportValidity()) return;
        editor.saving = true;
        errorText.textContent = 'Salvando…';
        Array.prototype.forEach.call(form.querySelectorAll('input, select, button'), function (element) { element.disabled = true; });
        $('#closeIndicatorDetailModal').disabled = true;
        requestPost('atualizar-titulo.php', { ticket: data.ticket, campo: field, valor: control.value }).then(function (result) {
          return refreshClientDetail(context, false, true).then(function () { showToast(result.mensagem); }).catch(function () {
            editor.cell.textContent = field === 'dataVencimento' ? format(control.value, 'data') : control.options[control.selectedIndex].text;
            showToast(result.mensagem + ' Não foi possível recarregar os dados; atualize o indicador.');
          }).then(function () {
            titleEditing = null;
          });
        }).catch(function (error) {
          errorText.textContent = error.message;
          Array.prototype.forEach.call(form.querySelectorAll('input, select, button'), function (element) { element.disabled = false; });
        }).then(function () {
          editor.saving = false;
          $('#closeIndicatorDetailModal').disabled = false;
        });
      });
    }).catch(function (error) { cancel(); showToast(error.message); });
  }

  function loadIndicator(preserveFilters, replacePending) {
    if (titleBulkSaving || (titleEditing && titleEditing.saving)) return;
    if (!state.configuration || !state.selectedId || (state.loading && !replacePending)) return;
    state.loading = true;
    var requestId = ++state.requestId;
    setExportAvailability(false);
    renderLoading();
    var params = indicatorParams();
    params.set('action', 'indicator');
    request('data.php?' + params.toString()).then(function (data) {
      if (requestId === state.requestId) {
        params.delete('action');
        return completeLoadingProgress($('#indicatorLoading')).then(function () {
          if (requestId === state.requestId) {
            state.exportParams = params.toString();
            renderResult(data);
          }
        });
      }
    }).catch(function (error) {
      if (requestId === state.requestId) renderError(error);
    }).then(function () {
      if (requestId === state.requestId) state.loading = false;
    });
  }

  function accessItems() { return state.access ? (state.access[state.accessMode] || []) : []; }
  function accessRules() { return state.access ? (state.access.acessos[state.accessMode] || {}) : {}; }
  function accessRule(subjectId) {
    var rule = accessRules()[subjectId] || [];
    return state.accessMode === 'usuarios' ? { indicadores: rule.indicadores || [], restricoes: rule.restricoes || {} } : rule;
  }
  function accessSelectedIndicators(subjectId) {
    var rule = accessRule(subjectId);
    return state.accessMode === 'usuarios' ? rule.indicadores : rule;
  }
  function commitAccessDetail() {
    if (!state.access || !state.accessSubject) return true;
    var indicadores = Array.prototype.map.call(document.querySelectorAll('.indicator-access-list input:checked'), function (input) { return input.value; });
    if (state.accessMode === 'usuarios') {
      var restricoes = {};
      var erro = '';
      Array.prototype.forEach.call(document.querySelectorAll('[data-seller-restriction]'), function (container) {
        var indicatorId = container.getAttribute('data-seller-restriction');
        if (indicadores.map(String).indexOf(String(indicatorId)) === -1) return;
        var seller = container.querySelector('select');
        if (!seller || !seller.value) {
          erro = 'Selecione o vendedor que poderá visualizar ' + indicatorId + '.';
          return;
        }
        var option = seller.options[seller.selectedIndex];
        restricoes[indicatorId] = { vendedorId: seller.value, vendedorNome: option ? option.text : '' };
      });
      if (erro) {
        $('#accessStatus').textContent = erro;
        return false;
      }
      state.access.acessos.usuarios[state.accessSubject] = { indicadores: indicadores, restricoes: restricoes };
      return true;
    }
    state.access.acessos.perfis[state.accessSubject] = indicadores;
    var manager = $('#accessManager');
    if (manager) {
      var managers = (state.access.acessos.perfisGestores || []).map(String).filter(function (id) { return id !== String(state.accessSubject); });
      if (manager.checked) managers.push(String(state.accessSubject));
      state.access.acessos.perfisGestores = managers;
    }
    return true;
  }
  function renderAccessSubjects() {
    var items = accessItems();
    $('#accessSubjectsHeading').textContent = state.accessMode === 'usuarios' ? 'Usuários' : 'Perfis';
    if (!items.length) { $('#accessSubjects').innerHTML = '<p class="empty-state">Nenhum registro disponível.</p>'; return; }
    if (!state.accessSubject || !items.some(function (item) { return String(item.id) === String(state.accessSubject); })) state.accessSubject = String(items[0].id);
    $('#accessSubjects').innerHTML = items.map(function (item) {
      var indicators = accessSelectedIndicators(item.id) || [];
      var active = String(item.id) === String(state.accessSubject) ? ' active' : '';
      return '<button type="button" class="access-subject' + active + '" data-subject-id="' + escapeHtml(item.id) + '"><span>' + escapeHtml(item.nome) + '</span><small>' + indicators.length + (indicators.length === 1 ? ' indicador liberado' : ' indicadores liberados') + '</small></button>';
    }).join('');
    Array.prototype.forEach.call(document.querySelectorAll('[data-subject-id]'), function (button) { button.addEventListener('click', function () { if (!commitAccessDetail()) return; state.accessSubject = button.getAttribute('data-subject-id'); renderAccessSubjects(); renderAccessDetail(); }); });
  }
  function renderAccessDetail() {
    var subject = accessItems().filter(function (item) { return String(item.id) === String(state.accessSubject); })[0];
    if (!subject) { $('#accessDetail').innerHTML = ''; return; }
    var selected = accessSelectedIndicators(subject.id) || [];
    var userRule = accessRule(subject.id);
    var isUser = state.accessMode === 'usuarios';
    var manager = state.access.root && !isUser && (state.access.acessos.perfisGestores || []).map(String).indexOf(String(subject.id)) !== -1;
    var managerHtml = state.access.root && !isUser ? '<label class="manager-option"><input type="checkbox" id="accessManager"' + (manager ? ' checked' : '') + '><span>Perfil responsável<small>Todos os usuários deste perfil podem gerenciar os acessos e visualizar todos os indicadores.</small></span></label>' : '';
    var description = isUser ? 'Libere indicadores diretamente para este usuário. Quando houver vendedor definido, ele verá apenas o próprio resultado.' : 'Selecione os indicadores que este perfil poderá visualizar.';
    var restrictionIndicators = state.access.indicadoresComRestricaoVendedor || {};
    var sellers = state.access.vendedores || [];
    var itemsHtml = state.access.indicadores.map(function (indicator) {
      var checked = selected.map(String).indexOf(String(indicator.id)) !== -1;
      var restriction = userRule.restricoes ? userRule.restricoes[indicator.id] : null;
      var sellerHtml = '';
      if (isUser && restrictionIndicators[indicator.id]) {
        var selectedSeller = restriction ? String(restriction.vendedorId) : '';
        sellerHtml = '<div class="seller-restriction" data-seller-restriction="' + escapeHtml(indicator.id) + '"' + (checked ? '' : ' hidden') + '><span>Vendedor que poderá visualizar</span><select><option value="">Selecione o vendedor</option>' + sellers.map(function (seller) { return '<option value="' + escapeHtml(seller.id) + '"' + (String(seller.id) === selectedSeller ? ' selected' : '') + '>' + escapeHtml(seller.nome) + '</option>'; }).join('') + '</select><small>Datas, vendedor e abas serão bloqueados para este acesso.</small></div>';
      }
      return '<div class="indicator-access-entry"><label class="indicator-access"><input type="checkbox" value="' + escapeHtml(indicator.id) + '"' + (checked ? ' checked' : '') + '><span>' + escapeHtml(indicator.titulo) + '</span></label>' + sellerHtml + '</div>';
    }).join('');
    $('#accessDetail').innerHTML = '<h3>' + escapeHtml(subject.nome) + '</h3><p>' + description + '</p>' + managerHtml + '<label class="indicator-access select-all"><input type="checkbox" id="selectAllIndicators"><span>Todos os indicadores</span></label>' + '<div class="indicator-access-list">' + itemsHtml + '</div>';
    var all = $('#selectAllIndicators');
    var choices = document.querySelectorAll('.indicator-access-list input');
    all.checked = choices.length > 0 && Array.prototype.every.call(choices, function (choice) { return choice.checked; });
    function updateSellerRestrictions() {
      Array.prototype.forEach.call(document.querySelectorAll('[data-seller-restriction]'), function (container) {
        var checkbox = container.parentNode.querySelector('.indicator-access input');
        container.hidden = !checkbox.checked;
      });
    }
    all.addEventListener('change', function () { Array.prototype.forEach.call(choices, function (choice) { choice.checked = all.checked; }); updateSellerRestrictions(); });
    Array.prototype.forEach.call(choices, function (choice) { choice.addEventListener('change', function () { all.checked = Array.prototype.every.call(choices, function (item) { return item.checked; }); updateSellerRestrictions(); }); });
  }
  function openAccessModal() {
    request('data.php?action=access').then(function (data) { state.access = data; state.accessMode = 'perfis'; state.accessSubject = ''; $('#accessModal').hidden = false; renderAccessSubjects(); renderAccessDetail(); }).catch(function (error) { showToast(error.message); });
  }
  function saveAccess() {
    if (!state.access) return;
    if (!commitAccessDetail()) return;
    $('#accessStatus').textContent = 'Salvando…';
    requestPost('data.php?action=saveAccess', state.access.acessos).then(function () { $('#accessModal').hidden = true; showToast('Acessos atualizados.'); }).catch(function (error) { $('#accessStatus').textContent = error.message; });
  }

  function start() {
    authorizeSession().then(function () {
      return request('data.php?action=configuration');
    }).then(function (configuration) {
      state.configuration = configuration;
      state.filterPreferences = configuration.preferenciasFiltros || {};
      $('#dashboardTitle').textContent = configuration.titulo || 'Dashboard';
      $('#manageAccess').hidden = !configuration.podeGerenciarAcessos;
      if (!configuration.indicadores || !configuration.indicadores.length) throw new Error(configuration.semAcesso ? 'Nenhum indicador foi liberado para o seu usuário.' : 'Nenhum indicador foi configurado.');
      state.selectedId = configuration.indicadores[0].id;
      renderMenu();
      renderIndicatorHeader(selectedDefinition());
      renderFilters(selectedFilters(selectedDefinition()));
      loadIndicator(false);
    }).catch(renderError);
  }

  $('#indicatorFilters').addEventListener('submit', function (event) { event.preventDefault(); state.paretoRange = null; state.paretoClass = ''; state.paretoClassTotal = 0; state.paretoScrollToList = false; persistCurrentFilters(false).catch(function (error) { showToast(error.message); }); loadIndicator(true); });
  $('#indicatorExports').addEventListener('click', function (event) {
    var format = event.target.getAttribute('data-export');
    if (!format || !state.exportParams) return;
    window.location.href = 'export.php?formato=' + encodeURIComponent(format) + '&' + state.exportParams;
  });
  $('#exportIndicatorDetailPdf').addEventListener('click', function () {
    if (!state.detailExportParams) return;
    window.location.href = 'export.php?formato=pdf&' + state.detailExportParams;
  });
  $('#refreshDashboard').addEventListener('click', function () { loadIndicator(true); showToast('Indicador atualizado.'); });
  $('#manageAccess').addEventListener('click', openAccessModal);
  Array.prototype.forEach.call(document.querySelectorAll('[data-access-mode]'), function (button) { button.addEventListener('click', function () { if (!commitAccessDetail()) return; state.accessMode = button.getAttribute('data-access-mode'); state.accessSubject = ''; Array.prototype.forEach.call(document.querySelectorAll('[data-access-mode]'), function (item) { item.classList.toggle('active', item === button); }); renderAccessSubjects(); renderAccessDetail(); }); });
  $('#closeAccessModal').addEventListener('click', function () { $('#accessModal').hidden = true; });
  $('#cancelAccess').addEventListener('click', function () { $('#accessModal').hidden = true; });
  $('#closeIndicatorDetailModal').addEventListener('click', closeListDetailModal);
  $('#indicatorDetailContent').addEventListener('click', function (event) {
    var editButton = event.target.closest('[data-title-edit]');
    if (editButton) { openTitleEditor(editButton); return; }
    var button = event.target.closest('[data-agenda-title]');
    if (button) openTitleAgenda(button);
  });
  $('#indicatorDetailModal').addEventListener('click', function (event) { if (event.target === this) closeListDetailModal(); });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !$('#indicatorDetailModal').hidden && !(window.dashboardAgenda && window.dashboardAgenda.isOpen())) closeListDetailModal(); });
  $('#saveAccess').addEventListener('click', saveAccess);
  bindCompanyLogo();
  start();
})();
