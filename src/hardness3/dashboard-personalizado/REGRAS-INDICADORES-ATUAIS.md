# Regras dos indicadores atuais

Este documento preserva as regras de negócio definidas para os indicadores personalizados atuais. Ele deve ser usado como referência ao migrar estes indicadores para o futuro motor genérico de indicadores por SQL.

## Regras comuns para NF-e válidas

Quando a regra mencionar NF-e de saída autorizada, considerar:

- empresa da sessão em `T007_C004_Id`;
- `T007_Flag_Entrada_Saida = 'S'`;
- nota emitida, e não recebida: `T007_Flag_Emitida_Recebida != 'R'`;
- protocolo da NF-e preenchido;
- nota não cancelada;
- nota não inutilizada;
- nota não denegada;
- período baseado em `T007_Data_Emissao`;
- filtros adicionais sempre aplicados ao card, gráfico e listagem da mesma forma.

## Indicador de frete

### Frete nas notas

- Fonte: NF-e, tabela `T007`.
- Considerar somente NF-e de saída autorizadas.
- Considerar somente notas com `T007_Valor_Frete > 0`.
- Valor do indicador: soma de `T007_Valor_Frete`.
- Quantidade: número de notas com frete.
- Frete médio: média de `T007_Valor_Frete`.
- Filtros: emissão inicial, emissão final e cliente.
- Gráfico: soma mensal de `T007_Valor_Frete`, usando os mesmos filtros.

### Frete grátis — custo

- A empresa concede frete grátis em NF-e cujo valor total seja superior a R$ 4.000,00.
- A NF-e não deve possuir frete cobrado do cliente: `T007_Valor_Frete <= 0`.
- Fonte do custo: CT-e de entrada válido, tabelas `T084` e `T085`.
- O vínculo correto entre CT-e e NF-e é `T085_T084_Id` e `T085_T007_Id`.
- Não relacionar documentos apenas pelo número textual da nota quando existe vínculo por ID.
- Considerar CT-e com protocolo, não cancelado e não denegado.
- Valor do custo: soma de `T085_Valor_Rateio`.
- Usar o rateio para evitar duplicar o valor integral de um CT-e ligado a mais de uma NF-e.
- Quantidades: CT-e distintos e NF-e distintas.
- Filtros: emissão da NF-e e cliente.
- Gráfico: custo rateado por mês de emissão da NF-e, usando os mesmos filtros.

## Faturamento versus custo

- Fonte: NF-e em `T007`, itens em `T008`, produtos em `D009` e natureza em `D006`.
- Considerar somente NF-e de saída autorizadas e de venda.
- Excluir devoluções.
- Excluir entradas e importações.
- A natureza deve possuir saída e classificação de venda.
- Faturamento: `T007_Valor_Total - T007_Valor_Total_IPI - T007_Valor_Frete`.
- Custo: custo manual do produto multiplicado pela quantidade faturada.
- Campo de custo manual: `D009_Valor_Preco_Tabela`.
- Markup: `((faturamento - custo) / custo) * 100`, quando o custo for maior que zero.
- Filtros: emissão inicial, emissão final, cliente e UF do destinatário.
- Gráfico: faturamento e custo manual por mês, usando exatamente a mesma base filtrada.

## Clientes inativos

- Fonte: histórico de NF-e de saída autorizadas em `T007`.
- Considerar somente clientes ativos em `D024`.
- O cliente precisa possuir histórico de compra; clientes que nunca compraram não pertencem à base.
- Referência: data final selecionada no filtro.
- Cliente inativo: última NF-e de venda anterior a seis meses da data de referência.
- Regra equivalente: `MAX(T007_Data_Emissao) < DATE_SUB(data_referencia, INTERVAL 6 MONTH)`.
- Resultado: quantidade de clientes inativos na referência.
- Listagem: cliente, última compra e dias sem comprar.
- Ordenação: compras mais antigas primeiro.
- Filtros: referência inicial e referência final.
- Gráfico: quantidade de clientes inativos em cada referência mensal do período.

## Vendas de amostra grátis

- Fonte das amostras: NF-e de saída autorizadas em `T007`.
- CFOP de amostra grátis: `5911` ou `6911`.
- Acompanhamento feito por NF-e de amostra e cliente.
- Procurar uma nova NF-e de venda autorizada do mesmo cliente.
- A recompra deve ocorrer depois da emissão da amostra e em até seis meses.
- A nota de recompra deve ser venda e não pode usar CFOP `5911` ou `6911`.
- Situações:
  - `Comprou no prazo`: encontrou recompra dentro dos seis meses;
  - `Aguardando prazo de recompra`: ainda não comprou, mas o prazo não terminou;
  - `Sem recompra — reenviar amostra`: prazo de seis meses terminou sem recompra.
- Destacar visualmente a última situação.
- Filtros: emissão inicial, emissão final e cliente.
- Listagem: cliente, número da NF-e, CFOP, emissão, prazo, recompra e situação.
- Gráfico: quantidade mensal de NF-e de amostra emitidas, usando a mesma listagem filtrada.

## Indicadores financeiros

### Títulos a receber em aberto

- Fonte: `T002`.
- Empresa da sessão.
- Título não cancelado.
- Título não agrupado.
- Sem data de recebimento.
- Período baseado na data de vencimento.
- Valor: `T002_Valor_Saldo`.

### Títulos a receber recebidos

- Fonte: `T002`.
- Empresa da sessão.
- Título não cancelado.
- Período baseado na data de recebimento.
- Valor: `T002_Valor_Recebido`.

### Títulos a pagar em aberto

- Fonte: `T015`.
- Empresa da sessão.
- Título não cancelado.
- Título não agrupado.
- Sem data de pagamento.
- Período baseado na data de vencimento.
- Valor: `T015_Valor_Saldo`.

### Títulos a pagar baixados

- Fonte: `T015`.
- Empresa da sessão.
- Título não cancelado.
- Período baseado na data de pagamento.
- Valor: `T015_Valor_Pago`.

### Regras comuns do financeiro

- Filtros: período e cliente.
- O card, a quantidade, o gráfico e a listagem devem usar exatamente as mesmas condições.
- O gráfico deve agrupar mensalmente pela data correspondente à aba.
- A listagem deve exibir pessoa, documento, data e valor.
- Títulos agrupados devem ser excluídos do card, gráfico e listagem das duas abas de títulos em aberto.

## Vendas por representante

- Fonte: NF-e em `T007`; não usar pedidos como resultado final.
- Considerar NF-e de saída autorizadas da empresa atual.
- Período baseado em `T007_Data_Emissao`.
- Valor: `T007_Valor_Total`.
- Vendedor da nota: vendedor externo quando informado; caso contrário, vendedor interno.
- Expressão: `COALESCE(NULLIF(T007_C007_Id_Vendedor_Externo, 0), T007_C007_Id_Vendedor_Interno)`.
- Filtros: emissão inicial, emissão final, nome do cliente e vendedor.
- O filtro de cliente é textual e pesquisa razão social ou nome fantasia.
- O seletor de vendedores deve listar somente usuários ativos vinculados à empresa atual em `C019`.
- Excluir usuários vinculados a cadastro de cliente em `C007_D024_Id`.
- Visão geral: total por vendedor no período filtrado.
- Visão individual: evolução mensal somente do vendedor selecionado.
- A listagem deve exibir NF-e, emissão, cliente, vendedor e valor.
- Card, gráfico, totais por vendedor e listagem devem compartilhar a mesma condição filtrada.

## Visão geral

- Deve apresentar todos os indicadores habilitados para a empresa.
- Indicador de frete deve mostrar separadamente frete nas notas e frete grátis — custo.
- Financeiro deve mostrar receber em aberto, recebidos, pagar em aberto e pagos.
- Cada cartão deve levar ao detalhamento do indicador correspondente.
- A visão geral herda os resultados calculados pelos indicadores e não deve criar fórmulas alternativas.

## Regras para o futuro motor genérico

- Cada indicador personalizado deve possuir uma única consulta-base.
- Card, gráfico e listagem devem ser derivados dessa mesma consulta-base.
- O SQL deve aceitar somente leitura e utilizar parâmetros preparados.
- `:empresa` deve ser obrigatório e preenchido exclusivamente pela sessão.
- Filtros definidos na personalização devem atualizar todos os componentes do indicador.
- As regras deste documento devem ser migradas para a personalização do cliente que solicitou estes indicadores, sem permanecer como regra global para todas as empresas.
