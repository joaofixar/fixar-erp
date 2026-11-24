Type: prototype
Orbit: ERPFX-39
Status: resolved
Blocked by: 10

# Quais valores o tema carrega

## Question

O tema é um só, e os tokens nascem no `@theme`, como ficou em [Onde nascem os tokens](10-onde-nascem-os-tokens.md). A folha do miolo repinta variável, seletor do cache, papel do ThemeRoller e a cor do ícone. O desenho do ícone continua o sprite. O passo 12, 13 ou 14 entra no `body`.

Quais cores, qual tipo e qual densidade esses tokens carregam, e qual token preenche cada variável e cada seletor?

## Comments

Protótipo para reagir: [tema.html](../prototype/tema.html). Três vocabulários na barra de baixo (`?variant=papeis`, `continuidade`, `azul`) e o passo 12, 13 ou 14 (`?passo=`). `?vazio=1` mostra a área sem aba. A seta troca a variante. O mapa à direita lista o token e o que ele preenche.

## Answer

O tema carrega o vocabulário Papéis. Shell e miolo leem os mesmos tokens. O cromado é quieto. A cor fica no status, no erro, na seleção e no acento. O preenchimento de cada variável e seletor é o mapa da variante Papéis em [tema.html](../prototype/tema.html). Registro no [ADR 0010](../../../docs/adr/0010-tema-carrega-o-vocabulario-papeis.md).

A face é `"Segoe UI", system-ui, sans-serif`.

Os tokens de cor:

| Token | Valor | O que pinta |
| --- | --- | --- |
| `side` | `#1b2430` | sidebar, título da janela, `.ui-widget-header` |
| `side-ink` | `#e8edf2` | texto da sidebar e do título, ícone sobre eles |
| `side-muted` | `#9aa6b4` | empresa sob a marca |
| `accent` | `#1d4f8a` | avatar, título de seção, estado ativo |
| `canvas` | `#e7eaee` | fundo da área, faixa de abas, trilha da rolagem |
| `paper` | `#ffffff` | grade, campo, aba ativa, `.ui-widget-content` |
| `ink` | `#1a1d21` | texto e ícone |
| `muted` | `#5c6570` | texto secundário, texto do cabeçalho da grade, texto da área vazia |
| `line` | `#d5d9e0` | bordas |
| `accent-soft` | `#e6eef8` | linha selecionada, realce, hover de aba |
| `hover` | `#FFE98C` | linha sob o mouse |
| `header` | `#f4f6f8` | cabeçalho da grade, zebra, filtro, aba em repouso |
| `selected-cell` | `#d3e2f5` | célula selecionada |
| `readonly` | `#f3f4f6` | campo somente leitura |
| `error` | `#ffaaaa` | erro do formulário e `.ui-state-error` |
| `shadow` | `#141c2833` | sombra |
| `scroll-handle` | `#b7c0cb` | alça da rolagem |

Célula e aviso ficam no hex do cache: verde `#B6F9B1`, amarelo `#FCF6A4`, amarelo-claro `#F9F7BD`, vermelho `#FCDAC2`, azul `#B3D8F9`, e os avisos `#063fdb`, `#E82728`, `#6fc293`, `#F1D126`. O ícone do erro usa o aviso vermelho. O fundo é chapado. Saem o gradiente cinza do `body` e a imagem `desktop.png`.

Ficam sem token as chaves de menu do `_cssVars.ini`, `--fundo-menu`, `--fundo-menu-modulo`, `--fundo-menu-ahover` e `--teste`. A textura do ThemeRoller sai. O sprite fica.

A densidade é a escala curta. O passo que abre é 12.

| Passo | Nome | Fonte | Cabeçalho | Célula | Aba | Campo | Título |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 12 | Pequeno | 12px | 4px 8px | 2px 8px | 22px | 24px | 4px 8px |
| 13 | Médio | 13px | 5px 8px | 4px 8px | 24px | 26px | 5px 8px |
| 14 | Grande | 14px | 6px 10px | 5px 10px | 28px | 30px | 6px 10px |

A entrelinha é 1.3 no 12 e no 13, e 1.35 no 14. O pixel que o cache grava nessas propriedades perde.

O menu do avatar, entre o perfil e Sair, oferece Pequeno, Médio e Grande. Dá para subir e voltar, e o passo atual fica marcado. A escolha grava `C007_Tamanho_Fonte_Sistema`. Quem já está em 13 ou 14 continua. O padrão da coluna, hoje 13, passa a 12. A tela de configuração da interface continua gravando o mesmo campo.

Isto revisa o padrão 14 de [Onde nascem os tokens](10-onde-nascem-os-tokens.md). O zoom de fonte sai da névoa: é este passo.

