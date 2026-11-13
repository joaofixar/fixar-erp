# Task 21 — Correção da seleção de municípios via D018/D020

## Decisão e escopo

Esta task substitui o plano da Task 20 de criar catálogo GNRE próprio. Não cria
tabela, não importa IBGE externo, não altera schema nem `GNRE007`, e não toca
as guias #8, #9 ou #13, Portal, transmissão, `GNRE004`, `GNRE005` ou estados.

A evidência direta em `e998` confirmou que `D020` já contém o IBGE de sete
dígitos e que `D020_UF` pode estar vazio:

| UF em D018 | cidade | IBGE | D020_D018_Id |
| --- | --- | --- | ---: |
| CE | Fortaleza | 2304400 | 10 |
| CE | Fortim | 2304459 | 10 |
| PR | Curitiba | 4106902 | 21 |

A relação confiável é `D020.D020_D018_Id → D018.D018_Id`, com UF em
`D018.D018_UF`. A consulta usa `D020 INNER JOIN D018`; `D020_UF` não é critério
de seleção.

## Implementação

- A lista de UFs e de cidades vem do join D020 → D018; a interface filtra por
  `D018_UF` e conserva o IBGE-7 como valor da cidade.
- `GNRE003_Destinatario_Codigo_Municipio` existente é `char(5)`. Sem migration,
  o IBGE-7 é persistido no envelope JSON já existente de
  `GNRE003_Campos_Extras_JSON`; o campo legado recebe apenas a projeção XML.
  Os campos adicionais oficiais continuam no mesmo envelope e são emitidos
  normalmente.
- O XML deriva os últimos cinco caracteres: `4106902 → 06902`,
  `2304400 → 04400` e `2304459 → 04459`. Nunca usa o prefixo de cinco.
- Ao reabrir, a UF/cidade é reconstruída pelo IBGE-7 e D020 → D018. Um registro
  sem IBGE-7, ou com IBGE ausente em D020/D018, recebe aviso de compatibilidade
  e a pré-validação bloqueia geração/transmissão até escolha explícita.

## Testes

- Sintético municipal: CE retorna Fortaleza e Fortim distintos, PR retorna
  Curitiba, `D020_UF` vazio é ignorado e as três derivações são verificadas.
- Regressão do formulário Task 18: UI usa IBGE-7 na reabertura.
- Regressão do gerador Task 14: XML contém `04400` a partir do IBGE persistido.
- Lint, `git diff --check` e suíte no `hardness-php7` registrados no handoff.

## Handoff

Arquivos funcionais: `src/Municipality.php`, `src/SemanticValidator.php`,
`src/XmlGenerator.php`, `src/Task18ProgressiveController.php` e testes das
Tasks 14/18/19. Não houve SQL de escrita, migration, alteração de schema,
consulta/transmissão ao Portal ou mudança de guia.
