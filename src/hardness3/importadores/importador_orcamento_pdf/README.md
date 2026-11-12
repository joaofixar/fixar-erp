# Importador de Orcamento por PDF

Modulo isolado para importar itens de PDF recebido do cliente para o orcamento.

## Decisao de arquitetura

- O modulo fica em `importadores/importador_orcamento_pdf`.
- A tela e os assets sao proprios, sem montagem visual padrao do FW.
- No MVP atual, o estado da importacao e persistido em arquivos JSON no diretorio de dados do usuario.
- A busca de produtos utiliza cache em arquivo JSON, gerado a partir de `D001`.
- A inclusao final no orcamento deve usar `VEN001->incluirProduto($form)`.
- Nao fazer insert direto em `T004`.

## Fases do MVP

1. Estrutura base e entrada pelo orcamento.
2. Upload seguro de PDF.
3. Extracao de texto e itens.
4. Matching e sugestoes.
5. Revisao do vendedor.
6. Insercao no orcamento.
7. Finalizacao, logs e retencao.
