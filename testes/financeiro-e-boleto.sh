#!/usr/bin/env bash
# Observa, pelo HTTP, contas a receber, contas a pagar, caixa e bancos e boletos.
# As quatro abrem sem movimento. Abrir o boleto não registra cobrança.
# A grade de boleto é a do Hardness: a coluna do produto aparece e a da Fixar não.
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
trap 'rm -f "$menu" "$corpo" "$biscuits"' EXIT

echo "Entrando com o usuário local"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "o envio do login não respondeu"
fi
if ! curl -sS --max-time 40 -o "$menu" -c "$biscuits" -b "$biscuits" "$base/hardness3/erp/"; then
  reclamar "o menu não abriu depois do login"
fi

python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
grupos = re.findall(r"class=\"ui-corner-top\">([^<]*)</a>", pagina)
itens = re.findall(r"class=\"ui-corner-tr\"[^>]*>([^<]*)</a>", pagina)
visiveis = grupos + itens
esperados = ["Contas a Receber", "Contas a Pagar", "Caixa e Bancos"]
faltando = [nome for nome in esperados if nome not in visiveis]
if faltando:
    print("FALTA " + ", ".join(faltando))
    raise SystemExit(1)
if "Boleto Bancário" in visiveis:
    print("INDEVIDO Boleto Bancário")
    raise SystemExit(1)
' "$menu" || reclamar "o menu não mostra o financeiro"

seguir() {
  local caminho="$1"
  local destino="$2"
  python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
rotulo, arquivo = sys.argv[2], sys.argv[3]
ancoras = re.findall(
    r"onclick=\"([^\"]*)\"[^>]*>" + re.escape(rotulo) + r"</a>",
    pagina,
)
for ancora in ancoras:
    urls = re.findall(r"(/[A-Za-z0-9_./-]+/)", ancora)
    if urls:
        pathlib.Path(arquivo).write_text(urls[-1], encoding="utf-8")
        raise SystemExit(0)
raise SystemExit(1)
' "$menu" "$caminho" "$destino"
}

url_final=""

grade() {
  local caminho="$1"
  url_final=""
  local url="${2:-}"
  local arquivo
  arquivo="$(mktemp)"
  if [ -z "$url" ]; then
    if ! seguir "$caminho" "$arquivo"; then
      reclamar "o menu não oferece ${caminho}"
      rm -f "$arquivo"
      return
    fi
    url="$(cat "$arquivo")"
  fi
  rm -f "$arquivo"
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
      url_final="$url"
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

vazia() {
  if grep -q 'linha color' "$corpo"; then
    reclamar "$1 não está vazio"
  fi
}

echo "Abrindo contas a receber"
grade "Contas a Receber"
vazia "Contas a receber"
if [ -n "$url_final" ] && [[ "$url_final" != *"/fin/fin001/"* ]]; then
  reclamar "contas a receber não abriu a grade de títulos"
fi

echo "Abrindo contas a pagar"
grade "Contas a Pagar"
vazia "Contas a pagar"
if [ -n "$url_final" ] && [[ "$url_final" != *"/fin/fin002/"* ]]; then
  reclamar "contas a pagar não abriu a grade de títulos"
fi

echo "Abrindo caixa e bancos"
grade "Caixa e Bancos"
vazia "Caixa e bancos"
if [ -n "$url_final" ] && [[ "$url_final" != *"/fin/fin003/"* ]]; then
  reclamar "caixa e bancos não abriu a tela de caixa"
fi

echo "Abrindo boletos"
grade "Boleto Bancário" "/fin/fin005/content/fin005ContentPrincipal/"
vazia "Boletos"
if [ -n "$url_final" ]; then
  if [[ "$url_final" != *"/fin/fin005/"* ]]; then
    reclamar "boletos não abriu a listagem"
  fi
  if python -c '
import sys
url = sys.argv[1].lower()
proibido = ("remessa", "registrar", "cnab", "retorno")
raise SystemExit(0 if any(trecho in url for trecho in proibido) else 1)
' "$url_final"; then
    reclamar "abrir boletos disparou o registro de cobrança"
  fi
fi
if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
registrada = ("Remessa gerada", "Boleto registrado", "Cobrança registrada")
raise SystemExit(0 if any(trecho in pagina for trecho in registrada) else 1)
' "$corpo"; then
  reclamar "a listagem de boletos registrou cobrança"
fi
if [ -n "$url_final" ] && [[ "$url_final" == *"/fin/fin005/"* ]]; then
  if ! grep -F -q 'Vencimento Boleto' "$corpo"; then
    reclamar "o boleto não é o do Hardness"
  fi
  if grep -F -q 'Atualiz.' "$corpo"; then
    reclamar "o boleto ficou o da Fixar"
  fi
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "Financeiro e boleto abrem vazios."
