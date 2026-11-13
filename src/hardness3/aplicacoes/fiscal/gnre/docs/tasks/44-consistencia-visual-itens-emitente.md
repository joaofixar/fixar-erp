# Task 44 — Consistência visual de itens, vencimentos e emitente

## Entrega

Esta task é exclusivamente de apresentação. Não altera persistência, schema,
`GNRE001`–`GNRE009`, XML, snapshots `GNRE007`, transação, transmissão, consulta,
Portal, PDF ou ambientes.

- A lista de tipos GNRE múltiplos passa a mostrar **Vencimento**, proveniente de
  `GNRE003_Data_Vencimento`, em `dd/mm/aaaa`; a ausência é exibida como `—`.
- Valores apresentados na lista e no resumo usam `R$ 0,00`. A formatação ocorre
  apenas no markup de leitura: inputs, POST e valor persistido continuam
  inalterados.
- Tipo único sem item exibe um estado vazio com **Adicionar item**. Com
  JavaScript, o link usa o mesmo controlador e abre o modal existente; sem
  JavaScript, navega para `new_item=1`, onde o formulário normal permanece
  funcional. Após salvar, a política existente volta ao resumo persistido.
- O emitente usa `dl.guide-draft-emitter` em grade de duas colunas, com rótulo à
  esquerda e valor à direita, tanto no formulário/modal quanto no fallback de
  consulta. No formulário, o bloco aparece antes dos grupos editáveis. A grade
  está em `public/gnre.css`, sem CSS injetado por JavaScript.

## Roteiro manual

1. Em desktop, abra uma guia de tipo 1 ou 2 com itens: confira Receita,
   Documento, Vencimento, Valor e Editar; reduza a largura para confirmar a
   rolagem horizontal controlada da tabela.
2. Em uma guia de tipo 0 sem item, confira que só há o estado vazio. Clique em
   **Adicionar item**, salve e confirme o resumo; edite o item em seguida.
3. Desative JavaScript, repita a inclusão de tipo 0 e confirme que o link abre
   o formulário normal na mesma janela e que o POST funciona.
4. Em desktop e mobile, abra inclusão/edição e confira CNPJ, Razão social, UF e
   Cidade do emitente em pares rótulo/valor, sem informações técnicas.
5. Não transmita a guia durante esta verificação.

## Verificação automatizada

`task44-visual-consistency-static.php` cobre colunas, formatações somente de
leitura, fallback, controlador compartilhado, grade do emitente e as restrições
de implementação. As regressões de formulário, hidratação, transação e snapshot
continuam nas Tasks 18, 22, 26.4, 31, 32, 35, 42 e 43.
