"""Chrome local com respostas artificiais; não acessa banco nem ERP de cliente."""
import ast, base64, http.server, json, os, pathlib, socket, struct, subprocess, tempfile, threading, time, urllib.parse, urllib.request

ROOT = pathlib.Path(__file__).resolve().parents[1]
tree = ast.parse((ROOT / 'tests/painel-cotacoes-browser.py').read_text())
client = next(n for n in tree.body if isinstance(n, ast.ClassDef) and n.name == 'CDP')
exec(compile(ast.Module(body=[client], type_ignores=[]), '<CDP>', 'exec'))
state = dict(root=True, error=False, progressive=False, calls=0, delay=0, invalid=False, render_error=False, warning=False, panel=False, panel_invalid=False)
definition = dict(id='sql_fixture', titulo='Faturamento', filtros=[], abas=[], card=dict(titulo='Total'), composicao=dict(habilitada=False), grafico=dict(habilitado=False), listagem=dict(habilitada=False))
query = dict(ordem=1, tempoMs=1200, linhasRetornadas=1, sql="SELECT '<img src=x onerror=window.injetado=true>' AS valor", parametros=dict(empresa=1), erro='', mysql=None)
trace = dict(titulo='Faturamento', indicador='sql_fixture', aba='', quantidadeConsultas=1, tempoSqlMs=1200, tempoServidorMs=1300, tempoForaSqlMs=100, consultas=[query], configuracaoJson=json.dumps(definition))

class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs): super().__init__(*args, directory=str(ROOT), **kwargs)
    def log_message(self, *args): pass
    def do_GET(self):
        path = urllib.parse.urlparse(self.path)
        if path.path == '/data.php':
            state['calls'] += 1
            params = urllib.parse.parse_qs(path.query)
            status = 200
            if params.get('action') == ['configuration']:
                d = dict(definition, carregamentoProgressivo=state['progressive'])
                if state['panel']: d['painelCotacoes'] = True
                data = dict(titulo='Dashboard', indicadores=[d], podeGerenciarAcessos=True, podeAnalisarSql=state['root'])
            else:
                if state['delay']: time.sleep(state['delay'])
                if state['invalid']:
                    self.send_response(502); self.send_header('Content-Type', 'text/html'); self.end_headers(); self.wfile.write(b'<h1>Bad Gateway</h1>'); return
                data = dict(indicador=definition, card=dict(valor=123, quantidade=1, formato='numero'), listagem=[], updatedAt='23/10/2026')
                diag = dict(trace)
                diag['aberturaId'] = params.get('diagnosticoCarga', [''])[0]
                if state['warning']: diag['avisos'] = [dict(componente='desempenhoComercial.resumo.metaAviso', mensagem='Meta mensal duplicada')]
                if state['render_error']: data['card'] = None
                if state['panel']:
                    data['painelCotacoes'] = dict(habilitado=True)
                    if 'cotacoesDados' in params and not state['panel_invalid']:
                        data['painelCotacoes'].update(periodo=dict(inicio='2026-10-01', fim='2026-10-23'), hoje='2026-10-23', fontes=dict(orcamentos=[],pedidos=[],notas=[]), vendedores=[], equipes=[], empresas=[], escopo='todos', origemIA=True, calendario={}, meta=None, metasUsuario={})
                if state['progressive'] and 'cargaToken' not in params:
                    status = 202
                    data = dict(carregamento=dict(token='a'*32, fontesConcluidas=1, intervalo=1, intervalos=2))
                elif state['error']:
                    status = 422
                    data = dict(error='Não foi possível executar a consulta configurada.')
                    diag['erro'] = data['error']
                    failed = dict(query, erro=data['error'], mysql=dict(codigo=1054, mensagem="Unknown column 'campo_inexistente'"))
                    diag['consultas'] = [failed]
                    diag['ultimaConsultaComErro'] = failed
                if state['root']: data['analiseSql'] = diag
            self.send_response(status); self.send_header('Content-Type', 'application/json'); self.end_headers(); self.wfile.write(json.dumps(data).encode()); return
        if path.path == '/static/img/logo_hardness.png':
            self.send_response(200); self.end_headers(); self.wfile.write((ROOT.parent/'static/img/logo_hardness.png').read_bytes()); return
        if path.path == '/company-logo.php': self.send_response(404); self.end_headers(); return
        super().do_GET()

server = http.server.ThreadingHTTPServer(('127.0.0.1', 0), Handler)
threading.Thread(target=server.serve_forever, daemon=True).start()
browser = os.environ.get('COTACOES_CHROME', '/root/.cache/puppeteer/chrome-headless-shell/linux-146.0.7680.31/chrome-headless-shell-linux64/chrome-headless-shell')
with tempfile.TemporaryDirectory(prefix='dashboard-sql-browser-') as profile, tempfile.TemporaryFile() as log:
    proc = subprocess.Popen([browser, '--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage', '--remote-debugging-port=0', '--user-data-dir='+profile, 'about:blank'], stdout=log, stderr=log)
    try:
        active = pathlib.Path(profile)/'DevToolsActivePort'
        for _ in range(150):
            if active.exists(): break
            if proc.poll() is not None: break
            time.sleep(.05)
        if not active.exists():
            log.seek(0)
            raise RuntimeError('Chrome não iniciou: ' + log.read().decode(errors='replace'))
        port = int(active.read_text().splitlines()[0])
        pages = json.load(urllib.request.urlopen(f'http://127.0.0.1:{port}/json'))
        c = CDP(pages[0]['webSocketDebuggerUrl']); c.call('Runtime.enable'); c.call('Page.enable')
        url = f'http://127.0.0.1:{server.server_port}/index.html'
        def wait(expression):
            for _ in range(200):
                if c.js('(function(){try{return !!('+expression+');}catch(e){return false;}})()'): return
                time.sleep(.03)
            raise AssertionError(expression)
        def report(): return c.js("JSON.parse(document.getElementById('sqlAnalysisText').value).registros")
        def openings(): return c.js("JSON.parse(document.getElementById('sqlAnalysisText').value).aberturas")
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('indicatorLoading').hidden && !document.getElementById('sqlAnalysis').hidden")
        before = state['calls']
        c.js("document.getElementById('sqlAnalysis').click()")
        assert len(report()) == 1
        assert len(openings()) == 1 and openings()[0]['estado'] == 'concluido'
        assert openings()[0]['requisicoes'] == 1 and openings()[0]['tempoSqlMs'] == 1200
        assert report()[0]['http']['tipoConteudo'] == 'application/json'
        c.js("document.querySelector('#sqlAnalysisReports details').open=true")
        assert not c.js('!!window.injetado'), 'SQL deve aparecer como texto'
        assert c.js("document.getElementById('sqlAnalysisReports').textContent.includes('1.200 ms')")
        assert state['calls'] == before, 'Abrir painel não faz requisição'
        c.js("Object.defineProperty(navigator,'clipboard',{configurable:true,value:{writeText:async text=>{window.copiado=text;}}});document.getElementById('copySqlAnalysis').click()")
        wait("!!window.copiado")
        assert json.loads(c.js('window.copiado'))['registros'][0]['consultas'][0]['sql'] == query['sql']
        c.js("document.getElementById('closeSqlAnalysis').click()")
        assert c.js("document.activeElement.id==='sqlAnalysis'")
        state['error'] = True
        c.js("document.getElementById('refreshDashboard').click()")
        wait("document.getElementById('sqlAnalysis').textContent.includes('1 erro')")
        c.js("document.getElementById('sqlAnalysis').click();document.getElementById('sqlAnalysisFilter').value='errors';document.getElementById('sqlAnalysisFilter').dispatchEvent(new Event('change'))")
        assert len(report()) == 1 and report()[0]['ultimaConsultaComErro']['mysql']['codigo'] == 1054
        before = state['calls']
        c.js("document.getElementById('clearSqlAnalysis').click()")
        assert report() == [] and state['calls'] == before
        c.js("document.getElementById('sqlAnalysisFilter').value='all';document.getElementById('sqlAnalysisFilter').dispatchEvent(new Event('change'))")
        c.js("for(let i=0;i<30;i++)DashboardSqlDiagnostics.receive({analiseSql:{indicador:'limite',consultas:[]}},'data.php',200,performance.now())")
        assert len(report()) == 20
        c.js("for(let i=0;i<20;i++)DashboardSqlDiagnostics.receive({analiseSql:{indicador:'memoria',configuracaoJson:'x'.repeat(100000),consultas:[]}},'data.php',200,performance.now())")
        assert 1 <= len(report()) <= 5, 'Limite de 1 MB'
        c.call('Emulation.setDeviceMetricsOverride', dict(width=390, height=844, deviceScaleFactor=1, mobile=False))
        assert c.js("document.querySelector('.sql-analysis-dialog').getBoundingClientRect().width <= innerWidth")
        c.js("document.getElementById('sqlAnalysisModal').dispatchEvent(new KeyboardEvent('keydown',{key:'Escape',bubbles:true}))")
        assert c.js("document.getElementById('sqlAnalysisModal').hidden")
        state['root'] = False
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('indicatorLoading').hidden && document.getElementById('manageAccess').hidden===false")
        assert c.js("document.getElementById('sqlAnalysis').hidden"), 'Gestor de acessos não é ROOT'
        c.js("document.getElementById('sqlAnalysis').hidden=false;document.getElementById('sqlAnalysis').click()")
        assert c.js("!document.getElementById('sqlAnalysisModal')"), 'Forçar botão não habilita diagnóstico'
        state.update(root=True, progressive=True)
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('sqlAnalysis').textContent.includes('1 erro')")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert [r['httpStatus'] for r in report()] == [202, 422], 'Capturar etapa progressiva e falha final'
        assert openings()[0]['estado'] == 'erro' and openings()[0]['requisicoes'] == 2
        assert openings()[0]['tempoSqlMs'] == 2400, 'Somar todas as etapas da abertura'

        state.update(error=False, progressive=False, delay=1.1)
        c.call('Page.navigate', dict(url=url)); wait("!document.getElementById('sqlAnalysis').hidden")
        c.js("document.getElementById('sqlAnalysis').click()")
        wait("document.getElementById('sqlAnalysisText').value.includes('carregando')")
        assert openings()[0]['estado'] == 'carregando', 'Abertura pendente deve existir antes da resposta'
        wait("document.getElementById('indicatorLoading').hidden")
        wait("document.getElementById('sqlAnalysisText').value.includes('concluido')")
        assert openings()[0]['tempoTotalMs'] >= 1100
        assert openings()[0]['tempoMontagemMs'] >= 0 and openings()[0]['tempoForaRequisicoesMs'] >= 0
        c.js("window.dispatchEvent(new ErrorEvent('error',{error:new Error('Falha depois de abrir'),filename:location.origin+'/dashboard.js'}))")
        assert openings()[0]['estado'] == 'erro'
        assert openings()[0]['erroFinal']['origem'] == 'navegador'
        assert openings()[0]['erroFinal']['mensagem'] == 'Falha depois de abrir'

        state.update(delay=0, invalid=True)
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('sqlAnalysis').textContent.includes('erro')")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert report()[0]['httpStatus'] == 502 and report()[0]['http']['tipoConteudo'] == 'text/html'
        assert openings()[0]['estado'] == 'erro'

        state.update(invalid=False, render_error=True)
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('sqlAnalysis').textContent.includes('erro')")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert report()[0]['httpStatus'] == 200, 'SQL pode responder com sucesso antes da falha de montagem'
        assert openings()[0]['estado'] == 'erro' and openings()[0]['erroFinal']['origem'] == 'navegador'
        assert 'dashboard.js' in openings()[0]['erroFinal']['pilha']

        state.update(render_error=False, warning=True)
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('indicatorLoading').hidden && !document.getElementById('sqlAnalysis').hidden")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert openings()[0]['estado'] == 'aviso' and openings()[0]['avisos'][0]['mensagem'] == 'Meta mensal duplicada'
        assert 'AVISO' in c.js("document.getElementById('sqlAnalysisReports').textContent")
        state.update(warning=False, panel=True)
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('cotacoes-main').getAttribute('aria-busy')==='false'")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert openings()[0]['estado'] == 'concluido' and openings()[0]['requisicoes'] >= 2, 'Incluir montagem assíncrona e carga interna de Cotações'
        state['panel_invalid'] = True
        c.call('Page.navigate', dict(url=url)); wait("document.getElementById('sqlAnalysis').textContent.includes('erro')")
        c.js("document.getElementById('sqlAnalysis').click()")
        assert openings()[0]['estado'] == 'erro' and 'JSON do Painel de Cotações' in openings()[0]['erroFinal']['mensagem']
        assert not c.errors, c.errors
        print('OK: ROOT/gestor, cópia, limites, abertura pendente/lenta, totais progressivos, HTTP 502/JSON inválido, erro de montagem com HTTP 200, erro após abrir, aviso parcial e nenhuma requisição extra.')
    finally:
        proc.terminate(); proc.wait(timeout=10); server.shutdown()
