# Tablet Separacao

Nova frente isolada para a separacao de pedidos em tablet.

## Objetivo

Substituir o fluxo legado de impressao + separacao manual por uma experiencia focada em:

- abrir o pedido direto no tablet
- entrar no item pendente
- bipar a locacao
- informar a quantidade separada
- finalizar o pedido pela rotina ja existente

## Principios desta frente

- isolamento do legado em pasta propria
- implementacao incremental
- reaproveitar apenas regras de negocio estaveis
- interface preparada para toque e leitor de codigo de barras
- rotas e responsabilidades separadas por tela e acao

## Fluxo V1

1. Login do operador no modulo tablet.
2. Lista de pedidos pendentes para separacao.
3. Abertura do pedido com resumo e itens pendentes.
4. Selecionar um item.
5. Bipar a locacao do item.
6. Informar ou confirmar a quantidade separada.
7. Registrar separacao do item.
8. Repetir ate zerar os itens pendentes.
9. Acionar "Continuar" para finalizar a separacao do pedido.

## Integracoes que podem ser reaproveitadas

- `/est/est002/form_func-ajax/est002SeparacaoPorLocacaoContinuar/`
- regras atuais do `EST002->SeparacaoPedido`
- regras de gravacao em `T238`
- validacoes de quantidade e estoque do fluxo atual

## Documentos desta pasta

- [docs/arquitetura-inicial.md](/root/sistema_hardness/hardness3/mobile/tablet_separacao/docs/arquitetura-inicial.md)
- [docs/wireframes.md](/root/sistema_hardness/hardness3/mobile/tablet_separacao/docs/wireframes.md)
- [index.php](/root/sistema_hardness/hardness3/mobile/tablet_separacao/index.php)
