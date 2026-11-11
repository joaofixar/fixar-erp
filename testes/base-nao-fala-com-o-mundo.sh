#!/usr/bin/env bash
# Observa, pelo HTTP, o login, cada tela do ciclo e os cliques de emitir nota,
# registrar boleto e enviar e-mail. Nenhuma conexão de saída fica registrada.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

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
saidas="$(mktemp)"
trap 'rm -f "$menu" "$corpo" "$biscuits" "$saidas"' EXIT

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

echo "Abrindo o login"
if ! curl -sS -L --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" "$base/login"; then
  reclamar "não foi possível abrir $base/login"
fi

echo "Entrando com o usuário local"
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

grade() {
  local caminho="$1"
  local arquivo
  arquivo="$(mktemp)"
  if ! seguir "$caminho" "$arquivo"; then
    case "$caminho" in
      "Orçamentos") printf '%s' "/ven/ven001/content/ven001ContentPrincipal/" >"$arquivo" ;;
      "Pedidos") printf '%s' "/ven/ven002/content/ven002content01/" >"$arquivo" ;;
      "Boleto Bancário") printf '%s' "/fin/fin005/content/fin005ContentPrincipal/" >"$arquivo" ;;
      *)
        reclamar "o menu não oferece ${caminho}"
        rm -f "$arquivo"
        return
        ;;
    esac
  fi
  local url
  url="$(cat "$arquivo")"
  rm -f "$arquivo"
  local salto
  for salto in 1 2 3 4 5; do
    local codigo
    if ! codigo="$(curl -sS --max-time 40 -o "$corpo" -c "$biscuits" -b "$biscuits" -w '%{http_code}' \
      "${base}${url}?ajax=true")"; then
      reclamar "${caminho} não respondeu"
      return
    fi
    if [ "$codigo" -ge 400 ]; then
      reclamar "${caminho} abriu com erro"
      return
    fi
    if python -c '
import pathlib, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
ruim = ("Fatal error", "Parse error", "Uncaught", "Erro ao executar")
raise SystemExit(0 if any(trecho in pagina for trecho in ruim) else 1)
' "$corpo"; then
      reclamar "${caminho} abriu com erro"
      return
    fi
    if python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
raise SystemExit(0 if re.search("class=.grid( |\042)", pagina) else 1)
' "$corpo"; then
      return
    fi
    url="$(python -c '
import pathlib, re, sys
pagina = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
urls = re.findall(r"url:\s*'\''([^'\'']+)'\''", pagina)
grades = re.findall(r"(/[a-z]{3}/[a-z0-9]{6}/grid/[A-Za-z0-9_]+/)", pagina)
print(urls[0] if urls else (grades[0] if grades else ""))
' "$corpo")"
    if [ -z "$url" ]; then
      reclamar "${caminho} não chegou numa grade"
      return
    fi
  done
  reclamar "${caminho} não chegou numa grade"
}

telas=(
  "Cliente/Fornecedor"
  "Produtos"
  "Orçamentos"
  "Pedidos"
  "Notas Fiscais"
  "Estoque"
  "Notas Fiscais Recebidas"
  "Expedição Mercadorias"
  "Contas a Receber"
  "Contas a Pagar"
  "Caixa e Bancos"
  "Boleto Bancário"
  "CFOP"
  "NCM"
  "ICMS"
  "Usuários/Perfis"
  "Empresas"
)

for tela in "${telas[@]}"; do
  echo "Abrindo ${tela}"
  grade "$tela"
done

echo "Conferindo conexões depois do login e das telas"
saida_limpa "no login e na abertura das telas"

clique() {
  local frase="$1"
  local url="$2"
  if ! curl -sS --max-time 20 -o "$corpo" -c "$biscuits" -b "$biscuits" \
    "${base}${url}"; then
    reclamar "o clique não respondeu: ${frase}"
    return
  fi
  if ! python -c '
import json, pathlib, sys
bruto = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
frase = sys.argv[2]
inicio, fim = bruto.find("("), bruto.rfind(")")
miolo = bruto[inicio + 1:fim] if inicio >= 0 and fim > inicio else bruto
try:
    dado = json.loads(miolo)
except json.JSONDecodeError:
    dado = None
texto = dado.get("data", "") if isinstance(dado, dict) else bruto
raise SystemExit(0 if frase in texto or frase in bruto else 1)
' "$corpo" "$frase"; then
    reclamar "o clique não ficou na máquina: ${frase}"
  fi
}

echo "Clicando em emitir nota"
clique "A base local não emite nota." \
  "/ven/ven012/grid_func-ajax/txSefaz/?ajax=true&tx=true&T007_Id=1"
clique "A base local não emite nota." \
  "/ven/ven012/grid_func-ajax/ven012EnviarLote/?ajax=true"
if grep -q "Autorizado o uso\|protNFe\|Lote transmitido" "$corpo"; then
  reclamar "emitir nota chegou à receita"
fi

echo "Clicando em registrar boleto"
clique "A base local não registra boleto." \
  "/fin/fin001/grid_func-ajax/verificaRemessa/?ajax=true"
if python -c '
import json, pathlib, sys
bruto = pathlib.Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace")
inicio, fim = bruto.find("("), bruto.rfind(")")
dado = json.loads(bruto[inicio + 1:fim])
# O botão segue para a remessa quando code == 0. false também entra.
raise SystemExit(0 if dado.get("code") in (0, False, "0") else 1)
' "$corpo"; then
  reclamar "registrar boleto seguiu para o banco"
fi
if grep -q "Remessa gerada" "$corpo"; then
  reclamar "registrar boleto chegou ao banco"
fi

echo "Clicando em enviar e-mail"
clique "A base local não envia e-mail." \
  "/ven/ven012/form_func-ajax/enviarEmailNotas/?ajax=true"
if grep -q "E-mails enviados com sucesso" "$corpo"; then
  reclamar "enviar e-mail saiu da máquina"
fi

echo "Conferindo conexões depois dos cliques"
saida_limpa "nos cliques"

echo "Saindo da sessão"
if ! curl -sS --max-time 20 -o /dev/null -c "$biscuits" -b "$biscuits" "$base/logout"; then
  reclamar "a saída da sessão não respondeu"
fi

echo "Conferindo conexões depois de sair"
saida_limpa "ao sair"

if [ "$falhou" -ne 0 ]; then
  exit 1
fi

echo "A base local não fala com o mundo."
