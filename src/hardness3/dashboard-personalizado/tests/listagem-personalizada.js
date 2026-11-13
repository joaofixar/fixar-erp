'use strict';
const fs = require('fs'), vm = require('vm'), assert = require('assert');
const source = fs.readFileSync(__dirname + '/../dashboard.js', 'utf8');
const names = ['escapeHtml', 'format', 'localDate', 'defaultValue', 'calendarDate', 'calendarValue', 'groupedChannelCharts', 'listPageSize', 'expandListPage', 'list', 'listCell', 'bindListPagination'];
const functions = names.map(name => {
  const start = source.indexOf('  function ' + name + '('), end = source.indexOf('\n  function ', start + 3);
  assert(start >= 0, 'Função ausente: ' + name);
  return source.slice(start, end < 0 ? source.length : end);
}).join('\n');
const rows = Array.from({ length: 125 }, (_, id) => ({ id, descricao: 'Registro ' + id, valor: id }));
const definition = {
  titulo: 'Tabela', linhasIniciais: 50, incremento: 50, cabecalhoFixo: true,
  rotuloQuantidadePlural: 'registros', paginacao: { habilitada: true },
  colunas: [{ campo: 'descricao', titulo: 'Registro' }, { campo: 'valor', titulo: 'Valor', formato: 'moeda' }],
  rodapeTotais: { habilitado: true, campos: ['valor'] }
};
const pagination = offset => ({ total: 125, offset, quantidade: Math.min(50, 125 - offset), temMais: offset < 100, totais: { valor: 7750 } });
let button, scroll, data, indicator;
const target = { querySelector: () => scroll };
Object.defineProperty(target, 'innerHTML', {
  get() { return this.html; },
  set(html) {
    this.html = html; scroll = { scrollTop: 0, scrollLeft: 0 };
    button = html.includes('id="showMoreResults"') ? { disabled: false, textContent: 'Mostrar mais', addEventListener(type, handler) { this.click = handler; } } : null;
  }
});
const context = {
  Intl, URLSearchParams, state: { listExpanded: false, listVisibleCount: 0, expandedGroups: {}, selectedId: 'teste', requestId: 1,
    exportParams: 'id=teste&f_ano=2026&f_cliente=Cliente&f_uf=PR&carregarProgressivo=1&cargaToken=antigo' },
  $: () => button, bindParetoList() {}, bindListDetails() {}, toasts: []
};
context.showToast = message => context.toasts.push(message);
vm.createContext(context); vm.runInContext(functions, context);
const countRows = html => (html.match(/<tbody>([\s\S]*?)<\/tbody>/)[1].match(/<tr/g) || []).length;
function initial() {
  data = { listagem: rows.slice(0, 50), listagemPaginacao: pagination(0) };
  indicator = { listagem: Object.assign({}, definition, { paginacaoAtual: data.listagemPaginacao }) };
  context.state.listExpanded = false; context.state.listVisibleCount = 0;
  target.innerHTML = context.list(data.listagem, indicator.listagem, {});
  context.bindListPagination(data, indicator, target);
}
const flush = () => new Promise(resolve => setImmediate(resolve));
(async () => {
  assert.equal(context.defaultValue({ padrao: 'ano_atual' }), String(new Date().getFullYear()));
  const groups = [0, 1, 2].map(id => ({ grupo: 'Grupo <' + id + '>', total: 100, serie: [
    { data: '2026-01-01', valor: 100 }, { data: '2026-12-31', valor: 0 }
  ] }));
  const graph = context.groupedChannelCharts(groups, { compacto: true, colunas: 3 }, 'moeda', []);
  assert.equal((graph.match(/class="channel-chart-card"/g) || []).length, 3);
  assert.equal((graph.match(/<rect /g) || []).length, 36);
  assert(graph.includes('group-chart-grid--3 group-chart-grid--compact') && graph.includes('0 0 380 220'));
  assert(graph.includes('title="Grupo &lt;0&gt;"') && !graph.includes('<0>'));
  const legacyGraph = context.groupedChannelCharts(groups, {}, 'moeda', []);
  assert(legacyGraph.includes('0 0 716 330') && !legacyGraph.includes('group-chart-grid--'));
  const legacy = { colunas: definition.colunas };
  assert.equal(countRows(context.list(rows, legacy, {})), 10);
  context.expandListPage(rows, legacy);
  assert.equal(countRows(context.list(rows, legacy, {})), 125);
  context.state.listExpanded = false;
  context.expandListPage(rows, definition);
  assert.equal(countRows(context.list(rows, definition, {})), 100, 'Expansão local também admite incrementos configurados.');

  const urls = [];
  context.request = async url => {
    urls.push(url);
    const params = new URLSearchParams(url.split('?')[1]), offset = Number(params.get('listaOffset'));
    assert.equal(params.get('f_ano'), '2026'); assert.equal(params.get('f_cliente'), 'Cliente'); assert.equal(params.get('f_uf'), 'PR');
    assert.equal(params.get('somenteListagem'), '1'); assert(!params.has('carregarProgressivo') && !params.has('cargaToken'));
    return { listagem: rows.slice(offset, offset + 50), listagemPaginacao: pagination(offset) };
  };
  initial();
  assert.equal(countRows(target.innerHTML), 50);
  assert(target.innerHTML.includes('Mostrando 50 de 125 registros') && target.innerHTML.includes('table-scroll-sticky'));
  assert(target.innerHTML.includes(context.format(7750, 'moeda')), 'Rodapé usa total global e não apenas os primeiros 50.');
  scroll.scrollTop = 180; scroll.scrollLeft = 90;
  button.click(); assert(button.disabled); await flush();
  assert.equal(countRows(target.innerHTML), 100); assert.equal(scroll.scrollTop, 180); assert.equal(scroll.scrollLeft, 90);
  assert(target.innerHTML.includes('Mostrar mais 25'));
  button.click(); await flush();
  assert.equal(countRows(target.innerHTML), 125); assert.equal(button, null);
  assert.equal(new Set(data.listagem.map(row => row.id)).size, 125);
  assert(urls[0].includes('listaOffset=50') && urls[1].includes('listaOffset=100'));
  initial(); context.request = () => Promise.reject(new Error('Falha simulada'));
  button.click(); await flush();
  assert.equal(data.listagem.length, 50); assert(!button.disabled); assert.equal(context.toasts.pop(), 'Falha simulada');
  initial(); let resolvePage;
  context.request = () => new Promise(resolve => { resolvePage = resolve; });
  button.click(); context.state.requestId++;
  resolvePage({ listagem: rows.slice(50, 100), listagemPaginacao: pagination(50) }); await flush();
  assert.equal(data.listagem.length, 50, 'Resposta anterior à troca de filtros deve ser descartada.');
  console.log('OK: três gráficos compactos, 12 meses, paginação 50/100/125, filtros, totais, rolagem, erros e comportamento legado.');
})().catch(error => { console.error(error); process.exitCode = 1; });
