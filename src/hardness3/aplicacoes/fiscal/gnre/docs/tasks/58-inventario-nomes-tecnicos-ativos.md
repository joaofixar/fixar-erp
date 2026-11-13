# Task 58 — Inventário de nomes técnicos `taskNN` ativos

## Escopo, método e limite

Análise documental concluída em 04/11/2026. Foram lidos integralmente
`docs/PROJECT_STATUS.md`, a revisão técnica da Task 30 e o backlog pós-MVP.
O inventário foi feito com buscas reproduzíveis abaixo, seguidas de inspeção
dos consumidores encontrados em `public/`, `src/`, `tests/`, `bootstrap/`,
`config/` e `scripts/`:

```sh
rg -n 'task[0-9]' aplicacoes/fiscal/gnre
rg -n 'Task[0-9]' aplicacoes/fiscal/gnre
rg -n 'TASK[0-9]' aplicacoes/fiscal/gnre
```

Não houve alteração de PHP, JavaScript, CSS, rotas, POST, sessão, banco,
schema, XML, snapshots, transmissão, consulta, fila, PDF, Portal ou guias.
Os documentos históricos em `docs/tasks/` não fazem parte de qualquer futura
renomeação de arquivo.

## Resultado executivo

O fluxo atual já usa `GuideDraftController`, `GuideItemTransaction`,
`GuideItemEditPolicy`, `guide-draft-flow.php`, `guide-form.js` e
`guide-item-hydration.js`. Ainda há nomes técnicos de task em cinco contratos
carregados pela interface (`task09`, `task10`, `task28`, `task34` e `task38`),
em duas faixas de compatibilidade (`task15` e `task18`) e em flashes isolados
(`task23` e `task25`). Nenhuma ocorrência encontrada em `bootstrap/`,
`config/` ou `scripts/` contém `taskNN`.

`task15_*` é o único prefixo `taskNN_*` ainda despachado diretamente pelo
servidor; no controlador vigente, qualquer action com esse prefixo recebe
bloqueio controlado antes das ramificações históricas. `task18_*` ainda é aceito
por adaptador antes do despacho do fluxo canônico. A interface atual não emite
esses POSTs: o primeiro é bloqueado e o segundo é traduzido para
`guide_draft_*`.

## Matriz de contratos ativos

| Nome/arquivo atual | Classificação e responsabilidade | Consumidores atuais | Nome lógico sugerido | Risco, pré-requisito e teste mínimo |
| --- | --- | --- | --- | --- |
| `public/task10-presentation.php`, `task10-layout.css` | **Ativo na interface atual.** Shell HTML, apresentação, abas e inclusão da Central/configuração. | `index.php` faz `require`; HTML referencia o CSS; testes estáticos de apresentação leem ambos. | `gnre-presentation.php`, `gnre-shell.css`. | **Alto.** É o template principal e caminho público de CSS. Antes, mapear cache/browser e todos os testes literais. Smoke de `?view=list`, `?view=new`, `?view=config`, `?edit=`, abas, PDF e responsividade. |
| `public/task28-guide-center.php`; função `task28_render_guide_center` | **Ativo na interface atual.** Renderiza a Central de Guias. | Incluído por `task10-presentation.php`; testes 28, 47 e 53 chamam a função. | `guide-center-view.php`; `gnre_render_guide_center`. | **Médio.** É include PHP e função global usada em testes. Renomear arquivo e função no mesmo pacote, após caracterizar filtros/listagem. Testes de filtros, arquivo de guia e colunas. |
| `public/task34-transmission-modal.js`, `task34-modal-state.php`, `task34-cycle-context.php`; globais `GnreTask34*` | **Ativo na interface atual.** Modal guiado e endpoints AJAX de transmissão; `gnre-ui.js` injeta o script. | Browser, URLs relativas do próprio JS, `?gnre34=1`, testes 34, 40, 52 e 57. | `guided-transmission.js`, `guided-transmission-state.php`, `guided-transmission-cycle.php`; `GnreGuidedTransmission*`. | **Muito alto.** Arquivos públicos, URLs no JS, globals e ciclo transitório. Não renomear antes de testar retomar/fechar modal, validação, confirmação, consulta, PDF e falhas sem retransmissão. |
| `public/task38-environment-panel.php` | **Ativo na interface atual.** Painel de preferência de ambiente. | Incluído por `task10-presentation.php` em `?view=config`; teste 38. | `environment-preference-panel.php`. | **Médio.** Include interno e teste literal. Manter o POST `save_environment_preference` sem mudança. Testar GET config e flash por empresa. |
| `public/task09-progressive.js`; global `GnreTask09Progressive`; referência `window.gnreTask09Rules` | **Ativo/carregado, com legado residual.** Script é carregado pelo shell; o bootstrap só ocorre se a variável histórica existir. | Tag `<script>` no shell, guarda em `gnre-ui.js`, teste 10 e teste JS 09. | `legacy-progressive-form.js` enquanto houver consumidor, depois remover; global `GnreProgressiveForm`. | **Médio/risco desconhecido.** Não há produtor encontrado para `gnreTask09Rules` fora de testes, mas o arquivo é carregado em toda página. Antes de remoção, caracterizar páginas com e sem a variável e procurar consumidores externos/cacheados. |
| `public/task10-manual-flow.php`; actions sem prefixo `add_document`, `delete_document`, `save_item`, `delete_item`, `generate_xml` | **Legado/inativo na apresentação atual, mas com contratos e formulários históricos.** É implementação pré-snapshot de CRUD manual; não ser incluído pelo shell atual não prova ausência de consumidor. | Testes Task 10/11 leem o arquivo; `index.php` ainda possui handlers sem prefixo para essas actions; possíveis favoritos, POSTs cacheados ou integrações externas não foram caracterizados. | `legacy-manual-guide-flow.php`, somente durante compatibilidade. | **Médio/alto; não remover, renomear nem alterar agora.** Antes, caracterizar actions emitidas, handlers aceitos, URLs/POSTs externos e estado de sessão associado. Testar CSRF/empresa/guia, resposta controlada de cada action, regressão da criação canônica e inexistência de links emitidos pela UI atual. |
| `src/GuidedDraftController.php`, `public/task15-flow.php`, `task15-ui.js`, `task15-consultation.php`; funções e atributos `task15` | **Compatibilidade transitória.** O controlador é carregado e o servidor reconhece qualquer `action` iniciada por `task15_`, encerrando-a com bloqueio controlado antes do código histórico. | `index.php`; arquivos e testes Task 15; sessão `task15_flash`, `temporary.task15` e `task15_form`; atributos `data-task15-*`. A apresentação atual não inclui esses arquivos de tela. | `LegacyGuideDraftCompatibility` e, só enquanto necessário, prefixo `legacy_guide_draft_*`. | **Alto; bloqueado para renomeação/remoção agora.** Há POSTs cacheados, favoritos ou integrações desconhecidas. Primeiro produzir matriz de cada action reconhecida/bloqueada, pesquisar consumidores fora do módulo e provar que nenhuma view atual emite esses contratos. |
| Actions `task15_restart_new`, `task15_start`, `task15_consult_config`, `task15_select_snapshot`, `task15_create`, `task15_delete_draft`, `task15_save_item`, `task15_add_document`, `task15_delete_document` | **Compatibilidade transitória; contrato HTTP ainda reconhecido pelo servidor.** No estado vigente, todas alcançam o guarda de prefixo e recebem a mensagem controlada para usar o fluxo atual; as ramificações históricas posteriores não são alcançadas. | Roteador de `index.php` e `gnre_task15_handle`. | Não criar alias novo sem evidência; futuro adaptador explícito `legacy_guide_draft_*`. | **Alto.** Executar matriz CSRF/empresa/guia, resultado controlado para todas as actions e prova de que a UI atual só emite `guide_draft_*`. |
| `src/LegacyTask18Compatibility.php`; função `gnre_legacy_task18_adapt_request`; actions `task18_*` | **Compatibilidade transitória.** Converte POST cacheado para `guide_draft_*` antes do controlador ativo. | `index.php`, sessão e teste 31. | `LegacyGuideDraftCompatibility`; `gnre_legacy_guide_draft_adapt_request`. | **Médio/alto; não remover ainda.** Depende de janela deliberada de compatibilidade. Testar tradução de action e migração de sessão sem sobrescrever estado canônico. |
| Sessão `temporary.task18`, `temporary.task18_form`, `task18_flash` | **Compatibilidade transitória.** São movidas para `guide_draft`, `guide_draft_form` e `guide_draft_flash`, depois removidas da sessão. | Adaptador Task 18 e possíveis páginas/POSTs cacheados. | Permanecer apenas como aliases de leitura temporários. | **Médio.** Não renomear isoladamente do adaptador; testar coexistência de estado legado e canônico por empresa, guia e item. |
| Sessão `task15_flash`, `temporary.task15`, `temporary.task15_form` | **Compatibilidade transitória.** Estado e feedback do fluxo legado. | `GuidedDraftController.php`, `task15-flow.php` e testes Task 15. | `legacy_guide_draft_flash` e estado equivalente somente se a compatibilidade for preservada. | **Alto.** Não há autorização para migrar/limpar até encerrar actions e telas legadas juntos. |
| Sessão `task23_flash` | **Ativo, mas somente nome histórico.** Feedback PRG da reabertura de guia rejeitada. | `index.php`; variável de view `$task23Flash`; testes de correção. | `correction_reopen_flash`. | **Baixo/médio.** É contrato interno de sessão, mas deve ser trocado de forma atômica com produtor, consumidor e testes; preservar isolamento empresa/guia e PRG. |
| Sessão `task25_flash` | **Ativo, mas somente nome histórico.** Feedback PRG da obtenção de PDF oficial. | `index.php`; variável de view `$task25Flash`; testes de PDF. | `official_pdf_flash`. | **Baixo/médio.** Troca atômica de produtor/consumidor/testes; testar sucesso e falha de PDF sem chamada adicional. |

## URLs, GET, DOM e duplicidade

- Não foi encontrado parâmetro GET `taskNN`. O parâmetro público ligado ao
  fluxo Task 34 é `gnre34=1`, que pode estar em favorito, histórico ou redirect
  transitório; ele não deve ser renomeado nesta limpeza sem uma estratégia de
  compatibilidade de URL.
- Não foi encontrada action `taskNN_*` emitida pelo shell ativo
  (`guide-draft-flow.php` usa `guide_draft_*`). A action `task15_*` continua
  aceitável pelo servidor como descrito na matriz, e `task18_*` é traduzida.
- Atributos DOM `data-task15-*` estão apenas no fluxo legado não incluído pelo
  shell atual. Os globais `GnreTask09Progressive` e `GnreTask34*` são contratos
  de JavaScript ativos; não devem ser mudados separadamente de seus arquivos e
  testes.
- Os arquivos `task10-presentation.php`, `task10-layout.css`,
  `task09-progressive.js`, `task28-guide-center.php`,
  `task34-transmission-modal.js` e `task38-environment-panel.php` ainda são
  carregados pelo HTML principal ou por include direto. `task15-*` não é
  carregado pelo shell atual, embora o controlador legado seja carregado pelo
  front controller. `task10-manual-flow.php` também não é incluído pelo shell
  atual, mas permanece artefato de formulário legado e não deve ser tratado
  como removível sem caracterização.
- Há três camadas distintas: o fluxo canônico ativo é
  `GuideDraftController`/`guide-draft-flow.php`; Task 15 é compatibilidade de
  POST com bloqueio controlado; e `task10-manual-flow.php` é fluxo manual
  legado não apresentado, cujas actions sem prefixo ainda têm handlers no
  front controller. Não se deve evoluir ou remover nenhuma dessas camadas por
  inferência visual.

## Testes e documentação histórica

Todos os arquivos `docs/tasks/*` são **somente documentação histórica** e
devem manter seus nomes. Em `tests/`, os nomes Task 07, 08, 09, 10, 11, 13–19,
22, 23, 25, 26.2–26.5, 28–31, 33–35, 38, 40, 42, 47, 48, 50, 52, 53 e 55–57,
além das fixtures `task16` e `task18`, são **somente teste/fixture** hoje. A
exceção é que vários fazem asserções literais sobre os arquivos e símbolos da
matriz; por isso devem ser renomeados por último, em paralelo com testes de
comportamento, e nunca junto de regra fiscal ou integração.

## Ordem recomendada de tasks pequenas

1. **Caracterização e isolamento de compatibilidade Task 15.** Inventariar
   cada POST e estado legado, provar ausência de links emitidos pela UI atual e
   estabelecer resposta controlada sem remover nada.
2. **Caracterização do fluxo manual legado.** Mapear
   `task10-manual-flow.php`, as actions sem prefixo e seus handlers, possíveis
   favoritos/POSTs cacheados e consumidores externos; não misturar esta análise
   com regra fiscal, transmissão ou ambiente.
3. **Encerramento mensurável do adaptador Task 18.** Definir janela de cache e
   telemetria/local de evidência permitida; só então remover actions/chaves
   `task18_*` em task exclusiva.
4. **Renomear flashes Task 23 e 25.** Pacote pequeno, interno e atômico, com
   testes de PRG e isolamento.
5. **Renomear a Central Task 28.** Arquivo, função e testes, sem mudar filtros
   ou regra de estado.
6. **Renomear o painel Task 38.** Apenas include e testes, preservando POST e
   sem alterar ambiente.
7. **Caracterizar Task 09 e decidir remover ou renomear.** A ausência de
   produtor local de `gnreTask09Rules` não basta para excluir o script.
8. **Renomear modal Task 34.** Arquivos públicos, URLs, globals e testes no
   mesmo pacote, com aliases de URL se houver consumidor confirmado.
9. **Renomear shell Task 10 e, por último, a suíte.** Fazer depois dos
   includes dependentes; migrar testes para capacidade e manter a documentação
   histórica imutável.

## Não renomear ainda

Não renomear/remover `task15_*`, `task18_*`, `gnre34`, os arquivos/endpoints
Task 34, `task10-presentation.php`/`task10-layout.css`,
`task10-manual-flow.php`, nem `task09-progressive.js` com base apenas nesta
busca. Todos têm consumidor ativo, compatibilidade HTTP, cache/browser, teste
literal ou risco externo ainda não caracterizado.

Validação desta task: somente as buscas `rg` acima e `git diff --check`.
