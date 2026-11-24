---
status: accepted
---

# O shell usa Tailwind sem o Preflight

O shell do FX ERP é estilizado com Tailwind v4. O Preflight fica de fora: o import não se limita ao que está fora do `#desktop`, e o diálogo do jQuery UI 1.8 vai para o `document.body`, no mesmo documento. O prefixo é `tw:`. O CSS é arquivo estático, gerado no CLI fora da imagem PHP e ligado por `<link>`. A utility cede na propriedade que o CSS sem layer do legado já declara no mesmo elemento. Fundo e tipo ficam no elemento do shell.

## Considered Options

- Folha própria, sem Tailwind. O shell perderia o vocabulário de utility que o tema do miolo vai compartilhar.
- Preflight ligado. O reset alcança o diálogo e o que o miolo não declara.
- Flag `important` em toda utility. A utility passaria a ganhar do miolo na propriedade que os dois declaram.

## Consequences

- O scan do CLI fica nos arquivos do shell. O `<link>` entra no gancho de `index.tpl`. O CSS do shell continua em arquivo que a publicação não envia.
- `body` (fundo, margem, fonte, altura), `li` (marcador), `select` (altura) e `input`/`textarea` no foco (contorno e sombra) seguem o que `principal.css` declara quando a utility repete a propriedade.
- A barra lateral, o topo, a faixa de abas e a paleta levam, nelas e nos descendentes, a base no layer `utilities`: `box-sizing: border-box`, fonte herdada no botão e no campo, sem borda nem fundo do navegador. A paleta não nasce dentro do `#desktop`. O `#desktop` fica fora dessa base. O diálogo, já no `body`, também fica fora.
