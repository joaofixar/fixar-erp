# Task 25 — Obtenção e armazenamento do PDF oficial GNRE

Concluída e validada em homologação em 02/11/2026. A implementação inicial não realizou chamadas ao Portal; a validação manual posterior foi autorizada e executada exclusivamente na guia #19. As guias #8, #9, #13, #14 e #15 não foram consultadas, transmitidas ou alteradas.

## Modelo e operação oficial

O único documento implementado é o **PDF oficial retornado pelo Portal Nacional GNRE**. A ação explícita usa `GnreResultadoLote/consultar`, com `incluirPDFGuias=S` e `incluirArquivoPagamento=N`; a consulta comum da fila continua com `incluirPDFGuias=N`.

`GNRE008` é uma tabela imutável exclusiva para esse binário. Ela guarda empresa, guia, tentativa `GNRE004`, recibo, tipo/origem, conteúdo original, MIME, tamanho, SHA-256, data/hora e usuário solicitante. A chave única `(GNRE004_Id, SHA256)` torna a repetição do mesmo documento idempotente. Não há binário em XML, SOAP, `GNRE004` ou `GNRE005`.

## Segurança e limites

Antes da chamada, a guia deve pertencer à empresa GNRE ativa e o SOAP de resultado já persistido em `GNRE005` deve ter exatamente uma guia individual com `situacaoGuia=0`. A nova resposta também é validada com esse mesmo critério. Base64 estrito, assinatura `%PDF-`, MIME `application/pdf`, SHA-256 e máximo de 10 MiB são obrigatórios; qualquer erro não grava nem remove documento existente.

Um lock local por empresa/guia evita consultas concorrentes e o insert idempotente protege a persistência. O download revalida sessão, empresa ativa, guia e documento pela junção de `GNRE008` com `GNRE001`; responde como attachment, `application/pdf`, `nosniff` e sem cache compartilhado.

## Interface e validação manual

Uma guia aprovada sem documento mostra **Obter PDF oficial**, com confirmação e desativação do botão; após armazenar, mostra **Baixar PDF oficial** e a data/hora de obtenção. Guias não aprovadas não oferecem ações de PDF.

Validação manual real em homologação concluída: a guia **#19**, com recibo `26000000045967`, tinha resultado individual aprovada/emitida. Pela interface foi executada a ação **Obter PDF oficial**; o Portal exibiu o número de controle `202690001313411`, retornou o PDF oficial e o documento foi aberto e visualizado com sucesso.

O registro persistido foi `GNRE008_Id=1`, empresa `3`, guia `19`, tentativa `GNRE004_Id=7`, tipo `PDF_OFICIAL`, origem `PORTAL_GNRE`, MIME `application/pdf`, tamanho `10333` bytes, SHA-256 `2a183e7943c442091d334c00998d5904e61eda4efa466ccd72bbe5b41b6a6b40` e data/hora de obtenção `2026-11-02 15:01:49`.

O PDF apresenta as marcas **“Ambiente de Teste”** e **“Guia Inválida para Pagamento”**, esperadas para homologação. A mensagem **“QR Code não emitido por falha sistêmica”** pertence ao documento retornado pelo Portal de testes e não caracteriza falha do ERP. Não houve retransmissão, alteração fiscal, mudança em XML, fila ou consulta das demais guias.

## Testes

`tests/task25-official-pdf-synthetic.php` cobre aceitação de PDF válido, bloqueio por situação diferente de `0`, base64 inválido, conteúdo não-PDF e excesso de tamanho. A implementação também contém isolamento de empresa na obtenção/download, lock por guia e chave única para concorrência/idempotência. Lint PHP e `git diff --check` foram executados; não houve Portal ou SQL no `e998`.
