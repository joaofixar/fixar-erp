"""Comparação visual/DOM com fixtures, sem ERP ou banco de cliente."""
import ast,base64,http.server,json,os,pathlib,socket,struct,subprocess,tempfile,threading,time,urllib.parse,urllib.request
ROOT=pathlib.Path(__file__).resolve().parents[1]
source=ast.parse((ROOT/'tests/painel-cotacoes-browser.py').read_text())
client=next(n for n in source.body if isinstance(n,ast.ClassDef) and n.name=='CDP')
exec(compile(ast.Module(body=[client],type_ignores=[]),'<CDP>','exec'))
oldjs=(ROOT/'dashboard.js').read_text().replace(
 "var requestParams = new URLSearchParams(context.params);\n    if (refreshDashboard) requestParams.delete('somenteDetalhamento');\n    else requestParams.set('somenteDetalhamento', '1');\n    return request('data.php?' + requestParams.toString())",
 "return request('data.php?' + context.params)"
)
definition=dict(id='geral_ficticio',titulo='Indicador geral fictício',subtitulo='Teste de desempenho sem dados reais',filtros=[dict(id='grupo',titulo='Grupo',tipo='selecao',padrao='',opcoes=[dict(valor='A',titulo='Grupo A')])],card=dict(titulo='Total',campo='valor',formato='numero'),listagem=dict(titulo='Registros',colunas=[dict(campo='grupo',titulo='Grupo'),dict(campo='valor',titulo='Valor',formato='numero')],agrupamento=dict(campo='grupo'),detalhamento=dict(modal=True,campoGrupo='grupo',titulo='Detalhe {grupo}',colunas=[dict(campo='documento',titulo='Documento'),dict(campo='valor',titulo='Valor',formato='numero')])),composicao=dict(habilitada=False),grafico=dict(habilitado=False),graficosPorGrupo=dict(habilitado=False),abas=[])
result=dict(indicador=definition,card=dict(valor=25,quantidade=1,formato='numero'),listagem=[dict(grupo='A',valor=25)],listagemDetalhes={'A':[dict(grupo='A',documento='101',valor=25)]},composicao=[],grafico=[],graficosPorGrupo=[],calendarios=[],resumos=[],secoes=[],updatedAt='15/10/2026 — fixture')
requests=[]
class Handler(http.server.SimpleHTTPRequestHandler):
 def __init__(self,*a,**kw): super().__init__(*a,directory=str(ROOT),**kw)
 def log_message(self,*a):pass
 def do_GET(self):
  path=urllib.parse.urlparse(self.path)
  if path.path=='/data.php':
   q=urllib.parse.parse_qs(path.query)
   if q.get('action')==['configuration']: data=dict(titulo='Dashboard fictício',indicadores=[definition],podeGerenciarAcessos=False)
   else:
    requests.append(q);data=result
    if q.get('somenteDetalhamento')==['1']: data=dict(result,card=dict(valor=0,quantidade=0,formato='numero'),listagem=[])
   self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(json.dumps(data).encode());return
  if path.path=='/dashboard.js' and 'legacy=1' in self.headers.get('Referer',''):
   self.send_response(200);self.send_header('Content-Type','application/javascript');self.end_headers();self.wfile.write(oldjs.encode());return
  if path.path=='/static/img/logo_hardness.png':
   self.send_response(200);self.send_header('Content-Type','image/png');self.end_headers();self.wfile.write((ROOT.parent/'static/img/logo_hardness.png').read_bytes());return
  return super().do_GET()
server=http.server.ThreadingHTTPServer(('127.0.0.1',0),Handler);threading.Thread(target=server.serve_forever,daemon=True).start()
browser=os.environ.get('COTACOES_CHROME','/root/.cache/puppeteer/chrome-headless-shell/linux-146.0.7680.31/chrome-headless-shell-linux64/chrome-headless-shell')
with tempfile.TemporaryDirectory(prefix='dashboard-perf-browser-') as profile, tempfile.TemporaryFile() as log:
 proc=subprocess.Popen([browser,'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
 try:
  active=pathlib.Path(profile)/'DevToolsActivePort'
  for _ in range(600):
   if active.exists():break
   time.sleep(.05)
  if not active.exists():
   log.seek(0);raise RuntimeError(log.read().decode())
  port=int(active.read_text().splitlines()[0]);pages=json.load(urllib.request.urlopen(f'http://127.0.0.1:{port}/json'))
  c=CDP(pages[0]['webSocketDebuggerUrl']);c.call('Runtime.enable');c.call('Page.enable')
  snapshots=[]
  for legacy in [True,False]:
   c.call('Emulation.setDeviceMetricsOverride',dict(width=1440,height=1000,deviceScaleFactor=1,mobile=False))
   c.call('Page.navigate',dict(url=f'http://127.0.0.1:{server.server_port}/index.html'+('?legacy=1' if legacy else '')))
   for _ in range(200):
    if c.js("document.querySelectorAll('.group-row').length===1 && document.querySelector('#indicatorLoading').hidden"):break
    time.sleep(.05)
   assert c.js("document.querySelectorAll('.group-row').length")==1
   main=c.js("document.querySelector('.workspace').innerHTML")
   c.js("document.querySelector('.group-row').click()")
   for _ in range(150):
    if c.js("document.querySelector('#indicatorDetailContent').textContent.includes('101')"):break
    time.sleep(.03)
   assert c.js("document.querySelector('#indicatorDetailContent').textContent.includes('101')")
   assert requests[-1].get('somenteDetalhamento')==(['1'] if not legacy else None)
   assert c.js("document.querySelector('.workspace').innerHTML")==main, 'Abrir detalhe alterou o dashboard'
   modal=c.js("document.querySelector('#indicatorDetailContent').innerHTML")
   assert c.js("document.querySelector('#exportIndicatorDetailPdf').disabled")==False
   c.js("document.querySelector('#closeIndicatorDetailModal').click()")
   c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True));time.sleep(.1)
   snapshots.append((main,modal,c.js("document.querySelector('.workspace').getBoundingClientRect().width")))
  assert snapshots[0]==snapshots[1], 'Layout ou conteúdo alterado entre legado e otimizado'
  print('PASS: DOM/layout desktop e celular idênticos; modal e valores preservados; caminho rápido usado somente no novo JS.')
 finally:proc.terminate();proc.wait(timeout=10);server.shutdown()
