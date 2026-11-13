(function () {
  'use strict';
  function esc(value) { return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function money(value) { return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
  function label(month) { return ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'][Number(month.slice(5)) - 1] + '/' + month.slice(2, 4); }
  function compact(value) {
    var divisor = Math.abs(value) >= 1000000 ? 1000000 : (Math.abs(value) >= 1000 ? 1000 : 1);
    return divisor === 1 ? money(value) : (value / divisor).toLocaleString('pt-BR', { maximumFractionDigits: 2 }) + (divisor === 1000000 ? ' mi' : ' mil');
  }
  function mount(container, rows, definition, options) {
    var active = true, busy = false, selected = definition.selecao || { periodo: '', serie: '' };
    var period = definition.periodoFiltrado, months = [], series = [], values = Object.create(null);
    var cursor = new Date(period.dataInicio.slice(0, 7) + '-01T00:00:00Z');
    var finalMonth = period.dataFim.slice(0, 7);
    for (var m = 0; m < 120 && cursor.toISOString().slice(0, 7) <= finalMonth; m++) {
      months.push(cursor.toISOString().slice(0, 7)); cursor.setUTCMonth(cursor.getUTCMonth() + 1);
    }
    (rows || []).forEach(function (row) {
      if (months.indexOf(row.periodo) < 0) return;
      var name = String(row.serie), number = Number(row.valor);
      if (!Number.isFinite(number)) return;
      if (series.indexOf(name) < 0) series.push(name);
      if (!values[row.periodo]) values[row.periodo] = Object.create(null);
      if (!values[row.periodo][name]) values[row.periodo][name] = { valor: 0, quantidade: 0 };
      values[row.periodo][name].valor += number;
      values[row.periodo][name].quantidade += Number(row.quantidade || 0);
    });
    series.sort(function (a, b) { return a.localeCompare(b, 'pt-BR'); });
    var colors = ['#4275c5', '#f48432', '#9ca3af', '#eab308', '#16a477', '#7c5ce6', '#0ea5a7', '#bc4ed7'];
    function color(name, index) {
      var configured = definition.cores && definition.cores[name];
      return /^#[a-f0-9]{6}$/i.test(configured || '') ? configured : (colors[index] || 'hsl(' + Math.round(index * 137.508 % 360) + ',55%,43%)');
    }
    var width = Math.max(1080, months.length * 84 + 160), height = 400, left = 150, right = 24, top = 30, bottom = 78;
    var positive = 0, negative = 0;
    months.forEach(function (month) {
      var plus = 0, minus = 0;
      series.forEach(function (name) { var v = values[month] && values[month][name] ? values[month][name].valor : 0; if (v >= 0) plus += v; else minus += v; });
      positive = Math.max(positive, plus); negative = Math.min(negative, minus);
    });
    positive = positive || (negative === 0 ? 1 : 0);
    var scale = positive - negative, padding = scale * .10;
    if (positive > 0) positive += padding;
    if (negative < 0) negative -= padding;
    function y(value) { return top + (positive - value) / (positive - negative) * (height - top - bottom); }
    function entries(month) {
      return series.filter(function (name) { var p = values[month] && values[month][name]; return p && (p.valor || p.quantidade); })
        .map(function (name) { return { nome: name, valor: values[month][name].valor, quantidade: values[month][name].quantidade }; });
    }
    function percentage(month, value) {
      var total = entries(month).reduce(function (sum, point) { return sum + Math.abs(point.valor); }, 0);
      var percent = total ? Math.abs(value) / total * 100 : 0;
      return percent > 0 && percent < .01 ? '< 0,01%' : percent.toLocaleString('pt-BR', { maximumFractionDigits: 2 }) + '%';
    }
    function small(value) { return Math.abs(y(0) - y(value)) < 4; }
    var step = (width - left - right) / Math.max(1, months.length), barWidth = Math.min(52, step * .66);
    var svg = '<svg viewBox="0 0 ' + width + ' ' + height + '" style="min-width:' + width + 'px" role="group" aria-label="Valores mensais por categoria"><title>' + esc(definition.titulo || 'Parcelas por vencimento') + '</title>';
    for (var tick = 0; tick <= 4; tick++) {
      var value = negative + (positive - negative) * tick / 4;
      svg += '<path class="stacked-grid" d="M' + left + ' ' + y(value) + 'H' + (width - right) + '"/><text x="' + (left - 12) + '" y="' + (y(value) + 4) + '" text-anchor="end">' + esc(money(value)) + '</text>';
    }
    svg += '<path class="stacked-zero" d="M' + left + ' ' + y(0) + 'H' + (width - right) + '"/>';
    months.forEach(function (month, mi) {
      var x = left + step * (mi + .5), plus = 0, minus = 0, total = 0;
      series.forEach(function (name, si) {
        var point = values[month] && values[month][name];
        if (!point || !point.valor) return;
        var before = point.valor > 0 ? plus : minus, after = before + point.valor;
        if (point.valor > 0) plus = after; else minus = after;
        total += point.valor;
        var text = label(month) + ' · ' + name + ' · ' + money(point.valor) + ' · ' + point.quantidade + ' títulos';
        svg += '<rect class="stacked-segment" x="' + (x - barWidth / 2) + '" y="' + Math.min(y(before), y(after)) + '" width="' + barWidth + '" height="' + Math.abs(y(before) - y(after)) + '" fill="' + color(name, si) + '" role="button" tabindex="0" data-stack-month="' + month + '" data-stack-series="' + esc(name) + '" aria-label="Ver títulos: ' + esc(text) + '"></rect>';
      });
      svg += '<text class="stacked-total" x="' + x + '" y="' + (y(plus) - 9) + '" text-anchor="middle"><title>' + esc(money(total)) + '</title>' + esc(compact(total)) + '</text>';
      svg += '<g role="button" tabindex="0" class="stacked-month" data-stack-month="' + month + '" aria-label="Ver todos os títulos de ' + esc(label(month)) + '"><rect x="' + (x - step / 2 + 1) + '" y="' + (height - bottom + 8) + '" width="' + (step - 2) + '" height="28" fill="transparent"/><text x="' + x + '" y="' + (height - 49) + '" text-anchor="middle">' + label(month) + '</text></g>';
      var count = entries(month).length;
      if (count) svg += '<g role="button" tabindex="0" class="stacked-breakdown-toggle" data-stack-breakdown="' + month + '" aria-expanded="false" aria-haspopup="dialog" aria-label="Ver as ' + count + ' categorias de ' + label(month) + ', incluindo as faixas pequenas"><rect x="' + (x - step / 2 + 2) + '" y="' + (height - 36) + '" width="' + (step - 4) + '" height="28" rx="5"/><text x="' + x + '" y="' + (height - 18) + '" text-anchor="middle">' + count + ' categorias ▾</text></g>';
    });
    svg += '</svg>';
    container.innerHTML = '<article class="wide-card stacked-bars"><header><div><h3>' + esc(definition.titulo || 'Parcelas por vencimento') + '</h3><p>Clique em uma faixa para ver os títulos. Em Categorias, consulte também os menores valores de cada mês.</p></div></header>'
      + '<div class="stacked-selection" hidden><strong data-stack-selection></strong><button type="button" data-stack-clear>Mostrar todos os títulos</button></div>'
      + (series.length ? '<div class="stacked-scroll">' + svg + '</div>' : '<p class="empty-state">Não há títulos para o período e os filtros selecionados.</p>')
      + '<p class="stacked-status" role="status" aria-live="polite"></p><div class="stacked-tooltip" role="tooltip" hidden></div>'
      + '<div class="stacked-breakdown" role="dialog" aria-label="Categorias do mês" hidden></div></article>';
    var status = container.querySelector('.stacked-status');
    var tooltip = container.querySelector('.stacked-tooltip'), breakdown = container.querySelector('.stacked-breakdown'), opener = null;
    function position(box, anchor) {
      var bounds = anchor.getBoundingClientRect(), boxWidth = box.offsetWidth, boxHeight = box.offsetHeight;
      box.style.left = Math.max(12, Math.min(window.innerWidth - boxWidth - 12, bounds.left + bounds.width / 2 - boxWidth / 2)) + 'px';
      var below = bounds.bottom + 8, above = bounds.top - boxHeight - 8;
      box.style.top = Math.max(12, Math.min(window.innerHeight - boxHeight - 12, below + boxHeight <= window.innerHeight - 12 ? below : above)) + 'px';
    }
    function hideTooltip() { tooltip.hidden = true; }
    function showTooltip(node) {
      if (busy || !breakdown.hidden) return;
      var month = node.getAttribute('data-stack-month'), name = node.getAttribute('data-stack-series'), point = values[month][name];
      tooltip.innerHTML = '<small>' + label(month) + ' · ' + esc(percentage(month, point.valor)) + ' do mês</small><strong>' + esc(name) + '</strong><b>' + esc(money(point.valor)) + '</b><span>' + point.quantidade + ' títulos · Clique para abrir</span>';
      tooltip.hidden = false; position(tooltip, node);
    }
    function closeBreakdown(restoreFocus) {
      breakdown.hidden = true;
      if (opener) { opener.setAttribute('aria-expanded', 'false'); if (restoreFocus) opener.focus(); }
      opener = null;
    }
    function openBreakdown(node) {
      if (busy) return;
      if (opener === node && !breakdown.hidden) { closeBreakdown(true); return; }
      closeBreakdown(false); hideTooltip(); opener = node;
      var month = node.getAttribute('data-stack-breakdown'), points = entries(month);
      points.sort(function (a, b) { return Math.abs(b.valor) - Math.abs(a.valor) || a.nome.localeCompare(b.nome, 'pt-BR'); });
      var total = points.reduce(function (sum, point) { return sum + point.valor; }, 0);
      breakdown.innerHTML = '<header><div><span>' + label(month) + ' · ' + points.length + ' categorias</span><strong>' + esc(money(total)) + '</strong></div><button type="button" class="stacked-breakdown-close" aria-label="Fechar categorias">×</button></header>'
        + (points.length > 8 ? '<input type="search" class="stacked-category-search" aria-label="Buscar categoria" placeholder="Buscar categoria">' : '')
        + '<div class="stacked-categories">' + points.map(function (point) {
          return '<button type="button" class="stacked-category" data-stack-month="' + month + '" data-stack-series="' + esc(point.nome) + '"><i style="background:' + color(point.nome, series.indexOf(point.nome)) + '"></i><span><strong>' + esc(point.nome) + '</strong><small>' + point.quantidade + ' títulos · ' + esc(percentage(month, point.valor)) + (small(point.valor) ? ' · Faixa pequena' : '') + '</small></span><b>' + esc(money(point.valor)) + '</b></button>';
        }).join('') + '</div><p class="stacked-category-empty" hidden>Nenhuma categoria encontrada.</p><button type="button" class="stacked-all-month" data-stack-month="' + month + '">Ver todos os títulos de ' + label(month) + '</button>';
      breakdown.hidden = false; node.setAttribute('aria-expanded', 'true'); update(); position(breakdown, node);
      var search = breakdown.querySelector('input');
      if (search) search.addEventListener('input', function () {
        var term = search.value.trim().toLocaleLowerCase('pt-BR'), visible = 0;
        breakdown.querySelectorAll('.stacked-category').forEach(function (row) {
          row.hidden = row.getAttribute('data-stack-series').toLocaleLowerCase('pt-BR').indexOf(term) < 0;
          if (!row.hidden) visible++;
        });
        breakdown.querySelector('.stacked-category-empty').hidden = visible > 0;
      });
      (search || breakdown.querySelector('.stacked-category') || breakdown.querySelector('button')).focus();
    }
    function update() {
      container.querySelector('[data-stack-selection]').textContent = selected.periodo ? 'Títulos de ' + label(selected.periodo) + (selected.serie ? ' · ' + selected.serie : ' · todas as categorias') : 'Listagem: todos os títulos do período';
      container.querySelector('.stacked-selection').hidden = !selected.periodo;
      container.querySelector('[data-stack-clear]').hidden = !selected.periodo;
      container.querySelectorAll('[data-stack-month]').forEach(function (node) {
        var match = selected.periodo === node.getAttribute('data-stack-month') && (!selected.serie || node.getAttribute('data-stack-series') === selected.serie);
        node.classList.toggle('stacked-selected', match);
        node.setAttribute('aria-pressed', match ? 'true' : 'false');
        node.setAttribute('aria-disabled', busy ? 'true' : 'false');
      });
      container.querySelector('[data-stack-clear]').disabled = busy;
    }
    function choose(next) {
      if (!active || busy) return;
      closeBreakdown(false); hideTooltip();
      busy = true; update(); status.textContent = 'Carregando os títulos considerados…';
      Promise.resolve().then(function () { return options.change(next); }).then(function (result) {
        if (!active) return;
        selected = next;
        status.textContent = result && result.total != null ? result.total + ' títulos na listagem.' : '';
      }).catch(function (error) { if (active) status.textContent = error.message || 'Não foi possível filtrar os títulos.'; })
        .finally(function () { if (active) { busy = false; update(); } });
    }
    function activate(event) {
      var node = event.target.closest('[data-stack-breakdown], [data-stack-month], .stacked-breakdown-close');
      if (!node || !container.contains(node)) return;
      if (event.type === 'keydown') {
        if (node.tagName.toLowerCase() === 'button' || (event.key !== 'Enter' && event.key !== ' ')) return;
        event.preventDefault();
      }
      if (node.classList.contains('stacked-breakdown-close')) closeBreakdown(true);
      else if (node.hasAttribute('data-stack-breakdown')) openBreakdown(node);
      else choose({ periodo: node.getAttribute('data-stack-month'), serie: node.getAttribute('data-stack-series') || '' });
    }
    container.addEventListener('click', activate); container.addEventListener('keydown', activate);
    container.querySelectorAll('.stacked-segment').forEach(function (node) {
      node.addEventListener('pointerenter', function () { showTooltip(node); });
      node.addEventListener('focus', function () { showTooltip(node); });
      node.addEventListener('pointerleave', hideTooltip); node.addEventListener('blur', hideTooltip);
    });
    function outside(event) { if (!breakdown.hidden && !breakdown.contains(event.target) && !event.target.closest('[data-stack-breakdown]')) closeBreakdown(false); }
    function escape(event) { if (event.key === 'Escape') { hideTooltip(); if (!breakdown.hidden) { event.stopPropagation(); closeBreakdown(true); } } }
    function viewport(event) {
      if (event.target && event.target.nodeType && breakdown.contains(event.target)) return;
      hideTooltip();
      if (event.type === 'resize' && opener && !breakdown.hidden) position(breakdown, opener);
      else closeBreakdown(false);
    }
    document.addEventListener('pointerdown', outside); document.addEventListener('keydown', escape);
    window.addEventListener('resize', viewport); window.addEventListener('scroll', viewport, true);
    container.querySelector('[data-stack-clear]').addEventListener('click', function () { choose({ periodo: '', serie: '' }); });
    update();
    return { destroy: function () {
      active = false; hideTooltip(); closeBreakdown(false);
      container.removeEventListener('click', activate); container.removeEventListener('keydown', activate);
      document.removeEventListener('pointerdown', outside); document.removeEventListener('keydown', escape);
      window.removeEventListener('resize', viewport); window.removeEventListener('scroll', viewport, true);
    } };
  }
  window.DashboardStackedBars = { mount: mount };
}());
