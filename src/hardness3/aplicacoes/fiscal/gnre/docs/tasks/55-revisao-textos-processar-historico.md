# Task 55 — revisão dos textos Processar e Histórico

## Entrega

## Simplificação da rejeição individual e motivos no Histórico — 04/11/2026

Quando a política já existente da Task 23 confirma que a última tentativa pode
ser reaberta (resultado individual persistido, inequívoco, `situacaoGuia=1` ou
`2`), **Processar** substitui os controles redundantes por um único bloco:
**Guia rejeitada pelo Portal**, aviso de que não houve emissão/autorização,
todos os motivos persistidos (código, descrição e campo, quando informado) e
**Corrigir e reenviar**. O texto informa que a própria guia será reaberta e
que tentativas e Histórico permanecem preservados. Pendência, inconclusivo,
rejeição de recepção e autorização continuam na apresentação anterior.

No **Histórico**, o motivo é anexado apenas ao cartão da tentativa cujo
`GNRE004_Id` está vinculado à fila/resultados persistidos; a situação de
transmissão “Recebida pelo Portal” permanece separada do resultado individual.
Cada motivo rejeitado recebe seu próprio rótulo, sem atribuí-lo a tentativas
anteriores e sem expor SOAP/XML.

A aba **Processar** passou a chamar o bloco de **Processar guia** e esclarece
que **Transmitir GNRE** é o fluxo guiado recomendado; os controles da aba são
ações avulsas de acompanhamento ou operação manual. Estado final `F` passou a
usar texto neutro: o Portal concluiu o processamento e o usuário deve conferir
o resultado. A confirmação de autorização continua dependendo exclusivamente
do resultado individual persistido com `situacaoGuia=0`.

Quando a consulta está em andamento, a orientação informa para aguardar alguns
instantes e atualizar a página; a aba não promete atualização automática.
Mensagens de recepção rejeitada, retorno inconclusivo, pendência, próxima
consulta, autorização individual e motivos de rejeição foram preservadas.

A aba **Histórico** deixou de concatenar valores técnicos em uma linha. Cada
tentativa já carregada é apresentada em cartão com situação de transmissão,
recibo, retorno do Portal, data/hora e, quando o resultado persistido carregado
pertence à tentativa, resultado individual. Campos ausentes são omitidos de
forma coerente e a situação sem valor recebe `Não informado`. SOAP, XML, URLs,
credenciais e outros dados sensíveis não são exibidos.

## Limites

Não houve schema, migration, alteração em GNRE001–GNRE009, XML, snapshots,
transmissão, recepção, fila, consulta, PDF, integração com Portal ou guia real.
Nenhuma consulta ao Portal é feita para compor o Histórico.

## Cobertura

`tests/task55-process-history-presentation-static.php` cobre textos/contratos
públicos para guia pendente, finalizada/autorizada, finalizada/rejeitada,
tentativa recebida com recibo e ausência de campo opcional.
