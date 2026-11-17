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
# No PHP 8 o mysqli recusa false. A consulta que falha devolve o vazio do PHP 7.
CONSULTAS = (
    (
        b"function mysql_fetch_assoc(",
        b"$retorno = \\mysqli_fetch_assoc($result);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_fetch_assoc($result) : null;",
    ),
    (
        b"function mysql_fetch_row(",
        b"$retorno = \\mysqli_fetch_row($result);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_fetch_row($result) : null;",
    ),
    (
        b"function mysql_fetch_array(",
        b"$retorno = \\mysqli_fetch_array($result);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_fetch_array($result) : null;",
    ),
    (
        b"function mysql_num_rows(",
        b"$retorno = \\mysqli_num_rows($result);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_num_rows($result) : 0;",
    ),
    (
        b"function mysql_free_result(",
        b"$retorno = \\mysqli_free_result($result);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_free_result($result) : null;",
    ),
    (
        b"function mysql_data_seek(",
        b"$retorno = \\mysqli_data_seek($result, $offset);",
        b"$retorno = $result instanceof \\mysqli_result ? \\mysqli_data_seek($result, $offset) : false;",
    ),
)


URLENCODE = (
    (
        b"function gSubstituirVariaveisParametros(",
        b"J4HNX: if (!(is_array($value) || is_object($value))) { goto hqhm0; }",
        b"J4HNX: if (is_scalar($value) || $value === null) { goto hqhm0; }",
    ),
    (
        b"function gSubstituirVariaveisParametros(",
        b"WMhAt: if (!(is_array($value) || is_object($value))) { goto HdfZJ; }",
        b"WMhAt: if (is_scalar($value) || $value === null) { goto HdfZJ; }",
    ),
    (
        b"function gSubstituirVariaveisParametros(",
        b"NPd5B: if (!(is_array($value) || is_object($value))) { goto mHdBj; }",
        b"NPd5B: if (is_scalar($value) || $value === null) { goto mHdBj; }",
    ),
)


GRID = (
    (
        b"class interfaceUsuario",
        b"public $gridFiltro, $colunasOcultar;",
        b"public $gridFiltro, $colunasOcultar = array();",
    ),
    (
        b"class interfaceUsuario",
        b"NTwn6: $value[0] = isset($value[0]) ? $value[0] : '';",
        b"NTwn6: if (is_string($value)) { $value = array($value, ''); } $value[0] = isset($value[0]) ? $value[0] : '';",
    ),
    (
        b"class interfaceUsuario",
        b"PQbU1: $callbackParameterBase64 = isset($grid_valor['callbackParameter']) ? base64_encode($grid_valor['callbackParameter']) : '';",
        b"PQbU1: $callbackParameterBase64 = isset($grid_valor['callbackParameter']) && is_scalar($grid_valor['callbackParameter']) ? base64_encode($grid_valor['callbackParameter']) : '';",
    ),
    (
        b"class interfaceUsuario",
        b"in_array($tabelaId, $tables)",
        b"in_array($tabelaId, is_array($tables) ? $tables : array())",
    ),
    (
        b"class interfaceUsuario",
        b"if (!(count($tables) > 0))",
        b"if (!(is_array($tables) && count($tables) > 0))",
    ),
)


def adaptar_grid(texto):
    # O PHP 8 recusa in_array em nulo e string vazia numa posição de string.
    for marca, cru, seguro in GRID:
        if marca not in texto:
            return texto
        if cru in texto:
            texto = texto.replace(cru, seguro)
        elif seguro not in texto:
            return None
    return texto


def adaptar_urlencode(texto):
    # O PHP 8 recusa resource no urlencode. A função percorre os globais.
    for marca, cru, seguro in URLENCODE:
        if marca not in texto:
            return texto
        if cru in texto:
            texto = texto.replace(cru, seguro)
        elif seguro not in texto:
            return None
    return texto


def adaptar_consultas(texto):
    for marca, cru, seguro in CONSULTAS:
        if marca not in texto:
            continue
        if cru in texto:
            texto = texto.replace(cru, seguro)
        elif seguro not in texto:
            return None
    return texto


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
            consultas = adaptar_consultas(novo)
            if consultas is None:
                print(
                    "A adaptação de bibliotecas/funcoesGerais.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
            urlencode = adaptar_urlencode(consultas)
            if urlencode is None:
                print(
                    "A adaptação de bibliotecas/funcoesGerais.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
            novo = urlencode
        if relativo == "sistema/interfaceUsuario.php":
            grid = adaptar_grid(novo)
            if grid is None:
                print(
                    "A adaptação de sistema/interfaceUsuario.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
            novo = grid
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
