#!/usr/bin/env python
# Reaplica os ajustes do PHP 8 e o gancho do desktop em cima da árvore da publicação.
# Caminhos alterados saem no stdout. Se um trecho não encaixa, termina em erro.
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
# A lista da empresa no shell mostra o nome completo. O select do rodapé continua abreviado.
EMPRESA_OPCAO = (
    b"$selectEmpresa .= \"<option value='{$row['C004_Id']}'{$selected}>{$row['C004_Nome_Abreviado']}</option>\";"
)
EMPRESA_OPCAO_COMPLETA = (
    b"$selectEmpresa .= \"<option value='{$row['C004_Id']}'{$selected} data-nome='\" . htmlspecialchars($row['C004_Nome_Empresa'], ENT_QUOTES) . \"'>{$row['C004_Nome_Abreviado']}</option>\";"
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


# Trocas pontuais: (arquivo, trecho do PHP 7, trecho que o PHP 8 aceita).
PONTUAIS = (
    # Divisão por zero na média do total quando a grade vem vazia.
    (
        "sistema/totais.php",
        b"'media' => $parcial['soma'] / $parcial['count']",
        b"'media' => $parcial['count'] ? $parcial['soma'] / $parcial['count'] : 0",
    ),
    # implode() recusa nulo quando o formulário não tem campo padrão de pesquisa.
    (
        "sistema/automacaoFormulario.php",
        b"implode(' ', $campoPadraoPesquisa);",
        b"implode(' ', is_array($campoPadraoPesquisa) ? $campoPadraoPesquisa : array());",
    ),
    # Ternário encadeado sem parênteses virou erro de compilação.
    (
        "bibliotecas/SPED/EFD/bloco_D.php",
        b"? '21' : ($T013['T013_Tipo'] == '8') ? '22' : '';",
        b"? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');",
    ),
    (
        "bibliotecas/SPED/Fiscal/bloco_D.php",
        b"? '21' : ($T013['T013_Tipo'] == '8') ? '22' : '';",
        b"? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');",
    ),
    (
        "prog/sistema/funcoes/gridFiltro.php",
        b"$count = ($count > 15) ? 15 : ($count < 0) ? 0 : $count;",
        b"$count = ($count > 15) ? 15 : (($count < 0) ? 0 : $count);",
    ),
    # Constante sem aspas virou Error.
    (
        "configuracoes/T084.php",
        b"$mT084['T084_Flag_Entrada_Saida'] == S)",
        b"$mT084['T084_Flag_Entrada_Saida'] == 'S')",
    ),
    # A vírgula antes do <?php sai na resposta e derruba o namespace.
    (
        "bibliotecas/classes/CAD012.php",
        b",<?php",
        b"<?php",
    ),
)

# A área de trabalho e a cobrança. O trecho seguro contém o cru, então a
# presença do seguro vem primeiro para não aplicar duas vezes.
AREA = (
    (
        "sistema/index-framework.php",
        b"verificaBoleto($Cnpj_Empresa_Usuaria, 'S', 63, true);",
        b"if (false) { verificaBoleto($Cnpj_Empresa_Usuaria, 'S', 63, true); }",
    ),
    (
        "sistema/index-framework.php",
        b"verificaBoletoParceiro('', 'S', 63, true);",
        b"if (false) { verificaBoletoParceiro('', 'S', 63, true); }",
    ),
    (
        "sistema/index-framework.php",
        b"$g['smarty']->assign('certificado', $alertaCertificado);",
        b"$g['smarty']->assign('certificado', $alertaCertificado);"
        b" require_once __DIR__ . '/../../fx/area.php';"
        b" $g['smarty']->assign('avisos', fxAreaTrabalho($g));",
    ),
    (
        "prog/etc/etc011/tab.php",
        "$tabsList[] = array(uniqid(), '/etc/etc011/outros/etc011Avisos/', 'Início', '&tab=geral');".encode(),
        b"/* o cockpit abre nos dashboards */",
    ),
)
# function __autoload() deixou de existir; o corpo de uma linha vai para o spl_autoload_register.
AUTOLOAD = re.compile(br"function __autoload\((\$\w+)\)\s*\{([^{}]*)\}")
# 'colunaAlinhar' => center: constante indefinida no PHP 8.
ALINHAR = re.compile(br"('colunaAlinhar'\s*=>\s*)(center|right|left)\b(?!['\"])")
# $x =& new Classe: erro de compilação no PHP 8.
REFERENCIA_NEW = re.compile(br"=\s*&\s*new\b")
NOME = re.compile(br"[A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*")


def pular_string(texto, i):
    # Devolve o índice logo depois da string que começa em texto[i].
    aspa = texto[i:i + 1]
    i += 1
    while i < len(texto):
        c = texto[i:i + 1]
        if c == b"\\":
            i += 2
            continue
        if c == aspa:
            return i + 1
        if aspa != b"'" and c == b"{" and texto[i + 1:i + 2] == b"$":
            i = pular_par(texto, i, b"{", b"}")
            continue
        i += 1
    return i


def pular_heredoc(texto, i):
    m = re.compile(br"<<<[ \t]*(['\"]?)([A-Za-z_][A-Za-z0-9_]*)\1\r?\n").match(texto, i)
    if not m:
        return None
    fim = re.compile(br"\n[ \t]*" + m.group(2) + br"\b").search(texto, m.end() - 1)
    return fim.end() if fim else len(texto)


def pular_par(texto, i, abre, fecha, trocar=None):
    # texto[i] é `abre`; devolve o índice depois do `fecha` correspondente.
    profundidade = 0
    while i < len(texto):
        c = texto[i:i + 1]
        if c in (b"'", b'"', b"`"):
            i = pular_string(texto, i)
            continue
        if c == abre:
            profundidade += 1
        elif c == fecha:
            profundidade -= 1
            if profundidade == 0:
                if trocar is not None:
                    trocar.append(i)
                return i + 1
        i += 1
    return i


def adaptar_chaves(texto):
    # $x{0} -> $x[0]. O PHP 8 recusa chave como índice de string e array.
    # Só mexe no código: string, comentário e HTML ficam como estão.
    trocas = []
    i, n, codigo = 0, len(texto), False
    while i < n:
        if not codigo:
            j = texto.find(b"<?", i)
            if j < 0:
                break
            i, codigo = j + 2, True
            continue
        c = texto[i:i + 1]
        if texto.startswith(b"?>", i):
            i, codigo = i + 2, False
        elif c in (b"'", b'"', b"`"):
            i = pular_string(texto, i)
        elif texto.startswith(b"<<<", i):
            i = pular_heredoc(texto, i) or i + 3
        elif texto.startswith(b"/*", i):
            j = texto.find(b"*/", i + 2)
            i = n if j < 0 else j + 2
        elif c == b"#" or texto.startswith(b"//", i):
            m = re.compile(br"\n|\?>").search(texto, i)
            i = n if not m else m.start()
        elif c == b"$":
            m = NOME.match(texto, i + 1)
            if not m:
                i += 1
                continue
            j = m.end()
            # Segue a cadeia $x[...]->y{...}; só a chave colada vira colchete.
            while j < n:
                if texto[j:j + 1] == b"[":
                    j = pular_par(texto, j, b"[", b"]")
                elif texto.startswith(b"->", j) and NOME.match(texto, j + 2):
                    j = NOME.match(texto, j + 2).end()
                elif texto[j:j + 1] == b"{":
                    trocas.append(j)
                    j = pular_par(texto, j, b"{", b"}", trocas)
                else:
                    break
            i = m.end()
        else:
            i += 1
    if not trocas:
        return texto
    novo = bytearray(texto)
    for posicao in trocas:
        novo[posicao:posicao + 1] = b"[" if novo[posicao:posicao + 1] == b"{" else b"]"
    return bytes(novo)


# O desktop liga o shell por este trecho. A publicação devolve o template sem ele.
ANCORA_GANCHO = (
    b"\t\t{$cssJs}\n"
    b'\t\t<style type="text/css">\n'
    b"\t\t{$cssExtra}\n"
    b"\t\t</style>\n"
)
GANCHO = (
    ANCORA_GANCHO
    + b'\t\t<link rel="stylesheet" href="/fx/shell.css" type="text/css">\n'
    + b'\t\t<link rel="stylesheet" href="/fx/tema.css" type="text/css">\n'
    + b'\t\t<meta name="fx-empresa-nome" content="{$empresaAtualNome|escape:\'html\'}">\n'
    + b'\t\t<script src="/fx/shell.js" defer></script>\n'
)


def adaptar_gancho(texto):
    if GANCHO in texto:
        return texto
    if ANCORA_GANCHO in texto:
        return texto.replace(ANCORA_GANCHO, GANCHO, 1)
    return None


def adaptar_pontuais(relativo, texto):
    for arquivo, cru, seguro in PONTUAIS:
        if arquivo != relativo:
            continue
        if cru in texto:
            texto = texto.replace(cru, seguro)
        elif seguro not in texto:
            return None
    return texto


def adaptar_area(relativo, texto):
    for arquivo, cru, seguro in AREA:
        if arquivo != relativo:
            continue
        if seguro in texto:
            continue
        if cru not in texto:
            return None
        texto = texto.replace(cru, seguro, 1)
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
        novo = adaptar_chaves(novo)
        novo = ALINHAR.sub(br"\1'\2'", novo)
        novo = REFERENCIA_NEW.sub(b"= new", novo)
        novo = AUTOLOAD.sub(br"spl_autoload_register(function (\1) {\2});", novo)
        pontual = adaptar_pontuais(relativo, novo)
        if pontual is None:
            print(f"A adaptação de {relativo} não encaixa.", file=sys.stderr)
            return 1
        novo = pontual
        area = adaptar_area(relativo, novo)
        if area is None:
            print(f"A adaptação de {relativo} não encaixa.", file=sys.stderr)
            return 1
        novo = area
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
            if EMPRESA_OPCAO in novo:
                novo = novo.replace(EMPRESA_OPCAO, EMPRESA_OPCAO_COMPLETA)
            elif EMPRESA_OPCAO_COMPLETA not in novo:
                print(
                    "A adaptação de sistema/index-framework.php não encaixa.",
                    file=sys.stderr,
                )
                return 1
        if novo != original:
            caminho.write_bytes(novo)
            alterados.append(relativo)
    gancho = raiz / "smarty/templates/index.tpl"
    if gancho.is_file():
        original = gancho.read_bytes()
        novo = adaptar_gancho(original)
        if novo is None:
            print(
                "A adaptação de smarty/templates/index.tpl não encaixa.",
                file=sys.stderr,
            )
            return 1
        if novo != original:
            gancho.write_bytes(novo)
            alterados.append("smarty/templates/index.tpl")
    for relativo in alterados:
        print(relativo)
    return 0


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Uso: adaptar-hardness.py DIRETORIO", file=sys.stderr)
        sys.exit(2)
    sys.exit(adaptar(sys.argv[1]))
