#!/usr/bin/env bash
# Sobe com o volume vazio e sem credencial de produção, entra com o usuário
# local na empresa de validação, vê esse usuário, essa empresa e, no C.R.M.,
# orçamento, pedido e nota, e sai. Qualquer conexão com a produção, a receita,
# um banco, o e-mail ou o fornecedor reprova. Subir de novo, com o volume já
# preenchido, continua na mesma empresa.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

case "$(uname -s)" in
  MINGW*|MSYS*|CYGWIN*)
    export DOCKER_HOST="${DOCKER_HOST:-npipe:////./pipe/kiri}"
    ;;
esac

projeto=fixar-t02
porta=8082
unset COMPOSE_FILE
export COMPOSE_PROJECT_NAME="$projeto"
unset PRODUCAO_HOST PRODUCAO_PORT PRODUCAO_BANCO PRODUCAO_USUARIO PRODUCAO_SENHA

base="http://127.0.0.1:${porta}"
usuario="${USUARIO_LOCAL:-fixar}"
senha="${SENHA_LOCAL:-fixar-local}"
falhou=0
override="${raiz}/docker-compose.nascimento.yml"

# O compose commitado publica 8080. O percurso descartável usa outra porta
# num arquivo que não entra no git, para não esvaziar fixar-erp_db-dados.
cat > "$override" <<EOF
services:
  web:
    ports: !override
      - "${porta}:80"
EOF

compose() {
  docker compose -p "$projeto" -f docker-compose.yml -f "$override" "$@"
}

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

if [ "$COMPOSE_PROJECT_NAME" != "$projeto" ]; then
  echo "FALHOU: o percurso só pode usar o projeto ${projeto}" >&2
  exit 1
fi

if [ -n "${PRODUCAO_HOST:-}${PRODUCAO_BANCO:-}${PRODUCAO_USUARIO:-}${PRODUCAO_SENHA:-}" ]; then
  echo "FALHOU: a subida ainda tem credencial de produção" >&2
  exit 1
fi

config="$(compose config)"
if printf '%s\n' "$config" | grep -q 'fixar-erp_db-dados'; then
  echo "FALHOU: a configuração usa o volume da base que já está nesta máquina" >&2
  exit 1
fi
if ! printf '%s\n' "$config" | grep -q "${projeto}_db-dados"; then
  echo "FALHOU: o volume descartável não está isolado" >&2
  exit 1
fi
if printf '%s\n' "$config" | grep -Eq 'published: "?8080"?|"8080:80"|- "?8080:80"?'; then
  echo "FALHOU: o percurso descartável publicaria a porta do stack commitado" >&2
  exit 1
fi
if ! printf '%s\n' "$config" | grep -Eq "published: \"?${porta}\"?|\"${porta}:80\"|- \"?${porta}:80\"?"; then
  echo "FALHOU: a porta publicada não é ${porta}" >&2
  exit 1
fi

volume_local=0
if docker volume inspect fixar-erp_db-dados >/dev/null 2>&1; then
  volume_local=1
fi

echo "Esvaziando o volume descartável"
compose down -v --remove-orphans >/dev/null

if [ "$volume_local" -eq 1 ] && ! docker volume inspect fixar-erp_db-dados >/dev/null 2>&1; then
  echo "FALHOU: o volume local foi removido" >&2
  exit 1
fi
if docker volume inspect "${projeto}_db-dados" >/dev/null 2>&1; then
  echo "FALHOU: o volume descartável continuou depois de esvaziar" >&2
  exit 1
fi

echo "Subindo sem credencial de produção"
if python -c "import sys" >/dev/null 2>&1; then
  py=python
else
  py=python3
fi
"$py" bin/preparar-base.py
if ! docker image inspect fixar-erp-web >/dev/null 2>&1; then
  docker build -t fixar-erp-web ./docker/php
fi
compose up -d --no-build --wait

if [ "$volume_local" -eq 1 ] && ! docker volume inspect fixar-erp_db-dados >/dev/null 2>&1; then
  echo "FALHOU: o volume local sumiu durante a subida" >&2
  exit 1
fi

menu="$(mktemp)"
corpo="$(mktemp)"
biscuits="$(mktemp)"
saidas="$(mktemp)"
cabecalhos="$(mktemp)"
trap 'rm -f "$menu" "$corpo" "$biscuits" "$saidas" "$cabecalhos" "$override"' EXIT

esperar_login() {
  local tentativa
  for tentativa in 1 2 3 4 5 6 7 8 9 10 11 12; do
    if curl -sS -L --max-time 10 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/login"; then
      return 0
    fi
    sleep 2
  done
  return 1
}

limpar_saidas() {
  compose exec -T web sh -c ': > /tmp/fixar-saidas.log'
}

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

nome_da_empresa() {
  python -c '
import html as html_lib
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
achado = re.search(r"id=\"login-company\">([^<]*)", pagina)
print(html_lib.unescape(achado.group(1)).strip() if achado else "")
' "$1"
}

echo "Abrindo o login"
if ! limpar_saidas; then
  reclamar "não deu para observar as conexões de saída"
fi
: >"$biscuits"
nome=""
if ! esperar_login; then
  reclamar "não foi possível abrir $base/login"
else
  nome="$(nome_da_empresa "$corpo")"
  if [ -z "$nome" ]; then
    reclamar "a tela de login não mostrou a empresa de validação"
  fi
fi

echo "Entrando com o usuário local"
: >"$biscuits"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "o envio do login não respondeu"
elif ! grep -q "location.replace" "$corpo" || ! grep -q "/erp/" "$corpo"; then
  reclamar "o usuário local não entrou na empresa de validação"
elif ! curl -sS --max-time 40 -o "$menu" -c "$biscuits" -b "$biscuits" "$base/hardness3/erp/"; then
  reclamar "a tela interna não abriu depois do login"
elif ! python -c '
import pathlib, sys
nome, pagina = sys.argv[1], pathlib.Path(sys.argv[2]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if nome and nome in pagina else 1)
' "$nome" "$menu"; then
  reclamar "a tela interna não é a empresa de validação"
elif ! grep -q "FIXAR" "$menu"; then
  reclamar "a tela interna não mostra o usuário local"
fi

echo "Abrindo orçamento, pedido e nota no C.R.M."
if ! curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  "$base/crm/crm001/tab/crm001TabGeral/?ajax=true"; then
  reclamar "o C.R.M. não respondeu"
else
  if ! grep -F -q ">Orçamentos</a>" "$corpo"; then
    reclamar "o C.R.M. não mostra orçamento"
  fi
  if ! grep -F -q ">Pedidos</a>" "$corpo"; then
    reclamar "o C.R.M. não mostra pedido"
  fi
  if ! grep -F -q ">Notas Fiscais</a>" "$corpo"; then
    reclamar "o C.R.M. não mostra nota"
  fi
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

percorrer() {
  local url="$1"
  local salto
  for salto in 1 2 3 4 5 6; do
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
    if python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if re.search("class=.grid( |\042)", pagina) else 1)
' "$corpo"; then
      return 0
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
    reclamar "${caminho} não abriu"
  fi
}

echo "Abrindo usuários"
abrir "Usuários/Perfis"
if ! grep -qi "$usuario" "$corpo"; then
  reclamar "a tela de usuários não mostra o usuário local"
fi

echo "Abrindo a empresa"
abrir "Empresas"
if [ -n "$nome" ] && ! grep -F -q "$nome" "$corpo"; then
  reclamar "a tela de empresa não mostra a empresa de validação"
fi

paginas="${menu}${corpo}"
if grep -F -q "atualizarBoleto" "$menu" "$corpo" || grep -F -q "Erro select boletos" "$menu" "$corpo"; then
  reclamar "a subida consultou boleto do fornecedor"
fi
if grep -F -q "hardness.hardness.com.br" "$menu" "$corpo" || grep -F -q "myexternalip.com" "$menu" "$corpo"; then
  reclamar "a subida consultou telemetria do fornecedor"
fi
unset paginas

echo "Conferindo conexões do percurso"
saida_limpa "no percurso"

echo "Saindo da sessão"
if ! curl -sS --max-time 20 -D "$cabecalhos" -o /dev/null -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "a saída da sessão não respondeu"
elif ! grep -qi '^Location: */login' "$cabecalhos"; then
  reclamar "a saída não voltou para o login"
fi

echo "Conferindo conexões ao sair"
saida_limpa "ao sair"

echo "Subindo de novo com o volume já preenchido"
if ! compose up -d --no-build --wait >/dev/null; then
  reclamar "a segunda subida não ficou pronta"
fi
if ! docker volume inspect "${projeto}_db-dados" >/dev/null 2>&1; then
  reclamar "a segunda subida não manteve o volume"
fi
if [ "$volume_local" -eq 1 ] && ! docker volume inspect fixar-erp_db-dados >/dev/null 2>&1; then
  echo "FALHOU: o volume local sumiu na segunda subida" >&2
  exit 1
fi

echo "Entrando de novo na mesma empresa"
: >"$biscuits"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "o login não respondeu depois de subir de novo"
elif ! grep -q "/erp/" "$corpo"; then
  reclamar "subir de novo com o volume preenchido não manteve o usuário local"
elif ! curl -sS -L --max-time 20 -o "$corpo" "$base/login"; then
  reclamar "o login não abriu depois de subir de novo"
else
  nome_de_novo="$(nome_da_empresa "$corpo")"
  if [ -z "$nome" ] || [ "$nome_de_novo" != "$nome" ]; then
    reclamar "subir de novo não manteve a empresa de validação"
  fi
fi

echo "Conferindo conexões depois de subir de novo"
saida_limpa "depois de subir de novo"

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O banco nasce sem ler a produção."
