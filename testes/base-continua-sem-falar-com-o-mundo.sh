#!/usr/bin/env bash
# Observa, pelo HTTP, o percurso de entrar, abrir cada item visível, abrir
# orçamento, pedido e boleto pelo endereço e sair. Nenhuma conexão sai para
# a receita, para um banco, para e-mail ou para o fornecedor. A subida não
# consulta boleto nem telemetria. O perfil local abre orçamento, pedido e
# nota no C.R.M.
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
saidas="$(mktemp)"
cabecalhos="$(mktemp)"
trap 'rm -f "$menu" "$corpo" "$biscuits" "$saidas" "$cabecalhos"' EXIT

if ! docker compose exec -T web sh -c ': > /tmp/fixar-saidas.log'; then
  reclamar "não deu para observar as conexões de saída"
fi

saida_limpa() {
  local quando="$1"
  if ! curl -sS --max-time 15 -o "$saidas" "$base/saidas.php"; then
    reclamar "não deu para observar as conexões de saída ${quando}"
    return
  fi
  if [ -s "$saidas" ]; then
    reclamar "houve conexão de saída ${quando}: $(tr '\n' ' ' <"$saidas")"
  fi
}

echo "Abrindo o login"
if ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/login"; then
  reclamar "não foi possível abrir $base/login"
fi

echo "Entrando com o usuário local"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "o envio do login não respondeu"
fi
if ! curl -sS --max-time 40 -o "$menu" -c "$biscuits" -b "$biscuits" "$base/hardness3/erp/"; then
  reclamar "o menu não abriu depois do login"
fi

echo "Conferindo a subida"
saida_limpa "na subida"
if grep -F -q "atualizarBoleto" "$menu" || grep -F -q "Erro select boletos" "$menu"; then
  reclamar "a subida consultou boleto do fornecedor"
fi
if grep -F -q "hardness.hardness.com.br" "$menu" || grep -F -q "myexternalip.com" "$menu"; then
  reclamar "a subida consultou telemetria do fornecedor"
fi

echo "Abrindo orçamento e pedido no C.R.M."
if ! curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  "$base/crm/crm001/tab/crm001TabGeral/?ajax=true"; then
  reclamar "o C.R.M. não respondeu"
fi
if ! grep -F -q ">Orçamentos</a>" "$corpo"; then
  reclamar "o perfil local não abre orçamento no C.R.M."
fi
if ! grep -F -q ">Pedidos</a>" "$corpo"; then
  reclamar "o perfil local não abre pedido no C.R.M."
fi
if ! grep -F -q ">Notas Fiscais</a>" "$corpo"; then
  reclamar "o perfil local não abre nota fiscal no C.R.M."
fi

echo "Abrindo cada item visível"
python - "$menu" "$corpo" "$biscuits" "$base" <<'PY' || falhou=1
import pathlib
import re
import subprocess
import sys
from urllib.parse import parse_qsl

menu, corpo, biscuits, base = sys.argv[1:5]
pagina = pathlib.Path(menu).read_text(encoding="utf-8", errors="replace")
ancoras = re.findall(
    r'class="ui-corner-tr"[^>]*onclick="([^"]*)"[^>]*>([^<]*)</a>',
    pagina,
)
ruim = (
    "Fatal error",
    "Parse error",
    "Uncaught",
    "Erro ao executar",
    "Division by zero",
    "DivisionByZeroError",
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
falhou = False
if not ancoras:
    print("FALHOU: o menu não tem item visível", file=sys.stderr)
    raise SystemExit(1)

def caminho_do_menu(onclick):
    urls = re.findall(r"(/[A-Za-z0-9_./-]+/)", onclick)
    return urls[-1] if urls else ""

def trecho(texto):
    for marca in ruim:
        posicao = texto.find(marca)
        if posicao >= 0:
            return texto[posicao : posicao + 180].replace("\n", " ").strip()
    return "resposta sem a tela"

def pedir(url, extras):
    comando = [
        "curl", "-sS", "--max-time", "25",
        "-o", corpo, "-c", biscuits, "-b", biscuits,
        "-w", "%{http_code}", "-X", "POST",
        "-d", "ajax=true",
        "-d", "divId=janela",
        "-d", "divIdChain=undefined",
        "-d", "divIdParentChain=undefined",
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
    texto = pathlib.Path(corpo).read_text(encoding="utf-8", errors="replace")
    try:
        return int(resultado.stdout.strip()), texto
    except ValueError:
        return 0, texto

def extras_de(texto):
    return [
        (chave, valor)
        for chave, valor in parse_qsl(texto, keep_blank_values=True)
        if chave not in campos_do_framework
    ]

def seguintes(texto):
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
        # Origem: sistema-legado/prog/etc/etc016/outros.php. O ajax do
        # código de barras concatena a leitura. Abrir a tela não dispara.
        if not dados or "+" in bloco[bloco.find("data:") : bloco.find("data:") + 240]:
            continue
        acrescentar(url.group(1), extras_de(dados.group(1)))
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
    if not url or url in vistos_menu:
        continue
    vistos_menu.add(url)
    print(f"Abrindo {rotulo}", flush=True)
    erro = percorrer(url, [], set(), 1)
    if erro != "":
        print(f"FALHOU: {rotulo} não abriu ({erro})", file=sys.stderr)
        falhou = True

raise SystemExit(1 if falhou else 0)
PY

grade() {
  local caminho="$1"
  local url="$2"
  local salto
  for salto in 1 2 3 4 5; do
    local codigo
    if ! codigo="$(curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" -w '%{http_code}' \
      "${base}${url}?ajax=true")"; then
      reclamar "${caminho} não respondeu"
      return
    fi
    if [ "$codigo" -ge 400 ]; then
      reclamar "${caminho} abriu com erro"
      return
    fi
    if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
ruim = ("Fatal error", "Parse error", "Uncaught", "Erro ao executar")
raise SystemExit(0 if any(trecho in pagina for trecho in ruim) else 1)
' "$corpo"; then
      reclamar "${caminho} abriu com erro"
      return
    fi
    if python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if re.search("class=.grid( |\042)", pagina) else 1)
' "$corpo"; then
      return
    fi
    url="$(python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
urls = re.findall(r"url:\s*'\''([^'\'']+)'\''", pagina)
grades = re.findall(r"(/[a-z]{3}/[a-z0-9]{6}/grid/[A-Za-z0-9_]+/)", pagina)
print(urls[0] if urls else (grades[0] if grades else ""))
' "$corpo")"
    if [ -z "$url" ]; then
      reclamar "${caminho} não chegou numa grade"
      return
    fi
  done
  reclamar "${caminho} não chegou numa grade"
}

echo "Abrindo orçamento pelo endereço do produto"
grade "Orçamento" "/ven/ven001/content/ven001ContentPrincipal/"
echo "Abrindo pedido pelo endereço do produto"
grade "Pedido" "/ven/ven002/content/ven002content01/"
echo "Abrindo boleto pelo endereço do produto"
grade "Boleto" "/fin/fin005/content/fin005ContentPrincipal/"

echo "Conferindo conexões depois do percurso"
saida_limpa "no percurso"

clique() {
  local frase="$1"
  local url="$2"
  if ! curl -sS --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" \
    "${base}${url}"; then
    reclamar "o clique não respondeu: ${frase}"
    return
  fi
  if ! python -c '
import json, pathlib, sys
bruto = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
frase = sys.argv[2]
inicio, fim = bruto.find("("), bruto.rfind(")")
miolo = bruto[inicio + 1:fim] if inicio >= 0 and fim > inicio else bruto
try:
    dado = json.loads(miolo)
except json.JSONDecodeError:
    dado = None
texto = dado.get("data", "") if isinstance(dado, dict) else bruto
raise SystemExit(0 if frase in texto or frase in bruto else 1)
' "$corpo" "$frase"; then
    reclamar "o clique não ficou na máquina: ${frase}"
  fi
}

echo "Clicando em emitir nota"
clique "A base local não emite nota." \
  "/ven/ven012/grid_func-ajax/txSefaz/?ajax=true&tx=true&T007_Id=1"
clique "A base local não emite nota." \
  "/ven/ven012/grid_func-ajax/ven012EnviarLote/?ajax=true"
if grep -q "Autorizado o uso\|protNFe\|Lote transmitido" "$corpo"; then
  reclamar "emitir nota chegou à receita"
fi

echo "Clicando em registrar boleto"
clique "A base local não registra boleto." \
  "/fin/fin001/grid_func-ajax/verificaRemessa/?ajax=true"
if python -c '
import json, pathlib, sys
bruto = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
inicio, fim = bruto.find("("), bruto.rfind(")")
dado = json.loads(bruto[inicio + 1:fim])
raise SystemExit(0 if dado.get("code") in (0, False, "0") else 1)
' "$corpo"; then
  reclamar "registrar boleto seguiu para o banco"
fi
if grep -q "Remessa gerada" "$corpo"; then
  reclamar "registrar boleto chegou ao banco"
fi

echo "Clicando em enviar e-mail"
clique "A base local não envia e-mail." \
  "/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true"
if grep -q "E-mails enviados com sucesso" "$corpo"; then
  reclamar "enviar e-mail saiu da máquina"
fi

echo "Conferindo conexões depois dos cliques"
saida_limpa "nos cliques"

echo "Saindo da sessão"
if ! curl -sS --max-time 20 -D "$cabecalhos" -o /dev/null -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "a saída da sessão não respondeu"
elif ! grep -qi '^Location: */login' "$cabecalhos"; then
  reclamar "a saída não voltou para o login"
fi

echo "Conferindo conexões depois de sair"
saida_limpa "ao sair"

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "A base continua sem falar com o mundo."
