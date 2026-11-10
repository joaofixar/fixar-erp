#!/usr/bin/env bash
# Observa, pelo HTTP, estoque, a listagem da nota de entrada e a expedição.
# As três abrem sem movimento. Abrir a entrada não importa XML.
# O histórico de produto é o da Fixar: o filtro próprio aparece e o genérico não.
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
esperados = ["Estoque", "Notas Fiscais Recebidas", "Expedição Mercadorias"]
faltando = [nome for nome in esperados if nome not in visiveis]
if faltando:
    print("FALTA " + ", ".join(faltando))
    raise SystemExit(1)
if "Conferência Cega" in visiveis:
    print("INDEVIDO Conferência Cega")
    raise SystemExit(1)
' "$menu" || reclamar "o menu não mostra estoque, entrada e expedição"

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

url_menu=""
url_final=""

grade() {
  local caminho="$1"
  url_menu=""
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
  url_menu="$url"
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

echo "Abrindo estoque"
grade "Estoque"
vazia "Estoque"
if [ -n "$url_menu" ] && [[ "$url_menu" != *"/est/est003/"* ]]; then
  reclamar "estoque não abriu a posição de estoque"
fi
if [ -n "$url_final" ] && [[ "$url_final" != *"/cad/cad020/grid/cad020lista/"* ]]; then
  reclamar "estoque não abriu a posição de estoque"
fi

echo "Abrindo a nota fiscal de entrada"
grade "Notas Fiscais Recebidas"
vazia "Nota fiscal de entrada"
if [ -n "$url_final" ]; then
  if [[ "$url_final" != *"/est/est004/"* ]]; then
    reclamar "a nota de entrada não abriu a listagem"
  fi
  if python -c '
import sys
url = sys.argv[1].lower()
proibido = ("importarxml", "importar_xml", "sefaz")
raise SystemExit(0 if any(trecho in url for trecho in proibido) else 1)
' "$url_final"; then
    reclamar "abrir a nota de entrada disparou a importação de XML"
  fi
fi
if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
importada = ("Importação realizada", "XML importado", "nfeProc")
raise SystemExit(0 if any(trecho in pagina for trecho in importada) else 1)
' "$corpo"; then
  reclamar "a listagem da nota de entrada importou XML"
fi

echo "Abrindo expedição"
grade "Expedição Mercadorias"
vazia "Expedição"
if [ -n "$url_final" ] && [[ "$url_final" != *"/est/est002/"* ]]; then
  reclamar "expedição não abriu a tela de expedição"
fi

echo "Abrindo o histórico de produto"
if ! codigo="$(curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" -w '%{http_code}' \
  "${base}/cad/cad002/grid/historico/?ajax=true")"; then
  reclamar "o histórico de produto não respondeu"
  codigo=0
fi
if [ "$codigo" -ge 400 ]; then
  reclamar "o histórico de produto abriu com erro"
fi
if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
ruim = ("Fatal error", "Parse error", "Uncaught", "Erro ao executar")
raise SystemExit(0 if any(trecho in pagina for trecho in ruim) else 1)
' "$corpo"; then
  reclamar "o histórico de produto abriu com erro"
fi
if ! grep -q 'Data Lançamento' "$corpo"; then
  reclamar "o histórico de produto não é o da Fixar"
fi
if grep -q 'NFe Entrada' "$corpo"; then
  reclamar "o histórico de produto ficou o genérico"
fi
if grep -q 'linha color' "$corpo"; then
  reclamar "o histórico de produto não está vazio"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "Estoque, entrada e expedição abrem vazios."
