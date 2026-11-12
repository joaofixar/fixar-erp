# Decisoes Praticas

## Campos minimos para `VEN001->incluirProduto($form)`

O contrato sera revisado em detalhe antes da Fase 7. Pela leitura atual, os campos minimos do `GFormWrap` para o fluxo de importacao devem incluir:

- `T004_D009_Id`
- `T004_T003_Id`
- `T004_Quantidade`
- `T004_Valor_Preco_Sem_Desconto_Unitario`
- `T004_D037_Id`
- `T004_D005_Id`
- `T004_Descricao_Produto`
- `T004_Codigo_Produto`
- `T004_D006_Id`
- `T004_Flag_Destino_Produto`
- `T004_Codigo_Substituto`
- `T004_Numero_Pedido_Compra`
- `T004_Item_Pedido_Compra`
- `T004_Observacao`
- `T004_Observacao_Entrega`
- `T004_Aliquota_IPI`
- `T004_Percentual_Desconto`
- `T004_Codigo_Local`
- `T004A_T066_Id`
- `T004A_Data_Entrega`
- `T004A_Quantidade_Conversao`
- `T004A_Observacao_Interna`
- `T004A_Codigo_Obra`
- `idVeiculoOrcamento`

Campos opcionais podem ser enviados vazios conforme o comportamento do form atual.

## Busca de produtos

No MVP, a busca normalizada e feita por cache em arquivo JSON gerado a partir de `D001`.

Atualizacao:

- rebuild inicial quando o cache nao existir;
- renovacao automatica por TTL;
- opcao de rebuild manual quando necessario.

## Limites recomendados

- PDF: 20 MB por arquivo na V1.
- Itens: ate 300 itens por lote na V1.
- Sugestoes salvas: top 10 por item.
- Candidatos de descricao: top 50 antes do score final.

Acima desses limites, o lote deve ser recusado com mensagem clara ou processado apenas quando o modo assincrono estiver habilitado.

## Execucao

V1 pode processar de forma sincrona para PDFs pequenos.

Para volumes maiores, o processo de inclusao roda em etapas com controle de progresso e idempotencia no proprio fluxo.
