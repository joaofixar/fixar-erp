'use strict';
// Renderizador real, dados fictícios, sem navegador, rede ou banco de cliente.
const assert = require('assert'), fs = require('fs'), vm = require('vm');
const source = fs.readFileSync(__dirname + '/../dashboard.js', 'utf8');
function extract(name) {
  const start = source.indexOf('  function ' + name + '(');
  assert(start >= 0, name);
  const end = source.indexOf('\n  function ', start + 3);
  return source.slice(start, end < 0 ? source.length : end);
}
const nodes = {};
function node(selector) {
  if (!nodes[selector]) nodes[selector] = {innerHTML:'',hidden:false,classList:{remove(){},toggle(){}},scrollIntoView(){}};
  return nodes[selector];
}
const noop = () => {};
const context = {Intl, URLSearchParams, window:{},
  state:{selectedId:'compacto',expandedGroups:{},listExpanded:false,listVisibleCount:0},
  $:selector => node(selector),
  renderIndicatorHeader:noop,renderFilters:noop,setExportAvailability:noop,
  bindParetoDetails:noop,bindCashFlowDetails:noop,calendars:() => '',positionCalendars:noop,
  bindCalendarDetails:noop,geographicMap:() => '',bindMapTooltip:noop,
  bindSectionChartDetails:noop,bindSectionChartPagination:noop,bindConferenceDetails:noop,
  bindParetoList:noop,bindListPagination:noop,bindListDetails:noop};
vm.createContext(context);
vm.runInContext(['escapeHtml','format','listCell','listPageSize','list','composition','chart',
  'groupedChannelCharts','indicatorSections','indicatorSectionChart','conferenceAttributes',
  'renderResult','detailTable'].map(extract).join('\n'), context);
const columns = [{campo:'documento',titulo:'Título'},{campo:'valor',titulo:'Valor',formato:'moeda'}];
const data = {
  indicador:{id:'compacto',titulo:'Financeiro',apresentacao:{compacta:true},filtros:[],
    card:{titulo:'Em aberto',descricao:'Explicação do cartão',conferencia:'abertos'},
    composicao:{habilitada:false},grafico:{habilitado:false},graficosPorGrupo:{habilitado:false},
    listagem:{titulo:'Títulos',colunas:columns}},
  card:{valor:900,quantidade:1,formato:'moeda'},
  resumos:[{titulo:'Vencido',valor:100,formato:'moeda',conferencia:'vencidos',descricao:'Explicação de vencidos'},
    {titulo:'A vencer',valor:800,formato:'moeda',conferencia:'vencer'},
    {titulo:'Pago',valor:50,formato:'moeda',conferencia:'pagos'}],
  secoes:[{titulo:'Por empresa',subtitulo:'Explicação do bloco',
    conferencia:{id:'empresa',campoGrupo:'documento'},colunas:columns,
    rodapeTotais:{habilitado:true,titulo:'Total',campos:['valor']},linhas:[{documento:'Matriz',valor:900}]}],
  listagem:[{documento:'001',valor:900}],updatedAt:'Exemplo'
};
context.renderResult(data);
const compact = node('#indicatorSummary').innerHTML;
assert.strictEqual((compact.match(/class="indicator-kpi"/g)||[]).length,4);
assert(!compact.includes('<p>') && !compact.includes('<small') && !compact.includes('Explicação'));
assert(compact.includes('data-conference="abertos"') && compact.includes('data-conference="pagos"'));
assert(compact.includes('role="button"') && compact.includes('tabindex="0"'),'Valores continuam acessíveis por teclado.');
const section = node('#indicatorPrimaryList').innerHTML;
assert(section.includes('indicator-sections--compact') && section.includes('data-conference="empresa"'));
assert(!section.includes('Explicação') && !section.includes('conference-hint'));
assert(node('#indicatorList').innerHTML.includes('001'),'As tabelas não podem sobrescrever a lista.');
assert.strictEqual(node('#indicatorChart').innerHTML,'');

// A mesma configuração sem a opção mantém os textos e as instruções anteriores.
delete data.indicador.apresentacao;
context.renderResult(data);
assert(node('#indicatorSummary').innerHTML.includes('Explicação do cartão'));
assert(node('#indicatorSummary').innerHTML.includes('Ver registros considerados'));
assert(node('#indicatorPrimaryList').innerHTML.includes('Explicação do bloco'));
assert(node('#indicatorPrimaryList').innerHTML.includes('Clique em uma linha'));
assert(!node('#indicatorPrimaryList').innerHTML.includes('indicator-sections--compact'));

const detail = {modal:true,apresentacao:'ficha',colunas:[
  {campo:'documento',titulo:'Título'},{campo:'valor',titulo:'Pago',formato:'moeda'},
  {campo:'data',titulo:'Vencimento',formato:'data'},{campo:'observacao',titulo:'Observação'}]};
const row = {documento:'<img src=x>',valor:0,data:'2026-11-04',observacao:null};
const record = context.detailTable([row],detail);
assert(record.includes('class="detail-record"') && !record.includes('<table>'));
assert(record.includes('&lt;img src=x&gt;') && !record.includes('<img'));
assert(record.includes(context.format(0,'moeda')) && record.includes('04/11/2026'));
assert(record.includes('<dd>—</dd>'));
assert(context.detailTable([row,row],detail).includes('<table>'),'Mais de um título permanece visível.');
assert(context.detailTable([row],{colunas:detail.colunas}).includes('<table>'),'Detalhe legado preservado.');
console.log('OK: quatro cartões sem textos, blocos compactos clicáveis, lista preservada, ficha segura e apresentação anterior mantida.');
