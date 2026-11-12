#!/usr/bin/env bash
# Observa, pelo HTTP, que orçamento, pedido e boleto abrem pelo endereço do
# produto e a tela é a do Hardness. O histórico de produto é o do Hardness.
# A totalização e o reprocesso da Fixar não substituem essas telas.
# Personalização de outro cliente não aparece. O usuário local é de suporte,
# então o atalho de personalização responde.
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

url_final=""

grade() {
  local caminho="$1"
  url_final=""
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

echo "Abrindo orçamento pelo endereço do produto"
grade "Orçamento" "/ven/ven001/content/ven001ContentPrincipal/"
vazia "Orçamento"
if [ -n "$url_final" ] && [[ "$url_final" != *"/ven/ven001/"* ]]; then
  reclamar "orçamento não abriu a tela do Hardness"
fi

echo "Abrindo pedido pelo endereço do produto"
grade "Pedido" "/ven/ven002/content/ven002content01/"
vazia "Pedido"
if [ -n "$url_final" ] && [[ "$url_final" != *"/ven/ven002/"* ]]; then
  reclamar "pedido não abriu a tela do Hardness"
fi

echo "Abrindo boleto pelo endereço do produto"
grade "Boleto" "/fin/fin005/content/fin005ContentPrincipal/"
vazia "Boleto"
if [ -n "$url_final" ] && [[ "$url_final" != *"/fin/fin005/"* ]]; then
  reclamar "boleto não abriu a grade do Hardness"
fi
if [ -n "$url_final" ] && [[ "$url_final" == *"/fin/fin005/"* ]]; then
  if ! grep -F -q 'Vencimento Boleto' "$corpo"; then
    reclamar "a grade de boleto não é a do Hardness"
  fi
  if grep -F -q 'Atualiz.' "$corpo"; then
    reclamar "a grade de boleto é a da Fixar"
  fi
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
if ! grep -F -q 'NFe Entrada' "$corpo"; then
  reclamar "o histórico de produto não é o do Hardness"
fi
if grep -F -q 'Data Lançamento' "$corpo"; then
  reclamar "o histórico de produto é o da Fixar"
fi
if grep -q 'linha color' "$corpo"; then
  reclamar "o histórico de produto não está vazio"
fi

echo "Conferindo personalização de outro cliente"
for nome in ceigon guimepa Derosso emprefix eurosul nacomaq Panceri vedapar; do
  if grep -F -q "$nome" "$menu" || grep -F -q "$nome" "$corpo"; then
    reclamar "personalização de ${nome} apareceu na tela"
  fi
  if ! codigo="$(curl -sS --max-time 20 -o "$corpo" -w '%{http_code}' \
    "$base/hardness3/outros/personalizacoes/${nome}/")"; then
    reclamar "o endereço de ${nome} não respondeu"
    continue
  fi
  if [ "$codigo" -lt 400 ] && grep -F -q "$nome" "$corpo"; then
    reclamar "personalização de ${nome} apareceu"
  fi
done

echo "Pedindo o atalho de personalização"
if ! curl -sS --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  "$base/sistema/ajax/verificaRoot/?ajax=true&callback=retorno"; then
  reclamar "o atalho de personalização não respondeu"
elif ! grep -F -q '"code":true' "$corpo"; then
  reclamar "o atalho de personalização recusou o usuário local"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "A personalização não executa e o usuário local é de suporte."
