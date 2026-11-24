#!/usr/bin/env bash
# Prova que o trazer devolve o gancho do desktop e para se a âncora sumiu.
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
saida="$(mktemp)"
erro="$(mktemp)"
trap 'rm -rf "$trabalho" "$saida" "$erro"' EXIT

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

ancora=$'\t\t{$cssJs}\n\t\t<style type="text/css">\n\t\t{$cssExtra}\n\t\t</style>\n'

modelo() {
  local destino="$1"
  local marca="$2"
  printf '%s<!-- %s -->\n' "$ancora" "$marca" >"$destino"
}

# --- sobrescrever devolve o gancho e fica com o texto da publicação ---

erp="$trabalho/erp-gancho"
pub="$trabalho/pub-gancho"
clone="$trabalho/clone-gancho"
mkdir -p "$erp/src/hardness3/smarty/templates" "$erp/bin" "$pub/smarty/templates"
modelo "$pub/smarty/templates/index.tpl" "base"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
modelo "$pub/smarty/templates/index.tpl" "deles"
sha_ponta="$(commitar "$pub" "ponta com a ancora")"
git init -q -b master "$erp"
identificar "$erp"
modelo "$erp/src/hardness3/smarty/templates/index.tpl" "fixar"
printf '%s\n' "$sha_base" >"$erp/bin/ponto"
commitar "$erp" "gancho local" >/dev/null
antes_head="$(git -C "$erp" rev-parse HEAD)"

if ! bash "$comando" trazer --sobrescrever --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "o trazer com sobrescrita não devolveu o gancho: $(cat "$erro") $(cat "$saida")"
else
  resultado="$(git -C "$erp" show "publicacao/$sha_ponta:src/hardness3/smarty/templates/index.tpl")"
  if ! grep -q '/fx/shell.css' <<<"$resultado"; then
    reclamar "a sobrescrita não religou o CSS do shell"
  fi
  if ! grep -q '/fx/tema.css' <<<"$resultado"; then
    reclamar "a sobrescrita não religou a folha do tema"
  fi
  if ! grep -q '<!-- deles -->' <<<"$resultado"; then
    reclamar "a sobrescrita não ficou com o texto da publicação"
  fi
  if grep -q '<!-- fixar -->' <<<"$resultado"; then
    reclamar "a sobrescrita manteve o texto da Fixar no template"
  fi
  if [ "$(tr -d '[:space:]' <"$erp/bin/ponto")" != "$sha_ponta" ]; then
    reclamar "o gancho recolocado não avançou o ponto"
  fi
  if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
    reclamar "o gancho recolocado trocou o checkout"
  fi
fi

# --- âncora ausente para sem avançar o ponto ---

erp="$trabalho/erp-ancora"
pub="$trabalho/pub-ancora"
clone="$trabalho/clone-ancora"
mkdir -p "$erp/src/hardness3/smarty/templates" "$erp/bin" "$pub/smarty/templates"
modelo "$pub/smarty/templates/index.tpl" "base"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf '<!-- sem ancora -->\n' >"$pub/smarty/templates/index.tpl"
sha_ponta="$(commitar "$pub" "ponta sem a ancora")"
git init -q -b master "$erp"
identificar "$erp"
modelo "$erp/src/hardness3/smarty/templates/index.tpl" "base"
printf '%s\n' "$sha_base" >"$erp/bin/ponto"
commitar "$erp" "template na base" >/dev/null
antes_head="$(git -C "$erp" rev-parse HEAD)"

if bash "$comando" trazer --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "a âncora ausente não parou o trazer"
fi
if ! grep -q "não encaixa" "$erro"; then
  reclamar "a parada não explicou a âncora: $(cat "$erro")"
fi
if [ "$(tr -d '[:space:]' <"$erp/bin/ponto")" != "$sha_base" ]; then
  reclamar "a âncora ausente avançou o ponto"
fi
if [ -n "$(git -C "$erp" branch --list 'publicacao/*')" ]; then
  reclamar "a âncora ausente criou uma branch"
fi
if [ "$(git -C "$erp" rev-parse HEAD)" != "$antes_head" ]; then
  reclamar "a âncora ausente trocou o checkout"
fi

# --- o gancho que já está no template não entra duas vezes ---

erp="$trabalho/erp-uma-vez"
pub="$trabalho/pub-uma-vez"
clone="$trabalho/clone-uma-vez"
mkdir -p "$erp/src/hardness3/smarty/templates" "$erp/bin" "$pub/smarty/templates"
modelo "$pub/smarty/templates/index.tpl" "base"
git init -q -b master "$pub"
identificar "$pub"
sha_base="$(commitar "$pub" "base da copia")"
git clone -q "$pub" "$clone"
identificar "$clone"
printf 'outro\n' >"$pub/outro.txt"
sha_ponta="$(commitar "$pub" "arquivo novo")"
git init -q -b master "$erp"
identificar "$erp"
{
  printf '%s' "$ancora"
  printf '\t\t<link rel="stylesheet" href="/fx/shell.css" type="text/css">\n'
  printf '\t\t<link rel="stylesheet" href="/fx/tema.css" type="text/css">\n'
  printf '\t\t<meta name="fx-empresa-nome" content="{$empresaAtualNome|escape:'\''html'\''}">\n'
  printf '\t\t<script src="/fx/shell.js" defer></script>\n'
  printf '<!-- fixar -->\n'
} >"$erp/src/hardness3/smarty/templates/index.tpl"
printf '%s\n' "$sha_base" >"$erp/bin/ponto"
commitar "$erp" "template com gancho" >/dev/null

if ! bash "$comando" trazer --raiz "$erp" --publicacao "$clone" >"$saida" 2>"$erro"; then
  reclamar "o trazer do gancho já presente terminou em erro: $(cat "$erro")"
else
  resultado="$(git -C "$erp" show "publicacao/$sha_ponta:src/hardness3/smarty/templates/index.tpl")"
  if [ "$(grep -c '/fx/shell.css' <<<"$resultado")" -ne 1 ]; then
    reclamar "o CSS do shell entrou mais de uma vez"
  fi
  if [ "$(grep -c '/fx/tema.css' <<<"$resultado")" -ne 1 ]; then
    reclamar "a folha do tema entrou mais de uma vez"
  fi
  if ! grep -q '<!-- fixar -->' <<<"$resultado"; then
    reclamar "o trazer de outro arquivo tirou o gancho da Fixar"
  fi
fi

# --- o checkout serve o gancho, e a cara mora fora da publicação ---

template="$raiz_repo/src/hardness3/smarty/templates/index.tpl"
if ! grep -q 'href="/fx/shell.css"' "$template"; then
  reclamar "o desktop servido não liga o CSS do shell"
fi
if ! grep -q 'href="/fx/tema.css"' "$template"; then
  reclamar "o desktop servido não liga a folha do tema"
fi
if ! grep -q 'src="/fx/shell.js"' "$template"; then
  reclamar "o desktop servido não liga o script do shell"
fi
for folha in shell.css tema.css shell.js; do
  caminho="$raiz_repo/src/fx/$folha"
  if [ ! -f "$caminho" ]; then
    reclamar "falta src/fx/$folha, arquivo que a publicação não envia"
  elif [[ "$caminho" == "$raiz_repo/src/hardness3/"* ]]; then
    reclamar "src/fx/$folha entrou na árvore que a publicação envia"
  fi
done

if [ "$falhou" -ne 0 ]; then
  exit 1
fi
echo "O gancho do shell sobrevive ao trazer."
