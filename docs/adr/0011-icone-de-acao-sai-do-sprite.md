---
status: accepted
---

# O ícone de ação sai do sprite

Os ícones de ação do miolo (buscar, fechar, vazio, mais, ajuda, atualizar, histórico) trocam o desenho do sprite do ThemeRoller pelo traço do Lucide, o mesmo da sidebar. A folha do tema troca só a máscara: a classe `ui-icon-*` e o HTML do Hardness ficam iguais, e a cor continua token. O restante do sprite continua. Revisa o [ADR 0009](0009-tokens-nascem-no-theme.md) no ponto em que o sprite era o único desenho.

## Considered Options

- Manter o sprite inteiro. Ao lado da sidebar e do estado vazio no Lucide, o filtro e a grade ficavam com dois desenhos.
- Trocar o sprite inteiro. São mais de 170 ícones, a maioria fora das telas de todo dia.
- Reescrever o botão com SVG no HTML. O HTML é do Hardness, e a publicação o reenvia.

## Consequences

- Um ícone a mais entra na lista da folha do tema com o traço em `--fx-glifo`. O `mask-image` com `!important` vence a posição do sprite.
- O botão que é o próprio ícone ("Exibir filtros", com a classe do sprite no `<button>`) leva o traço num `::before`.
- O Lucide é ISC.
