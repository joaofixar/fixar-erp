"""Teste Chrome local: carregamento original com lotes HTTP 202 invisíveis."""
import ast
import pathlib
# Reutilizar somente fixture/handler/CDP, sem executar o teste de carteira.
base=pathlib.Path(__file__).with_name('desempenho-comercial-carteira-browser.py')
tree=ast.parse(base.read_text());nodes=[]
for n in tree.body:
    if isinstance(n,ast.Assign) and any(isinstance(t,ast.Name) and t.id=='server' for t in n.targets):break
    nodes.append(n)
exec(compile(ast.Module(body=nodes,type_ignores=[]),str(base),'exec'))
definition['carregamentoProgressivo']=True
result['cargaToken']='a'*32
mode={'complete':False,'steps':0}
# A largura muda a cada frame; comparar a estrutura, textos e classes da carga.
loading_snapshot_js="""(()=>{const node=document.getElementById('indicatorLoading').cloneNode(true);const bar=node.querySelector('.indicator-loading-progress i');if(bar)bar.removeAttribute('style');return node.innerHTML;})()"""
normal=dict(id='padrao',titulo='Indicador padrão',filtros=[],abas=[],card=dict(habilitada=False),grafico=dict(habilitado=False),listagem=dict(habilitada=False))
OriginalHandler=Handler
class Handler(OriginalHandler):
    def do_GET(self):
        if self.path.startswith('/data.php?'):
            q=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            if q.get('action')==['configuration']:
                self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(dict(titulo='Painel',indicadores=[definition,normal],podeGerenciarAcessos=False)).encode());return
            if q.get('action')==['indicator'] and q.get('id')!=['padrao'] and (not mode['complete'] or not q.get('cargaToken')):
                mode['steps']+=1
                time.sleep(.06)
                self.send_response(202);self.send_header('Content-Type','application/json');self.end_headers()
                try:self.wfile.write(json.dumps(dict(carregamento=dict(token='a'*32,intervalo=1,intervalos=25,registros=2000*mode['steps']))).encode())
                except BrokenPipeError:pass
                return
        super().do_GET()
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler)
threading.Thread(target=server.serve_forever,daemon=True).start()
browser=os.environ['COTACOES_CHROME']
with tempfile.TemporaryDirectory(prefix='dashboard-progressivo-browser-') as profile:
    with (out/'progressivo-chrome.log').open('w') as log:
        proc=subprocess.Popen([browser,'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
        try:
            active=pathlib.Path(profile)/'DevToolsActivePort'
            for _ in range(100):
                if active.exists():break
                time.sleep(.05)
            port=int(active.read_text().splitlines()[0]);pages=json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port))
            c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable')
            c.call('Page.navigate',dict(url='http://127.0.0.1:%s/index.html'%server.server_port))
            for _ in range(100):
                if mode['steps']>=2:break
                time.sleep(.05)
            assert c.js("document.querySelectorAll('.commercial-kpi').length") == 0,'202 não exibe valores parciais'
            assert c.js("!document.querySelector('.progressive-controls') && !document.querySelector('.progressive-indeterminate')"), 'Sem elementos visuais novos'
            original_loading = c.js(loading_snapshot_js)
            image=c.call('Page.captureScreenshot',dict(format='png'));(out/'carregamento-padrao.png').write_bytes(base64.b64decode(image['data']))
            assert c.js("!document.getElementById('indicatorLoading').hidden"), 'Carregamento original permanece enquanto consulta lotes'
            assert not c.js("document.getElementById('indicatorLoading').textContent.includes('registros consultados') || document.getElementById('indicatorLoading').textContent.includes('Cancelar consulta')")
            assert c.js("Array.from(document.querySelectorAll('[data-export]')).every(b=>b.disabled)")
            mode['complete']=True
            for _ in range(100):
                if c.js("document.querySelectorAll('.commercial-kpi').length===6"):break
                time.sleep(.05)
            assert c.js("document.querySelectorAll('.commercial-kpi').length") == 6
            c.js("document.querySelector('[data-export=csv]').click()")
            for _ in range(100):
                if exports:break
                time.sleep(.02)
            assert exports[-1]['cargaToken']==['a'*32],'CSV usa snapshot concluído'
            c.js("document.querySelector('[data-id=padrao]').click()")
            assert c.js(loading_snapshot_js) == original_loading, 'Mesmo conteúdo visual do carregamento do indicador padrão'
            assert not c.errors,c.errors
            print('OK: carregamento original, sem botões/textos adicionais, lotes completos e exportação do snapshot.')
        finally:
            proc.terminate();proc.wait(timeout=10);server.shutdown()
