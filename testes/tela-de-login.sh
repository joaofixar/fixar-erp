#!/usr/bin/env bash
# Observa a base no ar pelo HTTP: a tela de login, o PHP 8.3 e a porta
# de administrador sem senha. Também confere o que o git não guarda.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

base="${BASE_URL:-http://127.0.0.1:8080}"
falhou=0

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

echo "Conferindo o que o git guarda"
while IFS= read -r arquivo; do
  case "$arquivo" in
    scripts/backup/*|*/scripts/backup/*)
      reclamar "script de backup no git: $arquivo" ;;
    */confUsuario.php|confUsuario.php)
      reclamar "credencial no git: $arquivo" ;;
    .env|.env.*)
      case "$arquivo" in
        .env.example) ;;
        *) reclamar "credencial no git: $arquivo" ;;
      esac ;;
    */templates_c/*|*/dados_usuarios/*|*/tema_*_cache.css)
      reclamar "cache ou arquivo de uso no git: $arquivo" ;;
    */inecel/*|*/loginBoticario/*|*/personalizacoes/Aneis*|*/personalizacoes/ceigon/*|*/personalizacoes/guimepa/*)
      reclamar "pasta de outro cliente no git: $arquivo" ;;
  esac
done < <(git ls-files)

git check-ignore -q base && echo "A cópia local fica fora do git" || reclamar "base/ não está no gitignore"

pesada="$(python -c '
import os, subprocess, sys
arquivos = subprocess.check_output(["git", "ls-files"], text=True).splitlines()
for arquivo in arquivos:
    try:
        tamanho = os.path.getsize(arquivo)
    except OSError:
        continue
    if tamanho > 5000000:
        print(f"{tamanho} {arquivo}")
')"
if [ -n "$pesada" ]; then
  reclamar "mídia pesada no git: $pesada"
fi

echo "Abrindo a tela de login em $base"
corpo="$(mktemp)"
cabecalhos="$(mktemp)"
trap 'rm -f "$corpo" "$cabecalhos"' EXIT

if ! tempo="$(curl -sS -L --max-time 15 -o "$corpo" -D "$cabecalhos" -w '%{time_total}' "$base/login")"; then
  reclamar "não foi possível abrir $base/login"
else
  grep -q "Seja bem-vindo" "$corpo" || reclamar "a resposta não é a tela de login"
  grep -q 'name="login"' "$corpo" || reclamar "falta o campo de usuário"
  grep -q 'name="senha"' "$corpo" || reclamar "falta o campo de senha"
  grep -q "PHP/8.3" "$cabecalhos" || reclamar "o servidor não é PHP 8.3"
  python -c 'import sys; raise SystemExit(0 if float(sys.argv[1]) < 8 else 1)' "$tempo" \
    || reclamar "a tela demorou ${tempo}s; a subida não pode esperar a rede"
fi

echo "Pedindo a URL que entrava como administrador"
: >"$cabecalhos"
if ! curl -sS --max-time 15 -D "$cabecalhos" -o /dev/null "$base/dev_sem_senha"; then
  reclamar "a URL de administrador não respondeu"
elif grep -qi '^Location:.*\/erp' "$cabecalhos"; then
  reclamar "a URL entrou como administrador sem senha"
fi

if ! curl -sS -L --max-time 15 -o "$corpo" "$base/dev_sem_senha"; then
  reclamar "não deu para seguir a URL de administrador"
elif ! grep -q "Seja bem-vindo" "$corpo"; then
  reclamar "a URL de administrador não voltou para a tela de login"
fi

if ! curl -sS -L --max-time 15 -o "$corpo" "$base/"; then
  reclamar "a raiz não abriu"
elif ! grep -q 'name="senha"' "$corpo"; then
  reclamar "a raiz não mostra a tela de login"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "A tela de login abre."
