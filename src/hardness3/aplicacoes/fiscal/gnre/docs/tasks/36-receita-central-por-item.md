# Task 36 — Receita projetada por item na Central de Guias

## Entrega

A coluna **Receita principal** da Central de Guias continua exibindo `GNRE001_Codigo_Receita` quando o cabeçalho possui valor. Quando ele está vazio, a listagem lê os itens `GNRE003` já persistidos da mesma guia em `GNRE003_Id ASC`: exibe a primeira receita e, se houver códigos distintos posteriores, informa a quantidade adicional, por exemplo `100102 · +2 receitas`.

Isso é exclusivamente uma projeção operacional de leitura. Não define uma receita fiscal principal, não atualiza/copía/preenche `GNRE001_Codigo_Receita` e não altera o snapshot `GNRE007` nem regras por UF, receita ou tipo.

O filtro GET **Receita principal** localiza a guia quando o código está no cabeçalho ou em qualquer `GNRE003` da mesma guia. O filtro de **vencimento** agora segue a mesma abrangência: aplica a faixa ao vencimento válido do cabeçalho ou a qualquer vencimento válido de item. As condições de item usam `EXISTS`, portanto múltiplos itens não duplicam a linha. A consulta continua iniciando por `GNRE001_C004_Id=?`, mantendo o isolamento da empresa GNRE ativa e os demais filtros existentes (situação, UF, ambiente, número e recibo).

## Limites preservados

Não houve escrita, migration, alteração de schema, snapshot, XML, Portal, transmissão, consulta, fila, PDF ou guia real. A apresentação de vencimento não foi alterada; somente seu filtro passou a considerar fatos de item já persistidos.

## Testes

`tests/task36-receita-central-por-item-synthetic.php` cobre: cabeçalho preenchido; um item; múltiplas receitas com primeira por `GNRE003_Id`; deduplicação de códigos; filtros de receita e vencimento por qualquer item com `EXISTS`; escopo da empresa; normalização das faixas; e ausência de comandos de escrita/migration no read model.

## Roteiro manual sem transmissão

1. Abra a Central de Guias com uma guia de cabeçalho sem receita e item com receita; confirme que a coluna não mostra `—`.
2. Filtre pelo código do item e confirme a localização da guia.
3. Em uma guia com itens de receitas distintas, confirme a primeira por ID e a indicação de receitas adicionais.
4. Filtre uma faixa que exista somente no vencimento de item e confirme a localização sem duplicação; confirme que empresa ativa e demais filtros permanecem corretos.
