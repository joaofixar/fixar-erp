# Task 52 — Transmissão GNRE com validação local automática

O botão principal **Transmitir GNRE** abre o modal e inicia imediatamente o POST já existente `generate_xml`. Durante a chamada, o modal informa **“Validando dados e gerando XML…”**. Não foi criado gerador, validador, tentativa ou chamada ao Portal adicional.

Se a validação local falhar, o endpoint de leitura do modal devolve apenas os erros públicos produzidos pelo validador. O ciclo guiado é encerrado, os erros são apresentados e a única ação do modal é **Corrigir dados e itens**. Assim, não há `GNRE004`, recepção no Portal ou retransmissão nesse caso.

Com XML válido, a prévia existente é exibida para qualquer quantidade de itens. A transmissão segue bloqueada até a confirmação explícita, usando o ambiente armazenado no snapshot da própria guia. Depois da confirmação, os POSTs existentes de transmissão, consulta da fila e PDF continuam inalterados.

Cobertura: `tests/task52-auto-local-validation-static.js` exercita fixtures sintéticas de um item, múltiplos documentos e múltiplas receitas, além de falha local e pausa obrigatória antes da transmissão. O teste comportamental da Task 34 também foi ampliado para o encerramento seguro por erro local.

## Correção de retomada

O ciclo guiado agora armazena transitoriamente, por empresa e guia, a aba de origem e o estado ativo. O PRG retorna à mesma aba (`Dados e itens` ou `Processar`) e o modal só retoma quando esse contexto estiver presente. Falta de contexto apresenta uma mensagem pública segura e encerra o ciclo, sem criar tentativa nem chamar o Portal. Fechar o modal, terminar por erro ou chegar a estado terminal remove apenas esse contexto transitório.

## Invalidação após correção

Erros e XML locais permanecem disponíveis enquanto o usuário apenas fecha o modal ou navega para corrigir. Uma gravação de item, documento ou dados da guia que conclui com sucesso invalida o resultado local completo — validade, XML, erros do modal e ciclo guiado — somente depois do commit. Falhas de validação ou de gravação não limpam esse diagnóstico anterior.
