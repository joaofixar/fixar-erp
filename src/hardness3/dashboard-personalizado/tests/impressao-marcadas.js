'use strict';
const assert = require('assert'), fs = require('fs'), vm = require('vm');
const source = fs.readFileSync(__dirname + '/../dashboard.js', 'utf8');
const names = ['escapeHtml', 'format', 'listCell', 'listPageSize', 'expandListPage', 'list', 'listPrintParams', 'bindListPrintSelection'];
const functions = names.map(name => {
  const start = source.indexOf('  function ' + name + '(');
  assert(start >= 0, name);
  return source.slice(start, source.indexOf('\n  function ', start + 3));
}).join('\n');
const context = { Intl, URLSearchParams, listPrintSelection: {}, state: { expandedGroups: {}, listExpanded: false,
  loading: false, exportParams: 'id=comissao&tab=representantes&f_dataInicio=2026-10-01&f_dataFim=2026-10-31&detalheGrupo=antigo&cargaToken=antigo&somenteDetalhamento=1' }, opens: [] };
context.window = { open(...args) { context.opens.push(args); } };
vm.createContext(context); vm.runInContext(functions, context);
const definition = { titulo: 'Representantes', colunas: [{ campo: 'id', titulo: 'Código' }, { campo: 'descricao', titulo: 'Representante' }, { campo: 'tipo', titulo: 'Tipo' }],
  detalhamento: { campoGrupo: 'id', modal: true, colunas: [{ campo: 'documento' }] },
  impressaoMarcadas: { habilitada: true, campoChave: 'id', campoNome: 'descricao', textoBotao: 'Imprimir marcadas' } };
const rows = Array.from({ length: 12 }, (_, n) => ({ id: 101 + n, descricao: 'Representante ' + n, tipo: 'E' }));
let html = context.list(rows, definition, {});
assert.equal((html.match(/data-list-print-row=/g) || []).length, 10);
assert(html.includes('data-list-print disabled') && html.includes('data-list-print-all'));
const header = html.match(/<thead>(.*?)<\/thead>/)[1];
assert(header.indexOf('Código') < header.indexOf('data-list-print-all') && header.indexOf('data-list-print-all') < header.indexOf('Representante'), 'Checkbox deve ficar imediatamente antes do nome, depois do código.');
const malicious = context.list([{ id: 101, descricao: '<img src=x onerror=alert(1)>', tipo: 'E' }], definition, {});
assert(!malicious.includes('<img src=x') && malicious.includes('&lt;img'), 'Nome do cadastro é escapado também no aria-label.');
const legacy = Object.assign({}, definition); delete legacy.impressaoMarcadas;
assert(!context.list(rows, legacy, {}).includes('data-list-print'), 'Outras listagens permanecem sem seleção.');

function element(attributes = {}) {
  return { dataset: {}, checked: false, handlers: {}, getAttribute(name) { return attributes[name]; }, addEventListener(name, fn) { this.handlers[name] = fn; } };
}
let button, boxes, all, count;
function mount(ids) {
  button = element(); all = element(); count = { textContent: '' };
  boxes = ids.map(id => element({ 'data-list-print-row': String(id) }));
  const card = { querySelectorAll() { return boxes; }, querySelector(selector) { return selector === '[data-list-print-all]' ? all : count; } };
  button.closest = () => card;
  context.document = { querySelector: () => button };
  context.bindListPrintSelection({ listagem: definition });
}
mount([101, 102]);
assert(button.disabled && !all.indeterminate);
let stopped = false;
boxes[0].handlers.click({ stopPropagation() { stopped = true; } });
assert(stopped, 'Marcar não deve abrir o detalhamento da linha.');
boxes[0].checked = true; boxes[0].handlers.change();
assert(context.listPrintSelection['101'] && !button.disabled && all.indeterminate && count.textContent === '1 selecionado');
context.expandListPage(rows, definition);
html = context.list(rows, definition, {});
assert.equal((html.match(/data-list-print-row=/g) || []).length, 12);
assert(html.includes('data-list-print-row="101" aria-label="Selecionar Representante 0" checked'), 'Seleção permanece após Mostrar mais.');
mount([101, 102, 103]);
assert(boxes[0].checked && all.indeterminate);
all.checked = true; all.handlers.change();
assert(all.checked && !all.indeterminate && Object.keys(context.listPrintSelection).length === 3);
context.listPrintSelection['154'] = true;
all.checked = false; all.handlers.change();
assert(Object.keys(context.listPrintSelection).join(',') === '154', 'Desmarcar exibidos não remove um marcado fora da página atual.');
boxes[0].checked = true; boxes[0].handlers.change();
button.handlers.click();
assert.equal(context.opens.length, 1);
const [url, target, features] = context.opens[0], params = new URLSearchParams(url.split('?')[1]);
assert.equal(target, '_blank'); assert.equal(features, 'noopener');
assert.equal(params.get('id'), 'comissao'); assert.equal(params.get('tab'), 'representantes');
assert.equal(params.get('f_dataInicio'), '2026-10-01'); assert.equal(params.get('f_dataFim'), '2026-10-31');
assert.equal(params.get('escopo'), 'marcadas'); assert.equal(params.get('formato'), 'pdf');
assert.deepEqual(params.get('selecionados').split(',').sort(), ['101', '154']);
assert(!params.has('detalheGrupo') && !params.has('cargaToken') && !params.has('somenteDetalhamento'));
assert.equal(context.listPrintParams({}, ''), null);
assert.equal(context.listPrintParams({ '101 OR 1=1': true }, ''), null);
context.state.loading = true; button.handlers.click(); assert.equal(context.opens.length, 1);
console.log('OK: checkbox antes do nome, seleção ao expandir, marcar exibidos, clique separado do detalhe, período aplicado e PDF em nova aba.');
