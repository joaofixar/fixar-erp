#!/usr/bin/env bash
# Observa, pelo HTTP, que a base sobe servindo o Hardness versionado.
# O login abre em /login. Um ajuste na grade aparece ao atualizar.
# Subir de novo não troca o Hardness que respondeu. Outro cliente não aparece.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

case "$(uname -s)" in
  MINGW*|MSYS*|CYGWIN*)
    export DOCKER_HOST="${DOCKER_HOST:-npipe:////./pipe/kiri}"
    ;;
esac

unset COMPOSE_PROJECT_NAME COMPOSE_FILE
base="${BASE_URL:-http://127.0.0.1:8080}"
falhou=0
grade="src/hardness3/prog/cad/cad002/grid_func-js.php"
backup=""

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

restaurar_grade() {
  if [ -n "$backup" ] && [ -f "$backup" ]; then
    cp "$backup" "$grade"
    rm -f "$backup"
    backup=""
  fi
}

corpo="$(mktemp)"
cabecalhos="$(mktemp)"
efetivo="$(mktemp)"
saida="$(mktemp)"
trap 'restaurar_grade; rm -f "$corpo" "$cabecalhos" "$efetivo" "$saida"' EXIT

echo "Abrindo a tela de login em $base"
if ! curl -sS -L --max-time 15 -o "$corpo" -D "$cabecalhos" -w '%{url_effective}' "$base/login" >"$efetivo"; then
  reclamar "não foi possível abrir $base/login"
else
  case "$(cat "$efetivo")" in
    */hardness3/*) ;;
    *) reclamar "o login não abriu em /hardness3/: $(cat "$efetivo")" ;;
  esac
  grep -q "Seja bem-vindo" "$corpo" || reclamar "a resposta não é a tela de login"
  grep -q 'name="login"' "$corpo" || reclamar "falta o campo de usuário"
  grep -q 'name="senha"' "$corpo" || reclamar "falta o campo de senha"
  grep -q "PHP/8.3" "$cabecalhos" || reclamar "o servidor não é PHP 8.3"
fi

echo "Conferindo personalização de outro cliente"
for nome in ceigon guimepa Derosso emprefix eurosul nacomaq Panceri vedapar; do
  if ! codigo="$(curl -sS --max-time 20 -o "$corpo" -w '%{http_code}' \
    "$base/hardness3/outros/personalizacoes/${nome}/")"; then
    reclamar "o endereço de ${nome} não respondeu"
    continue
  fi
  if grep -F -q "$nome" "$corpo"; then
    reclamar "personalização de ${nome} apareceu"
  fi
  if ! grep -q "Página não encontrada" "$corpo"; then
    reclamar "o endereço de ${nome} não recusou a pasta (HTTP ${codigo})"
  fi
done

echo "Abrindo a grade de produto"
url_grade="$base/hardness3/prog/cad/cad002/grid.php"
if ! curl -sS --max-time 20 -o "$corpo" "$url_grade"; then
  reclamar "a grade não respondeu"
elif ! grep -F -q "Certificado Excluído" "$corpo"; then
  reclamar "a grade não trouxe o texto do Hardness"
fi

if [ "$falhou" -eq 0 ]; then
  echo "Ajustando a grade versionada"
  backup="$(mktemp)"
  cp "$grade" "$backup"
  python - "$grade" <<'PY'
import pathlib, sys
caminho = pathlib.Path(sys.argv[1])
texto = caminho.read_bytes()
antigo = "Certificado Excluído".encode()
novo = "Certificado Excluído na base versionada".encode()
if antigo not in texto:
    raise SystemExit("não achei o texto da grade")
caminho.write_bytes(texto.replace(antigo, novo, 1))
PY
  if ! curl -sS --max-time 20 -o "$corpo" "$url_grade"; then
    reclamar "a grade não respondeu depois do ajuste"
  elif ! grep -F -q "Certificado Excluído na base versionada" "$corpo"; then
    reclamar "o ajuste da grade não apareceu ao atualizar"
  fi
fi

if [ "$falhou" -eq 0 ]; then
  echo "Subindo a base de novo"
  if ! ./subir >"$saida" 2>&1; then
    reclamar "subir de novo falhou"
    tail -n 20 "$saida" >&2
  elif grep -q "Falta o .env" "$saida"; then
    reclamar "subir de novo tratou a falta de credencial como sucesso"
  fi
  if ! curl -sS --max-time 20 -o "$corpo" "$url_grade"; then
    reclamar "a grade não respondeu depois de subir de novo"
  elif ! grep -F -q "Certificado Excluído na base versionada" "$corpo"; then
    reclamar "subir de novo trocou a grade que respondeu"
  fi
  if ! curl -sS -L --max-time 15 -o "$corpo" "$base/login"; then
    reclamar "o login não abriu depois de subir de novo"
  elif ! grep -q "Seja bem-vindo" "$corpo"; then
    reclamar "o login mudou depois de subir de novo"
  fi
fi

restaurar_grade

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "O Hardness versionado sobe e o ajuste permanece."
