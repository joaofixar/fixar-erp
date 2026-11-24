Type: research
Orbit: ERPFX-32
Blocked by: None
Status: resolved

# Tailwind no shell Smarty

## Question

Dá para estilizar o shell novo com Tailwind sem o reset atingir as telas jQuery UI que abrem dentro do `#desktop`, e como esse CSS é gerado no contêiner?

Fatos a cravar, cada um com a fonte primária (documentação do Tailwind, Dockerfile e templates deste repositório):

- O que o Preflight reseta, e o que deixa de valer com ele desligado.
- Se um prefixo separa as classes novas das classes do legado.
- Como as cascade layers do Tailwind convivem com o CSS atual (`principal.css` e os temas), que não está em layer.
- Onde o build roda. O repositório não tem Node. O runtime de contêiner é o Kiri. A imagem de produção está em `docker/php/Dockerfile.prod`.
- O que as chaves `{}` do Smarty exigem dentro de `<style>` e `<script>`.

O arquivo da resposta é `.scratch/visual-novo/research/tailwind-no-shell.md`. Não decidir o visual. Não editar template, PHP nem CSS de produção.

## Answer

Dá para tirar o reset do documento omitindo o import de `tailwindcss/preflight.css`. O prefixo `tw:` não faz isso: só renomeia classes e variáveis geradas. As telas jQuery UI são HTML no mesmo documento (o dialog 1.8 vai para `document.body`), então o Preflight, se importado, alcança o que o CSS sem layer não declara. `principal.css`, temas e `jqueryui.css` não estão em layer e, em declaração normal, ganham das utilities. O CSS é arquivo estático do CLI (npm ou binário standalone); `docker/php/Dockerfile.prod` é `php:8.3-apache`, sem Node. O Smarty desta cópia é 3.0.8 com `auto_literal` true: chave com espaço em volta é texto; chave colada em nome ou `$` é tag.

Pesquisa: [tailwind-no-shell.md](../research/tailwind-no-shell.md).
