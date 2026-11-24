'use strict';
const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const trend = require('../tendencia-series.js');
const source = fs.readFileSync(path.join(__dirname, '../dashboard.js'), 'utf8');
const start = source.indexOf('  function format(');
const context = {};
vm.createContext(context);
vm.runInContext(source.slice(start, source.indexOf('\n  function ', start + 1)), context);
const format = context.format;
const definition = {
  titulo: 'Faturamento', subtitulo: 'Segunda a sexta-feira · período atual e mês anterior',
  campoQuantidade: 'kg', formatoQuantidade: 'toneladas', ocultarFinsDeSemana: true,
  seriesSelecionaveis: { habilitado: true, series: [
    { id: 'a', titulo: 'Aço', cor: '#123456' }, { id: 'b', titulo: 'Serviço', cor: '#654321' }
  ] }
};
function row(periodo, valor, quantidade) {
  return { periodo, valor, quantidade, series: { a: { valor: valor / 2, quantidade: quantidade / 2 }, b: { valor: valor / 2, quantidade: quantidade / 2 } } };
}
const rows = [row('2026-11-03', 2000, 4)];
const comparison = {
  tipo: 'ano', ano: 2023, rotulo: '2023', agrupamento: 'dia',
  atual: { inicio: '2026-11-01', fim: '2026-11-19' }, anterior: { inicio: '2023-11-01', fim: '2023-11-19' },
  linhas: [row('2023-11-01', 1000, 2), row('2023-11-04', 400, 1)]
};
const snapshot = JSON.stringify(rows);
const data = trend.model(rows, definition, comparison);
assert.equal(data.comparisonLabel, '2023');
assert.equal(data.previous.dates[0], '2023-11-01');
assert(data.previous.dates.every(key => ![0, 6].includes(new Date(key + 'T12:00:00Z').getUTCDay())));
assert(data.current.dates.every(key => ![0, 6].includes(new Date(key + 'T12:00:00Z').getUTCDay())));
assert.equal(data.previous.totals.valor, 1400, 'Acumulado fiscal inclui o sábado omitido apenas da linha.');
assert.equal(data.previous.lines[0].totals.valor, 700);
assert.equal(data.previous.lines[0].points.reduce((sum, point) => sum + point.valor, 0), 500);
for (const metric of ['valor', 'quantidade']) {
  const html = trend.chart(data, definition, ['a', 'b'], metric, format);
  assert(html.includes('2023 · linha tracejada'));
  assert(html.includes('01/11/2023 a 19/11/2023') && html.includes('01/11/2026 a 19/11/2026'));
  assert(html.includes('Aço · 2023') && html.includes('Serviço · 2023'));
  assert(html.includes('Data de 2023') && html.includes('data-trend-previous-date="2023-11-01"'));
  assert(!html.includes('Mês anterior') && !html.includes('mês anterior'));
  assert.equal((html.match(/data-trend-series=/g) || []).length, 4);
  const detail = trend.pointDetail(data, definition, ['b'], metric, 0, format);
  assert(detail.includes('2023') && detail.includes('01/11/2023') && !detail.includes('Aço'));
}
const money = trend.chart(data, definition, ['a'], 'valor', format);
const summary = money.slice(money.indexOf('data-trend-period-summary'), money.indexOf('<details class="trend-values"'));
assert(summary.includes('700,00') && summary.includes('1.000,00') && !summary.includes('Serviço'));
const noMovements = trend.model(rows, definition, Object.assign({}, comparison, { ano: 2025, rotulo: '2025', anterior: { inicio: '2025-11-01', fim: '2025-11-19' }, linhas: [] }));
assert.equal(noMovements.previous.totals.valor, 0);
assert(trend.chart(noMovements, definition, ['a'], 'valor', format).includes('2025 · linha tracejada'));
const monthly = trend.model([row('2026-08', 20, 2)], definition, Object.assign({}, comparison, { agrupamento: 'mes', atual: { inicio: '2026-08-15', fim: '2026-11-19' }, anterior: { inicio: '2023-08-15', fim: '2023-11-19' }, linhas: [row('2023-08', 10, 1)] }));
assert.deepStrictEqual(monthly.previous.dates, ['2023-08', '2023-09', '2023-10', '2023-11']);
assert(trend.chart(monthly, definition, ['a'], 'valor', format).includes('15/08/2023 a 19/11/2023'));
assert.equal(JSON.stringify(rows), snapshot);
const legacy = trend.model(rows, definition, Object.assign({}, comparison, { ano: undefined, rotulo: undefined }));
assert(trend.chart(legacy, definition, ['a'], 'valor', format).includes('Mês anterior · linha tracejada'));
console.log('OK: ano escolhido nos dois gráficos, datas e legendas, dias de semana, acumulados fiscais, seleção de atividades, múltiplos meses e legado mensal.');
