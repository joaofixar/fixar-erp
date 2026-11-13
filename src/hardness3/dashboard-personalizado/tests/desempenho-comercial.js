'use strict';
// php dashboard-personalizado/tests/desempenho-comercial.php --fixture | node dashboard-personalizado/tests/desempenho-comercial.js
// DOM e dados simulados, sem navegador ou rede.
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const assert = require('assert');
const base = path.resolve(__dirname, '..');
class Element {
  constructor(tag) { this.tagName = tag; this.children = []; this.listeners = {}; this.attributes = {}; this.style = {}; this.className = ''; this.text = ''; }
  set textContent(text) { this.text = String(text); this.children = []; }
  get textContent() { return this.text + this.children.map(c => c.textContent).join(' '); }
  appendChild(child) { this.children.push(child); return child; }
  setAttribute(key, value) { this.attributes[key] = value; }
  getAttribute(key) { return this.attributes[key]; }
  addEventListener(event, listener) { this.listeners[event] = listener; }
  querySelector() { return null; }
}
// Busca independente do DOM do browser, preservando a árvore de nós criada pelo componente.
function all(root, fn) { let out = fn(root) ? [root] : []; root.children.forEach(c => { out = out.concat(all(c, fn)); }); return out; }
const context = { window: {}, document: { createElement: tag => new Element(tag) } };
vm.runInNewContext(fs.readFileSync(path.join(base, 'desempenho-comercial.js'), 'utf8'), context);
const fixture = JSON.parse(fs.readFileSync(0, 'utf8'));
fixture.rotuloResponsavel = 'Vendedor';
fixture.linhas[0].cliente = '<img src=x onerror=alert(1)>';
while (fixture.linhas.length < 26) fixture.linhas.push(Object.assign({}, fixture.linhas[0], { cliente: 'Cliente ' + fixture.linhas.length }));
const root = new Element('section');
context.window.DashboardDesempenhoComercial.render(root, fixture);
assert.strictEqual(all(root, n => n.className === 'commercial-kpi').length, 8);
const cards = all(root, n => n.className === 'commercial-kpi');
assert.strictEqual(cards[4].children[0].textContent, 'Ticket médio pedido');
assert.strictEqual(cards[4].children[1].textContent.replace(/\s/g, ''), 'R$400,00');
assert.strictEqual(cards[5].children[0].textContent, 'Ticket médio orçamento');
assert.strictEqual(cards[5].children[1].textContent.replace(/\s/g, ''), 'R$500,00');
assert(cards[5].textContent.includes('quantidade de orçamentos válidos no mês de referência'));
assert.strictEqual(cards[6].children[1].textContent.replace(/\s/g, ''), 'R$3.000,00', 'Valor orçado inclui todos os meses filtrados');
assert(cards[6].textContent.includes('período filtrado'));
assert.strictEqual(cards[7].children[1].textContent, '60%');
assert(cards[7].textContent.includes('3 de 5 orçamentos do período'), 'Conversão usa quantidades de todo o período');
assert(!root.textContent.includes('RESULTADOS DO MÊS'), 'Cabeçalho não apresenta todos os cartões como mensais');
assert(root.textContent.includes('Não definida'));
const metaFixture = Object.assign({}, fixture, { resumo: Object.assign({}, fixture.resumo,
  { meta: 1000, valorMetaAtingido: 4900.5, atingimentoMeta: 490.05 }) });
const metaRoot = new Element('section');
context.window.DashboardDesempenhoComercial.render(metaRoot, metaFixture);
const metaCard = all(metaRoot, n => n.className === 'commercial-kpi')[2];
assert.strictEqual(metaCard.children[0].textContent, 'Meta atingida');
assert(metaCard.textContent.includes('490,1%') && metaCard.textContent.includes('1.000,00') && metaCard.textContent.includes('4.900,50'));
assert(metaCard.textContent.includes('Pedidos registrados no ERP'));
const metaPendente = Object.assign({}, fixture, { resumo: Object.assign({}, fixture.resumo,
  { meta: 2650000, valorMetaAtingido: null, atingimentoMeta: null }) });
const metaPendenteRoot = new Element('section');
context.window.DashboardDesempenhoComercial.render(metaPendenteRoot, metaPendente);
const metaPendenteCard = all(metaPendenteRoot, n => n.className === 'commercial-kpi')[2];
assert.strictEqual(metaPendenteCard.children[0].textContent, 'Meta do mês');
assert.strictEqual(metaPendenteCard.children[1].textContent.replace(/\s/g, ''), 'R$2.650.000,00', 'Meta cadastrada aparece mesmo com atingimento e percentual nulos');
assert(!metaPendenteCard.textContent.includes('Não calculada') && !metaPendenteCard.textContent.includes('Calcule o valor atingido'));
metaPendente.resumo = Object.assign({}, metaPendente.resumo, { valorMetaAtingido: 0, atingimentoMeta: 0 });
context.window.DashboardDesempenhoComercial.render(metaPendenteRoot, metaPendente);
const metaZeroCard = all(metaPendenteRoot, n => n.className === 'commercial-kpi')[2];
assert.strictEqual(metaZeroCard.children[0].textContent, 'Meta atingida');
assert.strictEqual(metaZeroCard.children[1].textContent, '0%', 'Atingimento calculado em zero continua sendo percentual');
assert(!root.textContent.includes('Pedidos em Fábrica'));
assert.strictEqual(all(root, n => n.tagName === 'img').length, 0, 'Nomes são texto, nunca HTML');
assert(root.textContent.includes('<img src=x onerror=alert(1)>'));
let body = all(root, n => n.tagName === 'tbody')[0];
assert.strictEqual(body.children.length, 25, '25 clientes por página');
assert.strictEqual(all(root, n => n.tagName === 'thead')[0].children[0].children.length, 4, 'Cliente e três colunas de documentos');
const view = all(root, n => n.tagName === 'select')[0];
view.value = 'mensal'; view.listeners.change();
body = all(root, n => n.tagName === 'tbody')[0];
assert.strictEqual(body.children[0].children.length, 27, 'Cliente, orçamentos/pedidos por mês e seus dois totais');
assert.strictEqual(all(root, n => n.tagName === 'thead')[0].children.length, 2, 'Cabeçalho agrupado por mês');
assert(!all(root, n => n.tagName === 'table')[0].textContent.includes('Notas emitidas'), 'Evolução mensal não exibe NF');
const next = all(root, n => n.tagName === 'button' && n.textContent === 'Próxima')[0];
next.listeners.click();
body = all(root, n => n.tagName === 'tbody')[0];
assert.strictEqual(body.children.length, 1);
assert.strictEqual(next.disabled, true);
assert(root.textContent.includes('26–26 de 26'));
assert(root.textContent.includes('TOTAL DO PERÍODO'));
view.value = 'periodo'; view.listeners.change();
assert.strictEqual(all(root, n => n.tagName === 'tbody')[0].children[0].children.length, 4, 'Resumo do período preserva as três etapas');
assert(all(root, n => n.tagName === 'table')[0].textContent.includes('Notas emitidas'));
fixture.linhas = [];
fixture.resumo.conversao = null;
fixture.resumo.ticketMedio = null;
fixture.resumo.ticketMedioOrcamento = null;
fixture.resumo.projecao = null;
context.window.DashboardDesempenhoComercial.render(root, fixture);
assert(root.textContent.includes('Nenhum movimento encontrado'));
assert(!root.textContent.includes('NaN') && !root.textContent.includes('Infinity'));
assert.strictEqual(all(root, n => n.className === 'commercial-kpi')[4].children[1].textContent, '—');
assert.strictEqual(all(root, n => n.className === 'commercial-kpi')[5].children[1].textContent, '—');
fixture.rotuloResponsavel = 'Representante';
context.window.DashboardDesempenhoComercial.render(root, fixture);
assert(root.textContent.includes('Cliente / Representante'));
assert(root.textContent.includes('descontando IPI e frete'), 'Configuração anterior preserva o texto líquido');
fixture.valorNotas = 'total';
context.window.DashboardDesempenhoComercial.render(root, fixture);
assert(root.textContent.includes('incluindo IPI e frete'));
assert(root.textContent.includes('Valor dos pedidos ÷ quantidade de pedidos válidos no mês de referência'));
assert(root.textContent.includes('Orçamentos, pedidos e notas pelo valor total.'));
assert(!root.textContent.includes('líquido') && !root.textContent.includes('descontando IPI'), 'Valor total sem rótulos líquidos');

fixture.comparativo = {
  periodos: { atual: { inicio: '2026-04-01', fim: '2026-04-15' }, anterior: { inicio: '2026-03-01', fim: '2026-03-15' } },
  metricas: {
    faturamento: { atual: 800, anterior: 1000, percentual: -20, tendencia: 'baixa' },
    valorPedidos: { atual: 800, anterior: 1000, percentual: -20, tendencia: 'baixa' },
    ticketMedio: { atual: 400, anterior: 375, percentual: 6.668, tendencia: 'alta' },
    ticketMedioOrcamento: { atual: 500, anterior: 200, percentual: 150, tendencia: 'alta' },
    valorOrcado: { atual: 1000, anterior: 800, percentual: 25, tendencia: 'alta' }
  }
};
context.window.DashboardDesempenhoComercial.render(root, fixture);
const colored = all(root, n => n.tagName === 'article' && n.className.startsWith('commercial-kpi'));
assert(colored[0].className.includes('commercial-trend-down') && colored[0].textContent.includes('↓ -20%'));
assert(colored[4].className.includes('commercial-trend-up') && colored[4].textContent.includes('↑ +6,7%'));
assert(colored[5].className.includes('commercial-trend-up') && colored[5].textContent.includes('↑ +150%'));
assert(colored[5].textContent.includes('Anterior:') && colored[5].textContent.includes('200,00'));
assert(colored[6].textContent.includes('↑ +25%') && colored[6].textContent.replace(/\s/g, '').includes('Nestemês:R$1.000,00'));
assert.strictEqual(colored[6].children[1].textContent.replace(/\s/g, ''), 'R$3.000,00', 'Acumulado preservado com comparação mensal separada');
assert(colored[0].textContent.includes('01/04/2026 a 15/04/2026 × 01/03/2026 a 15/03/2026'));
assert.strictEqual(all(root, n => n.className === 'commercial-comparison').length, 5, 'Comparativo inclui os dois tickets');
assert.strictEqual(colored[2].className, 'commercial-kpi');
fixture.comparativo.metricas.faturamento = { atual: 100, anterior: 0, percentual: null, tendencia: 'alta' };
fixture.comparativo.metricas.ticketMedio = { atual: null, anterior: null, percentual: null, tendencia: 'sem_base' };
fixture.comparativo.metricas.ticketMedioOrcamento = { atual: null, anterior: null, percentual: null, tendencia: 'sem_base' };
fixture.comparativo.metricas.valorOrcado = { atual: 0, anterior: 0, percentual: 0, tendencia: 'estavel' };
context.window.DashboardDesempenhoComercial.render(root, fixture);
assert(root.textContent.includes('↑ Sem base percentual') && root.textContent.includes('— Sem base percentual'));
assert(root.textContent.includes('→ 0% vs mês anterior'));
assert(!root.textContent.includes('NaN') && !root.textContent.includes('Infinity'));
assert.strictEqual(all(root, n => n.className.includes('commercial-trend-neutral')).length, 3);

// Executa o salvamento real da seleção de acesso, com controles fictícios.
const source = fs.readFileSync(path.join(base, 'dashboard.js'), 'utf8');
const id = 'desempenho_comercial_teste';
let sellerValue = '__todos__';
const seller = { get value() { return sellerValue; }, options: [{ text: 'Vendedor A' }], selectedIndex: 0 };
const restrictionContainer = { getAttribute: () => id, querySelector: () => seller };
const access = {
  state: { access: { acessos: { usuarios: {} } }, accessSubject: '7', accessMode: 'usuarios' },
  document: { querySelectorAll: selector => selector === '.indicator-access-list input:checked' ? [{ value: id }] : [restrictionContainer] },
  $: () => ({ textContent: '' })
};
vm.runInNewContext(source.slice(source.indexOf('  function commitAccessDetail('), source.indexOf('  function renderAccessSubjects(')), access);
assert.strictEqual(access.commitAccessDetail(), true);
assert.strictEqual(Object.keys(access.state.access.acessos.usuarios['7'].restricoes).length, 0, 'Gestor só ganha o indicador escolhido');
assert.strictEqual(access.state.access.acessos.usuarios['7'].indicadores[0], id);
sellerValue = '7';
assert.strictEqual(access.commitAccessDetail(), true);
assert.strictEqual(access.state.access.acessos.usuarios['7'].restricoes[id].vendedorId, '7');
sellerValue = '';
assert.strictEqual(access.commitAccessDetail(), false, 'Nova liberação exige escolha explícita da visão');
console.log('OK: cartões, matriz, paginação, conteúdo seguro, vazio e seleção individual/gerencial.');
// Ao falhar uma consulta, o mapa do indicador anterior não pode continuar visível.
const elements = {};
let walletsDestroyed = 0;
const cleanup = {
  window: {}, portalRepresentantesController: null,
  cotacoesController: null, commercialController: null, stackedChartController: null, state: {},
  carteiraBancosController: { destroy() { walletsDestroyed++; } },
  $: selector => elements[selector] || (elements[selector] = { innerHTML: 'anterior', hidden: false }),
  startLoadingProgress: () => {}, stopLoadingProgress: () => {}, setExportAvailability: () => {},
  escapeHtml: value => value
};
vm.runInNewContext(source.slice(source.indexOf('  function renderLoading('), source.indexOf('  function chart(')), cleanup);
cleanup.renderLoading(); assert.strictEqual(elements['#indicatorMap'].innerHTML, '');
assert.strictEqual(walletsDestroyed, 1);
assert.strictEqual(cleanup.carteiraBancosController, null);
cleanup.carteiraBancosController = { destroy() { walletsDestroyed++; } };
elements['#indicatorMap'].innerHTML = 'anterior'; cleanup.renderError({ message: 'Falha simulada' });
assert.strictEqual(elements['#indicatorMap'].innerHTML, '');
assert.strictEqual(walletsDestroyed, 2);

// Cabeçalhos ordenam representantes recolhidos; na expansão, somente clientes.
const sortable = JSON.parse(JSON.stringify(fixture));
function portfolioClient(name, quotes, orders) { return { cliente:name, vendedor:'Vendedor', meses:{'2026-04':{orcamentos:quotes,pedidos:orders}}, totais:{orcamentos:quotes,pedidos:orders} }; }
sortable.carteiraRepresentantes = { linhas:[
 {representanteId:'1',representante:'Maior orçamento',meses:{},totais:{orcamentos:100,pedidos:10},clientes:[portfolioClient('Cliente orçamento',80,0),portfolioClient('Cliente pedido',20,10)]},
 {representanteId:'2',representante:'Maior pedido',meses:{},totais:{orcamentos:50,pedidos:200},clientes:[portfolioClient('Cliente 2 orçamento',50,0),portfolioClient('Cliente 2 pedido',0,200)]}
],totais:{orcamentos:150,pedidos:210},totaisMeses:{} };
const sortableRoot = new Element('section');
context.window.DashboardDesempenhoComercial.render(sortableRoot,sortable,'representantes');
const firstRep = () => all(sortableRoot,n=>n.className==='commercial-representative-toggle')[0];
assert(firstRep().textContent.includes('Maior orçamento'));
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='pedidos-total')[0].listeners.click();
assert(firstRep().textContent.includes('Maior pedido'));
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='orcamentos-total')[0].listeners.click();
assert(firstRep().textContent.includes('Maior orçamento'));
firstRep().listeners.click({stopPropagation(){}});
assert(all(sortableRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente orçamento'));
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='pedidos-total')[0].listeners.click();
assert(all(sortableRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente pedido'));

// Regressão: o segundo representante não pode trocar de posição ao ordenar seus clientes.
firstRep().listeners.click({stopPropagation(){}});
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='orcamentos-total')[0].listeners.click();
let reps = () => all(sortableRoot,n=>n.className==='commercial-representative-toggle');
reps()[1].listeners.click({stopPropagation(){}});
let repOrder = reps().map(n=>n.getAttribute('data-commercial-representative'));
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='pedidos-total')[0].listeners.click();
assert.deepStrictEqual(reps().map(n=>n.getAttribute('data-commercial-representative')),repOrder);
assert.strictEqual(reps()[1].getAttribute('aria-expanded'),'true');
assert(all(sortableRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente 2 pedido'));
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='orcamentos-total')[0].listeners.click();
assert(all(sortableRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente 2 orçamento'));
assert.deepStrictEqual(reps().map(n=>n.getAttribute('data-commercial-representative')),repOrder);
let monthlyView = all(sortableRoot,n=>n.tagName==='select')[0];monthlyView.value='mensal';monthlyView.listeners.change();
all(sortableRoot,n=>n.getAttribute('data-commercial-sort')==='pedidos-2026-04')[0].listeners.click();
assert(all(sortableRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente 2 pedido'));
assert.deepStrictEqual(reps().map(n=>n.getAttribute('data-commercial-representative')),repOrder);
// A ordenação dentro da expansão também preserva a segunda página de representantes.
const paged = JSON.parse(JSON.stringify(sortable));
paged.carteiraRepresentantes.linhas = Array.from({length:27},(_,i)=>({representanteId:String(i+1),representante:'Representante '+(i+1),meses:{},totais:{orcamentos:1000-i,pedidos:i},clientes:[portfolioClient('Cliente orçamento '+i,50,0),portfolioClient('Cliente pedido '+i,0,100)]}));
const pagedRoot = new Element('section');context.window.DashboardDesempenhoComercial.render(pagedRoot,paged,'representantes');
all(pagedRoot,n=>n.tagName==='button'&&n.textContent==='Próxima')[0].listeners.click();
let pagedReps = () => all(pagedRoot,n=>n.className==='commercial-representative-toggle');
assert.strictEqual(pagedReps()[1].getAttribute('data-commercial-representative'),'27');
pagedReps()[1].listeners.click({stopPropagation(){}});
all(pagedRoot,n=>n.getAttribute('data-commercial-sort')==='pedidos-total')[0].listeners.click();
assert.strictEqual(pagedReps()[0].getAttribute('data-commercial-representative'),'26');
assert.strictEqual(pagedReps()[1].getAttribute('data-commercial-representative'),'27');
assert.strictEqual(pagedReps()[1].getAttribute('aria-expanded'),'true');
assert(all(pagedRoot,n=>n.className==='commercial-portfolio-client')[0].textContent.includes('Cliente pedido 26'));
console.log('OK: ordenação interna mantém segundo representante, mês, expansão e página.');

// Histórico assíncrono: seleção, retorno e respostas que chegam fora de ordem.
(async function () {
  fixture.linhas = [{clienteId:'cad:1',responsavelId:'7',cliente:'Cliente histórico',responsavel:'Vendedor teste',meses:{},totais:{orcamentos:0,pedidos:0,notas:0}}];
  const historyRoot = new Element('section');
  const pending = [];
  const controller = context.window.DashboardDesempenhoComercial.render(historyRoot, fixture, 'vendedores', line => new Promise(resolve => pending.push({line, resolve})));
  const find = css => all(historyRoot, n => n.className.split(' ').includes(css))[0];
  assert(find('commercial-history').textContent.includes('Período filtrado'));
  let clients = all(historyRoot, n => n.className === 'commercial-client-select');
  clients[0].listeners.click({stopPropagation(){}});
  assert(find('commercial-history-body').textContent.includes('Carregando'));
  assert.strictEqual(pending[0].line.clienteId, fixture.linhas[0].clienteId);
  const reset = all(historyRoot, n => n.tagName === 'button' && n.textContent === 'Voltar ao período filtrado')[0];
  reset.listeners.click();
  const history = {periodo:{inicio:'2025-10-01',fim:'2026-03-15'},meses:['2025-10','2025-11','2025-12','2026-01','2026-02','2026-03'],totaisMeses:{'2025-10':{orcamentos:123,pedidos:45}}};
  pending[0].resolve(history); await Promise.resolve(); await Promise.resolve();
  assert(find('commercial-history').textContent.includes('Período filtrado'), 'Resposta antiga não substitui o gráfico restaurado');
  all(historyRoot, n => n.className === 'commercial-client-select')[0].listeners.click({stopPropagation(){}});
  pending[1].resolve(history); await Promise.resolve(); await Promise.resolve();
  assert(find('commercial-history').textContent.includes('Últimos seis meses'));
  assert.strictEqual(all(historyRoot,n=>n.className==='commercial-history-month').length,6);
  assert(find('commercial-history-totals').textContent.includes('123,00'));
  assert(find('commercial-history-totals').textContent.includes('45,00'));
  assert.strictEqual(reset.hidden,false);
  console.log('OK: gráfico mensal, totais, seleção do cliente e descarte de respostas antigas.');
})().catch(error => { console.error(error); process.exitCode = 1; });
