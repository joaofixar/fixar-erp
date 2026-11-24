Type: grilling
Orbit: ERPFX-38
Status: resolved
Blocked by: None

# Onde nascem os tokens

## Question

O shell usa Tailwind v4, com o prefixo `tw:` e o Preflight omitido, como ficou em [O shell usa Tailwind](08-o-shell-usa-tailwind.md). O miolo muda por um tema com os mesmos tokens do shell. Cada tela não se reescreve.

Os tokens de cor, tipo e densidade nascem no `@theme` desse Tailwind, e o tema do jQuery UI do miolo aponta para as mesmas variáveis?

## Answer

Sim. Cor, tipo e a escala de densidade nascem uma vez no `@theme` do Tailwind, nas variáveis `--tw-…`. O shell lê pelas utilities. O tema é um só. `tema-claro.css`, `tema-escuro.css` e `tema-2021.css` ficam fora deste esforço.

Uma folha no mesmo lugar do shell, que a publicação não envia, entra pelo gancho de `index.tpl` depois do cache e sem layer. Ela reescreve as variáveis que o miolo já lê (`--fundo` e as demais) como `var(--tw-…)`. Pinta a cor que o `principal.css` grava literal no `tema_{banco}_cache.css`: grade, filtro, formulário, abas, células de status, avisos, rolagem, gradiente do `body` e as chaves de cor do `_cssVars.ini`. Repinta os papéis do ThemeRoller no atalho `background`. O desenho do ícone continua o sprite. A cor do ícone sai do token. A sidebar segue nas utilities do shell.

O mesmo gancho marca no `body` o passo que `C007_Tamanho_Fonte_Sistema` escolheu: 12, 13 ou o padrão 14. A folha usa essa marca para tipo e espaçamento. O cache continua gravando o pixel e perde nessas propriedades.

Qual valor entra em cada token fica em [Quais valores o tema carrega](11-quais-valores-o-tema-carrega.md).

Registro no [ADR 0009](../../../docs/adr/0009-tokens-nascem-no-theme.md).
