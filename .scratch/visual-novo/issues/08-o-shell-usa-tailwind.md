Type: grilling
Orbit: ERPFX-36
Status: resolved
Blocked by: None

# O shell usa Tailwind

## Question

Dá para estilizar o shell com Tailwind v4 sem o reset nas telas, com estas amarras, lidas em [tailwind-no-shell.md](../research/tailwind-no-shell.md):

- O Preflight sai do CSS omitindo o import. Não há como aplicá-lo só fora do `#desktop`. O dialog do jQuery UI 1.8 vai para `document.body`, no mesmo documento.
- O prefixo `tw:` renomeia classes e variáveis geradas. Não reescreve seletor de elemento.
- `principal.css`, os temas e `jqueryui.css` não estão em layer. Em declaração normal, ganham das utilities.
- O CSS é arquivo estático. `docker/php/Dockerfile.prod` é `php:8.3-apache`, sem Node. O build fica fora dessa imagem (CLI standalone ou npm).
- O Smarty desta cópia é 3.0.8 com `auto_literal`: chave com espaço em volta é texto.

A spec estiliza o shell com Tailwind nessas condições?

## Answer

A spec estiliza o shell com Tailwind v4 nessas condições. O Preflight fica omitido. O prefixo é `tw:`. O CSS é arquivo estático, gerado no CLI fora da imagem `php:8.3-apache`, ligado por `<link>`, com o scan restrito aos arquivos do shell. A utility cede na propriedade que `principal.css`, os temas ou o `jqueryui.css` já declaram no mesmo elemento. Fundo e tipo ficam no elemento do shell.

A barra lateral, o topo, a faixa de abas e a paleta levam, nelas e nos descendentes, a base no layer `utilities`: `box-sizing: border-box`, fonte herdada no botão e no campo, sem borda nem fundo do navegador. A paleta não nasce dentro do `#desktop`. O `#desktop` fica fora dessa base.

Registro no [ADR 0008](../../../docs/adr/0008-shell-usa-tailwind-sem-preflight.md).
