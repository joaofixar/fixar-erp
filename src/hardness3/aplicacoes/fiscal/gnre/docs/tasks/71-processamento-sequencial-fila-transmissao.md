# Task 71 — Processamento sequencial automático na Fila de transmissão

Implementada em 05/11/2026. A Fila de transmissão agora é um controlador visual
assistido: depois de uma única confirmação, processa cada guia individualmente
na ordem selecionada e apresenta progresso, etapa atual e resumo público.

O controlador usa exclusivamente os POSTs e serviços do fluxo individual para
validar XML, transmitir, consultar o resultado e obter o PDF. Não há lote GNRE
real: cada guia mantém sua própria tentativa `GNRE004`, recibo, fila `GNRE005`,
resultado e PDF `GNRE008`.

Guias pendentes preservam o horário de consulta persistido. Enquanto aguardam,
o navegador inicia somente as demais guias ainda não iniciadas; depois retorna
à pendente quando seu horário for alcançado. A leitura desse horário é limitada
à tentativa/recibo mais recente da própria guia. Fechar o modal ou interromper
encerra apenas o ciclo no navegador e não cria novas operações.

Não foram alterados schema, migration, SQL de negócio, XML, clientes do Portal,
worker, locks, leases, regras de reagendamento, auditoria ou guias reais.

Correção posterior: o resultado final exibido pela Fila é classificado apenas
pela evidência individual persistida da tentativa/recibo mais recente. Somente
`situacaoGuia=0` autoriza; `1` e `2` rejeitam com o motivo público persistido;
resultado final sem uma única evidência inequívoca é inconclusivo. O PDF só é
informado como obtido quando a chamada idempotente feita pela própria Fila tem
sucesso; a disponibilidade manual do PDF na guia não é usada como evidência.

## Task 72 — resumo final e impressão dos PDFs autorizados

Ao encerrar o ciclo, o modal substitui a cronologia intermediária por um resumo
consolidado por estado final: autorizadas, rejeitadas, pendentes, resultado
inconclusivo, bloqueadas, falhas técnicas e, quando a operação é interrompida,
não iniciadas. Uma guia que esteve pendente e depois foi autorizada aparece
somente entre as autorizadas. O resumo não possui links individuais; os
detalhes e ações normais permanecem na página e no Histórico da própria guia.

Quando a etapa idempotente da própria Fila retorna um `document_id` de PDF
oficial persistido, ele passa a ser elegível à única impressão consolidada da
Fila. O PDF oficial individual continua disponível somente na página da própria
guia.

## Complemento da Task 72 — impressão consolidada e espera visual

O resumo final passou a oferecer somente uma ação de **Impressão das guias
autorizadas**. Ela combina, em ordem, exclusivamente os PDFs oficiais
individuais que a própria Fila persistiu e identificou neste ciclo; não os
renomeia como PDF oficial e não abre um arquivo por guia. A implementação usa
o mesmo motor FPDI que sustenta a concatenação de DANFEs da VEN003, diretamente
para evitar o wrapper legado dependente do diretório de execução. Cada
documento é relido com escopo de empresa e guia antes da composição. Se um não
puder ser lido, a impressão falha de modo atômico com a guia identificada.

Somente no modal da Fila, em Homologação e Produção, a primeira nova tentativa
visual de acompanhar uma pendência pode ocorrer após dois segundos. Ela continua
a passar pelo claim normal: se o servidor ainda apontar
`GNRE005_Proxima_Tentativa`, o navegador volta a esperar esse horário. O fluxo
individual conserva os cinco segundos persistidos, sem qualquer alteração em
GNRE005.
