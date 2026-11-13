"""Filtro comercial no Chrome, com data fixa e formulário local, sem conexão com o ERP."""
import ast
import base64
import json
import os
import pathlib
import socket
import struct
import subprocess
import tempfile
import time
import urllib.parse
import urllib.request

root = pathlib.Path(__file__).resolve().parents[1]
source = ast.parse((root / 'tests/painel-cotacoes-browser.py').read_text())
client = next(n for n in source.body if isinstance(n, ast.ClassDef) and n.name == 'CDP')
exec(compile(ast.Module(body=[client], type_ignores=[]), '<CDP>', 'exec'))

with tempfile.TemporaryDirectory(prefix='filtros-meses-comerciais-') as profile:
    with (pathlib.Path(profile) / 'chrome.log').open('w') as log:
        proc = subprocess.Popen([
            os.environ['COTACOES_CHROME'], '--no-sandbox', '--disable-gpu',
            '--disable-dev-shm-usage', '--remote-debugging-port=0',
            '--user-data-dir=' + profile, 'about:blank'
        ], stdout=log, stderr=log)
        try:
            active = pathlib.Path(profile) / 'DevToolsActivePort'
            for _ in range(100):
                if active.exists():
                    break
                time.sleep(.05)
            if not active.exists():
                raise RuntimeError((pathlib.Path(profile) / 'chrome.log').read_text())
            port = int(active.read_text().splitlines()[0])
            pages = json.load(urllib.request.urlopen('http://127.0.0.1:%s/json' % port))
            c = CDP(pages[0]['webSocketDebuggerUrl'])
            c.call('Runtime.enable')
            c.js("""
                window.RealDate = Date;
                window.Date = class extends RealDate {
                    constructor(...args) { super(...(args.length ? args : [2026, 9, 26, 12])); }
                };
            """)
            c.js((root / 'filtros-meses-comerciais.js').read_text())
            c.js("""
                window.changes = [];
                window.mount = function (start, end, blocked) {
                    document.body.innerHTML = '<form><input name="f_dataInicio"><input name="f_dataFim">' +
                        '<input name="f_equipe" value="william"><input name="f_vendedor" value="7">' +
                        '<input name="f_cliente" value="Cliente teste"></form>';
                    window.form = document.querySelector('form');
                    form.elements.f_dataInicio.value = start;
                    form.elements.f_dataFim.value = end;
                    form.elements.f_dataInicio.disabled = !!blocked;
                    form.elements.f_dataFim.disabled = !!blocked;
                    DashboardFiltrosMesesComerciais.mount(form, function () {
                        changes.push(Object.fromEntries(new FormData(form)));
                    });
                };
                window.clickMonth = function (month) {
                    form.querySelector('[data-commercial-month="' + month + '"]').click();
                };
                window.changeYear = function (year) {
                    var input = form.querySelector('input[type=number]');
                    input.value = year;
                    input.dispatchEvent(new Event('change'));
                };
                window.snapshot = function () {
                    return [form.elements.f_dataInicio.value, form.elements.f_dataFim.value,
                        Array.from(form.querySelectorAll('[aria-pressed=true]')).map(b => b.dataset.commercialMonth)];
                };
                mount('2026-10-01', '2026-10-26');
            """)
            assert c.js('snapshot()') == ['2026-10-01', '2026-10-26', ['2026-10']]
            assert c.js('changes.length') == 0
            c.js("clickMonth('2026-10')")
            assert c.js('snapshot()') == ['2026-01-01', '2026-10-26', []]
            assert c.js("form.querySelector('p').textContent.includes('Acumulado do ano')")
            assert c.js("changes[0]") == dict(f_dataInicio='2026-01-01', f_dataFim='2026-10-26',
                f_equipe='william', f_vendedor='7', f_cliente='Cliente teste')
            c.js("mount(form.elements.f_dataInicio.value, form.elements.f_dataFim.value)")
            assert c.js('snapshot()') == ['2026-01-01', '2026-10-26', []], 'Preservar acumulado ao remontar'
            c.js("clickMonth('2026-10'); clickMonth('2026-09')")
            assert c.js('snapshot()') == ['2026-09-01', '2026-09-30', ['2026-09']]
            c.js("clickMonth('2026-09')")
            assert c.js('snapshot()') == ['2026-01-01', '2026-10-26', []], 'Acumulado termina hoje, mesmo ao desmarcar setembro'
            count = c.js('changes.length')
            c.js("clickMonth('2026-11')")
            assert c.js('changes.length') == count, 'Mês futuro desabilitado'
            c.js('changeYear(2025)')
            assert c.js('snapshot()') == ['2025-01-01', '2025-12-31', []]
            c.js("clickMonth('2025-02')")
            assert c.js('snapshot()') == ['2025-02-01', '2025-02-28', ['2025-02']]
            c.js('changeYear(2024)')
            assert c.js('snapshot()') == ['2024-02-01', '2024-02-29', ['2024-02']]
            c.js("mount('2026-01-01', '2026-01-31')")
            assert c.js('snapshot()') == ['2026-01-01', '2026-01-31', ['2026-01']]
            c.js("clickMonth('2026-01')")
            assert c.js('snapshot()') == ['2026-01-01', '2026-10-26', []]
            c.js("mount('2026-10-05', '2026-10-20', true); clickMonth('2026-10')")
            assert c.js('snapshot()') == ['2026-10-05', '2026-10-20', ['2026-10']], 'Respeitar período bloqueado pelo acesso'
            assert not c.errors, c.errors
            print('OK: alternância mês/ano, restauração do período, filtros preservados, anos anteriores, fevereiro e acesso bloqueado.')
        finally:
            proc.terminate()
            proc.wait(timeout=10)
