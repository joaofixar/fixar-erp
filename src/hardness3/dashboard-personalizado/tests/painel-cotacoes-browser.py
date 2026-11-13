"""Chrome via CDP, somente HTTP local e dados artificiais; sem bibliotecas externas."""
import base64
import csv
import hashlib
import http.server
import io
import json
import os
import pathlib
import socket
import shutil
import struct
import subprocess
import tempfile
import threading
import time
import urllib.parse
import urllib.request

ROOT=pathlib.Path(__file__).resolve().parents[1]
BROWSER=os.environ.get('COTACOES_CHROME') or next((shutil.which(name) for name in ('chromium', 'chromium-browser', 'google-chrome', 'chrome-headless-shell') if shutil.which(name)), None)
if not BROWSER:
    raise SystemExit('Informe COTACOES_CHROME com o caminho do Chrome/Chromium para executar o teste local.')
OUT=pathlib.Path(os.environ.get('COTACOES_SCREENSHOTS',str(pathlib.Path(tempfile.gettempdir())/'dashboard-cotacoes-preview')))
OUT.mkdir(parents=True,exist_ok=True)
vendors=[dict(id=10,nome='Supervisor A',supervisorId=0),dict(id=20,nome='Supervisor B',supervisorId=0),dict(id=1,nome='Vendedor 01',supervisorId=10,supervisor='Supervisor A',supervisor2Id=20),dict(id=2,nome='Vendedor 02',supervisorId=20,supervisor='Supervisor B')]
vendors.extend([dict(id=11,nome='Supervisor A revenda',supervisorId=0),dict(id=30,nome='Supervisor C',supervisorId=0),dict(id=40,nome='Supervisor D',supervisorId=0),dict(id=99,nome='Outro vínculo',supervisorId=999,supervisor='Não é equipe comercial')])
teams=[dict(id='a',nome='Equipe A',empresaId=1,supervisores=[10,11]),dict(id='c',nome='Equipe C',empresaId=1,supervisores=[30]),dict(id='d',nome='Equipe D',empresaId=1,supervisores=[40]),dict(id='b',nome='Equipe B',empresaId=5,supervisores=[20])]
docs=[]
for i in range(1,45):
    seller=[1,2,10][i%3]
    docs.append(dict(id=i,numero=i,data='2026-10-%02d'%(1+i%13),valor=i*1510.5,toneladas=i/10,transferencia=0,excluido=1 if i%11==0 else 0,status='P',ia=1 if i%4==0 else 0,convertido=1 if i%3==0 else 0,vendedorId=seller,vendedor=next(v['nome'] for v in vendors if v['id']==seller),representanteId=5,representante='Representante teste',empresaId=1 if i%2 else 5,clienteId=i,cliente='CLIENTE %02d'%i,grupo=None,proposta=None))
docs[0]['cliente']='<img src=x onerror="window.injetado=true">'
daily=[]
import datetime
start=datetime.date(2024,10,1)
while start<=datetime.date(2026,10,13):
    if start.weekday()<5:
        for seller in [1,2,10]:daily.append(dict(data=start.isoformat(),vendedorId=seller,representanteId=5,empresaId=1 if seller!=2 else 5,valor=1000+seller*10,quantidade=1))
    start+=datetime.timedelta(days=1)
goal_state={'user':7,'values':{},'fail':False}
def payload(history=False):
    return dict(updatedAt='13/10/2026 19:32',painelCotacoes=dict(habilitado=True,periodo=dict(inicio='2026-10-01',fim='2026-10-13'),hoje='2026-10-13',fontes=dict(pedidos=daily,notas=daily) if history else {t:docs for t in ['orcamentos','pedidos','notas']},fontesToneladas=dict(pedidos=[dict(r,valor=r['valor']/1000) for r in daily]) if history else {},vendedores=vendors,equipes=teams,empresas=[dict(id=1,nome='Matriz',toneladas=False),dict(id=5,nome='Vendas Aço',toneladas=True)],escopo='todos',origemIA=True,calendario={},meta=None,metasUsuario=goal_state['values'].get(goal_state['user'],{})))
native_state={'mode':'ok'}
native_routes={'orcamentos':('/ven/ven001/content/ven001ContentEditarOrcamento/','T003'),'pedidos':('/ven/ven002/content/ven002content02/','T005'),'notas':('/ven/ven003/content/ven003contentIncluirNotaFiscal/','T007')}
class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self,*args,**kwargs):super().__init__(*args,directory=str(ROOT),**kwargs)
    def do_POST(self):
        if not self.path.startswith('/data.php?action=saveCotacoesGoal'):self.send_error(404);return
        data=json.loads(self.rfile.read(int(self.headers['Content-Length'])))
        key='|'.join(data[k] for k in ['mes','empresa','equipe','vendedor','representante','unidade'])
        metas=goal_state['values'].setdefault(goal_state['user'],{})
        if goal_state['fail']:result={'error':'Falha simulada ao salvar a meta.'};code=503
        else:
            if data['valor'] is None:metas.pop(key,None)
            else:metas[key]=data['valor']
            result={'chave':key,'valor':data['valor']};code=200
        self.send_response(code);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(result).encode())
    def do_GET(self):
        if self.path.startswith('/ven/'):
            company=5 if native_state['mode']=='session' else 1
            self.send_response(200);self.send_header('Content-Type','text/html');self.end_headers();self.wfile.write(('<!doctype html><html><head><script>var g={usuarioAtual:7,empresaAtual:%s};</script></head><body>Tela nativa simulada</body></html>'%company).encode());return
        if self.path=='/static/img/logo_hardness.png':
            self.send_response(200);self.send_header('Content-Type','image/png');self.end_headers();self.wfile.write((ROOT.parent/'static/img/logo_hardness.png').read_bytes());return
        if self.path.startswith('/data.php?'):
            q=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            if q.get('action')==['cotacoesDocumentContext']:
                company=int(q['documentoEmpresa'][0]);kind=q['documentoTipo'][0];doc=int(q['documentoId'][0]);route,table=native_routes[kind]
                if company!=1:result=dict(error='Selecione a empresa do documento no ERP.');code=422
                else:
                    url=route+'?'+urllib.parse.urlencode(dict(oneWindow='true',acaoId=doc,tabela=table))
                    if native_state['mode']=='url':url+='&p=5'
                    result=dict(url=url,tipo=kind,documentoId=doc,numero=doc,usuario=7,empresaSessao=1);code=200
                self.send_response(code);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(result).encode());return
            definition=dict(id='painel_cotacoes_teste',titulo='Painel de Cotações',subtitulo='Orçamentos, pedidos, faturamento e projeção',filtros=[],abas=[],card=dict(habilitada=False),painelCotacoes=True)
            legacy=dict(id='faturamento_teste',titulo='Faturamento',subtitulo='Indicador padrão de teste',filtros=[],abas=[],card=dict(titulo='Faturamento'),composicao=dict(habilitada=False),grafico=dict(habilitado=False),listagem=dict(habilitada=False))
            if q.get('id')==['faturamento_teste']:
                result=dict(indicador=legacy,card=dict(valor=1234,quantidade=1,formato='moeda'),listagem=[],updatedAt='13/10/2026')
            elif q.get('action')==['configuration']:
                result=dict(titulo='Painel de Indicadores',indicadores=[definition,legacy],podeGerenciarAcessos=False)
            elif 'cotacoesDados' not in q:
                result=dict(indicador=definition,card=dict(valor=0,quantidade=0,formato='moeda'),listagem=[],painelCotacoes=dict(habilitado=True),updatedAt='13/10/2026')
            else:
                result=payload(q.get('cotacoesDados')==['projecao'])
                if q.get('f_dataInicio'):result['painelCotacoes']['periodo']['inicio']=q['f_dataInicio'][0]
                if q.get('f_dataFim'):result['painelCotacoes']['periodo']['fim']=q['f_dataFim'][0]
            body=json.dumps(result).encode()
            self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(body)
        else:super().do_GET()
    def log_message(self,*args):pass
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler)
threading.Thread(target=server.serve_forever,daemon=True).start()
class CDP:
    def __init__(self,url):
        u=urllib.parse.urlparse(url);self.sock=socket.create_connection((u.hostname,u.port));self.sock.settimeout(25);self.next=0;self.errors=[]
        key=base64.b64encode(os.urandom(16)).decode();self.sock.sendall(('GET '+u.path+' HTTP/1.1\r\nHost: '+u.netloc+'\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Key: '+key+'\r\nSec-WebSocket-Version: 13\r\n\r\n').encode())
        headers=b''
        while not headers.endswith(b'\r\n\r\n'):headers+=self.sock.recv(1)
        assert headers.startswith(b'HTTP/1.1 101 '),headers
    def read(self,n):
        data=b''
        while len(data)<n:
            part=self.sock.recv(n-len(data))
            if not part:raise RuntimeError('Chrome fechou a conexão')
            data+=part
        return data
    def recv(self):
        a,b=self.read(2);n=b&127
        if n==126:n=struct.unpack('!H',self.read(2))[0]
        if n==127:n=struct.unpack('!Q',self.read(8))[0]
        return json.loads(self.read(n))
    def call(self,method,params=None):
        self.next+=1;i=self.next;raw=json.dumps(dict(id=i,method=method,params=params or {})).encode();mask=os.urandom(4);size=len(raw)
        header=bytes([129,128|size]) if size<126 else bytes([129,254])+struct.pack('!H',size)
        self.sock.sendall(header+mask+bytes(c^mask[k%4] for k,c in enumerate(raw)))
        while True:
            msg=self.recv()
            if msg.get('method')=='Runtime.exceptionThrown':self.errors.append(msg['params'])
            if msg.get('id')==i:
                if 'error' in msg:raise RuntimeError(msg['error'])
                return msg.get('result',{})
    def js(self,expression):
        r=self.call('Runtime.evaluate',dict(expression=expression,returnByValue=True,awaitPromise=True))
        if 'exceptionDetails' in r:raise RuntimeError(r['exceptionDetails'])
        return r.get('result',{}).get('value')
    def wait(self):self.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(document.getElementById('cotacoes-main').getAttribute('aria-busy')==='false'){clearInterval(t);resolve(true)}else if(n++>300){clearInterval(t);reject('timeout')}},30)})")
with tempfile.TemporaryDirectory(prefix='dashboard-cotacoes-browser-') as profile:
    log=open(OUT/'chrome.log','w')
    proc=subprocess.Popen([BROWSER,'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
    try:
        active=pathlib.Path(profile)/'DevToolsActivePort'
        for _ in range(100):
            if active.exists():break
            time.sleep(.05)
        port=int(active.read_text().splitlines()[0]);pages=json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port));c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable');c.call('Emulation.setDeviceMetricsOverride',dict(width=1700,height=1100,deviceScaleFactor=1,mobile=False));c.call('Page.navigate',dict(url='http://127.0.0.1:%s/painel-cotacoes.html?id=painel_cotacoes_teste'%server.server_port))
        for _ in range(100):
            if c.js("!!document.getElementById('cotacoes-main')"):break
            time.sleep(.02)
        c.wait()
        assert c.js("document.getElementById('cotacoes-error').hidden"),c.js("document.getElementById('cotacoes-error').textContent")
        assert c.js("document.querySelectorAll('#cotacoes-rankBody tr').length")==3
        assert c.js("Array.from(document.querySelectorAll('[data-team]')).map(b=>b.dataset.team)")==['all','a','c','d','b'],'Somente as quatro equipes configuradas, sem supervisores extras'
        assert c.js("document.querySelector('#cotacoes-kpis strong').textContent")=='40','Todas preserva documentos não classificados'
        assert not c.js("document.getElementById('cotacoes-teamNote').hidden")
        assert not c.js("!!window.injetado"),'Escape de cliente'
        assert c.js("document.querySelector('#cotacoes-trend svg')!==null")
        c.js("document.querySelector('[data-team=\"a\"]').click()")
        assert c.js("document.querySelectorAll('#cotacoes-rankBody tr').length")==2
        assert 'Vendedor 02' not in c.js("document.getElementById('cotacoes-rankBody').textContent")
        assert 'Supervisor A' in c.js("document.getElementById('cotacoes-rankBody').textContent"),'Vendas do próprio supervisor permanecem na equipe'
        assert c.js("document.getElementById('cotacoes-teamNote').hidden")
        c.js("document.querySelector('[data-company=\"1\"]').click()")
        assert c.js("document.querySelectorAll('[data-team]').length")==4,'Matriz: Todas mais três equipes'
        c.js("document.querySelector('[data-company=\"5\"]').click()")
        assert c.js("Array.from(document.querySelectorAll('[data-team]')).map(b=>b.dataset.team)")==['all','b'],'Filial: somente sua equipe'
        assert c.js("document.querySelector('[data-team=\"all\"]').classList.contains('active')"),'Troca de empresa limpa equipe incompatível'
        assert c.js("document.getElementById('cotacoes-metricTons').classList.contains('active')"),'Filial abre gráfico em toneladas'
        assert ' t' in c.js("document.getElementById('cotacoes-trend').textContent")
        assert 'Volume de material' in c.js("document.getElementById('cotacoes-kpis').textContent")
        # CSV em toneladas usa a população de itens, inclusive cabeçalho cancelado com item ativo.
        c.js("window.originalUrl=URL.createObjectURL;window.originalClick=HTMLAnchorElement.prototype.click;URL.createObjectURL=b=>{window.exportedBlob=b;return 'blob:teste'};HTMLAnchorElement.prototype.click=function(){};document.getElementById('cotacoes-exportCsv').click()")
        assert any(row[2]=='22' for row in csv.reader(io.StringIO(c.js("window.exportedBlob.text()")),delimiter=';')),'Volume do documento cancelado deve estar no CSV de toneladas'
        c.js("document.querySelector('[data-metric=\"value\"]').click()")
        assert 'R$' in c.js("document.getElementById('cotacoes-trend').textContent")
        c.js("document.getElementById('cotacoes-exportCsv').click()")
        assert not any(row[2]=='22' for row in csv.reader(io.StringIO(c.js("window.exportedBlob.text()")),delimiter=';')),'CSV monetário exclui cabeçalhos cancelados'
        c.js("URL.createObjectURL=window.originalUrl;HTMLAnchorElement.prototype.click=window.originalClick")
        c.js("document.querySelector('[data-company=\"all\"]').click();document.querySelector('[data-client]').click()")
        assert c.js("document.querySelector('.expanded-docs')!==null")
        assert not c.js("!!window.injetado")
        def wait_document():
            c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{const f=document.querySelector('#cotacoes-documentContent iframe');if(f&&f.style.visibility==='visible'){clearInterval(t);resolve(true)}else if(n++>150){clearInterval(t);reject(document.getElementById('cotacoes-documentStatus').textContent)}},30)})")
        def close_document():
            c.js("document.getElementById('cotacoes-documentClose').click()")
            c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(!document.getElementById('cotacoes-documentDialog').open&&!document.querySelector('#cotacoes-documentContent iframe')){clearInterval(t);resolve(true)}else if(n++>150){clearInterval(t);reject('janela não fechou')}},30)})")
            assert c.js("!document.getElementById('cotacoes-documentDialog').open&&document.querySelectorAll('#cotacoes-documentContent iframe').length===0")
        c.js("document.querySelector('[data-document-id] td:nth-child(3)').click()")
        wait_document()
        assert '/ven/ven001/content/ven001ContentEditarOrcamento/' in c.js("document.querySelector('#cotacoes-documentContent iframe').src")
        assert c.js("document.getElementById('cotacoes-documentDialog').open")
        close_document()
        assert c.js("document.activeElement===document.querySelector('[data-document-id] button')"),'Retorna foco e mantém lista expandida'
        # Teclado no número abre a mesma janela; Escape fecha e remove o iframe.
        c.call('Input.dispatchKeyEvent',dict(type='keyDown',key='Enter',code='Enter',windowsVirtualKeyCode=13,text='\r'));c.call('Input.dispatchKeyEvent',dict(type='keyUp',key='Enter',code='Enter',windowsVirtualKeyCode=13))
        wait_document()
        c.call('Input.dispatchKeyEvent',dict(type='keyDown',key='Escape',code='Escape',windowsVirtualKeyCode=27));c.call('Input.dispatchKeyEvent',dict(type='keyUp',key='Escape',code='Escape',windowsVirtualKeyCode=27))
        c.js("new Promise(r=>setTimeout(r,50))")
        assert c.js("!document.getElementById('cotacoes-documentDialog').open")
        for mode in ['url','session']:
            native_state['mode']=mode
            c.js("document.querySelector('[data-document-id] button').click()")
            c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{const s=document.getElementById('cotacoes-documentStatus').textContent;if(s&&!s.includes('Carregando')){clearInterval(t);resolve(true)}else if(n++>150){clearInterval(t);reject('sem mensagem de erro')}},30)})")
            assert c.js("document.querySelectorAll('#cotacoes-documentContent iframe').length===0"),'URL ou sessão divergente não exibe documento'
            close_document()
        native_state['mode']='ok'
        c.js("document.querySelector('[data-company=\"5\"]').click();document.querySelector('[data-client]').click();document.querySelector('[data-document-id] button').click()")
        c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(document.getElementById('cotacoes-documentStatus').textContent.includes('Selecione a empresa')){clearInterval(t);resolve(true)}else if(n++>150){clearInterval(t);reject('sem aviso de empresa')}},30)})")
        assert c.js("document.querySelectorAll('#cotacoes-documentContent iframe').length===0"),'Outra empresa não troca sessão nem carrega a tela no contexto errado'
        close_document()
        c.js("document.querySelector('[data-company=\"all\"]').click();document.querySelector('[data-client]').click()")
        image=c.call('Page.captureScreenshot',dict(format='png',captureBeyondViewport=True));(OUT/'orcamentos.png').write_bytes(base64.b64decode(image['data']))
        for mode in ['pedidos','notas','projecao']:
            c.js("document.querySelector('[data-mode=\"%s\"]').click()"%mode);c.wait()
            assert c.js("document.getElementById('cotacoes-error').hidden"),c.js("document.getElementById('cotacoes-error').textContent")
            if mode=='pedidos':assert c.js("!document.getElementById('cotacoes-statusPanel').hidden")
            if mode in ['pedidos','notas']:
                c.js("document.querySelector('[data-client]').click();document.querySelector('[data-document-id] td:last-child').click()")
                wait_document()
                assert native_routes[mode][0] in c.js("document.querySelector('#cotacoes-documentContent iframe').src")
                assert c.js("new URL(document.querySelector('#cotacoes-documentContent iframe').src).searchParams.get('acaoId')===document.querySelector('[data-document-id]').dataset.documentId"),'Abertura usa ID interno'
                close_document()
            if mode=='projecao':
                assert c.js("document.querySelector('#cotacoes-projectionChart svg')!==null")
                assert 'Não definida' in c.js("document.getElementById('cotacoes-projectionKpis').textContent")
                c.js("window.goalBaseline=Array.from(document.querySelectorAll('#cotacoes-projectionKpis strong')).slice(0,2).map(e=>e.textContent);document.querySelector('[data-goal]').click();document.getElementById('cotacoes-goalInput').value='5.000.000,00';document.getElementById('cotacoes-goalForm').requestSubmit()")
                c.js("new Promise(resolve=>{const t=setInterval(()=>{if(!document.getElementById('cotacoes-goalDialog').open){clearInterval(t);resolve(true)}},20)})")
                assert c.js("document.getElementById('cotacoes-goalCard').textContent.includes('5.000.000,00')")
                assert c.js("document.querySelectorAll('[data-goal-line]').length") == 1
                assert c.js("Array.from(document.querySelectorAll('#cotacoes-projectionKpis strong')).slice(0,2).every((e,i)=>e.textContent===goalBaseline[i])"), 'Meta não recalcula realizado/projeção'
                c.js("document.querySelector('[data-projection=next]').click()")
                assert c.js("document.getElementById('cotacoes-goalCard').textContent.includes('Não definida')"), 'Meta do mês seguinte independente'
                c.js("document.querySelector('[data-projection=current]').click()")
                assert c.js("document.querySelectorAll('[data-goal-line]').length") == 1
                goal_state['fail']=True
                c.js("document.querySelector('[data-goal]').click();document.getElementById('cotacoes-goalInput').value='6.000.000,00';document.getElementById('cotacoes-goalForm').requestSubmit()")
                c.js("new Promise(resolve=>{const t=setInterval(()=>{if(!document.getElementById('cotacoes-goalError').hidden){clearInterval(t);resolve(true)}},20)})")
                assert c.js("document.getElementById('cotacoes-goalCard').textContent.includes('5.000.000,00')"), 'Falha no salvamento preserva a meta anterior'
                goal_state['fail']=False;c.js("document.getElementById('cotacoes-goalCancel').click()")
                c.js("document.querySelector('[data-team=\"a\"]').click()")
                assert 'Vendedor 02' not in c.js("document.getElementById('cotacoes-projectionSellers').textContent"),'Projeção respeita equipe configurada'
                assert 'Vendedor 01' in c.js("document.getElementById('cotacoes-projectionSellers').textContent")
                assert c.js("document.querySelectorAll('[data-goal-line]').length") == 0, 'Equipe usa uma meta própria'
                c.js("document.querySelector('[data-team=\"all\"]').click()")
                c.js("document.querySelector('[data-projection=\"next\"]').click()")
                assert '11/2026' in c.js("document.getElementById('cotacoes-projectionNote').textContent")
        c.js("document.querySelector('[data-company=\"5\"]').click()")
        assert not c.js("document.getElementById('cotacoes-projectionUnits').hidden")
        assert '(t)' in c.js("document.getElementById('cotacoes-projectionKpis').textContent")
        assert 'R$' not in c.js("document.getElementById('cotacoes-projectionChart').textContent")
        assert 'R$' in c.js("document.getElementById('cotacoes-invoiceScenarios').textContent"),'Faturamento projetado continua monetário'
        c.js("document.querySelector('[data-projection-unit=\"reais\"]').click()")
        assert 'R$' in c.js("document.getElementById('cotacoes-projectionChart').textContent")
        c.js("document.querySelector('[data-company=\"all\"]').click()")
        c.js("document.querySelector('[data-projection=\"current\"]').click()")
        assert c.js("document.querySelectorAll('[data-goal-line]').length") == 1
        c.js("document.getElementById('cotacoes-refresh').click()");c.wait()
        assert c.js("document.querySelectorAll('[data-goal-line]').length") == 1, 'Meta recuperada após recarregar dados'
        image=c.call('Page.captureScreenshot',dict(format='png',captureBeyondViewport=True));(OUT/'projecao.png').write_bytes(base64.b64decode(image['data']))
        goal_state['user']=8;c.js("document.getElementById('cotacoes-refresh').click()");c.wait()
        assert c.js("document.querySelectorAll('[data-goal-line]').length") == 0, 'Outro usuário não herda a meta pessoal'
        goal_state['user']=7;c.js("document.getElementById('cotacoes-refresh').click()");c.wait()
        assert c.js("document.querySelectorAll('[data-goal-line]').length") == 1
        c.js("document.querySelector('[data-goal]').click();document.getElementById('cotacoes-goalRemove').click()")
        c.js("new Promise(resolve=>{const t=setInterval(()=>{if(!document.getElementById('cotacoes-goalDialog').open){clearInterval(t);resolve(true)}},20)})")
        assert c.js("document.querySelectorAll('[data-goal-line]').length") == 0
        assert c.js("document.getElementById('cotacoes-goalCard').textContent.includes('Não definida')")
        c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True))
        c.js("document.querySelector('[data-goal]').click()")
        assert c.js("document.getElementById('cotacoes-goalDialog').getBoundingClientRect().right<=innerWidth"), 'Editor de meta cabe no celular'
        c.js("document.getElementById('cotacoes-goalCancel').click();document.querySelector('[data-mode=orcamentos]').click()");c.wait()

        assert c.js("document.documentElement.scrollWidth<=window.innerWidth+1"),'Sem transbordamento horizontal no celular'
        c.call('Emulation.setDeviceMetricsOverride',dict(width=1700,height=1100,deviceScaleFactor=1,mobile=False))
        c.call('Page.navigate',dict(url='http://127.0.0.1:%s/index.html'%server.server_port))
        c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{const panel=document.querySelector('#indicatorSummary #cotacoes-main[aria-busy=false]');if(panel){clearInterval(t);resolve(true)}else if(n++>300){clearInterval(t);reject('indicador não carregou')}},30)})")
        assert c.js("document.querySelector('#indicatorSummary .cotacoes-panel').offsetWidth>=document.getElementById('indicatorSummary').offsetWidth-2"),'Painel deve ocupar a largura completa'
        assert c.js("document.querySelectorAll('iframe').length===0"),'Indicador nativo sem iframe'
        assert c.js("document.querySelectorAll('.sidebar').length===1"),'Somente a navegação do sistema'
        assert c.js("getComputedStyle(document.querySelector('.cotacoes-panel .wide-card')).backgroundColor==='rgb(255, 255, 255)'"),'Cartões brancos nativos'
        assert c.js("getComputedStyle(document.querySelector('.cotacoes-panel')).color==='rgb(7, 34, 77)'"),'Tipografia na paleta do sistema'
        assert c.js("getComputedStyle(document.getElementById('indicatorExports')).display!=='none'")
        assert c.js("!document.querySelector('[data-export=csv]').disabled")
        assert c.js("getComputedStyle(document.getElementById('indicatorFilters')).display==='none'")
        assert c.js("new Set(Array.from(document.querySelectorAll('[id]')).map(e=>e.id)).size===document.querySelectorAll('[id]').length"),'IDs únicos na integração'
        # Os quatro modos continuam funcionando dentro do dashboard, no mesmo documento.
        for mode in ['pedidos','notas','projecao','orcamentos']:
            c.js("document.querySelector('[data-mode=\"%s\"]').click()"%mode);c.wait()
            assert c.js("document.getElementById('cotacoes-error').hidden")
        # Atualização do cabeçalho preserva equipe e aba; exportações usam o painel ativo.
        c.js("document.querySelector('[data-team=\"a\"]').click();document.getElementById('refreshDashboard').click()")
        c.wait()
        assert c.js("document.querySelector('[data-team=\"a\"]').classList.contains('active')")
        c.js("window.print=()=>window.printCalls=(window.printCalls||0)+1;document.querySelector('[data-export=pdf]').click()")
        assert c.js("window.printCalls===1")
        c.js("window.csvClicks=0;HTMLAnchorElement.prototype.click=function(){window.csvClicks++};document.querySelector('[data-export=csv]').click()")
        assert c.js("window.csvClicks===1")
        c.js("document.getElementById('cotacoes-clearFilters').click()")
        c.wait()
        assert c.js("document.querySelector('[data-team=\"all\"]').classList.contains('active')")
        # Troca para um indicador padrão, verifica desmontagem e volta ao painel.
        c.js("document.querySelector('[data-id=\"faturamento_teste\"]').click()")
        c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(document.querySelector('#indicatorSummary .hero-card')){clearInterval(t);resolve(true)}else if(n++>150){clearInterval(t);reject('indicador padrão não carregou')}},30)})")
        assert c.js("document.querySelector('.cotacoes-panel')===null")
        assert c.js("document.getElementById('indicatorSummary').textContent.includes('1.234,00')")
        c.js("document.querySelector('[data-id=\"painel_cotacoes_teste\"]').click()")
        c.js("new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(document.querySelector('#cotacoes-main[aria-busy=false]')){clearInterval(t);resolve(true)}else if(n++>200){clearInterval(t);reject('painel não reabriu')}},30)})")
        assert c.js("document.querySelectorAll('.cotacoes-panel').length===1")
        image=c.call('Page.captureScreenshot',dict(format='png'));(OUT/'integrado.png').write_bytes(base64.b64decode(image['data']))
        c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True))
        assert c.js("document.documentElement.scrollWidth<=window.innerWidth+1"),'Layout integrado sem transbordamento no celular'
        assert not c.errors,c.errors
        print('OK: layout nativo, quatro equipes/abas, filtros, janelas de orçamento/pedido/NF, teclado, sessão/URL, projeção, CSV/PDF, atualização e celular.')
        print('Capturas: '+str(OUT))
    finally:
        proc.terminate();proc.wait(timeout=10);server.shutdown();log.close()
