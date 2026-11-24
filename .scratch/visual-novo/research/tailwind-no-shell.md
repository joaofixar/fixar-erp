# Tailwind no shell, sem o reset nas telas do `#desktop`

Fatos para a pergunta do ticket `.scratch/visual-novo/issues/04-tailwind-no-shell.md`. Não escolhe visual, paleta nem onde o shell novo mora.

A documentação vigente consultada é a de Tailwind CSS v4 em `https://tailwindcss.com/docs`. O repositório não fixa versão de Tailwind: não há `package.json` na raiz, e os `package.json` que existem são de bibliotecas já vendidas (Monaco, CodeMirror, Font Awesome do B2B). O arquivo de reset citado abaixo é o da tag `v4.3.5` do repositório `tailwindlabs/tailwindcss`, release de 6 de novembro de 2026 (`https://github.com/tailwindlabs/tailwindcss/releases/tag/v4.3.5`).

## O que o Preflight reseta

O Preflight é “an opinionated set of base styles”, construído sobre o modern-normalize. Ao importar `tailwindcss`, ele entra sozinho no layer `base`:

```css
@layer theme, base, components, utilities;
@import "tailwindcss/theme.css" layer(theme);
@import "tailwindcss/preflight.css" layer(base);
@import "tailwindcss/utilities.css" layer(utilities);
```

Fonte: `https://tailwindcss.com/docs/preflight`.

A página resume o que costuma surpreender. O arquivo completo, na tag `v4.3.5`, é `packages/tailwindcss/preflight.css` (`https://github.com/tailwindlabs/tailwindcss/blob/v4.3.5/packages/tailwindcss/preflight.css`). Os seletores são de elemento, universal, pseudo e atributo. Nenhum está limitado a um contêiner.

| O que muda | Seletores no `preflight.css` da v4.3.5 |
| --- | --- |
| `box-sizing: border-box`, `margin: 0`, `padding: 0`, `border: 0 solid` | `*`, `::after`, `::before`, `::backdrop`, `::file-selector-button` |
| `line-height: 1.5`, família sans do tema, `tab-size: 4`, sem realce de toque no iOS | `html`, `:host` |
| Título sem tamanho nem peso próprios (`font-size` e `font-weight: inherit`) | `h1`–`h6` |
| Link herda cor e decoração | `a` |
| Lista sem marcador | `ol`, `ul`, `menu` |
| Substituídos em bloco, alinhados ao meio | `img`, `svg`, `video`, `canvas`, `audio`, `iframe`, `embed`, `object` |
| Imagem e vídeo limitados à largura do pai, altura automática | `img`, `video` |
| Controle de formulário herda a fonte, `border-radius: 0`, fundo transparente, `opacity: 1` | `button`, `input`, `select`, `optgroup`, `textarea`, `::file-selector-button` |
| Placeholder semitransparente; `textarea` só redimensiona na vertical | `::placeholder`, `textarea` |
| `hidden` continua escondido | `[hidden]:where(:not([hidden="until-found"])) { display: none !important }` |

A página oficial repete margem, borda, título, lista, imagem e o atributo `hidden`, e aponta o stylesheet como a referência completa (`https://tailwindcss.com/docs/preflight`). O comentário no topo do arquivo liga o `border: 0 solid` ao truque da classe `border`: essa classe só define `border-width`, e o reset é o que faz o resultado ser uma borda sólida de 1px em `currentColor`.

Esses seletores valem para o documento que carrega o CSS. As telas jQuery UI não são outro documento. `novoDialog` cria um `div`, faz `.appendTo("#desktop")` e em seguida chama `.dialog()` (`referencia/static/js/sistema.js`, por volta das linhas 2181–2231). `abrirJanela` busca HTML (`dataType: "html"`) e grava com `.html()` nesse `div` (por volta das linhas 2128–2147). O widget do jQuery UI 1.8.16, porém, tira o diálogo de dentro do `#desktop`: `(a.uiDialog=c("<div></div>")).appendTo(document.body)` em `src/hardness3/static/js/jquery-ui-1.8.16.custom.min.js`. O nó continua no mesmo documento, agora direto no `body`. Um seletor `*` ou `button` do Preflight alcança essa tela. Um seletor `#desktop …` não, depois do reparent.

O shell inclui esse CSS no `<head>` de `src/hardness3/smarty/templates/index.tpl` via `{$cssJs}`. `processaCssJs()` em `src/hardness3/sistema/index-framework.php` emite `<link rel=stylesheet href="…" type="text/css">` para cada item de `cssArquivo`, mais um `<style>` com `cssTexto`. Não há `@layer` nesse HTML. A mesma função empilha, entre outros, `static/css/temas/padrao/jqueryui.css` (ou `temas/empresa/jqueryui.css`) e o tema. O jQuery UI ligado é `static/js/jquery-ui-1.8.16.custom.min.js`.

## O que deixa de valer com o Preflight desligado

A página manda desligar o Preflight importando só as partes usadas e omitindo o import dele, caso típico de “integrating Tailwind into an existing project” (`https://tailwindcss.com/docs/preflight#disabling-preflight`). O guia da v4 beta mostra o resultado dessa omissão (`https://v3.tailwindcss.com/docs/v4-beta`, seção “Disabling Preflight”):

```css
@layer theme, base, components, utilities;
@import "tailwindcss/theme" layer(theme);
@import "tailwindcss/utilities" layer(utilities);
```

A página atual escreve os mesmos arquivos com sufixo `.css` (`tailwindcss/theme.css`, `tailwindcss/utilities.css`). O que sai do CSS gerado é o conteúdo de `preflight.css`: nenhuma das regras da tabela acima é emitida. Tema e utilities continuam. A garantia documentada da classe `border` (largura só, estilo sólido e cor vindos do reset) deixa de existir (`https://tailwindcss.com/docs/preflight`, “Border styles are reset”). `box-sizing: border-box` no universal também sai com o arquivo; sem outra regra, vale o `content-box` do user agent.

Não há, na documentação, um interruptor que aplique o Preflight só fora do `#desktop`. O import é do stylesheet inteiro. Omitir o import tira o reset do documento todo, shell incluído.

Na v3 o interruptor era outro: `corePlugins.preflight: false` em `tailwind.config.js` (`https://v3.tailwindcss.com/docs/preflight`). Este repositório não usa essa configuração. A documentação vigente é a da v4.

## O prefixo separa classe nova de classe legada, e não separa o reset

O prefixo existe para “class names that conflict with Tailwind CSS utilities”. Ele prefixa as classes geradas e as variáveis CSS (`https://tailwindcss.com/docs/styling-with-utility-classes#using-the-prefix-option`):

```css
@import "tailwindcss" prefix(tw);
```

O CSS compilado nessa página é `.tw\:text-red-500` e `--tw-color-red-500`, dentro de `@layer utilities` e `@layer theme`. No HTML a classe é `tw:text-red-500`. O guia da v4 beta mostra `tw:flex`, `tw:bg-red-500`, `tw:hover:bg-red-600`, e diz que o prefixo da v4 “look[s] like variants and [is] always at the beginning of the class name”, diferente da v3 (`https://v3.tailwindcss.com/docs/v4-beta`, “Using a prefix”).

Quando o Preflight está desligado e o prefixo entra nos imports avulsos, a página atual manda colocá-lo em `theme.css` e em `utilities.css`, não num arquivo de reset (`https://tailwindcss.com/docs/preflight#disabling-preflight`). O `preflight.css` da v4.3.5 não declara classes de utility. Um prefixo não reescreve `*`, `button`, `ul` ou `img`.

Classes do legado que coincidem com uma utility (`flex`, `hidden`, `container`, `block`) deixam de receber essa utility se o prefixo estiver ligado. Classes que não coincidem (`ui-button`, `ui-corner-all`, `menubar`, `menuvertical`, `btn`, em `menu.tpl` e `index.tpl`) não são alvo do prefixo de qualquer forma. O prefixo não impede o Preflight de alcançar o diálogo.

## Layers do Tailwind e o CSS que não está em layer

Na v4 a saída usa cascade layers nativos, nesta ordem de declaração: `theme`, `base`, `components`, `utilities` (`https://tailwindcss.com/docs/preflight`; o formato da saída está em `https://v3.tailwindcss.com/docs/v4-beta`, “Native CSS cascade layers”). O guia de upgrade diz que a v4 “us[es] native cascade layers and no longer hijack[s] the `@layer` at-rule”; na v3 o `@layer` era reordenado no build, não era o layer nativo (`https://tailwindcss.com/docs/upgrade-guide`, “Adding custom utilities”).

O CSS Cascade Level 5 compara layers antes de especificidade e antes da ordem no arquivo (`https://www.w3.org/TR/css-cascade-5/#cascade-sort`, passo “Layers”):

- Declaração sem layer entra num layer implícito final.
- Em regra normal, ganha o layer que vem por último.
- Em regra `!important`, ganha o layer que vem primeiro.

A seção 6.4.3 repete: estilos sem layer ficam num layer externo implícito, de prioridade maior que os layers explícitos, e “come last in the layer order” (`https://www.w3.org/TR/css-cascade-5/#layer-ordering`). O exemplo do spec é um `audio { display: flex }` sem layer vencendo um `audio[controls] { display: block }` dentro de um layer, mesmo com especificidade menor e aparecendo antes no arquivo (`https://www.w3.org/TR/css-cascade-5/#cascade-layers`).

Ordem normal, da mais fraca para a mais forte, com o Tailwind v4 no mesmo documento: `theme`, `base` (Preflight), `components`, `utilities`, e por fim o CSS sem layer. Ordem `!important`, invertida: o sem layer é o mais fraco entre os `!important` de autor; o layer declarado primeiro é o mais forte.

O que isso faz com o CSS deste repositório:

- `src/hardness3/static/css/principal.css`, `tema-claro.css`, `tema-escuro.css`, `tema-2021.css`, `tema-azul-claro-contraste.css`, `temas/tema_fixar_cache.css` e `temas/padrao/jqueryui.css` não contêm `@layer`. Uma busca por `@layer` em `src/hardness3/static/css` não acha ocorrência. São folhas sem layer, ligadas pelo `<link>` de `processaCssJs()`.
- `principal.css` estiliza `#topo` (linha 189), `#footer` (233), `#desktop` (748) e `ul.menubar` (275). Não há seletor `#barra`. O id `barra` está no markup: `menu.tpl` abre com `<ul id="barra" class="menubar …">`. Os temas repetem `#footer`, partes de `#topo` e `ul.menubar`; `#desktop` aparece em `principal.css` e em `temas/tema_fixar_cache.css`.
- Uma declaração normal sem layer ganha de uma utility no layer `utilities` na mesma propriedade, mesmo que a utility seja uma classe e o legado seja `#topo`, e mesmo que o `<link>` do Tailwind venha depois. A utility só pinta a propriedade que o legado não declara.
- O Preflight no layer `base` perde, na propriedade que o legado declara, para `principal.css`, o tema e o `jqueryui.css`. Na propriedade que ninguém declara, o reset continua valendo dentro do diálogo. Layer não substitui desligar o import.
- A exceção `!important` do próprio Preflight é `[hidden] { display: none !important }` no layer `base`. Entre `!important`, o layer `base` (declarado antes) ganha do layer implícito final. Um `[hidden] { display: … !important }` sem layer perde para essa regra.
- O modificador `!` no fim da classe (`bg-red-500!`) e a flag `@import "tailwindcss" important` geram utilities com `!important`, ainda dentro de `@layer utilities` (`https://tailwindcss.com/docs/styling-with-utility-classes#using-the-important-modifier` e `#using-the-important-flag`). A flag é o mecanismo documentado para “existing complex CSS with high specificity rules”. Pelo spec, esse `!important` de autor ganha de declaração normal sem layer, e também ganha de `!important` sem layer, porque `utilities` vem antes do layer implícito final. Sem o `!` e sem a flag, a utility normal perde para o sem layer.

`jqueryui.css` não é um reset universal. A regra larga dele é `.ui-helper-reset` (margem, padding, borda, lista), na linha 15 de `src/hardness3/static/css/temas/padrao/jqueryui.css`, mais regras de componente (`.ui-tabs`, `.ui-datepicker`, `button.ui-button-icon-only`). Onde essa folha não declara a propriedade, o Preflight importado ainda alcança o elemento.

## Onde o CSS é gerado

O Tailwind “works by scanning all of your HTML files, JavaScript components, and any other templates for class names, generating the corresponding styles and then writing them to a static CSS file”. É “zero-runtime” (`https://tailwindcss.com/docs/installation/tailwind-cli`). Não roda no request do PHP. O Apache só precisa servir o arquivo já escrito.

O CLI documentado:

```
npm install tailwindcss @tailwindcss/cli
npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
```

A mesma página diz que o CLI “is also available as a standalone executable if you want to use it without installing Node.js”. O guia da v4 beta repete: dá para baixar “standalone builds of the new CLI tool from GitHub for projects that don’t otherwise depend on the Node.js ecosystem” (`https://v3.tailwindcss.com/docs/v4-beta`, “Installing the CLI”). A release `v4.3.5` publica esses binários, entre eles `tailwindcss-linux-x64`, `tailwindcss-linux-x64-musl` e `tailwindcss-windows-x64.exe` (`https://github.com/tailwindlabs/tailwindcss/releases/tag/v4.3.5`).

O scan da v4 trata o fonte como texto, sem parser da linguagem, e percorre todo arquivo do projeto exceto o que está no `.gitignore`, `node_modules`, binários, CSS e lockfiles de gerenciador de pacote (`https://tailwindcss.com/docs/detecting-classes-in-source-files`). `src/hardness3/smarty/templates/index.tpl` e `menu.tpl` não estão no `.gitignore`. Estão ignorados `src/hardness3/smarty/templates_c/` e `src/hardness3/smarty/cache/` (`.gitignore`). Classe montada por concatenação não é vista: o exemplo oficial `text-{{ error ? 'red' : 'green' }}-600` não gera `text-red-600` nem `text-green-600`. O nome completo tem de aparecer no arquivo. `@source`, `source(…)` e `@source not` estreitam o scan (`https://tailwindcss.com/docs/detecting-classes-in-source-files`). Um scan automático na raiz deste repositório também leria a árvore PHP e o JavaScript vendido, e emitiria utility para todo token que parecesse classe.

Neste repositório o build não existe:

- `docker/php/Dockerfile.prod` começa em `FROM php:8.3-apache`. Instala extensões PHP (apcu, gd, zip, soap, intl), `unzip` e bibliotecas de imagem, copia `src` para `/var/www/html`, roda `composer install` em `hardness3/bibliotecas/composer` e apaga o binário do Composer. Não instala Node, npm nem o CLI do Tailwind.
- `docker/php/Dockerfile` (a imagem local do `docker-compose.yml`) também é `FROM php:8.3-apache`, sem Node.
- `docker-compose.yml` sobe `web` (build `./docker/php`) e `db` (`mariadb:11`). `docker-compose.prod.yml` sobe `web` a partir de `docker/php/Dockerfile.prod` e `db`. Não há serviço Node.
- `CODING_STANDARDS.md`, seção “Contêineres”: o runtime é o Kiri, que fala a Docker Engine API; `docker`, `docker compose` e `buildx` seguem iguais. O Kiri não acrescenta Node à imagem.

O `COPY src /var/www/html` do `Dockerfile.prod` leva o que já estiver em `src` no momento do build da imagem. Um CSS estático gerado fora, e presente em `src`, seria copiado. A imagem, como está, não o gera.

Há também o Play CDN na lista de instalação da mesma página do CLI. Isso não é build: é script no browser, no documento da página. Não é o caminho do arquivo estático que o Apache serviria.

## O que as chaves do Smarty exigem em `<style>` e `<script>`

A constante está legível em `src/hardness3/smarty/libs/Smarty.class.php`, apesar do ofuscamento do arquivo: `const SMARTY_VERSION = 'Smarty-3.0.8'`. No mesmo construtor da classe: `public $auto_literal = true`, `public $left_delimiter = "{"`, `public $right_delimiter = "}"`. É Smarty 3, não Smarty 4. O lexer desta cópia, `src/hardness3/smarty/libs/sysplugins/smarty_internal_templatelexer.php`, consulta `$this->smarty->auto_literal`. Com ele ligado, o token vira `TP_OTHER` (texto), não tag. O padrão do lexer (linha 152) inclui o delimitador esquerdo seguido de whitespace (`\s{1,}`).

O manual oficial descreve o mesmo comportamento como novidade do Smarty 3 (`https://www.smarty.net/docs/en/language.escaping.tpl` e `https://www.smarty.net/docs/en/variable.auto.literal.tpl`; o resumo da v3 está em `https://www.smarty.net/v3_overview`, “{ and } (Javascript) Auto-Escapement”):

- `{` e `}` cercados de whitespace são ignorados. O exemplo do manual é uma função JavaScript cujo `{` fica no fim da linha e cujo corpo está indentado. Isso vale dentro de `<script>` e de `<style>`.
- `{` colado no que vem depois não é ignorado. O exemplo é `function bazzy {alert('Bar!');}`, que precisa de escape.
- `$auto_literal = false` desliga essa folga. Nesta cópia o default é `true`, e não há atribuição de `auto_literal` fora de `Smarty.class.php` e do lexer.
- `{literal}…{/literal}` copia o bloco sem interpretar. Tag Smarty dentro do bloco não roda; o manual manda usar `{ldelim}` e `{rdelim}` (ou `{$smarty.ldelim}` / `{$smarty.rdelim}`) quando o trecho precisa das duas coisas (`https://www.smarty.net/docs/en/language.function.literal.tpl`, `https://www.smarty.net/docs/en/language.function.ldelim.tpl`).
- Trocar `$left_delimiter` e `$right_delimiter` muda toda a sintaxe, não só o CSS. O manual pede limpar cache e compilados.
- A prática que o manual recomenda, para não escapar, é CSS e JavaScript em arquivo separado, ligados por `<link>` e `<script src>`.

Os templates atuais já misturam tag e estilo, e dependem do `{` colado:

- `index.tpl` linhas 12–14: `<style type="text/css">` com `{$cssExtra}` dentro. `{$cssExtra}` é tag, porque não há whitespace depois do `{`.
- `index.tpl` linha 21: `style="overflow: hidden;{if $oneWindow} … {/if}"`.
- `menu.tpl` linha 1: `style="{if $modoDesenvolvedor} background-color: rgb(232 55 4); {/if}"`.
- `index.tpl` a partir da linha 145: `<script>` com funções jQuery. Os `{` de função estão cercados de whitespace (fim de linha ou espaço), que é o caso que o `auto_literal` deixa passar. Não há `{literal}` nesses dois templates.

Um bloco `<style>` novo com regra CSS no formato `seletor{propriedade:valor}` (chave colada) seria lido como tag e quebraria a compilação, a menos que haja whitespace em volta das chaves ou um `{literal}`. Um `{literal}` em volta de `{$cssExtra}` ou de `{if}` impediria essas tags de rodarem.
