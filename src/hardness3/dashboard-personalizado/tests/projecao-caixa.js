'use strict';

// Executa as funções reais de agrupamento e renderização, sem navegador ou rede.
var fs = require('fs');
var path = require('path');
var vm = require('vm');
var assert = require('assert');
var source = fs.readFileSync(path.join(__dirname, '..', 'dashboard.js'), 'utf8');
var start = source.indexOf('  function cashFlowDate(');
var end = source.indexOf('  function paretoChart(', start);
assert(start >= 0 && end > start);
var context = {
  state: { selectedId: 'teste', cashFlowGranularities: { teste: 'day' } },
  escapeHtml: function (value) { return String(value); },
  format: function (value) { return Number(value).toFixed(2); },
  calendarValue: function (value) { return Number(value).toFixed(2); },
  contextualHelp: function () { return ''; }
};
vm.runInNewContext(source.slice(start, end), context);
var period = { dataInicio: '2026-10-01', dataFim: '2026-10-31' };
var rows = [
  { periodo: '2026-09-30', rotulo: '30/09', serie: 'receber', valor: 900 },
  { periodo: '2026-10-01', rotulo: '01/10', serie: 'receber', valor: 100 },
  { periodo: '2026-10-30', rotulo: '30/10', serie: 'receber', valor: 50 },
  { periodo: '2026-10-30', rotulo: '30/10', serie: 'pagar', valor: 60 },
  { periodo: '2026-11-03', rotulo: '03/11', serie: 'pagar', valor: 800 }
];
['day', 'week', 'month'].forEach(function (granularity) {
  var grouped = context.aggregateCashFlowRows(rows, granularity, period);
  assert.strictEqual(grouped.reduce(function (sum, row) { return sum + row.valor; }, 0), 210);
  grouped.forEach(function (row) {
    assert(row.dataInicio >= period.dataInicio && row.dataFim <= period.dataFim);
  });
});
var weeks = context.aggregateCashFlowRows(rows, 'week', period);
assert.strictEqual(weeks[0].rotulo, '01/10–04/10');
assert.strictEqual(weeks[1].rotulo, '26/10–31/10');
var legacyWeeks = context.aggregateCashFlowRows(rows, 'week');
assert.strictEqual(legacyWeeks[0].rotulo, '28/09–04/10');
assert.strictEqual(legacyWeeks.reduce(function (sum, row) { return sum + row.valor; }, 0), 1910);

var definition = {
  limitarAoPeriodoFiltrado: true,
  periodoFiltrado: period,
  exibirSaldoProjetado: false,
  detalhamento: { habilitado: true },
  series: [{ chave: 'receber', rotulo: 'Contas a receber' }, { chave: 'pagar', rotulo: 'Contas a pagar' }]
};
['day', 'week', 'month'].forEach(function (granularity) {
  context.state.cashFlowGranularities.teste = granularity;
  var html = context.comparativeBarChart(rows, definition, 'moeda');
  assert(!html.includes('Saldo projetado'));
  assert(html.includes('cash-flow-summary--two'));
  assert(html.includes('Contas a receber<b>150.00</b>'));
  assert(html.includes('Contas a pagar<b>60.00</b>'));
  assert(!html.includes('03/11') && !html.includes('30/09'));
  var clicks = html.match(/data-cash-flow-(?:start|end)="[^"]+"/g) || [];
  assert(clicks.length > 0);
  clicks.forEach(function (attribute) {
    var date = attribute.split('"')[1];
    assert(date >= period.dataInicio && date <= period.dataFim);
  });
});
delete definition.exibirSaldoProjetado;
var legacyHtml = context.comparativeBarChart(rows, definition, 'moeda');
assert(legacyHtml.includes('Saldo projetado<b>90.00</b>'));
assert(!legacyHtml.includes('cash-flow-summary--two'));
console.log('OK: totais, cartão opcional e limites de datas/rótulos/cliques em dia, semana e mês.');
