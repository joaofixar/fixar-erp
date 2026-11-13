(function () {
  'use strict';
  var tipos = [['orcamentos', 'Orçamentos'], ['pedidos', 'Pedidos'], ['notas', 'Notas emitidas']];
  var nomesMeses = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
  function node(tag, className, text) {
    var element = document.createElement(tag);
    if (className) element.className = className;
    if (text !== undefined) element.textContent = text;
    return element;
  }
  function moeda(value) { return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
  function numero(value) { return Number(value).toLocaleString('pt-BR'); }
  function valor(value, type) {
    if (value === null || value === undefined) return '—';
    return type === 'percentual' ? Number(value).toLocaleString('pt-BR', { maximumFractionDigits: 1 }) + '%' : moeda(value);
  }
  function comparativo(card, data, key) {
    var comparison = data.comparativo;
    if (!comparison || !comparison.metricas[key]) return;
    var metric = comparison.metricas[key];
    var direction = metric.tendencia;
    var css = direction === 'alta' ? 'up' : direction === 'baixa' ? 'down' : 'neutral';
    var arrow = direction === 'alta' ? '↑' : direction === 'baixa' ? '↓' : direction === 'estavel' ? '→' : '—';
    var text = metric.percentual === null ? 'Sem base percentual' : (metric.percentual > 0 ? '+' : '') + valor(metric.percentual, 'percentual') + ' vs mês anterior';
    card.className += ' commercial-trend-' + css;
    var badge = node('span', 'commercial-comparison', arrow + ' ' + text);
    badge.setAttribute('aria-label', (direction === 'alta' ? 'Aumento: ' : direction === 'baixa' ? 'Queda: ' : direction === 'estavel' ? 'Estável: ' : '') + text);
    card.appendChild(badge);
    var multiMonth = key === 'valorOrcado' && data.periodo.inicio.slice(0, 7) !== data.mesReferencia;
    card.appendChild(node('small', 'commercial-comparison-values', (multiMonth ? 'Neste mês: ' + valor(metric.atual) + ' · ' : '') + 'Anterior: ' + valor(metric.anterior)));
    function range(period) { return period.inicio.split('-').reverse().join('/') + ' a ' + period.fim.split('-').reverse().join('/'); }
    card.appendChild(node('small', 'commercial-comparison-dates', range(comparison.periodos.atual) + ' × ' + range(comparison.periodos.anterior)));
  }
  function render(container, data, initialTab, loadClientHistory) {
    container.textContent = '';
    var panel = node('div', 'commercial-performance');
    var reference = data.mesReferencia.slice(5) + '/' + data.mesReferencia.slice(0, 4);
    var resumo = data.resumo;
    var metaSemAtingimento = resumo.meta != null && resumo.atingimentoMeta == null;
    var notasTotal = data.valorNotas === 'total';
    var heading = node('div', 'commercial-heading');
    heading.appendChild(node('p', 'eyebrow', 'RESULTADOS COMERCIAIS'));
    heading.appendChild(node('p', '', 'Referência mensal: ' + reference + ' • Período filtrado: ' + data.periodo.inicio.split('-').reverse().join('/') + ' a ' + data.periodo.fim.split('-').reverse().join('/')));
    panel.appendChild(heading);
    var cards = node('div', 'commercial-kpis');
    [
      ['Faturamento no mês', valor(resumo.faturamento), notasTotal ? 'Valor total das notas autorizadas, incluindo IPI e frete.' : 'Notas de venda autorizadas, descontando IPI e frete.', 'faturamento'],
      ['Valor dos pedidos', valor(resumo.valorPedidos), 'Valor total dos pedidos válidos no mês de referência, dentro do período filtrado.', 'valorPedidos'],
      [metaSemAtingimento ? 'Meta do mês' : 'Meta atingida', resumo.meta == null ? 'Não definida' : metaSemAtingimento ? moeda(resumo.meta) : valor(resumo.atingimentoMeta, 'percentual'),
        resumo.metaAviso || (resumo.meta == null ? (Object.prototype.hasOwnProperty.call(resumo, 'valorMetaAtingido') ? 'Meta comercial não cadastrada para o mês de referência.' : 'Meta comercial ainda não configurada.') : metaSemAtingimento ? 'Valor cadastrado para o mês de referência. Configuração > Comercial > Acompanhamento Meta Vendedor.' : 'Meta do mês: ' + moeda(resumo.meta) + '. ' + (resumo.valorMetaAtingido == null ? 'Calcule o valor atingido no ERP.' : 'Atingido: ' + moeda(resumo.valorMetaAtingido) + '. Pedidos registrados no ERP.') + ' Configuração > Comercial > Acompanhamento Meta Vendedor.')],
      ['Projeção do mês', valor(resumo.projecao), resumo.projecao === null ? 'Disponível para períodos que incluem o início do mês e terminam até hoje.' : 'Faturamento ÷ dias corridos até a data final × dias do mês.'],
      ['Ticket médio pedido', valor(resumo.ticketMedio), 'Valor dos pedidos ÷ quantidade de pedidos válidos no mês de referência, dentro do período filtrado.', 'ticketMedio'],
      ['Ticket médio orçamento', valor(resumo.ticketMedioOrcamento), 'Valor dos orçamentos ÷ quantidade de orçamentos válidos no mês de referência, dentro do período filtrado.', 'ticketMedioOrcamento'],
      ['Valor orçado', valor(resumo.valorOrcado), 'Total dos orçamentos válidos no período filtrado.', 'valorOrcado'],
      ['Taxa de conversão', valor(resumo.conversao, 'percentual'), resumo.orcamentosConvertidos + ' de ' + resumo.quantidadeOrcamentos + ' orçamentos do período geraram ao menos um pedido válido.']
    ].forEach(function (item) {
      var card = node('article', 'commercial-kpi');
      card.appendChild(node('span', '', item[0]));
      card.appendChild(node('strong', '', item[1]));
      if (item[3]) comparativo(card, data, item[3]);
      card.appendChild(node('small', '', item[2]));
      cards.appendChild(card);
    });
    panel.appendChild(cards);
    var stats = node('div', 'commercial-stats');
    [numero(resumo.quantidades.pedidos) + ' pedidos',
      numero(resumo.clientesAtendidos) + ' clientes atendidos', numero(resumo.clientesOrcados) + ' clientes orçados',
      numero(resumo.quantidades.notas) + ' notas emitidas'].forEach(function (text) { stats.appendChild(node('span', '', text)); });
    panel.appendChild(stats);
    var article = node('article', 'wide-card commercial-matrix');
    var header = node('header');
    var title = node('div');
    title.appendChild(node('p', 'card-kicker', 'PERFORMANCE DE VENDAS • ' + data.periodo.fim.slice(0, 4)));
    var tableTitle = node('h3'); title.appendChild(tableTitle);
    var description = node('p', 'comparison-description');
    title.appendChild(description);
    header.appendChild(title);
    var viewLabel = node('label', 'commercial-view', 'Visão da tabela');
    var view = node('select');
    [['periodo', 'Resumo do período'], ['mensal', 'Evolução mensal']].forEach(function (option) {
      var item = node('option', '', option[1]); item.value = option[0]; view.appendChild(item);
    });
    viewLabel.appendChild(view); header.appendChild(viewLabel);
    article.appendChild(header);
    var scroll = node('div', 'table-scroll commercial-table-scroll');
    scroll.tabIndex = 0;
    scroll.setAttribute('role', 'region');
    scroll.setAttribute('aria-label', 'Tabela mensal de clientes. Role horizontalmente para ver todos os meses.');
    article.appendChild(scroll);
    var pagination = node('div', 'commercial-pagination');
    var previous = node('button', 'secondary', 'Anterior'); previous.type = 'button';
    var next = node('button', 'secondary', 'Próxima'); next.type = 'button';
    var count = node('span'); count.setAttribute('aria-live', 'polite');
    pagination.appendChild(count); pagination.appendChild(previous); pagination.appendChild(next);
    article.appendChild(pagination);
    var page = 0, pageSize = 25, monthly = false;
    var representatives = !!data.carteiraRepresentantes && initialTab === 'representantes';
    var chart = node('article', 'wide-card commercial-history');
    var chartHeader = node('header'), chartHeading = node('div');
    var chartTitle = node('h3'), chartDescription = node('p', 'comparison-description');
    chartHeading.appendChild(chartTitle); chartHeading.appendChild(chartDescription); chartHeader.appendChild(chartHeading);
    var resetChart = node('button', 'secondary', 'Voltar ao período filtrado'); resetChart.type = 'button';
    chartHeader.appendChild(resetChart); chart.appendChild(chartHeader);
    var chartBody = node('div', 'commercial-history-body');
    chartBody.setAttribute('aria-live', 'polite'); chart.appendChild(chartBody);
    var selectedClient = null, historyRequest = 0;
    function drawChart(source, line) {
      chartBody.textContent = '';
      chartTitle.textContent = line ? 'Orçamentos e pedidos — ' + line.cliente : 'Orçamentos e pedidos do vendedor';
      chartDescription.textContent = (line ? 'Últimos seis meses • ' + line.responsavel + ' • ' : 'Período filtrado • ') + source.periodo.inicio.split('-').reverse().join('/') + ' a ' + source.periodo.fim.split('-').reverse().join('/');
      resetChart.hidden = !line;
      var months = source.meses.filter(function (month) { return month >= source.periodo.inicio.slice(0, 7) && month <= source.periodo.fim.slice(0, 7); });
      var maximum = 0, totals = { orcamentos: 0, pedidos: 0 };
      months.forEach(function (month) { tipos.slice(0, 2).forEach(function (type) {
        var amount = Number((source.totaisMeses[month] || {})[type[0]] || 0);
        maximum = Math.max(maximum, Math.abs(amount)); totals[type[0]] += amount;
      }); });
      var legend = node('div', 'commercial-history-totals');
      tipos.slice(0, 2).forEach(function (type) { legend.appendChild(node('span', 'commercial-label-' + type[0], type[1] + ': ' + moeda(totals[type[0]]))); });
      chartBody.appendChild(legend);
      var plot = node('div', 'commercial-history-plot');
      months.forEach(function (month) {
        var group = node('div', 'commercial-history-month');
        group.appendChild(node('strong', '', nomesMeses[Number(month.slice(5)) - 1] + '/' + month.slice(0, 4)));
        tipos.slice(0, 2).forEach(function (type) {
          var amount = Number((source.totaisMeses[month] || {})[type[0]] || 0);
          var barRow = node('div', 'commercial-history-bar-row');
          var track = node('div', 'commercial-history-track');
          var bar = node('div', 'commercial-history-bar commercial-history-' + type[0]);
          bar.style.width = (maximum ? Math.abs(amount) / maximum * 100 : 0) + '%';
          barRow.setAttribute('aria-label', type[1] + ': ' + moeda(amount));
          track.appendChild(bar); barRow.appendChild(track); barRow.appendChild(node('span', '', moeda(amount))); group.appendChild(barRow);
        });
        plot.appendChild(group);
      });
      chartBody.appendChild(plot);
      if (!maximum) chartBody.appendChild(node('p', 'empty-state', 'Nenhum orçamento ou pedido neste período.'));
      if (!line) chartBody.appendChild(node('p', 'comparison-description', 'Clique em um cliente da listagem para consultar seus últimos seis meses.'));
    }
    function selectClient(line) {
      selectedClient = line;
      var requestId = ++historyRequest;
      chartTitle.textContent = 'Orçamentos e pedidos — ' + line.cliente;
      chartDescription.textContent = 'Últimos seis meses • ' + line.responsavel;
      resetChart.hidden = false;
      chartBody.textContent = 'Carregando histórico do cliente…';
      draw();
      loadClientHistory(line).then(function (history) {
        if (requestId === historyRequest) drawChart(history, line);
      }).catch(function (error) {
        if (requestId === historyRequest) chartBody.textContent = error.message || 'Não foi possível carregar o histórico. Clique no cliente para tentar novamente.';
      });
    }
    resetChart.addEventListener('click', function () { historyRequest++; selectedClient = null; drawChart(data); draw(); });
    var expanded = {}, clientPages = {}, sortType = 'orcamentos', sortMonth = null;
    var representativeSortType = 'orcamentos', representativeSortMonth = null;
    function hasExpandedRepresentative() { return Object.keys(expanded).some(function (id) { return expanded[id]; }); }
    function ordered(lines, type, month) {
      type = type || sortType;
      if (month === undefined) month = sortMonth;
      return lines.slice().sort(function (a, b) {
        var av = month ? (a.meses[month] || {}) : a.totais, bv = month ? (b.meses[month] || {}) : b.totais;
        var other = type === 'pedidos' ? 'orcamentos' : 'pedidos';
        return (Number(bv[type] || 0) - Number(av[type] || 0)) || (Number(bv[other] || 0) - Number(av[other] || 0)) || String(a.representante || a.cliente).localeCompare(String(b.representante || b.cliente), 'pt-BR');
      });
    }
    function tableData() { return representatives ? data.carteiraRepresentantes : data; }
    function cells(row, values, totals, shownTypes) {
      if (monthly) data.meses.forEach(function (month, index) {
        var outside = month < data.periodo.inicio.slice(0, 7) || month > data.periodo.fim.slice(0, 7);
        shownTypes.forEach(function (type) {
          var amount = values[month] ? values[month][type[0]] : 0;
          var cell = node('td', (month === data.mesReferencia ? 'commercial-current-month ' : '') + (outside ? 'commercial-outside' : ''), outside ? '—' : moeda(amount));
          cell.title = outside ? 'Fora do período selecionado' : nomesMeses[Number(month.slice(5))-1] + '/' + month.slice(0,4) + ' • ' + type[1] + ': ' + moeda(amount);
          row.appendChild(cell);
        });
      });
      shownTypes.forEach(function (type) { row.appendChild(node('td', 'commercial-total', moeda(totals[type[0]]))); });
    }
    function draw() {
      scroll.textContent = '';
      var source = tableData();
      var shownTypes = monthly || representatives ? tipos.slice(0, 2) : tipos;
      tableTitle.textContent = representatives ? 'Performance por representante da carteira' : 'Performance de vendas por cliente';
      description.textContent = (representatives ? 'Cada representante soma os orçamentos e pedidos de todos os clientes de sua carteira no período filtrado. Clique para ver os totais por cliente, conforme os vínculos de representante e vendedor principal no cadastro. Clientes sem movimento aparecem zerados. ' : '') + (monthly || representatives ? 'Orçamentos e pedidos pelo valor total.' : (notasTotal ? 'Orçamentos, pedidos e notas pelo valor total.' : 'Orçamentos e pedidos pelo valor total; notas pelo valor líquido.')) + ' Cada etapa tem seu próprio total. Meses fora do filtro aparecem com traço.';
      var table = node('table', 'comparison-table commercial-table');
      var thead = node('thead'), labels = node('tr');
      var clientHeader = node('th', '', representatives ? 'Representante / Cliente' : 'Cliente / ' + data.rotuloResponsavel); clientHeader.scope = 'col';
      if (monthly) clientHeader.rowSpan = 2;
      labels.appendChild(clientHeader);
      if (monthly) data.meses.map(function (month) { return nomesMeses[Number(month.slice(5))-1] + (data.periodo.inicio.slice(0,4) !== data.periodo.fim.slice(0,4) ? '/' + month.slice(0,4) : ''); }).concat(['Total do período']).forEach(function (label) {
        var cell = node('th', '', label); cell.scope = 'colgroup'; cell.colSpan = shownTypes.length; labels.appendChild(cell);
      });
      thead.appendChild(labels); table.appendChild(thead);
      var typeLabels = monthly ? node('tr', 'commercial-subhead') : labels;
      for (var group = 0; group < (monthly ? data.meses.length + 1 : 1); group++) shownTypes.forEach(function (type) {
        var cell = node('th', 'commercial-document commercial-label-' + type[0]); cell.scope = 'col';
        var month = monthly && group < data.meses.length ? data.meses[group] : null;
        var representativeView = representatives && !hasExpandedRepresentative();
        var active = (representativeView ? representativeSortType : sortType) === type[0] && (representativeView ? representativeSortMonth : sortMonth) === month;
        cell.setAttribute('aria-sort', active ? 'descending' : 'none');
        var button = node('button', 'commercial-sort-button', type[1] + (active ? ' ↓' : ''));
        button.type = 'button'; button.setAttribute('data-commercial-sort', type[0] + '-' + (month || 'total'));
        button.setAttribute('aria-label', 'Ordenar ' + type[1] + (month ? ' em ' + month : ' no período') + ' do maior para o menor');
        button.addEventListener('click', function () {
          var keepRepresentatives = representatives && hasExpandedRepresentative(), previousScroll = scroll.scrollTop, previousLeft = scroll.scrollLeft;
          sortType = type[0]; sortMonth = month; clientPages = {};
          if (!keepRepresentatives) { representativeSortType = sortType; representativeSortMonth = sortMonth; page = 0; }
          draw();
          if (keepRepresentatives) scroll.scrollTop = previousScroll;
          scroll.scrollLeft = previousLeft;
          var focus = scroll.querySelector('[data-commercial-sort="' + type[0] + '-' + (month || 'total') + '"]'); if (focus) focus.focus({ preventScroll: true });
        });
        cell.appendChild(button);
        typeLabels.appendChild(cell);
      });
      if (monthly) thead.appendChild(typeLabels);
      var tbody = node('tbody');
      function appendClient(line, child) {
        var row = node('tr', child ? 'commercial-portfolio-client' : '');
        var client = node('th', 'commercial-client'); client.scope = 'row';
        if (!representatives && loadClientHistory) {
          var selected = selectedClient && selectedClient.clienteId === line.clienteId && selectedClient.responsavelId === line.responsavelId;
          row.className = selected ? 'commercial-history-selected' : '';
          var select = node('button', 'commercial-client-select', line.cliente); select.type = 'button';
          select.setAttribute('aria-pressed', selected ? 'true' : 'false');
          select.setAttribute('aria-label', 'Ver últimos seis meses de ' + line.cliente);
          select.addEventListener('click', function (event) { event.stopPropagation(); selectClient(line); });
          row.addEventListener('click', function () { selectClient(line); });
          client.appendChild(select);
        } else client.appendChild(node('strong', '', line.cliente));
        client.appendChild(node('small', '', child ? line.vendedor : line.responsavel)); row.appendChild(client);
        cells(row, line.meses, line.totais, shownTypes); tbody.appendChild(row);
      }
      ordered(source.linhas, representatives ? representativeSortType : sortType, representatives ? representativeSortMonth : sortMonth).slice(page * pageSize, (page + 1) * pageSize).forEach(function (line) {
        if (!representatives) { appendClient(line, false); return; }
        var row = node('tr');
        row.className = 'commercial-representative-row';
        var client = node('th', 'commercial-client'); client.scope = 'row';
        var id = String(line.representanteId);
        var button = node('button', 'commercial-representative-toggle', (expanded[id] ? '▾ ' : '▸ ') + line.representante);
        button.type = 'button'; button.setAttribute('aria-expanded', expanded[id] ? 'true' : 'false');
        button.setAttribute('data-commercial-representative', id);
        client.appendChild(button);
        client.appendChild(node('small', '', numero(line.clientes.length) + ' cliente(s) na carteira')); row.appendChild(client);
        function toggle() {
          expanded[id] = !expanded[id]; draw();
          var focused = scroll.querySelector('[data-commercial-representative="' + id + '"]');
          if (focused) focused.focus({ preventScroll: true });
        }
        button.addEventListener('click', function (event) { event.stopPropagation(); toggle(); });
        row.addEventListener('click', toggle);
        cells(row, line.meses, line.totais, shownTypes); tbody.appendChild(row);
        if (!expanded[id]) return;
        var clientPage = clientPages[id] || 0;
        ordered(line.clientes).slice(clientPage * pageSize, (clientPage + 1) * pageSize).forEach(function (item) { appendClient(item, true); });
        if (line.clientes.length > pageSize) {
          var controlRow = node('tr'), controlCell = node('td');
          controlCell.colSpan = 1 + (monthly ? data.meses.length + 1 : 1) * shownTypes.length;
          var controls = node('div', 'commercial-pagination');
          controls.appendChild(node('span', '', (clientPage * pageSize + 1) + '–' + Math.min((clientPage + 1) * pageSize, line.clientes.length) + ' de ' + line.clientes.length + ' clientes de ' + line.representante));
          [['Anterior', -1], ['Próxima', 1]].forEach(function (item) {
            var control = node('button', 'secondary', item[0]); control.type = 'button';
            control.disabled = item[1] < 0 ? clientPage === 0 : (clientPage + 1) * pageSize >= line.clientes.length;
            control.setAttribute('aria-label', item[0] + ' página de clientes de ' + line.representante);
            control.addEventListener('click', function () { clientPages[id] = clientPage + item[1]; draw(); });
            controls.appendChild(control);
          });
          controlCell.appendChild(controls); controlRow.appendChild(controlCell); tbody.appendChild(controlRow);
        }
      });
      if (!source.linhas.length) {
        var empty = node('tr'), emptyCell = node('td', 'empty-state', representatives ? 'Nenhum representante vinculado aos clientes desta carteira.' : 'Nenhum movimento encontrado para os filtros selecionados.');
        emptyCell.colSpan = 1 + (monthly ? data.meses.length + 1 : 1) * shownTypes.length; empty.appendChild(emptyCell); tbody.appendChild(empty);
      }
      table.appendChild(tbody);
      var tfoot = node('tfoot');
      var totalRow = node('tr');
      var total = node('th', 'commercial-client', 'TOTAL DO PERÍODO'); total.scope = 'row'; totalRow.appendChild(total);
      cells(totalRow, source.totaisMeses, source.totais, shownTypes); tfoot.appendChild(totalRow);
      table.appendChild(tfoot); scroll.appendChild(table);
      count.textContent = source.linhas.length ? (page * pageSize + 1) + '–' + Math.min((page + 1) * pageSize, source.linhas.length) + ' de ' + source.linhas.length + (representatives ? ' representantes' : ' vínculos cliente / ' + data.rotuloResponsavel.toLowerCase()) : (representatives ? '0 representantes' : '0 clientes');
      previous.disabled = page === 0;
      next.disabled = (page + 1) * pageSize >= source.linhas.length;
    }
    previous.addEventListener('click', function () { if (page > 0) { page--; draw(); } });
    next.addEventListener('click', function () { if ((page + 1) * pageSize < tableData().linhas.length) { page++; draw(); } });
    view.addEventListener('change', function () { monthly = view.value === 'mensal'; sortMonth = representativeSortMonth = null; page = 0; clientPages = {}; draw(); });
    draw(); panel.appendChild(article);
    drawChart(data); chart.hidden = representatives || initialTab === 'regioes'; panel.appendChild(chart);
    var regionHost = data.carteiraRegioes ? node('article', 'wide-card commercial-regions') : null;
    var regionView = regionHost ? window.DashboardDesempenhoRegioes.mount(regionHost, data.carteiraRegioes) : null;
    if (regionHost) { regionHost.hidden = true; panel.appendChild(regionHost); }
    if (data.analiseComercial && window.DashboardAnaliseComercial) {
      var analysis = node('article');
      window.DashboardAnaliseComercial.render(analysis, data.analiseComercial);
      panel.appendChild(analysis);
    }
    container.appendChild(panel);
    if (regionView && initialTab === 'regioes') { article.hidden = true; regionHost.hidden = false; regionView.show(); }
    return { setTab: function (tab) {
      if (tab === 'regioes' && regionView) { article.hidden = true; chart.hidden = true; regionHost.hidden = false; regionView.show(); return true; }
      if ((tab !== 'vendedores' && tab !== 'representantes') || (tab === 'representantes' && !data.carteiraRepresentantes)) return false;
      article.hidden = false; if (regionHost) regionHost.hidden = true;
      representatives = tab === 'representantes'; chart.hidden = representatives; page = 0; draw(); return true;
    } };
  }
  function renderPedidos(container, data, loadPage, documentUrl) {
    container.textContent = '';
    var panel = node('div', 'commercial-orders');
    var heading = node('div', 'commercial-heading');
    function date(value) { return /^\d{4}-\d{2}-\d{2}/.test(value || '') && value.slice(0, 4) !== '0000' ? value.slice(0, 10).split('-').reverse().join('/') : '—'; }
    heading.appendChild(node('p', 'eyebrow', 'PEDIDOS DO PERÍODO'));
    heading.appendChild(node('p', '', date(data.periodo.inicio) + ' a ' + date(data.periodo.fim)));
    panel.appendChild(heading);
    var cards = node('div', 'commercial-kpis'), summary = data.resumo;
    var items = [['Valor dos pedidos', summary.valor, summary.quantidade + ' pedidos nos filtros selecionados.'],
      ['Com NF autorizada', summary.valorComNota, summary.quantidadeComNota + ' pedidos com faturamento iniciado.'],
      ['Sem NF vinculada', summary.valorSemNota, (summary.quantidade - summary.quantidadeComNota) + ' pedidos sem nota autorizada.'],
      ['Na expedição', summary.valorExpedicao, summary.quantidadeExpedicao + ' pedidos aguardando as próximas etapas.']];
    if (data.ticketMedioCartao) items.push(['Ticket médio por pedido', summary.quantidade ? summary.valor / summary.quantidade : 0, numero(summary.clientes) + ' clientes nos filtros selecionados.']);
    items.forEach(function (item) {
      var card = node('article', 'commercial-kpi'); card.appendChild(node('span', '', item[0]));
      card.appendChild(node('strong', '', moeda(item[1]))); card.appendChild(node('small', '', item[2])); cards.appendChild(card);
    });
    panel.appendChild(cards);
    if (!data.ticketMedioCartao) panel.appendChild(node('p', 'comparison-description', numero(summary.clientes) + ' clientes • Ticket médio: ' + moeda(summary.quantidade ? summary.valor / summary.quantidade : 0)));
    var article = node('article', 'wide-card commercial-matrix'), header = node('header'), title = node('div');
    title.appendChild(node('p', 'card-kicker', 'DOCUMENTOS')); title.appendChild(node('h3', '', 'Pedidos do período'));
    title.appendChild(node('p', 'comparison-description', 'NF vinculada indica faturamento iniciado, inclusive por vínculo de itens.'));
    header.appendChild(title);
    var csv = node('button', 'secondary', 'Exportar página CSV'); csv.type = 'button'; if (data.exportarCsv !== false) header.appendChild(csv); article.appendChild(header);
    var controls = node('div', 'commercial-pagination'), label = node('label', 'commercial-view', 'Situação do painel '), situation = node('select');
    [['','Todas as situações'],['pendente','Pendente'],['expedicao','Na expedição'],['com_nota','Com NF autorizada'],['atrasado','Entrega prevista vencida'],['cancelado','Cancelado'],['negado','Negado'],['outras_etapas','Outras etapas']].forEach(function (item) {
      var option = node('option', '', item[1]); option.value = item[0]; situation.appendChild(option);
    });
    situation.value = data.situacao; label.appendChild(situation); controls.appendChild(label); article.appendChild(controls);
    var headers = ['Nº do pedido','Cliente','Emissão','Valor','Situação','% separado','Nº carga','Orçamento de origem','Faturamento','Entrega prevista'];
    if (data.impressaoDisponibilidade) headers.push('Impressão de disponibilidade');
    function values(row) { return [row.numero || row.id, row.cliente, date(row.data), moeda(row.valor), row.status,
      Number(row.percentualSeparado) > 0 ? Number(row.percentualSeparado).toLocaleString('pt-BR', {maximumFractionDigits:2}) + '%' : '—',
      Number(row.numeroCarga) > 0 ? row.numeroCarga : '—', Number(row.origem) > 0 ? row.origem : '—', row.faturamento, date(row.entrega)]; }
    var scroll = node('div', 'table-scroll commercial-table-scroll'); scroll.tabIndex = 0;
    scroll.setAttribute('role','region'); scroll.setAttribute('aria-label','Pedidos do período');
    var table = node('table', 'comparison-table commercial-table'), thead = node('thead'), tr = node('tr');
    var sortFields = ['numero','cliente','data','valor','status','percentualSeparado','numeroCarga','origem','faturamento','entrega'];
    var sortButtons = [];
    headers.forEach(function (text, index) {
      var th = node('th'); th.scope = 'col';
      if (data.ordenacaoHabilitada && sortFields[index]) {
        var field = sortFields[index], active = data.ordem === field;
        th.setAttribute('aria-sort', active ? (data.direcao === 'ASC' ? 'ascending' : 'descending') : 'none');
        var button = node('button', 'commercial-sort-button', text + (active ? (data.direcao === 'ASC' ? ' ↑' : ' ↓') : ' ↕'));
        button.type = 'button'; button.setAttribute('data-order-sort', field); sortButtons.push(button);
        button.addEventListener('click', function () { change(1, data.situacao, field, active && data.direcao === 'ASC' ? 'DESC' : 'ASC'); });
        th.appendChild(button);
      } else th.textContent = text;
      tr.appendChild(th);
    }); thead.appendChild(tr); table.appendChild(thead);
    var tbody = node('tbody');
    data.linhas.forEach(function (row) {
      var tr = node('tr'); values(row).forEach(function (text, index) { tr.appendChild(node('td', index === 1 ? 'commercial-order-client' : '', text == null || text === '' ? '—' : text)); });
      if (data.impressaoDisponibilidade) {
        var td = node('td'), link = node('a', 'secondary', 'Abrir PDF'); link.href = documentUrl(row); link.target = '_blank'; link.rel = 'noopener';
        link.setAttribute('aria-label','Impressão de disponibilidade do pedido ' + (row.numero || row.id)); td.appendChild(link); tr.appendChild(td);
      }
      tbody.appendChild(tr);
    });
    if (!data.linhas.length) { var empty = node('tr'), td = node('td', 'empty-state', 'Nenhum pedido encontrado para os filtros selecionados.'); td.colSpan = headers.length; empty.appendChild(td); tbody.appendChild(empty); }
    table.appendChild(tbody); scroll.appendChild(table); article.appendChild(scroll);
    var pagination = node('div', 'commercial-pagination'), count = node('span'), previous = node('button','secondary','Anterior'), next = node('button','secondary','Próxima');
    previous.type = next.type = 'button';
    count.textContent = data.total ? ((data.pagina - 1) * 50 + 1) + '–' + ((data.pagina - 1) * 50 + data.linhas.length) + ' de ' + numero(data.total) + ' pedidos' : '0 pedidos';
    count.setAttribute('aria-live','polite'); previous.disabled = data.pagina <= 1; next.disabled = !data.temMais;
    pagination.appendChild(count); pagination.appendChild(previous); pagination.appendChild(next); article.appendChild(pagination);
    var status = node('p', 'comparison-description'); status.setAttribute('role','status'); article.appendChild(status);
    function change(page, selected, order, direction) {
      previous.disabled = next.disabled = situation.disabled = true; status.textContent = 'Carregando pedidos…';
      sortButtons.forEach(function (button) { button.disabled = true; });
      loadPage(page, selected, order || data.ordem, direction || data.direcao).catch(function (error) {
        sortButtons.forEach(function (button) { button.disabled = false; });
        previous.disabled = data.pagina <= 1; next.disabled = !data.temMais; situation.disabled = false; situation.value = data.situacao;
        status.textContent = error.message || 'Não foi possível carregar os pedidos.';
      });
    }
    previous.addEventListener('click', function () { change(data.pagina - 1, data.situacao); });
    next.addEventListener('click', function () { change(data.pagina + 1, data.situacao); });
    situation.addEventListener('change', function () { change(1, situation.value); });
    csv.addEventListener('click', function () {
      function escape(value) { var text = String(value == null ? '' : value); if (/^[=+@-]/.test(text)) text = "'" + text; return '"' + text.replace(/"/g,'""') + '"'; }
      var rows = [headers.slice(0, 10)].concat(data.linhas.map(values));
      var url = URL.createObjectURL(new Blob(['\uFEFF' + rows.map(function (row) { return row.map(escape).join(';'); }).join('\r\n')], {type:'text/csv;charset=utf-8'}));
      var link = node('a'); link.href = url; link.download = 'pedidos-pagina-' + data.pagina + '.csv'; document.body.appendChild(link); link.click(); link.remove();
      setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    });
    panel.appendChild(article); container.appendChild(panel);
  }
  window.DashboardDesempenhoComercial = { render: render, renderPedidos: renderPedidos };
}());
