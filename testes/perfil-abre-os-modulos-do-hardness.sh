#!/usr/bin/env bash
# Observa, pelo HTTP, cada item visível do menu que o ciclo ainda não abria.
# PDV, nota de loja, CRM, e-mail, PCP, SRM, comex, Serasa, SPED, módulo
# contábil e BI abrem quando o menu os mostra. Grade vazia passa: a página
# não cai numa conta nem num aviso. Sair da sessão volta ao login.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

case "$(uname -s)" in
  MINGW*|MSYS*|CYGWIN*)
    export DOCKER_HOST="${DOCKER_HOST:-npipe:////./pipe/kiri}"
    ;;
esac

base="${BASE_URL:-http://127.0.0.1:8080}"
usuario="${USUARIO_LOCAL:-fixar}"
senha="${SENHA_LOCAL:-fixar-local}"
falhou=0

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

menu="$(mktemp)"
corpo="$(mktemp)"
biscuits="$(mktemp)"
cabecalhos="$(mktemp)"
trap 'rm -f "$menu" "$corpo" "$biscuits" "$cabecalhos"' EXIT

echo "Entrando com o usuário local"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "o envio do login não respondeu"
fi
if ! curl -sS --max-time 40 -o "$menu" -c "$biscuits" -b "$biscuits" "$base/hardness3/erp/"; then
  reclamar "o menu não abriu depois do login"
fi

echo "Abrindo os módulos que o ciclo ainda não abria"
python - "$menu" "$corpo" "$biscuits" "$base" <<'PY' || falhou=1
import pathlib
import re
import subprocess
import sys
from urllib.parse import parse_qsl, urlencode

menu, corpo, biscuits, base = sys.argv[1:5]
pagina = pathlib.Path(menu).read_text(encoding="utf-8", errors="replace")

# O ciclo anterior já abre estes endereços. Este teste fica com o resto.
ja_abertos = {
    "/cad/cad001/content/cad001ContentPrincipal/",
    "/cad/cad002/content/grid1/",
    "/ven/ven003/content/ven003ContentPrincipal/",
    "/fin/fin001/content/fin001content01/",
    "/fin/fin002/content/fin002content01/",
    "/fin/fin003/content/fin003ContentGeral/",
    "/est/est003/content/est003Content/",
    "/est/est002/content/est002ContentPrincipal/",
    "/est/est004/content/est004ContentPrincipal/",
    "/fis/fis001/content/fis001content01/",
    "/fis/fis003/content/fis003ContentNcm/",
    "/fis/fis002/content/fis002ContentICMS/",
    "/cad/cad030/content/cad030ContentPrincipal/",
    "/cad/cad031/content/cad031ContentEmpresas/",
    "/etc/etc008/content/etc008ContentBancoDadosClientes/",
    "/etc/etc009/content/etc009ContentContadoresClientes/",
    "/etc/etc010/content/etc010ContentUsuariosClientes/",
    "/cad/cad058/content/cad058ContentConfGlobal/",
    "/etc/etc016/content/etc016VerificarPorCodbarra/",
    "/fin/fin001/content/fin001ContentCreditoTrocaCentral/",
}

obrigatorios = (
    "PDV",
    "Emitir Notas Fiscais (Loja)",
    "C.R.M.",
    "Emails",
    "P.C.P.",
    "S.R.M.",
    "Comex",
    "Serasa - Relato",
    "SPED Contribuições",
    "SPED Fiscal",
    "Contábil",
    "BI Dashboards",
    "BI",
)

grupos = [
    nome.strip()
    for nome in re.findall(r'class="ui-corner-top">([^<]*)</a>', pagina)
]
ancoras = re.findall(
    r'class="ui-corner-tr"[^>]*onclick="([^"]*)"[^>]*>([^<]*)</a>',
    pagina,
)
visiveis = grupos + [rotulo.strip() for _, rotulo in ancoras]
faltando = [nome for nome in obrigatorios if nome not in visiveis]
falhou = False
if faltando:
    print("FALHOU: o menu não mostra " + ", ".join(faltando), file=sys.stderr)
    falhou = True

ruim = (
    "Fatal error",
    "Parse error",
    "Uncaught",
    "Erro ao executar",
    "Division by zero",
    "DivisionByZeroError",
    "Warning:",
    "<b>Warning</b>",
    "<b>Notice</b>",
    "<b>Deprecated</b>",
    "sem acesso",
    "msgError",
)
campos_do_framework = {
    "ajax",
    "divIdRoot",
    "divIdParent",
    "divIdParentChain",
    "divId",
    "divIdChain",
    "titleTab",
    "PHPSESSID",
    "liberacaoCreditoFlag",
}
def caminho_do_menu(onclick):
    urls = re.findall(r"(/[A-Za-z0-9_./-]+/)", onclick)
    return urls[-1] if urls else ""


def trecho(texto):
    for marca in ruim:
        posicao = texto.find(marca)
        if posicao >= 0:
            linha = texto[posicao : posicao + 180].replace("\n", " ").strip()
            return linha
    return "resposta sem a tela"


def pedir(url, extras):
    comando = [
        "curl",
        "-sS",
        "--max-time",
        "25",
        "-o",
        corpo,
        "-c",
        biscuits,
        "-b",
        biscuits,
        "-w",
        "%{http_code}",
        "-X",
        "POST",
        "-d",
        "ajax=true",
        "-d",
        "divId=janela",
        "-d",
        "divIdChain=undefined",
        "-d",
        "divIdParentChain=undefined",
    ]
    for chave, valor in extras:
        comando.extend(["--data-urlencode", f"{chave}={valor}"])
    comando.append(base + url)
    try:
        resultado = subprocess.run(comando, check=False, capture_output=True, text=True)
    except OSError:
        return 0, ""
    if resultado.returncode != 0:
        return 0, ""
    codigo = resultado.stdout.strip()
    texto = pathlib.Path(corpo).read_text(encoding="utf-8", errors="replace")
    try:
        return int(codigo), texto
    except ValueError:
        return 0, texto


def extras_de(texto):
    return [
        (chave, valor)
        for chave, valor in parse_qsl(texto, keep_blank_values=True)
        if chave not in campos_do_framework
    ]


def seguintes(texto):
    # O navegador carrega o que o ajax pede e a grade da primeira aba.
    achados = []
    vistos = set()

    def acrescentar(url, extras):
        caminho = url.split("?", 1)[0]
        if not caminho.startswith("/") or "func-ajax" in caminho or caminho in vistos:
            return
        vistos.add(caminho)
        achados.append((caminho, extras))

    for bloco in re.findall(r"\$\.ajax\(\{(.*?)\}\);", texto, re.S):
        url = re.search(r"url:\s*'([^']+)'", bloco)
        if not url:
            continue
        dados = re.search(r"data:\s*'([^']*)'", bloco)
        acrescentar(url.group(1), extras_de(dados.group(1)) if dados else [])
    for bloco in re.findall(r"\.data\(\{(.*?)\}\);", texto, re.S):
        href = re.search(r"'href':\s*'([^']+)'", bloco)
        if not href or "/grid/" not in href.group(1):
            continue
        variaveis = re.search(r"'vars':\s*'([^']*)'", bloco)
        acrescentar(href.group(1), extras_de(variaveis.group(1).lstrip("&")) if variaveis else [])
        break
    return achados


def abriu(texto):
    if re.search(r'class=.grid( |")', texto):
        return True
    return any(
        marca in texto
        for marca in ("<form", "<table", "<button", "<input", "<section", "<iframe")
    )


def percorrer(url, extras, vistos, profundidade):
    # "" abre. None é um salto que não prova abertura. "resposta sem a tela"
    # é um miolo sem grade e não reprova quando outra resposta da mesma tela abriu.
    if url in vistos or profundidade > 10:
        return None
    vistos.add(url)
    codigo, texto = pedir(url, extras)
    if codigo != 200:
        return f"{url}: " + (trecho(texto) if texto else f"HTTP {codigo}")
    if any(marca in texto for marca in ruim):
        return f"{url}: {trecho(texto)}"
    # Origem: sistema-legado/prog/outros/integracaoPDV/integracaoSG.php.
    # O menu aponta para contentExportar e o produto deixa o case comentado.
    if not texto.strip() and url.rstrip("/").endswith("contentExportar"):
        return ""
    viu = abriu(texto)
    for seguinte, dados in seguintes(texto):
        erro = percorrer(seguinte, dados, vistos, profundidade + 1)
        if erro is None or erro == "resposta sem a tela":
            continue
        if erro == "":
            viu = True
            continue
        return erro
    if viu:
        return ""
    return "resposta sem a tela"


vistos_menu = set()
for onclick, rotulo in ancoras:
    rotulo = rotulo.strip()
    url = caminho_do_menu(onclick)
    if not url or url in ja_abertos or url in vistos_menu:
        continue
    vistos_menu.add(url)
    print(f"Abrindo {rotulo}", flush=True)
    erro = percorrer(url, [], set(), 1)
    if erro != "":
        print(f"FALHOU: {rotulo} não abriu ({erro})", file=sys.stderr)
        falhou = True

if not vistos_menu and not faltando:
    print("FALHOU: o menu não tem item novo para abrir", file=sys.stderr)
    falhou = True

raise SystemExit(1 if falhou else 0)
PY

echo "Saindo da sessão"
if ! curl -sS --max-time 20 -D "$cabecalhos" -o /dev/null -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "a saída da sessão não respondeu"
elif ! grep -qi '^Location: */login' "$cabecalhos"; then
  reclamar "a saída não voltou para o login"
elif ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "não deu para abrir o login depois de sair"
elif ! grep -q 'name="senha"' "$corpo"; then
  reclamar "depois de sair a tela não é o login"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O perfil abre os módulos que o Hardness mostra."
