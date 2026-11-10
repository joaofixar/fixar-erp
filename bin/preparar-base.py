"""Monta a cópia executável a partir de sistema-legado, sem gravar nele."""

import os
import re
import shutil
import sys

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
ORIGEM = os.path.join(RAIZ, "sistema-legado")
HTML = os.path.join(RAIZ, "base", "html")
COPIA = os.path.join(HTML, "hardness3")

CHAVES = re.compile(
    br"(\$[A-Za-z_][A-Za-z0-9_]*(?:->[A-Za-z_][A-Za-z0-9_]*)?(?:\[[^\[\]]+\])*)\{([^{}\[']+)\}"
)


def ignorar(diretorio, nomes):
    relativo = os.path.relpath(diretorio, ORIGEM).replace("\\", "/")
    if relativo == ".":
        relativo = ""
    fora = []
    for nome in nomes:
        caminho = f"{relativo}/{nome}" if relativo else nome
        if nome in {".git", "templates_c", "dados_usuarios"}:
            fora.append(nome)
        elif caminho in {"scripts/backup", "inecel", "outros/loginBoticario"}:
            fora.append(nome)
        elif relativo == "outros/personalizacoes" and nome != "fixar":
            fora.append(nome)
        elif nome == "confUsuario.php":
            fora.append(nome)
        elif nome.startswith("tema_") and nome.endswith("_cache.css"):
            fora.append(nome)
    return fora


def copiar():
    if not os.path.isdir(ORIGEM):
        sys.exit("sistema-legado/ não está nesta máquina")
    if os.path.isdir(HTML):
        shutil.rmtree(HTML)
    os.makedirs(HTML)
    print("Copiando o legado para a base local...")
    shutil.copytree(ORIGEM, COPIA, ignore=ignorar)
    shutil.copy2(os.path.join(ORIGEM, ".htaccess"), os.path.join(HTML, ".htaccess"))
    with open(os.path.join(HTML, "index.php"), "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write("<?php\nheader('Location: /login');\n")


def ler(caminho):
    with open(caminho, "rb") as arquivo:
        return arquivo.read()


def gravar(caminho, conteudo):
    with open(caminho, "wb") as arquivo:
        arquivo.write(conteudo)


def substituir(conteudo, antigo, novo, descricao):
    if novo in conteudo:
        return conteudo
    if antigo not in conteudo:
        sys.exit(f"não achei o trecho para {descricao}")
    return conteudo.replace(antigo, novo, 1)


def corrigir_chaves(raiz):
    for diretorio, _, arquivos in os.walk(raiz):
        for nome in arquivos:
            if not nome.endswith(".php"):
                continue
            caminho = os.path.join(diretorio, nome)
            original = ler(caminho)
            corrigido = CHAVES.sub(br"\1[\2]", original)
            if corrigido != original:
                gravar(caminho, corrigido)


def ajustar_entrada():
    caminho = os.path.join(COPIA, "sistema", "index-framework.php")
    texto = ler(caminho)
    texto = substituir(
        texto,
        b"""if (isset($g['r'][0]) && strpos($g['r'][0], 'dev_') !== false) {
    $_SESSION['dbDatabase'] = $g['r'][0];
    $_SESSION['Empresa_Atual'] = 1;
    $_SESSION['Usuario_Atual'] = 63;
    header('Location: /erp');
    die;
}""",
        b"""if (isset($g['r'][0]) && strpos($g['r'][0], 'dev_') !== false) {
    header('Location: /login');
    die;
}""",
        "entrada de administrador sem senha",
    )
    ancora = b"""if (isset($g['r'][0]) && $g['r'][0] == 'login') {
    header('Location: /hardness3/outros/login/index.php');
    die;
}"""
    vazio = ancora + b"""
if (!isset($g['r'][0]) || $g['r'][0] === '') {
    header('Location: /login');
    die;
}"""
    if b"header('Location: /login');\n    die;\n}\nif (!isset($g['r'][0]) || $g['r'][0] === '')" not in texto:
        texto = substituir(texto, ancora, vazio, "redirecionar a raiz ao login")
    texto = substituir(
        texto,
        b"$info['ip_externo'] = @file_get_contents('http://myexternalip.com/raw');",
        b"$info['ip_externo'] = '';",
        "consulta de IP externo",
    )
    texto = substituir(
        texto,
        b"$result = @file_get_contents(base64_decode('aHR0cHM6Ly9oYXJkbmVzcy5oYXJkbmVzcy5jb20uYnIvaGFyZG5lc3MzL291dHJvcy9zdGF0cy9pbmRleC5waHA='), false, $context);",
        "$result = false; // a base local não envia telemetria".encode(),
        "telemetria do fornecedor",
    )
    gravar(caminho, texto)


def ajustar_conexao():
    caminho = os.path.join(COPIA, "bibliotecas", "conexaoBanco.php")
    texto = ler(caminho)
    texto = substituir(
        texto,
        b"log('conexaoBanco');",
        b"if (function_exists(__NAMESPACE__ . '\\\\log')) { log('conexaoBanco'); }",
        "log antes da biblioteca",
    )
    texto = substituir(
        texto,
        b"log('ERRO Conexao mysql = ' .$Error);",
        b"if (function_exists(__NAMESPACE__ . '\\\\log')) { log('ERRO Conexao mysql = ' .$Error); }",
        "log de erro de conexão",
    )
    texto = texto.replace(b"mysqli_connect_error($con)", b"mysqli_connect_error()")
    texto = substituir(
        texto,
        b"$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 240);",
        b"$con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);",
        "falha rápida quando o banco não responde",
    )
    gravar(caminho, texto)


def ajustar_menu():
    caminho = os.path.join(COPIA, "bibliotecas", "menu.php")
    texto = ler(caminho)
    alvos = (
        b"etc008ContentBancoDadosClientes",
        b"etc009ContentContadoresClientes",
        b"etc010ContentUsuariosClientes",
        b"cad058ContentConfGlobal",
    )
    if not all(alvo in texto for alvo in alvos):
        sys.exit("não achei os itens fora do ciclo no menu")
    saida = []
    for linha in texto.splitlines(keepends=True):
        if any(alvo in linha for alvo in alvos) and not linha.lstrip().startswith(b"//"):
            quebra = b""
            if linha.endswith(b"\r\n"):
                quebra = b"\r\n"
            elif linha.endswith(b"\n"):
                quebra = b"\n"
            linha = b"// " + linha[: len(linha) - len(quebra)] + quebra
        saida.append(linha)
    gravar(caminho, b"".join(saida))


def ajustar_totais():
    caminho = os.path.join(COPIA, "sistema", "totais.php")
    texto = ler(caminho)
    antigo = b"'media' => $parcial['soma'] / $parcial['count']"
    novo = b"'media' => $parcial['count'] ? $parcial['soma'] / $parcial['count'] : 0"
    if novo in texto:
        return
    texto = substituir(texto, antigo, novo, "média de grade vazia no PHP 8.3")
    gravar(caminho, texto)


def ajustar_colunas():
    caminho = os.path.join(COPIA, "sistema", "interfaceUsuario.php")
    texto = ler(caminho)
    antigo = b"public $gridFiltro, $colunasOcultar;"
    novo = b"public $gridFiltro; public $colunasOcultar = array();"
    if novo not in texto:
        texto = substituir(texto, antigo, novo, "colunas ocultas no PHP 8.3")
    antigo_parametro = (
        b"$callbackParameterBase64 = isset($grid_valor['callbackParameter']) ? "
        b"base64_encode($grid_valor['callbackParameter']) : '';"
    )
    novo_parametro = (
        b"$callbackParameterBase64 = (isset($grid_valor['callbackParameter']) && "
        b"is_string($grid_valor['callbackParameter'])) ? "
        b"base64_encode($grid_valor['callbackParameter']) : '';"
    )
    if novo_parametro not in texto:
        texto = substituir(texto, antigo_parametro, novo_parametro, "parâmetro de coluna no PHP 8.3")
    texto = texto.replace(
        b"in_array($tabelaId, $tables)",
        b"in_array($tabelaId, is_array($tables) ? $tables : array())",
    )
    texto = texto.replace(
        b"in_array($grid_valor['colunaValor'], $this->colunasOcultar)",
        b"in_array($grid_valor['colunaValor'], is_array($this->colunasOcultar) ? $this->colunasOcultar : array())",
    )
    if b"if (is_array($value)) { $value[0] =" not in texto:
        texto = texto.replace(
            b"$value[0] = isset($value[0]) ? $value[0] : '';",
            b"if (is_array($value)) { $value[0] = isset($value[0]) ? $value[0] : ''; }",
        )
        texto = texto.replace(
            b"$value[1] = isset($value[1]) ? $value[1] : '';",
            b"if (is_array($value)) { $value[1] = isset($value[1]) ? $value[1] : ''; }",
        )
    gravar(caminho, texto)


def ajustar_grade():
    caminho = os.path.join(COPIA, "bibliotecas", "funcoesGerais.php")
    texto = ler(caminho)
    antigo = b"$display = array_shift(array_values($g['smarty']->template_objects))->resource_name;"
    novo = (
        b"$objetosTemplate = is_array($g['smarty']->template_objects) ? $g['smarty']->template_objects : array();"
        b" $ultimoTemplate = $objetosTemplate ? array_shift(array_values($objetosTemplate)) : null;"
        b" $display = is_object($ultimoTemplate) ? $ultimoTemplate->resource_name : '';"
    )
    if novo in texto:
        return
    texto = substituir(texto, antigo, novo, "grade no PHP 8.3")
    gravar(caminho, texto)


def ajustar_funcoes():
    caminho = os.path.join(COPIA, "bibliotecas", "funcoesGerais.php")
    texto = ler(caminho)
    if b"instanceof \\mysqli_result" not in texto:
        trocas = {
            br"\mysqli_fetch_assoc($result)": br"($result instanceof \mysqli_result ? \mysqli_fetch_assoc($result) : null)",
            br"\mysqli_fetch_array($result)": br"($result instanceof \mysqli_result ? \mysqli_fetch_array($result) : null)",
            br"\mysqli_fetch_row($result)": br"($result instanceof \mysqli_result ? \mysqli_fetch_row($result) : null)",
            br"\mysqli_num_rows($result)": br"($result instanceof \mysqli_result ? \mysqli_num_rows($result) : 0)",
        }
        for antigo, novo in trocas.items():
            if antigo not in texto:
                sys.exit(f"não achei {antigo!r} na cópia")
            texto = texto.replace(antigo, novo)
    gravar(caminho, texto)


def escrever_configuracao():
    conf = os.path.join(COPIA, "bibliotecas", "confUsuario.php")
    with open(conf, "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write(
            """<?php
namespace hardness;
$confUsuario = array(
    'dbDatabase' => 'fixar',
    'dbHost' => 'db',
    'dbUser' => 'fixar',
    'dbPass' => 'fixar',
    'pathRaiz' => '/var/www/html/hardness3/',
    'urlRaiz' => 'http://127.0.0.1:8080/hardness3/',
);
"""
        )
    ide = os.path.join(COPIA, "ide")
    os.makedirs(ide, exist_ok=True)
    with open(os.path.join(ide, "sysConfig.php"), "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write(
            """<?php
$sys = array(
    'nomeEmpresa' => 'Fixar',
    'dominio' => 'localhost',
    'dbDatabase' => 'fixar',
    'dbHost' => 'db',
    'dbUser' => 'fixar',
    'dbPass' => 'fixar',
);
"""
        )


def corrigir_php8(raiz):
    """Ajusta na cópia a sintaxe que o PHP 8 recusa carregar."""
    virgula = 0
    for pasta, _, nomes in os.walk(raiz):
        for nome in nomes:
            if not nome.endswith(".php"):
                continue
            caminho = os.path.join(pasta, nome)
            with open(caminho, "rb") as arquivo:
                texto = arquivo.read()
            novo = texto
            marca = novo.find(b"<?php")
            if 0 < marca <= 4 and b"namespace " in novo[marca : marca + 40]:
                novo = novo[marca:]
                virgula += 1
            novo = novo.replace(b"=& new ", b"= new ")
            if novo != texto:
                with open(caminho, "wb") as arquivo:
                    arquivo.write(novo)
    if virgula:
        print(f"Abertura de namespace ajustada em {virgula} arquivo(s).")

    trocas = {
        os.path.join(COPIA, "bibliotecas", "SPED", "EFD", "bloco_D.php"): (
            b"$COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : ($T013['T013_Tipo'] == '8') ? '22' : '';",
            b"$COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');",
        ),
        os.path.join(COPIA, "bibliotecas", "SPED", "Fiscal", "bloco_D.php"): (
            b"$COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : ($T013['T013_Tipo'] == '8') ? '22' : '';",
            b"$COD_MOD = ($T013['T013_Tipo'] == '7') ? '21' : (($T013['T013_Tipo'] == '8') ? '22' : '');",
        ),
        os.path.join(COPIA, "prog", "sistema", "funcoes", "gridFiltro.php"): (
            b"$count = ($count > 15) ? 15 : ($count < 0) ? 0 : $count;",
            b"$count = ($count > 15) ? 15 : (($count < 0) ? 0 : $count);",
        ),
        os.path.join(COPIA, "hardness", "class", "codebar", "html", "function.php"): (
            b"function __autoload($class_name) {\n\tglobal $class_dir;\n\trequire_once $class_dir.'/'.$class_name.'.php';\n}",
            b"spl_autoload_register(function ($class_name) {\n\tglobal $class_dir;\n\trequire_once $class_dir.'/'.$class_name.'.php';\n});",
        ),
        os.path.join(COPIA, "mobile", "tablet_nao_usar", "pedido", "tablet_func-ajax.php"): (
            b"break;",
            b"return;",
        ),
    }
    for caminho, (antigo, novo) in trocas.items():
        with open(caminho, "rb") as arquivo:
            texto = arquivo.read()
        if antigo not in texto:
            if novo not in texto:
                sys.exit(f"não achei o trecho de PHP 8 em {caminho}")
            continue
        with open(caminho, "wb") as arquivo:
            arquivo.write(texto.replace(antigo, novo))

    assinatura = b"function fetchAll(int $mode = \\PDO::FETCH_DEFAULT, mixed ...$args): array"
    for caminho in (
        os.path.join(COPIA, "mcp", "tests", "admin-foundation.php"),
        os.path.join(COPIA, "mcp", "tests", "DocumentSummary", "foundation.php"),
        os.path.join(COPIA, "mcp", "tests", "OAuth", "foundation.php"),
        os.path.join(COPIA, "mcp", "tests", "QuoteItem", "run.php"),
    ):
        with open(caminho, "rb") as arquivo:
            texto = arquivo.read()
        if assinatura in texto:
            continue
        texto, vezes = re.subn(
            br"function fetchAll\([^)]*\)",
            lambda _: assinatura,
            texto,
            count=1,
        )
        if vezes != 1:
            sys.exit(f"não achei fetchAll em {caminho}")
        with open(caminho, "wb") as arquivo:
            arquivo.write(texto)


def garantir_autoload():
    caminho = os.path.join(COPIA, "bibliotecas", "composer", "vendor", "autoload.php")
    if os.path.isfile(caminho):
        return
    os.makedirs(os.path.dirname(caminho), exist_ok=True)
    with open(caminho, "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write("<?php\n// Esta cópia não traz a pasta vendor. O framework exige o arquivo.\n")


def main():
    if os.path.isdir(os.path.join(COPIA, "sistema")) and os.environ.get("FIXAR_RECRIAR") != "1":
        print("A cópia local já existe. FIXAR_RECRIAR=1 refaz a partir do legado.")
    else:
        copiar()
        print("Corrigindo a sintaxe que o PHP 8.3 recusa carregar...")
        corrigir_chaves(COPIA)
    ajustar_menu()
    ajustar_entrada()
    ajustar_conexao()
    ajustar_funcoes()
    ajustar_grade()
    ajustar_colunas()
    ajustar_totais()
    corrigir_php8(COPIA)
    escrever_configuracao()
    garantir_autoload()
    for relativa in ("smarty/templates_c", "smarty/cache"):
        os.makedirs(os.path.join(COPIA, relativa), exist_ok=True)
    print("Cópia pronta em base/html.")


if __name__ == "__main__":
    main()
