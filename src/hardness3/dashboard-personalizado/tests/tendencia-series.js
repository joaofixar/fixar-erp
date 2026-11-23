'use strict';
const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const trend = require('../tendencia-series.js');
const source = fs.readFileSync(path.join(__dirname, '../dashboard.js'), 'utf8');
function extract(name) {
  const start = source.indexOf('  function ' + name + '(');
  assert(start >= 0, name);
  return source.slice(start, source.indexOf('\n  function ', start + 1));
}
const context = {};
vm.createContext(context);
vm.runInContext(['escapeHtml', 'format', 'calendarDate', 'calendarKey', 'calendarValue', 'calendarColor', 'calendarBands', 'calendarRange', 'calendar'].map(extract).join('\n'), context);
const format = context.format;
const definition = {
  titulo: '<Receita>', campoQuantidade: 'toneladas', ocultarFinsDeSemana: true,
  seriesSelecionaveis: { habilitado: true, series: [
    { id: 'a', titulo: '<Grupo A>', cor: '#123456' },
    { id: 'b', titulo: 'Grupo B', cor: '#654321' }
  ] }
};
function row(periodo, valor, quantidade) {
  return { periodo, valor, quantidade, series: { a: { valor: valor / 2, quantidade: quantidade / 2 }, b: { valor: 1, quantidade: 0.1 } } };
}
const rows = [row('2026-11-03', 2000, 4), row('2026-11-07', 400, 2)];
const comparison = { atual: { inicio: '2026-11-01', fim: '2026-11-08' }, anterior: { inicio: '2026-10-01', fim: '2026-10-08' }, agrupamento: 'dia', linhas: [row('2026-10-01', 1000, 3)] };
const data = trend.model(rows, definition, comparison);
assert.deepStrictEqual(data.current.dates, ['2026-11-02', '2026-11-03', '2026-11-04', '2026-11-05', '2026-11-06']);
assert.equal(data.previous.dates.length, 6, 'O anterior conserva seu próprio calendário de segunda a sexta.');
assert.equal(data.previous.lines[0].points[0].valor, 500);
assert.equal(data.current.lines[0].points[1].valor, 1000);
assert.equal(data.current.totals.valor, 2400, 'Ocultar o fim de semana não altera o total fiscal.');
assert.equal(data.current.totals.quantidade, 6);
assert.deepStrictEqual(data.current.lines[0].totals, { valor: 1200, quantidade: 3 }, 'Acumulado por atividade inclui emissões no sábado ocultado do gráfico.');
assert.deepStrictEqual(data.previous.lines[0].totals, { valor: 500, quantidade: 1.5 });
assert.equal(data.current.lines[0].points[1].valor, 1000, 'A linha continua diária, sem transformar os pontos em acumulados.');
const money = trend.chart(data, definition, ['a', 'b'], 'valor', format);
assert.equal((money.match(/data-trend-series=/g) || []).length, 4);
assert(money.includes('stroke-dasharray') && !money.includes('º DU'));
assert(money.includes('data-trend-date="2026-11-02"') && money.includes('data-trend-previous-date="2026-10-01"'));
assert(money.includes('data-trend-previous-date="2026-10-08"') && !money.includes('data-trend-date="2026-11-07"'));
assert(money.includes('Atual · linha contínua') && money.includes('Mês anterior · linha tracejada'));
assert(money.includes('01/11/2026 a 08/11/2026') && money.includes('01/10/2026 a 08/10/2026'));
assert(money.includes('Data atual') && money.includes('Data do mês anterior'));
assert(money.includes('Total da visão') && money.includes('2.400,00'));
assert(money.includes('Acumulado dos períodos') && money.includes('data-trend-period-summary'));
assert(money.includes('1.200,00') && money.includes('500,00'));
assert(money.includes('&lt;Grupo A&gt;') && !money.includes('<Grupo A>') && !money.includes('<Receita>'));
const quantity = trend.chart(data, definition, ['b'], 'quantidade', format);
assert.equal((quantity.match(/data-trend-series=/g) || []).length, 2);
assert(quantity.includes('6,000 t') && !quantity.includes('NaN') && !quantity.includes('Infinity'));
const quantitySummary = quantity.slice(quantity.indexOf('data-trend-period-summary'), quantity.indexOf('<details class="trend-values"'));
assert(quantitySummary.includes('0,200 t') && quantitySummary.includes('0,100 t'));
assert(quantitySummary.includes('Grupo B') && !quantitySummary.includes('Grupo A'), 'Acumulados seguem as atividades marcadas.');
const detail = trend.pointDetail(data, definition, ['a'], 'valor', 0, format);
assert(detail.includes('02/11/2026') && detail.includes('01/10/2026') && detail.includes('500,00'));
assert(detail.includes('&lt;Grupo A&gt;') && !detail.includes('Grupo B'));
const lastDetail = trend.pointDetail(data, definition, ['a'], 'quantidade', 5, format);
assert(lastDetail.includes('Sem data correspondente') && lastDetail.includes('08/10/2026') && lastDetail.includes('—'));
assert(trend.chart(data, definition, [], 'valor', format).includes('Marque uma ou mais opções'));
assert(!trend.chart(data, definition, [], 'valor', format).includes('<svg'));
assert.throws(() => trend.model([{ periodo: '2026-11-03', valor: 1 }], definition, comparison), /incompletas/);
const monthly = trend.model([row('2026-11', 20, 2)], definition, Object.assign({}, comparison, { agrupamento: 'mes', atual: { inicio: '2026-11-01', fim: '2026-12-01' }, linhas: [] }));
assert.deepStrictEqual(monthly.current.dates, ['2026-11', '2026-12']);
assert(!monthly.weekdays);
const daily = trend.model(rows, Object.assign({}, definition, { ocultarFinsDeSemana: false }), comparison);
assert.equal(daily.current.dates.length, 8);
const noComparison = trend.model(rows, Object.assign({}, definition, { periodoFiltrado: { dataInicio: '2026-11-01', dataFim: '2026-11-08' } }), null);
const currentOnly = trend.chart(noComparison, definition, ['a'], 'valor', format);
assert(!currentOnly.includes('Data do mês anterior') && !currentOnly.includes('data-trend-previous-date='));
assert(!trend.pointDetail(noComparison, definition, ['a'], 'valor', 0, format).includes('Mês anterior'));
const zeroComparison = Object.assign({}, comparison, { linhas: [] });
const zeroData = trend.model(rows, definition, zeroComparison);
assert.deepStrictEqual(zeroData.previous.lines[0].totals, { valor: 0, quantidade: 0 }, 'Período sem movimentos mantém total zero real.');
const weekendData = trend.model(rows, definition, Object.assign({}, comparison, { linhas: comparison.linhas.concat([row('2026-10-03', 400, 2)]) }));
assert.equal(weekendData.previous.lines[0].totals.valor, 700, 'O acumulado anterior também conserva emissões do fim de semana.');
assert.equal(weekendData.previous.lines[0].points.reduce((sum, p) => sum + p.valor, 0), 500, 'O gráfico anterior continua sem sábado.');
// Precisão só na apresentação: o total arredonda a soma, não soma os valores já arredondados.
const calendarRows = [{ data: '2026-11-03', valor: 10, quantidade: 377871.444 }, { data: '2026-11-04', valor: 10, quantidade: 0.004 }];
const calendarDefinition = { campoQuantidade: 'kg', formatoQuantidade: 'quilos', casasDecimaisQuantidade: 2, exibirAcumuladoAteHoje: true, exibirTotaisSemanais: true, dataAtual: '2026-11-19' };
const html = context.calendar(calendarRows, calendarDefinition, []);
assert(html.includes('377.871,44 kg') && html.includes('377.871,45 kg'));
assert.equal((html.match(/377\.871,45 kg/g) || []).length, 2, 'Acumulado e semana arredondam a soma original.');
assert(!html.includes('377.871,444 kg'));
assert.equal(calendarRows[0].quantidade, 377871.444);
assert.equal(format(1.2345, 'quilos'), '1,235 kg', 'Os demais indicadores conservam três casas.');
assert.equal(format(1.2, 'quilos', 2), '1,20 kg');
assert.equal(format(1.2, 'toneladas'), '1,200 t');
console.log('OK: datas reais dos dois períodos, conferência de valores, seleção, fins de semana, totais fiscais, escalas, HTML seguro e kg em duas casas sem alterar somas.');
