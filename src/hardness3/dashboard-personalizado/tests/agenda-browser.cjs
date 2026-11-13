'use strict';

// Node 18+ e Playwright. HTML, Ajax e dados do ERP são fixtures interceptadas;
// jQuery e jquery.form são os reais do repositório. Nenhuma base é acessada.
const fs = require('fs');
const path = require('path');
const assert = require('node:assert/strict');
const { chromium } = require('playwright');
const root = path.resolve(__dirname, '..', '..');
const script = fs.readFileSync(path.join(root, 'dashboard-personalizado/agenda.js'), 'utf8');
const dashboardHtml = fs.readFileSync(path.join(root, 'dashboard-personalizado/index.html'), 'utf8');
const agendaModalHtml = dashboardHtml.slice(dashboardHtml.indexOf('  <div class="agenda-modal"'), dashboardHtml.indexOf('  <script src="brazil-map.js'));
const nativePath = '/crm/crm001/content/crm001contentEditarAgenda/';
const formPath = '/crm/crm001/form/crm001FormEditarAgenda/';

async function check(browser, version) {
  const page = await browser.newPage();
  const errors = [];
  let pendingResponse;
  const unexpected = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.route('**/*', async route => {
    const url = new URL(route.request().url());
    assert.equal(url.origin, 'https://agenda.test');
    let body;
    let contentType = 'text/html';
    if (url.pathname === '/dashboard') {
      body = `<link rel="stylesheet" href="/dashboard.css"><div id="indicatorDetailModal"><button id="closeIndicatorDetailModal">Fechar</button></div>
        ${agendaModalHtml}
        <script>window.abrirJanela = function () { window.outsideCalls = (window.outsideCalls || 0) + 1; };</script>
        <script src="/agenda.js"></script>`;
    } else if (url.pathname === '/agenda.js') {
      contentType = 'application/javascript';
      body = script;
    } else if (url.pathname === '/jquery.js') {
      contentType = 'application/javascript';
      body = fs.readFileSync(path.join(root, `static/js/jquery-${version}.min.js`), 'utf8');
    } else if (url.pathname === '/jquery.form.js') {
      contentType = 'application/javascript';
      body = fs.readFileSync(path.join(root, 'static/js/jquery.form.js'), 'utf8');
    } else if (url.pathname === '/dashboard.css') {
      contentType = 'text/css';
      body = fs.readFileSync(path.join(root, 'dashboard-personalizado/dashboard.css'), 'utf8');
    } else if (url.pathname === nativePath) {
      body = `<script src="/jquery.js"></script><script src="/jquery.form.js"></script><script>var g = {usuarioAtual: 7, empresaAtual: 1};</script>
        <div class="contentCabecalho"><button id="cliente">Cliente fictício</button></div>
        <a id="linkCliente" href="/cad/cad001/content/cad001contentCadastro/">Cadastro</a>
        <button id="crm001CadastroCliente">Incluir cliente</button><button id="crm001CadastroContato">Incluir contato</button>
        <form id="agendaForm"><input type="hidden" name="crm001FormAgenda[internal_sent]" value="1">
        <input type="hidden" name="crm001FormAgenda[D085_T002_Id]" value="123">
        <textarea id="mensagem" name="crm001FormAgenda[D085_Mensagem]"></textarea></form>
        <button id="enviar">Enviar</button><div id="resultado"></div><div id="etapas" hidden>Tarefa do cadastro</div>
        <script>
        function abrirJanela() { parent.windowCalls++; $('#etapas').removeAttr('hidden'); }
        function nova_janela() { parent.popupCalls++; }
        function dialogConfirm(title) { parent.dialogTitles.push(title); $('#resultado').text(title); }
        $('#cliente').bind('click', function () {
          parent.headerCalls++;
          abrirJanela(false, '', '', 'crm', '', 'CRM - Cliente', '/crm/crm001/content/crm001ContentPrincipalCRM/');
        });
        $('#enviar').bind('click', function () {
          $('#agendaForm').ajaxSubmit({url:'${formPath}', type:'POST', data:{ajax:true},
            success:function (data) {
              if (data === 'VALIDACAO') { dialogConfirm('Erro: preencha a mensagem'); return; }
              $('#agendaForm, #enviar, #resultado').hide();
              setTimeout(function () {
                dialogConfirm('Incluir orçamento?');
                abrirJanela(false, '', '', 'tarefa', '', 'Tarefa', '/etc/etc017/content/etc017ContentTarefa/');
                parent.followupAttempted = true;
              }, 150);
            },
            error:function () {$('#resultado').text('Erro nativo simulado');}
          });
        });</script>`;
    } else if (url.pathname === formPath) {
      const fields = new URLSearchParams(route.request().postData());
      assert.equal(fields.get('crm001FormAgenda[internal_sent]'), '1');
      assert.equal(fields.get('crm001FormAgenda[D085_T002_Id]'), '123');
      assert.equal(fields.get('crm001FormAgenda[D085_Mensagem]'), 'Mensagem fictícia');
      await new Promise(resolve => { pendingResponse = { route, resolve }; });
      return;
    } else {
      unexpected.push(url.pathname);
      await route.abort();
      return;
    }
    await route.fulfill({ contentType: contentType + '; charset=utf-8', body });
  });

  async function open() {
    await page.evaluate(nativePath => {
      window.refreshCount = 0;
      window.agendaId = 90;
      window.windowCalls = 0;
      window.headerCalls = 0;
      window.popupCalls = 0;
      window.dialogTitles = [];
      window.savedMessage = '';
      window.followupAttempted = false;
      window.holdRefresh = false;
      window.confirmRefresh = null;
      let previous = 90;
      window.dashboardAgenda.open({
        tituloId: 123, clienteId: 45, usuario: 7, empresaSessao: 1,
        url: nativePath + '?oneWindow=true&D024_Id=45&T002_Id=123&tab=geral'
      }, '123', () => {
        window.refreshCount++;
        const changed = window.agendaId > previous;
        previous = window.agendaId;
        if (window.holdRefresh) return new Promise(resolve => { window.confirmRefresh = () => resolve(changed); });
        return Promise.resolve(changed);
      }, null, message => { window.savedMessage = message; });
    }, nativePath);
    await page.waitForFunction(() => document.querySelector('iframe')?.style.visibility === 'visible');
    await page.frameLocator('iframe').locator('#enviar').waitFor({ state: 'visible' });
  }
  async function respond(status, body) {
    assert(pendingResponse, 'O formulário deve emitir uma requisição Ajax real');
    await pendingResponse.route.fulfill({ status, body: body || (status === 200 ? 'OK' : 'Erro simulado') });
    pendingResponse.resolve();
    pendingResponse = null;
  }
  try {
    await page.goto('https://agenda.test/dashboard');
    await open();
    const nativeFrame = page.frames().find(frame => frame.url().includes(nativePath));
    assert.equal(await nativeFrame.evaluate(() => jQuery.fn.jquery), version);
    if (version === '1.6.2') assert.equal(await nativeFrame.evaluate(() => typeof jQuery.fn.on), 'undefined');

    await page.frameLocator('iframe').locator('#cliente').click();
    await page.frameLocator('iframe').locator('#linkCliente').click();
    await page.frameLocator('iframe').locator('#linkCliente').press('Enter');
    assert.equal(await page.evaluate(() => window.headerCalls), 0, 'Cabeçalho somente para leitura');
    assert.equal(await page.frameLocator('iframe').locator('#crm001CadastroCliente').isVisible(), false);
    assert.equal(await page.frameLocator('iframe').locator('#crm001CadastroContato').isVisible(), false);
    await nativeFrame.evaluate(() => {
      abrirJanela(false, '', '', 'outro', '', 'CRM', '/crm/crm001/content/crm001ContentPrincipalCRM/');
      abrirJanela(false, '', '', 'outro', '', 'Cliente', '/cad/cad001/content/cad001contentCadastro/');
      nova_janela('/cad/cad001/content/cad001contentCadastro/');
      window.open('/cad/cad001/content/cad001contentCadastro/');
      dialogConfirm('Incluir orçamento?');
      dialogConfirm('Incluir tarefa?');
    });
    assert.equal(await page.evaluate(() => window.windowCalls + window.popupCalls), 0);
    assert.deepEqual(await page.evaluate(() => window.dialogTitles), []);
    await page.evaluate(() => window.abrirJanela());
    assert.equal(await page.evaluate(() => window.outsideCalls), 1, 'Não alterar a navegação externa');
    await page.frameLocator('iframe').locator('#mensagem').fill('Mensagem fictícia');

    await page.frameLocator('iframe').locator('#enviar').click();
    await page.waitForFunction(() => document.getElementById('closeAgendaModal').disabled);
    assert.equal(await page.locator('#agendaSaving').isVisible(), true);
    assert.equal(await page.locator('iframe').evaluate(frame => frame.inert), true);
    assert((await page.locator('#agendaSavingMessage').textContent()).includes('Salvando'));
    await respond(500);
    await page.waitForFunction(() => window.refreshCount === 1 && !document.getElementById('closeAgendaModal').disabled);
    assert.equal(await page.locator('#agendaSaving').isVisible(), false);
    assert.equal(await page.locator('iframe').evaluate(frame => frame.inert), false);
    assert(!await page.locator('#agendaStatus').textContent().then(text => text.includes('Última ocorrência atualizada')));

    await page.frameLocator('iframe').locator('#enviar').click();
    await page.waitForFunction(() => document.getElementById('closeAgendaModal').disabled);
    await respond(200, 'VALIDACAO');
    await page.waitForFunction(() => window.refreshCount === 2);
    assert.deepEqual(await page.evaluate(() => window.dialogTitles), ['Erro: preencha a mensagem']);
    assert.equal(await page.locator('iframe').count(), 1, 'Manter o formulário diante de erro de validação');
    assert.equal(await page.locator('#agendaSaving').isVisible(), false);

    await page.frameLocator('iframe').locator('#enviar').click();
    await page.waitForFunction(() => document.getElementById('closeAgendaModal').disabled);
    await page.evaluate(() => { window.agendaId = 91; window.holdRefresh = true; });
    await respond(200);
    await page.waitForFunction(() => typeof window.confirmRefresh === 'function');
    assert.equal(await page.locator('#agendaSaving').isVisible(), true, 'Cobrir o formulário que o ERP fechou enquanto confirma a gravação');
    assert((await page.locator('#agendaSavingMessage').textContent()).includes('Atualizando'));
    assert.equal(await page.locator('#closeAgendaModal').isDisabled(), true);
    assert.equal(await page.evaluate(() => window.savedMessage), '');
    await page.evaluate(() => window.confirmRefresh());
    await page.waitForFunction(() => window.savedMessage.includes('Última ocorrência atualizada'));
    assert.equal(await page.locator('iframe').count(), 0);
    assert.equal(await page.evaluate(() => window.refreshCount), 3);
    assert.equal(await page.evaluate(() => window.followupAttempted), true, 'Reproduzir o callback nativo anterior à releitura');
    assert.equal(await page.evaluate(() => window.windowCalls + window.popupCalls), 0, 'Não abrir tarefa nem outro cadastro');
    assert.deepEqual(await page.evaluate(() => window.dialogTitles), ['Erro: preencha a mensagem']);
    assert.equal(await page.evaluate(() => document.getElementById('indicatorDetailModal').inert), false);
    await open();
    await page.locator('#closeAgendaModal').click();
    assert.deepEqual(errors, []);
    assert.deepEqual(unexpected, []);
    console.log(`OK navegador/jQuery ${version} + jquery.form: POST agrupado, carregamento, validação e retorno automático aos títulos.`);
  } finally {
    await page.close();
  }
}

(async () => {
  const browser = await chromium.launch({
    headless: true,
    executablePath: process.env.CHROMIUM_EXECUTABLE || undefined,
    args: ['--no-sandbox', '--disable-dev-shm-usage']
  });
  try {
    // Mesmo conjunto carregado pelo ERP; jquery.form usa APIs legadas do 1.6.2.
    await check(browser, '1.6.2');
  } finally {
    await browser.close();
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
