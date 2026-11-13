"""Tela do PCP com o comparativo calculado pelo PHP sobre registros fictícios."""
import ast,base64,http.server,json,os,pathlib,socket,struct,subprocess,tempfile,threading,time,urllib.parse,urllib.request

root=pathlib.Path(__file__).resolve().parents[1]
source=ast.parse((root/'tests/painel-cotacoes-browser.py').read_text())
client=next(n for n in source.body if isinstance(n,ast.ClassDef) and n.name=='CDP')
exec(compile(ast.Module(body=[client],type_ignores=[]),'<CDP>','exec'))
result=json.loads(subprocess.check_output(['php',str(root/'tests/comparativo-periodos.php'),'--fixture']))
definition=result['indicador']
requests=[]

class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self,*args,**kwargs):super().__init__(*args,directory=str(root),**kwargs)
    def log_message(self,*args):pass
    def do_POST(self):
        self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(b'{"ok":true}')
    def do_GET(self):
        if self.path.startswith('/data.php?'):
            query=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query,keep_blank_values=True)
            requests.append(query)
            data=dict(titulo='PCP · Dados fictícios',indicadores=[definition],podeGerenciarAcessos=False) if query.get('action')==['configuration'] else result
            self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(data).encode());return
        if self.path=='/static/img/logo_hardness.png':
            self.send_response(200);self.end_headers();self.wfile.write((root.parent/'static/img/logo_hardness.png').read_bytes());return
        super().do_GET()

server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler)
threading.Thread(target=server.serve_forever,daemon=True).start()
out=pathlib.Path('/tmp/pcp-comparativo-preview');out.mkdir(exist_ok=True)

def until(c,expression):
    for _ in range(150):
        if c.js(expression):return
        time.sleep(.05)
    raise AssertionError(expression)

with tempfile.TemporaryDirectory(prefix='pcp-comparativo-browser-') as profile:
    with (out/'chrome.log').open('w') as log:
        proc=subprocess.Popen([os.environ['COTACOES_CHROME'],'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
        try:
            active=pathlib.Path(profile)/'DevToolsActivePort'
            for _ in range(100):
                if active.exists():break
                time.sleep(.05)
            port=int(active.read_text().splitlines()[0])
            pages=json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port))
            c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable')
            c.call('Emulation.setDeviceMetricsOverride',dict(width=1440,height=1100,deviceScaleFactor=1,mobile=False))
            c.call('Page.navigate',dict(url='http://127.0.0.1:%s/index.html'%server.server_port))
            until(c,"document.querySelectorAll('[data-period-series]').length===3")
            assert c.js("document.querySelectorAll('.period-comparison-legend > div').length")==3
            text=c.js("document.querySelector('.period-comparison').textContent")
            assert '36.133,638 kg' in text and '28.906,914 kg' in text and '23.486,867 kg' in text
            assert c.js("document.querySelectorAll('.period-comparison-values tbody tr').length")==19
            assert c.js("!document.querySelector('.period-comparison').textContent.includes('T257')"),'Texto de uso sem detalhes internos'
            c.js("document.querySelector('.period-comparison-values summary').click()")
            assert c.js("document.querySelector('.period-comparison-values').open")
            c.js("document.querySelector('.period-comparison-values summary').click()")
            until(c,"!document.querySelector('.period-comparison-values').open")
            # O filtro do painel continua na requisição que atualiza todas as séries.
            before=len(requests)
            c.js("document.querySelector('[name=f_maquina]').value='0';document.getElementById('indicatorFilters').requestSubmit()")
            until(c,"document.querySelectorAll('[data-period-series]').length===3 && !document.querySelector('#indicatorLoading:not([hidden])')")
            assert len(requests)>before and requests[-1]['f_maquina']==['0']
            c.js("document.querySelector('.period-comparison').scrollIntoView({block:'start'})")
            shot=c.call('Page.captureScreenshot',dict(format='png'))
            (out/'comparativo-desktop.png').write_bytes(base64.b64decode(shot['data']))
            (out/'comparativo-dados-ficticios.png').write_bytes(base64.b64decode(shot['data']))
            c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True))
            assert c.js("document.documentElement.scrollWidth<=window.innerWidth"),'Página móvel sem transbordamento'
            assert c.js("document.querySelector('.period-comparison .chart-scroll').scrollWidth > document.querySelector('.period-comparison .chart-scroll').clientWidth"),'Rolagem limitada ao gráfico'
            c.js("document.querySelector('.period-comparison').scrollIntoView({block:'start'})")
            assert not c.errors,c.errors
            shot=c.call('Page.captureScreenshot',dict(format='png'))
            (out/'comparativo-mobile.png').write_bytes(base64.b64decode(shot['data']))
            print('OK: três linhas e totais reais da fixture PHP, detalhes diários, filtro do painel, desktop e celular sem erros JavaScript.')
        finally:
            proc.terminate();proc.wait(timeout=10);server.shutdown()
