# Referências oficiais GNRE

## Pacote v2.17

Origem: material obtido pelo usuário no Portal GNRE e incorporado ao repositório
em 28/10/2026. O PDF identifica a versão **2.17**, setembro de 2026, com revisão
de 03/09/2026.

O pacote é a referência do contrato técnico inicial. Antes de alterar cliente
SOAP, schemas ou limites, conferir se o Portal publicou uma versão posterior.
O conteúdo não substitui a validação fiscal de receita, UF, vencimento ou campos
adicionais.

### Arquivos e SHA-256

| Arquivo | SHA-256 |
| --- | --- |
| `Manual_de_Integracao_Contribuintes_GNRE_v2.17.pdf` | `0bb65994243467c820e60c985002ddff96d99fb8aa4879bf9eeef82e6ed56c91` |
| `cabecMsg_v1.00.xsd` | `2f0969b768cf408c02d00bfe5a9398f85803d87010275f79d77780c800ba7914` |
| `config_uf_v1.00.xsd` | `d77c6d36b548a63b4f06842589e1c0e37d8c9b8bd0f6abbad60ce7f226f437fb` |
| `consulta_config_uf_v1.00.xsd` | `505142808ab777e4b6b3f92f62dc5465f08ab059d82b66647483de7d8375358e` |
| `dados_gnre_v1.00.xsd` | `a53ea134d1feca9d4ee6a373f1cf77aa430929158d03b16aeb1ce232b4579949` |
| `dados_gnre_v2.00.xsd` | `24da62357a879e9d745fbe961b5804f00fd12693dc6a79b88baa2505ebe86e04` |
| `lote_consulta_guia_v2.00.xsd` | `50fbb59bf7af41396c7ad8247452d1018caebb2a1d47a42f58707348296e685d` |
| `lote_gnre_consulta_v1.00.xsd` | `28f39ed98181a2ce36af361b7aa55235117ac5541827c1d8e4b0a155a27e342d` |
| `lote_gnre_recibo_v1.00.xsd` | `2a6282ec7bf7acfd018b796f5944db1f34acb6a9e24425c20c3f94b9681f52fb` |
| `lote_gnre_result_consulta_v2.00.xsd` | `533c3f1d1cfbc2538557030444114a77d6ccbdf90037cdf2e71fdbf9fa55d5dc` |
| `lote_gnre_result_v1.00.xsd` | `583ffbea8cadd755e1764e9c6a3f35da3d27c9c03de828e65eec7619ffe5c814` |
| `lote_gnre_result_v2.00.xsd` | `cd831382f4136af9ac2307974d440e6905980204989f78ebb31ad3b5caffff19` |
| `lote_gnre_v2.00.xsd` | `c985125b196d5cea220adea5bf2c00e86924cebf7e29831050e04fc9f6045e31` |
| `retorno_gnre_v1.00.xsd` | `6ba0491be568f23953e03c8ab369822c2e3199f6bede32135146051d39855827` |
| `retorno_gnre_v2.00.xsd` | `668ecfe2227d7b6c244841de49028aee9fc4be44cc48215517063858b41723d2` |
| `tiposBasicoGNRE_v1.00.xsd` | `002024c9db0bbb534d9bc54511d7cb676de4889ee92c58e7a1645ec9caa1e49d` |
| `tiposBasicoGNRE_v2.00.xsd` | `9307d6c80bc5824f4c643e11f9bcafb6d8959260f91ae61b09ebae45abae23cc` |

## Endpoints documentados

Os URLs abaixo vêm da seção 4 do manual. A Task 03 deve consultar os WSDLs de
homologação e registrar seu conteúdo e hash antes de usar o cliente SOAP.

| Serviço | Homologação |
| --- | --- |
| Recepção de lote | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` |
| Resultado do lote | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote` |
| Recepção de consulta | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcaoConsulta` |
| Resultado de consulta | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLoteConsulta` |
| Configuração de UF | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF` |

Produção usa o mesmo caminho sob `https://www.gnre.pe.gov.br/`.

## WSDL capturado — GnreConfigUF em homologação

- Endpoint: `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF?wsdl`
- Ambiente: homologação (`ambiente=2`)
- Obtido em: 28/10/2026, com certificado cliente A1 da empresa de teste
  configurada no ERP; o arquivo não contém certificado, senha ou chave privada.
- Artefato: `wsdl/GnreConfigUF-homologacao.wsdl`
- SHA-256: `a9c5513df28e4d0b7248de8530737e917e3202e4e94f6328c27c8016142a0eed`

O servidor exige autenticação mútua também para baixar o WSDL. Na data da
captura, o hostname oficial apresentou certificado para `*.sefaz.pe.gov.br`.
O cliente GNRE não aceita certificados arbitrários: mantém a validação da
cadeia e fixa a chave pública TLS observada. A troca de certificado do Portal
exige revisão explícita desta referência e do pin antes de nova consulta.

## WSDL capturado — GnreLoteRecepcao em homologação

- Endpoint: `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao?wsdl`
- Obtido em 28/10/2026 com o A1 homologado da FSC Tecnologia.
- Artefato: `wsdl/GnreLoteRecepcao-homologacao.wsdl`
- SHA-256: `2c376abd5d007b377504ee494f89fd34a11ddd0fffad7ae0fb484104aa3ec308`.
- Contrato: SOAP 1.2, Document/Literal, `processar`, ação
  `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao/processar`, cabeçalho
  `gnreCabecMsg/versaoDados=2.00` e corpo `gnreDadosMsg` com `TLote_GNRE`.

O hostname segue divergente no certificado do Portal. O cliente preserva
validação de cadeia e o pin `sha256//qzVnuUkaTpHigHbsUuGcvXOEz87ediZfEdpCPq3/wH8=`;
a exceção explícita restringe-se à comparação de hostname.

## WSDL capturado — GnreResultadoLote em homologação

- Endpoint: `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote?wsdl`
- Obtido em 28/10/2026 com A1 homologado da FSC Tecnologia.
- Artefato: `wsdl/GnreResultadoLote-homologacao.wsdl`
- SHA-256: `f01a85af07f807eaa1c6757208f5779b9d87d8e69868a6360ea47f1c2aba1e0c`.
- Contrato: SOAP 1.2, Document/Literal, `consultar`, ação
  `http://www.gnre.pe.gov.br/webservice/GnreResultadoLote/consultar`, cabeçalho
  `gnreCabecMsg/versaoDados=2.00` e corpo `gnreDadosMsg` com `TConsLote_GNRE`.

## Limites relevantes para o módulo

- SOAP 1.2, Document/Literal e TLS 1.2 com autenticação mútua.
- Certificado ICP-Brasil A1 ou A3, com CNPJ do responsável pela transmissão e
  permissão de autenticação de cliente. O MVP será homologado com A1.
- Recepção de lote é assíncrona: persiste recibo e consulta resultado depois.
- Limite do cliente: 500 chamadas/minuto/IP; respeitar 120 ms entre chamadas,
  HTTP 429 e o cabeçalho `Retry-After`.
