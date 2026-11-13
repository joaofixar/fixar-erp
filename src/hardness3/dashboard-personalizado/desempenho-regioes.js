(function () {
  'use strict';
  function node(tag, css, text) {
    var e = document.createElement(tag);
    if (css) e.className = css;
    if (text !== undefined) e.textContent = text;
    return e;
  }
  function money(value) { return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
  function mount(host, data) {
    var selectedUf = null, selectedCity = null, selectedRegion = null, regional = false, page = 0, pageSize = 25;
    var map = window.DASHBOARD_CARTEIRA_MAP || window.BRAZIL_MAP || { locations: [], viewBox: '0 0 613 639' };
    var names = {};
    map.locations.forEach(function (location) { names[location.id.toUpperCase()] = location.name; });
    function ufName(uf) { return uf ? uf + ' — ' + (names[uf] || uf) : 'UF não informada'; }
    function choose(uf, city, region) {
      selectedUf = uf; selectedCity = city; selectedRegion = region == null ? null : String(region); page = 0; draw();
      var back = host.querySelector('[data-region-back-uf]') || host.querySelector('[data-region-brasil]');
      if (back) back.focus({ preventScroll: true });
    }
    function button(text, action, attribute, value) {
      var b = node('button', 'secondary', text); b.type = 'button';
      if (attribute) b.setAttribute(attribute, value);
      b.addEventListener('click', action); return b;
    }
    function monthlyDetail(line) {
      var table = node('table', 'comparison-table commercial-region-months');
      table.setAttribute('aria-label', 'Orçamentos e pedidos mensais de ' + line.cliente);
      var head = node('thead'), labels = node('tr');
      ['Mês', 'Orçamentos', 'Pedidos'].forEach(function (text) { var th = node('th', '', text); th.scope = 'col'; labels.appendChild(th); });
      head.appendChild(labels); table.appendChild(head);
      var body = node('tbody'), names = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
      (data.meses || []).forEach(function (month) {
        var row = node('tr'), label = names[Number(month.slice(5)) - 1] + '/' + month.slice(0, 4);
        var th = node('th', '', label); th.scope = 'row'; row.appendChild(th);
        var included = !data.periodo || (month >= data.periodo.inicio.slice(0, 7) && month <= data.periodo.fim.slice(0, 7));
        var values = (line.meses || {})[month] || {};
        ['orcamentos', 'pedidos'].forEach(function (kind) { row.appendChild(node('td', '', included ? money(values[kind] || 0) : '—')); });
        body.appendChild(row);
      });
      table.appendChild(body);
      var footer = node('tfoot'), total = node('tr'); total.appendChild(node('th', '', 'TOTAL DO PERÍODO'));
      ['orcamentos', 'pedidos'].forEach(function (kind) { total.appendChild(node('td', '', money(line.totais[kind]))); });
      footer.appendChild(total); table.appendChild(footer); return table;
    }
    function draw() {
      host.textContent = '';
      var uf = data.ufs.filter(function (item) { return item.uf === selectedUf; })[0];
      var city = uf && uf.cidades.filter(function (item) { return String(item.cidadeId) === selectedCity; })[0];
      var region = regional && uf && (uf.regioes || []).filter(function (item) { return String(item.regiaoId) === selectedRegion; })[0];
      var current = region || uf || data;
      var clientLevel = !!uf && (!regional || !!region);
      var header = node('header'), title = node('div');
      title.appendChild(node('p', 'card-kicker', 'CARTEIRA DE CLIENTES • REGIÕES'));
      title.appendChild(node('h3', '', 'Atuação por região'));
      title.appendChild(node('p', 'comparison-description', regional ? 'Regiões vinculadas às cidades no cadastro do sistema. Selecione uma UF e uma região para ver seus clientes. Clique no cliente para abrir seus orçamentos e pedidos por mês. Valores da carteira do vendedor no período filtrado.' : 'Endereços do cadastro dos clientes vinculados ao vendedor principal. Selecione uma UF para ver seus clientes e cidades. Valores de orçamentos e pedidos no período filtrado.'));
      header.appendChild(title);
      if (data.territorios) {
        var mode = button('Regiões cadastradas', function () {
          regional = !regional; selectedRegion = null; selectedCity = null; page = 0; draw();
          var currentMode = host.querySelector('[data-region-mode]'); if (currentMode) currentMode.focus({ preventScroll: true });
        }, 'data-region-mode', 'true');
        mode.className = 'commercial-view commercial-region-mode secondary' + (regional ? ' active' : '');
        mode.setAttribute('aria-pressed', String(regional)); header.appendChild(mode);
      }
      host.appendChild(header);
      var crumbs = node('nav', 'commercial-region-breadcrumb'); crumbs.setAttribute('aria-label', 'Navegação por região');
      crumbs.appendChild(button('Brasil', function () { choose(null, null); }, 'data-region-brasil', 'true'));
      if (uf) crumbs.appendChild(button(ufName(uf.uf), function () { choose(uf.uf, null); }, 'data-region-back-uf', uf.uf));
      if (region) crumbs.appendChild(button(region.regiao, function () { choose(uf.uf, null, region.regiaoId); }, 'data-region-back-region', region.regiaoId));
      if (city) crumbs.appendChild(node('span', '', city.cidade));
      host.appendChild(crumbs);
      var summary = node('p', 'commercial-region-summary', current.quantidade.toLocaleString('pt-BR') + ' clientes • Orçamentos: ' + money(current.totais.orcamentos) + ' • Pedidos: ' + money(current.totais.pedidos));
      summary.setAttribute('aria-live', 'polite'); host.appendChild(summary);
      var layout = node('div', 'commercial-region-layout'), mapHost = node('div', 'commercial-region-map');
      var mapCard = node('section', 'commercial-territory-card'), mapHeader = node('div', 'commercial-territory-heading');
      var mapTitle = node('div');
      mapTitle.appendChild(node('p', 'commercial-territory-kicker', 'PRESENÇA COMERCIAL'));
      mapTitle.appendChild(node('h4', '', 'Seu território de vendas'));
      mapTitle.appendChild(node('p', '', 'Explore as regiões e descubra onde sua carteira está presente.'));
      mapHeader.appendChild(mapTitle);
      var badge = node('span', 'commercial-territory-live', uf ? uf.uf || 'BRASIL' : 'BRASIL');
      badge.prepend(node('i')); mapHeader.appendChild(badge); mapCard.appendChild(mapHeader);
      var stage = node('div', 'commercial-territory-stage'); stage.appendChild(mapHost); mapCard.appendChild(stage);
      var ns = 'http://www.w3.org/2000/svg';
      var svg = document.createElementNS(ns, 'svg'); svg.setAttribute('viewBox', map.viewBox);
      svg.setAttribute('role', 'group'); svg.setAttribute('aria-label', uf ? 'Mapa da UF ' + ufName(uf.uf) : 'Mapa do Brasil: UFs com clientes da carteira');
      var largest = Math.max(1, ...data.ufs.map(function (item) { return item.quantidade; }));
      var zoomPath = null;
      map.locations.forEach(function (location) {
        var code = location.id.toUpperCase(), item = data.ufs.filter(function (entry) { return entry.uf === code; })[0];
        var path = document.createElementNS(ns, 'path'); path.setAttribute('d', location.path);
        path.setAttribute('class', 'commercial-region-state' + (item ? ' has-clients' : '') + (selectedUf === code ? ' selected' : ''));
        path.setAttribute('fill', item ? window.DashboardRegioesVisual.color(location.regiao) : '#25445e');
        path.style.fillOpacity = item ? .38 + .5 * Math.sqrt(item.quantidade / largest) : .7;
        var tooltip = document.createElementNS(ns, 'title'); tooltip.textContent = code + ' — ' + location.name + ': ' + (item ? item.quantidade : 0) + ' clientes'; path.appendChild(tooltip);
        if (item) {
          path.setAttribute('tabindex', '0'); path.setAttribute('role', 'button');
          path.setAttribute('data-region-map-uf', code); path.setAttribute('aria-label', tooltip.textContent);
          path.addEventListener('click', function () { choose(code, null); });
          path.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(code, null); } });
        }
        if (selectedUf === code) zoomPath = path;
        svg.appendChild(path);
      });
      window.DashboardRegioesVisual.details(svg, map, data, selectedUf, function (code) { choose(code, null); });
      mapHost.appendChild(svg);
      var visual = window.DashboardRegioesVisual.mount(stage, mapHost, {
        title: region ? region.regiao : uf ? ufName(uf.uf) : 'Visão nacional',
        mode: regional ? 'Regiões cadastradas' : 'Carteira de clientes',
        count: current.quantidade,
        reset: function () { choose(null, null); }
      });
      if (regional && (!uf || uf.uf)) {
        var mapRegions = uf ? uf.regioes || [] : data.ufs.reduce(function (all, entry) { return all.concat((entry.regioes || []).map(function (region) { return Object.assign({ uf: entry.uf }, region); })); }, []);
        window.DashboardRegioesMapa.paint(mapHost, uf ? uf.uf : null, data.territorios, mapRegions, selectedRegion, function (id, code) { choose(code || uf.uf, null, id); }, visual.refresh);
      }
      if (!regional) mapCard.appendChild(node('p', 'commercial-territory-note', uf && uf.uf ? 'UF selecionada: ' + ufName(uf.uf) : 'Cores por região do Brasil. Intensidade conforme os clientes da carteira. Pontos por município com localização IBGE válida.'));
      layout.appendChild(mapCard);
      var list = node('div', 'commercial-region-list'), scroll = node('div', 'table-scroll');
      var table = node('table', 'comparison-table commercial-table');
      var thead = node('thead'), headings = node('tr');
      [clientLevel ? 'Cliente / Cidade' : regional && uf ? 'Região cadastrada' : 'UF', 'Clientes', 'Orçamentos', 'Pedidos'].forEach(function (label) { headings.appendChild(node('th', '', label)); });
      thead.appendChild(headings); table.appendChild(thead);
      var tbody = node('tbody'), lines = clientLevel ? uf.cidades.filter(function (item) { return !region || String(item.regiaoId) === selectedRegion; }).reduce(function (all, item) {
        return all.concat(item.clientes.map(function (client) { return Object.assign({}, client, { cidade: item.cidade }); }));
      }, []).sort(function (a, b) { return Number(b.totais.orcamentos) - Number(a.totais.orcamentos) || Number(b.totais.pedidos) - Number(a.totais.pedidos) || a.cliente.localeCompare(b.cliente, 'pt-BR'); }) : regional && uf ? uf.regioes || [] : data.ufs;
      lines.slice(page * pageSize, (page + 1) * pageSize).forEach(function (line, index) {
        var row = node('tr'), label = node('th', 'commercial-client'); label.scope = 'row';
        var detail = null;
        if (clientLevel) {
          if (regional) {
            detail = node('tr', 'commercial-region-detail'); detail.hidden = true; detail.id = 'region-client-months-' + index;
            var detailCell = node('td'); detailCell.colSpan = 4; detail.appendChild(detailCell);
            var toggle = button('▸ ' + line.cliente, function () {
              detail.hidden = !detail.hidden;
              toggle.setAttribute('aria-expanded', String(!detail.hidden));
              toggle.textContent = (detail.hidden ? '▸ ' : '▾ ') + line.cliente;
              if (!detail.hidden && !detailCell.firstChild) detailCell.appendChild(monthlyDetail(line));
            }, 'data-region-client-months', line.clienteId);
            toggle.className = 'commercial-representative-toggle';
            toggle.setAttribute('aria-expanded', 'false'); toggle.setAttribute('aria-controls', detail.id);
            label.appendChild(toggle);
          } else label.appendChild(node('strong', '', line.cliente));
          label.appendChild(node('small', '', line.cidade + ' • ' + line.vendedor));
        }
        else if (regional && uf && !region) label.appendChild(button(line.regiao, function () { choose(uf.uf, null, line.regiaoId); }, 'data-region-item', line.regiaoId));
        else if (uf) label.appendChild(button(line.cidade, function () { choose(uf.uf, String(line.cidadeId), selectedRegion); }, 'data-region-city', line.cidadeId));
        else label.appendChild(button(ufName(line.uf), function () { choose(line.uf, null); }, 'data-region-uf', line.uf));
        row.appendChild(label); row.appendChild(node('td', '', clientLevel ? '1' : line.quantidade.toLocaleString('pt-BR')));
        row.appendChild(node('td', '', money(line.totais.orcamentos))); row.appendChild(node('td', '', money(line.totais.pedidos))); tbody.appendChild(row);
        if (detail) tbody.appendChild(detail);
      });
      if (!lines.length) { var empty = node('td', 'empty-state', 'Nenhum cliente encontrado nesta carteira.'); empty.colSpan = 4; var emptyRow = node('tr'); emptyRow.appendChild(empty); tbody.appendChild(emptyRow); }
      table.appendChild(tbody);
      var footer = node('tfoot'), total = node('tr');
      total.appendChild(node('th', '', 'TOTAL')); total.appendChild(node('td', '', current.quantidade.toLocaleString('pt-BR')));
      total.appendChild(node('td', '', money(current.totais.orcamentos))); total.appendChild(node('td', '', money(current.totais.pedidos))); footer.appendChild(total); table.appendChild(footer);
      scroll.appendChild(table); list.appendChild(scroll);
      var pagination = node('div', 'commercial-pagination');
      pagination.appendChild(node('span', '', lines.length ? (page * pageSize + 1) + '–' + Math.min((page + 1) * pageSize, lines.length) + ' de ' + lines.length : '0 registros'));
      [-1, 1].forEach(function (direction) { var control = button(direction < 0 ? 'Anterior' : 'Próxima', function () { page += direction; draw(); }); control.disabled = direction < 0 ? page === 0 : (page + 1) * pageSize >= lines.length; pagination.appendChild(control); });
      list.appendChild(pagination); layout.appendChild(list); host.appendChild(layout);
      window.requestAnimationFrame(function () {
        if (!svg.isConnected) return;
        if (zoomPath) { var box = zoomPath.getBBox(), pad = Math.max(box.width, box.height) * .12; svg.setAttribute('viewBox', [box.x - pad, box.y - pad, box.width + 2 * pad, box.height + 2 * pad].join(' ')); }
        visual.refresh();
      });
    }
    return { show: draw };
  }
  window.DashboardDesempenhoRegioes = { mount: mount };
}());
