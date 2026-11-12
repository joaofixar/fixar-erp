#!/usr/bin/env bash
# Sobe uma base descartável e deixa o passo das rotinas rodar depois da
# fotografia. O status pendente volta como pendente, as rotinas da
# fotografia continuam com a mesma definição, um arquivo que falha fica
# nomeado e o resto da lista controlada ainda é criado. Rodar o passo de
# novo não recria rotina que já existe.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

case "$(uname -s)" in
  MINGW*|MSYS*|CYGWIN*)
    export DOCKER_HOST="${DOCKER_HOST:-npipe:////./pipe/kiri}"
    ;;
esac

projeto=fixar-t01
porta=8081
porta_banco=33061
unset COMPOSE_FILE
export COMPOSE_PROJECT_NAME="$projeto"
unset PRODUCAO_HOST PRODUCAO_PORT PRODUCAO_BANCO PRODUCAO_USUARIO PRODUCAO_SENHA

falhou=0
# Caminho relativo: o absoluto do Git Bash vira C:\c\... para o docker.exe.
override="docker-compose.rotinas-ausentes.yml"
antes=".rotinas-antes.sh"
rotinas_da_fotografia=(
  D024_Email_Cliente
  D032_Tipo
  D137_Quantidade_Regras
  T005_Prazos
  T005_Status_Pedido
)

# O compose commitado publica 8080 e 3306. O percurso descartável troca as
# duas num arquivo que não entra no git, e grava a definição da fotografia
# antes do passo, no nascimento do volume.
{
  printf '%s\n' '# Grava a definição das rotinas da fotografia antes do passo.'
  printf '%s\n' 'saida=/tmp/definicoes-da-fotografia.txt'
  printf '%s\n' ': > "$saida"'
  printf '%s\n' 'banco="${MARIADB_DATABASE:-fixar}"'
  printf '%s\n' 'senha="${MARIADB_ROOT_PASSWORD:-}"'
  printf '%s\n' "for nome in ${rotinas_da_fotografia[*]}; do"
  printf '%s\n' '  printf "%s\n" "-- ${nome}" >> "$saida"'
  printf '%s\n' '  mariadb --protocol=socket -uroot -hlocalhost -p"$senha" "$banco" -e "SHOW CREATE FUNCTION \`$nome\`" >> "$saida" || printf "%s\n" "falhou ${nome}" >> "$saida"'
  printf '%s\n' '  printf "\n" >> "$saida"'
  printf '%s\n' 'done'
  printf '%s\n' 'mariadb --protocol=socket -uroot -hlocalhost -p"$senha" "$banco" -N -B -e "SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA='"'"'${banco}'"'"' AND ROUTINE_NAME='"'"'T003_Status_Orcamento'"'"'" > /tmp/status-orcamento-antes.txt || printf "%s\n" "falhou" > /tmp/status-orcamento-antes.txt'
  printf '%s\n' 'true'
} > "$antes"

cat > "$override" <<EOF
services:
  web:
    ports: !override
      - "${porta}:80"
  db:
    ports: !override
      - "127.0.0.1:${porta_banco}:3306"
    volumes:
      - ./.rotinas-antes.sh:/docker-entrypoint-initdb.d/yy-antes.sh:ro
EOF

definir_host=""
definir_depois=""
definir_de_novo=""
diferenca=""
limpar() {
  rm -f "$override" "$antes" "$definir_host" "$definir_depois" "$definir_de_novo" "$diferenca"
}
trap limpar EXIT

compose() {
  docker compose -p "$projeto" -f docker-compose.yml -f "$override" "$@"
}

# O Git Bash reescreve /tmp e /docker-entrypoint-initdb.d antes de chamar o
# docker. Só o exec precisa do caminho de dentro do contêiner.
exec_db() {
  MSYS_NO_PATHCONV=1 compose exec -T "$@"
}

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

consultar() {
  exec_db db mariadb --protocol=socket -uroot -hlocalhost -pfixar fixar -N -B -e "$1" 2>/dev/null | tr -d '\r[:space:]' || true
}

descrever_rotinas() {
  local destino="$1"
  local nome
  : > "$destino"
  for nome in "${rotinas_da_fotografia[@]}"; do
    printf '%s\n' "-- ${nome}" >> "$destino"
    exec_db db mariadb --protocol=socket -uroot -hlocalhost -pfixar fixar \
      -e "SHOW CREATE FUNCTION \`$nome\`" >> "$destino" 2>/dev/null \
      || printf '%s\n' "falhou ${nome}" >> "$destino"
    printf '\n' >> "$destino"
  done
}

if ! docker info >/dev/null 2>&1; then
  echo "FALHOU: o motor de contêiner não respondeu" >&2
  exit 1
fi

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
if printf '%s\n' "$config" | grep -Eq 'published: "?8080"?([^0-9]|$)|"8080:80"|- "?8080:80"?'; then
  echo "FALHOU: o percurso descartável publicaria a porta do stack commitado" >&2
  exit 1
fi
if ! printf '%s\n' "$config" | grep -Eq "published: \"?${porta}\"?([^0-9]|\$)|\"${porta}:80\""; then
  echo "FALHOU: a porta publicada não é ${porta}" >&2
  exit 1
fi
if printf '%s\n' "$config" | grep -Eq 'published: "?3306"?([^0-9]|$)|"127\.0\.0\.1:3306:3306"|"3306:3306"'; then
  echo "FALHOU: o percurso descartável publicaria a porta 3306" >&2
  exit 1
fi
if ! printf '%s\n' "$config" | grep -Eq "published: \"?${porta_banco}\"?|\"${porta_banco}:3306\""; then
  echo "FALHOU: a porta do banco publicada não é ${porta_banco}" >&2
  exit 1
fi
for trecho in zz-rotinas.sh fotografia.sql yy-antes.sh /rotinas/lista.php /rotinas/sql; do
  if ! printf '%s\n' "$config" | grep -q "$trecho"; then
    echo "FALHOU: a configuração não monta ${trecho}" >&2
    exit 1
  fi
done

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

echo "Subindo a base descartável"
if python -c "import sys" >/dev/null 2>&1; then
  py=python
else
  py=python3
fi
"$py" bin/preparar-base.py
if ! docker image inspect fixar-erp-web >/dev/null 2>&1; then
  docker build -t fixar-erp-web ./docker/php
fi
if ! compose up -d --no-build; then
  echo "FALHOU: a base descartável não subiu" >&2
  exit 1
fi
if ! compose up -d --no-build --wait; then
  echo "A espera do healthcheck terminou antes da base responder; seguindo"
fi

if [ "$volume_local" -eq 1 ] && ! docker volume inspect fixar-erp_db-dados >/dev/null 2>&1; then
  echo "FALHOU: o volume local sumiu durante a subida" >&2
  exit 1
fi

definir_host="$(mktemp)"
definir_depois="$(mktemp)"
definir_de_novo="$(mktemp)"
diferenca="$(mktemp)"

echo "Esperando o status pendente do orçamento"
status=""
tentativa=0
while [ "$tentativa" -lt 120 ]; do
  tentativa=$((tentativa + 1))
  status="$(consultar "SELECT T003_Status_Orcamento('P')")"
  if [ "$status" = "Pendente" ]; then
    break
  fi
  sleep 5
done
if [ "$status" != "Pendente" ]; then
  echo "FALHOU: o status pendente do orçamento não voltou como pendente (veio: ${status:-vazio})" >&2
  compose logs db 2>&1 | tail -n 80 >&2 || true
  exit 1
fi

antes_do_passo="$(exec_db db cat /tmp/status-orcamento-antes.txt 2>/dev/null | tr -d '\r[:space:]' || true)"
if [ "$antes_do_passo" != "0" ]; then
  reclamar "o status do orçamento já existia antes do passo (contagem: ${antes_do_passo:-vazia})"
fi

if ! exec_db db cat /tmp/definicoes-da-fotografia.txt > "$definir_host"; then
  reclamar "a definição da fotografia não foi gravada antes do passo"
fi
descrever_rotinas "$definir_depois"
if ! diff -u <(tr -d '\r' < "$definir_host") <(tr -d '\r' < "$definir_depois") > "$diferenca"; then
  reclamar "a definição das rotinas da fotografia mudou depois do passo"
  head -n 40 "$diferenca" >&2 || true
fi

echo "Rodando o passo de novo"
segunda="$(exec_db db bash /docker-entrypoint-initdb.d/zz-rotinas.sh 2>&1 | tr -d '\r' || true)"
linha="$(printf '%s\n' "$segunda" | grep -E '^criadas=' | tail -1 || true)"
echo "Segunda passada: ${linha:-sem linha}"
if [ -z "$linha" ] || ! printf '%s\n' "$linha" | grep -q '^criadas=0 '; then
  reclamar "rodar o passo de novo recriou rotina: ${linha:-sem linha}"
  printf '%s\n' "$segunda" >&2
fi
descrever_rotinas "$definir_de_novo"
if ! diff -u <(tr -d '\r' < "$definir_host") <(tr -d '\r' < "$definir_de_novo") > "$diferenca"; then
  reclamar "a definição das rotinas da fotografia mudou ao rodar o passo de novo"
  head -n 40 "$diferenca" >&2 || true
fi

echo "Incluindo um arquivo de rotina que falha"
exec_db db bash -s << 'JORNADA_FIM'
mkdir -p /tmp/jornada-rotinas/sql
cat > /tmp/jornada-rotinas/lista.php << 'LISTA'
Atualizar_Rotina("FUNCTION", "Jornada_Arquivo_Ausente");
Atualizar_Rotina("FUNCTION", "Jornada_Rotina_Criada");
LISTA
cat > /tmp/jornada-rotinas/sql/Jornada_Rotina_Criada.sql << 'SQL'
CREATE FUNCTION `Jornada_Rotina_Criada`() RETURNS varchar(20)
BEGIN
    RETURN 'criada';
END
SQL
JORNADA_FIM

controlada="$(exec_db \
  -e ROTINA_LISTA=/tmp/jornada-rotinas/lista.php \
  -e ROTINA_SQL=/tmp/jornada-rotinas/sql \
  db bash /docker-entrypoint-initdb.d/zz-rotinas.sh 2>&1 | tr -d '\r' || true)"
printf '%s\n' "$controlada"
if ! printf '%s\n' "$controlada" | grep -q 'Jornada_Arquivo_Ausente (arquivo ausente)'; then
  reclamar "o arquivo que falha não foi nomeado"
fi
criada="$(consultar "SELECT Jornada_Rotina_Criada()")"
if [ "$criada" != "criada" ]; then
  reclamar "a outra rotina da lista controlada não foi criada (veio: ${criada:-vazio})"
fi

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "As rotinas ausentes entram no percurso da base."
