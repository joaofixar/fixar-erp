# Revisão técnica de consolidação do módulo GNRE

Concluída em 03/11/2026. Esta é uma análise estática, sem alteração de
fluxo, banco, snapshots, XML, transmissão, consulta, PDF, Portal ou interface.
O módulo funcional atual é orientado pela guia e pelo snapshot imutável
`GNRE007`; ainda assim, sua nomenclatura e parte do código carregado refletem
as etapas em que foi construído.

## Diagnóstico resumido

`public/index.php` é hoje o front controller, roteador de POST, repositório
de consultas SQL, orquestrador de XML/transmissão/consulta/PDF e preparador de
dados de apresentação. `public/task10-presentation.php` é o layout e também
decide qual fluxo incluir. A edição e criação ativas passam por
`task18-flow.php` e `GnreTask18ProgressiveController`; a camada antiga da
Task 15 ainda é carregada e aceita seus POSTs, porém seus arquivos de tela não
são incluídos pela apresentação atual. Isso caracteriza compatibilidade
temporária não isolada, não um segundo fluxo que deva continuar evoluindo.

Há dois pontos de verdade que devem continuar separados: `GNRE007` para as
regras oficiais vinculadas à guia e `GNRE001`--`GNRE008` para fatos
persistidos/auditoria. Sessão só deve reter estado transitório de tela e XML
local; ela não deve recuperar semântica fiscal nem substituir snapshot.

## Itens encontrados

| Arquivo/classe atual | Responsabilidade atual | Nome lógico recomendado | Decisão | Dependências afetadas | Risco da alteração | Testes necessários |
| --- | --- | --- | --- | --- | --- | --- |
| `public/index.php` e funções globais `gnre_*` | Entrada HTTP, autorização, consultas de guia, roteamento POST, geração XML, transmissão, consulta, PDF e preparação da view. | `GnreApplicationController` mais serviços/repositórios explícitos; manter `index.php` como entrada mínima. | Manter temporariamente; fatiar antes de renomear. | URL pública, `bootstrap/app.php`, todos os formulários/actions, scripts administrativos e testes que leem o arquivo. | Alto: includes manuais, funções globais sem autoload, redirects/headers e ordem de `require_once`. | Smoke HTTP de todas as ações; autorização por empresa; regressões de XML, transmissão, consulta, PDF e sintéticos atuais. |
| `src/GuidedDraftController.php` e funções `gnre_task15_*` | CRUD legado de guia/item/documento, estado em sessão e redirecionamento da antiga interface por tipo. | `LegacyDraftCompatibility` enquanto necessário; depois remover. | Manter temporariamente, congelado; candidatar a remoção após auditoria de ações e sessão. | `index.php` despacha prefixo `task15_`; `task15-flow.php`, `task15-ui.js`, `task15-consultation.php`, testes Task 15 e chaves de sessão `task15_*`. | Alto: embora a view atual não o inclua, POST forjado/URL interna pode atingir ações; remoção pode quebrar rascunhos abertos, favoritos ou testes. | Matriz de ações `task15_*` (aceita/rejeita), busca de referências fora do módulo, teste de inexistência de links/POSTs emitidos pela UI atual e regressão de rascunho legado. |
| `public/task15-flow.php`, `task15-ui.js`, `task15-consultation.php` | Renderização e JS do fluxo substituído, além de fallback de compatibilidade para guia sem configuração. | `legacy-draft-flow` somente durante migração; nenhum nome final novo deve conter task. | Manter temporariamente sem novos recursos; remover junto do controlador legado, não isoladamente. | Variável global `gnreTask15Edit`, actions `task15_*`, sessão e testes Task 15; a consulta de compatibilidade é conceitualmente sobreposta ao fallback do fluxo atual. | Médio/alto: arquivo pode ser incluído por integração local não mapeada por Git; PHP 7 não oferece namespace/autoload que absorva a troca. | Busca integral no repositório e URLs externas conhecidas; comparação visual de guia legada; teste de bloqueio de XML/transmissão sem snapshot. |
| `src/Task18ProgressiveController.php`, `Task18ItemTransaction.php`, `Task18ReopenPolicy.php` | Fluxo ativo de criação/edição por snapshot, persistência transacional de item e política de reabertura por tipo. | `GuideDraftController`, `GuideItemTransaction`, `GuideItemEditPolicy`. | Renomear em task dedicada, depois de encapsular o roteamento. | `index.php`, `task18-flow.php`, sessão/flash `task18_*`, actions POST, testes Task 18/26/29 e JS. | Alto: nomes de classe devem acompanhar nome do arquivo em PHP sem autoload; actions e chaves de sessão são contrato interno e os testes fazem asserções textuais. | CRUD tipo 0/1/2, rollback transacional, flash/pending por empresa-guia-item, reabertura e regressões de valores/municípios. |
| `public/task18-flow.php`, `task18-readonly-items.php`, `task18-ui.js` | View ativa de nova guia/dados, renderização de definição de formulário do snapshot e comportamento DOM. | `guide-draft-flow.php`, `guide-readonly-items.php`, `guide-form.js`. | Renomear, somente em pacote atômico com os consumidores. | Inclusões em `task10-presentation.php`, scripts carregados no HTML, `data-task18-*`, `window.gnreTask18Definition`, CSS, testes JS/PHP. | Alto: caminhos são relativos à URL pública; seletores/data attributes e global JS são contrato entre PHP, JS e testes. | Carregamento direto do browser, DOM JS, formulário/POST, modo somente leitura e rotas `?view=new`/`?edit=`. |
| `public/task10-presentation.php`, `task10-layout.css` | Shell, navegação, layout, inclusão do fluxo e apresentação de resumo/processamento/histórico. | `presentation.php`/`application-shell.css` ou componente equivalente. | Renomear após separar layout de apresentação de guia. | `index.php`, referências em testes Task 08/10/18/23/26, links CSS e inclusões `task18-flow.php`. | Alto: é o único template carregado; testes estáticos e URLs de CSS dependem do nome. | Renderização das três áreas, abas, responsividade, download PDF e testes de shell/acessibilidade. |
| `public/task09-progressive.js`, `public/task10-manual-flow.php` e helpers antigos ainda em `index.php` (`gnre_item_values`, ações sem prefixo) | Implementação pré-snapshot do formulário e CRUD manual. Não são incluídos pela apresentação atual, mas permanecem no diretório e parte das ações ainda existe no front controller. | `legacy-manual-flow` durante inventário; remover quando não houver rota/consumidor. | Remover somente depois de auditoria de compatibilidade; não renomear agora. | Testes Task 09/10/11 e possíveis bookmarks/POSTs antigos; ações `add_document`, `save_item`, `generate_xml` e sessão antiga. | Médio: código aparentemente morto pode ser fallback operacional não documentado; remoção parcial deixaria handlers órfãos. | Inventário de actions emitidas pela UI, teste 404/rejeição controlada de actions aposentadas e regressão da criação ativa. |
| `public/task26-item-hydration.js` | Hidrata no DOM os valores persistidos que o formulário dinâmico não serializa integralmente. | `guide-item-hydration.js`. | Renomear junto do formulário ativo; manter a responsabilidade, mas documentar seu contrato. | `task18-flow.php`, campos de `GNRE003`, extras JSON, `FormDefinitionBuilder`, controlador e testes Task 26.5. | Médio/alto: erro silencioso pode sobrescrever dados persistidos ou enviar campos incompatíveis. | Edição de cada tipo; hydration de documento, pagamento, destinatário, extras e valores; DOM test e envio real de POST. |
| `src/GuideExperiencePolicy.php` e classificação duplicada em `src/GuideCenter.php`/`public/task10-presentation.php` | Política de estado/ações existe como classe, mas a listagem e a apresentação também derivam estado, aprovação e PDF com fatos próprios. | `GuideStateProjection` única, consumida pela listagem e página da guia. | Consolidar antes de novas ações de negócio. | GNRE001/004/005/008, `GuideCenter`, template, correção, PDF e testes Task 26.1/28. | Alto funcional: divergência pode liberar ação indevida ou mostrar “emitida” sem resultado individual aprovado. | Matriz por estado técnico e resultado: R/P/I/F, recepção rejeitada, rejeição individual, aprovado sem PDF, aprovado com PDF, snapshot ausente e empresa diversa. |
| `GnreConfigUfSnapshot`, `gnre_rules`, `gnre_current_rules`, `GnreFormDefinitionBuilder`, `GnreSemanticValidator`, `GnreXmlGenerator` | A mesma regra oficial é carregada/decodificada em helpers globais e classes; builder, validação e XML consomem projeções próximas, mas por caminhos diferentes. | `SnapshotRulesProvider` (leitura/autorização/decodificação) e contrato de regras compartilhado. | Consolidar gradualmente; não alterar formato de `GNRE007` nesta etapa. | Criação, edição, XML, transmissão, correção e views somente leitura. | Alto fiscal: snapshot incorreto, sem posse ou com versão errada compromete validação e XML. | Posse empresa/UF/ambiente, snapshot inexistente/corrompido, guia legada, mesmas regras em tela e XML, e regressões Task 14/16/17/18/22. |
| `GnreQueueWorker`, `GnreResultClient`, `GnreResultInterpreter`, `GnreOfficialPdfService` e trechos em `index.php` | Consulta gera SOAP, worker persiste resultado, intérprete decide aprovação e PDF refaz consulta com `incluirPDFGuias=S`; `index.php` ainda acopla os passos. | `GuideResultService` como orquestrador, preservando clientes/interpretador/PDF especializados. | Manter e consolidar por extração, sem mudar protocolo. | GNRE004/GNRE005/GNRE008, ação `consult_result`, scripts de fila/reparo, tela e central. | Muito alto: idempotência, locks, recibo, SOAP auditável e PDF oficial dependem da sequência exata. | Consulta manual e worker, retry/lock, recepção rejeitada, resultado final 0/não-0, PDF repetido/idempotente, download autorizado e falha de PDF após aprovação. |
| `tests/task*.php`, `tests/task*.js`, fixtures `task16`/`task18` e documentação `docs/tasks/*` | Testes e histórico documentam a ordem de entrega, não o domínio. Muitos testes estáticos dependem de nomes/caminhos literais. | Testes por capacidade (`guide-draft`, `snapshot-rules`, `guide-result`, `official-pdf`) e fixtures sem task. | Renomear por último, após compatibilidade temporária. | CI/manual, documentação de handoff e qualquer automação que execute arquivos pelo nome. | Médio: a mudança pode reduzir rastreabilidade histórica ou esconder regressões se feita junto de alteração funcional. | Executar testes antigos e novos em paralelo por uma task; registrar correspondência antiga→nova e manter documentação histórica imutável. |

## Acoplamentos e compatibilidade a preservar até a consolidação

- O encadeamento correto é: seleção/criação → vínculo de `GNRE007` → edição
  contra as regras do snapshot → validação/geração XML → `GNRE004`/recepção →
  `GNRE005`/consulta e interpretação → `GNRE008`/PDF oficial. Nenhuma
  refatoração deve inferir regras da sessão nem usar `F` isoladamente como
  aprovação.
- Há acoplamento manual de PHP: `index.php` faz `require_once` explícito e os
  arquivos de apresentação fazem `require` relativo. Em PHP 7, renomear classe
  e arquivo exige alterar todos os requires e manter temporariamente um
  adaptador ou realizar a troca atômica.
- URLs não usam roteador: `index.php`, `?view=`, `?edit=`, `?tab=`, `action`
  e valores de POST são contratos. Arquivos JS/CSS são URL relativas e não
  podem ser movidos sem rever o HTML e cache do navegador.
- `task15_*`/`task18_*`, `data-task18-*`, variáveis `window.gnreTask*` e
  `$_SESSION[GNRE_SESSION]` são contratos internos de fato. Devem ganhar nomes
  de domínio apenas com período de compatibilidade deliberado e testes de
  isolamento por empresa, guia e item.
- Os scripts `reparar-*` são compatibilidade operacional/auditoria, não devem
  ser removidos junto de arquivos de interface sem confirmar sua execução e
  finalidade administrativa.

## Documentação e testes que devem acompanhar a consolidação

Atualizar `PROJECT_STATUS.md` com o handoff de cada pacote e o mapa de
compatibilidade enquanto ele existir. Criar um documento arquitetural de
fluxos e contratos de estado, sem reescrever os documentos históricos das
tasks. Substituir testes estáticos baseados em caminhos gradualmente por testes
de comportamento; durante a transição, executar ambos. Cada renomeação precisa
de busca de referências no repositório, lint compatível com PHP 7, teste JS e
roteiro manual das URLs públicas.

## Próximas tasks sugeridas, em risco crescente

1. **Inventário de contratos e cobertura de caracterização**: mapear actions,
   sessão, query strings, caminhos públicos e consumidores de scripts; adicionar
   testes de comportamento sem renomear nada.
2. **Consolidação da projeção de estado da guia**: fazer listagem e página usar
   uma única projeção derivada dos fatos já persistidos; preservar a política
   atual e cobrir a matriz completa de estados.
3. **Extração do provedor de regras do snapshot**: centralizar posse,
   carregamento e decodificação de `GNRE007`, mantendo o formato e os clientes
   fiscais inalterados.
4. **Isolamento do fluxo legado**: provar que nenhuma view atual emite
   `task15_*`/ações manuais, bloquear/registrar compatibilidade de modo
   explícito e retirar somente código comprovadamente sem consumidor.
5. **Fatiamento do front controller**: extrair casos de uso de rascunho,
   transmissão, resultado e PDF com testes de caracterização, sem mudar URLs.
6. **Renomeação atômica da camada ativa**: trocar arquivos, classes, actions,
   sessão, data attributes, globais JS e testes por nomes de domínio; manter
   adaptadores temporários apenas onde houver consumidor confirmado.
7. **Limpeza final de testes e documentação histórica**: migrar a suíte para
   nomes por capacidade e remover adaptadores/arquivos legados após uma janela
   de compatibilidade validada.

## Verificação desta análise

Executar `git diff --check` após registrar este documento e a linha de status.
Não foram realizadas mudanças funcionais.
