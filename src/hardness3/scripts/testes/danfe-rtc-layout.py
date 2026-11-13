#!/usr/bin/env python3
"""Confere divisões e alinhamentos contra medidas das páginas 8/9 da NT, no PDF gerado."""
from pathlib import Path
import re
import subprocess
import sys
import xml.etree.ElementTree as ET
import zlib

pasta = Path(sys.argv[1] if len(sys.argv) > 1 else '/tmp/danfe-rtc-testes')
# Medições independentes do documento fornecido, em pontos PDF.
medidas = {
    'P': {
        'cabecalho': [28.5, 201, 327, 581],
        'inscricoes': [28.5, 201, 398.5, 581],
        'destinatario': [28.5, 338, 479, 581],
        'endereco': [28.5, 282.5, 415, 479, 581],
        'duplicatas': [28.5, 100.75, 159.25, 202.75, 289.75, 340.75, 391.75, 471.75, 529.75, 581],
        'produtos': [28.5, 145.5, 180, 221.5, 270, 318.5, 394.5, 470.5, 581],
        'transporte': [28.5, 194.25, 271.25, 348.75, 426.25, 470.25, 581],
        'adicionais': [28.5, 276, 461.5, 467.25, 581],
        'passo': 5.7,
    },
    'L': {
        'cabecalho': [21.5, 274.5, 424, 766.5],
        'inscricoes': [21.5, 274.5, 520.5, 766.5],
        'destinatario': [21.5, 439, 629.5, 766.5],
        'endereco': [21.5, 364, 543, 629.5, 766.5],
        'duplicatas': [21.5, 119.25, 197.75, 256.25, 374.25, 442.75, 511.5, 619.25, 697.75, 766.5],
        'produtos': [21.5, 262.5, 312, 368.5, 425.5, 475, 553, 645, 766.5],
        'transporte': [21.5, 244.75, 349.25, 453.75, 557.75, 617.25, 766.5],
        'adicionais': [21.5, 381, 655.5, 661.75, 766.5],
        'passo': 6.3,
    },
}

def retangulos(arquivo):
    ret = []
    for match in re.finditer(rb'stream\r?\n(.*?)\r?\nendstream', arquivo.read_bytes(), re.S):
        conteudo = match.group(1)
        try: conteudo = zlib.decompress(conteudo)
        except zlib.error: pass
        for r in re.finditer(rb'([-\d.]+) ([-\d.]+) ([-\d.]+) ([-\d.]+) re ([BfS])', conteudo):
            ret.append(tuple(float(r[i]) for i in range(1, 5)) + (r[5].decode(),))
    return ret

for orientacao, modelo in medidas.items():
    arquivo = pasta / ('rtc-%s.pdf' % orientacao)
    rs = retangulos(arquivo)
    for grupo, xs in modelo.items():
        if not isinstance(xs, list): continue
        pares = [(a, b) for a, b in zip(xs, xs[1:])]
        if grupo == 'adicionais': pares = [pares[0], pares[1], pares[3]]  # A terceira faixa é o vão ao lado do QR.
        for a, b in pares:
            assert any(abs(x-a) < .4 and abs(w-(b-a)) < .4 for x,y,w,h,cor in rs), (orientacao, grupo, a, b, 'divisão do modelo não encontrada')
    doc = ET.fromstring(subprocess.check_output(['pdftotext', '-bbox-layout', str(arquivo), '-']))
    linhas = doc.findall('.//{*}line')
    texto = lambda l: ' '.join(w.text or '' for w in l.findall('{*}word'))
    protocolo = next(l for l in linhas if texto(l).startswith('141260000000123'))
    direita = modelo['cabecalho'][-1]
    assert direita - 4 < float(protocolo.attrib['xMax']) < direita, (orientacao, 'protocolo não alinhado à direita')
    assert float(protocolo.attrib['xMin']) > modelo['cabecalho'][2] + 30, (orientacao, 'protocolo no início da célula')
    cnpj = next(w for w in doc.findall('.//{*}word') if w.text == '12.345.678/0001-95')
    assert direita - 4 < float(cnpj.attrib['xMax']) < direita, (orientacao, 'CNPJ do emitente não alinhado à direita')
    for coluna in [6, 7]:
        esquerda, limite = modelo['produtos'][coluna:coluna+2]
        celulas = []
        for linha in linhas:
            palavras = [w for w in linha.findall('{*}word') if esquerda < float(w.attrib['xMin']) < limite]
            if not palavras: continue
            t = ' '.join(w.text or '' for w in palavras)
            if '/' not in t or not t.startswith(('ICMS', 'IBS')): continue
            celulas.append({'texto': t, 'yMin': min(float(w.attrib['yMin']) for w in palavras), 'xMax': max(float(w.attrib['xMax']) for w in palavras)})
        tributos = sorted(celulas, key=lambda l: l['yMin'])[:3]
        assert len(tributos) == 3, (orientacao, coluna, 'tributos não estão em três linhas')
        for l,par in zip(tributos, [('ICMS','CBS'),('IBS UF','IPI'),('IBS MUN','IS')]):
            t=l['texto']
            assert t.startswith(par[0]) and re.search(r'/\s*'+par[1],t), (orientacao, coluna, t)
            assert l['xMax'] < limite, (orientacao, coluna, 'texto do tributo ultrapassa a divisão')
        for a,b in zip(tributos,tributos[1:]):
            assert abs(b['yMin']-a['yMin']-modelo['passo']) < 1, (orientacao, 'entrelinhas dos tributos')
    qr = next(l for l in linhas if texto(l)=='QR CODE')
    extras = next(l for l in linhas if texto(l)=='DADOS ADICIONAIS')
    assert abs(float(qr.attrib['yMin'])-float(extras.attrib['yMin'])) < 1, (orientacao, 'QR fora do cabeçalho final')
    xqr, fimqr = modelo['adicionais'][-2:]
    assert abs((float(qr.attrib['xMin'])+float(qr.attrib['xMax']))/2-(xqr+fimqr)/2) < 1, (orientacao, 'título QR fora do centro')
    yqr=float(qr.attrib['yMax']);altura=float(doc.find('.//{*}page').attrib['height'])
    assert sum(cor=='f' and xqr < x < fimqr and altura-y > yqr and 0<w<3 for x,y,w,h,cor in rs) > 100, (orientacao,'matriz QR ausente apesar da tag no XML')
    print('OK: %s, divisões medidas na NT, protocolo à direita, tributos pareados e QR no quadro final.' % orientacao)
