'use strict';
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
class AbortController { constructor() { this.signal={aborted:false}; } abort() { this.signal.aborted=true; } }
class DOMException extends Error { constructor(message,name) { super(message); this.name=name; } }
const calls = [], rendered = [], errors = [];
const state = { configuration: {}, selectedId: 'fixture', loading: false, requestId: 0, exportParams: null };
const definition = { carregamentoProgressivo: true, limitarPeriodoProgressivo: false };
let responses = [], afterFirst;
const context = {
  window: {}, URL, URLSearchParams, AbortController, DOMException, Promise, Date, setTimeout,
  location: {href:'http://localhost/index.html'},
  state, titleBulkSaving:false, titleEditing:null, indicatorLoadAbort:null,
  selectedDefinition: () => definition, $: () => ({}),
  indicatorParams: () => new URLSearchParams('id=fixture&f_dataInicio=2026-10-01'),
  setExportAvailability() {}, renderLoading() {},
  completeLoadingProgress: () => Promise.resolve(),
  renderResult: data => rendered.push(data), renderError: error => errors.push(error.message),
  request: () => { throw new Error('Não usou carregamento progressivo'); },
  fetch: async (url, options) => {
    calls.push({params:new URL(url).searchParams, options});
    const response = responses.shift();
    if (!response) throw new Error('Resposta inesperada');
    if (calls.length===2 && afterFirst) afterFirst();
    return {ok:response.status<400, status:response.status, json:async () => response.data};
  }
};
vm.createContext(context);
vm.runInContext(fs.readFileSync(__dirname + '/../carregamento-progressivo.js','utf8'),context);
const source=fs.readFileSync(__dirname + '/../dashboard.js','utf8');
vm.runInContext(source.slice(source.indexOf('  function loadIndicator('),source.indexOf('  function accessItems(')),context);
async function finish() {
  const deadline=Date.now()+2000;
  while (state.loading && Date.now()<deadline) await new Promise(resolve => setTimeout(resolve,2));
  assert.strictEqual(state.loading,false,'Carregamento não concluiu');
}
(async () => {
  const token='a'.repeat(32);
  responses=[{status:202,data:{carregamento:{token,fontesConcluidas:1}}},{status:200,data:{cargaToken:token,card:{valor:10}}}];
  afterFirst=() => assert.strictEqual(rendered.length,0,'Renderizou total parcial');
  context.loadIndicator(true);
  await finish();
  assert.deepStrictEqual(errors,[]);
  assert.strictEqual(rendered.length,1);
  assert.strictEqual(calls[0].params.has('cargaToken'),false);
  assert.strictEqual(calls[1].params.get('cargaToken'),token);
  calls.forEach(c => assert.strictEqual(c.params.get('f_dataInicio'),'2026-10-01'));
  assert.strictEqual(new URLSearchParams(state.exportParams).get('cargaToken'),token);
  responses=[{status:422,data:{error:'Fonte indisponível'}}];
  context.loadIndicator(true);
  await finish();
  assert.strictEqual(calls[2].params.has('cargaToken'),false,'Atualização reutilizou carga anterior');
  assert.strictEqual(rendered.length,1,'Erro apresentou resultado parcial');
  assert.deepStrictEqual(errors,['Fonte indisponível']);
  assert(!context.window.DashboardProgressivo.text({fontesConcluidas:1}).includes('undefined'));
  // Exercita renderFilters para conferir que o modo geral não herda o limite comercial.
  context.bindCashAccountsFilter=() => {};
  context.preferredFilterValue=() => '';
  context.escapeHtml=value => String(value);
  const from={}, to={}, form={elements:{namedItem:name => name==='f_dataInicio'?from:to}};
  context.$=selector => selector==='#resetFilters'?null:form;
  let limits=0;
  context.window.DashboardProgressivo.limitDates=() => limits++;
  vm.runInContext(source.slice(source.indexOf('  function renderFilters('),source.indexOf('  function ',source.indexOf('  function renderFilters(')+10)),context);
  const filters=[{id:'dataInicio',tipo:'data',bloqueado:true},{id:'dataFim',tipo:'data',bloqueado:true}];
  context.renderFilters(filters);
  assert.strictEqual(limits,0,'Modo geral herdou limite de dois anos');
  definition.limitarPeriodoProgressivo=true;
  context.renderFilters(filters);
  assert.strictEqual(limits,1,'Limite comercial deixou de ser aplicado');
  console.log('OK: continuação geral, ausência de parciais, token na exportação, atualização nova, falhas e limite comercial preservado.');
})().catch(error => { console.error(error); process.exit(1); });
