(() => {
  'use strict';
  const el = (tag, text, cls) => { const node = document.createElement(tag); if (text != null) node.textContent = text; if (cls) node.className = cls; return node; };
  const number = value => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(Number(value || 0));
  const money = value => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value || 0));
  const percent = value => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format(value) + '%';
  const svg = (tag, attrs) => { const node = document.createElementNS('http://www.w3.org/2000/svg', tag); Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value)); return node; };
  function trend(chart, series, x, y, prefix) {
    const defs = svg('defs', {});
    chart.append(defs);
    series.forEach(({ key, color, values }) => {
      const id = prefix + '-area-' + key, gradient = svg('linearGradient', { id, x1: 0, y1: 0, x2: 0, y2: 1 });
      gradient.append(svg('stop', { offset: '0%', 'stop-color': color, 'stop-opacity': '.22' }), svg('stop', { offset: '100%', 'stop-color': color, 'stop-opacity': '0' }));
      defs.append(gradient);
      const points = values.map((value, i) => `${x(i)},${y(value)}`), path = 'M' + points.join(' L');
      chart.append(svg('path', { d: path + ` L${x(values.length - 1)},${y(0)} L${x(0)},${y(0)} Z`, fill: `url(#${id})`, 'pointer-events': 'none' }));
      const line = svg('path', { d: path, fill: 'none', class: 'home-line ' + key, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' });
      // A política do portal bloqueia setAttribute('style', ...); use a API de estilos.
      line.style.stroke = color;
      chart.append(line);
      values.forEach((value, i) => {
        if (value === 0 && values.length !== 1) return;
        const point = svg('circle', { cx: x(i), cy: y(value), r: 3, class: 'home-point ' + key });
        point.style.fill = color;
        chart.append(point);
      });
    });
  }
  function segments(host, rows, total, onSelect, selected) {
    host.replaceChildren();
    const grouped = new Map(['Revenda','Construção','Obra'].map(segmento => [segmento, {segmento, quantidade: 0, valor: 0}]));
    (rows || []).forEach(row => {
      const label = row.segmento || 'Não classificado';
      if (!grouped.has(label)) grouped.set(label, {segmento: label, quantidade: 0, valor: 0});
      const result = grouped.get(label); result.quantidade += Number(row.quantidade || 0); result.valor += Number(row.valor || 0);
    });
    grouped.forEach(row => {
      const ratio = Number(total) ? Number(row.valor || 0) / Number(total) * 100 : 0;
      const selectable = typeof onSelect === 'function' && ['Revenda','Construção','Obra'].includes(row.segmento);
      const item = el(selectable ? 'button' : 'div', null, 'commercial-breakdown-row' + (selectable ? ' commercial-breakdown-action' : '')), copy = el('span'), values = el('span');
      if (selectable) {
        item.type = 'button'; item.dataset.quoteSegment = row.segmento;
        item.setAttribute('aria-pressed', String(selected === row.segmento)); item.setAttribute('aria-controls', 'documents');
        item.title = 'Ver orçamentos de ' + row.segmento;
        item.addEventListener('click', () => onSelect(row.segmento));
      }
      copy.append(el('b', row.segmento), el('small', number(row.quantidade) + ' documentos'));
      values.append(el('b', money(row.valor)), el('small', percent(ratio) + ' do valor total'));
      item.append(copy, values); host.append(item);
    });
    host.append(el('p', 'Total: ' + money(total), 'commercial-breakdown-total'));
  }
  function cancellations(host, rows, summary) {
    host.replaceChildren();
    host.append(el('p', number(summary.cancelados) + ' cancelamentos · ' + money(summary.valorCancelado), 'commercial-breakdown-total'));
    if (!rows || !rows.length) { host.append(el('p', 'Nenhum cancelamento com motivo neste período.', 'analytics-empty')); return; }
    rows.forEach(row => {
      const item = el('div', null, 'commercial-breakdown-row'), copy = el('span'), values = el('span');
      copy.append(el('b', row.motivo || 'Motivo não informado'));
      values.append(el('b', number(row.quantidade)), el('small', money(row.valor)));
      item.append(copy, values); host.append(item);
    });
  }
  window.PortalAnaliseComercial = { trend, segments, cancellations };
})();
