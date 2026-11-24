'use strict';
const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const source = fs.readFileSync(path.join(__dirname, '../dashboard.js'), 'utf8');
function extract(name) {
  const start = source.indexOf('  function ' + name + '(');
  assert(start >= 0, name);
  return source.slice(start, source.indexOf('\n  function ', start + 1));
}
const context = {};
vm.createContext(context);
vm.runInContext(['escapeHtml', 'format', 'calendarDate', 'calendarKey', 'calendarValue', 'calendarColor', 'calendarBands', 'calendarRange', 'calendarMonths', 'calendarMonthly', 'calendar'].map(extract).join('\n'), context);
function range(start, end) { return { start: context.calendarDate(start), end: context.calendarDate(end) }; }
function filters(start, end) { return [{ id: 'dataInicio', valor: start }, { id: 'dataFim', valor: end }]; }
const definition = {
  titulo: 'Calendário <Faturamento>', periodo: 'periodo_filtrado', agruparMesesPorDiaSemana: true,
  campoQuantidade: 'kg', formatoQuantidade: 'quilos', casasDecimaisQuantidade: 2,
  exibirTotaisSemanais: true, exibirAcumuladoAteHoje: true, dataAtual: '2026-11-19'
};
const rows = [
  { data: '2026-07-31', valor: 999, quantidade: 999 }, // Fora do filtro.
  { data: '2026-08-03', valor: '100.25', quantidade: '10.444' }, // Segunda.
  { data: '2026-08-10 16:00:00', valor: '200.25', quantidade: '20.004' }, // Outra segunda.
  { data: '2026-08-04', valor: 50, quantidade: 5 }, // Terça.
  { data: '2026-08-08', valor: 7, quantidade: 1 }, // Sábado também compõe o total fiscal.
  { data: '2026-08-09', valor: 8, quantidade: 2 }, // Domingo.
  { data: '2026-09-01', valor: 80, quantidade: 8 },
  { data: '2026-09-02', valor: -10, quantidade: -1 },
  { data: '2026-11-19', valor: 90, quantidade: 9 },
  { data: '2026-11-20', valor: 999, quantidade: 999 }, // Depois do último dia selecionado.
  { data: null, valor: 999, quantidade: 999 }
];
const snapshot = JSON.stringify(rows);
const months = context.calendarMonths(rows, range('2026-08-01', '2026-11-19'));
assert.deepStrictEqual(Array.from(months, month => month.key), ['2026-08', '2026-09', '2026-10', '2026-11']);
assert.equal(months[0].weekdays[1].value, 300.5, 'Soma todas as segundas do mês na mesma célula.');
assert(Math.abs(months[0].weekdays[1].quantity - 30.448) < 1e-9, 'Arredonda somente na apresentação.');
assert.equal(months[0].weekdays[2].value, 50);
assert.equal(months[0].weekdays[6].value, 7);
assert.equal(months[0].weekdays[0].value, 8);
assert.equal(months[0].value, 365.5);
assert.equal(months[1].value, 70);
assert.equal(months[2].value, 0, 'Mês sem movimento ainda tem sua linha.');
assert.equal(months[3].end, '2026-11-19');
assert.equal(months[3].value, 90);
months.forEach(month => {
  assert.equal(month.weekdays.reduce((sum, day) => sum + day.value, 0), month.value);
  assert(Math.abs(month.weekdays.reduce((sum, day) => sum + day.quantity, 0) - month.quantity) < 1e-9);
});
assert.equal(months.reduce((sum, month) => sum + month.value, 0), 525.5);
assert.equal(JSON.stringify(rows), snapshot, 'Não altera os dados recebidos.');
const html = context.calendar(rows, definition, filters('2026-08-01', '2026-11-19'));
assert.equal((html.match(/<tr>/g) || []).length, 5, 'Quatro meses produzem só quatro linhas mais o cabeçalho.');
assert(html.includes('Agosto 2026') && html.includes('Novembro 2026') && html.includes('01/11 a 19/11'));
assert(html.indexOf('>Mês</th>') < html.indexOf('>Segunda</th>'));
assert(html.includes('>Sábado</th>') && html.includes('>Domingo</th>') && html.includes('>Total do mês</th>'));
assert(html.includes('30,45 kg') && !html.includes('30,448 kg'));
assert(html.includes('525,50') && html.includes('Acumulado até 19/11/2026'));
assert(html.includes('&lt;Faturamento&gt;') && !html.includes('<Faturamento>'));
assert(!html.includes('calendar-days') && !html.includes('Total da semana') && !html.includes('data-calendar-date'));
const two = context.calendarMonths(rows, range('2026-08-04', '2026-09-01'));
assert.equal(two.length, 2);
assert.equal(two[0].start, '2026-08-04');
assert.equal(two[0].value, 265.25, 'Mês inicial incompleto não inclui movimentos anteriores ao filtro.');
assert.equal(two[1].end, '2026-09-01');
const nine = context.calendarMonths([], range('2026-03-15', '2026-11-19'));
assert.equal(nine.length, 9);
assert(nine.every(month => month.value === 0 && month.quantity === 0));
const nineHtml = context.calendar([], definition, filters('2026-03-15', '2026-11-19'));
assert.equal((nineHtml.match(/<tr>/g) || []).length, 10);
const crossYear = context.calendarMonths([{ data: '2027-01-04', valor: 7, quantidade: 1 }], range('2026-12-31', '2027-01-04'));
assert.deepStrictEqual(Array.from(crossYear, month => month.key), ['2026-12', '2027-01']);
assert.equal(crossYear[1].weekdays[1].value, 7);
const crossYearHtml = context.calendar([], definition, filters('2026-12-31', '2027-01-04'));
assert(crossYearHtml.includes('Dezembro 2026') && crossYearHtml.includes('Janeiro 2027'));
const leap = context.calendarMonths([{ data: '2024-02-29', valor: 29 }], range('2024-02-01', '2024-03-01'));
assert.equal(leap[0].end, '2024-02-29');
assert.equal(leap[0].weekdays[4].value, 29);
const daily = context.calendar(rows, definition, filters('2026-08-01', '2026-08-31'));
assert(daily.includes('calendar-days') && daily.includes('Total da semana') && !daily.includes('calendar-months'));
const legacy = context.calendar(rows, Object.assign({}, definition, { agruparMesesPorDiaSemana: false }), filters('2026-08-01', '2026-11-19'));
assert(legacy.includes('calendar-days') && !legacy.includes('calendar-months'), 'Os demais indicadores conservam a apresentação original.');
const moneyOnly = context.calendar([], Object.assign({}, definition, { campoQuantidade: '' }), filters('2026-08-01', '2026-09-01'));
assert(moneyOnly.includes('calendar-months') && !moneyOnly.includes(' kg'));
const defaultQuantity = context.calendar(rows, Object.assign({}, definition, { casasDecimaisQuantidade: undefined }), filters('2026-08-01', '2026-11-19'));
assert(defaultQuantity.includes('30,448 kg'), 'Precisão dos demais calendários continua configurável.');
console.log('OK: 2, 4 e 9 meses, soma por dia da semana, totais fiscais, meses vazios e parciais, virada de ano, bissexto, kg em duas casas e calendário diário preservado.');
