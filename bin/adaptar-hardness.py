#!/usr/bin/env python
# Reaplica os ajustes do PHP 8 em cima da árvore da publicação.
# Caminhos alterados saem no stdout. Se o funcoesGerais.php não encaixa, termina em erro.
import re
import sys
from pathlib import Path

CHAMADA = re.compile(
    br"mysqli_connect_error\s*\(\s*\\?\$[A-Za-z_][A-Za-z0-9_]*\s*\)"
)
SEM_ARGUMENTO = b"mysqli_connect_error()"
CONEXOES = {
    "bibliotecas/conexaoBanco.php",
    "bibliotecas/conexaoBancoEspelhado.php",
}
LOGS = (
    b"log('conexaoBanco');",
    b"log('ERRO Conexao mysql = ' .$Error);",
)
LISTA_NULA = (
    b"$display = array_shift(array_values($g['smarty']->template_objects))->resource_name;"
)
LISTA_VAZIA = (
    b"$display = (($__modelos = $g['smarty']->template_objects) && is_array($__modelos))"
    b" ? array_shift($__modelos)->resource_name : '';"
)
CADEIA = (
    b"jGJfq: $g['divId-last'] = $g['divIdChain'][count($g['divIdChain']) - 1];"
)
CADEIA_SEGURA = (
    b"jGJfq: if (!is_array($g['divIdChain'])) { $g['divIdChain'] = array($g['divId']); }"
    b" $g['divId-last'] = $g['divIdChain'][count($g['divIdChain']) - 1];"
)
PAI = (
    b"wQr4K: $g['divIdParent-last'] = $g['divIdParentChain'][count($g['divIdParentChain']) - 1];"
)
PAI_SEGURO = (
    b"wQr4K: if (!is_array($g['divIdParentChain'])) { $g['divIdParentChain'] = array(); }"
    b" $g['divIdParent-last'] = $g['divIdParentChain'] ? $g['divIdParentChain'][count($g['divIdParentChain']) - 1] : '';"
)


def guardar_log(texto, chamada):
    # A conexão chama log() antes de o funcoesGerais.php declarar a função.
    # No PHP 8 o nome cai na função matemática. A guarda deixa o pedido seguir.
    guarda = b"if (function_exists(__NAMESPACE__ . '\\\\log')) { " + chamada + b" }"
    marcador = b"\x00guarda-log\x00"
    texto = texto.replace(guarda, marcador)
    texto = texto.replace(chamada, guarda)
    return texto.replace(marcador, guarda)


def adaptar(raiz):
    raiz = Path(raiz)
    alterados = []
    for caminho in sorted(raiz.rglob("*.php")):
        if not caminho.is_file():
            continue
        original = caminho.read_bytes()
        novo = CHAMADA.sub(SEM_ARGUMENTO, original)
        relativo = caminho.relative_to(raiz).as_posix()
        if relativo in CONEXOES:
            for chamada in LOGS:
                novo = guardar_log(novo, chamada)
        if relativo == "bibliotecas/funcoesGerais.php":
            if LISTA_NULA in novo:
                novo = novo.replace(LISTA_NULA, LISTA_VAZIA)
            elif LISTA_VAZIA not in novo:
                print(
                    "A adaptação de bibliotecas/funcoesGerais.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
        if relativo == "sistema/index-framework.php":
            if CADEIA in novo:
                novo = novo.replace(CADEIA, CADEIA_SEGURA)
            elif CADEIA_SEGURA not in novo:
                print(
                    "A adaptação de sistema/index-framework.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
            if PAI in novo:
                novo = novo.replace(PAI, PAI_SEGURO)
            elif PAI_SEGURO not in novo:
                print(
                    "A adaptação de sistema/index-framework.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
        if novo != original:
            caminho.write_bytes(novo)
            alterados.append(relativo)
    for relativo in alterados:
        print(relativo)
    return 0


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Uso: adaptar-hardness.py DIRETORIO", file=sys.stderr)
        sys.exit(2)
    sys.exit(adaptar(sys.argv[1]))
