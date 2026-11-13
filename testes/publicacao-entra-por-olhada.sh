#!/usr/bin/env bash
# Prova o comando da publicação num repositório descartável.
# A base não sobe e o banco não é consultado.
set -euo pipefail

raiz_repo="$(cd "$(dirname "$0")/.." && pwd)"
comando="$raiz_repo/bin/publicacao"
falhou=0

reclamar() {
  echo "FALHOU: $1" >&2
  falhou=1
}

trabalho="$(mktemp -d)"
trap 'rm -rf "$trabalho"' EXIT

identificar() {
  git -C "$1" config user.email teste@fixar.local
  git -C "$1" config user.name Teste
}

commitar() {
  local repo="$1"
  local assunto="$2"
  git -C "$repo" add -A
  git -C "$repo" commit -q -m "$assunto"
  git -C "$repo" rev-parse HEAD
}

preparar_erp() {
  local erp="$1"
  mkdir -p "$erp/src/hardness3"
  printf 'fixar\n' >"$erp/src/hardness3/leia.txt"
  git init -q -b master "$erp"
  identificar "$erp"
  commitar "$erp" "hardness inicial" >/dev/null
}

# --- gravar o ponto não copia o Hardness ---

erp="$trabalho/erp-gravar"
pub="$trabalho/pub-gravar"
clone="$trabalho/clone-gravar"
preparar_erp "$erp"
mkdir -p "$pub"
printf 'base\n' >"$pub/leia.txt"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"

antes_head="$(git -C "$erp" rev-parse HEAD)"
antes_ramo="$(git -C "$erp" branch --show-current)"
antes_leia="$(cat "$erp/src/hardness3/leia.txt")"

saida="$(mktemp)"
erro="$(mktemp)"
if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" --ponto "$sha_base" >"$saida" 2>"$erro"; then
  reclamar "gravar o ponto terminou em erro: $(cat "$erro")"
fi
if [ "$(tr -d '[:space:]' <"$erp/publicacao/ponto")" != "$sha_base" ]; then
  reclamar "o ponto gravado não é o SHA informado"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "$antes_leia" ]; then
  reclamar "gravar o ponto alterou o Hardness"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "gravar o ponto trocou o checkout"
fi
if [ "$(git -C "$erp" branch --show-current)" != "$antes_ramo" ]; then
  reclamar "gravar o ponto trocou o ramo"
fi
if [ "$(git -C "$clone" branch --show-current)" != "master" ]; then
  reclamar "gravar o ponto tirou o clone de master"
fi

# --- a olhada lista os commits depois do ponto, do mais antigo ao mais novo ---

erp="$trabalho/erp-lista"
pub="$trabalho/pub-lista"
clone="$trabalho/clone-lista"
preparar_erp "$erp"
mkdir -p "$pub"
printf 'base\n' >"$pub/leia.txt"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'um\n' >"$pub/leia.txt"
sha_um="$(commitar "$pub" "um")"
printf 'dois\n' >"$pub/leia.txt"
sha_dois="$(commitar "$pub" "dois")"
mkdir -p "$erp/publicacao"
printf '%s\n' "$sha_base" >"$erp/publicacao/ponto"

antes_head="$(git -C "$erp" rev-parse HEAD)"
antes_master="$(git -C "$clone" rev-parse master)"
printf 'rascunho\n' >"$clone/nota.txt"
git -C "$clone" add nota.txt
git -C "$clone" commit -q -m "rascunho local"
sha_rascunho="$(git -C "$clone" rev-parse HEAD)"

if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a olhada com commits novos terminou em erro: $(cat "$erro")"
fi
esperado="$(printf '%s um\n%s dois\n' "$sha_um" "$sha_dois")"
if [ "$(cat "$saida")" != "$esperado" ]; then
  reclamar "a lista não é o vão do mais antigo ao mais novo: $(cat "$saida")"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "a olhada trocou o checkout"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "fixar" ]; then
  reclamar "a olhada alterou o Hardness"
fi
if [ "$(git -C "$clone" branch --show-current)" != "master" ]; then
  reclamar "a olhada tirou o clone de master"
fi
if [ "$(git -C "$clone" rev-parse master)" != "$sha_rascunho" ]; then
  reclamar "a olhada apagou o commit local do clone"
fi
if ! git -C "$clone" merge-base --is-ancestor "$antes_master" master; then
  reclamar "o master local do clone perdeu o histórico anterior"
fi

# --- publicação parada: lista vazia e sucesso ---

printf '%s\n' "$sha_dois" >"$erp/publicacao/ponto"
if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a olhada sem commit novo terminou em erro: $(cat "$erro")"
fi
if [ -s "$saida" ]; then
  reclamar "a olhada sem commit novo não saiu vazia: $(cat "$saida")"
fi

# --- busca que falha não parece ausência de atualização ---

git -C "$clone" remote set-url origin "$trabalho/publicacao-ausente"
if bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a busca que falha terminou bem"
fi
if [ -s "$saida" ]; then
  reclamar "a busca que falha escreveu uma lista: $(cat "$saida")"
fi
if ! grep -q "publicação" "$erro"; then
  reclamar "a busca que falha não avisou na saída de erro: $(cat "$erro")"
fi

# --- sem ponto, a olhada mostra o candidato e não copia ---

erp="$trabalho/erp-candidato"
pub="$trabalho/pub-candidato"
clone="$trabalho/clone-candidato"
preparar_erp "$erp"
mkdir -p "$pub/bibliotecas"
printf 'v1\n' >"$pub/leia.txt"
printf 'segredo-1\n' >"$pub/bibliotecas/confUsuario.php"
git init -q -b master "$pub"
identificar "$pub"
sha_um="$(commitar "$pub" "versao um")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'v2\n' >"$pub/leia.txt"
printf 'segredo-2\n' >"$pub/bibliotecas/confUsuario.php"
sha_dois="$(commitar "$pub" "versao dois")"
printf 'v3\n' >"$pub/leia.txt"
printf 'b\n' >"$pub/outro.txt"
printf 'segredo-3\n' >"$pub/bibliotecas/confUsuario.php"
sha_tres="$(commitar "$pub" "versao tres")"

printf 'v2\n' >"$erp/src/hardness3/leia.txt"
printf 'so a fixar\n' >"$erp/src/hardness3/so-fixar.txt"
git -C "$erp" add -A
git -C "$erp" commit -q -m "ajuste local"
antes_head="$(git -C "$erp" rev-parse HEAD)"
antes_ramo="$(git -C "$erp" branch --show-current)"

if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a primeira olhada terminou em erro: $(cat "$erro")"
fi
if [ "$(cat "$saida")" != "candidato $sha_dois 0 versao dois" ]; then
  reclamar "a primeira olhada não apontou o commit que fecha: $(cat "$saida")"
fi
if [ -e "$erp/publicacao/ponto" ]; then
  reclamar "a primeira olhada gravou o ponto sem o SHA informado"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "v2" ]; then
  reclamar "a primeira olhada alterou o Hardness"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "a primeira olhada trocou o checkout"
fi
if [ "$(git -C "$erp" branch --show-current)" != "$antes_ramo" ]; then
  reclamar "a primeira olhada trocou o ramo"
fi
if [ "$(git -C "$clone" branch --show-current)" != "master" ]; then
  reclamar "a primeira olhada tirou o clone de master"
fi

if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" --ponto "$sha_dois" >"$saida" 2>"$erro"; then
  reclamar "gravar o candidato terminou em erro: $(cat "$erro")"
fi
if [ "$(tr -d '[:space:]' <"$erp/publicacao/ponto")" != "$sha_dois" ]; then
  reclamar "o candidato informado não foi gravado"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "v2" ]; then
  reclamar "gravar o candidato alterou o Hardness"
fi

if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a olhada depois do candidato terminou em erro: $(cat "$erro")"
fi
if [ "$(cat "$saida")" != "$sha_tres versao tres" ]; then
  reclamar "a olhada seguinte não partiu do SHA gravado: $(cat "$saida")"
fi

# --- nenhum commit fecha: mostra os mais próximos e não copia ---

erp="$trabalho/erp-proximo"
pub="$trabalho/pub-proximo"
clone="$trabalho/clone-proximo"
preparar_erp "$erp"
mkdir -p "$pub"
printf 'v1\n' >"$pub/leia.txt"
printf 'a\n' >"$pub/outro.txt"
git init -q -b master "$pub"
identificar "$pub"
sha_um="$(commitar "$pub" "versao um")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'v2\n' >"$pub/leia.txt"
sha_dois="$(commitar "$pub" "versao dois")"
printf 'v3\n' >"$pub/leia.txt"
printf 'b\n' >"$pub/outro.txt"
sha_tres="$(commitar "$pub" "versao tres")"
printf 'quase\n' >"$erp/src/hardness3/leia.txt"
printf 'a\n' >"$erp/src/hardness3/outro.txt"
git -C "$erp" add -A
git -C "$erp" commit -q -m "texto local"
antes_leia="$(cat "$erp/src/hardness3/leia.txt")"

if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a olhada sem fechamento terminou em erro: $(cat "$erro")"
fi
if ! grep -qx "candidato $sha_um 1 versao um" "$saida"; then
  reclamar "faltou o candidato mais próximo $sha_um: $(cat "$saida")"
fi
if ! grep -qx "candidato $sha_dois 1 versao dois" "$saida"; then
  reclamar "faltou o candidato mais próximo $sha_dois: $(cat "$saida")"
fi
if grep -q "$sha_tres" "$saida"; then
  reclamar "um commit mais distante entrou na lista: $(cat "$saida")"
fi
if [ -e "$erp/publicacao/ponto" ]; then
  reclamar "a olhada sem fechamento gravou um ponto"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "$antes_leia" ]; then
  reclamar "a olhada sem fechamento alterou o Hardness"
fi

# --- trazer limpo nasce numa branch e não troca o checkout ---

erp="$trabalho/erp-trazer"
pub="$trabalho/pub-trazer"
clone="$trabalho/clone-trazer"
preparar_erp "$erp"
mkdir -p "$pub/scripts/sql" "$pub/outros/personalizacoes/fixar"
printf 'base\n' >"$pub/leia.txt"
printf 'base\n' >"$pub/outro.txt"
printf 'antiga\n' >"$pub/scripts/sql/rotina.sql"
printf 'nossa\n' >"$pub/outros/personalizacoes/fixar/regra.php"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'publicacao\n' >"$pub/outro.txt"
printf 'nova\n' >"$pub/scripts/sql/rotina.sql"
printf 'deles\n' >"$pub/outros/personalizacoes/fixar/regra.php"
mkdir -p "$pub/bibliotecas"
printf 'segredo\n' >"$pub/bibliotecas/confUsuario.php"
printf 'outro cliente\n' >"$pub/ceigon-nota.txt"
mkdir -p "$pub/ceigon"
printf 'outro cliente\n' >"$pub/ceigon/nota.txt"
sha_ponta="$(commitar "$pub" "ponta")"

mkdir -p "$erp/src/hardness3/scripts/sql" "$erp/src/hardness3/outros/personalizacoes/fixar" "$erp/publicacao"
printf 'fixar\n' >"$erp/src/hardness3/leia.txt"
printf 'base\n' >"$erp/src/hardness3/outro.txt"
printf 'antiga\n' >"$erp/src/hardness3/scripts/sql/rotina.sql"
printf 'nossa\n' >"$erp/src/hardness3/outros/personalizacoes/fixar/regra.php"
printf 'local\n' >"$erp/src/hardness3/so-fixar.txt"
printf '%s\n' "$sha_base" >"$erp/publicacao/ponto"
printf 'intocado\n' >"$erp/marcador-banco"
git -C "$erp" add -A
git -C "$erp" commit -q -m "hardness com ajuste"
antes_head="$(git -C "$erp" rev-parse HEAD)"
antes_ramo="$(git -C "$erp" branch --show-current)"

if ! bash "$comando" trazer --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "o trazer limpo terminou em erro: $(cat "$erro") $(cat "$saida")"
fi
if [ "$(git -C "$erp" branch --show-current)" != "$antes_ramo" ]; then
  reclamar "o trazer trocou o checkout"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "o trazer avançou o checkout"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "fixar" ]; then
  reclamar "o trazer alterou o Hardness do checkout"
fi
if [ "$(cat "$erp/marcador-banco")" != "intocado" ]; then
  reclamar "o trazer mexeu no banco"
fi
ramo="publicacao/$sha_ponta"
if ! git -C "$erp" show-ref --verify --quiet "refs/heads/$ramo"; then
  reclamar "o trazer não deixou a branch $ramo: $(git -C "$erp" branch)"
fi
if [ "$(git -C "$erp" show "$ramo:src/hardness3/leia.txt")" != "fixar" ]; then
  reclamar "o ajuste da Fixar não permaneceu na branch"
fi
if [ "$(git -C "$erp" show "$ramo:src/hardness3/outro.txt")" != "publicacao" ]; then
  reclamar "a mudança só da publicação não entrou na branch"
fi
if [ "$(git -C "$erp" show "$ramo:src/hardness3/so-fixar.txt")" != "local" ]; then
  reclamar "o arquivo só da Fixar sumiu da branch"
fi
if [ "$(git -C "$erp" show "$ramo:src/hardness3/scripts/sql/rotina.sql")" != "nova" ]; then
  reclamar "o texto da rotina não entrou na branch"
fi
if [ "$(git -C "$erp" show "$ramo:src/hardness3/outros/personalizacoes/fixar/regra.php" 2>/dev/null || true)" != "nossa" ]; then
  reclamar "a personalização da publicação entrou na branch"
fi
if git -C "$erp" cat-file -e "$ramo:src/hardness3/bibliotecas/confUsuario.php" 2>/dev/null; then
  reclamar "o segredo entrou na branch"
fi
if git -C "$erp" cat-file -e "$ramo:src/hardness3/ceigon-nota.txt" 2>/dev/null; then
  reclamar "arquivo de outro cliente entrou na branch"
fi
if git -C "$erp" cat-file -e "$ramo:src/hardness3/ceigon/nota.txt" 2>/dev/null; then
  reclamar "pasta de outro cliente entrou na branch"
fi
if [ "$(git -C "$erp" log -1 --format='%s' "$ramo")" != "feat(publicacao): traz a publicação até $sha_ponta" ]; then
  reclamar "o commit da branch não segue o padrão: $(git -C "$erp" log -1 --format='%s' "$ramo")"
fi
if [ "$(tr -d '[:space:]' <"$erp/publicacao/ponto")" != "$sha_ponta" ]; then
  reclamar "o trazer limpo não avançou o ponto"
fi
if ! bash "$comando" olhar --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a olhada depois do trazer terminou em erro: $(cat "$erro")"
fi
if [ -s "$saida" ]; then
  reclamar "a olhada depois do trazer não saiu vazia: $(cat "$saida")"
fi

# --- conflito não avança o ponto e não deixa a árvore no meio do merge ---

erp="$trabalho/erp-conflito"
pub="$trabalho/pub-conflito"
clone="$trabalho/clone-conflito"
preparar_erp "$erp"
mkdir -p "$pub" "$erp/publicacao"
printf 'base\n' >"$pub/leia.txt"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'deles\n' >"$pub/leia.txt"
sha_ponta="$(commitar "$pub" "ponta")"
printf 'fixar\n' >"$erp/src/hardness3/leia.txt"
printf '%s\n' "$sha_base" >"$erp/publicacao/ponto"
git -C "$erp" add -A
git -C "$erp" commit -q -m "ajuste no mesmo arquivo"
antes_head="$(git -C "$erp" rev-parse HEAD)"
antes_ponto="$(tr -d '[:space:]' <"$erp/publicacao/ponto")"

if bash "$comando" trazer --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "o conflito terminou bem"
fi
if ! grep -qx "conflito leia.txt" "$saida"; then
  reclamar "o conflito não listou o arquivo: $(cat "$saida")"
fi
if [ "$(tr -d '[:space:]' <"$erp/publicacao/ponto")" != "$antes_ponto" ]; then
  reclamar "o conflito avançou o ponto"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "o conflito trocou o checkout"
fi
if [ "$(cat "$erp/src/hardness3/leia.txt")" != "fixar" ]; then
  reclamar "o conflito alterou o Hardness do checkout"
fi
if [ -n "$(git -C "$erp" diff --name-only --diff-filter=U)" ]; then
  reclamar "o conflito deixou a árvore no meio do merge"
fi
if [ -n "$(git -C "$erp" branch --list 'publicacao/*')" ]; then
  reclamar "o conflito criou uma branch: $(git -C "$erp" branch)"
fi

rm -f "$saida" "$erro"

if [ "$falhou" -ne 0 ]; then
  exit 1
fi
echo "Publicação por olhada e por merge."
