"""Navegador local: flag de gestor, salvar e painel limitado, sem ERP."""
import ast
import pathlib
base=pathlib.Path(__file__).with_name('desempenho-comercial-carteira-browser.py')
tree=ast.parse(base.read_text());nodes=[]
for n in tree.body:
    if isinstance(n,ast.Assign) and any(isinstance(t,ast.Name) and t.id=='server' for t in n.targets):break
    nodes.append(n)
exec(compile(ast.Module(body=nodes,type_ignores=[]),str(base),'exec'))
state={'root':True,'saved':[],'acl':dict(perfisGestores=[],perfis={'10':[]},usuarios={'77':dict(indicadores=[definition['id'],'cotacoes'],restricoes={},gestor=False),'7':dict(indicadores=[],restricoes={},gestor=False)})}
defs=[definition,dict(definition,id='cotacoes',titulo='Painel de Cotações'),dict(definition,id='receber',titulo='Contas a receber')]
OriginalHandler=Handler
class Handler(OriginalHandler):
    def sendjson(self,data):
        self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(data).encode())
    def do_POST(self):
        if self.path.startswith('/data.php?action=saveAccess'):
            data=json.loads(self.rfile.read(int(self.headers['Content-Length'])));state['saved'].append(data);state['acl']=data;self.sendjson(dict(ok=True));return
        self.send_error(404)
    def do_GET(self):
        if self.path.startswith('/data.php?'):
            q=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            if q.get('action')==['configuration']:
                self.sendjson(dict(titulo='Painel',indicadores=defs if state['root'] else defs[:2],podeGerenciarAcessos=True));return
            if q.get('action')==['access']:
                self.sendjson(dict(indicadores=[dict(id=d['id'],titulo=d['titulo']) for d in (defs if state['root'] else defs[:2])],perfis=[dict(id=10,nome='Comercial')],usuarios=[dict(id=77,nome='William'),dict(id=7,nome='Rafaela')],vendedores=[],indicadoresComRestricaoVendedor={},acessos=state['acl'],root=state['root'],edicaoLimitada=not state['root'],usuarioAtual=63 if state['root'] else 77));return
        super().do_GET()
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler);threading.Thread(target=server.serve_forever,daemon=True).start()
with tempfile.TemporaryDirectory(prefix='acl-user-manager-browser-') as profile:
    with (out/'gestor-chrome.log').open('w') as log:
        proc=subprocess.Popen([os.environ['COTACOES_CHROME'],'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
        try:
            active=pathlib.Path(profile)/'DevToolsActivePort'
            for _ in range(100):
                if active.exists():break
                time.sleep(.05)
            port=int(active.read_text().splitlines()[0]);pages=json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port));c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable')
            url='http://127.0.0.1:%s/index.html'%server.server_port
            def wait(expression):
                for _ in range(100):
                    if c.js('(function(){try{return !!('+expression+');}catch(e){return false;}})()'):return
                    time.sleep(.03)
                raise AssertionError(expression)
            c.call('Page.navigate',dict(url=url));wait("!document.getElementById('manageAccess').hidden")
            c.js("document.getElementById('manageAccess').click()");wait("!document.getElementById('accessModal').hidden")
            assert c.js("!!document.getElementById('accessManager')"),'Perfil responsável continua disponível'
            c.js("document.querySelector('[data-access-mode=usuarios]').click();document.querySelector('[data-subject-id=\"77\"]').click()")
            assert c.js("!!document.getElementById('accessUserManager') && !document.getElementById('accessUserManager').checked")
            c.js("document.getElementById('accessUserManager').click();document.querySelector('[data-subject-id=\"7\"]').click();document.querySelector('[data-subject-id=\"77\"]').click()")
            assert c.js("document.getElementById('accessUserManager').checked"),'Flag é preservada ao trocar usuário'
            c.js("document.getElementById('saveAccess').click()");wait("document.getElementById('accessModal').hidden")
            assert state['saved'][-1]['usuarios']['77']['gestor'] is True
            assert set(state['saved'][-1]['usuarios']['77']['indicadores'])=={definition['id'],'cotacoes'}
            state['root']=False;c.call('Page.navigate',dict(url=url));wait("document.querySelectorAll('[data-id]').length===2")
            c.js("document.getElementById('manageAccess').click()");wait("!document.getElementById('accessModal').hidden")
            c.js("document.querySelector('[data-access-mode=usuarios]').click();document.querySelector('[data-subject-id=\"77\"]').click()")
            assert c.js("!document.getElementById('accessUserManager') && !document.getElementById('accessManager')")
            assert c.js("Array.from(document.querySelectorAll('#accessDetail input,#accessDetail select')).every(e=>e.disabled)"),'Gestor não edita próprio acesso'
            c.js("document.querySelector('[data-subject-id=\"7\"]').click()")
            assert c.js("document.querySelectorAll('.indicator-access-list input').length") == 2
            c.js("document.querySelector('.indicator-access-list input').click();document.getElementById('saveAccess').click()")
            wait("document.getElementById('accessModal').hidden")
            assert state['saved'][-1]['usuarios']['77']['gestor'] is True
            assert state['saved'][-1]['usuarios']['7']['indicadores']==[definition['id']]
            assert not c.errors,c.errors
            print('OK: flag por usuário, persistência na UI, dois indicadores, acesso próprio bloqueado e distribuição a outro usuário.')
        finally:
            proc.terminate();proc.wait(timeout=10);server.shutdown()
