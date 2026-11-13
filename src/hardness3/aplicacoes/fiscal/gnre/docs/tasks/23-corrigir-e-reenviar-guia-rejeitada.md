# Task 23 — Corrigir e reenviar guia rejeitada

## Correção urgente — reabertura da guia rejeitada

Em 04/11/2026, a ação pública **Corrigir e reenviar** foi restaurada na aba
**Processar**, junto ao resultado individual persistido. Ela reabre a própria
`GNRE001` — nunca cria uma guia de correção — e explica que o Histórico é
preservado. A guarda da tela e a guarda transacional usam a mesma decisão
conservadora: somente a tentativa mais recente pode liberar a ação, quando é
recebida com recibo, possui exatamente uma fila correspondente em
`FINALIZADA`, SOAP válido e exatamente um resultado individual `situacaoGuia`
`1` ou `2`. Resultado `0`, mais de um resultado individual, SOAP ausente ou
inválido, fila duplicada, pendente/processando/bloqueada e qualquer tentativa
posterior inconclusiva não liberam a reabertura.

## Entrega corrigida

Uma guia em estado técnico `F` oferece **Reabrir para correção** somente se o
SOAP de resultado já persistido contiver **exatamente um** resultado individual
e ele for final `1` ou `2`. O resultado individual `0` (emitida) prevalece
sempre; qualquer combinação múltipla (`0/1`, `0/2`, `1/2`), pendência,
recepção inconclusiva, ausência de resultado individual válido e demais falhas
técnicas não são elegíveis.

A ação bloqueia a própria `GNRE001` em transação e muda somente seu status para
`R`, mantendo o mesmo identificador. A segunda ação, quando a guia já está em
`R`, é idempotente e não cria ou altera auditoria. A reabertura invalida o XML
local da sessão; o usuário precisa gerar e validar novo XML antes de transmitir.

Nenhum `GNRE001`, `GNRE002`, `GNRE003`, `GNRE004`, `GNRE005` ou `GNRE006` é
criado, apagado ou copiado durante a reabertura. Tentativas, recibos, SOAP,
XMLs enviados, filas e resultados anteriores permanecem como histórico. A
transmissão posterior usa o fluxo normal, que cria a próxima `GNRE004`
sequencial para a mesma guia somente após ação explícita do usuário.

Na listagem, uma guia reaberta (`R`) aparece como **Rascunho**, mesmo que o
histórico anterior contenha rejeição. Isso não muda o critério de emissão: só
o resultado individual `situacaoGuia=0` confirma autorização.

## Compatibilidade de schema

O campo legado nullable `GNRE001_GNRE001_Id_Origem_Correcao` e seu índice
permanecem no schema por compatibilidade, mas não são utilizados por este
fluxo. Esta correção não contém migration nem executa SQL de schema ou de
dados.

## Arquivos

- `src/CorrectionReopen.php`: elegibilidade, decisão idempotente e transação
  que reabre a própria guia.
- `src/GuideCenter.php`: prioriza `R` sobre resultado histórico para a lista.
- `public/index.php` e `public/task10-presentation.php`: endpoint protegido,
  invalidação do XML local e mensagem da própria guia reaberta.
- `tests/task23-correction-draft-synthetic.php`: contrato da reabertura.

## Validação

No `hardness-php7`:

```sh
php aplicacoes/fiscal/gnre/tests/task23-correction-draft-synthetic.php
```

Roteiro manual em uma guia de homologação individualmente rejeitada criada
apenas para o teste: abrir, reabrir e conferir o mesmo número/mesmas
tentativas; reabrir novamente e conferir que não há novo registro; corrigir um
campo, gerar XML, confirmar que a transmissão só é liberada após validação e
enviar somente por ação explícita. Não usar guias históricas protegidas.
