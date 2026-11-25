Status: ready-for-human
Orbit: ERPFX-66

# 37: O formulário ganha o tema dos campos

**What to build:** Todo formulário ganha o tema dos campos: campo branco com borda e raio de 6px, somente leitura num cinza uniforme e sem sombra, foco no azul do tema, rótulo apagado e o asterisco do obrigatório em vermelho.

A reorganização das posições absolutas do `formEditor.tpl` numa grade foi experimentada no Cliente/Fornecedor e recuada: o resultado não ficou bom. Fica para planejar depois. As posições gravadas continuam como o editor deixou.

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] Campo, somente leitura, foco, rótulo e asterisco em todo formulário
- [x] A posição gravada de cada campo continua a do editor

## Comments

Tudo em `src/fx/tema.css`, mais o `marcarObrigatorios` em `src/fx/shell.js`.

- O degradê cinza vinha do `tema-2021.css` (`--fundo-campo-form`, `[readonly="readonly"]`). O tema repete os mesmos seletores e entra depois.
- O `* ` do obrigatório vem no texto do rótulo. O shell põe o asterisco num `span.fx-asterisco`; o `textContent` do rótulo continua igual.
- A grade chegou a agrupar os campos do Cliente em duas colunas de rótulo e campo, desfeita quando o editor abria. Produto não coube (mais de três colunas) e ficou absoluto. O resultado no Cliente não convenceu e a grade saiu por inteiro, inclusive o embrulho do `showHideFormEditor`.
- O usuário `fixar` da base local abre a inclusão de Cliente com quase todo campo `readonly`. É dado da base, não do tema; o cinza do somente leitura cobre os dois estados.
