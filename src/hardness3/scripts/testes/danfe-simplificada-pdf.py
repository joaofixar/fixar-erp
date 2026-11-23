#!/usr/bin/env python3
"""Verifica a saída gráfica com Poppler, usando somente os PDFs fictícios do teste PHP."""
from pathlib import Path
import re
import subprocess
import sys
import xml.etree.ElementTree as ET
import ctypes as C

# Opcional: biblioteca ZBar fornecida pelo ambiente, sem instalar dependências no projeto.
leitor = C.CDLL(sys.argv[2]) if len(sys.argv) > 2 else None
if leitor:
    for nome_funcao, argumentos, retorno in (
        ('zbar_image_scanner_create', [], C.c_void_p),
        ('zbar_image_scanner_set_config', [C.c_void_p, C.c_int, C.c_int, C.c_int], C.c_int),
        ('zbar_image_create', [], C.c_void_p),
        ('zbar_image_set_format', [C.c_void_p, C.c_ulong], None),
        ('zbar_image_set_size', [C.c_void_p, C.c_uint, C.c_uint], None),
        ('zbar_image_set_data', [C.c_void_p, C.c_void_p, C.c_ulong, C.c_void_p], None),
        ('zbar_scan_image', [C.c_void_p, C.c_void_p], C.c_int),
        ('zbar_image_first_symbol', [C.c_void_p], C.c_void_p),
        ('zbar_symbol_get_data', [C.c_void_p], C.c_char_p),
        ('zbar_symbol_next', [C.c_void_p], C.c_void_p),
        ('zbar_image_destroy', [C.c_void_p], None),
        ('zbar_image_scanner_destroy', [C.c_void_p], None),
    ):
        funcao = getattr(leitor, nome_funcao)
        funcao.argtypes, funcao.restype = argumentos, retorno


def ler_qr(arquivo, pagina):
    raster = subprocess.check_output(['pdftoppm', '-f', str(pagina), '-singlefile', '-r', '200', '-gray', str(arquivo)])
    cabecalho = re.match(rb'P5\s+(\d+)\s+(\d+)\s+255\s', raster)
    assert cabecalho, 'Imagem de teste inválida'
    largura, altura = map(int, cabecalho.groups())
    pixels = C.create_string_buffer(raster[cabecalho.end():])
    scanner, imagem = leitor.zbar_image_scanner_create(), leitor.zbar_image_create()
    try:
        leitor.zbar_image_scanner_set_config(scanner, 0, 0, 1)
        leitor.zbar_image_set_format(imagem, int.from_bytes(b'Y800', 'little'))
        leitor.zbar_image_set_size(imagem, largura, altura)
        leitor.zbar_image_set_data(imagem, pixels, largura * altura, None)
        assert leitor.zbar_scan_image(scanner, imagem) > 0, 'QR impresso não pôde ser lido'
        simbolo, destinos = leitor.zbar_image_first_symbol(imagem), []
        while simbolo:
            destinos.append(leitor.zbar_symbol_get_data(simbolo))
            simbolo = leitor.zbar_symbol_next(simbolo)
        return destinos
    finally:
        leitor.zbar_image_destroy(imagem)
        leitor.zbar_image_scanner_destroy(scanner)

pasta = Path(sys.argv[1] if len(sys.argv) > 1 else '/tmp/danfe-simplificada-testes')
for nome in ('normal', 'cancelada', 'sem-qr', 'sc-1', 'sc-2', 'sem-ajustes', 'multipagina', 'textos-longos'):
    arquivo = pasta / (nome + '.pdf')
    conteudo = arquivo.read_bytes()
    texto = subprocess.check_output(['pdftotext', '-layout', str(arquivo), '-']).decode()
    doc = ET.fromstring(subprocess.check_output(['pdftotext', '-bbox', str(arquivo), '-']))
    paginas = doc.findall('.//{*}page')
    for pagina in paginas:
        largura, altura = float(pagina.attrib['width']), float(pagina.attrib['height'])
        assert abs(largura * 25.4 / 72 - 80) < .05, (nome, 'largura diferente de 80 mm')
        assert altura * 25.4 / 72 <= 297.05, (nome, 'altura excessiva')
        for palavra in pagina.findall('.//{*}word'):
            assert 7 <= float(palavra.attrib['xMin']) <= float(palavra.attrib['xMax']) <= largura - 7, (nome, palavra.text, 'margem lateral')
            assert 0 <= float(palavra.attrib['yMin']) <= float(palavra.attrib['yMax']) <= altura, (nome, palavra.text, 'altura')
    folhas = re.findall(r'Folha\s+(\d+)/(\d+)', texto)
    assert folhas == [(str(i), str(len(paginas))) for i in range(1, len(paginas) + 1)], (nome, 'numeração')
    assert 'DANFE SIMPLIFICADO TIPO 2' in texto, (nome, 'título')
    assert 'REPRESENTAÇÃO SIMPLIFICADA' not in texto, (nome, 'título antigo')
    assert '141260000000123' in texto and '13/10/2026 10:21:00' in texto, (nome, 'autorização')
    assert '300,123456' in texto, (nome, 'precisão do preço unitário')
    assert '522,97' not in texto, (nome, 'Valor a pagar deve usar vNF, conforme manual')
    assert re.search(r'Troco R\$\s+[\d.,]+', texto), (nome, 'Troco obrigatório ausente')
    assert 'CONSUMIDOR CNPJ:' in texto and 'Qtde. total de itens' in texto
    if nome == 'sem-qr':
        assert '515,12' in texto and 'CBS R$' not in texto
        assert re.search(r'Troco R\$\s+0,00', texto), (nome, 'Troco zero ausente')
        destino = b'http://www.fazenda.pr.gov.br/nfce/qrcode?p=41261012345678000195550010000001231000001230|3|2'
    else:
        for esperado in ('Dinheiro', 'PIX', '(+) CBS R$', '(+) IBS R$', '3,33', '0,52'):
            assert esperado in texto, (nome, esperado)
        if nome == 'sem-ajustes':
            assert 'Acréscimos R$' not in texto and 'Desconto R$' not in texto and 'Valor a pagar R$' not in texto
            assert '(+) IS R$' not in texto and 'Rua da Entrega' in texto
            assert re.search(r'Troco R\$\s+0,00', texto)
        else:
            assert re.search(r'Valor a pagar R\$\s+515,12', texto)
            assert re.search(r'Acréscimos R\$\s+10,00', texto)
            assert '14,88' in texto and '(+) IS R$' in texto
        destino = b'https://example.com/nfe?chave=41261012345678000195550010000001231000001230&ambiente=2'
        if nome in ('sc-1', 'sc-2'):
            dominio = 'hom.sat.sef.sc.gov.br' if nome == 'sc-2' else 'sat.sef.sc.gov.br'
            destino = ('https://' + dominio + '/nfce/consulta?p=42261012345678000195550010000001231000001230|3|' + nome[-1]).encode()
    link = re.search(rb'/Subtype /Link /Rect \[([\d.]+) ([\d.]+) ([\d.]+) ([\d.]+)\].*?/URI \(([^)]+)\)', conteudo)
    assert link, (nome, 'QR sem link')
    x1, y1, x2, y2 = (float(link[i]) for i in range(1, 5))
    assert abs((x2 - x1) * 25.4 / 72 - 30) < .05 and abs((y1 - y2) * 25.4 / 72 - 30) < .05, (nome, 'dimensão do QR')
    assert link[5] == destino, (nome, 'destino incorreto')
    folha_qr = next(i for i, folha in enumerate(texto.split('\f'), 1) if 'Protocolo de autorização:' in folha)
    texto_qr = texto.split('\f')[folha_qr - 1]
    assert 'Consulte pela Chave de Acesso em' in texto_qr and 'CONSUMIDOR CNPJ:' in texto_qr, (nome, 'bloco de consulta dividido')
    chave = re.search(r'\b(?:\d{4} ){10}\d{4}\b', texto_qr)
    assert chave and chave[0].replace(' ', '').encode() in destino, (nome, 'chave deve ter 11 blocos de 4 na mesma linha')
    if leitor:
        assert ler_qr(arquivo, folha_qr) == [destino], (nome, 'QR lido difere do destino esperado')
    aviso = 'EMITIDA EM AMBIENTE DE HOMOLOGAÇÃO - SEM VALOR FISCAL'
    assert (aviso in ' '.join(texto.split())) == (nome != 'sc-1'), (nome, 'ambiente da impressão incorreto')
    if nome == 'cancelada':
        assert 'NF-e CANCELADA' in texto, 'Cancelamento não identificado'
    if nome == 'multipagina':
        assert len(paginas) > 1
        for i in range(3, 81):
            assert texto.count('MULTI%03d' % i) == 1, (nome, i, 'item perdido/duplicado')
    if nome == 'textos-longos':
        assert len(paginas) > 1
        for marcador in ('FIM-ITEM-LONGO', 'FIM-OBSERVACAO-LONGA'):
            assert texto.count(marcador) == 1, (nome, marcador)
    print('%s: %d folha(s), campos, margens e QR %s' % (nome, len(paginas), 'decodificado' if leitor else 'conferidos'))
