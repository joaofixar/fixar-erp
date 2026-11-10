#!/usr/bin/env bash
# Observa, pelo HTTP, o menu depois do login e a grade de cada cadastro do ciclo.
# Cliente, produto e os fiscais abrem sem linha. Usuários e empresa mostram o que foi semeado.
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

echo "Abrindo o login para ler a empresa"
if ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/login"; then
  reclamar "não foi possível abrir $base/login"
  nome=""
else
  nome="$(python -c '
import html as html_lib
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
achado = re.search(r"id=\"login-company\">([^<]*)", pagina)
print(html_lib.unescape(achado.group(1)).strip() if achado else "")
' "$corpo")"
  if [ -z "$nome" ]; then
    reclamar "a tela de login não mostrou a empresa"
  fi
fi

echo "Entrando com o usuário local"
: >"$biscuits"
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
esperados = [
    "Cliente/Fornecedor",
    "Produtos",
    "CFOP",
    "NCM",
    "ICMS",
    "Usuários/Perfis",
    "Empresas",
]
proibidos = (
    "PDV",
    "Loja",
    "C.R.M.",
    "Emails",
    "P.C.P.",
    "S.R.M.",
    "Comex",
    "Serasa",
    "SPED",
    "Contábil",
    "B.I.",
    "Coletor",
    "B2B",
    "BI",
    "Opções",
    "Banco Dados Clientes",
    "Contadores Clientes",
    "Usuarios Clientes",
)
faltando = [nome for nome in esperados if nome not in visiveis]
if faltando:
    print("FALTA " + ", ".join(faltando))
    raise SystemExit(1)
indevidos = [nome for nome in visiveis if any(trecho in nome for trecho in proibidos)]
if indevidos:
    print("INDEVIDO " + ", ".join(indevidos))
    raise SystemExit(1)
' "$menu" || reclamar "o menu não é o do ciclo"

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

grade() {
  local caminho="$1"
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
ruim = ("Fatal error", "Parse error", "Uncaught")
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

vazia() {
  if grep -q 'linha color' "$corpo"; then
    reclamar "$1 não está vazio"
  fi
}

echo "Abrindo cliente e fornecedor"
grade "Cliente/Fornecedor"
vazia "Cliente/Fornecedor"

echo "Abrindo produtos"
grade "Produtos"
vazia "Produtos"

echo "Abrindo CFOP"
grade "CFOP"
vazia "CFOP"

echo "Abrindo NCM"
grade "NCM"
vazia "NCM"

echo "Abrindo ICMS"
grade "ICMS"
vazia "ICMS"

echo "Abrindo usuários"
grade "Usuários/Perfis"
if ! grep -qi "$usuario" "$corpo"; then
  reclamar "a tela de usuários não mostra o usuário local"
fi

echo "Abrindo a empresa"
grade "Empresas"
if [ -n "$nome" ] && ! grep -q "$nome" "$corpo"; then
  reclamar "a tela de empresa não mostra ${nome}"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O menu do ciclo e os cadastros abrem vazios."
