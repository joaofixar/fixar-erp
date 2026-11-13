(() => {
  'use strict';
  const $ = (id) => document.getElementById(id);
  const csrf = document.body.dataset.csrf;
  const gestor = document.body.dataset.gestor || '';
  const impressaoDisponibilidade = JSON.parse(document.body.dataset.layout || '{}').impressaoDisponibilidadePedido === true;
  // O seletor do convite fica somente na sessão; e-mail, senha e código nunca vão para a URL.
  if (location.search.includes('convite=')) history.replaceState(null, '', location.pathname);
  const el = (tag, text, cls) => { const node = document.createElement(tag); if (text != null) node.textContent = String(text); if (cls) node.className = cls; return node; };
  async function request(action, params = {}, options = {}) {
    const response = await fetch(`api.php?${new URLSearchParams({ acao: action, ...params })}`, { credentials: 'same-origin', ...options, headers: { 'X-Portal-Demonstracao': document.body.dataset.demonstracao || '0', ...(gestor ? { 'X-Portal-Gestor': gestor } : {}), ...options.headers } });
    const data = await response.json();
    if (!response.ok) { if (!gestor && (response.status === 401 || response.status === 409) && $('representanteApp')) location.reload(); throw new Error(data.error || 'Não foi possível concluir a solicitação.'); }
    return data;
  }
  const post = (action, values) => request(action, {}, { method: 'POST', headers: { 'X-CSRF-Token': csrf }, body: new URLSearchParams(values) });
  if (!$('representanteApp')) return;
  if (gestor && $('gestorRepresentante')) $('gestorRepresentante').addEventListener('change', (event) => {
    if (window.parent === window) { const original = event.target.querySelector('option[selected]'); if (original) event.target.value = original.value; return; }
    event.target.disabled = true;
    window.parent.postMessage({ tipo: 'hardness-portal-gestor-trocar', contexto: gestor, representante: event.target.value }, location.origin);
  });
  function updateCalendar() {
    const now = new Date(), options = { timeZone: 'America/Sao_Paulo' };
    const parts = Object.fromEntries(new Intl.DateTimeFormat('pt-BR', { ...options, year:'numeric', month:'2-digit', day:'2-digit' }).formatToParts(now).map(part => [part.type, part.value]));
    $('portalToday').dateTime = `${parts.year}-${parts.month}-${parts.day}`;
    $('portalToday').textContent = `${parts.day}/${parts.month}/${parts.year}`;
    $('portalWeekday').textContent = new Intl.DateTimeFormat('pt-BR', { ...options, weekday:'long' }).format(now);
  }
  updateCalendar(); setInterval(updateCalendar, 60000);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) updateCalendar(); });
  const number = (v) => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(Number(v || 0));
  const money = (v) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
  const percent = (v) => new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format(Number(v || 0)) + '%';
  const ratio = (a, b) => Number(b) ? Number(a) / Number(b) * 100 : 0;
  const date = (v) => /^\d{4}-\d{2}-\d{2}/.test(v || '') && !v.startsWith('0000') ? v.slice(0, 10).split('-').reverse().join('/') : '—';
  const shortDate = (v) => date(v).slice(0, 5);
  const categories = { convertido: 'Pedido gerado', pendente: 'Pendente', analise: 'Análise', finalizado: 'Finalizado, sem pedido vinculado', aprovado: 'Aprovado', vencido: 'Vencido', negado: 'Negado', sem_conversao: 'Status não informado', cancelado: 'Cancelado', com_nota: 'Com NF autorizada', expedicao: 'Na expedição', outras_etapas: 'Outras etapas do sistema', pendente_direto: 'Pendentes — inclusão direta', sem_nota: 'Sem NF autorizada', atrasado: 'Entrega prevista vencida, sem NF', em_aberto: 'Com saldo em aberto', quitada: 'Cobranças quitadas', sem_cobranca: 'Sem cobrança vinculada', pago: 'Pago', aberto: 'Em aberto', parcial: 'Parcial' };
  const views = {
    inicio: { label: 'Início', number: '', description: 'Seu desempenho comercial em uma visão, do orçamento ao recebimento.', chart: '', situation: '', categories: [] },
    orcamentos: { label: 'Orçamentos', number: 'Nº do orçamento', description: 'Propostas, conversão em pedidos e oportunidades que precisam de acompanhamento.', chart: 'Volume de orçamentos', situation: 'Situação dos orçamentos', categories: ['pendente','convertido','cancelado','analise','aprovado','vencido','negado','finalizado','sem_conversao'] },
    pedidos: { label: 'Pedidos', number: 'Nº do pedido', description: 'Vendas registradas, origem dos pedidos e andamento do faturamento.', chart: 'Entrada de pedidos', situation: 'Etapas dos pedidos', categories: ['pendente','expedicao','com_nota','cancelado','negado','outras_etapas','pendente_direto','atrasado'] },
    notas: { label: 'Notas fiscais', number: 'Nº da nota fiscal', description: 'Faturamento autorizado, clientes atendidos e situação das cobranças vinculadas.', chart: 'Evolução do faturamento', situation: 'Cobranças das notas', categories: ['em_aberto','quitada','sem_cobranca'] },
    boletos: { label: 'Pagamentos', number: 'Nº do documento', description: 'Parcelas de boletos, recebimentos e saldos que exigem atenção.', chart: 'Vencimentos e recebimentos', situation: 'Situação dos pagamentos', categories: [] },
    regioes: { label: 'Vendas por região', number: '', description: 'Sua carteira no mapa: clientes, municípios e oportunidades em todo o Brasil.', chart: '', situation: '', categories: [] },
    comparativo: { label: 'Comparativo por cliente', number: '', description: 'Compare os clientes com o mesmo período do ano anterior.', chart: '', situation: '', categories: [] }
  };
  Object.entries(window.PortalPersonalizacao.config.visoes || {}).forEach(([key, value]) => {
    if (views[key]) Object.assign(views[key], { label: value.titulo, ...(value.evolucao != null ? { chart: value.evolucao, situation: value.distribuicao } : {}) });
  });
  const commissions = (window.PortalPersonalizacao.config.visoes.boletos || {}).naturezaFinanceira === 'comissoes';
  const commissionByNote = commissions && (window.PortalPersonalizacao.config.visoes.boletos || {}).comissaoPorNota === true;
  const quoteCancellationReasons = (window.PortalPersonalizacao.config.visoes.orcamentos.blocos || []).includes('cancelamentos');
  const quoteProposal = (window.PortalPersonalizacao.config.visoes.orcamentos || {}).exibirClienteProposta !== false;
  const periodField = (window.PortalPersonalizacao.config.visoes.boletos || {}).campoPeriodo || 'vencimento';
  const periodName = {data:'emissão',vencimento:'vencimento',pagamento:'recebimento'}[periodField] || 'vencimento';
  if (commissionByNote) views.boletos.number = 'Nº da NF-e';
  const commissionStatuses = commissionByNote ? {pago:'Liberada', aberto:'Pendente', parcial:'Pendente · recebimento parcial', vencido:'Pendente · cliente em atraso'} : {pago:'Cliente quitou', aberto:'Aguardando cliente', parcial:'Cliente pagou parcialmente', vencido:'Cliente em atraso'};
  const financialLabel = (standard, commission, perNote = commission) => commissions ? (commissionByNote ? perNote : commission) : standard;
  const statusLabel = (key) => (commissions && type === 'boletos' ? commissionStatuses[key] : '') || categories[key] || key;
  let type = 'inicio', page = 1, generation = 0, controller, chargeGeneration = 0, analyticsData, rows = [], measure = 'valor';
  let applied = Object.fromEntries(new FormData($('filters')));
  let composition = '', compositionCaption = '';
  let quoteSegment = '';
  let currentClient = null, overviewPeriod = null, regionalSelection = '';
  const updateTime = (value) => { $('updatedAt').textContent = `Atualizado às ${new Date(value).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`; };
  const regional = window.PortalRegioes.create(request, updateTime, (label) => { regionalSelection=label; updateSelection(); });
  const home = window.PortalInicio.create(changeView);
  const comparison = window.PortalComparativo.create(request, changeView);
  const loading = $('portalLoading'), workspace = document.querySelector('#representanteApp > .workspace');
  let loadingFocus = null;
  function setLoading(active) {
    if (active && !workspace.inert) loadingFocus = workspace.contains(document.activeElement) ? document.activeElement : null;
    workspace.inert = active;
    workspace.setAttribute('aria-busy', String(active));
    loading.hidden = !active;
    if (!active) {
      if (loadingFocus && loadingFocus.isConnected && !loadingFocus.disabled && document.activeElement === document.body) loadingFocus.focus({ preventScroll: true });
      loadingFocus = null;
    }
  }
  function rememberOverview() { if (!overviewPeriod) overviewPeriod={inicio:applied.inicio,fim:applied.fim}; }
  function updateSelection() {
    const labels=[];
    if(currentClient) labels.push((currentClient.grupoId>0?'Grupo: ':'Cliente: ')+currentClient.nome); else if(applied.cliente) labels.push('Cliente: '+applied.cliente);
    if(applied.numero && type!=='regioes') labels.push('Documento: '+applied.numero);
    if(applied.etapa && type!=='regioes') labels.push('Situação: '+(categories[applied.etapa] || applied.etapa));
    if(applied.status && type==='boletos') labels.push('Situação: '+statusLabel(applied.status));
    if(overviewPeriod && (applied.inicio!==overviewPeriod.inicio || applied.fim!==overviewPeriod.fim)) labels.push('Intervalo selecionado no gráfico');
    if(type==='regioes' && regionalSelection) labels.push(regionalSelection);
    $('selectionBar').hidden=type==='inicio' || !labels.length; $('selectionTitle').textContent=labels.join(' · ');
    $('selectionDescription').textContent='Indicadores, gráficos, clientes e documentos refletem os filtros selecionados.';
    document.querySelectorAll('.clear-client').forEach((button) => {button.hidden=!labels.length;});
    document.querySelectorAll('.situation-item').forEach((button)=>button.setAttribute('aria-pressed',String(button.dataset.category===(type==='boletos'?applied.status:applied.etapa))));
  }
  function clearSelection() {
    if(overviewPeriod) { $('filters').elements.inicio.value=overviewPeriod.inicio;$('filters').elements.fim.value=overviewPeriod.fim; }
    overviewPeriod=null;currentClient=null;regionalSelection='';quoteSegment='';regional.clear(false);
    ['cliente','numero','etapa','status'].forEach((name) => { $('filters').elements[name].value=''; });
    applyFilters('reset');
  }
  document.querySelectorAll('[data-clear-selection]').forEach((button) => button.addEventListener('click',clearSelection));
  function setView() {
    const view = views[type];
    window.PortalPersonalizacao.view(type);
    $('filters').hidden=type==='inicio';
    document.querySelector('.period-presets').hidden=type==='inicio';
    document.querySelector('.period-toolbar').classList.toggle('home-toolbar',type==='inicio');
    $('analytics').dataset.view = type;
    $('viewTitle').textContent = view.label;
    $('regionalView').hidden = type !== 'regioes';
    $('comparisonView').hidden = type !== 'comparativo';
    $('comparisonSources').hidden = type !== 'comparativo';
    $('homeView').hidden = type !== 'inicio'; $('summary').hidden = type === 'inicio' || type === 'comparativo';
    $('summary').classList.toggle('regional-summary',type === 'regioes');
    ['analytics','insights','documentsCard'].forEach((id) => { $(id).hidden = type === 'regioes' || type === 'inicio' || type === 'comparativo'; });
    $('numberLabel').closest('label').hidden = type === 'regioes' || type === 'inicio' || type === 'comparativo';
    $('numberLabel').textContent = view.number; $('documentsTitle').textContent = `${view.label} do período`;
    $('chartTitle').textContent = view.chart; $('situationTitle').textContent = view.situation;
    $('stageFilter').hidden = type === 'boletos'; $('paymentFilter').hidden = type !== 'boletos';
    Array.from($('filters').elements.status.options).forEach(option => { if (option.value) option.textContent = statusLabel(option.value); });
    $('stageSelect').replaceChildren(new Option('Todas as situações', ''), ...view.categories.map((key) => new Option(categories[key], key)));
    $('periodNote').textContent = type === 'boletos' ? financialLabel('Período por vencimento. Recebido e saldo representam a situação atual dessas parcelas. Até 366 dias.', 'Comissões por vencimento dos títulos, proporcionais ao pagamento do cliente. Não indica repasse ao representante. Até 366 dias.') : 'Período por emissão. Conversão e cobranças consideram a situação atual dos documentos. Até 366 dias.';
    if (type === 'boletos' && (periodField !== 'vencimento' || commissionByNote)) $('periodNote').textContent = `Período por ${periodName} ${commissionByNote ? 'da NF-e' : 'dos títulos'}. Valores refletem a situação atual dos títulos. ${commissions ? 'Não indica repasse ao representante. ' : ''}Até 366 dias.`;
    if (type === 'regioes') $('periodNote').textContent = 'Carteira atual do cadastro. Faturamento e pedidos por emissão no período, da mesma empresa e representante. Até 366 dias.';
    if (type === 'inicio') $('periodNote').textContent = 'Orçamentos, pedidos e notas por emissão. Pagamentos por vencimento das parcelas. Até 366 dias.';
    if (type === 'inicio' && periodField !== 'vencimento') $('periodNote').textContent = `Orçamentos, pedidos e notas por emissão. ${commissions ? 'Comissões' : 'Pagamentos'} por ${periodName}. Até 366 dias.`;
    if (type === 'comparativo') $('periodNote').textContent = 'Período por emissão, comparado com o mesmo intervalo do ano anterior. Somente documentos válidos do seu representante. Até 366 dias.';
    $('listCaption').textContent = type === 'orcamentos' ? 'Pedido gerado exige vínculo ativo do mesmo representante e empresa. Cancelados ficam fora do volume.' : type === 'pedidos' ? 'NF vinculada indica faturamento iniciado, inclusive por vínculo de itens. Cancelados e negados ficam fora do volume.' : type === 'notas' ? 'Somente notas autorizadas. Os recebimentos consideram todas as cobranças da nota, em qualquer vencimento.' : financialLabel('Uma linha por parcela do boleto. Pagamentos parciais preservam o saldo em aberto.', 'Comissão gravada em cada título; recebimentos parciais são proporcionais. O boleto permanece com o valor integral cobrado do cliente.');
    if (type === 'boletos' && commissionByNote) $('listCaption').textContent = 'Uma linha por NF-e. A comissão liberada considera a quitação dos títulos elegíveis conforme a regra da empresa.';
    $('chartDescription').textContent = type === 'boletos' ? financialLabel('Valor e recebido das parcelas, agrupados pela data de vencimento.', 'Comissão prevista e comissão sobre pagamentos dos clientes, por vencimento.') : 'Documentos não cancelados, agrupados pela data de emissão.';
    if (type === 'boletos' && (periodField !== 'vencimento' || commissionByNote)) $('chartDescription').textContent = `${financialLabel('Valor e recebido das parcelas', 'Comissão prevista e sobre recebimentos', 'Comissões previstas e liberadas')}, por ${periodName}.`;
    $('customersTitle').textContent = type === 'boletos' ? financialLabel('Maiores valores em parcelas', 'Maiores comissões previstas') : 'Maiores volumes do período';
    $('attentionTitle').textContent = type === 'boletos' ? financialLabel('Saldos em atraso por faixa', 'Comissões em títulos vencidos') : type === 'notas' ? 'Recebimentos das notas' : type === 'pedidos' ? 'Próximas etapas dos pedidos' : 'Orçamentos que precisam de ação';
    $('attentionEyebrow').textContent = type==='orcamentos'?'AÇÕES COMERCIAIS':type==='pedidos'?'ACOMPANHAMENTO DOS PEDIDOS':'ACOMPANHAMENTO';
    $('attentionDescription').textContent = type==='orcamentos'?'Quantidade e valor das propostas nesta seleção. Selecione uma situação para atualizar todo o painel.':type==='pedidos'?'Selecione uma etapa para atualizar os indicadores, gráficos, clientes e documentos do painel.':'';
  }
  function filterPanel(category) {
    const field = $('filters').elements[type === 'boletos' ? 'status' : 'etapa'];
    field.value = field.value === category ? '' : category;
    applied = { ...applied, status: type === 'boletos' ? field.value : '', etapa: type === 'boletos' ? '' : field.value };
    page = 1; load(true, true);
  }
  function focusChartsAfterUpdate(current) {
    requestAnimationFrame(() => {
      const charts = $('analytics'), selection = $('selectionBar');
      if (current !== generation || charts.hidden) return;
      const offset = selection.hidden ? 16 : selection.offsetHeight + (parseFloat(getComputedStyle(selection).top) || 0) + 14;
      charts.focus({ preventScroll: true });
      window.scrollTo({ top: Math.max(0, window.scrollY + charts.getBoundingClientRect().top - offset), behavior: 'instant' });
    });
  }
  function detailValue(key, label) {
    composition = key; compositionCaption = label; page = 1;
    document.querySelectorAll('[data-composition]').forEach(node => node.setAttribute('aria-pressed', String(node.dataset.composition === key)));
    load(false).then(() => {
      if (type !== 'boletos' || composition !== key) return;
      $('documentsCard').scrollIntoView({behavior:'smooth', block:'start'});
      $('documents').focus({preventScroll:true});
    });
  }
  function detailQuoteSegment(key) {
    quoteSegment = key; page = 1;
    document.querySelectorAll('[data-quote-segment]').forEach(node => node.setAttribute('aria-pressed', String(node.dataset.quoteSegment === key)));
    load(false).then(() => {
      if (type !== 'orcamentos' || quoteSegment !== key) return;
      $('documentsCard').scrollIntoView({behavior:'smooth', block:'start'});
      $('documents').focus({preventScroll:true});
    });
  }
  $('clearComposition').addEventListener('click', () => type === 'orcamentos' ? detailQuoteSegment('') : detailValue('', ''));
  function renderSummary(data) {
    const r = data.resumo, count = Number(r.quantidade), total = Number(r.valor), ticket = count ? total / count : 0;
    const fragment = document.createDocumentFragment();
    const card = (label, value, caption, cls = '', metric = '') => { const article = el(metric ? 'button' : 'article', null, `portal-card ${cls}`); if (metric) { article.type='button'; article.dataset.composition=metric; article.setAttribute('aria-pressed',String(composition===metric)); article.title='Ver títulos que compõem '+label; article.addEventListener('click',()=>detailValue(metric,label)); } article.append(el('p', label, 'card-kicker'), el('strong', value), el('small', caption)); fragment.append(article); };
    const highlights = document.createDocumentFragment();
    const insight = (value, label, caption, category, metric = '') => {
      const node = el(category || metric ? 'button' : 'article', null, 'portal-insight'); if (metric) { node.type='button'; node.dataset.composition=metric; node.setAttribute('aria-pressed',String(composition===metric)); node.title='Ver títulos que compõem '+label; node.addEventListener('click',()=>detailValue(metric,metric === 'recebido' ? financialLabel('Recebido','Comissão sobre recebimentos','Comissão liberada') : label)); } if (category) { node.type = 'button'; node.setAttribute('aria-pressed', String(category === applied.etapa)); node.addEventListener('click', () => filterPanel(category)); }
      const text = el('span'); text.append(el('b', label), el('small', caption)); node.append(el('strong', value), text); highlights.append(node);
    };
    if (type === 'orcamentos') {
      card('Orçamentos emitidos', number(count), `${number(r.clientes)} clientes no período`);
      card('Volume orçado', money(total), 'Total dos orçamentos não cancelados');
      card('Ticket médio', money(ticket), 'Valor médio por orçamento');
      card('Conversão em pedidos', percent(ratio(r.convertidos, count)), `${number(r.convertidos)} de ${number(count)} orçamentos`, 'finance');
      insight(number(r.convertidos), 'Convertidos em pedido', 'Pedidos ativos vinculados', 'convertido');
      insight(number(r.pendentes), 'Orçamentos pendentes', money(r.valorPendente) + ' em propostas', 'pendente');
      insight(number(r.clientes), 'Clientes com propostas', 'Carteira atendida no período');
    } else if (type === 'pedidos') {
      card('Pedidos registrados', number(count), `${number(r.clientes)} clientes no período`);
      card('Valor em pedidos', money(total), 'Pedidos não cancelados nem negados');
      card('Ticket médio', money(ticket), 'Valor médio por pedido');
      card('Com NF autorizada', percent(ratio(r.comNota, count)), `${number(r.comNota)} de ${number(count)} pedidos · faturamento iniciado`, 'finance');
      insight(number(r.comOrcamento), 'Originados de orçamento', percent(ratio(r.comOrcamento, count)) + ' dos pedidos');
      insight(number(r.pendentesDiretos), 'Pendentes', 'Inclusão direta, sem orçamento · status Pendente', 'pendente_direto');
      insight(number(r.naExpedicao), 'Na expedição', 'Em preparação, sem NF autorizada', 'expedicao');
    } else if (type === 'notas') {
      card('Notas autorizadas', number(count), 'Quantidade emitida no período');
      card('Faturamento', money(total), 'Total das notas fiscais autorizadas');
      card('Ticket médio da nota', money(ticket), 'Faturamento por nota autorizada');
      card('Clientes faturados', number(r.clientes), 'Clientes distintos atendidos', 'finance');
      insight(money(r.recebido), 'Recebido das notas', 'Todas as cobranças vinculadas');
      insight(money(r.saldo), 'Saldo a receber', `${number(r.notasAbertas)} notas com saldo`, 'em_aberto');
      insight(number(r.semCobranca), 'Sem cobrança vinculada', 'Consulte a condição de pagamento', 'sem_cobranca');
    } else {
      card(financialLabel('Previsto no período', 'Comissão prevista'), money(total), `${number(count)} ${commissionByNote ? 'NF-e' : 'parcelas'} por ${periodName}`, '', 'previsto');
      card(financialLabel('Recebido', 'Comissão sobre recebimentos', 'Comissão liberada'), money(r.recebido), percent(ratio(r.recebido, total)) + financialLabel(' do valor das parcelas', ' da comissão prevista'), 'finance', 'recebido');
      card(financialLabel('Saldo em aberto', 'Comissão pendente'), money(r.saldo), financialLabel('Saldo atual das parcelas do período', 'Comissão vinculada ao saldo dos clientes'), '', 'saldo');
      card(financialLabel('Saldo vencido', 'Comissão em títulos vencidos'), money(r.vencido), 'Parcelas com vencimento anterior a hoje', 'overdue', 'vencido');
      insight(percent(ratio(r.recebido, total)), financialLabel('Taxa de recebimento', 'Comissão sobre recebimentos (%)', 'Comissão liberada (%)'), commissionByNote ? `${number(r.pagos)} NF-e com títulos elegíveis quitados` : `${number(r.pagos)} parcelas quitadas pelos clientes; inclui recebimentos parciais`, null, 'recebido');
      insight(money(ticket), financialLabel('Ticket médio da parcela', 'Comissão média por parcela', 'Comissão média por NF-e'), `${number(count)} ${commissionByNote ? 'NF-e' : 'parcelas de boletos'}`, null, 'media');
      insight(money(r.proximos7), financialLabel('Vence nos próximos 7 dias', 'Comissão a vencer em 7 dias'), 'Dentro do período selecionado', null, 'proximos7');
    }
    $('summary').replaceChildren(fragment); $('insights').replaceChildren(highlights);
    $('segmentsCard').hidden = !Array.isArray(data.segmentos);
    $('cancellationsCard').hidden = type !== 'orcamentos' || !Array.isArray(data.cancelamentos);
    window.PortalAnaliseComercial.segments($('segmentsBreakdown'), data.segmentos, r.valor,
      type === 'orcamentos' ? key => detailQuoteSegment(quoteSegment === key ? '' : key) : null, quoteSegment);
    window.PortalAnaliseComercial.cancellations($('cancellationsBreakdown'), data.cancelamentos, r);
    renderSituations(data.situacoes); renderCustomers(data.clientes); renderAttention(data); renderChart();
  }
  const svgNode = (tag, attrs = {}, text) => { const node = document.createElementNS('http://www.w3.org/2000/svg', tag); Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, String(value))); if (text != null) node.textContent = text; return node; };
  const iso = (d) => d.toISOString().slice(0, 10);
  function renderChart() {
    if (!analyticsData) return;
    const source = analyticsData.evolucao;
    if (!source.length) { $('evolutionChart').replaceChildren(el('p', 'Nenhum movimento neste período. Experimente ampliar as datas ou limpar os filtros.', 'analytics-empty')); return; }
    const start = new Date(applied.inicio + 'T12:00:00Z'), end = new Date(applied.fim + 'T12:00:00Z');
    const days = Math.round((end - start) / 86400000) + 1, size = days > 31 ? 7 : 1;
    const lookup = new Map(source.map((row) => [row.data.slice(0, 10), row])), groups = [];
    for (let i = 0; i < days; i++) {
      const current = new Date(start.getTime() + i * 86400000), key = iso(current);
      if (i % size === 0) groups.push({ start: key, end: key, valor: 0, quantidade: 0, recebido: 0 });
      const group = groups[groups.length - 1], value = lookup.get(key); group.end = key;
      if (value) ['valor','quantidade','recebido'].forEach((field) => { group[field] += Number(value[field] || 0); });
    }
    const financial = type === 'boletos' && measure === 'valor';
    const lineChart = (window.PortalPersonalizacao.config.visoes[type] || {}).tipoGrafico === 'linha';
    const colors = window.PortalPersonalizacao.config.coresSeries || {};
    const seriesColor = colors[type] || '#83b5dc', receivedColor = colors.recebido || '#ffffff';
    const host = $('evolutionChart');
    const meta = el('div', null, 'chart-meta'), peak = Math.max(...groups.map(group => group[measure]));
    meta.append(el('span', (measure === 'quantidade' ? 'Quantidade' : 'Valores em R$') + ' · ' + (size === 1 ? 'por dia' : 'por semana')),
      el('span', 'Pico ' + (measure === 'quantidade' ? number(peak) : money(peak)), 'chart-peak'));
    host.replaceChildren(meta);
    meta.style.setProperty('--chart-series-color',seriesColor);
    let legend;
    if (financial) {
      legend = el('div', null, 'chart-legend');
      const labels = [financialLabel('Valor das parcelas', 'Comissão prevista'),financialLabel('Recebido', 'Sobre recebimentos dos clientes', 'Comissão liberada')];
      labels.forEach((label,i)=>{const item=el('span',label);if(lineChart)item.style.setProperty('--chart-series-color',i?receivedColor:seriesColor);legend.append(item);});
      host.append(legend);
    }
    // Usa a altura disponível no quadro, sem esticar os textos do SVG.
    const style = getComputedStyle(host);
    const width = Math.max(260, host.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight));
    const height = Math.max(180, host.clientHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom) - meta.offsetHeight - (legend ? legend.offsetHeight : 0));
    const left = 52, right = width - 12, top = 18, bottom = height - 32;
    const values = groups.flatMap((group) => [group[measure], ...(financial ? [group.recebido] : [])]);
    let maximum = Math.max(1, ...values), minimum = Math.min(0, ...values);
    const quantityStep = Math.max(1,Math.ceil(maximum/4));
    if(measure==='quantidade') { minimum=0;maximum=Math.ceil(maximum/quantityStep)*quantityStep; }
    const span = maximum - minimum, tickCount=measure==='quantidade'?maximum/quantityStep:4;
    const y = (v) => bottom - (v - minimum) / span * (bottom - top), zero = y(0);
    const svg = svgNode('svg', { viewBox: `0 0 ${width} ${height}`, role: 'group', 'aria-label': views[type].chart });
    const defs = svgNode('defs');
    [['period-volume', '#83b5dc', '#5286b5'], ['period-received', '#76cbb4', '#409c88']].forEach(([id, from, to]) => {
      const gradient = svgNode('linearGradient', { id, x1: 0, y1: 0, x2: 0, y2: 1 });
      gradient.append(svgNode('stop', { offset: '0%', 'stop-color': from }), svgNode('stop', { offset: '100%', 'stop-color': to })); defs.append(gradient);
    }); svg.append(defs);
    if (lineChart) {
      const series=[{key:type,color:seriesColor,values:groups.map(group=>group[measure])}];
      if(financial)series.push({key:'recebido',color:receivedColor,values:groups.map(group=>group.recebido)});
      window.PortalAnaliseComercial.trend(svg,series,i=>left+(i+.5)*(right-left)/groups.length,y,'period');
    }
    const tooltip = el('div', null, 'period-tooltip'); tooltip.id = 'periodTooltip'; tooltip.setAttribute('role', 'tooltip'); tooltip.hidden = true;
    let activeColumn;
    const dismiss = () => { tooltip.hidden = true; if (activeColumn) { activeColumn.classList.remove('is-highlighted'); activeColumn.removeAttribute('aria-describedby'); } activeColumn = null; };
    const show = (column, group) => {
      dismiss(); activeColumn = column; column.classList.add('is-highlighted'); column.setAttribute('aria-describedby', tooltip.id);
      tooltip.replaceChildren(el('strong', date(group.start) + (group.start !== group.end ? ' a ' + date(group.end) : '')));
      const detail = (label, value) => { const line = el('div'); line.append(el('span', label), el('b', value)); tooltip.append(line); };
      detail('Quantidade', number(group.quantidade) + (type === 'boletos' ? (commissionByNote ? ' NF-e' : group.quantidade === 1 ? ' parcela' : ' parcelas') : (group.quantidade === 1 ? ' documento' : ' documentos')));
      detail(type === 'boletos' ? financialLabel('Previsto', 'Comissão prevista') : 'Volume', money(group.valor));
      if (type === 'boletos') detail(financialLabel('Recebido', 'Sobre recebimentos dos clientes', 'Comissão liberada'), money(group.recebido));
      tooltip.append(el('small', 'Clique para explorar este intervalo')); tooltip.hidden = false;
      const box = host.getBoundingClientRect(), hit = column.getBoundingClientRect(), tip = tooltip.getBoundingClientRect();
      const center = hit.left + hit.width / 2 - box.left;
      const desired = center > box.width / 2 ? center - tip.width - 14 : center + 14;
      tooltip.style.left = Math.max(8, Math.min(box.width - tip.width - 8, desired)) + 'px';
      tooltip.style.top = '42px';
    };
    for (let i = 0; i <= tickCount; i++) {
      const value = maximum - span * i / tickCount, position = y(value);
      svg.append(svgNode('line', { x1: left, x2: right, y1: position, y2: position, class: 'chart-grid' }));
      const label = measure === 'quantidade' ? number(value) : new Intl.NumberFormat('pt-BR', { notation: 'compact', maximumFractionDigits: 1 }).format(value);
      svg.append(svgNode('text', { x: left - 9, y: position + 3, 'text-anchor': 'end', class: 'chart-axis' }, label));
    }
    groups.forEach((group, i) => {
      const step = (right - left) / groups.length, barWidth = Math.max(2, Math.min(financial ? 38 : 26, step * .48)), x = left + i * step + (step - barWidth) / 2;
      const value = group[measure], valueLabel = measure === 'quantidade' ? number(value) + ' documentos' : money(value);
      const column = svgNode('g', { class: 'chart-column', role: 'button', tabindex: 0, 'aria-label': `${date(group.start)} a ${date(group.end)}: ${valueLabel}. Filtrar intervalo.` });
      column.append(svgNode('rect', { x: left + i * step, y: top - 5, width: step, height: bottom - top + 10, rx: 5, class: 'chart-hit' }));
      column.append(svgNode('line', { x1: left + (i + .5) * step, x2: left + (i + .5) * step, y1: top, y2: bottom, class: 'chart-guide' }));
      const singleWidth = financial ? Math.max(1, (barWidth - 3) / 2) : barWidth;
      const drawBar = (position, amount, className) => {
        const length = Math.abs(zero - y(amount));
        if (!length) return;
        const tip = y(amount), direction = amount >= 0 ? 1 : -1, radius = Math.min(4, singleWidth / 2, length);
        const edge = position + singleWidth;
        const shape = `M ${position} ${zero} V ${tip + direction * radius} Q ${position} ${tip} ${position + radius} ${tip} H ${edge - radius} Q ${edge} ${tip} ${edge} ${tip + direction * radius} V ${zero} Z`;
        column.append(svgNode('path', { d: shape, class: className }));
        // Linha de acabamento acompanha o valor, com base reta e cantos apenas na extremidade.
        if (length > 3 && singleWidth > 4) column.append(svgNode('line', { x1: position + radius, x2: edge - radius, y1: tip + direction * .65, y2: tip + direction * .65, class: 'chart-bar-cap' + (className === 'chart-received' ? ' received' : '') }));
      };
      if (!lineChart) {
        drawBar(x, value, 'chart-bar');
        if (colors[type]) column.querySelectorAll('.chart-bar').forEach(bar => bar.style.fill = colors[type]);
        if (financial) drawBar(x + singleWidth + 3, group.recebido, 'chart-received');
      }
      column.addEventListener('pointerenter', () => show(column, group)); column.addEventListener('pointerleave', dismiss);
      column.addEventListener('focus', () => show(column, group)); column.addEventListener('blur', dismiss);
      const activate = () => { dismiss(); rememberOverview(); $('filters').elements.inicio.value = group.start; $('filters').elements.fim.value = group.end; applyFilters('drill'); };
      column.addEventListener('click', activate); column.addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); activate(); } }); svg.append(column);
      const labelStep = Math.max(1, Math.ceil(groups.length / 5));
      if (i === 0 || i === groups.length - 1 || (i % labelStep === 0 && i * step >= 44 && (groups.length - 1 - i) * step >= 44)) svg.append(svgNode('text', { x: left + (i + .5) * step, y: bottom + 21, 'text-anchor': 'middle', class: 'chart-axis' }, shortDate(group.start)));
    });
    host.insertBefore(svg, legend || null); host.append(tooltip);
    host.onkeydown = event => { if (event.key === 'Escape') dismiss(); };
  }
  function renderSituations(situations) {
    const ordered=views[type].categories;
    situations=[...situations].sort((a,b) => ordered.indexOf(a.categoria)-ordered.indexOf(b.categoria));
    if (!situations.length) { $('situations').replaceChildren(el('p', 'Sem documentos para distribuir no período.', 'analytics-empty')); return; }
    const total = situations.reduce((sum, row) => sum + Number(row.quantidade), 0), circumference = 2 * Math.PI * 64;
    const svg = svgNode('svg', { viewBox: '0 0 180 180', class: 'situation-donut', role: 'group', 'aria-label': `${number(total)} documentos por situação` });
    const defs = svgNode('defs'), colors = ['#619dff','#39d8b3','#f4b75b','#b18aff','#f5839c','#62d2e9','#a9bdd3'];
    colors.forEach((color, i) => {
      const gradient = svgNode('linearGradient', { id: 'distribution-' + i, x1: 0, y1: 0, x2: 1, y2: 1 });
      gradient.append(svgNode('stop', { offset: '0%', 'stop-color': color }), svgNode('stop', { offset: '100%', 'stop-color': color, 'stop-opacity': '.72' })); defs.append(gradient);
    });
    svg.append(defs, svgNode('circle', { cx: 90, cy: 90, r: 82, class: 'donut-orbit' }), svgNode('circle', { cx: 90, cy: 90, r: 64, class: 'donut-track' }), svgNode('circle', { cx: 90, cy: 90, r: 47, class: 'donut-core' }));
    let offset = 0; const legend = el('div', null, 'situation-legend'), pairs = [];
    const highlight = (category) => pairs.forEach(({ circle, button, category: key }) => {
      circle.classList.toggle('is-muted', !!category && category !== key); circle.classList.toggle('is-highlighted', category === key); button.classList.toggle('is-highlighted', category === key);
    });
    situations.forEach((row) => {
      const palette={pendente:2,convertido:1,cancelado:4,analise:3,aprovado:0,vencido:4,negado:4,finalizado:6,sem_conversao:6,com_nota:1,expedicao:0,outras_etapas:6,em_aberto:2,quitada:1,sem_cobranca:6,pago:1,parcial:2,aberto:0};
      const color=palette[row.categoria]===undefined?6:palette[row.categoria];
      const fraction = total ? Number(row.quantidade) / total : 0;
      const label = statusLabel(row.categoria), selected = row.categoria === (type === 'boletos' ? applied.status : applied.etapa);
      const arcLength = fraction * circumference, gap = situations.length > 1 ? Math.min(4, arcLength * .15) : 0;
      const circle = svgNode('circle', { cx: 90, cy: 90, r: 64, class: 'donut-segment', 'data-category': row.categoria, stroke: `url(#distribution-${color})`, 'stroke-dasharray': `${Math.max(0, arcLength - gap)} ${circumference - Math.max(0, arcLength - gap)}`, 'stroke-dashoffset': -offset - gap / 2, transform: 'rotate(-90 90 90)', role: 'button', tabindex: 0, 'aria-pressed': selected, 'aria-label': `${label}: ${number(row.quantidade)} (${percent(fraction * 100)}). Filtrar painel.` });
      circle.append(svgNode('title', {}, `${label} · ${percent(fraction * 100)} · ${money(row.valor)}`)); svg.append(circle); offset += arcLength;
      const button = el('button', null, 'situation-item'); button.type = 'button';
      button.dataset.category = row.categoria; button.setAttribute('aria-pressed', String(selected)); button.style.setProperty('--slice-color', colors[color]);
      const copy = el('span', null, 'situation-copy'), stats = el('span', null, 'situation-stats'), track = el('span', null, 'situation-track'), fill = el('i');
      copy.append(el('span', label), el('small', money(row.valor)));
      stats.append(el('strong', number(row.quantidade)), el('span', percent(fraction * 100), 'situation-percent'));
      fill.style.width = (fraction * 100) + '%'; track.append(fill); track.setAttribute('aria-hidden', 'true');
      button.append(el('span', null, 'legend-dot'), copy, stats, track);
      pairs.push({ circle, button, category: row.categoria });
      [circle, button].forEach(target => {
        target.addEventListener('pointerenter', () => highlight(row.categoria)); target.addEventListener('pointerleave', () => highlight(null));
        target.addEventListener('focus', () => highlight(row.categoria)); target.addEventListener('blur', () => highlight(null));
        target.addEventListener('click', () => filterPanel(row.categoria));
      });
      circle.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); filterPanel(row.categoria); } });
      legend.append(button);
    });
    svg.append(svgNode('text', { x: 90, y: 88, class: 'donut-count' }, number(total)), svgNode('text', { x: 90, y: 107, class: 'donut-label' }, type === 'boletos' ? (commissionByNote ? 'NF-e' : 'parcelas') : 'documentos'));
    const layout = el('div', null, 'situation-layout'); layout.append(svg, legend);
    $('situations').replaceChildren(layout, el('p', 'Selecione uma fatia ou situação para filtrar o painel.', 'chart-hint'));
  }
  function renderCustomers(customers) {
    const fragment = document.createDocumentFragment();
    customers.forEach((row, index) => {
      const button = el('button', null, 'customer-rank'); button.type = 'button'; const copy = el('span'); copy.append(el('b', row.cliente), el('small', number(row.quantidade) + (type === 'boletos' ? (commissionByNote ? ' NF-e' : ' parcelas') : ' documentos')));
      button.append(el('span', String(index + 1).padStart(2, '0')), copy, el('strong', money(row.valor)));
      button.addEventListener('click', () => { rememberOverview();currentClient=Number(row.clienteId)>0||Number(row.grupoClienteId)>0?{id:Number(row.clienteId),grupoId:Number(row.grupoClienteId)||0,nome:row.cliente}:null;$('filters').elements.cliente.value = row.cliente; applyFilters('drill'); }); fragment.append(button);
    });
    $('topCustomers').replaceChildren(customers.length ? fragment : el('p', 'Nenhum cliente com movimento no período.', 'analytics-empty'));
  }
  function renderAttention(data) {
    const fragment = document.createDocumentFragment(), r = data.resumo;
    const add = (value, label, caption) => { const row = el('div', null, 'attention-row'), copy = el('span'); copy.append(el('b', label), el('small', caption)); row.append(el('strong', value), copy); fragment.append(row); };
    const quantity = (category) => (data.situacoes.find((row) => row.categoria === category) || {}).quantidade || 0;
    const volume = (category) => (data.situacoes.find((row) => row.categoria === category) || {}).valor || 0;
    const commercial = (count,label,caption,category,value) => {
      const row=el('button',null,'attention-action');row.type='button';row.dataset.attentionCategory=category;row.disabled=!Number(count);row.setAttribute('aria-pressed',String(category===applied.etapa));
      const countBlock=el('span',null,'attention-quantity');countBlock.append(el('strong',number(count)),el('small',type==='orcamentos'?'orçamentos':'pedidos'));
      const copy=el('span',null,'attention-copy');copy.append(el('b',label));if(value!=null)copy.append(el('span',money(value),'attention-volume'));copy.append(el('small',caption));
      row.append(countBlock,copy,el('span','›','attention-chevron'));row.addEventListener('click',()=>filterPanel(category));fragment.append(row);
    };
    if (type === 'orcamentos') {
      commercial(r.pendentes,'Pendentes','Acompanhe a resposta do cliente para avançar a proposta.','pendente',r.valorPendente);
      if(Number(quantity('analise')))commercial(quantity('analise'),'Em análise','Confira a análise registrada no sistema.','analise',volume('analise'));
      if(Number(quantity('aprovado')))commercial(quantity('aprovado'),'Aprovados, sem pedido gerado','Acompanhe a geração do pedido para concluir esta etapa.','aprovado',volume('aprovado'));
      if(Number(quantity('vencido')))commercial(quantity('vencido'),'Vencidos','Revise a validade e retome a negociação com o cliente.','vencido',volume('vencido'));
      if(Number(quantity('finalizado')))commercial(quantity('finalizado'),'Finalizados, sem pedido vinculado','Confira o vínculo do pedido no sistema.','finalizado',volume('finalizado'));
    } else if (type === 'pedidos') {
      commercial(r.pendentesDiretos,'Pendentes — inclusão direta','Sem orçamento de origem e ainda no status Pendente. Acompanhe a liberação.','pendente_direto',r.valorPendentesDiretos);
      commercial(r.naExpedicao,'Na expedição','Em preparação operacional, sem nota fiscal autorizada.','expedicao',volume('expedicao'));
      commercial(r.comNota,'Com NF autorizada','Faturamento iniciado. A nota pode corresponder a um faturamento parcial.','com_nota',volume('com_nota'));
      if(Number(r.atrasados))commercial(r.atrasados,'Alerta: entrega prevista vencida','Data de entrega anterior a hoje, sem NF. Pode incluir pendentes e pedidos na expedição.','atrasado');
    } else if (type === 'notas') {
      add(money(r.recebido), 'Recebido das cobranças', 'Inclui as parcelas dessas notas em qualquer vencimento.');
      add(number(r.notasAbertas), 'Notas com saldo em aberto', money(r.saldo) + ' a receber nas cobranças vinculadas.');
      add(number(r.semCobranca), 'Sem cobrança vinculada', 'Não indica quitação. Pode haver venda à vista ou cobrança ainda não registrada.');
    } else if (data.atrasos.length) data.atrasos.forEach((row) => add(money(row.valor), row.faixa, number(row.quantidade) + financialLabel(' parcelas com saldo vencido', ' títulos de clientes em atraso')));
    else fragment.append(el('p', financialLabel('Nenhum saldo vencido nas parcelas deste período.', 'Nenhuma comissão vinculada a títulos vencidos neste período.'), 'attention-good'));
    $('attention').replaceChildren(fragment);
  }
  function columns(financial, charges = false) {
    if (financial && !charges && commissionByNote) return ['NF-e', 'Cliente', 'Emissão', 'Comissão prevista', 'Comissão liberada', 'Comissão pendente', 'Situação', 'Último recebimento'];
    if (financial && !charges && commissions) return [views.boletos.number, 'Cliente', 'Nota fiscal', 'Vencimento', 'Comissão prevista', 'Sobre recebimentos', 'Comissão pendente', 'Situação do cliente', 'Cliente pagou em', 'Nosso número', 'Documento'];
    if (financial) return [views.boletos.number, ...(charges ? ['Cobrança'] : []), 'Cliente', 'Nota fiscal', 'Vencimento', 'Valor', 'Recebido', 'Saldo', 'Situação', 'Recebimento', 'Nosso número', 'Documento'];
    return [views[type].number, ...(type === 'orcamentos' && quoteProposal ? ['Cliente proposta','Cliente'] : ['Cliente']), 'Emissão', 'Valor', 'Situação', ...(type === 'orcamentos' ? [...(quoteCancellationReasons ? ['Motivo do cancelamento'] : []),'Conversão','PDF / Impressão'] : type === 'pedidos' ? ['% Separado','Nº carga','Orçamento de origem','Faturamento','Entrega prevista', ...(impressaoDisponibilidade ? ['Impressão de disponibilidade'] : [])] : ['Pedido de origem','Recebido','Saldo a receber','Pagamentos'])];
  }
  const separationPercent = value => Number.isFinite(Number(value)) && Number(value)>0 ? new Intl.NumberFormat('pt-BR', {maximumFractionDigits:2}).format(Number(value)) + '%' : '—';
  function values(row, financial, charges = false) {
    if (financial && !charges && commissionByNote) return [row.numero || row.nota || row.id, row.cliente, date(row.data), money(row.valor), money(row.recebido), money(row.saldo), statusLabel(row.status), date(row.pagamento)];
    if (financial) return [row.numero || row.id, ...(charges ? [row.tipoCobranca || 'Boleto'] : []), row.cliente, row.nota, date(row.vencimento), money(row.valor), money(row.recebido), money(row.saldo), (charges ? categories[row.status] : statusLabel(row.status)) + (row.status === 'vencido' && Number(row.recebido) > 0 ? ' · parcial' : ''), date(row.pagamento), row.nossoNumero, row.boletoDisponivel ? 'Boleto disponível' : '—'];
    return [type === 'orcamentos' && Number(row.origem)>0 ? row.origem : row.numero || row.id, ...(type === 'orcamentos' ? [...(quoteProposal ? [row.clienteProposta] : []), row.clienteNormal || row.cliente] : [row.cliente]), date(row.data), money(row.valor), row.status, ...(type === 'orcamentos' ? [...(quoteCancellationReasons ? [Number(row.cancelamentoReal) ? row.motivoCancelamento : '—'] : []), Number(row.convertido) ? 'Convertido em pedido' : 'Sem pedido ativo', document.body.dataset.demonstracao === '1' ? '—' : 'Abrir PDF'] : type === 'pedidos' ? [separationPercent(row.percentualSeparado), Number(row.numeroCarga)>0 ? row.numeroCarga : '—', Number(row.origem) || '—', Number(row.comNota) ? 'Com NF autorizada' : 'Sem NF vinculada', date(row.entrega), ...(impressaoDisponibilidade ? [document.body.dataset.demonstracao === '1' ? '—' : 'Abrir PDF'] : [])] : [Number(row.origem) || '—', money(row.recebido), money(row.saldo), 'Ver cobranças'])];
  }
  function table(data, financial, charges = false) {
    if (!data.length) return el('p', charges ? 'Nenhuma cobrança vinculada a esta nota.' : 'Nenhum registro encontrado. Amplie o período ou limpe os filtros para consultar outros documentos.', 'empty-state');
    const node = el('table'), head = el('thead'), rowHead = el('tr');
    const quote = !financial && type === 'orcamentos';
    const noteCommission = financial && !charges && commissionByNote;
    const quoteClient = quoteProposal ? 2 : 1, quoteStatus = quoteProposal ? 5 : 4;
    const numericColumns=financial?(noteCommission?[3,4,5]:charges?[5,6,7]:[4,5,6]):type==='notas'?[3,6,7]:quote?[quoteProposal?4:3]:type==='pedidos'?[3,5]:[3];
    columns(financial, charges).forEach((title,index) => { const th = el('th', title); th.scope = 'col';if(numericColumns.includes(index))th.className='numeric'; if(financial && title==='Documento')th.classList.add('document-download-cell'); rowHead.append(th); }); head.append(rowHead); node.append(head); const body = el('tbody');
    data.forEach((row) => {
      const line = el('tr'), cells = values(row, financial, charges);
      cells.forEach((value, index) => {
        const td = el('td', value == null || value === '' ? '—' : value);
        if (quote ? index === quoteClient || (quoteProposal && index === 1) : index === (financial && charges ? 2 : 1)) td.className = 'wrap';
        if (quote && index === 0) td.classList.add('document-reference');
        if (quote && index === (quoteProposal && Number(row.clientePropostaId)>0 ? 1 : quoteClient)) td.append(el('span', 'Principal', 'principal-client'));
        if (numericColumns.includes(index)) td.classList.add('numeric');
        if ((financial && index === (noteCommission ? 6 : charges ? 8 : 7)) || (!financial && index === (quote ? quoteStatus : 4))) { td.replaceChildren(el('span', value, `badge ${financial ? row.status : row.categoria || ''}`)); }
        if (!financial && type === 'notas' && index === 0 && document.body.dataset.demonstracao !== '1') {
          const button = documentButton('DANFE', 'danfe', row.id, 'document-open');
          const icon = svgNode('svg', {viewBox:'0 0 24 28', width:19, height:23, 'aria-hidden':'true'});
          icon.append(svgNode('path', {d:'M4 1h10l6 6v19H4zM14 1v7h6M7 12h10M7 15h10M7 19v4m3-4v4m3-4v4m3-4v4', fill:'none', stroke:'currentColor', 'stroke-width':1.5, 'stroke-linejoin':'round'}));
          button.replaceChildren(icon, el('span', 'DANFE'));
          button.title = 'Abrir DANFE da nota ' + (row.numero || row.id); button.setAttribute('aria-label', button.title);
          const number = el('span', null, 'document-number-group'); number.append(el('span', value), button); td.replaceChildren(number);
        }
        if (!financial && type === 'orcamentos' && index === cells.length - 1 && document.body.dataset.demonstracao !== '1') {
          td.replaceChildren(documentButton('Abrir PDF', 'orcamento', row.id));
        }
        if (!financial && type === 'pedidos' && impressaoDisponibilidade && index === cells.length - 1 && document.body.dataset.demonstracao !== '1') {
          const button = documentButton('Abrir PDF', 'disponibilidade', row.id);
          button.title = 'Impressão de disponibilidade do pedido ' + (row.numero || row.id);
          button.setAttribute('aria-label', button.title); td.replaceChildren(button);
        }
        if (!financial && type === 'notas' && index === cells.length - 1) {
          const actions = el('div', null, 'document-actions'), button = el('button', 'Ver cobranças', 'document-action'); button.type = 'button'; button.addEventListener('click', () => showCharges(row)); actions.append(button);
          if (row.boletoDisponivel) { const boleto = documentButton('Baixar boletos', 'boletosNota', row.id); boleto.title = 'Baixar todos os boletos disponíveis da nota em um ZIP'; actions.append(boleto); }
          td.replaceChildren(actions);
        }
        if (financial && !noteCommission && index === cells.length - 1) { td.classList.add('document-download-cell'); if(row.boletoDisponivel)td.replaceChildren(documentButton('Baixar boleto', 'boleto', row.id)); }
        line.append(td);
      }); body.append(line);
    }); node.append(body); return node;
  }
  function documentButton(label, kind, id, cls = 'document-action') {
    const button = el('button', label, cls); button.type = 'button';
    button.addEventListener('click', () => downloadDocument(kind, id, button)); return button;
  }
  async function downloadDocument(kind, id, button) {
    const inline = kind === 'danfe' || kind === 'orcamento' || kind === 'disponibilidade';
    if (inline) {
      // A própria aba carrega o PDF; não depende do painel em segundo plano.
      const tab = window.open(`documento.php?${new URLSearchParams({documento:kind,id,...(gestor ? {gestor} : {})})}`, '_blank');
      if (tab) tab.opener = null;
      else $('portalStatus').textContent = 'Permita abrir uma nova janela para visualizar o PDF.';
      return;
    }
    const original = button && button.textContent;
    if (button) { button.disabled = true; button.textContent = 'Preparando…'; }
    try {
      const response = await fetch(`api.php?${new URLSearchParams({acao:'documento',documento:kind,id})}`, {method:'POST',credentials:'same-origin',headers:{'X-CSRF-Token':csrf,...(gestor ? {'X-Portal-Gestor':gestor} : {})}});
      if (!response.ok) { const error = await response.json(); throw new Error(error.error || 'Não foi possível abrir o documento.'); }
      const contentType = kind === 'boletosNota' ? 'application/zip' : 'application/pdf';
      if (!(response.headers.get('Content-Type') || '').startsWith(contentType)) throw new Error('O documento não está disponível no formato esperado.');
      const url = URL.createObjectURL(await response.blob());
      const link = el('a'); link.href = url; link.download = kind === 'boletosNota' ? `boletos-nota-${id}.zip` : `boleto-${id}.pdf`; document.body.append(link); link.click(); link.remove();
      setTimeout(() => URL.revokeObjectURL(url), 60000);
    } catch (error) {
      if ($('chargesDialog').open) { const status = el('p', error.message, 'form-status'); status.setAttribute('role','alert'); $('chargesContent').prepend(status); }
      else $('portalStatus').textContent = error.message;
    } finally { if (button && button.isConnected) { button.disabled = false; button.textContent = original; } }
  }
  async function showCharges(row) {
    const current = ++chargeGeneration; $('chargesTitle').textContent = `Cobranças da nota ${row.numero}`; $('chargesContent').replaceChildren(el('p', 'Carregando cobranças…', 'empty-state')); $('chargesDialog').showModal();
    $('chargesDescription').textContent = 'Todas as parcelas da nota, independentemente do vencimento.';
    try {
      const result = await request('cobrancas', { nota: row.id });
      if (current !== chargeGeneration) return;
      $('chargesContent').replaceChildren(table(result.linhas, true, true));
    }
    catch (error) { if (current === chargeGeneration) $('chargesContent').replaceChildren(el('p', error.message, 'form-status')); }
  }
  $('closeCharges').addEventListener('click', () => { chargeGeneration++; $('chargesDialog').close(); }); $('chargesDialog').addEventListener('cancel', () => chargeGeneration++);
  async function load(withAnalytics = true, focusCharts = false) {
    withAnalytics = withAnalytics || !analyticsData;
    if (withAnalytics) { composition=''; compositionCaption=''; }
    $('compositionDetail').hidden = !composition && !quoteSegment;
    focusCharts = focusCharts && type !== 'inicio' && type !== 'regioes';
    const current = ++generation; if (controller) controller.abort(); controller = new AbortController();
    setLoading(true);
    $('portalStatus').textContent = '';
    if (!$('documents').hasChildNodes()) $('documents').replaceChildren(el('p', 'Carregando documentos…', 'empty-state'));
    $('documents').setAttribute('aria-busy', 'true');
    $('previous').disabled = true; $('next').disabled = true; $('exportCsv').disabled = true;
    if (withAnalytics) {
      analyticsData = null;
      // Preserva a altura e os blocos enquanto a nova consulta é carregada.
      ['evolutionChart','situations','topCustomers','attention'].forEach((id) => {
        if (!$(id).hasChildNodes()) $(id).replaceChildren(el('p', 'Carregando análise…', 'analytics-empty'));
      });
    }
    ['summary','analytics','insights'].forEach((id) => $(id).setAttribute('aria-busy', String(withAnalytics)));
    syncMonthFilters();
    $('periodLabel').textContent = date(applied.inicio) + ' a ' + date(applied.fim);
    if (type === 'inicio') home.loading();
    updateSelection();
    try {
      const options = { signal: controller.signal }, params = { ...applied, tipo: type };
      if (type === 'comparativo') {
        const result = await comparison.load(applied,options);
        if (current===generation && result) updateTime(result.atualizado);
        return;
      }
      if (type === 'regioes') { await regional.load(applied,options); if(current===generation) $('portalStatus').textContent=''; return; }
      if (type === 'inicio') {
        const types = ['orcamentos','pedidos','notas','boletos'];
        const results = await Promise.all(types.map(tipo => request('resumo', { inicio:applied.inicio, fim:applied.fim, tipo }, options)));
        if (current !== generation) return;
        home.render(Object.fromEntries(types.map((key,i) => [key,results[i]])), applied);
        updateTime(results[0].atualizado); $('portalStatus').textContent=''; return;
      }
      const jobs = [request('lista', { ...params, pagina: page, ...(type === 'boletos' && composition ? {composicao:composition} : {}), ...(type === 'orcamentos' && quoteSegment ? {segmento:quoteSegment} : {}) }, options)]; if (withAnalytics) jobs.push(request('resumo', params, options));
      const [list, analysis] = await Promise.all(jobs); if (current !== generation) return;
      rows = list.linhas; $('documents').replaceChildren(table(rows, type === 'boletos')); $('listCount').textContent = number(rows.length) + ' registros nesta página';
      if (analysis) { analyticsData = analysis; renderSummary(analysis); updateTime(analysis.atualizado); }
      if (composition) {
        const r = analyticsData.resumo, field = ['saldo','vencido','proximos7'].includes(composition) ? 'saldo' : composition === 'recebido' ? 'recebido' : 'valor';
        const target = composition === 'media' ? Number(r.quantidade) ? Number(r.valor)/Number(r.quantidade) : 0 : r[composition === 'previsto' ? 'valor' : composition];
        const subtotal = rows.reduce((sum,row)=>sum+Number(row[field] || 0),0);
        $('compositionCaption').textContent = `${compositionCaption}: ${money(target)}. ${composition === 'media' ? 'Média = total dividido pela quantidade de ' + (commissionByNote ? 'NF-e. ' : 'títulos. ') : ''}Soma da coluna ${field === 'recebido' ? financialLabel('Recebido','Sobre recebimentos','Comissão liberada') : field === 'saldo' ? financialLabel('Saldo','Comissão pendente') : financialLabel('Valor','Comissão prevista')} nesta página: ${money(subtotal)}. ${composition === 'recebido' ? commissionByNote ? 'Somente NF-e com comissão liberada.' : 'Inclui pagamentos parciais.' : ''}`;
        $('documents').dataset.compositionField = field;
      } else delete $('documents').dataset.compositionField;
      if (quoteSegment) $('compositionCaption').textContent = 'Orçamentos do distrito ' + quoteSegment + '.';
      $('pageLabel').textContent = `Página ${page}`; $('previous').disabled = page <= 1; $('next').disabled = !list.mais; $('exportCsv').disabled = !rows.length; $('portalStatus').textContent = '';
      if (focusCharts) focusChartsAfterUpdate(current);
    } catch (error) {
      if (current !== generation || error.name === 'AbortError') return;
      $('portalStatus').textContent = error.message; $('documents').replaceChildren(el('p', 'Não foi possível carregar. Atualize para tentar novamente.', 'empty-state'));
      if (type === 'regioes') regional.error(error.message);
      if (type === 'inicio') home.error();
      if (withAnalytics) {
        $('summary').replaceChildren(); $('insights').replaceChildren();
        ['evolutionChart','situations','topCustomers','attention'].forEach((id) => $(id).replaceChildren(el('p', 'Análise indisponível. Atualize para tentar novamente.', 'analytics-empty')));
      }
    } finally {
      if (current === generation) {
        ['documents','summary','analytics','insights'].forEach((id) => $(id).setAttribute('aria-busy', 'false'));
        setLoading(false);
      }
    }
  }
  function applyFilters(origin='manual') { if(origin!=='month')explicitMonth=0;if(origin!=='drill')overviewPeriod=null;applied = Object.fromEntries(new FormData($('filters')));if(currentClient && applied.cliente===currentClient.nome){if(currentClient.grupoId>0)applied.grupoClienteId=currentClient.grupoId;else applied.clienteId=currentClient.id;applied.cliente='';}else currentClient=null; applied.status = type === 'boletos' ? applied.status : ''; applied.etapa = type === 'boletos' ? '' : applied.etapa; page = 1; load(true, origin==='drill' || origin==='reset'); }
  $('filters').addEventListener('submit', (event) => { event.preventDefault(); applyFilters(); });
  $('stageSelect').addEventListener('change', () => { applied.etapa = $('stageSelect').value; page = 1; load(true, true); });
  $('filters').elements.status.addEventListener('change', () => { applied.status = $('filters').elements.status.value; page = 1; load(true, true); });
  const monthNames = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
  let explicitMonth = 0;
  function monthPeriod(year, month = 0) {
    const today = $('portalToday').dateTime;
    const start = `${year}-${month ? String(month).padStart(2,'0') : '01'}-01`;
    const end = month ? new Date(Date.UTC(year, month, 0)).toISOString().slice(0,10) : `${year}-12-31`;
    return { inicio:start, fim:end > today ? today : end };
  }
  function syncMonthFilters() {
    const today = $('portalToday').dateTime, year = Number(applied.inicio.slice(0,4));
    $('periodYear').value = year; $('periodYear').max = Number(today.slice(0,4));
    const annual = monthPeriod(year);
    const accumulated = applied.inicio === annual.inicio && applied.fim === annual.fim;
    let selected = 0;
    document.querySelectorAll('[data-month]').forEach(button => {
      const month = Number(button.dataset.month), period = monthPeriod(year, month);
      button.disabled = period.inicio > today;
      const active = !button.disabled && applied.inicio === period.inicio && applied.fim === period.fim && (!accumulated || explicitMonth === month);
      button.setAttribute('aria-pressed', String(active));
      if (active) selected = month;
    });
    $('monthPeriodNote').textContent = `${date(applied.inicio)} a ${date(applied.fim)} · ` + (selected ? 'Clique novamente no mês para ver o acumulado do ano.' : accumulated ? 'Acumulado do ano. Clique em um mês para filtrar.' : 'Intervalo personalizado. Clique em um mês para filtrar.');
  }
  function selectMonth(year, month = 0) {
    const today = $('portalToday').dateTime;
    if (!Number.isInteger(year) || year < 1000 || year > Number(today.slice(0,4))) { syncMonthFilters(); return; }
    const period = monthPeriod(year, month);
    if (period.inicio > today) return;
    $('filters').elements.inicio.value = period.inicio; $('filters').elements.fim.value = period.fim;
    explicitMonth = month; applyFilters('month');
  }
  monthNames.forEach((name,index) => {
    const button = el('button', name); button.type = 'button'; button.dataset.month = index + 1;
    button.setAttribute('aria-pressed','false');
    button.addEventListener('click', () => selectMonth(Number($('periodYear').value), button.getAttribute('aria-pressed') === 'true' ? 0 : index + 1));
    $('monthBlocks').append(button);
  });
  $('periodYear').addEventListener('change', () => selectMonth(Number($('periodYear').value)));
  document.querySelectorAll('[data-period]').forEach((button) => button.addEventListener('click', () => {
    const today = $('portalToday').dateTime, start = new Date(today + 'T12:00:00Z'); if (button.dataset.period === 'month') start.setUTCDate(1); else start.setUTCDate(start.getUTCDate() - Number(button.dataset.period) + 1);
    $('filters').elements.inicio.value = iso(start); $('filters').elements.fim.value = today; applyFilters();
  }));
  $('clearFilters').addEventListener('click', () => { overviewPeriod=null; const period=monthPeriod(Number($('periodYear').value)); $('filters').elements.inicio.value=period.inicio; $('filters').elements.fim.value=period.fim; clearSelection(); });
  document.querySelectorAll('[data-measure]').forEach((button) => button.addEventListener('click', () => { measure = button.dataset.measure; document.querySelectorAll('[data-measure]').forEach((node) => node.setAttribute('aria-pressed', String(node === button))); renderChart(); }));
  function changeView(next, selection = {}) {
    if (!views[next]) return;
    const fromHome = type === 'inicio';
    if(selection.inicio && selection.fim) { rememberOverview(); applied.inicio=selection.inicio;applied.fim=selection.fim;$('filters').elements.inicio.value=selection.inicio;$('filters').elements.fim.value=selection.fim; }
    if(selection.cliente) { currentClient=selection.cliente.id>0||selection.cliente.grupoId>0?selection.cliente:null;delete applied.clienteId;delete applied.grupoClienteId;if(currentClient){if(currentClient.grupoId>0)applied.grupoClienteId=currentClient.grupoId;else applied.clienteId=currentClient.id;}applied.cliente=currentClient?'':selection.cliente.nome;$('filters').elements.cliente.value=selection.cliente.nome; }
    quoteSegment = ''; type = next; page = 1; applied.status = selection.status || ''; applied.etapa = selection.etapa || '';
    if(fromHome || next==='inicio' || next==='comparativo') { applied.numero='';$('filters').elements.numero.value=''; }
    $('portalMenu').querySelectorAll('[data-tipo]').forEach((node) => { const active=node.dataset.tipo===type;node.classList.toggle('active',active);if(active)node.setAttribute('aria-current','page');else node.removeAttribute('aria-current'); });
    setView(); $('filters').elements.status.value=applied.status; $('filters').elements.etapa.value=applied.etapa; window.scrollTo(0, 0); load();
  }
  $('portalMenu').addEventListener('click', (event) => { const button=event.target.closest('[data-tipo]');if(button && button.dataset.tipo!==type)changeView(button.dataset.tipo); });
  $('previous').addEventListener('click', () => { if (page > 1) { page--; load(false); } }); $('next').addEventListener('click', () => { page++; load(false); }); $('refresh').addEventListener('click', () => load());
  $('exportCsv').addEventListener('click', () => {
    const quote = (value) => { let text = String(value == null ? '' : value); if (/^\s*[=+\-@]|^[\t\r\n]/.test(text)) text = "'" + text; return '"' + text.replace(/"/g, '""') + '"'; };
    const csv = [columns(type === 'boletos'), ...rows.map((row) => values(row, type === 'boletos'))].map((row) => row.map(quote).join(';')).join('\r\n');
    const url = URL.createObjectURL(new Blob(['\uFEFF', csv], { type: 'text/csv;charset=utf-8' })), link = el('a'); link.href = url; link.download = `${document.body.dataset.demonstracao === '1' ? 'demonstracao-' : ''}representante-${type}-${applied.inicio}-pagina-${page}.csv`; document.body.append(link); link.click(); link.remove(); setTimeout(() => URL.revokeObjectURL(url), 1000);
  });
  let resizeTimer; window.addEventListener('resize', () => { clearTimeout(resizeTimer); resizeTimer = setTimeout(renderChart, 100); });
  if ($('demoToggle')) $('demoToggle').addEventListener('click', async () => {
    $('demoToggle').disabled = true;
    try { await post('alternarDemonstracao', { ativo: document.body.dataset.demonstracao === '1' ? '0' : '1' }); location.reload(); }
    catch (error) { $('portalStatus').textContent = error.message; $('demoToggle').disabled = false; }
  });
  $('logout').addEventListener('click', async () => {
    $('logout').disabled = true;
    try {
      await post('sair', {});
      if (gestor && window.parent !== window) window.parent.postMessage({ tipo: 'hardness-portal-gestor-fechar', contexto: gestor }, location.origin);
      else if (gestor) location.href = '../dashboard-personalizado/';
      else location.reload();
    } catch (error) { $('portalStatus').textContent = error.message; $('logout').disabled = false; }
  });
  setView(); load();
})();
