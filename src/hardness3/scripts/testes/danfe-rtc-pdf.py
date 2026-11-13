#!/usr/bin/env python3
"""Confere os PDFs produzidos por danfe-rtc.php; requer pdftotext (Poppler)."""
from pathlib import Path
import re
import subprocess
import sys
import xml.etree.ElementTree as ET

pasta = Path(sys.argv[1] if len(sys.argv) > 1 else '/tmp/danfe-rtc-testes')
arquivos = sorted(pasta.glob('*.pdf'))
assert len(arquivos) >= 13, 'Execute danfe-rtc.php antes da conferência dos PDFs.'
for arquivo in arquivos:
    texto = subprocess.check_output(['pdftotext', '-layout', str(arquivo), '-']).decode()
    bbox = ET.fromstring(subprocess.check_output(['pdftotext', '-bbox', str(arquivo), '-']))
    paginas = bbox.findall('.//{*}page')
    for pagina in paginas:
        largura, altura = float(pagina.attrib['width']), float(pagina.attrib['height'])
        for palavra in pagina.findall('.//{*}word'):
            assert 0 <= float(palavra.attrib['xMin']) <= float(palavra.attrib['xMax']) <= largura + .01, (arquivo.name, palavra.text, 'largura')
            assert 0 <= float(palavra.attrib['yMin']) <= float(palavra.attrib['yMax']) <= altura + .01, (arquivo.name, palavra.text, 'altura')
    folhas = re.findall(r'FOLHA\s+(\d+)/(\d+)', texto)
    assert folhas == [(str(i), str(len(paginas))) for i in range(1, len(paginas) + 1)], (arquivo.name, 'numeração de folhas')
    if 'multipagina' in arquivo.name:
        for i in range(3, 46):
            assert texto.count('MULTI%03d' % i) == 1, (arquivo.name, i, 'item perdido/duplicado')
    if 'item-longo' in arquivo.name:
        assert texto.count('FIM-ITEM-LONGO') == 1, arquivo.name
    if 'observacao-longa' in arquivo.name:
        assert texto.count('FIM-OBSERVACAO-LONGA') == 1, arquivo.name
    if arquivo.name in ('rtc-P.pdf', 'rtc-L.pdf'):
        assert len(paginas) == 1, arquivo.name
        assert texto.index('TOTAL DO ICMS / IPI') < texto.index('TOTAL DO IBS / CBS / IS') < texto.index('DADOS DOS PRODUTOS'), arquivo.name
        for valor in ['0,04%', '0,36%', '0,00%', '0,90%', '522,97', '3,33', '0,37', '0,15', '200001', '000001', 'QR CODE']:
            assert valor in texto, (arquivo.name, valor)
        assert '9999,99' not in texto and '9.999,99' not in texto, 'Base de grupo aninhado indevida'
    if 'sem-opcionais' in arquivo.name:
        assert 'QR CODE' in texto, 'Quadro final do QR não foi preservado'
        for ausente in ['522,97', '[cClassTrib 200001]', '[cClassTrib 000001]', '0,04%', '0,36%', '3,33']:
            assert ausente not in texto, (arquivo.name, 'valor ausente no XML foi inventado', ausente)
    if 'cancelada-preview' in arquivo.name:
        assert 'NF-e CANCELADA' in texto and 'SEM AUTORIZAÇÃO DE USO' in texto, arquivo.name
    if 'sem-canhoto' in arquivo.name:
        assert 'DATA DE RECEBIMENTO' not in texto, arquivo.name
    if 'rtc-opcionais-' in arquivo.name:
        for esperado in ['VALOR DO FCP', 'DIFAL', 'MONOFÁSIC', 'CÁLCULO DO ISSQN', '42,00', '41,00', '2,05']:
            assert esperado in texto, (arquivo.name, esperado)
    print('%s: %d página(s), conteúdo e limites conferidos' % (arquivo.name, len(paginas)))
