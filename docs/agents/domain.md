# Documentação de domínio

Como os skills de engenharia consomem a documentação de domínio deste repositório ao explorar o código.

## Antes de explorar, leia

- **`GLOSSARY.md`** na raiz do repositório, ou
- **`GLOSSARY-MAP.md`** na raiz, se existir: aponta um `GLOSSARY.md` por contexto. Leia cada um que tocar o assunto.
- **`docs/adr/`**: leia os ADRs que tocam a área em que você vai trabalhar. Em repositórios de vários contextos, olhe também `src/<contexto>/docs/adr/` para decisões daquele contexto.

Se algum desses arquivos não existir, **siga em silêncio**. Não comente a ausência e não proponha criá-los de antemão. O skill `/domain-modeling` (alcançado via `/grill-with-docs` e `/improve-codebase-architecture`) cria esses arquivos no momento em que um termo ou uma decisão de fato se resolve.

## Estrutura de arquivos

Repositório de contexto único (a maioria, inclusive este):

```
/
├── GLOSSARY.md
├── docs/adr/
│   ├── 0001-event-sourced-orders.md
│   └── 0002-postgres-for-write-model.md
└── src/
```

O exemplo acima é formato de layout. Não crie esses arquivos até existir um termo ou uma decisão para registrar.

Repositório de vários contextos (existe `GLOSSARY-MAP.md` na raiz):

```
/
├── GLOSSARY-MAP.md
├── docs/adr/                          ← decisões do sistema todo
└── src/
    ├── ordering/
    │   ├── GLOSSARY.md
    │   └── docs/adr/                  ← decisões daquele contexto
    └── billing/
        ├── GLOSSARY.md
        └── docs/adr/
```

## Use o vocabulário do glossário

Quando a saída nomear um conceito de domínio (título de issue, proposta de refactor, hipótese, nome de teste), use o termo como `GLOSSARY.md` o define. Não escorregue para um sinônimo que o glossário evita de propósito.

Se o conceito ainda não está no glossário, isso é um sinal: ou você está inventando uma língua que o projeto não usa (reconsidere), ou há uma lacuna real (anote para o `/domain-modeling`).

## Sinalize conflito com ADR

Se a saída contradiz um ADR existente, diga isso de forma explícita em vez de passar por cima:

> _Contradiz o ADR-0007 (pedidos event-sourced), mas vale reabrir porque…_
