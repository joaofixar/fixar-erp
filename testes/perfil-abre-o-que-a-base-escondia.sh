#!/usr/bin/env bash
# Observa, pelo HTTP, o que a base escondia e o cadastro de quem entrou.
# Banco Dados Clientes, Contadores Clientes, Usuarios Clientes, Opções,
# Conferência Cega e Créditos Troca abrem depois do login. Grade vazia passa.
# A página não cai numa conta nem num aviso do PHP. Usuários mostra o usuário
# local e empresa mostra a empresa de validação.
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

tem_grade=0

pagina_ruim() {
  python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
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
raise SystemExit(0 if any(trecho in pagina for trecho in ruim) else 1)
' "$corpo"
}

proxima() {
  # O navegador manda, junto da url, o tab e o acaoId que o framework colocou
  # no data do ajax. Sem o tab a tela de opções abre o formulário vazio.
  python - "$corpo" <<'PY'
import pathlib, re, sys
from urllib.parse import parse_qsl, urlencode

pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
urls = re.findall(r"url:\s*'([^']+)'", pagina)
dados = re.findall(r"data:\s*'([^']*)'", pagina)
grades = re.findall(
    r"(/[a-z]{3}/[a-z0-9]{6}/(?:grid|form|content|tab|outros)/[A-Za-z0-9_]+/)",
    pagina,
)
if not urls and not grades:
    print("")
    raise SystemExit
url = urls[0] if urls else grades[0]
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
if urls and dados:
    extras = [
        (chave, valor)
        for chave, valor in parse_qsl(dados[0], keep_blank_values=True)
        if chave not in campos_do_framework
    ]
    if extras:
        url += ("&" if "?" in url else "?") + urlencode(extras)
print(url)
PY
}

tela() {
  python - "$corpo" <<'PY'
import pathlib, re, sys

pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
if re.search(r'class=.grid( |")', pagina):
    print("grade")
# Origem: sistema-legado/prog/cad/cad058/tab.php. Com a permissão 185
# o primeiro conteúdo é o geral.
elif (
    "cad058FormConfInterface" in pagina and "Tamanho da fonte" in pagina
) or (
    "cad058FormConfGeral" in pagina and "Nome Empresa Exibido" in pagina
):
    print("opcoes")
elif "Leia o ID para iniciar" in pagina or "Leia um código de barras" in pagina:
    print("conferencia")
else:
    print("seguir")
PY
}

percorrer() {
  local url="$1"
  local esperada="$2"
  tem_grade=0
  local salto
  for salto in 1 2 3 4 5 6; do
    # O menu abre com POST. Sem cadeia gravada, o JavaScript manda o texto undefined.
    local codigo
    if ! codigo="$(curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" -w '%{http_code}' \
      -X POST \
      -d "ajax=true" \
      -d "divId=janela" \
      -d "divIdChain=undefined" \
      -d "divIdParentChain=undefined" \
      "${base}${url}")"; then
      return 1
    fi
    if [ "$codigo" -ge 400 ]; then
      return 1
    fi
    if pagina_ruim; then
      return 1
    fi
    local achada
    achada="$(tela)"
    if [ "$achada" = "$esperada" ]; then
      if [ "$achada" = "grade" ]; then
        tem_grade=1
      fi
      return 0
    fi
    # Ainda não é a tela pedida. A url seguinte pode ser o envio do formulário.
    if [ "$achada" != "seguir" ]; then
      return 1
    fi
    local seguinte
    seguinte="$(proxima)"
    if [ -z "$seguinte" ]; then
      return 1
    fi
    url="$seguinte"
  done
  return 1
}

abrir() {
  local caminho="$1"
  local esperada="$2"
  local exigir_vazio="${3:-1}"
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
  if ! percorrer "$url" "$esperada"; then
    reclamar "${caminho} não abriu"
    return
  fi
  if [ "$exigir_vazio" -eq 1 ] && [ "$tem_grade" -eq 1 ] && grep -q 'linha color' "$corpo"; then
    reclamar "${caminho} não está vazio"
  fi
}

echo "Abrindo Banco Dados Clientes"
abrir "Banco Dados Clientes" grade

echo "Abrindo Contadores Clientes"
abrir "Contadores Clientes" grade

echo "Abrindo Usuarios Clientes"
abrir "Usuarios Clientes" grade

echo "Abrindo Opções"
abrir "Opções" opcoes 0

echo "Abrindo Conferência Cega"
abrir "Conferência Cega" conferencia 0

echo "Abrindo Créditos Troca"
abrir "Créditos Troca" grade

echo "Abrindo usuários"
abrir "Usuários/Perfis" grade 0
if ! grep -qi "$usuario" "$corpo"; then
  reclamar "a tela de usuários não mostra o usuário local"
fi

echo "Abrindo a empresa"
abrir "Empresas" grade 0
if [ -n "$nome" ] && ! grep -q "$nome" "$corpo"; then
  reclamar "a tela de empresa não mostra ${nome}"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O perfil abre o que a base escondia."
