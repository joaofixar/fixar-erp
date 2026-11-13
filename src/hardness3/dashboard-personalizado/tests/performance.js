'use strict';
const fs = require('fs');
const assert = require('assert');
const source = fs.readFileSync(__dirname + '/../dashboard.js', 'utf8');
const start = source.indexOf('  function refreshClientDetail(');
const end = source.indexOf('\n  function titleBulkToolbar(', start);
let clientDetail, captured, rendered = 0;
const nodes = {
  '#indicatorDetailModal': { hidden: false },
  '#indicatorDetailContent': { innerHTML: '' },
  '#exportIndicatorDetailPdf': { disabled: true }
};
function $(selector) { return nodes[selector]; }
const state = {};
const data = { indicador: {listagem: {detalhamento: {colunas: []}}}, listagemDetalhes: { A: [{ documento: '1' }] } };
function request(url) { captured = new URL(url, 'http://localhost').searchParams; return Promise.resolve(data); }
function completeLoadingProgress() { return Promise.resolve(); }
function renderResult() { rendered++; }
function titleBulkToolbar() { return ''; }
function detailTable(rows) { return rows.length + ' registros'; }
function bindTitleBulk() {}
function stopLoadingProgress() {}
function escapeHtml(text) { return text; }
eval(source.slice(start, end) + '\nglobal.refreshPerfDetail = refreshClientDetail;');
(async () => {
  const context = { group: 'A', params: 'action=indicator&detalheGrupo=A&f_empresa=2', requestId: 0, definition: {}, selectedTitles: {} };
  clientDetail = context;
  await global.refreshPerfDetail(context, true);
  assert.strictEqual(captured.get('somenteDetalhamento'), '1');
  assert.strictEqual(captured.get('detalheGrupo'), 'A');
  assert.strictEqual(rendered, 0);
  assert.strictEqual(context.rows[0].documento, '1');
  assert.strictEqual(new URLSearchParams(state.detailExportParams).get('escopo'), 'detalhamento');
  await global.refreshPerfDetail(context, false, true);
  assert.strictEqual(captured.has('somenteDetalhamento'), false);
  assert.strictEqual(rendered, 1, 'Atualização após edição mantém renderização completa');
  assert.strictEqual(context.params.includes('somenteDetalhamento'), false);
  await global.refreshPerfDetail(context, false);
  assert.strictEqual(captured.get('somenteDetalhamento'), '1');
  console.log('OK: abrir/renovar detalhe, exportação e atualização completa após edição; sem alterar HTML do modal.');
})().catch(error => { console.error(error); process.exit(1); });
