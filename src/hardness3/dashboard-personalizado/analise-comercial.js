(function () {
  'use strict';
  var number = function (v) { return Number(v || 0).toLocaleString('pt-BR', {maximumFractionDigits: 0}); };
  var money = function (v) { return Number(v || 0).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}); };
  function node(tag, text, css) {
    var el = document.createElement(tag);
    if (text != null) el.textContent = text;
    if (css) el.className = css;
    return el;
  }
  function normalize(value) {
    var text = String(value || '').trim();
    var key = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    return key === 'construcao' || key === 'construtora' ? 'Construtora' : key === 'revenda' ? 'Revenda' : key === 'obra' ? 'Obra' : text || 'Não classificado';
  }
  function row(host, name, amount, detail, subdetail) {
    var item = node('div', null, 'commercial-analysis-row'), copy = node('span'), values = node('span');
    copy.appendChild(node('b', name));
    if (detail) copy.appendChild(node('small', detail));
    values.appendChild(node('b', amount));
    if (subdetail) values.appendChild(node('small', subdetail));
    item.appendChild(copy); item.appendChild(values); host.appendChild(item);
  }
  function segments(host, rows) {
    host.textContent = '';
    var groups = new Map(['Revenda', 'Construtora', 'Obra'].map(function (name) { return [name, {quantidade: 0, valor: 0}]; }));
    var total = 0;
    (rows || []).forEach(function (item) {
      var name = normalize(item.segmento);
      if (!groups.has(name)) groups.set(name, {quantidade: 0, valor: 0});
      var group = groups.get(name);
      group.quantidade += Number(item.quantidade || 0); group.valor += Number(item.valor || 0); total += Number(item.valor || 0);
    });
    groups.forEach(function (item, name) {
      var percent = total ? 100 * item.valor / total : 0;
      row(host, name, money(item.valor), number(item.quantidade) + ' documentos', percent.toLocaleString('pt-BR', {maximumFractionDigits: 1}) + '% do valor total');
    });
    host.appendChild(node('p', 'Total: ' + money(total), 'commercial-analysis-total'));
  }
  function cancellations(host, rows) {
    host.textContent = '';
    var groups = new Map(), count = 0, total = 0;
    (rows || []).forEach(function (item) {
      var name = String(item.motivo || 'Motivo não informado');
      if (!groups.has(name)) groups.set(name, {motivo: name, quantidade: 0, valor: 0});
      var group = groups.get(name);
      group.quantidade += Number(item.quantidade || 0); group.valor += Number(item.valor || 0);
      count += Number(item.quantidade || 0); total += Number(item.valor || 0);
    });
    host.appendChild(node('p', number(count) + ' cancelamentos · ' + money(total), 'commercial-analysis-total'));
    if (!groups.size) host.appendChild(node('p', 'Nenhum cancelamento com motivo neste período.', 'comparison-description'));
    Array.from(groups.values()).sort(function (a, b) { return b.quantidade - a.quantidade || a.motivo.localeCompare(b.motivo, 'pt-BR'); }).slice(0, 30).forEach(function (item) {
      row(host, item.motivo, number(item.quantidade), '', money(item.valor));
    });
  }
  function section(title, description) {
    var article = node('section', null, 'commercial-analysis-section');
    article.appendChild(node('h4', title)); article.appendChild(node('p', description, 'comparison-description'));
    return article;
  }
  function render(host, data) {
    host.textContent = ''; host.className = 'wide-card commercial-analysis commercial-analysis-summary';
    host.setAttribute('aria-label', 'Distritos e cancelamentos do período');
    var heading = node('div', null, 'commercial-analysis-heading');
    heading.appendChild(node('p', 'ANÁLISE DO PERÍODO', 'card-kicker'));
    heading.appendChild(node('h3', 'Distritos e motivos de cancelamento'));
    host.appendChild(heading);
    var sections = node('div', null, 'commercial-analysis-sections');
    if (!data.segmentos || !data.cancelamentos) sections.className += ' commercial-analysis-sections-single';
    host.appendChild(sections);
    if (data.segmentos) {
      var article = section('Vendas por distrito', 'Quantidade, valor e participação no período.');
      var select = node('select'); select.setAttribute('aria-label', 'Etapa comercial da distribuição por distrito');
      [['orcamentos', 'Orçamentos'], ['pedidos', 'Pedidos'], ['notas', 'Notas emitidas']].forEach(function (pair) {
        var option = node('option', pair[1]); option.value = pair[0]; select.appendChild(option);
      });
      select.value = 'orcamentos'; article.appendChild(select);
      var breakdown = node('div'); article.appendChild(breakdown);
      function draw() { segments(breakdown, data.segmentos[select.value] || []); }
      select.addEventListener('change', draw); draw(); sections.appendChild(article);
    }
    if (data.cancelamentos) {
      var cancelled = section('Orçamentos cancelados', 'Principais motivos do período. Revisões sem motivo não são contabilizadas.');
      var body = node('div', null, 'commercial-analysis-reasons');
      body.setAttribute('tabindex', '0'); body.setAttribute('aria-label', 'Lista de motivos de cancelamento');
      cancelled.appendChild(body); cancellations(body, data.cancelamentos); sections.appendChild(cancelled);
    }
  }
  window.DashboardAnaliseComercial = {segments: segments, cancellations: cancellations, render: render};
})();
