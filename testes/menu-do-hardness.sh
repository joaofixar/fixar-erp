#!/usr/bin/env bash
# Observa, pelo HTTP, o menu depois do login. O menu é o que o Hardness entrega:
# orçamento, pedido e boleto ficam de fora; o que a base escondia volta;
# o que o produto mostra aparece; o que o produto comenta continua de fora.
# Cliente, produto, orçamento, pedido, nota e título continuam sem linha.
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

python - "$menu" "$raiz/sistema-legado/bibliotecas/menu.php" <<'PY' || reclamar "o menu não é o do Hardness"
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
produto = pathlib.Path(sys.argv[2]).read_text(encoding="utf-8", errors="replace")
grupos = [
    re.sub(r"<[^>]+>", "", miolo)
    for miolo in re.findall(r"class=\"ui-corner-top\">(.*?)</a>", pagina, re.S)
]
itens = re.findall(r"class=\"ui-corner-tr\"[^>]*>([^<]*)</a>", pagina)
visiveis = [nome.strip() for nome in grupos + itens]
esperados = [
    "Banco Dados Clientes",
    "Contadores Clientes",
    "Usuarios Clientes",
    "Opções",
    "Conferência Cega",
    "Créditos Troca",
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
]
faltando = [nome for nome in esperados if nome not in visiveis]
if faltando:
    print("FALTA " + ", ".join(faltando))
    raise SystemExit(1)

def limpar(nome):
    nome = nome.replace("&nbsp;", " ").replace("\xa0", " ")
    nome = re.sub(r"\s+", " ", nome).strip()
    return re.sub(r"^[>\s]+", "", nome)

def rotulo(linha):
    achado = re.search(r"\$menu\['[^']+'\]\['([^']+)'\]", linha)
    return achado.group(1) if achado else ""

comentados = set()
ativos = set()
for linha in produto.splitlines():
    nome = rotulo(linha)
    if not nome:
        continue
    if linha.lstrip().startswith("//"):
        comentados.add(limpar(nome))
    else:
        ativos.add(limpar(nome))
proibidos = comentados - ativos
indevidos = [nome for nome in visiveis if limpar(nome) in proibidos]
if indevidos:
    print("INDEVIDO " + ", ".join(indevidos))
    raise SystemExit(1)
PY

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

percorrer() {
  local url="$1"
  local salto
  url_final=""
  for salto in 1 2 3 4 5; do
    local codigo
    if ! codigo="$(curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" -w '%{http_code}' \
      "${base}${url}?ajax=true")"; then
      return 1
    fi
    if [ "$codigo" -ge 400 ]; then
      return 1
    fi
    if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
ruim = ("Fatal error", "Parse error", "Uncaught")
raise SystemExit(0 if any(trecho in pagina for trecho in ruim) else 1)
' "$corpo"; then
      return 1
    fi
    if python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if re.search("class=.grid( |\042)", pagina) else 1)
' "$corpo"; then
      url_final="$url"
      return 0
    fi
    url="$(python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
urls = re.findall(r"url:\s*'\''([^'\'']+)'\''", pagina)
grades = re.findall(r"(/[a-z]{3}/[a-z0-9]{6}/grid/[A-Za-z0-9_]+/)", pagina)
print(urls[0] if urls else (grades[0] if grades else ""))
' "$corpo")"
    if [ -z "$url" ]; then
      return 1
    fi
  done
  return 1
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
  if ! percorrer "$url"; then
    reclamar "${caminho} não chegou numa grade"
  fi
}

vazia() {
  if grep -q 'linha color' "$corpo"; then
    reclamar "$1 não está vazio"
  fi
}

echo "Abrindo cliente e fornecedor"
grade "Cliente/Fornecedor"
vazia "Cliente"

echo "Abrindo produtos"
grade "Produtos"
vazia "Produto"

echo "Abrindo a nota fiscal"
grade "Notas Fiscais"
vazia "Nota"

echo "Abrindo títulos"
grade "Contas a Receber"
vazia "Título"

echo "Abrindo orçamento pelo endereço do produto"
if ! percorrer "/ven/ven001/content/ven001ContentPrincipal/"; then
  reclamar "orçamento não chegou numa grade"
fi
vazia "Orçamento"

echo "Abrindo pedido pelo endereço do produto"
if ! percorrer "/ven/ven002/content/ven002content01/"; then
  reclamar "pedido não chegou numa grade"
fi
vazia "Pedido"

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O menu é o do Hardness e a subida não trouxe movimento."
