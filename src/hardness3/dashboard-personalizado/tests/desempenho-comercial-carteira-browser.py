"""Navegador local com dados simulados; não acessa o ERP."""
import ast
import base64
import http.server
import json
import os
import sys
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

root = pathlib.Path(__file__).resolve().parents[1]
# Reutiliza somente o cliente CDP do teste existente; não executa aquele teste.
source = ast.parse((root/'tests/painel-cotacoes-browser.py').read_text())
client = next(n for n in source.body if isinstance(n, ast.ClassDef) and n.name == 'CDP')
exec(compile(ast.Module(body=[client], type_ignores=[]), '<CDP>', 'exec'))
result = json.loads(subprocess.check_output(['php', str(root/'tests/desempenho-comercial.php'), '--fixture-regioes-cadastradas' if '--regioes-cadastradas' in sys.argv else '--fixture-gestor' if '--gestor' in sys.argv else '--fixture-regioes' if '--regioes' in sys.argv else '--fixture-carteira']))
result['updatedAt'] = '14/10/2026 12:00'
definition = result['indicador']
requests = []
exports = []
# Exercitar paginação de clientes e texto seguro, sem alterar totais da fixture.
group = result['desempenhoComercial']['carteiraRepresentantes']['linhas'][0]
if '--gestor' not in sys.argv:
    for n in range(26):
        group['clientes'].append(dict(group['clientes'][1], cliente='Cliente zerado %02d' % n, clienteId='cad:%s' % (100+n)))
    group['clientes'][-1]['cliente'] = '<img src=x onerror=alert(1)>'
out = pathlib.Path('/tmp/vendedores-carteira-preview')
out.mkdir(exist_ok=True)
class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs): super().__init__(*args, directory=str(root), **kwargs)
    def log_message(self, *args): pass
    def do_GET(self):
        if self.path.startswith('/export.php?'):
            exports.append(urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query))
            self.send_response(204); self.end_headers(); return
        if self.path.startswith('/data.php?'):
            q = urllib.parse.parse_qs(urllib.parse.urlparse(self.path).query)
            if q.get('action') == ['commercialClientHistory']:
                data = dict(periodo=dict(inicio='2026-05-01',fim='2026-10-15'),meses=['2026-05','2026-06','2026-07','2026-08','2026-09','2026-10'],totaisMeses={'2026-05':dict(orcamentos=123,pedidos=45)})
                self.send_response(200); self.send_header('Content-Type', 'application/json'); self.end_headers(); self.wfile.write(json.dumps(data).encode()); return
            if q.get('action') == ['indicator']: requests.append(q)
            data = dict(titulo='Painel de Indicadores',indicadores=[definition],podeGerenciarAcessos=False) if q.get('action') == ['configuration'] else result
            self.send_response(200); self.send_header('Content-Type', 'application/json'); self.end_headers(); self.wfile.write(json.dumps(data).encode()); return
        if self.path == '/static/img/logo_hardness.png':
            self.send_response(200); self.send_header('Content-Type','image/png'); self.end_headers(); self.wfile.write((root.parent/'static/img/logo_hardness.png').read_bytes()); return
        if '--regioes-cadastradas' in sys.argv and self.path.startswith('/mapas-municipios/'): time.sleep(.2)
        super().do_GET()
server = http.server.ThreadingHTTPServer(('127.0.0.1',0), Handler)
threading.Thread(target=server.serve_forever,daemon=True).start()
browser = os.environ.get('COTACOES_CHROME') or next((shutil.which(name) for name in ('chromium', 'google-chrome', 'chrome-headless-shell') if shutil.which(name)), None)
if not browser: raise SystemExit('Informe COTACOES_CHROME com o caminho do Chrome/Chromium.')
with tempfile.TemporaryDirectory(prefix='vendedores-comparativo-') as profile:
    with (out/'chrome.log').open('w') as log:
        proc = subprocess.Popen([browser,'--no-sandbox','--disable-gpu','--disable-dev-shm-usage','--remote-debugging-port=0','--user-data-dir='+profile,'about:blank'],stdout=log,stderr=log)
        try:
            active = pathlib.Path(profile)/'DevToolsActivePort'
            for _ in range(100):
                if active.exists(): break
                time.sleep(.05)
            port = int(active.read_text().splitlines()[0])
            pages = json.load(urllib.request.urlopen('http://127.0.0.1:%s/json'%port))
            c = CDP(pages[0]['webSocketDebuggerUrl']); c.call('Runtime.enable'); c.call('Page.enable')
            c.call('Emulation.setDeviceMetricsOverride',dict(width=1900,height=1100,deviceScaleFactor=1,mobile=False))
            c.call('Page.navigate',dict(url='http://127.0.0.1:%s/index.html'%server.server_port))
            for _ in range(150):
                if c.js("document.querySelectorAll('.commercial-kpi').length===8"): break
                time.sleep(.05)
            assert c.js("document.querySelectorAll('.commercial-kpi').length") == 8
            assert c.js("Array.from(document.querySelectorAll('.commercial-kpi > span')).some(n=>n.textContent==='Ticket médio pedido')")
            assert c.js("Array.from(document.querySelectorAll('.commercial-kpi > span')).some(n=>n.textContent==='Ticket médio orçamento')")
            assert c.js("!document.querySelector('.commercial-history').hidden")
            c.js("document.querySelector('.commercial-client-select').click()")
            for _ in range(100):
                if c.js("document.querySelectorAll('.commercial-history-month').length===6"): break
                time.sleep(.05)
            assert c.js("document.querySelectorAll('.commercial-history-month').length") == 6
            assert c.js("document.querySelector('.commercial-history').textContent.includes('Últimos seis meses')")
            assert c.js("document.querySelector('.commercial-history-totals').textContent.includes('123,00') && document.querySelector('.commercial-history-totals').textContent.includes('45,00')")
            assert c.js("document.querySelector('.commercial-history-pedidos').getBoundingClientRect().width > 0")
            c.js("document.querySelector('.commercial-history header button').click()")
            assert c.js("document.querySelector('.commercial-history').textContent.includes('Período filtrado')")
            if '--regioes-cadastradas' in sys.argv:
                c.js("window.originalCards=Array.from(document.querySelectorAll('.commercial-kpi'));document.querySelector('[data-tab=regioes]').click();var flag=document.querySelector('[data-region-mode]');flag.click();")
                assert c.js("document.querySelector('[data-region-mode]').tagName==='BUTTON' && document.querySelector('[data-region-mode]').getAttribute('aria-pressed')==='true'")
                assert c.js("document.querySelector('.commercial-region-map').getAttribute('aria-busy')==='true' && !!document.querySelector('.commercial-map-loading')"), 'Carregamento dentro do mapa enquanto malha chega'
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                for _ in range(150):
                    if c.js("document.querySelectorAll('.commercial-region-map path').length>5000"): break
                    time.sleep(.05)
                assert c.js("!document.querySelector('.commercial-map-loading') && document.querySelector('.commercial-region-map').getAttribute('aria-busy')==='false'")
                assert c.js("document.querySelectorAll('.commercial-region-map path').length") > 5000, 'Brasil mostra divisões municipais, não somente estados'
                assert c.js("document.querySelectorAll('[data-region-polygon]').length") >= 2
                c.js("document.querySelector('[data-region-uf=PR]').click()")
                for _ in range(100):
                    if c.js("document.querySelectorAll('[data-region-polygon]').length>0"): break
                    time.sleep(.05)
                assert c.js("document.querySelectorAll('.commercial-region-map path').length") == 399
                assert c.js("document.querySelectorAll('[data-region-item]').length") == 3
                assert c.js("document.querySelector('.commercial-region-list').textContent.includes('PR1 GRANDE CURITIBA')")
                c.js("document.querySelector('[data-region-polygon=\"101\"]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
                assert c.js("document.querySelectorAll('[data-region-city]').length") == 0
                assert c.js("document.querySelector('.commercial-region-list').textContent.includes('Curitiba')")
                assert not c.js("document.querySelector('.commercial-region-list').textContent.includes('Londrina')")
                assert c.js("document.querySelector('.commercial-region-list').textContent.includes('Cliente 1')")
                before_months = len(requests)
                c.js("document.querySelector('[data-region-client-months]').click()")
                assert c.js("document.querySelector('[data-region-client-months]').getAttribute('aria-expanded')") == 'true'
                assert c.js("document.querySelectorAll('.commercial-region-months tbody tr').length") == 12
                assert c.js("document.querySelector('.commercial-region-months').textContent.includes('Mar/2026') && document.querySelector('.commercial-region-months').textContent.includes('2.000,00')")
                assert c.js("document.querySelector('.commercial-region-months tbody tr:nth-child(5)').textContent.includes('—')"), 'Meses fora do filtro aparecem com traço'
                assert not c.js("document.querySelector('.commercial-region-months').textContent.includes('Notas')")
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                assert len(requests) == before_months, 'Expansão mensal usa os dados já carregados'
                c.js("document.querySelector('[data-region-client-months]').focus()")
                c.call('Input.dispatchKeyEvent',dict(type='keyDown',key='Enter',code='Enter',windowsVirtualKeyCode=13,text='\r',unmodifiedText='\r'))
                c.call('Input.dispatchKeyEvent',dict(type='keyUp',key='Enter',code='Enter',windowsVirtualKeyCode=13))
                assert c.js("document.querySelector('[data-region-client-months]').getAttribute('aria-expanded')") == 'false'
                c.js("document.querySelector('[data-region-back-uf]').click();document.querySelector('[data-region-item=\"0\"]').click()")
                assert c.js("document.querySelector('.commercial-region-list').textContent.includes('Sem região')")
                c.js("document.querySelector('[data-region-client-months]').click()")
                assert c.js("document.querySelector('.commercial-region-months tbody tr:nth-child(4)').textContent.includes('0,00')"), 'Sem movimento dentro do filtro aparece zerado'
                c.js("document.querySelector('[data-region-back-uf]').click();var flag=document.querySelector('[data-region-mode]');flag.click()")
                assert c.js("document.querySelector('.commercial-region-list tbody').children.length") == 3
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                assert len(requests) == 1, 'Flag/UF/região/cidade não recarregam os cartões'
                c.js("var flag=document.querySelector('[data-region-mode]');flag.click()")
                for _ in range(100):
                    if c.js("document.querySelectorAll('.commercial-region-map path').length===399"): break
                    time.sleep(.025)
                c.js("document.querySelector('.commercial-regions').scrollIntoView()")
                c.js("document.querySelector('[data-region-item=\"101\"]').click();document.querySelector('[data-region-client-months]').click()")
            elif '--gestor' in sys.argv:
                assert c.js("document.querySelectorAll('.error-card').length") == 0
                c.js("window.originalCards=Array.from(document.querySelectorAll('.commercial-kpi'));document.querySelector('[data-tab=representantes]').click();document.querySelector('.commercial-representative-row').click()")
                assert c.js("document.querySelector('.commercial-representative-row').textContent.includes('20.000')")
                assert c.js("document.querySelectorAll('.commercial-portfolio-client').length") == 25
                c.js("var view=document.querySelector('.commercial-view select');view.value='mensal';view.dispatchEvent(new Event('change'))")
                assert not c.js("document.querySelector('.commercial-matrix').textContent.includes('NaN')")
                c.js("document.querySelector('[aria-label=\"Próxima página de clientes de INECEL\"]').click()")
                assert c.js("document.querySelectorAll('.commercial-portfolio-client').length") == 25
                c.js("document.querySelector('[data-tab=regioes]').click()")
                assert c.js("document.querySelector('.commercial-region-summary').textContent.includes('20.000')")
                assert c.js("document.querySelectorAll('[data-region-uf]').length") == 1
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                assert len(requests) == 1
            else:
                time.sleep(.1)
                assert len(requests) == 1
                c.js("window.originalCards=Array.from(document.querySelectorAll('.commercial-kpi'));window.originalFilters=document.getElementById('indicatorFilters');window.originalStats=document.querySelector('.commercial-stats');document.querySelector('[data-tab=representantes]').click()")
                assert len(requests) == 1, 'Troca de aba não consulta a API'
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i]) && originalFilters===document.getElementById('indicatorFilters') && originalStats===document.querySelector('.commercial-stats')"), 'Cartões e filtros preservam os mesmos nós'
                assert c.js("document.querySelectorAll('.commercial-representative-row').length") == 2
                assert c.js("document.querySelector('.commercial-representative-row').children.length") == 3
                c.js("document.querySelector('[data-commercial-sort=pedidos-total]').click()")
                assert c.js("document.querySelector('[data-commercial-sort=pedidos-total]').closest('th').getAttribute('aria-sort')") == 'descending'
                c.js("document.querySelector('[data-commercial-sort=orcamentos-total]').click()")
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                assert not c.js("document.querySelector('.commercial-matrix').textContent.includes('Notas emitidas')"), 'Resumo da carteira não exibe NF'
                assert c.js("document.querySelectorAll('.commercial-portfolio-client').length") == 0
                c.js("document.querySelector('.commercial-representative-row').click()")
                assert c.js("document.querySelectorAll('.commercial-portfolio-client').length") == 25
                assert c.js("document.querySelector('.commercial-portfolio-client').children.length") == 3
                assert c.js("document.activeElement.getAttribute('aria-expanded')") == 'true'
                c.js("document.querySelector('[aria-label=\"Próxima página de clientes de INECEL\"]').click()")
                assert c.js("document.querySelectorAll('.commercial-portfolio-client').length") == 3
                assert c.js("document.querySelector('.commercial-matrix').textContent.includes('<img src=x onerror=alert(1)>') && !document.querySelector('.commercial-matrix img')")
                c.js("document.querySelector('.commercial-representative-toggle').focus()")
                assert c.js("document.activeElement.className") == 'commercial-representative-toggle'
                c.call('Page.bringToFront')
                c.call('Input.dispatchKeyEvent',dict(type='keyDown',key='Enter',code='Enter',windowsVirtualKeyCode=13,text='\r',unmodifiedText='\r'))
                c.call('Input.dispatchKeyEvent',dict(type='keyUp',key='Enter',code='Enter',windowsVirtualKeyCode=13))
                time.sleep(.1)
                assert c.js("document.querySelector('.commercial-representative-toggle').getAttribute('aria-expanded')") == 'false'
                c.js("document.querySelector('.commercial-representative-row').click();var view=document.querySelector('.commercial-view select');view.value='mensal';view.dispatchEvent(new Event('change'))")
                assert c.js("document.querySelector('.commercial-representative-row').children.length") == 27
                assert not c.js("document.querySelector('.commercial-table').textContent.includes('Notas emitidas')")
                c.js("document.querySelector('[data-tab=vendedores]').click()")
                assert c.js("document.querySelectorAll('.commercial-representative-row').length") == 0
                assert len(requests) == 1
                c.js("document.querySelector('[data-tab=representantes]').click();var view=document.querySelector('.commercial-view select');view.value='periodo';view.dispatchEvent(new Event('change'));document.querySelector('[name=f_cliente]').value='Edição ainda não aplicada';document.querySelector('[data-export=csv]').click()")
                for _ in range(40):
                    if exports: break
                    time.sleep(.025)
                assert exports and exports[-1]['tab'] == ['representantes']
                assert exports[-1].get('f_cliente',[''])[0] == '', 'Exportação usa os filtros aplicados e a aba atual'
                assert exports[-1]['f_vendedor'] == ['7'], exports
                assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")

                if '--regioes' in sys.argv:
                    before = len(requests)
                    c.js("document.querySelector('[data-tab=regioes]').click()")
                    assert c.js("!document.querySelector('.commercial-regions').hidden && document.querySelector('.commercial-matrix').hidden")
                    assert c.js("originalCards.every((card,i)=>card===document.querySelectorAll('.commercial-kpi')[i])")
                    assert c.js("document.querySelectorAll('.commercial-region-map path').length") == 27
                    assert c.js("document.querySelectorAll('[data-region-map-uf]').length") == 2
                    c.js("document.querySelector('[data-region-map-uf=PR]').dispatchEvent(new MouseEvent('click',{bubbles:true}))")
                    assert c.js("document.querySelectorAll('[data-region-city]').length") == 0
                    assert c.js("document.querySelector('.commercial-region-list').textContent.includes('Cliente A')")
                    assert c.js("document.querySelector('.commercial-region-list').textContent.includes('Cliente B')")
                    assert not c.js("document.querySelector('.commercial-regions').textContent.includes('Notas emitidas')")
                    c.js("document.querySelector('[data-region-brasil]').click()")
                    assert c.js("document.querySelectorAll('[data-region-uf]').length") == 3
                    c.js("document.querySelector('[data-region-uf=SP]').click()")
                    assert c.js("document.querySelector('.commercial-region-list').textContent.includes('São Paulo')")
                    assert len(requests) == before, 'Brasil/UF/cidade não recarregam dados ou cartões'
                    c.js("document.querySelector('[data-export=csv]').click()")
                    for _ in range(40):
                        if exports[-1]['tab'] == ['regioes']: break
                        time.sleep(.025)
                    assert exports[-1]['tab'] == ['regioes']
                    c.js("document.querySelector('[data-tab=vendedores]').click()")
                    assert c.js("document.querySelector('.commercial-regions').hidden && !document.querySelector('.commercial-matrix').hidden")
                    c.js("document.querySelector('[data-tab=regioes]').click();document.querySelector('[data-region-brasil]').click()")
            assert c.js("document.documentElement.scrollWidth <= innerWidth+1")
            image = c.call('Page.captureScreenshot',dict(format='png')); (out/'carteira-desktop.png').write_bytes(base64.b64decode(image['data']))
            c.call('Emulation.setDeviceMetricsOverride',dict(width=390,height=844,deviceScaleFactor=1,mobile=True))
            assert c.js("document.documentElement.scrollWidth <= innerWidth+1"), 'Sem transbordamento horizontal no celular'
            if '--regioes' not in sys.argv and '--gestor' not in sys.argv and '--regioes-cadastradas' not in sys.argv: assert c.js("Array.from(document.querySelectorAll('.commercial-representative-toggle')).every(e=>e.offsetWidth>0)")
            image = c.call('Page.captureScreenshot',dict(format='png',captureBeyondViewport=True)); (out/'carteira-celular.png').write_bytes(base64.b64decode(image['data']))
            assert not c.errors, c.errors
            print('PASSOU: carteira de gestor com 20.000 clientes, paginação, meses zerados, mapa, cartões preservados e resposta sem erro.' if '--gestor' in sys.argv else 'PASSOU: abas sem requisições, mesmos cartões/filtros, expansão por clique/teclado, paginação, texto seguro, mensal sem NF, exportação e layout desktop/celular.')
            print('Capturas: '+str(out))
        finally:
            proc.terminate(); proc.wait(timeout=10); server.shutdown()
