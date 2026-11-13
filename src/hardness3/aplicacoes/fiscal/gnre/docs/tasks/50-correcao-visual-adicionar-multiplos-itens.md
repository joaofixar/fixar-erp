# Task 50 — correção visual da ação Adicionar em guias com múltiplos itens

Implementada em 04/11/2026. Nas listas de itens dos tipos `1` (Múltiplos
documentos de origem) e `2` (Múltiplas receitas), a ação abaixo da lista usa a
estrutura de ação do formulário e a classe `button`, com espaçamento próprio
após a tabela. A regra final da tela aplica a mesma métrica dos botões do
cabeçalho (42 px, preenchimento horizontal de 16 px e raio de 9 px), além da
cor primária. O seletor visual é limitado ao link funcional de inclusão na
lista múltipla (`data-guide-item-modal` e `new_item=1`), sem depender de uma
classe removida ou alterada na saída HTML. Assim, `Adicionar documento/item` e
`Adicionar receita` seguem o botão primário do módulo.

O link preserva integralmente `data-guide-item-modal` e o `href` com
`new_item=1`: com JavaScript abre o modal existente; sem JavaScript navega para
o formulário normal. A ação `Adicionar item` do tipo `0` não foi modificada.

Não houve alteração de POST, URL, atributos de dados, modal, JavaScript,
fallback, regras fiscais, tipos GNRE, itens, documentos, valores, vencimentos,
XML, transmissão, consulta, PDF, ambiente, banco ou guias reais.

## Roteiro manual

1. Abra uma guia de Múltiplos documentos com itens gravados e confirme que
   `Adicionar documento/item` é um botão primário abaixo da lista.
2. Clique na ação e confirme a abertura do modal.
3. Repita em uma guia de Múltiplas receitas para `Adicionar receita`.
4. Confira uma guia simples e confirme que `Adicionar item` não regressou.
5. Não salve, transmita, consulte resultado ou obtenha PDF.
