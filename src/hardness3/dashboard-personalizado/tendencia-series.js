(function (root, factory) {
  'use strict';
  var api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  else root.DashboardTendenciaSeries = api;
}(typeof window === 'undefined' ? this : window, function () {
  'use strict';
  function esc(value) { return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function date(key) { return /^\d{4}-\d{2}-\d{2}$/.test(key || '') ? new Date(key + 'T12:00:00Z') : null; }
  function shortDate(key) { return key.length === 7 ? key.slice(5) + '/' + key.slice(0, 4) : key.slice(8) + '/' + key.slice(5, 7); }
  function fullDate(key, format) { return key ? (key.length === 7 ? shortDate(key) : format(key, 'data')) : 'Sem data correspondente'; }
  function periodText(period, format) { return period ? format(period.inicio, 'data') + ' a ' + format(period.fim, 'data') : ''; }
  function comparisonLabel(data) { return data.comparisonLabel || 'Mês anterior'; }
  function periodGuide(data, format) {
    return '<div class="trend-period-guide trend-period-guide--dates"><span><i></i><span><strong>Atual · linha contínua</strong>' + esc(periodText(data.periods.current, format)) + '</span></span>'
      + (data.periods.previous ? '<span><i class="previous"></i><span><strong>' + esc(comparisonLabel(data)) + ' · linha tracejada</strong>' + esc(periodText(data.periods.previous, format)) + '</span></span>' : '') + '</div>';
  }
  function dates(period, monthly, weekdays) {
    var start = date(period.inicio), end = date(period.fim), out = [];
    if (!start || !end) return out;
    if (monthly) { start.setUTCDate(1); end.setUTCDate(1); }
    for (var cursor = new Date(start), guard = 0; cursor <= end && guard < 5000 && out.length < 500; guard++) {
      if (monthly || !weekdays || (cursor.getUTCDay() !== 0 && cursor.getUTCDay() !== 6)) out.push(cursor.toISOString().slice(0, monthly ? 7 : 10));
      if (monthly) cursor.setUTCMonth(cursor.getUTCMonth() + 1); else cursor.setUTCDate(cursor.getUTCDate() + 1);
    }
    return out;
  }
  function model(rows, definition, comparison) {
    var config = definition.seriesSelecionaveis, definitions = config.series;
    var filtered = definition.periodoFiltrado || {}, current = comparison ? comparison.atual : { inicio: filtered.dataInicio, fim: filtered.dataFim };
    var monthly = comparison ? comparison.agrupamento === 'mes' : String(current.inicio).slice(0, 7) !== String(current.fim).slice(0, 7);
    var weekdays = !monthly && definition.ocultarFinsDeSemana === true;
    function collect(items, period) {
      var byDate = Object.create(null), totals = { valor: 0, quantidade: 0 }, seriesTotals = Object.create(null);
      definitions.forEach(function (d) { seriesTotals[d.id] = { valor: 0, quantidade: 0 }; });
      (items || []).forEach(function (row) {
        if (!row.series || !definitions.every(function (d) { return row.series[d.id] && isFinite(Number(row.series[d.id].valor)) && (!definition.campoQuantidade || isFinite(Number(row.series[d.id].quantidade))); })) throw new Error('As séries da tendência estão incompletas. Atualize o indicador.');
        byDate[row.periodo] = row;
        totals.valor += Number(row.valor || 0); totals.quantidade += Number(row.quantidade || 0);
        definitions.forEach(function (d) { seriesTotals[d.id].valor += Number(row.series[d.id].valor); seriesTotals[d.id].quantidade += Number(row.series[d.id].quantidade || 0); });
      });
      var keys = period ? dates(period, monthly, weekdays) : [];
      return { dates: keys, totals: totals, lines: definitions.map(function (d) {
        return { id: d.id, title: d.titulo, color: /^#[a-f0-9]{6}$/i.test(d.cor) ? d.cor : '#2563eb', totals: seriesTotals[d.id], points: keys.map(function (key) {
          var value = byDate[key] && byDate[key].series[d.id];
          return { date: key, valor: value ? Number(value.valor) : 0, quantidade: value ? Number(value.quantidade || 0) : 0 };
        }) };
      }) };
    }
    return { current: collect(rows, current), previous: collect(comparison && comparison.linhas, comparison && comparison.anterior), monthly: monthly, weekdays: weekdays,
      comparisonLabel: comparison && comparison.rotulo || 'Mês anterior', comparisonYear: comparison && comparison.ano,
      periods: { current: current, previous: comparison && comparison.anterior } };
  }
  function chart(data, definition, selected, metric, format) {
    var quantity = metric === 'quantidade', type = quantity ? definition.formatoQuantidade || 'toneladas' : definition.formato || 'moeda';
    var title = quantity ? definition.tituloQuantidade || 'Quantidade faturada (t)' : (definition.titulo || 'Tendência do faturamento') + ' (R$)';
    var current = data.current.lines.filter(function (line) { return selected.indexOf(line.id) >= 0; });
    var previous = data.previous.lines.filter(function (line) { return selected.indexOf(line.id) >= 0; });
    var count = Math.max(data.current.dates.length, data.previous.dates.length);
    var subtitle = definition.subtitulo || 'Período atual e mês anterior';
    if (data.comparisonYear) subtitle = subtitle.replace(/mês anterior/gi, comparisonLabel(data));
    var header = '<header><div><p class="card-kicker">EVOLUÇÃO</p><h3>' + esc(title) + '</h3><p class="trend-subtitle">' + esc(subtitle) + '</p></div><div class="trend-total"><span>Total da visão</span><strong>' + esc(format(data.current.totals[metric], type)) + '</strong></div></header>';
    if (!selected.length || !count) return '<article class="wide-card trend-card">' + header + '<p class="empty-state">' + (!selected.length ? 'Marque uma ou mais opções para comparar.' : 'Não há dias disponíveis no período selecionado.') + '</p></article>';
    var numbers = [0, 1];
    current.concat(previous).forEach(function (line) { line.points.forEach(function (p) { numbers.push(p[metric]); }); });
    var min = Math.min.apply(null, numbers), max = Math.max.apply(null, numbers), rough = (max - min) / 4;
    var magnitude = Math.pow(10, Math.floor(Math.log10(rough))), step = [1, 2, 5, 10].filter(function (n) { return n * magnitude >= rough; })[0] * magnitude;
    min = Math.floor(min / step) * step; max = Math.ceil(max / step) * step;
    var width = 1100, height = 350, left = 112, right = 24, top = 24, bottom = data.periods.previous ? 80 : 54;
    function x(i) { return count === 1 ? (width + left - right) / 2 : left + (width - left - right) * i / (count - 1); }
    function y(value) { return top + (max - value) * (height - top - bottom) / (max - min); }
    function axisValue(value) { if (type !== 'moeda') return format(value, type); var n = Math.abs(value), divisor = n >= 1000000 ? 1000000 : n >= 1000 ? 1000 : 1; return divisor === 1 ? format(value, type) : 'R$ ' + format(value / divisor, 'numero') + (divisor === 1000000 ? ' mi' : ' mil'); }
    var svg = '<svg class="trend-chart trend-chart--selected" viewBox="0 0 ' + width + ' ' + height + '" role="group" aria-label="' + esc(title) + '"><title>' + esc(title + ': ' + periodText(data.periods.current, format) + (data.periods.previous ? '; ' + comparisonLabel(data) + ': ' + periodText(data.periods.previous, format) : '')) + '</title>';
    for (var tick = min, guard = 0; tick <= max + step / 100 && guard < 12; tick += step, guard++) svg += '<line class="trend-grid" x1="' + left + '" x2="' + (width - right) + '" y1="' + y(tick) + '" y2="' + y(tick) + '"/><text class="chart-label" text-anchor="end" x="' + (left - 12) + '" y="' + (y(tick) + 4) + '">' + esc(axisValue(tick)) + '</text>';
    var every = Math.max(1, Math.ceil(count / (data.monthly ? 9 : 23))), axisTop = height - bottom + 25;
    svg += '<text class="chart-label trend-axis-heading" text-anchor="end" x="' + (left - 28) + '" y="' + axisTop + '">Atual</text>';
    if (data.periods.previous) svg += '<text class="chart-label trend-axis-heading" text-anchor="end" x="' + (left - 28) + '" y="' + (axisTop + 24) + '">' + esc(comparisonLabel(data)) + '</text>';
    for (var i = 0; i < count; i++) if (!(i % every) || i === count - 1) {
      svg += '<text class="chart-label trend-axis-date" data-trend-date="' + esc(data.current.dates[i] || '') + '" text-anchor="middle" x="' + x(i) + '" y="' + axisTop + '">' + esc(data.current.dates[i] ? shortDate(data.current.dates[i]) : '—') + '</text>';
      if (data.periods.previous) svg += '<text class="chart-label trend-axis-date trend-axis-date--previous" data-trend-previous-date="' + esc(data.previous.dates[i] || '') + '" text-anchor="middle" x="' + x(i) + '" y="' + (axisTop + 24) + '">' + esc(data.previous.dates[i] ? shortDate(data.previous.dates[i]) : '—') + '</text>';
    }
    function plot(lines, old) {
      lines.forEach(function (line) {
        if (!line.points.length) return;
        var path = line.points.map(function (p, i) { return (i ? 'L' : 'M') + x(i).toFixed(2) + ',' + y(p[metric]).toFixed(2); }).join(' ');
        if (!old && lines.length === 1) svg += '<path d="' + path + ' L' + x(line.points.length - 1) + ',' + y(0) + ' L' + x(0) + ',' + y(0) + ' Z" fill="' + line.color + '" opacity=".10"/>';
        svg += '<path data-trend-series="' + esc(line.id) + '" data-trend-period="' + (old ? 'anterior' : 'atual') + '" d="' + path + '" fill="none" stroke="' + line.color + '" stroke-width="2.5"' + (old ? ' stroke-dasharray="7 5"' : '') + '/>';
        line.points.forEach(function (p, i) { svg += '<circle cx="' + x(i) + '" cy="' + y(p[metric]) + '" r="2.8" fill="' + line.color + '"><title>' + esc(line.title + ' · ' + (old ? comparisonLabel(data) : 'Atual') + ' · ' + fullDate(p.date, format) + ': ' + format(p[metric], type)) + '</title></circle>'; });
      });
    }
    plot(current, false); plot(previous, true);
    for (var column = 0; column < count; column++) {
      var hitLeft = column ? (x(column - 1) + x(column)) / 2 : left - 8;
      var hitRight = column < count - 1 ? (x(column) + x(column + 1)) / 2 : width - right + 8;
      var label = 'Conferir atual: ' + fullDate(data.current.dates[column], format) + (data.periods.previous ? '; ' + comparisonLabel(data) + ': ' + fullDate(data.previous.dates[column], format) : '');
      svg += '<rect class="trend-date-hit" data-trend-point="' + column + '" x="' + hitLeft + '" y="' + top + '" width="' + (hitRight - hitLeft) + '" height="' + (height - top - bottom) + '" tabindex="' + (column ? -1 : 0) + '" role="button" aria-describedby="trend-day-tooltip-' + metric + '" aria-label="' + esc(label) + '"><title>' + esc(label) + '</title></rect>';
    }
    svg += '</svg>';
    var legend = '<div class="trend-legend trend-legend--comparison trend-legend--activities">' + current.map(function (line) {
      return '<span><i style="background:' + line.color + '"></i>' + esc(line.title + ' · Atual') + '</span>' + (data.periods.previous ? '<span><i class="trend-swatch--previous" style="border-color:' + line.color + '"></i>' + esc(line.title + ' · ' + comparisonLabel(data)) + '</span>' : '');
    }).join('') + '</div>';
    var rows = '';
    for (var index = 0; index < count; index++) rows += '<tr><td>' + esc(fullDate(data.current.dates[index], format)) + '</td>' + (data.periods.previous ? '<td>' + esc(fullDate(data.previous.dates[index], format)) + '</td>' : '') + current.map(function (line, n) {
      var p = line.points[index], prev = previous[n].points[index];
      return '<td>' + (p ? esc(format(p[metric], type)) + '<small>' + esc(shortDate(p.date)) + '</small>' : '—') + '</td>' + (data.periods.previous ? '<td>' + (prev ? esc(format(prev[metric], type)) + '<small>' + esc(shortDate(prev.date)) + '</small>' : '—') + '</td>' : '');
    }).join('') + '</tr>';
    var table = '<details class="trend-values"><summary>Ver valores por ' + (data.monthly ? 'mês' : 'dia') + '</summary><div class="table-scroll"><table><thead><tr><th>Data atual</th>' + (data.periods.previous ? '<th>' + esc(data.comparisonYear ? 'Data de ' + comparisonLabel(data) : 'Data do mês anterior') + '</th>' : '') + current.map(function (line) { return '<th>' + esc(line.title) + ' · Atual</th>' + (data.periods.previous ? '<th>' + esc(line.title + ' · ' + comparisonLabel(data)) + '</th>' : ''); }).join('') + '</tr></thead><tbody>' + rows + '</tbody></table></div></details>';
    return '<article class="wide-card trend-card" data-trend-metric="' + metric + '">' + header + periodGuide(data, format) + '<p class="trend-dates-help">' + (data.periods.previous ? (data.comparisonYear ? 'Cada coluna mostra a data atual e, abaixo, a data de ' + esc(comparisonLabel(data)) + ' comparada.' : 'Cada coluna mostra a data atual e, abaixo, a data do mês anterior comparada.') : 'As datas aparecem abaixo do gráfico.') + '</p><div class="trend-chart-wrap"><div class="chart-scroll">' + svg + '</div><div class="trend-day-tooltip" id="trend-day-tooltip-' + metric + '" data-trend-tooltip role="tooltip" hidden></div></div>' + legend + periodSummary(data, definition, selected, metric, format) + table + '</article>';
  }
  function periodSummary(data, definition, selected, metric, format) {
    var type = metric === 'quantidade' ? definition.formatoQuantidade || 'toneladas' : definition.formato || 'moeda';
    return '<section class="trend-period-summary" data-trend-period-summary><h4>Acumulado dos períodos</h4><div class="trend-point-heading"><span>Atual<strong>' + esc(periodText(data.periods.current, format)) + '</strong></span>'
      + (data.periods.previous ? '<span>' + esc(comparisonLabel(data)) + '<strong>' + esc(periodText(data.periods.previous, format)) + '</strong></span>' : '') + '</div>'
      + data.current.lines.filter(function (line) { return selected.indexOf(line.id) >= 0; }).map(function (line) {
        var previous = data.previous.lines.filter(function (old) { return old.id === line.id; })[0];
        return '<section class="trend-point-values" data-trend-summary-series="' + esc(line.id) + '"><h4><i style="background:' + line.color + '"></i>' + esc(line.title) + '</h4><div><strong>' + esc(format(line.totals[metric], type)) + '</strong>'
          + (data.periods.previous ? '<strong>' + esc(format(previous.totals[metric], type)) + '</strong>' : '') + '</div></section>';
      }).join('') + '</section>';
  }
  function pointDetail(data, definition, selected, metric, index, format) {
    var type = metric === 'quantidade' ? definition.formatoQuantidade || 'toneladas' : definition.formato || 'moeda';
    return '<div class="trend-point-heading"><span>Atual<strong>' + esc(fullDate(data.current.dates[index], format)) + '</strong></span>'
      + (data.periods.previous ? '<span>' + esc(comparisonLabel(data)) + '<strong>' + esc(fullDate(data.previous.dates[index], format)) + '</strong></span>' : '') + '</div>'
      + data.current.lines.filter(function (line) { return selected.indexOf(line.id) >= 0; }).map(function (line) {
        var current = line.points[index], previous = data.previous.lines.filter(function (old) { return old.id === line.id; })[0].points[index];
        return '<section class="trend-point-values"><h4><i style="background:' + line.color + '"></i>' + esc(line.title) + '</h4><div><strong>' + (current ? esc(format(current[metric], type)) : '—') + '</strong>'
          + (data.periods.previous ? '<strong>' + (previous ? esc(format(previous[metric], type)) : '—') + '</strong>' : '') + '</div></section>';
      }).join('');
  }
  function mount(container, rows, definition, comparison, format, loadComparison) {
    if (container._trendSeriesChange) container.removeEventListener('change', container._trendSeriesChange);
    if (container._trendSeriesInspect) ['mouseover', 'focusin', 'click', 'keydown', 'mouseout', 'focusout'].forEach(function (name) { container.removeEventListener(name, container._trendSeriesInspect); });
    var definitions = definition.seriesSelecionaveis.series, selected = definitions.filter(function (d) { return d.selecionada !== false; }).map(function (d) { return d.id; }), data;
    var generation = {}, revision = 0, comparisonKey = 'mesAnterior', busy = false, errorMessage = '', cache = { mesAnterior: comparison }, pending = {};
    container._trendMount = generation;
    var yearConfig = definition.comparativoAnos || {};
    var years = yearConfig.habilitado === true && typeof loadComparison === 'function' && Array.isArray(yearConfig.anos)
      ? yearConfig.anos.map(String).filter(function (year, index, all) { return /^(19|20|21)\d{2}$/.test(year) && all.indexOf(year) === index; }).slice(0, 10) : [];
    try { data = model(rows, definition, comparison); }
    catch (error) { container.innerHTML = '<article class="wide-card"><p class="empty-state">' + esc(error.message) + '</p></article>'; return; }
    mount.sequence = (mount.sequence || 0) + 1;
    var yearName = 'trend-comparison-year-' + mount.sequence;
    var yearPicker = years.length ? '<fieldset class="trend-comparison-picker"><legend>Comparar com</legend><p>Escolha um ano para comparar o mesmo intervalo de datas nos dois gráficos.</p><div class="trend-comparison-options">'
      + ['mesAnterior'].concat(years).map(function (key) { return '<label class="trend-comparison-option"><input type="radio" name="' + yearName + '" data-trend-year="' + key + '"><span>' + (key === 'mesAnterior' ? 'Mês anterior' : key) + '</span></label>'; }).join('') + '</div></fieldset>' : '';
    container.innerHTML = '<section class="wide-card trend-series-picker"><fieldset><legend>' + esc(definition.seriesSelecionaveis.titulo || 'Comparar tendências') + '</legend><p>Marque as opções para comparar nos dois gráficos.</p><div class="trend-series-options"><label class="trend-series-option trend-series-all"><input type="checkbox" data-trend-all><span>Todas<small>Marcar ou desmarcar opções</small></span></label>' + definitions.map(function (d) { return '<label class="trend-series-option" style="--series-color:' + esc(d.cor) + '"><input type="checkbox" data-trend-select="' + esc(d.id) + '"><i aria-hidden="true"></i><span>' + esc(d.titulo) + '</span></label>'; }).join('') + '</div></fieldset>' + yearPicker + '<div data-trend-period-guide>' + periodGuide(data, format) + '</div></section><div class="trend-panels" data-trend-plots aria-live="polite"></div>';
    var mountRoot = container.firstElementChild;
    function render() {
      var all = container.querySelector('[data-trend-all]');
      all.checked = selected.length === definitions.length; all.indeterminate = selected.length > 0 && selected.length < definitions.length;
      container.querySelectorAll('[data-trend-select]').forEach(function (input) { input.checked = selected.indexOf(input.getAttribute('data-trend-select')) >= 0; input.closest('label').classList.toggle('is-selected', input.checked); });
      container.querySelectorAll('[data-trend-year]').forEach(function (input) { input.checked = input.getAttribute('data-trend-year') === comparisonKey; input.closest('label').classList.toggle('is-selected', input.checked); });
      container.querySelector('[data-trend-period-guide]').innerHTML = busy || errorMessage ? '' : periodGuide(data, format);
      var plots = container.querySelector('[data-trend-plots]');
      plots.setAttribute('aria-busy', busy ? 'true' : 'false'); plots.setAttribute('data-trend-comparison', comparisonKey);
      plots.innerHTML = busy ? '<article class="wide-card trend-comparison-status" role="status"><strong>Carregando comparativo de ' + esc(comparisonKey) + '</strong><p>Consultando o mesmo intervalo de datas para os dois gráficos.</p></article>'
        : errorMessage ? '<article class="wide-card trend-comparison-status" role="alert"><strong>Não foi possível carregar o comparativo.</strong><p>' + esc(errorMessage) + '</p><button type="button" data-trend-retry>Tentar novamente</button></article>'
        : chart(data, definition, selected, 'valor', format) + (definition.campoQuantidade ? chart(data, definition, selected, 'quantidade', format) : '');
    }
    function selectComparison(key) {
      if (key !== 'mesAnterior' && years.indexOf(key) < 0) return;
      comparisonKey = key; errorMessage = ''; var requestRevision = ++revision;
      if (Object.prototype.hasOwnProperty.call(cache, key)) {
        data = model(rows, definition, cache[key]); busy = false; render(); return;
      }
      busy = true; render();
      if (!pending[key]) pending[key] = Promise.resolve().then(function () { return loadComparison(Number(key)); }).then(function (result) {
        if (!result || result.tipo !== 'ano' || String(result.ano) !== key || !result.atual || !Array.isArray(result.linhas)
            || result.atual.inicio !== data.periods.current.inicio || result.atual.fim !== data.periods.current.fim) throw new Error('A resposta do ano escolhido está incompleta. Tente novamente.');
        model(rows, definition, result); cache[key] = result; delete pending[key]; return result;
      }, function (error) { delete pending[key]; throw error; });
      pending[key].then(function (result) {
        if (container._trendMount !== generation || !container.contains(mountRoot) || revision !== requestRevision) return;
        data = model(rows, definition, result); busy = false; render();
      }).catch(function (error) {
        delete pending[key];
        if (container._trendMount !== generation || !container.contains(mountRoot) || revision !== requestRevision) return;
        busy = false; errorMessage = error.message || 'Tente novamente.'; render();
      });
    }
    container._trendSeriesChange = function (event) {
      if (event.target.hasAttribute('data-trend-year')) { selectComparison(event.target.getAttribute('data-trend-year')); return; }
      if (event.target.hasAttribute('data-trend-all')) selected = event.target.checked ? definitions.map(function (d) { return d.id; }) : [];
      else if (event.target.hasAttribute('data-trend-select')) selected = Array.prototype.filter.call(container.querySelectorAll('[data-trend-select]'), function (input) { return input.checked; }).map(function (input) { return input.getAttribute('data-trend-select'); });
      else return;
      render();
    };
    container._trendSeriesInspect = function (event) {
      if (event.type === 'click' && event.target.closest('[data-trend-retry]')) { selectComparison(comparisonKey); return; }
      if (event.type === 'mouseout' || event.type === 'focusout') {
        var wrap = event.target.closest('.trend-chart-wrap');
        if (wrap && (!event.relatedTarget || !wrap.contains(event.relatedTarget))) wrap.querySelector('[data-trend-tooltip]').hidden = true;
        return;
      }
      if ((event.type === 'keydown' && event.key === 'Escape') || (event.type === 'click' && !event.target.closest('[data-trend-point]'))) {
        container.querySelectorAll('[data-trend-tooltip]').forEach(function (tooltip) { tooltip.hidden = true; }); return;
      }
      var point = event.target.closest('[data-trend-point]');
      if (!point || !container.contains(point)) return;
      if (event.type === 'keydown') {
        var keys = { ArrowLeft: -1, ArrowRight: 1, Home: -Infinity, End: Infinity };
        if (Object.prototype.hasOwnProperty.call(keys, event.key)) {
          event.preventDefault(); var points = point.parentNode.querySelectorAll('[data-trend-point]');
          var next = Math.max(0, Math.min(points.length - 1, Number(point.getAttribute('data-trend-point')) + keys[event.key]));
          point.setAttribute('tabindex', '-1'); points[next].setAttribute('tabindex', '0'); points[next].focus(); return;
        }
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
      }
      var card = point.closest('[data-trend-metric]'), tooltip = card.querySelector('[data-trend-tooltip]');
      tooltip.innerHTML = pointDetail(data, definition, selected, card.getAttribute('data-trend-metric'), Number(point.getAttribute('data-trend-point')), format);
      var bounds = point.closest('.trend-chart-wrap').getBoundingClientRect(), pointBounds = point.getBoundingClientRect();
      var leftHalf = (pointBounds.left + pointBounds.right) / 2 < bounds.left + bounds.width / 2;
      tooltip.style.left = leftHalf ? 'auto' : '12px'; tooltip.style.right = leftHalf ? '12px' : 'auto'; tooltip.hidden = false;
    };
    container.addEventListener('change', container._trendSeriesChange);
    ['mouseover', 'focusin', 'click', 'keydown', 'mouseout', 'focusout'].forEach(function (name) { container.addEventListener(name, container._trendSeriesInspect); });
    render();
  }
  return { model: model, chart: chart, pointDetail: pointDetail, mount: mount };
}));
