#!/usr/bin/env bash
# Observa, pelo HTTP, orçamento, pedido e a listagem da nota de saída.
# As três abrem sem linha. Abrir a nota não emite documento.
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
esperados = ["Orçamentos", "Pedidos", "Notas Fiscais"]
faltando = [nome for nome in esperados if nome not in visiveis]
if faltando:
    print("FALTA " + ", ".join(faltando))
    raise SystemExit(1)
' "$menu" || reclamar "o menu não mostra orçamento, pedido e nota de saída"

seguir() {
  local caminho="$1"
  local destino="$2"
  python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
rotulo, arquivo = sys.argv[2], sys.argv[3]
ancora = re.search(
    r"onclick=\"([^\"]*)\"[^>]*>" + re.escape(rotulo) + r"</a>",
    pagina,
)
if not ancora:
    raise SystemExit(1)
urls = re.findall(r"(/[A-Za-z0-9_./-]+/)", ancora.group(1))
if not urls:
    raise SystemExit(1)
pathlib.Path(arquivo).write_text(urls[-1], encoding="utf-8")
' "$menu" "$caminho" "$destino"
}

url_final=""

grade() {
  local caminho="$1"
  url_final=""
  local arquivo
  arquivo="$(mktemp)"
  if ! seguir "$caminho" "$arquivo"; then
    reclamar "o menu não oferece ${caminho}"
    rm -f "$arquivo"
    return
  fi
  local url
  url="$(cat "$arquivo")"
  rm -f "$arquivo"
  local salto
  for salto in 1 2 3 4; do
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

echo "Abrindo orçamento"
grade "Orçamentos"
vazia "Orçamento"
if [ -n "$url_final" ] && [[ "$url_final" != *"/ven/ven001/"* ]]; then
  reclamar "orçamento não abriu a tela de orçamento"
fi

echo "Abrindo pedido"
grade "Pedidos"
vazia "Pedido"
if [ -n "$url_final" ] && [[ "$url_final" != *"/ven/ven002/"* ]]; then
  reclamar "pedido não abriu a tela de pedido"
fi

echo "Abrindo a nota fiscal de saída"
grade "Notas Fiscais"
vazia "Nota fiscal de saída"
if [ -n "$url_final" ]; then
  if [[ "$url_final" != *"/ven/ven003/"* ]]; then
    reclamar "a nota de saída não abriu a listagem"
  fi
  if python -c '
import sys
url = sys.argv[1].lower()
proibido = ("transmit", "sefaz", "autoriz", "emitir", "enviar")
raise SystemExit(0 if any(trecho in url for trecho in proibido) else 1)
' "$url_final"; then
    reclamar "abrir a nota de saída disparou emissão"
  fi
fi
if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
emitida = ("Autorizado o uso", "protNFe", "nfeProc", "Nota autorizada")
raise SystemExit(0 if any(trecho in pagina for trecho in emitida) else 1)
' "$corpo"; then
  reclamar "a listagem da nota trouxe um documento emitido"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "Orçamento, pedido e nota de saída abrem vazios."
