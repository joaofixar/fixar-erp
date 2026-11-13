'use strict';
// Fixtures locais do motor; não dependem de JSON ou dados de cliente.
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
const children = ['summary', 'indicatorPrimaryList', 'chart', 'indicatorCalendar', 'indicatorMap', 'list'];
const nodes = {};
const parent = {
  insertBefore(node, anchor) {
    assert(children.includes(node.id) && children.includes(anchor.id));
    children.splice(children.indexOf(node.id), 1);
    children.splice(children.indexOf(anchor.id), 0, node.id);
  }
};
children.forEach(id => { nodes['#' + id] = { id, parentNode: parent }; });
const context = { Intl, $: selector => nodes[selector] };
vm.createContext(context);
vm.runInContext(['escapeHtml', 'format', 'listCell', 'positionCalendars'].map(extract).join('\n'), context);
const column = { campo: 'peso', formato: 'numero', textoNulo: 'Não informado' };
assert.equal(context.listCell({ peso: null }, column), '<td>Não informado</td>');
assert.equal(context.listCell({}, column), '<td>Não informado</td>');
assert.equal(context.listCell({ peso: 0 }, column), '<td>0</td>');
assert.equal(context.listCell({ peso: 10880 }, column), '<td>10.880</td>');
assert.equal(context.listCell({ peso: null }, { campo: 'peso', formato: 'numero' }), '<td>0</td>', 'Preserva a apresentação anterior sem textoNulo.');
assert.equal(context.listCell({ peso: null }, { campo: 'peso', textoNulo: '<img src=x>' }), '<td>&lt;img src=x&gt;</td>');

context.positionCalendars([{ posicao: 'apos_resumo' }]);
assert.equal(children.indexOf('indicatorCalendar') + 1, children.indexOf('indicatorPrimaryList'));
context.positionCalendars([]);
assert.equal(children.indexOf('indicatorCalendar') + 1, children.indexOf('indicatorMap'), 'Restaura a posição ao trocar de indicador.');
context.positionCalendars([{ posicao: 'apos_resumo' }, { posicao: 'padrao' }]);
assert.equal(children.indexOf('indicatorCalendar') + 1, children.indexOf('indicatorPrimaryList'));
context.positionCalendars([null, { habilitado: false, posicao: 'apos_resumo' }]);
assert.equal(children.indexOf('indicatorCalendar') + 1, children.indexOf('indicatorMap'), 'Calendários desabilitados não mudam a posição.');
context.positionCalendars(null);
assert.equal(children.indexOf('indicatorCalendar') + 1, children.indexOf('indicatorMap'));
assert.equal(new Set(children).size, 6, 'Move os elementos sem duplicar os contêineres.');
console.log('OK: nulos distintos de zero, HTML escapado, comportamento legado e posição/restauração dos calendários.');
