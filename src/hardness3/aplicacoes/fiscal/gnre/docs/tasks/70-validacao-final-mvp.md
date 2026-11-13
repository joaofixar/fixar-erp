# Task 70 — validação final de liberação do MVP GNRE

Revisão técnica concluída em 05/11/2026, sem transmissão ao Portal, sem
alteração de banco e sem mudança de código de negócio. O MVP está **apto para
liberação controlada em Homologação**, condicionado à execução e ao registro do
roteiro visual/manual abaixo em sessão autorizada. Não foi encontrado bloqueador
automatizado nem correção pequena pré-liberação.

## Escopo confirmado automaticamente

### Fila de transmissão

- A tela permite selecionar uma ou várias guias, marcar/desmarcar todas as
  listadas, filtrar por UF, receita, vencimento, número e ambiente, abrir a
  guia e confirmar antes do processamento.
- O texto público informa que cada guia é validada e transmitida
  individualmente. A confirmação também informa recibo e resultado próprios.
- O navegador processa uma guia por vez. A interrupção somente impede o início
  das próximas; o item já iniciado é concluído e nada persistido é revertido.
- A tela pública da Fila não exibe “lote real”, “coorte”, “preparar lote” ou
  “recibo compartilhado”.
- A elegibilidade exige rascunho, mesma empresa/snapshot/UF, ambiente válido e
  ausência de tentativa, fila, arquivamento e participação em coorte
  `PREPARADO`. Essas condições são conferidas novamente antes de cada etapa.

### Reaproveitamento do fluxo individual

Não há uma segunda implementação de regra fiscal, XML, recepção, recibo,
consulta ou PDF na Fila. Para cada guia, a orquestração chama os mesmos
contratos individuais: geração/validação XML pelo snapshot `GNRE007`,
`gnre_transmit_atomic()`, auditoria `GNRE004`, recepção/recibo, `GNRE005` e
`GnreQueueWorker`, interpretação individual de `situacaoGuia` e
`GnreOfficialPdfService`. A consulta da fila não retransmite; o PDF é pedido
somente após autorização individual.

### Estados persistidos e segurança operacional

- Rascunho incompleto entra apenas para validação local; XML inválido não cria
  tentativa nem aciona Portal.
- Recepção aceita cria tentativa e fila próprias; pendência, timeout ou retorno
  inconclusivo não liberam retransmissão insegura.
- Somente resultado individual persistido com `situacaoGuia=0` identifica guia
  autorizada/emitida. `1` e `2` são rejeições individuais; demais resultados
  não autorizam.
- A reabertura elegível preserva a mesma guia, tentativas, recibos, filas e
  Histórico; resultados ambíguos, pendentes ou inconclusivos são bloqueados.
- O PDF oficial exige associação inequívoca a uma única guia autorizada.

### Geração por NF

Os testes e a inspeção estática confirmaram seleção/conferência de NFs,
idempotência NF + tributo, valores monetários de `T007` e uso de `T008`/`D006`
apenas para elegibilidade. Cobriram ST, DIFAL, FCP, DIFAL+FCP do RJ em uma guia
e FCP-ST na guia ST quando aplicável. O preenchimento assistido usa o snapshot
`GNRE007`; campos sem fonte oficial inequívoca ficam para revisão. A criação
permanece em rascunho e não transmite automaticamente.

## Evidências executadas

No contêiner `hardness-php7`, sem chamadas ao Portal e sem alteração de banco:

- Sintéticos: Tasks 08, 23, 25, 35, 44, 49, 50, 51, 55, 56, 57, 60, 61, 62,
  63, 65, 67, 68 e 69 — todos aprovados.
- `php -l` em todos os PHP de `public/` e `src/` do módulo — aprovado.
- `node --check` em `public/gnre-ui.js` e `public/task63-batch.js` — aprovado.
- `git diff --check` — aprovado na conferência final, inclusive para este
  relatório.

## Roteiro manual obrigatório em Homologação

Não transmitir nenhuma guia de Produção. Use rascunhos descartáveis e registre
capturas da tela/Histórico para cada item aplicável.

1. Em **Guias**, **Nova guia**, **Dados e itens**, modal de item, **Processar**,
   **Histórico**, **Configuração**, **Gerar guia por notas** e **Fila de
   transmissão**, conferir desktop e uma largura menor (por exemplo 768 px):
   sem sobreposição, corte, botão sem estilo ou texto técnico (SOAP, URL, SQL,
   certificado, hash ou exceção); datas e valores em formato brasileiro.
2. Criar ou abrir rascunhos por NF e conferir NF/tributo, ST, DIFAL, FCP,
   composição RJ DIFAL+FCP e FCP-ST. Reabrir a guia e confirmar persistência;
   confirmar que ela continua rascunho e não foi transmitida.
3. Na Fila, testar uma seleção e depois duas ou mais do mesmo ambiente.
   Conferir filtros, limpar, marcar/desmarcar todas, **Abrir**, modal de
   confirmação e a explicação de processamento individual.
4. Com dois rascunhos descartáveis, iniciar a Fila e usar **Interromper itens
   ainda não iniciados** durante o primeiro: confirmar que só os próximos não
   iniciam. Para não acionar Portal nesta validação, este passo deve ser feito
   apenas quando a equipe de Homologação autorizar o ensaio de transmissão.
5. Conferir, com dados já persistidos ou ensaio autorizado, os estados:
   recepção aceita/pendente, timeout ou inconclusivo, rejeição individual `1`
   ou `2`, autorização `0`, PDF disponível/pendente e reabertura de rejeitada.
   Confirmar que somente `0` libera PDF/emitida e que a reabertura preserva o
   Histórico.
6. Confirmar que guia arquivada, com tentativa, recibo, fila, PDF ou coorte
   `PREPARADO` não aparece nem é aceita por seleção obsoleta.

## Achados e classificação

| Achado | Classificação | Decisão |
| --- | --- | --- |
| Nenhum defeito automatizado encontrado. | — | Sem ação corretiva nesta task. |
| Evidência visual e de interação autenticada ainda requer execução do roteiro. | Verificação de aceite manual, não defeito | Registrar evidências antes do primeiro uso controlado. |
| Fila sequencial não é lote GNRE real; lote real/Portal único permanece pós-MVP. | Melhoria pós-MVP | Manter fora da interface operacional do MVP. |
| Financeiro automático não integra este MVP. | Melhoria pós-MVP | Manter procedimento financeiro manual após autorização individual. |
| Nomes técnicos `taskNN` continuam no código. | Dívida técnica documentada pós-MVP | Não renomear nesta liberação. |

Não há bloqueador de liberação nem correção pequena pré-liberação identificados.
Após o roteiro manual, o MVP pode ser usado de forma controlada em Homologação.
