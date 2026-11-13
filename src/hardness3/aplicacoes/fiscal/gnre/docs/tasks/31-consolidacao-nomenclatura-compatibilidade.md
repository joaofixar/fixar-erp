# Task 31 — Consolidação de nomenclatura e compatibilidade interna

Concluída em 03/11/2026 como refatoração interna, sem alteração de regras fiscais,
interface, banco, XML, Portal ou guias.

## Mapeamento

Antes da alteração, a busca integral separou:

- código executável ativo: `Task18ProgressiveController`, `Task18ItemTransaction`,
  `Task18ReopenPolicy`, `task18-flow.php`, `task18-readonly-items.php`,
  `task18-ui.js` e `task26-item-hydration.js`;
- compatibilidade legada congelada: `GuidedDraftController` e arquivos `task15-*`;
- testes e documentos históricos: mantidos pelos seus nomes de entrega, sem renomeação
  estética.

## Nomes canônicos

| Anterior | Canônico | Responsabilidade |
| --- | --- | --- |
| `Task18ProgressiveController` | `GuideDraftController` | criação/edição progressiva baseada no snapshot |
| `Task18ItemTransaction` | `GuideItemTransaction` | limite transacional da persistência de item |
| `Task18ReopenPolicy` | `GuideItemEditPolicy` | decisão de inclusão, resumo e edição por tipo |
| `task18-flow.php` | `guide-draft-flow.php` | fluxo/renderização da guia em rascunho |
| `task18-readonly-items.php` | `guide-readonly-items.php` | apresentação somente leitura |
| `task18-ui.js` | `guide-form.js` | formulário progressivo da guia |
| `task26-item-hydration.js` | `guide-item-hydration.js` | hidratação de valores persistidos |

Os contratos emitidos pelo fluxo ativo agora são `guide_draft_*`,
`data-guide-draft-*` e `window.gnreGuideDraftDefinition`.

## Compatibilidade temporária

`src/LegacyTask18Compatibility.php` é o único adaptador de Task 18 no caminho
ativo. Ele converte POSTs cacheados `task18_*` e as chaves transitórias de sessão
para os contratos canônicos antes do despacho. Não há adaptador para Task 15:
ela permanece como fluxo legado isolado e congelado, conforme a revisão técnica.

Remover o adaptador após a janela de cache/atalhos internos, quando a busca de
consumidores não encontrar POSTs `task18_*` fora da documentação e dos testes
históricos.

## Arquivos alterados

Foram renomeados os componentes ativos acima, atualizados `public/index.php` e
`public/task10-presentation.php`, e criado
`tests/task31-naming-compatibility-static.php`.

## Validações

- `php -l` passou nos arquivos PHP canônicos, adaptador, entrada e views.
- `node --check` passou em `guide-form.js` e `guide-item-hydration.js`.
- O teste estático Task 31 passou localmente e no `hardness-php7`.
- As asserções históricas afetadas foram migradas para os contratos canônicos;
  fixtures históricas preservam seus nomes para rastreabilidade. A asserção
  funcional preexistente de lista oficial vazia da Task 26.3 segue falhando e
  não foi alterada nesta consolidação de nomenclatura.

## Próxima etapa

Migrar gradualmente os nomes dos testes para capacidades e, após a janela de
compatibilidade, remover `LegacyTask18Compatibility.php`. O
`public/index.php` não foi decomposto nesta task.
