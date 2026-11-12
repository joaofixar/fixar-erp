# Wireframes e Fluxo

## Tela 1 - Login

Objetivo: autenticar o operador no modulo tablet.

```text
+-----------------------------------------------------------+
| HARDNESS - TABLET SEPARACAO                               |
|                                                           |
| [ logo ]                                                  |
|                                                           |
| Login                                                     |
| [________________________]                                |
| Senha                                                     |
| [________________________]                                |
|                                                           |
| [ Entrar ]                                                |
+-----------------------------------------------------------+
```

## Tela 2 - Lista de pedidos

Objetivo: trocar a etapa antiga de impressao por abertura direta do pedido.

```text
+-----------------------------------------------------------+
| Separacao                                                 |
| Buscar pedido [____________________]                      |
|                                                           |
| Pedido 2245   ADALBERTO SACCHI        01/04/2026 [ Abrir ]|
| Pedido 1422   GABRIEL PASQUINI        10/11/2020 [ Abrir ]|
| Pedido 1163   ROMERO SANTANA XAVIER   04/11/2020 [ Abrir ]|
+-----------------------------------------------------------+
```

Elementos importantes:

- busca por numero do pedido
- toque grande no card inteiro
- status enxuto
- sem acao de impressao

## Tela 3 - Resumo do pedido

Objetivo: mostrar progresso da separacao e entrada nos itens.

```text
+-----------------------------------------------------------+
| Pedido 2245                                               |
| Cliente: ADALBERTO SACCHI                                 |
| Cidade: ABATIA - PR                                       |
| CFOP: 5.403                                               |
| Transportadora: ...                                       |
|                                                           |
| Progresso: 2 de 5 itens separados                         |
|                                                           |
| [ Item 1 ] FRESA TOPO ACO RAPIDO                          |
| Saldo: 3,0000                           [ Separar item ]   |
|                                                           |
| [ Item 2 ] BROCA X                                        |
| Saldo: 1,0000                           [ Separar item ]   |
|                                                           |
| [ Continuar ]                                             |
+-----------------------------------------------------------+
```

Elementos importantes:

- listar somente itens pendentes
- mostrar saldo a separar
- botao `Continuar` fixo no rodape
- acesso rapido ao item

## Tela 4 - Separacao do item

Objetivo: fazer a separacao com o minimo de toques.

```text
+-----------------------------------------------------------+
| Pedido 2245 > Item 1                                      |
| Produto: FRESA TOPO ACO RAPIDO                            |
| Saldo pendente: 3,0000                                    |
|                                                           |
| Bipar locacao                                             |
| [__________________________________________]              |
|                                                           |
| Locacao encontrada: LOTE ND - LOC 00.0                    |
| Endereco: 00.0                                            |
|                                                           |
| Quantidade                                                |
| [__________]                                              |
|                                                           |
| [ Confirmar separacao ]                                   |
| [ Voltar ao pedido ]                                      |
+-----------------------------------------------------------+
```

Regras da tela:

- o foco deve abrir direto no campo de leitura
- aceitar digitacao do leitor como teclado
- ao bipar uma locacao valida, preencher automaticamente os dados
- se a locacao nao servir para o item, bloquear e avisar

## Tela 5 - Finalizacao

Objetivo: manter compatibilidade com a rotina atual.

```text
+-----------------------------------------------------------+
| Finalizar separacao                                       |
|                                                           |
| Informe o BOX                                             |
| [____________________]                                    |
|                                                           |
| [ Finalizar ]   [ Cancelar ]                              |
+-----------------------------------------------------------+
```

Comportamento:

- chamar `/est/est002/form_func-ajax/est002SeparacaoPorLocacaoContinuar/`
- manter `EST002->SeparacaoPedido` como regra oficial

## Funcionalidades da V1

- login do operador
- fila de pedidos pendentes
- abertura do pedido
- lista de itens pendentes
- selecao de item
- leitura de locacao
- validacao da locacao no item
- lancamento da quantidade separada
- gravacao da separacao
- finalizacao do pedido

## Funcionalidades previstas para fase seguinte

- leitura direta do produto por barcode
- divergencia durante a separacao
- separacao acelerada por locacao
- historico por operador
- indicador visual de progresso por item e pedido
- modo offline ou fila local, se um dia houver necessidade
