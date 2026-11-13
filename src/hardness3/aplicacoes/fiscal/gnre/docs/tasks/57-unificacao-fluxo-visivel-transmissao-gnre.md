# Task 57 — unificação do fluxo visível de transmissão GNRE

Implementada em 04/11/2026. **Transmitir GNRE**, no cabeçalho da guia, é o
único caminho visível que inicia ou retoma operações com o Portal: validação
local, prévia, confirmação, transmissão, acompanhamento pela fila e PDF.

A aba **Processar** agora é de conferência local: mantém **Gerar e validar
XML**, erros locais, Visualizar XML e Copiar XML. XML válido informa “XML
validado com sucesso.” e orienta usar Transmitir GNRE. Não exibe formulários
manuais de transmissão, consulta ou PDF. Em pendência, apresenta somente a
situação pública e o próximo horário permitido, com orientação para retomar o
acompanhamento guiado. Sem JavaScript, o cabeçalho preserva um caminho de
validação local, prévia resumida e confirmação explícita antes da transmissão.
O Histórico continua sendo a fonte de detalhes
auditáveis.

Guias autorizadas continuam dependentes exclusivamente de `situacaoGuia=0`
individual persistido. Se ainda falta PDF, a única ação no cabeçalho é **Obter
PDF**, que abre o mesmo fluxo guiado; com PDF existente há somente download.
Rejeições preservam motivos e **Corrigir e reenviar**, sem controles
concorrentes.

Os endpoints e serviços existentes foram preservados por compatibilidade. Não
houve chamada ao Portal, transmissão, consulta, obtenção de PDF, alteração de
banco, schema, migrations, entidades GNRE, snapshots, XML persistido, fila ou
guia real.
