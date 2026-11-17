#!/usr/bin/env bash
# Sobe a base local numa máquina nova: confere o que falta, instala as bibliotecas
# do Composer e chama o ./subir. Pode rodar de novo a qualquer hora.
set -euo pipefail

cd "$(dirname "$0")"

falta() {
  echo "Falta: $1" >&2
  exit 1
}

case "$(uname -s)" in
  MINGW*|MSYS*|CYGWIN*)
    export DOCKER_HOST="${DOCKER_HOST:-npipe:////./pipe/kiri}"
    export MSYS_NO_PATHCONV=1
    raiz="$(pwd -W)"
    ;;
  *)
    raiz="$(pwd)"
    ;;
esac

command -v docker >/dev/null 2>&1 || falta "o CLI do Docker. Instale o Kiri: https://docs.runkiri.com"
docker version >/dev/null 2>&1 || falta "o engine do Kiri respondendo. Abra o Kiri e rode de novo."
docker compose version >/dev/null 2>&1 || falta "o docker compose."
python -c "import sys" >/dev/null 2>&1 || python3 -c "import sys" >/dev/null 2>&1 || falta "o Python 3."

# O vendor/ fica fora do git; sem ele a tela interna responde 500.
composer_dir="src/hardness3/bibliotecas/composer"
if [ ! -f "$composer_dir/vendor/autoload.php" ]; then
  echo "Instalando as bibliotecas do Composer (só na primeira vez)"
  usuario=()
  if [ "$(uname -s)" = "Linux" ]; then
    usuario=(--user "$(id -u):$(id -g)")
  fi
  # O composer.lock fixa as versões. As extensões são as do contêiner web, não as desta imagem.
  docker run --rm "${usuario[@]}" -v "$raiz/$composer_dir:/app" -w /app composer:2.8 \
    install --no-interaction --no-progress --ignore-platform-reqs
fi

echo "Subindo a base. Na primeira vez o banco aplica as rotinas e leva alguns minutos."
./subir

cat <<'EOF'

Entrar em http://127.0.0.1:8080/login
  usuário: fixar
  senha:   fixar-local
EOF
