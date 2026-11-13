# Task 04 — Geração e validação local do XML GNRE v2.00

**Situação:** concluída e validada em 28/10/2026.

## Escopo realizado

- Criado `GnreXmlGenerator`, exclusivo do módulo GNRE, para formar um único
  `TLote_GNRE` v2.00 a partir de uma guia manual e seus itens de recolhimento.
- A validação usa somente `DOMDocument::schemaValidate()` com
  `docs/referencias/gnre-v2.17/lote_gnre_v2.00.xsd`; os `include` dos XSDs são
  resolvidos no próprio diretório local. Não existe chamada HTTP, SOAP, Portal,
  PDF, recibo, fila, cache ou código NF-e nesta task.
- A tela permite cadastrar documentos de origem e itens de recolhimento, gerar
  o XML apenas em memória e apresentar o XML ou os erros locais de validação.
- Todas as ações passam pelo bootstrap: sessão ERP, empresa ativa, permissão
  `C029_Id=310` e CSRF nos POSTs.

## Modelo incremental

Foi criada `GNRE003`, pois a guia e os documentos existentes não continham os
dados variáveis necessários para formar cada `item` do XML. É uma relação 1:N
com `GNRE001` e N:1 opcional com `GNRE002`:

- `GNRE003_GNRE001_Id`: guia proprietária, obrigatório.
- `GNRE003_GNRE002_Id`: documento de origem, opcional para apuração sem
  documento. Um documento pode originar vários recolhimentos.
- Receita, detalhamento, tipo e valor: representam o recolhimento (ICMS,
  FECP, multa, juros etc.), nunca uma linha de produto da NF.
- Tipo do documento, produto GNRE, período/referência, destinatário e campos
  extras JSON: dados genéricos previstos no XML, sem campo específico por UF.
  A coluna é `TEXT`, não o tipo nativo `JSON`, para manter compatibilidade com
  MySQL 5.4; seu conteúdo é validado pela aplicação antes de gerar o XML.

O total em `GNRE001_Valor` é conferido contra a soma de `GNRE003_Valor` antes
da geração. O XML usa tipo 0 para um item, tipo 1 para múltiplos documentos de
origem e tipo 2 para múltiplos recolhimentos do mesmo documento/apuração.

## Arquivos alterados

- `scripts/atualizacaoBase.php`
- `aplicacoes/fiscal/gnre/src/XmlGenerator.php`
- `aplicacoes/fiscal/gnre/public/index.php`
- `aplicacoes/fiscal/gnre/docs/PROJECT_STATUS.md`
- `aplicacoes/fiscal/gnre/docs/tasks/04-geracao-validacao-xml.md`

## Migration

Execute o procedimento usual do ERP para `scripts/atualizacaoBase.php`. A nova
tabela idempotente é `GNRE003`; não há alteração de `GNRE001` nem `GNRE002`.
Ela não possui chave estrangeira física, seguindo o padrão do ERP, mas a tela
impede remover um documento de origem que já esteja associado a um item.

## Testes executados

- `php -l` em `public/index.php`, `src/XmlGenerator.php` e bootstrap.
- `git diff --check`.
- No PHP 7 do ERP, geração de lote com dois itens (ICMS principal e FECP),
  total R$ 102,00: XML de 1.045 bytes validado com sucesso contra os XSDs
  locais.
- Teste negativo no mesmo runtime: período `9` foi recusado pelo XSD local,
  que aceita exclusivamente `0` a `5`.

O PHP CLI do host não possui a extensão DOM; por isso os testes XSD foram
executados no container PHP do ERP, que tem `dom`, `libxml` e `mysqli`.

## Roteiro manual

1. Aplique a atualização-base e entre com empresa ativa e perfil liberado para
   a permissão 310.
2. Abra uma guia rascunho, confira UF, vencimento e valor total; inclua um ou
   mais documentos de origem se houver.
3. Inclua itens de recolhimento. Para apuração, deixe o documento vazio. Para
   documento de origem, informe o tipo de documento exigido pela receita.
4. Faça a soma dos itens coincidir com o total da guia e acione **Gerar e
   validar XML v2.00**. Confirme a mensagem de XML válido e revise o conteúdo.
5. Informe deliberadamente período `9` e tente gerar: a tela deve mostrar o
   erro do XSD local. Restaure um período de `0` a `5`.
6. Confirme que nenhuma chamada ao Portal, recibo, PDF, fila ou XML persistido
   foi criado.

## Limitações e condição para Task 05

- O XSD garante o leiaute, mas regras de obrigatoriedade por UF/receita serão
  avaliadas pelo Portal na futura transmissão. A consulta de configuração da
  UF continua apenas informativa.
- O formulário aceita o município com cinco dígitos porque esse é o padrão do
  XSD GNRE local, ainda que integrações internas possam usar códigos IBGE em
  outro formato.
- Não existe vínculo real nem leitura de NF-e; a futura automação deverá
  preencher os mesmos `GNRE002`/`GNRE003`, sem reutilizar código NF-e.

A Task 05 só poderá começar após o usuário validar manualmente a geração e
validação local desta task. Ela deverá criar a tabela de tentativa de
transmissão (`GNRE004`) e nada além da recepção/persistência do recibo. A fila
da Task 06 passará a ser `GNRE005`.

## Validação manual concluída

Em 28/10/2026, o usuário criou a guia manual `GNRE001_Id=2` para a empresa FSC
Tecnologia, UF RJ, receita `100211`, vencimento `2026-10-28` e total R$
5.000,00. Foi criado o item de recolhimento `GNRE003_Id=1`, sem documento de
origem (apuração), tipo de valor `11` e valor R$ 5.000,00. A tela gerou o
`TLote_GNRE` v2.00 e exibiu “XML válido contra os XSDs locais.”

Não houve chamada ao Portal nem criação de recibo, PDF, fila ou XML persistido.
Task 05 permanece pendente e só será iniciada sob nova autorização do usuário.
