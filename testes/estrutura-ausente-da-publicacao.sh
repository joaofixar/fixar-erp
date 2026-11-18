#!/usr/bin/env bash
# A subida cria coluna e índice que o script do Hardness pede. Não grava
# configuração. Este teste confere a lista, sem falar com o banco.
set -euo pipefail

raiz="$(cd "$(dirname "$0")/.." && pwd)"
cd "$raiz"

fixo=$(mktemp)
saida=$(mktemp)
real=$(mktemp)
limpar() {
  rm -f "$fixo" "$saida" "$real" estrutura-cmp-awk.txt
}
trap limpar EXIT

cat >"$fixo" <<'EOF'
<?php
Criar_Campo("T182_Hora_Conclusao ","time");
Criar_Campo("C004_Flag_Usar_DANFE_RTC", "char(1) NOT NULL DEFAULT 'N' COMMENT 'N=Modelo atual; S=Usar DANFE RTC'");
Criar_Indice('T007_Flag_ACP ');
Criar_Indice("INT008_C004_Id,,INT008_Proxima_Tentativa,,INT008_Id", 'UNIQUE');
Criar_Campos('T003A', array(
    'T003A_Valor_Total_CBS' => "decimal(15,2) NOT NULL DEFAULT 0.00",
));
mysql_query("INSERT INTO C031 (C031_Campo) VALUES ('x')");
EOF

ESTRUTURA_LISTAR=1 ESTRUTURA_ARQUIVO="$fixo" bash banco/aplicar-estrutura-ausente.sh >"$saida"

reclamar() {
  printf '%s\n' "$1" >&2
  exit 1
}

grep -qx $'coluna\tT182\tT182_Hora_Conclusao\ttime' "$saida" || reclamar "o nome com espaço não foi aparado"
grep -qx $'coluna\tC004\tC004_Flag_Usar_DANFE_RTC\tchar(1) NOT NULL DEFAULT '\''N'\'' COMMENT '\''N=Modelo atual; S=Usar DANFE RTC'\' "$saida" || reclamar "a coluna da DANFE não entrou na lista"
grep -qx $'indice\tT007\tIDX_T007_Flag_ACP\tT007_Flag_ACP\t' "$saida" || reclamar "o índice de um campo não entrou"
grep -qx $'indice\tINT008\tIDX_INT008_C004_Id_INT008_Proxima_Tentativa_INT008_Id\tINT008_C004_Id,INT008_Proxima_Tentativa,INT008_Id\tUNIQUE' "$saida" || reclamar "o índice único não entrou"
grep -qx $'coluna\tT003A\tT003A_Valor_Total_CBS\tdecimal(15,2) NOT NULL DEFAULT 0.00' "$saida" || reclamar "o lote de colunas não entrou"
if grep -q 'INSERT' "$saida"; then
  reclamar "a lista trouxe SQL que não é coluna nem índice"
fi

ESTRUTURA_LISTAR=1 ESTRUTURA_ARQUIVO="src/hardness3/scripts/atualizacaoBase.php" bash banco/aplicar-estrutura-ausente.sh >"$real"
grep -q $'^coluna\tC004\tC004_Flag_Usar_DANFE_RTC\t' "$real" || reclamar "o script publicado não pede a coluna da DANFE"
if grep -q 'INSERT' "$real"; then
  reclamar "o script publicado vazou um INSERT"
fi

printf '%s\n' "Coluna e índice ausentes entram na subida."
