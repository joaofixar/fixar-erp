"""Conferência do PCP em Chrome local com HTTP e registros simulados."""
import ast,base64,http.server,json,os,pathlib,socket,struct,subprocess,tempfile,threading,time,urllib.parse,urllib.request
root=pathlib.Path(__file__).resolve().parents[1]
source=ast.parse((root/'tests/painel-cotacoes-browser.py').read_text())
client=next(n for n in source.body if isinstance(n,ast.ClassDef) and n.name=='CDP')
exec(compile(ast.Module(body=[client],type_ignores=[]),'<CDP>','exec'))
config=json.loads((root/'indicadores-pcp-realizado.json').read_text())
i=next(x for x in config['indicadores'] if x['id']=='inecel_pcp_planejamento')
definition={k:i[k] for k in ['id','titulo','subtitulo','icone','cor','filtros','card','grafico','composicao','listagem']}
for f in definition['filtros']:
    f.pop('opcoesSql',None);f['valor']={'dataInicio':'2026-10-01','dataFim':'2026-10-31','abrangenciaEmpresa':'atual'}.get(f['id'],'')
    if f['id']=='abrangenciaEmpresa':
        f['opcoes']=[dict(valor='atual',titulo='Empresa atual'),dict(valor='2',titulo='Filial A'),dict(valor='4',titulo='Filial B'),dict(valor='5',titulo='Filial C'),dict(valor='todas',titulo='Todas as empresas')]
row=dict(op='12',id=11,maquina='Blank',semanaMes='2026-10 / Semana 2',data='2026-10-10',dataInicio='2026-10-10',dataFim='2026-10-11',valor=300.75,blank=300.75,segunda=300.75,pesoAberto=300.75,pesoFinalizado=0,pesoAtrasado=0,ops=1,opsAbertas=1,opsFinalizadas=0,opsAtrasadas=0,quantidade=1,apontamentos=1,um=1,semPeso=0,prioridade='Programada e liberada',ordemPrioridade=6,cliente='Cliente')
sections=[dict(titulo=s['titulo'],subtitulo=s['subtitulo'],colunas=s['colunas'],rodapeTotais=s['rodapeTotais'],conferencia=s['conferencia'],linhas=[row]) for s in i['secoes']]
result=dict(indicador=definition,card=dict(valor=300.75,quantidade=1,formato='numero'),resumos=[dict(r,valor=1) for r in i['resumos']],secoes=sections,listagem=[row],listagemDetalhes={},updatedAt='15/10/2026 12:00')
plain=dict(id='indicador_padrao',titulo='Indicador padrão',filtros=[],card=dict(titulo='Padrão'),grafico=dict(habilitado=False),listagem=dict(habilitada=False))
requests=[];mode={'error':False,'empty':False}
class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self,*args,**kwargs):super().__init__(*args,directory=str(root),**kwargs)
    def log_message(self,*args):pass
    def do_POST(self):
        self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(b'{}')
    def do_GET(self):
        if self.path.startswith('/data.php?'):
            q=urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query,keep_blank_values=True);requests.append(q);code=200
            if q.get('action')==['configuration']:data=dict(titulo='Painel',indicadores=[definition,plain],podeGerenciarAcessos=False)
            elif q.get('detalheBloco'):
                key=q['detalheBloco'][0];c=i['conferencia'][key]
                data=dict(conferencia=dict(titulo=c['titulo'],criterio=c['criterio']+' <img src=x onerror=alert(1)>',colunas=c['colunas'],linhas=[] if mode['empty'] else [row],totais=[dict(titulo='Realizado (kg)',valor=300.75,formato='numero')],filtros=definition['filtros']))
                if mode['error']:data={'error':'A conferência excedeu 5.000 registros. Reduza o período.'};code=422
            elif q.get('detalheGrupo'):data=dict(result,listagemDetalhes={'12':[dict(itemId=1,produto='Produto A',quantidade=12,produzido=10)]})
            elif q.get('id')==['indicador_padrao']:data=dict(indicador=plain,card=dict(valor=1,quantidade=1,formato='numero'),listagem=[])
            else:
                definition['filtros'][0]['valor']=q.get('f_abrangenciaEmpresa',['atual'])[0]
                data=result
            self.send_response(code);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(data).encode());return
        if self.path=='/static/img/logo_hardness.png':
            self.send_response(200);self.end_headers();self.wfile.write((root.parent/'static/img/logo_hardness.png').read_bytes());return
        super().do_GET()
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler);threading.Thread(target=server.serve_forever,daemon=True).start()
out=pathlib.Path('/tmp/pcp-conferencia-preview');out.mkdir(exist_ok=True)
def until(c,expression):
    for _ in range(120):
        if c.js(expression):return
        time.sleep(.05)
    raise AssertionError(expression)
with tempfile.TemporaryDirectory(prefix='pcp-conferencia-') as profile:
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
            until(c,"document.querySelectorAll('[data-conference]').length===21")
            assert c.js("document.querySelector('[name=f_abrangenciaEmpresa]').value")=='atual'
            assert c.js("Array.from(document.querySelector('[name=f_abrangenciaEmpresa]').options).map(o=>o.value)")==['atual','2','4','5','todas']
            c.js("var companies=document.querySelector('[name=f_abrangenciaEmpresa]');companies.value='2';companies.dispatchEvent(new Event('change',{bubbles:true}))")
            until(c,"document.querySelector('[name=f_abrangenciaEmpresa]').value==='2' && document.querySelectorAll('[data-conference]').length===21 && !document.querySelector('[name=f_abrangenciaEmpresa]').disabled")
            c.js("document.querySelector('[data-conference=realizado_dia]').click()")
            until(c,"!!document.querySelector('.conference-totals')")
            assert requests[-1]['f_abrangenciaEmpresa']==['2']
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            c.js("var companies=document.querySelector('[name=f_abrangenciaEmpresa]');companies.value='todas';companies.dispatchEvent(new Event('change',{bubbles:true}))")
            until(c,"document.querySelector('[name=f_abrangenciaEmpresa]').value==='todas' && document.querySelectorAll('[data-conference]').length===21 && !document.querySelector('[name=f_abrangenciaEmpresa]').disabled")
            assert requests[-1]['f_abrangenciaEmpresa']==['todas']
            assert c.js("document.querySelectorAll('.indicator-kpi[data-conference]').length") == 6
            assert c.js("document.querySelectorAll('.indicator-section tbody tr[data-conference]').length") == 4
            assert c.js("document.querySelectorAll('.indicator-section tfoot [data-conference]').length") == 4
            for n in range(21):
                c.js("document.querySelectorAll('[data-conference]')[%d].click()"%n)
                until(c,"!!document.querySelector('.conference-totals')")
                assert c.js("!document.querySelector('.conference-criteria img')"),'HTML é escapado'
                assert c.js("document.querySelector('.conference-totals').textContent.includes('300,75')")
                assert requests[-1]['f_dataInicio']==['2026-10-01'] and requests[-1]['f_dataFim']==['2026-10-31']
                assert requests[-1]['f_abrangenciaEmpresa']==['todas']
                c.js("document.getElementById('closeIndicatorDetailModal').click()")
                assert c.js("document.activeElement===document.querySelectorAll('[data-conference]')[%d]"%n)
            # Células enviam semana e dia, com apenas uma requisição mesmo com bubbling.
            for day in ['segunda','terca','quarta','quinta','sexta','sabado','domingo']:
                before=len(requests)
                c.js("document.querySelector('[data-conference-day=%s]').dispatchEvent(new MouseEvent('click',{bubbles:true}))"%day)
                until(c,"!!document.querySelector('.conference-totals')")
                assert len(requests)==before+1
                assert requests[-1]['detalheBloco']==['semanal']
                assert requests[-1]['detalheBlocoGrupo']==['2026-10 / Semana 2']
                assert requests[-1]['detalheBlocoDia']==[day]
                c.js("document.getElementById('closeIndicatorDetailModal').click()")
            c.js("document.querySelector('[data-conference-day=quinta]').dispatchEvent(new KeyboardEvent('keydown',{key:'Enter',bubbles:true}))")
            until(c,"!!document.querySelector('.conference-totals')")
            assert requests[-1]['detalheBlocoDia']==['quinta']
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            # Teclado, data/grupo enviados e rodapé sem restrição de grupo.
            c.js("document.querySelector('[data-conference=realizado_dia]').dispatchEvent(new KeyboardEvent('keydown',{key:'Enter',bubbles:true}))")
            until(c,"!!document.querySelector('.conference-totals')")
            assert requests[-1]['detalheBlocoGrupo']==['2026-10-10']
            image=c.call('Page.captureScreenshot',dict(format='png'));(out/'conferencia-realizado.png').write_bytes(base64.b64decode(image['data']))
            c.js("document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape',bubbles:true}))")
            c.js("document.querySelector('.indicator-section tfoot [data-conference]').click()")
            until(c,"!!document.querySelector('.conference-totals')")
            assert requests[-1]['detalheBlocoGrupo']==['']
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            # A listagem original das OPs continua abrindo produtos/pedidos.
            c.js("document.querySelector('.group-row').click()")
            until(c,"document.getElementById('indicatorDetailContent').textContent.includes('Produto A')")
            assert c.js("!document.getElementById('exportIndicatorDetailPdf').hidden")
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            mode['error']=True;c.js("document.querySelector('[data-conference]').click()")
            until(c,"document.getElementById('indicatorDetailContent').textContent.includes('5.000')")
            assert c.js("!document.querySelector('.conference-totals')")
            c.js("document.getElementById('closeIndicatorDetailModal').click()")
            mode['error']=False;mode['empty']=True;c.js("document.querySelector('[data-conference]').click()")
            until(c,"document.getElementById('indicatorDetailContent').textContent.includes('Nenhum registro considerado')")
            c.js("document.getElementById('closeIndicatorDetailModal').click();document.querySelector('[data-id=indicador_padrao]').click()")
            until(c,"document.querySelectorAll('[data-conference]').length===0")
            assert not c.errors,c.errors
            print('OK: seis cartões, quatro blocos e quatro totais; grupo/data, filtros, teclado, foco, HTML seguro, lista original, erros e indicadores sem conferência.')
        finally:proc.terminate();proc.wait(timeout=10);server.shutdown()
