# Task 60 — Inclusão assistida de GNRE por NF

## Situação

Escopos 1 e 2 aprovados. Escopo 3 implementado e aguardando validação manual.

## Contrato vigente do Escopo 3

O fluxo é exclusivamente de inclusão em lote. O usuário seleciona NFs elegíveis
e **Preparar guias selecionadas** relê os fatos sem criar registros. A
conferência abre em modal interno, sobre a lista de NFs, e mostra uma linha por
combinação NF + tributo temporário: `ST`, `DIFAL` ou `FCP`. Cada combinação
válida requer tipo GNRE explícito e cria um rascunho independente. Não há
inclusão individual, consolidação de componentes ou unicidade somente por NF.

Um componente só é criado se seu flag de CFOP estiver em `S` e seu total final
de `T007` for positivo: ST usa `T007_Valor_ICMS_Substituicao_Retencao`, DIFAL
usa `T007_Valor_Total_Partilha_ICMS_Destino` e FCP usa
`T007_Valor_Total_FCP_Partilha`. Os valores são apenas referência; não criam
valores GNRE automaticamente. Componentes sem valor final positivo não são
falha e não aparecem na conferência nem no resultado; os demais componentes da
mesma NF continuam independentes.

O modal oferece **Criar rascunhos conferidos**, **Cancelar** e fechamento por
**X**. Cancelar ou fechar descarta somente o plano transitório, sem criar,
alterar ou excluir registros. O backdrop e Esc não fecham o modal. Sem
JavaScript, a mesma conferência, escolha, cancelamento e criação continuam
acessíveis na página. A criação preserva POST, CSRF, token transitório,
releitura no servidor, transação por combinação e idempotência composta.

## Vínculo e rascunho

`GNRE002_T007_Id` e `GNRE002_Tributo_Origem` (`ST`, `DIFAL`, `FCP`) identificam
a origem. A única chave vigente é composta:

```sql
UQ_GNRE002_NF_TRIBUTO (GNRE002_T007_Id, GNRE002_Tributo_Origem)
```

Assim, uma NF pode gerar guias diferentes por tributo, mas nunca duplica a
mesma combinação. Documentos manuais mantêm ambos os campos nulos.

O rascunho incompleto é explícito: `GNRE001_Codigo_Receita=''`,
`GNRE001_Valor=0.00` e `GNRE001_Data_Vencimento=NULL`. Receita, vencimento,
valor final da GNRE, itens, destinatário e demais dados fiscais continuam
manuais; nenhuma data ou valor fiscal fictício é gravado.

## SQL manual — não executado

```sql
SELECT GNRE002_T007_Id, GNRE002_Tributo_Origem, COUNT(*) AS quantidade
FROM GNRE002
WHERE GNRE002_T007_Id IS NOT NULL OR GNRE002_Tributo_Origem IS NOT NULL
GROUP BY GNRE002_T007_Id, GNRE002_Tributo_Origem
HAVING COUNT(*) > 1;

ALTER TABLE GNRE002
  ADD COLUMN GNRE002_T007_Id INT UNSIGNED NULL,
  ADD COLUMN GNRE002_Tributo_Origem VARCHAR(10) NULL,
  ADD UNIQUE KEY UQ_GNRE002_NF_TRIBUTO
    (GNRE002_T007_Id, GNRE002_Tributo_Origem);

ALTER TABLE GNRE001
  MODIFY GNRE001_Codigo_Receita VARCHAR(20) NOT NULL DEFAULT '',
  MODIFY GNRE001_Valor DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  MODIFY GNRE001_Data_Vencimento DATE NULL;
```

Nenhum SQL foi executado. Snapshot ausente ou ambíguo bloqueia sem escrita;
nunca há escolha silenciosa da primeira versão.

## Roteiro manual

1. Selecionar NF com mais de um tributo elegível e preparar o lote; confirmar
   que a lista de NFs permanece ao fundo e que o modal se chama **Conferir
   rascunhos**.
2. Confirmar uma linha e um rascunho por tributo, com seletor de tipo GNRE
   oriundo exclusivamente do snapshot compatível.
3. Confirmar que um componente com total zero/nulo não aparece como bloqueio e
   não impede os outros componentes positivos da mesma NF.
4. Fechar por X, Cancelar, recarregar e refazer a busca; confirmar que nenhum
   rascunho é criado antes da confirmação.
5. Repetir o lote e confirmar ausência de duplicidade NF + tributo.
6. Confirmar que valores do grid são referência de `T007` e que não há item,
   valor GNRE, receita ou vencimento automático.

Não há XML, transmissão, consulta, PDF, Portal, financeiro ou contas a pagar.
Agrupamento por UF depende de futura task de configuração empresa × UF.
