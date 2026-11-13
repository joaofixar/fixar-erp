'use strict';
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const source = fs.readFileSync(__dirname + '/../dashboard.js', 'utf8');
const queries = [];
const nodes = {};
const context = {
  URLSearchParams, Promise, Array, String, Number,
  clientDetail: null, state: {},
  $: selector => nodes[selector] || (nodes[selector] = {}),
  format: value => value, escapeHtml: value => value,
  indicatorParams: () => new URLSearchParams('id=fixture&f_dataInicio=2026-10-01'),
  request: url => { queries.push(new URL(url, 'http://localhost').searchParams); return Promise.resolve({calendarioDetalhes:[],graficoDetalhes:[]}); },
  showDetailLoading() {}, stopLoadingProgress() {},
  completeLoadingProgress: () => Promise.resolve(), detailTable: () => ''
};
vm.createContext(context);
for (const [first, last] of [['  function calendarDefinition(', '  function bindCalendarDetails('], ['  function cashFlowSeriesLabel(', '  function bindCashFlowDetails(']]) {
  vm.runInContext(source.slice(source.indexOf(first), source.indexOf(last)), context);
}
(async () => {
  context.openCalendarDetailModal('agenda','2026-10-16',{calendarios:[{id:'agenda',detalhamento:{}}]});
  context.openCashFlowDetailModal('receber','2026-10-16','2026-10-17','período',{grafico:{detalhamento:{}}});
  await new Promise(resolve => setImmediate(resolve));
  assert.strictEqual(queries.length,2);
  queries.forEach(q => { assert.strictEqual(q.get('somenteDetalhamento'),'1'); assert.strictEqual(q.get('f_dataInicio'),'2026-10-01'); });
  assert.strictEqual(queries[0].get('detalheCalendarioId'),'agenda');
  assert.strictEqual(queries[1].get('detalheGraficoDataFim'),'2026-10-17');
  console.log('OK: calendário e barras solicitam somente detalhes, preservando filtros e período.');
})().catch(error => { console.error(error); process.exit(1); });
