"""Interface real; agregados do gráfico da planilha e títulos fictícios, sem ERP."""
import ast, base64, copy, http.server, json, os, pathlib, socket, struct, subprocess, tempfile, threading, time, urllib.parse, urllib.request

root = pathlib.Path(__file__).resolve().parents[1]
tree = ast.parse((root / 'tests/painel-cotacoes-browser.py').read_text())
client = next(n for n in tree.body if isinstance(n, ast.ClassDef) and n.name == 'CDP')
exec(compile(ast.Module(body=[client], type_ignores=[]), '<CDP>', 'exec'))
original = json.loads(subprocess.check_output(['php', str(root / 'tests/endividamento.php'), '--payload']))
definition = original['indicador']
plot = json.loads((root / 'tests/fixtures/endividamento-mensal-planilha.json').read_text())
rows = []
for bar in plot:
    total = round(bar['valor'] * 100)
    if not total:
        continue
    for part in range(51):
        cents = total // 51 + int(part < total % 51)
        month = int(bar['periodo'][5:])
        paid = cents if month < 10 or (month == 10 and part < 40) else 0
        if month == 11 and part == 0:
            paid = cents // 4
        opened = cents - paid
        id = len(rows) + 1
        rows.append(dict(id=id, descricao='Credor fictício', documento='CONTRATO-' + str(id), duplicata='TIT-' + str(id),
            parcela=str(part + 1) + '/51', empresaNome='Empresa fictícia', dataVencimento=bar['periodo'] + '-15', conta=bar['serie'],
            valorPago=paid / 100, valorAberto=opened / 100, valorVencido=opened / 100 if month <= 10 else 0,
            valorAVencer=opened / 100 if month > 10 else 0, valorComprometido=cents / 100))
rows.sort(key=lambda r: (r['dataVencimento'], r['id']))
legacy = dict(id='legado_teste', titulo='Outro indicador', filtros=[], card=dict(titulo='Total'), grafico=dict(habilitado=False), composicao=dict(habilitada=False), listagem=dict(habilitada=False))
state = dict(queries=[], fail=False)


def response(query):
    result = copy.deepcopy(original)
    result['updatedAt'] = 'Prévia local · títulos simulados'
    selected = rows
    month = query.get('listaGraficoPeriodo', [''])[0]
    series = query.get('listaGraficoSerie', [''])[0]
    if month:
        selected = [row for row in selected if row['dataVencimento'].startswith(month) and (not series or row['conta'] == series)]
    offset = int(query.get('listaOffset', ['0'])[0])
    result['indicador']['grafico']['selecao'] = dict(periodo=month, serie=series)
    if month:
        result['indicador']['listagem']['observacaoRodape'] = 'Seleção: ' + month + ' · ' + (series or 'todas as categorias') + '. Cartões mantêm o período.'
    totals = {field: round(sum(row[field] for row in selected), 2) for field in ['valorPago', 'valorAberto', 'valorComprometido']}
    result['listagem'] = selected[offset:offset + 50]
    result['listagemPaginacao'] = dict(total=len(selected), offset=offset, tamanhoPagina=50, quantidade=len(result['listagem']), temMais=offset + 50 < len(selected), totais=totals)
    result['card']['valor'] = round(sum(row['valorPago'] for row in rows), 2)
    result['card']['quantidade'] = len(rows)
    for metric, field in zip(result['resumos'], ['valorAberto', 'valorVencido', 'valorAVencer']):
        metric['valor'] = round(sum(row[field] for row in rows), 2)
    result['grafico'] = [dict(bar, quantidade=51 if bar['valor'] else 0) for bar in plot]
    return result


class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *a, **kw):
        super().__init__(*a, directory=str(root), **kw)

    def log_message(self, *a):
        pass

    def send(self, payload, code=200):
        self.send_response(code)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(payload).encode())

    def do_GET(self):
        if self.path.startswith('/data.php?'):
            query = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            state['queries'].append(query)
            if query.get('action') == ['configuration']:
                self.send(dict(titulo='Endividamento — prévia local', indicadores=[definition, legacy], podeGerenciarAcessos=False))
            elif query.get('id') == ['legado_teste']:
                self.send(dict(indicador=legacy, card=dict(valor=1234, quantidade=1, formato='moeda'), grafico=[], listagem=[], updatedAt='Prévia'))
            elif state['fail'] and query.get('somenteListagem') == ['1']:
                self.send(dict(error='Falha simulada ao consultar os títulos.'), 422)
            else:
                self.send(response(query))
            return
        if self.path == '/static/img/logo_hardness.png':
            self.send_response(200)
            self.end_headers()
            self.wfile.write((root.parent / 'static/img/logo_hardness.png').read_bytes())
            return
        super().do_GET()

    def do_POST(self):
        self.rfile.read(int(self.headers['Content-Length']))
        self.send(dict(ok=True))


server = http.server.ThreadingHTTPServer(('127.0.0.1', 0), Handler)
threading.Thread(target=server.serve_forever, daemon=True).start()
out = pathlib.Path(os.environ.get('ENDIVIDAMENTO_SCREENSHOTS', '/tmp/endividamento-preview'))
out.mkdir(exist_ok=True)


def until(c, expression):
    for _ in range(150):
        if c.js(expression):
            return
        time.sleep(.05)
    raise AssertionError((expression, c.errors, c.js("document.querySelector('#indicatorSummary').textContent")))


with tempfile.TemporaryDirectory(prefix='endividamento-browser-') as profile:
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
            until(c, "document.querySelectorAll('.stacked-segment').length === 40")
            assert c.js("Array.from(document.querySelectorAll('.indicator-kpi>span')).map(n=>n.textContent)") == ['Pago', 'Em aberto', 'Vencido', 'A vencer']
            assert c.js("document.querySelectorAll('.stacked-month').length") == 12
            assert c.js("document.querySelector('#indicatorSummary').getBoundingClientRect().top < document.querySelector('#indicatorChart').getBoundingClientRect().top && document.querySelector('#indicatorChart').getBoundingClientRect().top < document.querySelector('#indicatorList').getBoundingClientRect().top")
            cards = c.js("document.querySelector('#indicatorSummary').textContent")
            assert c.js("!document.querySelector('.stacked-legend') && document.querySelector('.stacked-selection').hidden")
            assert c.js("document.querySelectorAll('.stacked-breakdown-toggle').length") == 12
            c.js("var tiny = document.querySelector('.stacked-segment[data-stack-month=\"2026-11\"][data-stack-series=\"Impostos/PERT\"]'); tiny.dispatchEvent(new PointerEvent('pointerenter'))")
            assert c.js("!document.querySelector('.stacked-tooltip').hidden && document.querySelector('.stacked-tooltip').textContent.includes('14.593,69')")
            assert c.js("Number(tiny.getAttribute('height')) > 0 && Number(tiny.getAttribute('height')) < 1"), 'Faixas pequenas preservam a proporção do valor'
            c.js("tiny.dispatchEvent(new PointerEvent('pointerleave')); document.querySelector('[data-stack-breakdown=\"2026-11\"]').dispatchEvent(new KeyboardEvent('keydown',{key:'Enter',bubbles:true}))")
            assert c.js("!document.querySelector('.stacked-breakdown').hidden && document.querySelector('.stacked-breakdown').textContent.includes('Faixa pequena')")
            assert c.js("document.querySelectorAll('.stacked-category').length") == 3
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=False))
            (out / 'endividamento-categorias.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.querySelector('.stacked-category[data-stack-series=\"Impostos/PERT\"]').click()")
            until(c, "document.querySelector('#indicatorList tfoot').textContent.includes('14.593,69')")
            assert state['queries'][-1]['listaGraficoSerie'] == ['Impostos/PERT']
            assert c.js("document.querySelector('.stacked-breakdown').hidden && document.activeElement === document.querySelector('#indicatorList h3')")
            assert c.js("document.querySelector('#indicatorList h3').textContent.includes('11/2026 · Impostos/PERT')")
            count = len(state['queries'])
            c.js("tiny.dispatchEvent(new MouseEvent('click',{bubbles:true}))")
            until(c, "document.querySelector('.stacked-status').textContent.includes('títulos na listagem') && document.querySelector('[data-stack-selection]').textContent.includes('Impostos/PERT')")
            assert len(state['queries']) > count and state['queries'][-1]['listaGraficoSerie'] == ['Impostos/PERT'], 'Clicar de novo abre os títulos da mesma faixa'
            c.js("document.querySelector('.stacked-segment[data-stack-month=\"2026-11\"][data-stack-series=\"Capital Giro\"]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
            until(c, "document.querySelector('[data-stack-selection]').textContent.includes('nov/26 · Capital Giro') && document.querySelector('#indicatorList tfoot').textContent.includes('6.218.995,14')")
            assert c.js("document.querySelectorAll('#indicatorList tbody tr').length") == 50
            assert c.js("document.querySelector('#indicatorSummary').textContent") == cards
            assert state['queries'][-1]['somenteListagem'] == ['1']
            assert state['queries'][-1]['listaGraficoSerie'] == ['Capital Giro']
            c.js("document.querySelector('#showMoreResults').click()")
            until(c, "document.querySelectorAll('#indicatorList tbody tr').length === 51")
            assert state['queries'][-1]['listaOffset'] == ['50'] and state['queries'][-1]['listaGraficoPeriodo'] == ['2026-11']
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=True))
            (out / 'endividamento-barra-selecionada.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.querySelector('.stacked-month[data-stack-month=\"2026-11\"]').dispatchEvent(new KeyboardEvent('keydown',{key:'Enter',bubbles:true}))")
            until(c, "document.querySelector('[data-stack-selection]').textContent.includes('nov/26 · todas as categorias')")
            assert state['queries'][-1].get('listaGraficoSerie') is None
            old_list = c.js("document.querySelector('#indicatorList').textContent")
            state['fail'] = True
            c.js("document.querySelector('.stacked-segment[data-stack-month=\"2026-11\"][data-stack-series=\"Impostos/PERT\"]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
            until(c, "document.querySelector('.stacked-status').textContent.includes('Falha simulada')")
            assert c.js("document.querySelector('#indicatorList').textContent") == old_list
            state['fail'] = False
            c.js("document.querySelector('[data-stack-clear]').click()")
            until(c, "document.querySelector('[data-stack-clear]').hidden && document.querySelector('.stacked-status').textContent.includes('títulos na listagem')")
            assert not state['queries'][-1].get('listaGraficoPeriodo')
            c.js("window.scrollTo(0,0)")
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=False))
            (out / 'endividamento-desktop.png').write_bytes(base64.b64decode(image['data']))
            c.call('Emulation.setDeviceMetricsOverride', dict(width=390, height=844, deviceScaleFactor=1, mobile=True))
            assert c.js("document.documentElement.scrollWidth <= window.innerWidth"), 'Layout móvel sem transbordamento'
            image = c.call('Page.captureScreenshot', dict(format='png', captureBeyondViewport=True))
            (out / 'endividamento-mobile.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.querySelector('[data-stack-breakdown=\"2026-11\"]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
            assert c.js("!document.querySelector('.stacked-breakdown').hidden && (()=>{const r=document.querySelector('.stacked-breakdown').getBoundingClientRect();return r.left>=0 && r.right<=innerWidth && r.top>=0 && r.bottom<=innerHeight})()"), 'Categorias cabem na tela do celular'
            c.js("window.dispatchEvent(new Event('resize'))")
            assert c.js("!document.querySelector('.stacked-breakdown').hidden"), 'Redimensionar a tela não fecha a consulta de categorias'
            c.js("document.querySelector('.stacked-breakdown-close').click()")
            assert c.js("document.querySelector('.stacked-breakdown').hidden && document.activeElement.getAttribute('data-stack-breakdown') === '2026-11'")
            c.js("document.querySelector('[data-id=legado_teste]').click()")
            until(c, "!document.querySelector('.stacked-bars') && !!document.querySelector('.hero-card')")
            # Muitas categorias e um centavo: acessíveis sem engrossar as barras.
            c.js("""
                var dense = document.createElement('section'); document.body.appendChild(dense);
                var denseRows = Array.from({length:30}, (_,i)=>({periodo:'2026-11',serie:i===0?'Principal':i===29?'<img src=x onerror=alert(1)>': 'Categoria '+i,valor:i===0?1000000:.01,quantidade:1}));
                var selections=[];
                var denseView=DashboardStackedBars.mount(dense,denseRows,{periodoFiltrado:{dataInicio:'2026-11-01',dataFim:'2026-11-30'}},{change:async selection=>{selections.push(selection);return {total:1}}});
                dense.querySelector('[data-stack-breakdown]').dispatchEvent(new MouseEvent('click',{bubbles:true}));
            """)
            assert c.js("dense.querySelectorAll('.stacked-category').length") == 30
            assert c.js("!dense.querySelector('img') && dense.querySelector('.stacked-category-search') === document.activeElement")
            c.js("var search=dense.querySelector('input');search.value='inexistente';search.dispatchEvent(new Event('input'))")
            assert c.js("dense.querySelectorAll('.stacked-category:not([hidden])').length === 0 && !dense.querySelector('.stacked-category-empty').hidden")
            c.js("var search=dense.querySelector('input');search.value='Categoria 17';search.dispatchEvent(new Event('input'))")
            assert c.js("dense.querySelectorAll('.stacked-category:not([hidden])').length") == 1
            assert c.js("dense.querySelector('.stacked-category:not([hidden])').textContent.includes('R$ 0,01')")
            c.js("dense.querySelector('.stacked-category:not([hidden])').click()")
            until(c, "selections.length === 1 && !dense.querySelector('[data-stack-clear]').disabled")
            assert c.js('selections[0]') == dict(periodo='2026-11', serie='Categoria 17')
            c.js("dense.querySelector('[data-stack-breakdown]').dispatchEvent(new MouseEvent('click',{bubbles:true}));document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape',bubbles:true}))")
            assert c.js("dense.querySelector('.stacked-breakdown').hidden")
            c.js('denseView.destroy();dense.remove()')
            assert not c.errors, c.errors
            print('OK: sem legenda inferior, faixas proporcionais, tooltip, categorias por mês, clique abre títulos, paginação/totais, teclado, 30 categorias com um centavo, falha recuperável e celular.')
        finally:
            proc.terminate()
            proc.wait(timeout=10)
            server.shutdown()
