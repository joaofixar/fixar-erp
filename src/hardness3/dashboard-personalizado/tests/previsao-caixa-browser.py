"""Tela real em Chrome local com registros fictícios, sem banco de cliente."""
import ast,base64,http.server,json,os,pathlib,socket,struct,subprocess,tempfile,threading,time,urllib.parse,urllib.request
root=pathlib.Path(__file__).resolve().parents[1]
source=ast.parse((root/'tests/painel-cotacoes-browser.py').read_text())
client=next(n for n in source.body if isinstance(n,ast.ClassDef) and n.name=='CDP')
exec(compile(ast.Module(body=[client],type_ignores=[]),'<CDP>','exec'))
i=json.loads(subprocess.check_output(['php','-r','echo json_encode(require $argv[1]);',str(root/'tests/fixtures/previsao-caixa.php')]))
definition={k:i[k] for k in ['id','titulo','subtitulo','icone','cor','filtros','card','grafico','composicao','listagem']}
definition['previsaoCaixa']=True
definition['grafico']['periodoFiltrado']=dict(dataInicio='2026-10-01',dataFim='2026-11-30')
for f in definition['filtros']:
    f['valor']={'dataInicio':'2026-10-01','dataFim':'2026-11-30'}.get(f['id'],f['padrao'])
state=dict(rows=[],next=1,fail=False)
def payload(query):
    bars=[dict(periodo='2026-10-20',rotulo='20/10',serie=s,valor=v) for s,v in [('receber',100),('pagar',50),('pedidos',200)]]
    bars += [dict(periodo=r['data'],rotulo='21/10',serie='previsao_pagar',valor=r['valor']) for r in state['rows']]
    return dict(indicador=definition,card=dict(valor=0,quantidade=0,formato='moeda'),grafico=bars,
        previsaoCaixa=dict(lancamentos=state['rows'],pedidosPendentes=[123]),listagem=[],updatedAt='15/10/2026 12:00',
        graficoDetalhes=[dict(documento='Pedido 1 / parcela 1',descricao='Cliente fictício',valor=200,data='2026-10-20',dataProjetada='2026-10-20')])
class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self,*a,**kw):super().__init__(*a,directory=str(root),**kw)
    def log_message(self,*a):pass
    def send(self,data,code=200):
        self.send_response(code);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(data).encode())
    def do_GET(self):
        if self.path.startswith('/data.php?'):
            query=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            self.send(dict(titulo='Teste fictício',indicadores=[definition],podeGerenciarAcessos=False) if query.get('action')==['configuration'] else payload(query));return
        if self.path=='/static/img/logo_hardness.png':
            self.send_response(200);self.end_headers();self.wfile.write((root.parent/'static/img/logo_hardness.png').read_bytes());return
        super().do_GET()
    def do_POST(self):
        entry=json.loads(self.rfile.read(int(self.headers['Content-Length'])))
        if 'saveCashForecast' not in self.path:self.send(dict(ok=True));return
        if state['fail']:self.send(dict(error='Falha simulada ao salvar.'),422);return
        if entry.get('id'):state['rows'][:]=[r for r in state['rows'] if r['id']!=entry['id']]
        if not entry.get('remover'):
            entry['id']=entry.get('id') or str(state['next']);state['next']+=1
            state['rows'].append(dict(entry,empresa=1,editavel=True))
        self.send(dict(ok=True))
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler);threading.Thread(target=server.serve_forever,daemon=True).start()
out=pathlib.Path('/tmp/previsao-caixa-preview');out.mkdir(exist_ok=True)
def until(c,expression):
    for _ in range(120):
        if c.js(expression):return
        time.sleep(.05)
    raise AssertionError(expression)
with tempfile.TemporaryDirectory(prefix='previsao-caixa-browser-') as profile:
    with (out/'chrome.log').open('w') as log:
        proc=subprocess.Popen([os.environ['COTACOES_CHROME'],'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
        try:
            active=pathlib.Path(profile)/'DevToolsActivePort'
            for _ in range(100):
                if active.exists():break
                time.sleep(.05)
            port=int(active.read_text().splitlines()[0]);pages=json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port))
            c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable')
            c.call('Emulation.setDeviceMetricsOverride',dict(width=1700,height=1100,deviceScaleFactor=1,mobile=False))
            c.call('Page.navigate',dict(url='http://127.0.0.1:%s/index.html'%server.server_port))
            until(c,"!!document.querySelector('.cash-forecast-form')")
            assert c.js("document.querySelectorAll('.cash-flow-metric').length") == 4
            assert c.js("document.querySelector('.cash-forecast-warning').textContent.includes('123')")
            c.js("var form=document.querySelector('.cash-forecast-form');form.elements.descricao.value='<img src=x onerror=alert(1)>';form.elements.data.value='2026-10-21';form.elements.valor.value='123.45';form.requestSubmit()")
            until(c,"!!document.querySelector('[data-edit]')")
            assert state['rows'][0]['valor']==123.45
            assert c.js("!document.querySelector('.cash-forecast tbody img')")
            assert c.js("document.querySelector('.cash-flow-summary').textContent.includes('123,45')")
            c.js("document.querySelector('[data-edit]').click();document.querySelector('.cash-forecast-form').elements.valor.value='250';document.querySelector('.cash-forecast-form').requestSubmit()")
            until(c,"!!document.querySelector('.cash-forecast tbody') && document.querySelector('.cash-forecast tbody').textContent.includes('250,00')")
            assert state['rows'][0]['valor']==250
            c.js("document.querySelector('[data-cash-flow-granularity=month]').click()")
            assert c.js("document.querySelectorAll('.cash-flow-metric').length") == 4
            c.js("document.querySelector('[data-cash-flow-series=pedidos]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
            until(c,"document.querySelector('#indicatorDetailContent').textContent.includes('Pedido 1')")
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            state['fail']=True
            c.js("document.querySelector('[data-remove]').click()")
            until(c,"document.querySelector('.cash-forecast-status').textContent.includes('Falha simulada')")
            assert len(state['rows'])==1
            state['fail']=False
            c.js("document.querySelector('[data-remove]').click()")
            until(c,"!!document.querySelector('.cash-forecast') && !document.querySelector('[data-remove]')")
            assert not state['rows']
            c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True))
            assert c.js("document.documentElement.scrollWidth <= window.innerWidth"), 'Sem rolagem horizontal da página'
            assert not c.errors, c.errors
            image=c.call('Page.captureScreenshot',dict(format='png',captureBeyondViewport=True));(out/'fluxo-caixa.png').write_bytes(base64.b64decode(image['data']))
            print('OK: tela real, quatro séries, inclusão/edição/remoção, falha de gravação, escape de HTML, agrupamento, detalhe e viewport móvel.')
        finally:proc.terminate();proc.wait(timeout=10);server.shutdown()
