# Task 35 — Modal de item e emitente

O formulário de item GNRE passou a abrir em um diálogo interno quando há JavaScript. Os links continuam apontando para a mesma rota de edição/inclusão, portanto sem JavaScript o formulário normal permanece disponível. O POST, `GuideItemTransaction`, `GuideItemEditPolicy`, validação e hidratação continuam os existentes e usam exclusivamente o snapshot vinculado `GNRE007`.

O diálogo inclui salvar, cancelar e fechar. Cancelar/fechar apenas retorna ao resumo e não envia POST. Erros retornam ao mesmo diálogo com os valores pendentes preservados. Após salvar, o redirecionamento existente retorna ao resumo/lista persistida.

O emitente mostra somente CNPJ, razão social, UF e cidade. O código XML/IBGE e o identificador ERP D020 continuam subjacentes para validação e XML, sem exposição operacional.

Não houve alteração de banco, schema, migration, snapshot, XML persistido, Portal, transmissão, consulta, fila, PDF ou guias reais.

Roteiro manual, sem transmitir:

1. Em uma guia rascunho com snapshot válido, abra **Adicionar item** e confira o diálogo em desktop e mobile.
2. Preencha parcialmente, cancele e confirme que não houve gravação.
3. Salve item compatível, reabra-o e confira documento, referência, valores, adicionais e dados persistidos.
4. Force erro de validação e confirme que os valores continuam no diálogo.
5. Confira o emitente: CNPJ, razão social, UF e cidade visíveis; código XML/D020 não visível.

## Ajuste de cabeçalho e abas

O cabeçalho operacional e as abas da guia agora permanecem no fluxo normal abaixo da barra global fixa. Foram removidos os comportamentos `sticky` independentes que podiam reposicionar cabeçalho e abas de forma diferente entre Dados e itens, Processar e Histórico. Apenas o conteúdo da aba varia; URLs, ações e dados não foram alterados.

A causa da variação era a margem automática da área de trabalho dentro do grid, que a centralizava verticalmente conforme a altura da aba. A margem passou a ser somente horizontal (`margin: 0 auto`), mantendo Dados e itens, Processar e Histórico na mesma posição abaixo da barra global fixa. A rota de modal não é afetada.

Roteiro adicional: abrir a mesma guia nas três abas, comparar a posição do cabeçalho e das abas em desktop, tablet e mobile, e confirmar que modal de item e botões existentes continuam funcionando sem transmitir.
