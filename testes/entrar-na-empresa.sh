#!/usr/bin/env bash
# Observa, pelo HTTP, a entrada na empresa de validação, a senha errada,
# a saída da sessão, a base que sobrevive a um reinício e o banco ausente.
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
banco_parado=0

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

restaurar_banco() {
  if [ "$banco_parado" -eq 1 ]; then
    docker compose start db >/dev/null
    docker compose up -d --wait --no-build db >/dev/null
    banco_parado=0
  fi
}

trap 'restaurar_banco' EXIT

corpo="$(mktemp)"
cabecalhos="$(mktemp)"
biscuits="$(mktemp)"
trap 'restaurar_banco; rm -f "$corpo" "$cabecalhos" "$biscuits"' EXIT

nome_da_empresa() {
  python -c '
import html as html_lib
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
achado = re.search(r"id=\"login-company\">([^<]*)", pagina)
print(html_lib.unescape(achado.group(1)).strip() if achado else "")
' "$1"
}

pagina_tem_empresa() {
  python -c '
import pathlib, sys
nome, pagina = sys.argv[1], pathlib.Path(sys.argv[2]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if nome and nome in pagina else 1)
' "$1" "$2"
}

echo "Abrindo o login para ler a empresa"
if ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/login"; then
  reclamar "não foi possível abrir $base/login"
  nome=""
else
  nome="$(nome_da_empresa "$corpo")"
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
elif ! grep -q "location.replace" "$corpo" || ! grep -q "/erp/" "$corpo"; then
  reclamar "a senha definida aqui não entrou no sistema"
elif ! curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  "$base/hardness3/erp/"; then
  reclamar "a tela interna não abriu depois do login"
elif ! pagina_tem_empresa "$nome" "$corpo"; then
  reclamar "a tela interna não é a empresa ${nome}"
elif ! grep -q "FIXAR" "$corpo"; then
  reclamar "a tela interna não mostra o usuário local"
fi

echo "Tentando a senha errada"
: >"$biscuits"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=senha-que-nao-e" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "a senha errada não teve resposta"
elif ! grep -q "Login ou Senha inválida" "$corpo"; then
  reclamar "a senha errada não mostrou a mensagem de recusa"
elif ! grep -q 'name="senha"' "$corpo"; then
  reclamar "a senha errada saiu da tela de login"
elif grep -q "location.replace" "$corpo"; then
  reclamar "a senha errada entrou no sistema"
elif ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/hardness3/erp/"; then
  reclamar "não deu para conferir a sessão depois da senha errada"
elif grep -q "FIXAR" "$corpo"; then
  reclamar "a senha errada deixou o usuário logado"
fi

echo "Saindo da sessão"
: >"$biscuits"
if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
  -d "login=${usuario}" -d "senha=${senha}" \
  "$base/hardness3/outros/login/index.php"; then
  reclamar "não deu para entrar de novo antes de sair"
fi
: >"$cabecalhos"
if ! curl -sS --max-time 20 -D "$cabecalhos" -o /dev/null -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "a saída da sessão não respondeu"
elif ! grep -qi '^Location: */login' "$cabecalhos"; then
  reclamar "a saída não voltou para o login"
elif ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "não deu para abrir o login depois de sair"
elif ! grep -q 'name="senha"' "$corpo"; then
  reclamar "depois de sair a tela não é o login"
fi

echo "Reiniciando o banco"
if ! docker compose restart db >/dev/null; then
  reclamar "não foi possível reiniciar o banco"
elif ! docker compose up -d --wait --no-build db >/dev/null; then
  reclamar "o banco não ficou pronto depois do reinício"
else
  : >"$biscuits"
  if ! curl -sS --max-time 30 -o "$corpo" -c "$biscuits" -b "$biscuits" \
    -d "login=${usuario}" -d "senha=${senha}" \
    "$base/hardness3/outros/login/index.php"; then
    reclamar "o login não respondeu depois do reinício"
  elif ! grep -q "/erp/" "$corpo"; then
    reclamar "o usuário local sumiu depois de reiniciar o banco"
  fi
fi

echo "Parando o banco"
if docker compose stop db >/dev/null; then
  banco_parado=1
  if ! curl -sS -L --max-time 20 -o "$corpo" "$base/login"; then
    reclamar "com o banco parado a página não respondeu"
  elif ! grep -q "Ocorreu um erro ao conectar com banco de dados" "$corpo"; then
    reclamar "com o banco parado a falha não foi explícita"
  elif ! grep -q '[^[:space:]]' "$corpo"; then
    reclamar "com o banco parado a tela veio em branco"
  fi
  restaurar_banco
else
  reclamar "não foi possível parar o banco para o teste"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O desenvolvedor entra na empresa de validação."
