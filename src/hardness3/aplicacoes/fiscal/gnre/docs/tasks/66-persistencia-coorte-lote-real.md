# Task 66 — persistência segura da coorte do lote GNRE real

Implementada localmente em 05/11/2026. Esta entrega cria somente a intenção
persistente e transacional de uma coorte homogênea; não gera XML, não chama o
Portal, não transmite, não consulta, não obtém PDF e não altera a fila/tela
individual da Task 63.

## Modelo

`GNRE010` representa a coorte física futura, ainda sem envio: empresa,
`GNRE007` imutável comum, UF, ambiente vindo exclusivamente do snapshot,
layout `2.00`, versão `GNRE-BATCH-CORRELATION-v1`, situação `PREPARADO` ou
`CANCELADO` e criação. Seus índices são empresa+situacao e snapshot.

`GNRE011` representa uma participante imutável: coorte, guia, fragmento
canônico, SHA-256 do fragmento, fingerprint de negócio, versão e criação. A
chave única `(GNRE011_GNRE010_Id, GNRE011_GNRE001_Id)` impede duplicação dentro
da coorte; há índices por guia e lote. Não há unicidade global por guia: depois
de uma coorte `CANCELADO` sem transmissão, ela poderá entrar em nova coorte.
Decisão e candidatos de correlação iniciam `NULL`; ficam reservados para a
futura auditoria de `MATCHED`, `UNMATCHED`, `AMBIGUOUS` e
`INSUFFICIENT_EVIDENCE`. `GNRE012` não foi criado.

SQL manual para instalações locais (não executado nesta task):

```sql
-- Aplicar os CREATE TABLE GNRE010 e GNRE011 documentados em
-- scripts/atualizacaoBase.php, na seção “Task 66”.
-- Não executar automaticamente nem em banco real.
```

## Serviço e garantias

`GnreRealBatchCohort::prepare()` recebe IDs, inicia uma transação e bloqueia a
empresa e todas as guias com `FOR UPDATE`. Aceita somente empresa ativa e
guias rascunho, não arquivadas, sem `GNRE004`, `GNRE005` ou `GNRE008`. Exige
uma coorte única de empresa, UF, ambiente `1`/`2`, mesmo `GNRE007` válido e
layout. Não existe fallback de ambiente.

O serviço cria `GNRE010` e todas as `GNRE011` na mesma transação. Uma repetição
da seleção exatamente igual encontra e devolve a mesma coorte `PREPARADO`;
uma guia já em outra coorte preparada bloqueia. Qualquer erro, inclusive dados
de uma participante incompletos, faz rollback integral. Nenhuma situação de
`GNRE001` é alterada.

Para cada participante, a única fonte do fragmento, SHA-256 e fingerprint é o
contrato da Task 65 (`GnreBatchCorrelationFingerprint`). Assim, NF-e,
documento de origem, campos extras, identificador, posição, recibo, PDF,
controle, estado técnico e `situacaoGuia` não entram no fingerprint de negócio.
`situacaoGuia=0` continua sendo a futura e única confirmação individual de
autorização.

Correção posterior da Task 66: antes de congelar a participante, o serviço
valida os mesmos dados usados pelo gerador atual, sem materializar XML, e usa
`GnreTdadosProjection` como fonte exclusiva de `TDadosGNRE`. A projeção inclui
emitente somente quando o snapshot exige, normaliza município como o gerador,
preserva documento, destinatário, referência, valores e extras somente quando
seriam serializados, e omite `valorGNRE` quando a UF calcula o valor. Nesse
último caso, a ausência da âncora mínima do contrato v1 bloqueia a coorte antes
de qualquer `GNRE010`/`GNRE011` ser gravada.

Homologação e Produção usam a mesma persistência e contrato; esta task não
libera transmissão real em nenhum ambiente. A análise de coorte/recibo comum
da Task 64 e o contrato de correlação da Task 65 são preservados, enquanto a
Task 63 segue independente e estritamente individual.
