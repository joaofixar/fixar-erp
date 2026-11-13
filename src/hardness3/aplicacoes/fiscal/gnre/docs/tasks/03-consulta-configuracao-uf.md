# Task 03 — Consulta de configuração da UF

**Situação:** concluída e validada em 28/10/2026.

## Escopo realizado

- Criado cliente SOAP próprio do módulo em `src/ConfigUfClient.php`, sem
  includes, classes, métodos ou contratos de NF-e.
- A tela GNRE ganhou uma consulta separada de configuração da UF. Ela recebe UF
  e, opcionalmente, receita; usa somente homologação e mantém o resultado apenas
  na requisição corrente. Não altera a guia aberta e não cria cache, tabela,
  campo ou configuração persistente.
- A rota continua revalidando sessão ERP, empresa ativa e permissão GNRE
  `C029_Id=310` por meio do bootstrap. A ação POST exige CSRF.
- O certificado A1 é obtido da empresa ativa no ERP e convertido somente em
  arquivo temporário com permissão 0600 durante a chamada; ele é removido ao
  fim da requisição. Senha, chave e conteúdo do certificado não são exibidos ou
  armazenados pelo módulo.

## WSDL, endpoint e contrato SOAP

- Endpoint: `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF`
- Ambiente exclusivo: homologação (`ambiente=2`). Não há endpoint de produção
  no código da task.
- WSDL obtido em 28/10/2026 e armazenado em
  `docs/referencias/wsdl/GnreConfigUF-homologacao.wsdl`.
- SHA-256: `a9c5513df28e4d0b7248de8530737e917e3202e4e94f6328c27c8016142a0eed`.
- Contrato implementado: SOAP 1.2, Document/Literal, operação síncrona
  `consultar`, ação
  `http://www.gnre.pe.gov.br/webservice/GnreConfigUF/consultar`, cabeçalho
  `gnreCabecMsg/versaoDados=1.00`, corpo `gnreDadosMsg/TConsultaConfigUf` e
  resposta `gnreRespostaMsg/TConfigUf`.

O endpoint exige certificado cliente inclusive no WSDL. Em 28/10/2026, o
certificado do servidor foi emitido para `*.sefaz.pe.gov.br`, embora o endpoint
oficial use `www.testegnre.pe.gov.br`. Para não aceitar certificados
arbitrários, o cliente mantém validação de cadeia e fixa a chave pública TLS
observada (`sha256//qzVnuUkaTpHigHbsUuGcvXOEz87ediZfEdpCPq3/wH8=`). A renovação
do certificado do Portal exigirá atualização assistida do pin e nova captura do
WSDL; esta limitação é deliberada e não há desativação silenciosa de TLS.

## Arquivos alterados

- `aplicacoes/fiscal/gnre/src/ConfigUfClient.php`
- `aplicacoes/fiscal/gnre/public/index.php`
- `aplicacoes/fiscal/gnre/docs/referencias/wsdl/GnreConfigUF-homologacao.wsdl`
- `aplicacoes/fiscal/gnre/docs/referencias/README.md`
- `aplicacoes/fiscal/gnre/docs/PROJECT_STATUS.md`
- `aplicacoes/fiscal/gnre/docs/tasks/03-consulta-configuracao-uf.md`

Não houve migration nem alteração em `GNRE001` ou `GNRE002`.

## Testes realizados

- Certificado A1 da matriz de teste do `e998` validado localmente: chave privada
  disponível e validade até 18/09/2027.
- WSDL capturado autenticadamente no endpoint oficial de homologação e hash
  conferido contra o arquivo salvo.
- Cliente do módulo executado no PHP 7.4 do ambiente `e998`, para UF `PR`:
  conexão TLS mútua, SOAP 1.2, cabeçalho, ação e leitura da resposta validados.
  O Portal devolveu `TConfigUf`, ambiente `2`, UF `PR`, situação `102`:
  certificado/CNPJ ainda não habilitado para uso do serviço.
- O certificado A1 substituído na empresa `C004_Id=6` também foi validado
  localmente (válido até 30/03/2027) e testado para `PR` em homologação. O
  Portal retornou novamente situação `102` para o CNPJ `17750961000116`.
- O certificado A1 da FSC Tecnologia (`C004_Id=3`) foi validado localmente
  (válido até 13/02/2027) e testado para `PR` em homologação. O Portal retornou
  inicialmente situação `102` para o CNPJ `41660477000180`. Após a habilitação
  confirmada pelo Portal, a mesma consulta retornou situação `450` — “Consulta
  da Configuração efetuada com sucesso!” — e 11 receitas para a UF PR.
- Lint PHP dos arquivos modificados no PHP do ambiente e `git diff --check`.

## Roteiro de teste manual

1. Habilite no Portal GNRE o CNPJ do certificado A1 da empresa de homologação.
2. Entre no ERP, selecione essa empresa e um perfil com a permissão 310.
3. Abra `aplicacoes/fiscal/gnre/public/index.php` e, em **Consultar configuração
   da UF (homologação)**, informe uma UF participante; informe receita apenas se
   desejar o filtro.
4. Confirme que a tela mostra o código e a descrição retornados e que o XML
   técnico aparece apenas de modo escapado no detalhe expansível.
5. Recarregue a página e confirme que o resultado sumiu; confira também que
   nenhuma guia, documento ou tabela foi criada/alterada.
6. Tente sem permissão, com outra empresa ou sem CSRF: a consulta deve ser
   bloqueada antes de acessar o Portal.

## Limitações e pendências

- O certificado da FSC Tecnologia (`C004_Id=3`) está habilitado apenas em
  homologação. A habilitação de produção não foi solicitada nem é necessária
  para esta task.
- A apresentação mostra o `TConfigUf` técnico escapado e temporário para apoiar
  o cadastro manual; ela não transforma regras retornadas em validação da guia.
- Não foram implementados XML de guia, validação de XSD de guia, transmissão,
  recibo, PDF, fila, cache ou integração NF-e.

## Validação e condição para iniciar a Task 04

O usuário validou a consulta manual positiva na tela GNRE, com a empresa FSC
Tecnologia ativa e CNPJ habilitado em homologação, e autorizou expressamente o
início da Task 04.
