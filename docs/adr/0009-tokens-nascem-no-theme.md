---
status: accepted
---

# Os tokens nascem no @theme

Cor, tipo e densidade do desktop nascem no `@theme` do Tailwind do shell, nas variáveis `--tw-…` que o prefixo `tw:` emite. O tema é um só. Uma folha que a publicação não envia entra pelo gancho de `index.tpl`, depois do cache e sem layer, e o miolo lê esses tokens por ela. O sprite do ThemeRoller continua o desenho do ícone. A cor do ícone é token. O passo de densidade é 12, 13 ou 14, e o padrão é 12. A marca entra no `body`.

## Considered Options

- Editar `jqueryui.css` e o `principal.css` no lugar. A publicação reenvia esses arquivos.
- Repetir o hex no shell e no miolo. Os dois lados divergem.
- Variáveis soltas, com o `@theme` só apontando para elas. Duas declarações para o mesmo valor.
- Um conjunto novo de ícones. O miolo já tem o sprite.
- Manter a troca entre `tema-claro.css`, `tema-escuro.css` e `tema-2021.css`. Este esforço tem um tema.

## Consequences

- A utility do shell e a folha do miolo leem a mesma variável. Os valores ficaram no vocabulário Papéis, no [ADR 0010](0010-tema-carrega-o-vocabulario-papeis.md).
- `jqueryui.css`, `principal.css` e o cache `tema_{banco}_cache.css` continuam as folhas da publicação. A folha nova ganha por vir depois, sem layer.
- O campo `C007_Tamanho_Fonte_Sistema` segue escolhendo o passo. O padrão de quem não escolheu é 12. Quem já gravou 13 ou 14 mantém. O cache grava o pixel e perde na propriedade que a folha repinta.
- A sidebar segue nas utilities do shell.
