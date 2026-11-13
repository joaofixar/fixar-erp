"""Dashboard real com agregados da planilha e API simulada; sem banco de cliente."""
import ast, base64, copy, http.server, json, os, pathlib, socket, struct, subprocess, tempfile, threading, time, urllib.parse, urllib.request

root = pathlib.Path(__file__).resolve().parents[1]
source = ast.parse((root / 'tests/painel-cotacoes-browser.py').read_text())
client = next(n for n in source.body if isinstance(n, ast.ClassDef) and n.name == 'CDP')
exec(compile(ast.Module(body=[client], type_ignores=[]), '<CDP>', 'exec'))
original = json.loads(subprocess.check_output(['php', str(root / 'tests/controle-carteira-bancos.php'), '--payload']))
definition = original['indicador']
legacy = dict(id='legado_teste', titulo='Indicador padrão', subtitulo='Teste de transição', filtros=[], abas=[], card=dict(titulo='Total'), composicao=dict(habilitada=False), grafico=dict(habilitado=False), listagem=dict(habilitada=False))
state = dict(fail=False, saves=[], guarantees={}, revision='inicial', queries=[])


class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *a, **kw):
        super().__init__(*a, directory=str(root), **kw)

    def log_message(self, *a):
        pass

    def send(self, data, code=200):
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(data).encode())

    def do_GET(self):
        if self.path.startswith('/data.php?'):
            query = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            state['queries'].append(query)
            if query.get('action') == ['configuration']:
                self.send(dict(titulo='Carteira — conferência local', indicadores=[definition, legacy], podeGerenciarAcessos=False))
            elif query.get('id') == ['legado_teste']:
                self.send(dict(indicador=legacy, card=dict(valor=1234, quantidade=1, formato='moeda'), grafico=[], listagem=[], updatedAt='04/11/2026'))
            else:
                payload = copy.deepcopy(original)
                scope = query.get('f_abrangenciaEmpresa', ['todas'])[0]
                factor = {'todas': 1, 'atual': .5, '5': .25}.get(scope, 1)
                company_filter = payload['indicador']['filtros'][0]
                company_filter['valor'] = scope
                payload['controleCarteiraBancos']['empresasTitulo'] = next(option['titulo'] for option in company_filter['opcoes'] if option['valor'] == scope)
                payload['controleCarteiraBancos']['revisao'] = state['revision']
                for bank in payload['controleCarteiraBancos']['bancos']:
                    bank['garantia'] = state['guarantees'].get(bank['id'], bank['garantia'])
                    bank['carteiraCentavos'] = round(bank['carteiraCentavos'] * factor)
                    if isinstance(bank['vencimentos'], dict):
                        bank['vencimentos'] = {day: round(value * factor) for day, value in bank['vencimentos'].items()}
                for field in ['totalCentavos', 'boletosCentavos']:
                    payload['controleCarteiraBancos'][field] = round(payload['controleCarteiraBancos'][field] * factor)
                for item in payload['controleCarteiraBancos']['composicao']:
                    item['valorCentavos'] = round(item['valorCentavos'] * factor)
                self.send(payload)
            return
        if self.path == '/static/img/logo_hardness.png':
            self.send_response(200)
            self.end_headers()
            self.wfile.write((root.parent / 'static/img/logo_hardness.png').read_bytes())
            return
        super().do_GET()

    def do_POST(self):
        payload = json.loads(self.rfile.read(int(self.headers['Content-Length'])))
        if 'saveBankGuarantees' not in self.path:
            self.send(dict(ok=True))
        elif state['fail']:
            self.send(dict(error='Outro usuário alterou as garantias. Atualize o indicador.'), 422)
        else:
            state['saves'].append(payload)
            state['guarantees'] = payload['garantias']
            state['revision'] = 'revisao-' + str(len(state['saves']))
            self.send(dict(ok=True, garantias=payload['garantias'], revisao=state['revision']))


server = http.server.ThreadingHTTPServer(('127.0.0.1', 0), Handler)
threading.Thread(target=server.serve_forever, daemon=True).start()
out = pathlib.Path(os.environ.get('CARTEIRA_SCREENSHOTS', '/tmp/controle-carteira-bancos-preview'))
out.mkdir(exist_ok=True)


def until(c, expression):
    for _ in range(150):
        if c.js(expression):
            return
        time.sleep(.05)
    raise AssertionError((expression, c.errors, c.js("document.querySelector('#indicatorSummary').textContent")))


with tempfile.TemporaryDirectory(prefix='carteira-bancos-browser-') as profile:
    with (out / 'chrome.log').open('w') as log:
        proc = subprocess.Popen([os.environ['COTACOES_CHROME'], '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage', '--remote-debugging-port=0', '--user-data-dir=' + profile, 'about:blank'], stdout=log, stderr=log)
        try:
            active = pathlib.Path(profile) / 'DevToolsActivePort'
            for _ in range(100):
                if active.exists():
                    break
                time.sleep(.05)
            port = int(active.read_text().splitlines()[0])
            pages = json.load(urllib.request.urlopen('http://127.0.0.1:%s/json' % port))
            c = CDP(pages[0]['webSocketDebuggerUrl'])
            c.call('Runtime.enable')
            c.call('Page.enable')
            c.call('Emulation.setDeviceMetricsOverride', dict(width=1700, height=1100, deviceScaleFactor=1, mobile=False))
            c.call('Page.navigate', dict(url='http://127.0.0.1:%s/index.html' % server.server_port))
            until(c, "document.querySelectorAll('[data-wallet-bank]').length === 6")
            assert c.js("document.querySelector('[name=f_abrangenciaEmpresa]').value") == 'todas'
            assert c.js("document.querySelector('.wallet-header p').textContent.includes('Todas as empresas')")
            assert c.js("document.querySelectorAll('.wallet-global-chart rect').length") == 42
            assert c.js("document.querySelector('.wallet-selected-chart').textContent.includes('Marque os bancos')")
            assert c.js("document.querySelector('[data-wallet-bank=itau] [data-wallet-alert]').textContent.includes('11/11/2026')")
            assert c.js("document.querySelector('[data-wallet-bank=bradesco] [data-wallet-alert]').textContent.includes('04/11/2026')")
            assert c.js("document.querySelector('.wallet-banks').getBoundingClientRect().top < document.querySelector('.wallet-global-chart').getBoundingClientRect().top")
            c.js("var scope=document.querySelector('[name=f_abrangenciaEmpresa]');scope.value='atual';scope.dispatchEvent(new Event('change',{bubbles:true}))")
            until(c, "!!document.querySelector('[data-wallet-bank=itau]') && document.querySelector('.wallet-header p').textContent.includes('Empresa atual')")
            assert c.js("document.querySelector('[data-wallet-bank=itau]').textContent.includes('1.623.299,16')")
            assert c.js("document.querySelector('[data-wallet-bank=itau] [data-wallet-alert]').textContent.includes('04/11/2026')")
            assert c.js("document.querySelector('.wallet-global-chart rect title').textContent.includes('1.383.081,10')"), 'Gráfico acompanha a carteira filtrada'
            c.js("var scope=document.querySelector('[name=f_abrangenciaEmpresa]');scope.value='5';scope.dispatchEvent(new Event('change',{bubbles:true}))")
            until(c, "!!document.querySelector('[data-wallet-bank=itau]') && document.querySelector('.wallet-header p').textContent.includes('Empresa 5')")
            assert c.js("document.querySelector('[data-wallet-bank=itau]').textContent.includes('811.649,58')")
            c.js("var scope=document.querySelector('[name=f_abrangenciaEmpresa]');scope.value='todas';scope.dispatchEvent(new Event('change',{bubbles:true}))")
            until(c, "!!document.querySelector('[data-wallet-bank=itau]') && document.querySelector('.wallet-header p').textContent.includes('Todas as empresas')")
            assert c.js("document.querySelector('[data-wallet-bank=itau] [data-wallet-alert]').textContent.includes('11/11/2026')")
            c.js("document.querySelector('[data-wallet-select=itau]').click(); document.querySelector('[data-wallet-select=caixa]').click()")
            assert c.js("document.querySelectorAll('.wallet-selected-chart polyline').length") == 2
            before = len(state['queries'])
            c.js("var field=document.querySelector('[data-wallet-guarantee=itau]'); field.value='0'; field.dispatchEvent(new Event('input',{bubbles:true}))")
            assert c.js("document.querySelector('[data-wallet-bank=itau] [data-wallet-margin]').textContent.includes('3.246.598,32')")
            assert len(state['queries']) == before, 'Edição recalcula sem buscar os títulos novamente'
            c.js("document.querySelector('[data-wallet-save]').click()")
            until(c, "document.querySelector('.wallet-status').textContent.includes('Garantias salvas')")
            assert state['saves'][-1]['garantias']['itau'] == 0
            assert state['saves'][-1]['revisao'] == 'inicial'
            state['fail'] = True
            c.js("var field=document.querySelector('[data-wallet-guarantee=itau]'); field.value='3000000'; field.dispatchEvent(new Event('input',{bubbles:true})); document.querySelector('[data-wallet-save]').click()")
            until(c, "document.querySelector('.wallet-status').textContent.includes('Outro usuário')")
            assert c.js("!document.querySelector('[data-wallet-save]').disabled")
            state['fail'] = False
            c.js("document.querySelector('[data-wallet-save]').click()")
            until(c, "document.querySelector('.wallet-status').textContent.includes('Garantias salvas')")
            assert state['saves'][-1]['revisao'] == 'revisao-1'
            c.js("var field=document.querySelector('[data-wallet-guarantee=itau]'); field.value='-1'; field.dispatchEvent(new Event('input',{bubbles:true}))")
            assert c.js("document.querySelector('[data-wallet-save]').disabled")
            c.js("var field=document.querySelector('[data-wallet-guarantee=caixa]'); field.value='100'; field.dispatchEvent(new Event('input',{bubbles:true})); var other=document.querySelector('[data-wallet-guarantee=itau]'); other.value='3000000';other.dispatchEvent(new Event('input',{bubbles:true}))")
            assert c.js("document.querySelector('[data-wallet-bank=caixa] [data-wallet-margin]').textContent.includes('12.625.012,37')"), 'Considera todos os campos após corrigir um inválido'
            c.js("var field=document.querySelector('[data-wallet-guarantee=caixa]'); field.value='9000000';field.dispatchEvent(new Event('input',{bubbles:true}))")
            c.call('Browser.setDownloadBehavior', dict(behavior='allow', downloadPath=str(out)))
            c.js("document.querySelector('[data-export=csv]').click()")
            csv = out / 'controle-carteira-bancos-2026-11-04.csv'
            for _ in range(100):
                if csv.exists():
                    break
                time.sleep(.05)
            assert 'Itaú' in csv.read_text(encoding='utf-8-sig')
            assert len(csv.read_text().splitlines()) == 277
            c.js("document.querySelector('[data-wallet-all]').click()")
            assert c.js("document.querySelectorAll('.wallet-selected-chart polyline').length") == 6
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=True))
            (out / 'controle-carteira-bancos-desktop.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.querySelector('[data-export=pdf]').click()")
            for _ in range(100):
                tabs = json.load(urllib.request.urlopen('http://127.0.0.1:%s/json' % port))
                popup_tabs = [tab for tab in tabs if tab['id'] != pages[0]['id'] and tab.get('type') == 'page']
                if popup_tabs:
                    break
                time.sleep(.05)
            popup = CDP(popup_tabs[0]['webSocketDebuggerUrl'])
            popup.call('Runtime.enable')
            popup.call('Page.enable')
            until(popup, "document.querySelectorAll('.wallet-banks tbody tr').length === 6")
            assert popup.js("document.querySelectorAll('input,button').length") == 0
            assert popup.js("document.body.textContent.includes('R$') && document.body.textContent.includes('04/11/2026')")
            pdf = popup.call('Page.printToPDF', dict(landscape=True, printBackground=True))
            (out / 'controle-carteira-bancos.pdf').write_bytes(base64.b64decode(pdf['data']))
            popup.call('Page.close')
            c.call('Emulation.setDeviceMetricsOverride', dict(width=390, height=844, deviceScaleFactor=1, mobile=True))
            assert c.js("document.documentElement.scrollWidth <= window.innerWidth"), 'Página sem transbordamento horizontal no celular'
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=True))
            (out / 'controle-carteira-bancos-mobile.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.querySelector('[data-wallet-none]').click()")
            assert c.js("document.querySelectorAll('.wallet-selected-chart polyline').length") == 0
            c.js("document.querySelector('[data-wallet-select=itau]').click(); document.querySelector('[data-id=legado_teste]').click()")
            until(c, "!document.querySelector('.bank-wallet') && !!document.querySelector('.hero-card')")
            c.js("document.querySelector('[data-id=controle_carteira_bancos]').click()")
            until(c, "document.querySelectorAll('.wallet-selected-chart polyline').length === 1")
            assert not c.errors, c.errors
            print('OK: filtro Todas/Atual/Empresa 5 atualiza carteira, alertas e gráfico; edição, gravação/conflito, CSV, PDF, celular e troca de indicador.')
        finally:
            proc.terminate()
            proc.wait(timeout=10)
            server.shutdown()
