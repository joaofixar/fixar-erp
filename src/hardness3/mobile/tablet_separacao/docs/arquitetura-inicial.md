# Arquitetura Inicial

## Decisao

Criar um modulo novo e isolado em `mobile/tablet_separacao` para evitar acoplamento com:

- `mobile/phone`
- `mobile/tablet`
- formularios legados de `prog/est/est002`

O legado continuara como fonte de regra de negocio e consulta, mas a experiencia do tablet sera tratada como frente propria.

## Objetivo da V1

Entregar a separacao por item com leitura de locacao, mantendo a finalizacao oficial no backend atual.

## Fluxo principal

1. Operador autentica no modulo.
2. Sistema lista pedidos pendentes.
3. Operador abre o pedido.
4. Sistema mostra resumo e itens ainda nao separados.
5. Operador entra no item.
6. Operador bipa a locacao da etiqueta.
7. Sistema encontra a locacao valida para o item.
8. Operador informa a quantidade.
9. Sistema grava a separacao.
10. Operador conclui o pedido com `Continuar`.

## Rotas sugeridas

### Navegacao

- `GET /mobile/tablet_separacao/login.php`
- `POST /mobile/tablet_separacao/login.php`
- `GET /mobile/tablet_separacao/pedidos.php`
- `GET /mobile/tablet_separacao/pedido.php?id={T005_Id}`
- `GET /mobile/tablet_separacao/item.php?pedido={T005_Id}&item={T006_Id}`

### API interna do modulo

- `GET /mobile/tablet_separacao/api/pedidos.php`
- `GET /mobile/tablet_separacao/api/pedido.php?id={T005_Id}`
- `POST /mobile/tablet_separacao/api/locacao-resolver.php`
- `POST /mobile/tablet_separacao/api/item-separar.php`
- `POST /mobile/tablet_separacao/api/pedido-finalizar.php`

## Responsabilidade de cada rota

### `pedidos.php`

- listar pedidos aptos para separacao
- permitir busca rapida por numero do pedido
- mostrar indicadores minimos: pedido, cliente, emissao, status, volumes se houver

### `pedido.php`

- mostrar cabecalho do pedido
- listar apenas itens pendentes
- permitir abrir item para separacao
- exibir progresso do pedido
- manter o botao `Continuar`

### `item.php`

- destacar produto selecionado
- receber leitura da locacao por teclado ou leitor
- validar a locacao contra o item
- permitir informar quantidade
- gravar a linha de separacao
- voltar para o pedido com lista atualizada

## Reaproveitamento do legado

### Finalizacao do pedido

Manter a rotina atual:

- `/est/est002/form_func-ajax/est002SeparacaoPorLocacaoContinuar/`
- `EST002->SeparacaoPedido`

### Regras de gravacao por item

Reaproveitar a mesma modelagem atual de separacao:

- `T238_T005_Id`
- `T238_T006_Id`
- `T238_T066_Id`
- `T238_D004_Id`
- `T238_Quantidade_Separacao`

### Validacoes que devem migrar junto

- quantidade nao pode exceder o saldo do item
- locacao deve pertencer ao produto do item
- locacao deve possuir endereco valido
- quando aplicavel, validar estoque suficiente

## Lacunas que precisam ser validadas antes da implementacao de leitura

### Etiqueta da locacao

Precisamos confirmar qual valor esta codificado no barcode:

- `D004_Local`
- `T066_Id`
- outro identificador

### Conflitos de locacao

Confirmar se uma mesma locacao pode apontar para:

- mais de um lote do mesmo produto
- mais de um produto
- produtos vencidos ou bloqueados

### Divergencia

Hoje a divergencia e tratada no final. A V1 pode manter esse comportamento, mas a tela deve prever:

- item nao encontrado na locacao
- estoque fisico insuficiente
- necessidade futura de registrar falta durante a separacao

## Estrategia de entrega

1. Scaffold do modulo isolado.
2. Login e sessao.
3. Lista de pedidos.
4. Tela do pedido.
5. Tela do item com leitura da locacao.
6. Gravacao da separacao.
7. Finalizacao com rotina atual.
8. Ajustes de UX para uso real em tablet.
