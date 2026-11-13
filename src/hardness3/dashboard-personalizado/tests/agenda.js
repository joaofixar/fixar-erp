'use strict';
// DOM, transporte e ERP simulados; nenhum servidor/banco é acessado.
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const assert = require('assert');
const base = path.join(__dirname, '..');
const source = fs.readFileSync(path.join(base, 'dashboard.js'), 'utf8');
const escapeSource = source.slice(source.indexOf('  function escapeHtml('), source.indexOf('  function icon('));
const renderer = {};
vm.runInNewContext(escapeSource, renderer);
renderer.listCell = (row, column) => '<td>' + renderer.escapeHtml(row[column.campo]) + '</td>';
vm.runInNewContext(source.slice(source.indexOf('  function detailTable('), source.indexOf('  function detailLoading(')), renderer);
const row = { tituloId: 123, clienteId: 45, empresaTituloId: 5, agenda: '<img src=x onerror=alert(1)>', documento: '123" onclick="alert(1)' };
const definition = { modal: true, colunas: [{ campo: 'agenda', titulo: 'Agenda' }], agenda: { habilitarInclusao: true } };
const html = renderer.detailTable([row], definition);
assert(html.includes('data-agenda-title="123"'));
assert(!html.includes('<img') && html.includes('&lt;img'));
assert(!html.includes('onclick="alert'));
assert(!renderer.detailTable([{ ...row, tituloId: '123" onmouseover="x' }], definition).includes('data-agenda-title'));
assert(!renderer.detailTable([row], { ...definition, agenda: {} }).includes('data-agenda-title'));
assert(!renderer.detailTable([row], { ...definition, modal: false }).includes('data-agenda-title'));

function element() {
  return { hidden: true, style: {}, listeners: {}, children: [], textContent: '',
    addEventListener(type, callback) { this.listeners[type] = callback; },
    removeEventListener(type) { delete this.listeners[type]; },
    appendChild(child) { this.children.push(child); },
    remove() { this.removed = true; }, focus() { this.focused = true; } };
}
const elements = {};
['agendaModal', 'agendaNativeContent', 'agendaStatus', 'closeAgendaModal', 'agendaTitle', 'indicatorDetailModal', 'closeIndicatorDetailModal', 'agendaSaving', 'agendaSavingMessage'].forEach(id => { elements[id] = element(); });
let frame, timer, handler, sendHandler, refreshCount = 0;
// O ERP carrega jQuery 1.6.2: não há .on/.off. O teste deve reproduzir essa API.
const docBridge = { unbind() { handler = null; sendHandler = null; }, bind(name, callback) { if (name.startsWith('ajaxSend')) sendHandler = callback; else handler = callback; } };
const win = { location: { origin: 'https://erp.example', href: 'https://erp.example/hardness3/dashboard-personalizado/' } };
const context = {
  window: win, URL, URLSearchParams, document: {
    getElementById: id => elements[id],
    createElement: () => {
      frame = element();
      const nativeDocument = element();
      nativeDocument.head = element();
      nativeDocument.createElement = element;
      frame.contentWindow = { location: win.location, document: nativeDocument, g: { usuarioAtual: '7', empresaAtual: '1' }, jQuery: () => docBridge };
      return frame;
    }
  },
  setTimeout: callback => { timer = callback; return 1; }, clearTimeout: () => { timer = null; }
};
const agendaSource = fs.readFileSync(path.join(base, 'agenda.js'), 'utf8');
vm.runInNewContext(agendaSource, context);
const submission = { URLSearchParams };
vm.runInNewContext(agendaSource.slice(agendaSource.indexOf('  function isAgendaSubmission('), agendaSource.indexOf('  function showSaving(')), submission);
for (const data of [
  'crm001FormAgenda%5Binternal_sent%5D=1&crm001FormAgenda%5BD085_Mensagem%5D=Teste',
  'crm001FormAgenda[D085_Mensagem]=Teste', 'D085_Mensagem=Teste',
  { 'crm001FormAgenda[D085_Mensagem]': 'Teste' }, { crm001FormAgenda: { internal_sent: '1', D085_Mensagem: 'Teste' } }
]) assert.strictEqual(submission.isAgendaSubmission({ type: 'POST', data }), true);
for (const data of ['D024_Id=45&T002_Id=123', 'outroForm%5BD085_Mensagem%5D=Teste', 'texto=D085_Mensagem%3DTeste', {}, null]) {
  assert.strictEqual(submission.isAgendaSubmission({ type: 'POST', data }), false);
}
assert.strictEqual(submission.isAgendaSubmission({ type: 'GET', data: 'D085_Mensagem=Teste' }), false);
const nativeContext = { tituloId: 123, clienteId: 45, usuario: 7, empresaSessao: 1, url: '/crm/crm001/content/crm001contentEditarAgenda/?oneWindow=true&D024_Id=45&T002_Id=123&tab=geral' };
const refresh = () => { refreshCount++; return Promise.resolve(true); };

async function run() {
  for (const url of ['https://other.example' + nativeContext.url, nativeContext.url + '&p=5', nativeContext.url.replace('123', '999')]) {
    assert.throws(() => win.dashboardAgenda.open({ ...nativeContext, url }, '123', refresh));
  }
  let confirmation = '';
  win.dashboardAgenda.open(nativeContext, '123', refresh, null, message => { confirmation = message; });
  assert(elements.indicatorDetailModal.inert);
  assert.strictEqual(frame.style.visibility, 'hidden');
  frame.listeners.load();
  assert.strictEqual(frame.style.visibility, 'visible');
  handler({}, {}, { url: '/outra/rota/' });
  assert.strictEqual(timer, undefined);
  // Formulario agrupa os campos: crm001FormAgenda[D085_Mensagem].
  sendHandler({}, {}, { url: '/crm/crm001/form/crm001FormEditarAgenda/', type: 'POST', data: 'crm001FormAgenda%5Binternal_sent%5D=1&crm001FormAgenda%5BD085_Mensagem%5D=Teste' });
  assert(elements.closeAgendaModal.disabled);
  assert(!elements.agendaSaving.hidden && elements.agendaSavingMessage.textContent.includes('Salvando'));
  elements.closeAgendaModal.listeners.click();
  assert(win.dashboardAgenda.isOpen() && !frame.removed, 'Aguardar o envio nativo antes de fechar');
  handler({}, {}, { url: '/crm/crm001/form/crm001FormEditarAgenda/' });
  assert(elements.closeAgendaModal.disabled, 'Aguardar a confirmação da gravação');
  assert(!elements.agendaSaving.hidden && elements.agendaSavingMessage.textContent.includes('Atualizando'));
  const obsoleteHandler = handler;
  timer();
  await Promise.resolve();
  assert.strictEqual(refreshCount, 1);
  assert(confirmation.includes('Última ocorrência atualizada'));
  assert(elements.agendaSaving.hidden);
  assert(frame.removed && !elements.indicatorDetailModal.inert && !win.dashboardAgenda.isOpen());
  obsoleteHandler({}, {}, { url: '/crm/crm001/form/crm001FormEditarAgenda/' });
  assert.strictEqual(timer, null, 'Evento antigo não deve agendar atualização');
  win.dashboardAgenda.open(nativeContext, '123', refresh);
  frame.listeners.load();
  elements.closeAgendaModal.listeners.click();
  assert.strictEqual(refreshCount, 2);
  for (const g of [{ usuarioAtual: 8, empresaAtual: 1 }, { usuarioAtual: 7, empresaAtual: 5 }, null]) {
    win.dashboardAgenda.open(nativeContext, '123', refresh);
    frame.contentWindow.g = g;
    frame.listeners.load();
    assert(frame.removed && elements.agendaStatus.textContent.includes('sessão'));
    win.dashboardAgenda.close(false);
  }
  assert.strictEqual(refreshCount, 2);

  // Atualizações atrasadas não substituem outro cliente nem reabrem modal fechado.
  let resolveRequest;
  const detailElements = { '#indicatorDetailModal': { hidden: false }, '#indicatorDetailContent': { innerHTML: 'Inicial' }, '#exportIndicatorDetailPdf': {} };
  const detailContext = {
    clientDetail: null, Promise, URLSearchParams, state: {},
    $: selector => detailElements[selector],
    request: () => new Promise(resolve => { resolveRequest = resolve; }),
    detailTable: renderer.detailTable, completeLoadingProgress: () => Promise.resolve(),
    stopLoadingProgress() {}, escapeHtml: renderer.escapeHtml
  };
  vm.runInNewContext(source.slice(source.indexOf('  function refreshClientDetail('), source.indexOf('  function openTitleAgenda(')), detailContext);
  const client = { params: 'id=teste&f_abrangenciaEmpresa=todas&detalheGrupo=Cliente', group: 'Cliente', definition, requestId: 0 };
  detailContext.clientDetail = client;
  let pending = detailContext.refreshClientDetail(client, false);
  detailContext.clientDetail = null;
  resolveRequest({ listagemDetalhes: { Cliente: [row] } });
  assert.strictEqual(await pending, null);
  assert.strictEqual(detailElements['#indicatorDetailContent'].innerHTML, 'Inicial');
  detailContext.clientDetail = client;
  pending = detailContext.refreshClientDetail(client, false);
  resolveRequest({ listagemDetalhes: { Cliente: [{ ...row, agendaId: 91, agenda: 'Nova ocorrência' }] } });
  await pending;
  assert(detailElements['#indicatorDetailContent'].innerHTML.includes('Nova ocorrência'));
  assert.strictEqual(client.rows[0].agendaId, 91);
  assert(detailContext.state.detailExportParams.includes('f_abrangenciaEmpresa=todas'));
  console.log('OK: botão, escape de conteúdo, contexto nativo, atualização após salvar/fechar e respostas obsoletas.');
}
run().catch(error => { console.error(error); process.exitCode = 1; });
