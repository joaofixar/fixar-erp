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
vm.runInContext(['escapeHtml', 'format', 'indicatorSectionChart'].map(extract).join('\n'), context);
const draw = context.indicatorSectionChart;
const definition = { habilitado: true, titulo: '<Estoque>', subtitulo: '<img src=x>', fonteSecao: 'estoque', campoRotulo: 'acabamento', campoValor: 'kg', formato: 'numero', unidade: 'kg' };
const rows = [
  { acabamento: 'PZ', kg: '1000' }, { acabamento: 'PZ', kg: '250' },
  { acabamento: '<img src=x onerror=alert(1)>', kg: 500 },
  { acabamento: '__proto__', kg: 100 }, { acabamento: 'Zero', kg: 0 },
  { acabamento: 'Negativo', kg: -250 }, { acabamento: 'Ausente', kg: null },
  { acabamento: 'Inválido', kg: 'não informado' }, { acabamento: 'Vazio', kg: '' },
  { acabamento: 'Infinito', kg: Infinity },
];
const html = draw(definition, [{ id: 'estoque', linhas: rows }]);
assert(html.includes('1.250 kg') && html.includes('-250 kg') && html.includes('0 kg'));
assert.equal((html.match(/<li /g) || []).length, 5);
assert(!html.includes('<img') && html.includes('&lt;Estoque&gt;') && html.includes('__proto__'));
assert(!html.includes('NaN') && !html.includes('Infinity') && !html.includes('Ausente'));
assert(html.indexOf('PZ') < html.indexOf('Negativo'));
const bars = /style="left:([\d.]+)%;width:([\d.]+)%"/g;
let match;
while ((match = bars.exec(html))) {
  const left = Number(match[1]), width = Number(match[2]);
  assert(left >= 0 && width >= 0 && left + width <= 100.001);
}
assert(html.includes('has-negative') && html.includes('class="negative"'));
assert(draw(definition, [{ id: 'estoque', linhas: [] }]).includes('Não há valores'));
assert(draw(definition, []).includes('Dados do gráfico indisponíveis'));
assert.equal(draw({ habilitado: false }, []), '');
assert.equal(draw(undefined, []), '');
assert(!draw(definition, [{ id: 'estoque', linhas: [{ acabamento: 'Zero', kg: 0 }] }]).includes('NaN'));
console.log('OK: valores agrupados, zeros, negativos, ausências, ordenação, escala, HTML seguro e configuração opcional.');
