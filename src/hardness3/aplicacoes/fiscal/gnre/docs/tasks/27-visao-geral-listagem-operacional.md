# Task 27 — Visão geral e listagem operacional de GNRE

**Situação:** concluída documentalmente em 02/11/2026. Nenhuma implementação funcional foi realizada; a Task 26.5 continua fora deste escopo e em sua própria validação visual.

## Escopo e decisão

Esta especificação separa os dois destinos já presentes no shell GNRE, sempre no contexto da empresa GNRE ativa:

- **Visão geral:** painel operacional, curto e priorizado por pendências.
- **Guias:** central de consulta e operação, com pesquisa, filtros e a listagem integral.

Não cria tabela, migration, campo, rota, chamada ao Portal, SOAP, consulta externa nem altera guia, CRUD, XML, PDF, transmissão, banco, regras fiscais, sidebar, shell ou a página individual. As URLs públicas atuais devem ser preservadas; filtros novos, quando implementados, devem permanecer na query string para permitir voltar, compartilhar e recarregar a mesma consulta.

## 1. Diagnóstico da redundância atual

O carregador busca todas as `GNRE001` da empresa ativa, calcula `gnre_business_state`, aplica os poucos filtros existentes e entrega a mesma variável `$filtered` ao renderer. O renderer, quando não há guia aberta, sempre apresenta a mesma tabela mínima (`#`, situação, tipo e Abrir), tanto na rota padrão de Visão geral como em `?view=list`. Portanto, a diferença hoje é somente o item ativo/título da sidebar, e não a função da tela.

Há ainda dois limites que a listagem futura deve respeitar:

- `GNRE001_Status='F'` significa somente que a consulta técnica foi finalizada; não significa emissão. A emissão exige resultado individual persistido e interpretado com `situacaoGuia=0`.
- O resultado individual vive no SOAP de `GNRE005_Ultima_Consulta_SOAP`; não há coluna normalizada de situação individual, número de controle ou data de movimentação consolidada.

## 2. Jornada e navegação propostas

1. Ao entrar em **Visão geral**, o usuário vê a empresa ativa, contadores acionáveis e a fila de pendências. A ação prioritária leva diretamente à guia ou à central de Guias já filtrada.
2. Ao precisar localizar, comparar ou tratar muitas guias, seleciona **Guias**. A central conserva os filtros na URL e mostra o conjunto completo paginado.
3. A linha/ação abre a página individual já existente; esta task não altera suas abas. Ao voltar, o navegador retorna à consulta preservada.
4. Toda consulta restringe obrigatoriamente `GNRE001_C004_Id` à empresa GNRE ativa. União com `GNRE004`, `GNRE005` ou `GNRE008` nunca pode retirar essa condição.

Padrão visual: reaproveitar sidebar, barra fixa, card da empresa, cards claros, badges com texto e ícones SVG inline já adotados pela GNRE e inspirados no `dashboard-personalizado`. Visão geral usa grade de indicadores + listas curtas; Guias usa cabeçalho de contexto, filtros compactos e tabela responsiva. Cor nunca será o único significado; badges devem trazer rótulo textual. Em celular, filtros empilham, indicadores passam a uma coluna e a tabela oferece rolagem horizontal identificável ou cards equivalentes.

Exemplos de contratos de URL futuros, sem alterar as rotas atuais nesta task:

```text
/gnre?status=RASCUNHO
/gnre?pending=validar_xml
/gnre?due=overdue
/gnre?view=list&status=REJEITADA&uf=BA&due_from=2026-11-01&due_to=2026-11-30
/gnre?view=list&receipt=26000000045967&environment=2
```

Parâmetros vazios, inválidos ou não autorizados devem ser ignorados/normalizados no servidor; valores de filtro devem ser bindados, nunca concatenados em SQL. A nomenclatura final deve ser uma só: `status` representa situação de negócio, e um eventual `technical_status` fica separado.

## 3. Wireframes textuais

### Visão geral

```text
[sidebar GNRE]                         [Visão geral] [empresa ativa: seletor]

Visão geral da empresa <nome>                         [+ Nova guia]
Resumo operacional e pendências que exigem ação

[ Rascunhos       4 ] [ Aguardando resultado  2 ] [ Emitidas       18 ]
[ Rejeitadas      3 ] [ Inconclusivas          1 ] [ Vencem em 7d    5 ]
 cada card é link para Guias com o filtro correspondente

Pendências prioritárias                                      [Ver todas]
| Guia | Situação | Motivo/estado útil            | Venc. | Ação          |
| #42  | Rascunho | XML ainda não validado         | 05/11 | Validar XML    |
| #39  | Aguarda  | Recibo 260...; consulta devida| 03/11 | Consultar      |
| #36  | Rejeitada| resultado individual disponível| 01/11 | Corrigir       |

Últimas guias movimentadas                                   [Abrir Guias]
| Guia | Situação | UF | Receita | Vencimento | Valor | Abrir |

Vencimentos
| Vencidas: <lista curta> | Próximos 7 dias: <lista curta> |
```

Se não houver registros, mostrar estado vazio com **Nova guia** e **Abrir Guias**, sem inventar totais ou pendências.

### Guias

```text
[sidebar GNRE]                              [Guias] [empresa ativa: seletor]

Guias                                                        [+ Nova guia]
Consulta e operação de todas as guias da empresa ativa

[Situação de negócio v] [Situação técnica v] [UF v] [Receita v]
[Vencimento de __/__/__ até __/__/__] [Nº guia] [Recibo] [Controle]
[Ambiente v] [Valor mín.] [Valor máx.]                 [Limpar] [Filtrar]
Filtros ativos: [Rejeitada ×] [BA ×]

N guias encontradas · ordenação padrão: atividade mais recente, depois ID desc
| Guia | Situação | UF | Receita(s) | Venc. | Valor | Recibo/controle | Atividade | Ação |
| #... | badge    | .. | 1000 / +2  | ...   | ...   | 260... / —      | ...       | Abrir |
```

Em telas pequenas, o card da guia mostra primeiro número, badge e ação; depois UF/receita, vencimento/valor e identificadores. Paginação é obrigatória antes de a consulta ser exposta a grande volume; a ordenação por “atividade” somente entra quando sua semântica for definida conforme a seção 8.

## 4. Indicadores, fonte e limites

| Indicador/atalho | Fonte atual possível | Regra confiável e destino | Limitação |
| --- | --- | --- | --- |
| Rascunhos | `GNRE001.Status='R'` | Contagem; abre `view=list&status=RASCUNHO`. | Não afirma que XML foi validado: essa validação atual é temporária de sessão. |
| Aguardando processamento/resultado | `GNRE001.Status='P'`, tentativa `GNRE004.RECEBIDA` e fila `GNRE005` | Contagem `P`; atalho para `status=AGUARDANDO`. Linha pode informar recibo e situação da fila. | Não confundir fila processando/reagendada com aprovação. |
| Emitidas/autorizadas | SOAP final já persistido em `GNRE005`, interpretado como exatamente uma guia com `situacaoGuia=0` | Contagem/link `status=EMITIDA`; é a única regra que permite este rótulo. | Sem normalização, exige interpretação do SOAP e cuidado com lotes/resultado ausente. |
| Rejeitadas | SOAP final interpretado com `situacaoGuia=1` ou `2` | Contagem/link `status=REJEITADA`; ação de correção quando houver política aplicável. | Rejeição de recepção sem resultado individual é outra situação, não deve entrar automaticamente aqui. |
| Inconclusivas | `GNRE001.Status='I'`; também resultado individual `3`, resultado ausente/desconhecido após finalização e recepção rejeitada, conforme a política aprovada | Contagem/link explícito por subestado, se necessário. | A tela atual simplifica parte desses casos; a implementação deve usar uma política única, não rótulos concorrentes. |
| Financeiro por situação | `GNRE001_Valor` por situação de negócio | Exibir apenas como **valor informado da guia**, somado por estado e empresa, com link ao filtro. | Para tipos múltiplos, é campo legado/de compatibilidade e não prova total fiscal/calculado; ocultar se a análise de paridade por tipo não for aprovada. |
| Vencidas e próximas | `GNRE001_Data_Vencimento`, referência temporal do servidor | “Vencida” < hoje e não emitida; “próximos 7 dias” de hoje a hoje+7, em dias de calendário; links `due=overdue`/intervalo. | Vencimento não confirma pagamento; para itens múltiplos há `GNRE003_Data_Vencimento`, sem regra atual que o consolide sobre o cabeçalho. |
| Últimas movimentadas | Criação/alteração de `GNRE001`, criação/envio de `GNRE004`, última consulta de `GNRE005` e obtenção de `GNRE008` | Inicialmente, lista por criação/alteração da guia, explicitamente rotulada “Criadas/alteradas recentemente”. | Não existe `ultima_movimentacao` única; uma linha cronológica fiel exige read model/consulta derivada definida e indexada. |
| Pendências acionáveis | fatos acima + política de estados | Validar XML (rascunho), transmitir (somente validação local atual), consultar recibo/resultado (`P`/fila elegível), corrigir (rejeição individual), obter PDF (aprovação individual sem `GNRE008`). | “Validar XML” e “Transmitir” dependem de fato temporário de sessão hoje; dashboard não deve prometer disponibilidade persistente. PDF só após `situacaoGuia=0`. |

Não há indicador de pagamento, quitação, linha digitável, QR Code ou PIX: essas informações não são confirmação de pagamento e não foram persistidas como dado operacional desta tarefa.

## 5. Filtros e colunas de Guias

### Filtros

| Filtro | Fonte/viabilidade | Decisão proposta |
| --- | --- | --- |
| Situação de negócio | Derivada de `GNRE001` + SOAP de `GNRE005` | Sim: Rascunho, Aguardando resultado, Emitida, Rejeitada, Inconclusiva; incluir “Recepção rejeitada” como subestado técnico/operacional distinto. |
| Situação técnica | `GNRE001_Status`, última `GNRE004_Estado`, `GNRE005_Situacao` | Sim, filtro avançado e separado: R/C/P/I/F, CRIADA/RECEBIDA/REJEITADA/INCONCLUSIVA, PENDENTE/PROCESSANDO/FINALIZADA/BLOQUEADA. `F` não recebe rótulo Emitida. |
| UF favorecida | `GNRE001_UF_Favorecida` | Sim, seleção por valores existentes na empresa/consulta. |
| Receita | Cabeçalho `GNRE001_Codigo_Receita` e itens `GNRE003_Codigo_Receita` | Sim, com decisão explícita de semântica: “receita principal/cabeçalho” no MVP; filtro por qualquer receita de item é posterior e exige `EXISTS`, índice e texto claro. |
| Período de vencimento | `GNRE001_Data_Vencimento` | Sim, intervalo inclusivo. Não usar vencimento de item até haver regra de consolidação. |
| Número da guia | `GNRE001_Id` | Sim, igualdade exata; não é número oficial do Portal. |
| Recibo | `GNRE004_Recibo` / `GNRE005_Recibo` | Sim, igualdade ou prefixo limitado, sempre empresa-escopado. |
| Número de controle | Disponível somente dentro do SOAP/PDF; não há coluna | Não implementar como filtro agora. Exige persistência normalizada ou índice de busca aprovado; não fazer `LIKE` amplo em SOAP. |
| Ambiente | `GNRE007_Ambiente` para guias com snapshot | Sim, quando houver snapshot; guias legadas ficam “sem snapshot/ambiente não determinado”. Não assumir ambiente pelo estado. |
| Faixa de valor | `GNRE001_Valor` | Opcional, com mínimo/máximo decimal validados. Só habilitar junto de paginação e plano/índice medido; não aplicar a total fiscal dos tipos múltiplos. |

### Colunas

- **Número da guia:** `GNRE001_Id`, identificado como número interno.
- **Situação:** badge de negócio; tooltip/detalhe curto pode expor o fato técnico decisivo sem substituir o rótulo.
- **UF favorecida:** `GNRE001_UF_Favorecida`.
- **Receita:** código do cabeçalho no MVP; depois, “código + N receitas” derivado de `GNRE003` quando a agregação estiver definida.
- **Vencimento:** cabeçalho, com indicação textual de vencida/próxima quando aplicável.
- **Valor informado:** `GNRE001_Valor`, rotulado para não sugerir soma fiscal confirmada.
- **Recibo/controle:** recibo mais recente aplicável; controle somente depois de persistido/normalizado. Ausência usa “—”.
- **Criação/atividade:** criação agora; alteração só se preenchida. “Última atividade” somente após a definição futura indicada abaixo.
- **Ação contextual:** uma ação principal segura e o link Abrir; opções adicionais em menu acessível, não uma sequência de botões por linha.

## 6. Situação técnica versus situação de negócio

| Fato técnico persistido | Situação exibida | Condição e observação |
| --- | --- | --- |
| `GNRE001.Status='R'` | Rascunho | Pode haver tentativa de recepção rejeitada anterior; a implementação deve apresentar esse histórico em detalhe, sem chamar a guia de emitida. |
| `P` + tentativa `RECEBIDA`/recibo | Aguardando resultado | Consulta pode estar pendente, processando ou reagendada. |
| `F` + SOAP interpretável com `situacaoGuia=0` | Emitida/autorizada | Único critério de emissão. |
| `F` + SOAP interpretável com `situacaoGuia=1` ou `2` | Rejeitada | Nunca emitida; permite fluxo de correção quando já aprovado pela política. |
| `F` + `situacaoGuia=3`, ausente/desconhecida ou SOAP inválido | Inconclusiva | Resultado técnico terminou, mas emissão não foi confirmada. |
| `I`, tentativa `CRIADA` ou `INCONCLUSIVA` | Inconclusiva | Sem reenvio automático. |
| Tentativa `REJEITADA` sem recibo | Recepção rejeitada | Subestado técnico explícito; não é rejeição individual do Portal. |
| `C` | Cancelada | Existe no enum, mas não há fluxo funcional descrito nesta task; exibir somente se houver registros. |

Precedência para um read model único: inacessível/legada bloqueada; resultado individual `0`; resultado individual `1/2`; resultado individual `3` ou tentativa inconclusiva; pendência com recibo; rejeição de recepção; rascunho; demais/inválido como inconclusivo. A política existente da página individual é a referência inicial, mas a central deve receber testes próprios para garantir que todos os seus dados sejam carregados por empresa antes da classificação.

## 7. Ações contextuais por estado

| Estado de negócio/subestado | Ação principal possível | Condição/fonte |
| --- | --- | --- |
| Rascunho | Abrir/editar dados; Validar XML | Guia da empresa; edição segue a página individual. Validar é ação da guia, não ação em massa. |
| Rascunho com XML local válido na sessão atual | Transmitir | Somente se as pré-condições servidoras continuam válidas; o painel não concede autorização nem guarda esse sinal. |
| Aguardando resultado | Consultar resultado | Recibo confirmado e fila/estado elegível; usar a ação já existente da guia. |
| Rejeitada individualmente | Corrigir / abrir rascunho de correção | Só conforme vínculo de correção/política; não retransmitir a original. |
| Recepção rejeitada | Abrir e revisar | Não oferecer “corrigir rejeição” como se existisse resultado individual. |
| Inconclusiva | Abrir histórico | Sem retransmitir/reprocessar automaticamente; ação futura depende de regra autorizada. |
| Emitida sem PDF | Obter PDF oficial | Exige resultado individual `0`; ação explícita existente. |
| Emitida com PDF | Baixar PDF oficial | `GNRE008` da mesma empresa/guia. |
| Qualquer estado acessível | Abrir | Sempre preserva o histórico e aplica autorização novamente. |

## 8. Implementável sem banco e decisões futuras

### Pode ser implementado sem nova persistência

- Separação visual e de consulta entre Visão geral e Guias.
- Contagens de `R`, `P`, `I`, recebimento/rejeição, fila e PDF com joins empresa-escopados.
- Estado de negócio a partir do SOAP já persistido, com interpretação em lote/paginação para evitar N+1.
- Filtros por UF, receita de cabeçalho, vencimento, ID, recibo, ambiente por snapshot e faixa de `GNRE001_Valor` se o plano for aceito.
- Links em query string, paginação, estado vazio, responsividade e ações que somente apontam para os fluxos existentes.
- “Criadas/alteradas recentemente” usando campos de `GNRE001`, com nomenclatura honesta.

### Exige decisão ou persistência futura

- **Número de controle pesquisável:** extrair e persistir o identificador retornado pelo Portal (com origem/tentativa), ou aprovar solução de busca indexada; não vasculhar SOAP/PDF em listagens.
- **Situação individual indexável e performática:** read model/colunas derivadas versionadas, caso a interpretação repetida de SOAP seja cara; a fonte bruta e a auditoria devem continuar preservadas.
- **Última movimentação única:** definir evento e precedência entre criação/alteração, transmissão, consulta e PDF; depois materializar ou consultar com `UNION`/agregação e índices medidos.
- **Receitas resumidas por item e total financeiro confiável:** definir se `GNRE001_Valor` é compatível em todos os tipos e se uma soma de `GNRE003`/`GNRE006` é somente visual ou fiscal; não recalcular nem substituir valores nesta feature.
- **Vencimento por itens:** decidir se, para tipos múltiplos, o cabeçalho segue sendo referência operacional ou se o dashboard deve calcular um conjunto/menor vencimento.
- **Ambiente das guias sem snapshot:** não inferir. Definir migração/read model ou manter “não determinado”.
- **Índices para escala:** medir `EXPLAIN` com volume real antes de adicionar índice para valor, filtros compostos ou read model. Não criar migration nesta task.

## 9. Próximas tasks sugeridas

| Ordem | Task sugerida | Dependências | Critérios de aceite |
| --- | --- | --- | --- |
| 27.1 | Read model de consulta e classificação | Validação desta especificação; Task 26.5 isolada | Uma consulta empresa-escopada, sem N+1, produz situação de negócio e fatos técnicos; casos `F+0`, `F+1/2`, `F+3`, `I`, `P`, recepção rejeitada e legado cobertos por teste. |
| 27.2 | Central Guias: filtros, tabela e paginação | 27.1 | Todos os filtros aprovados preservam query string, são bindados, não cruzam empresas, limpam corretamente e a listagem é responsiva; não há alteração de guia. |
| 27.3 | Visão geral operacional | 27.1 e 27.2 | Cards e pendências usam apenas fatos confiáveis, cada card/linha navega ao filtro/guia correto, estados vazios funcionam e não se afirma emissão por `F`. |
| 27.4 | Desempenho e acessibilidade | 27.2 e 27.3 com volume de teste | Paginação/índices ou plano aprovados por medição; teclado, foco, leitores de tela, contraste e mobile validados. |
| 27.5 (decisão) | Normalização opcional de controle/atividade | Evidência de necessidade e aprovação de modelo | Contrato de novos dados, origem, migração/backfill seguro, índices e regressões; não iniciar sem decisão explícita. |

As Tasks 27.1--27.4 continuam proibidas de alterar Task 26.5, CRUD, transmissão, XML, PDF, Portal e regras fiscais, salvo autorização explícita que amplie o escopo.

## 10. Plano de testes manuais para a implementação

1. Alternar duas empresas GNRE e confirmar que contagens, linhas, recibos, PDFs e URLs nunca mostram dados da outra empresa; tentar abrir ID alheio e confirmar bloqueio.
2. Criar ou preparar dados representativos: rascunho, `P` com recibo, `F` com `situacaoGuia=0`, `F` com `1/2`, `F` com `3`/resultado inválido, `I`, tentativa de recepção rejeitada e guia emitida com/sem `GNRE008`. Conferir cada badge, contador e ação.
3. Confirmar especificamente que `F` sem `situacaoGuia=0` não aparece em Emitidas nem oferece PDF oficial.
4. Em Visão geral, abrir cada indicador e pendência; conferir que o destino é Guias com query string correta ou a guia certa e que Voltar conserva o contexto.
5. Em Guias, combinar situação, UF, receita, vencimento, número interno, recibo, ambiente e valores; conferir limpar filtros, parâmetros inválidos, nenhuma ocorrência e paginação/ordenação.
6. Conferir receita de cabeçalho versus guia de múltiplas receitas: o texto deve indicar a semântica adotada, sem omitir que há itens adicionais.
7. Conferir vencida/próxima em mudança de dia e em guia emitida, rejeitada e pendente; garantir que o texto não indica pagamento.
8. Abrir as ações contextuais e conferir que elas apenas usam as guardas servidoras já existentes: XML/transmissão, consulta, correção e PDF continuam bloqueados quando não elegíveis.
9. Testar desktop, 900 px e 320 px: sidebar/gaveta, filtros, cards/tabela, foco, rolagem horizontal e labels de badge. Usar somente teclado e leitor de tela quando disponível.
10. Com volume representativo, registrar tempo e plano de consultas; assegurar ausência de N+1 e que paginação mantém tempo aceitável antes de considerar índices ou persistência nova.

## Validação documental desta task

Arquivos alterados: este documento e `docs/PROJECT_STATUS.md`. Não há alteração de PHP, JS, CSS, banco, migrations, dados GNRE ou chamadas externas. A validação aplicável é `git diff --check`.
