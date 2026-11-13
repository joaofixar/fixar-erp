# Task 24 — Análise de documento oficial e PDF da GNRE

**Situação:** concluída analiticamente em 02/11/2026. Não houve implementação, migration, geração de arquivo, consulta/transmissão ao Portal ou alteração da guia #19.

## Decisão objetiva

A alternativa principal é obter e preservar o **PDF oficial fornecido pelo Portal Nacional GNRE** no `GnreResultadoLote`, mediante consulta explícita com `incluirPDFGuias=S`, somente após confirmação individual de aprovação (`situacaoGuia=0`).

Fallback: se o Portal estiver indisponível antes de o PDF ser obtido, a guia permanece emitida, mas a interface informa “documento oficial pendente de obtenção” e permite nova tentativa controlada. PDF feito pelo ERP só pode existir, mediante decisão futura de produto, como **representação gerada pelo ERP**; não substitui o documento oficial nem deve ser chamado de PDF oficial.

Não foi identificado endpoint, URL, payload ou operação oficial autônoma de impressão. Portanto, redirecionamento para impressão no Portal não possui contrato para implementação nesta etapa.

## Evidência oficial confirmada

Foram examinados o Manual GNRE v2.17 local (revisão 03/09/2026), os XSDs em `docs/referencias/gnre-v2.17/` e WSDLs de homologação em `docs/referencias/wsdl/`. Pesquisa externa não foi necessária e não houve chamada ao Portal.

| Tema | Evidência e conclusão |
| --- | --- |
| Serviço oficial | Manual item 4.2, pp. 22--25: `GnreResultadoLote`, operação síncrona `consultar`, retorna o resultado do lote. O WSDL de homologação tem essa única operação, ação `http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar`. |
| Endpoint | Manual p. 12: homologação `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote`; produção `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote`. Produção não foi chamada. |
| Pedido | Manual 4.2.1 p. 23 e `lote_gnre_consulta_v1.00.xsd`: `TConsLote_GNRE/incluirPDFGuias` aceita `S`, `N` ou omissão. Com `S`, pede PDF base64 de guias processadas com sucesso; a própria fonte alerta que a consulta é mais lenta. |
| Retorno | Manual 4.2.2 p. 24 e `lote_gnre_result_v2.00.xsd`: `resultado/pdfGuias` opcional contém o PDF das guias bem-sucedidas em base64, a converter para binário. Não há URL, token de download ou PDF por guia documentado. |
| Dados oficiais | O retorno prevê dados da guia, `nossoNumero`, `linhaDigitavel` (48), `codigoBarras` (44) e, quando aplicável, `qrcodePayload`. Esses dados não equivalem ao PDF: `pdfGuias` é elemento opcional próprio. |
| Limites | Recepção emite/processa lote de modo assíncrono; `GnreResultadoLote` consulta seu recibo. Os serviços de consulta de guia localizam dados por código/controle/documento, mas a documentação não lhes atribui PDF/impressão. |

O código atual em `src/ResultClient.php` fixa `<incluirPDFGuias>N</incluirPDFGuias>`. Isso explica a ausência de PDF no resultado armazenado; não é prova de inexistência do recurso oficial. O limite atual de resposta de 2 MiB também requer revisão antes de receber PDF.

## SOAP persistido da guia #19, com dados mascarados

Foi feita leitura somente da última resposta `GNRE005` da guia #19, sem imprimir XML ou dados pessoais, fiscais ou financeiros. Hash SHA-256 do SOAP: `bae0151812bd4e1c77d9764b272d18f9188a3242ece6e892f0e49a1713af1763`.

Confirmados: recibo `26000000045967`; ambiente; situação do lote; uma `guia` com `situacaoGuia=0`; UF favorecida; receita; tipo; itens e valores GNRE; vencimento; pagamento; limite de pagamento; identificações de emitente/destinatário; documento de origem; `nossoNumero`; `linhaDigitavel`; e `codigoBarras`.

Ausentes: `pdfGuias`, `arquivoPagamento`, `qrcodePayload`, `representacaoNumerica`, `valorPrincipal` e `valorTotal` com esses nomes específicos. Os valores existem na estrutura retornada por outros campos, mas foram deliberadamente omitidos desta documentação. A ausência de PDF decorre da consulta com `N` e não permite concluir que o Portal não o fornece.

## Avaliação das alternativas

| Alternativa | Natureza e uso | Dependências / persistência | Riscos e homologação |
| --- | --- | --- | --- |
| PDF pelo resultado do lote | **Documento oficial fornecido pelo Portal**. Melhor opção para download, impressão e pagamento. | A1/mTLS, recibo e Portal apenas na obtenção; depois, binário imutável, MIME, tamanho, hash, recibo, tentativa/origem e data/hora. | Resposta lenta/grande; PDF pode ser de lote. Validar base64, `%PDF-`, tamanho e vínculo/ordem das guias. |
| Redirecionar para Portal | Poderia resultar em impressão oficial, mas não há URL/contrato comprovado. Não anunciar como integração. | Possível sessão/certificado e disponibilidade contínua; não há auditoria local. | Alto acoplamento à UI. Só considerar com fonte oficial ou homologação manual estável e autorizada. |
| Representação ERP | **Representação gerada pelo ERP**, nunca original/oficial; apenas consulta interna se aprovada no futuro. | Dados oficiais, versão de leiaute, hash e origem do snapshot. | Pode divergir de layout, barcode ou dados oficiais. Não gerar automaticamente enquanto o PDF oficial for recuperável. |

## Arquitetura recomendada para task futura

1. Liberar a obtenção apenas se o SOAP persistido for válido e confirmar `situacaoGuia=0`; `F`/`FINALIZADA` sozinhos não bastam.
2. Criar ação explícita e idempotente “Obter documento oficial”, que consulta o mesmo recibo com `incluirPDFGuias=S` e `incluirArquivoPagamento=N`. Não retransmite nem altera dados da guia e não roda em massa pelo worker atual.
3. Validar SOAP final, base64 estrito, tamanho próprio e cabeçalho PDF. Para lote com mais de uma guia, não atribuir automaticamente PDF multi-guia sem regra homologada.
4. Preservar o binário original sem transformação, SHA-256, MIME, tamanho, origem `PORTAL_GNRE`, recibo, `GNRE004` origem, data/hora e auditoria de usuário/ação. O acesso deve validar empresa pela junção `GNRE004 -> GNRE001`, nunca por ID recebido no navegador. Download: attachment, `nosniff` e sem cache compartilhado.
5. Reimpressão serve o binário preservado, sem Portal. Se ausente e o Portal falhar, registrar erro técnico e manter “guia emitida; documento oficial pendente”, sem fallback silencioso.
6. Rótulos obrigatórios: “Documento oficial fornecido pelo Portal Nacional GNRE” para binário retornado; “Representação gerada pelo ERP — não substitui o documento oficial do Portal” para eventual PDF próprio.

### Persistência mínima indispensável

O documento original, auditável e reimprimível exige armazenamento próprio. Recomenda-se tabela nova (nome a decidir, por exemplo `GNRE008`), sem sobrecarregar `GNRE004`/`GNRE005`, que são trilhas de transmissão/consulta. Mínimo: ID, `GNRE004_Id`, tipo/origem, MIME, binário ou referência privada equivalente, SHA-256, tamanho, recibo, data/hora e auditoria; índice único idempotente por tentativa/recibo e hash. Não criar essa estrutura nesta task.

Se surgir armazenamento privado comum com imutabilidade e autorização equivalente, referência a objeto poderá substituir BLOB. Nenhum padrão fiscal independente já pronto foi localizado; a GNRE não deve acoplar-se à NF-e.

## Infraestrutura do repositório

- `bibliotecas/composer/composer.json` declara TCPDF 6.3 e `picqer/php-barcode-generator` 2.2, com `vendor/autoload.php`; também há TCPDF e QR-code locais. São adequados somente a eventual representação ERP em componente GNRE próprio, não para recriar o documento oficial.
- `GNRE004` já preserva XML enviado e SHA-256; `GNRE005` preserva SOAP. Esse padrão orienta a auditoria, mas não contém o binário oficial.

## Mínimo da futura implementação

- migration incremental da persistência descrita; cliente GNRE específico para a consulta com PDF; parser/validador; armazenamento e download autenticado por empresa; interface de obtenção/reimpressão/estado;
- testes para `S`/`N`, base64/PDF inválido ou grande, guia não aprovada, idempotência, isolamento de empresas, reimpressão sem Portal e indisponibilidade; regressão da fila;
- homologação manual usando #19 **sem alterar seus dados**: uma consulta com PDF, validação de PDF legível e compatível com identificadores já guardados, armazenamento de hash e reimpressão sem segunda chamada. Confirmar quantidade de guias no PDF do lote e associação segura.

Exigem homologação: consulta com `S` após finalização, tamanho real, formato/quantidade de `pdfGuias`, lote multi-guia e comportamento em produção. Campo, endpoint e base64 estão confirmados na documentação; não foram testados na prática nesta task.

## Registro da task

Foram alterados apenas este arquivo e `docs/PROJECT_STATUS.md`. Não houve PDF gerado, tabela/campo novo, alteração em `GNRE004`/`GNRE005`, mudança em #19, chamada ao Portal ou início de task posterior.
