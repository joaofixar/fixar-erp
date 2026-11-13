# Task 74 — Tratamento conservador de resultado pendente GNRE

Corrigida em 05/11/2026, sem executar SQL, migration, chamadas ao Portal,
transmissão, consulta, PDF ou alteração de guias reais.

Quando a consulta persistida indica pendência oficial — processo `400`, `401`
ou `403` sem resultado individual, inclusive `situacaoGuia=4` — a tentativa recebida continua em `P`
e sua única `GNRE005` continua `PENDENTE`. Não há retransmissão, nova
`GNRE004`, retorno ao rascunho nem regra por UF.

## Reagendamento

`GNRE004_Tempo_Estimado_Processamento_MS`, quando entre 1 segundo e 1 hora,
orienta a próxima consulta, limitada a esse mesmo intervalo. Sem estimativa
válida, a Fila tenta novamente em 3 segundos; esse valor foi escolhido para
acompanhamento rápido de lotes recebidos sem prazo oficial.
Só uma única guia individual `situacaoGuia=0`, `1` ou `2` pode finalizar a
consulta. Resultado múltiplo, código desconhecido, ausência de guia e qualquer
outra evidência ambígua permanecem pendentes/inconclusivos e não tornam
`GNRE001=F` nem `GNRE005=FINALIZADA`. Falhas técnicas, timeout, TLS, HTTP/SOAP inválido e `Retry-After` continuam na
política técnica anterior; não são classificados como pendência.

O worker preserva lease, token, lock, empresa, guia e ambiente imutável. SOAP,
código HTTP e descrição continuam auditáveis em `GNRE005`; GETs continuam sem
chamada ao Portal. Códigos não oficiais/desconhecidos sem resultado individual
inequívoco permanecem inconclusivos, nunca autorização.

## Apresentação

Na transmissão individual e na Fila, a pendência mostra:

> O Portal recebeu a guia e ainda está processando o resultado. Não retransmita esta guia.

A descrição pública persistida do Portal aparece separadamente, e o próximo
horário permitido é exibido em horário brasileiro. O modal individual informa
que o acompanhamento seguirá o horário registrado. A Fila segue primeiro para
guias ainda não iniciadas; enquanto o modal permanecer aberto, aguarda a menor
data persistida, mostra contagem regressiva e retoma somente as guias já
liberadas. Ela só consolida o resumo quando não houver pendência ou quando o
usuário interromper. PDF permanece condicionado ao `situacaoGuia=0`
individual persistido; `1`/`2` seguem rejeição final.

## Reparo controlado da regressão 401

O exemplo manual [reparar-pendencias-task74.sql.example](../../scripts/reparar-pendencias-task74.sql.example)
apenas seleciona e, após inspeção humana e troca explícita de
`<IDS_CONFIRMADOS>`, restaura os casos afetados: última tentativa recebida,
SOAP `400`/`401` sem situação individual `0`/`1`/`2`, guia `F` e fila
`FINALIZADA`. Ele preserva XML, tentativa, recibo, SOAP e auditoria, não cria
`GNRE004` e agenda a nova tentativa pela mesma política conservadora. Não foi
executado; a seleção também é coberta por teste sintético.
