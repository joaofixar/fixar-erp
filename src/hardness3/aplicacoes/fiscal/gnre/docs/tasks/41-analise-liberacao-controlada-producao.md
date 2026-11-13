# Task 41 — Análise de pré-requisitos e liberação controlada de Produção GNRE

## Limite e conclusão

Esta é uma análise documental. Não altera código executável, banco, schema, migrations, XML, filas, estados, regras fiscais, snapshots ou guias. Não foi feita chamada ao Portal, transmissão, consulta de resultado ou obtenção de PDF.

**Produção segue bloqueada** em `GnreEnvironmentResolver::assertOperationalAllowed()` antes de certificado, XML e I/O HTTP/SOAP. A preferência `GNRE009_Ambiente=1` não autoriza operação alguma.

> **Nota de superação — estado posterior às Tasks 40 e 42.** A afirmação
> acima registra o estado encontrado nesta análise. Ela não descreve mais o
> comportamento atual do módulo: Produção não é bloqueada artificialmente por
> `GnreEnvironmentResolver::assertOperationalAllowed()`. `GNRE009` continua
> sendo apenas a preferência para novas guias; a nova guia exige um snapshot
> `GNRE007` compatível com empresa, UF e ambiente, e as operações posteriores
> usam exclusivamente o ambiente imutável desse snapshot. Não há fallback
> entre Produção e Homologação. A operação real em Produção ainda depende de
> certificado válido da empresa e de habilitação prévia do Web Service no
> Portal GNRE, que são pré-requisitos operacionais externos, não bloqueios
> artificiais do módulo.
> As demais referências a bloqueio nesta análise mantêm somente seu significado
> histórico e propositivo original.

As classificações abaixo têm este significado:

- **Confirmado:** evidência no Manual de Integração GNRE v2.17 arquivado ou no estado atual do módulo.
- **Pendente de validação:** depende de evidência atual do Portal, do cliente ou de uma operação futura expressamente autorizada; não é pressuposto.
- **Decisão proposta:** direcionamento para tarefa futura, sem implementação nesta task.

## 1. Certificado por empresa

### Confirmado

O Manual v2.17 (§3.2.3 e §4.1.4--4.1.5, cópia local com SHA-256 registrado em `docs/referencias/README.md`) prevê certificado X.509 v3 ICP-Brasil, A1 ou A3, com CNPJ do titular na extensão `otherName` OID `2.16.76.1.3.3`. Ele é usado para autenticação mútua TLS e identifica o responsável pela transmissão, que não precisa ser o CNPJ da empresa da guia. O Portal documenta a validação de presença, período de validade, Basic Constraints, KeyUsage de autenticação de cliente, LCR/CRL, revogação, cadeia ICP-Brasil, extensão CNPJ e habilitação do CNPJ do transmissor no Web Service (retornos 101/102 quando aplicáveis).

No ERP atual, os clientes GNRE independentes consultam `C004` da empresa ativa: `C004_Localizacao_Certificados` e `C004_Senha_Certificados`; a convenção atual resolve o arquivo A1 como `<localizacao>/<C004_Id>.pfx`. Eles leem PKCS#12/PFX com OpenSSL, exigem certificado e chave privada e formam PEM temporário com permissão `0600` para mTLS. Isso é infraestrutura GNRE já existente; não há dependência de método ou classe de NF-e.

### Decisão proposta — validação local mínima

Antes de **qualquer** criação de cliente HTTP/SOAP, inclusive WSDL, configuração, recepção, resultado ou PDF, a futura validação deve produzir somente um estado seguro (`apto`/motivo não sensível) e verificar:

1. empresa ativa/autorizada, cadastro `C004` ativo, caminho permitido e arquivo PFX legível; senha não vazia e nunca retornada, logada ou persistida fora do cadastro protegido;
2. abertura PKCS#12 com senha, existência de `cert` e `pkey`, par chave/certificado correspondente e material temporário removido em `finally`;
3. X.509: `notBefore`/`notAfter` válidos no relógio do servidor, CNPJ no OID oficial e identidade do transmissor extraída sem expor o certificado;
4. política local de certificado final (não-AC, KeyUsage/Extended Key Usage compatível com autenticação de cliente e cadeia confiável, na medida em que a biblioteca e a cadeia disponibilizada permitirem validar localmente);
5. vínculo de identidade: CNPJ do transmissor autorizado pela empresa para a operação GNRE; igualdade com o CNPJ da empresa **não** deve ser presumida, pois o manual permite responsável distinto.

Revogação/LCR e a habilitação final no Portal são verificações remotas: podem ser pré-checadas somente se houver contrato/evidência aprovados e nunca substituem a validação do Portal. Falha local deve impedir a chamada e mostrar mensagem operacional sem PFX, senha, PEM, CNPJ completo ou detalhe de OpenSSL.

### Pendente de validação

- Se A3 será suportado pelo ambiente PHP/servidor e qual middleware seguro é necessário. O código atual materializa A1/PFX; não se deve declarar A3 suportado por inferência documental.
- Fonte de autorização ERP para vincular um CNPJ transmissor diferente ao CNPJ da empresa, e responsável por mantê-la.
- Política de antecedência para expiração, cadeia intermediária e checagem de revogação local; o manual confirma a validação pelo Portal, não a estratégia local do ERP.
- Existência, validade operacional e habilitação produtiva do certificado de cada empresa. Êxito em homologação não prova Produção.

### Reuso permitido

Criar um serviço GNRE próprio de inspeção/validação de certificado, sem chamar NF-e e sem devolver segredos. `ConfigUfClient`, `ReceptionClient` e `ResultClient` podem reutilizar o mesmo resultado validado e o mesmo adaptador de mTLS; cada operação ainda deve exigir validação no servidor. Não duplicar PFX, senha ou chave privada em `GNRE009` nem em uma futura autorização.

## 2. Portal GNRE em Produção

### Confirmado pela documentação oficial local v2.17

O manual arquivado informa os caminhos produtivos abaixo. O PDF oficial não é um quarto endpoint: é solicitado no resultado do lote com `incluirPDFGuias=S`, após resultado individual persistido `situacaoGuia=0`.

| Necessidade | Serviço/produção documentado | Contrato documentado |
| --- | --- | --- |
| Configuração da UF | `https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF` | SOAP 1.2, Document/Literal; `consultar`; namespace/action `http://www.gnre.pe.gov.br/webservice/GnreConfigUF` e `/consultar`; `versaoDados=1.00`. |
| Recepção de lote | `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | SOAP 1.2, Document/Literal; `processar`; namespace/action `http://www.gnre.pe.gov.br/webservice/GnreLoteRecepcao` e `/processar`; `versaoDados=2.00`; fluxo assíncrono. |
| Resultado e PDF | `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote` | SOAP 1.2, Document/Literal; `consultar`; namespace/action `http://www.gnre.pe.gov.br/webservice/GnreResultadoLote` e `/consultar`; `versaoDados=2.00`. |
| Consultas de guia, se futuramente aplicáveis | `GnreLoteRecepcaoConsulta` e `GnreResultadoLoteConsulta` sob o mesmo host e caminho `gnreWS/services` | Endpoints listados no manual; fora do fluxo MVP atual e sem WSDL local capturado. |

O manual especifica TLS 1.2 com autenticação mútua, SOAP 1.2 e Document/Literal. A política comum atual mantém TLS 1.2+, verificação de cadeia e de hostname por CA do sistema. Os endpoints, namespaces, actions e versões acima já estão em allowlist fechada no `GnreEnvironmentResolver`; entrada de banco, POST ou URL livre não os modifica.

### Pendente de validação real

- WSDLs produtivos vigentes, hashes, operações, bindings, actions, namespaces, versões e limites: o repositório só arquiva WSDLs de homologação.
- Certificado apresentado, hostname, cadeia, CAs e negociação TLS/mTLS do host produtivo atual. A documentação histórica de pin em `README.md` foi superada pela Task 17.1; a política vigente não usa pin.
- Credenciamento/habilitação efetiva do CNPJ transmissor e eventuais restrições adicionais por empresa, UF favorecida, receita ou perfil. O Manual confirma que o Portal verifica habilitação do transmissor, mas não permite inventar uma matriz de credenciamento por UF.
- Se a versão v2.17 permanece vigente no dia da liberação e se há comunicado oficial posterior.

### Decisão proposta

Antes de permitir tráfego produtivo, uma tarefa de evidência, explicitamente autorizada, deve capturar somente os metadados/WSDLs necessários, versioná-los com origem, data e SHA-256 e comparar o contrato contra a allowlist. Diferença relevante bloqueia Produção até revisão humana; nunca altera endpoint em tempo de execução. Essa tarefa não deve transmitir, consultar resultado ou obter PDF.

## 3. Modelo mínimo de liberação por empresa

| Conceito | Estado atual / finalidade | Pode autorizar operação? |
| --- | --- | --- |
| Ambiente preferido | `GNRE009_Ambiente`; escolhe somente o ambiente pretendido para **nova** guia/snapshot. | Não. `1` continua bloqueado. |
| Ambiente do ato fiscal | `GNRE007_Ambiente`, imutável e vinculado à guia. Guia, transmissão, fila, resultado e PDF usam apenas ele. | Não sozinho. |
| Autorização produtiva por empresa | Registro administrativo explícito, revogável e auditável de prontidão para Produção. | Somente como uma das pré-condições server-side. |

### Decisão proposta

O mínimo funcional é uma autorização por `C004_Id`, separada logicamente da preferência, com estado inicialmente negado, autor/data/motivo da decisão, revisão concorrente e evento imutável de habilitar/suspender/revogar. Para o MVP ela pode caber em **GNRE009**, pois a tabela já é a política GNRE por empresa e ainda não contém autorização produtiva; acrescentar uma tabela separada só se o padrão de auditoria ERP exigir eventos normalizados ou houver mais de uma autorização simultânea (por exemplo, por certificado/UF).

Isso não propõe migration nesta task. A futura task deve decidir no DDL mínimo se acrescenta à GNRE009 um estado explícito e metadados de auditoria ou cria um registro de eventos complementar. Não usar uma flag global, valor derivado da preferência, URL por empresa, nem campo editável na guia.

A autorização válida exige cumulativamente: empresa GNRE autorizada, estado administrativo aprovado e não suspenso, certificado local apto, contrato produtivo previamente aceito, snapshot produtivo imutável compatível e usuário com permissão operacional. A seleção de Produção jamais muda qualquer um desses fatos para verdadeiro.

## 4. Segurança operacional e matriz de comportamento

Para uma ação produtiva, o servidor deve negar antes do cliente HTTP/SOAP se qualquer condição falhar: ambiente não é `1`; empresa sem autorização vigente; usuário/CSRF/contexto de empresa inválidos; certificado não apto; contrato produtivo não aprovado; snapshot ausente, de outra empresa/UF ou ambiente; guia legada sem snapshot; ou confirmação reforçada ausente na transmissão.

Se uma guia tem snapshot de Produção e a empresa está suspensa/não liberada, todas as ações dependentes de Portal (transmissão, consulta, fila e PDF) devem falhar explicitamente com bloqueio de Produção, sem criar tentativa, consulta ou PDF e sem trocar seu snapshot. A guia permanece com seu contexto imutável; a preferência posterior em GNRE009 não a converte para Homologação. Para configuração/snapshot novo em Produção, negar antes de Portal e antes de criar registro parcial enquanto a liberação específica não existir.

| Ação | Homologação (`2`) | Produção (`1`) enquanto não liberada | Regra comum |
| --- | --- | --- | --- |
| Escolher preferência | Permitido, isolado por empresa. | Permitido apenas como preferência administrativa já existente; sem efeito operacional. | Preferência não reescreve guia/snapshot. |
| Configuração/snapshot novo | Fluxo atual, com resposta oficial validada. | Bloqueado antes de certificado/HTTP/snapshot. | Não há fallback para `2`. |
| Criar guia com snapshot | Permitido com snapshot `2` compatível. | Bloqueado; nenhum registro parcial. | Ambiente só vem do snapshot. |
| Transmitir | Fluxo atual. | Bloqueado antes de certificado, XML e HTTP/SOAP. | Nunca retransmitir ou cair para `2`. |
| Fila/consulta/PDF | Usa ambiente do snapshot e controles existentes. | Bloqueado antes de chamada externa. | Não mudar guia nem simular resultado. |
| Emissão reconhecida | Somente resultado individual persistido `situacaoGuia=0`. | A mesma regra após futura liberação. | `F`/fila finalizada, recibo, PDF ou HTTP 200 isolados não aprovam. |

### Decisão proposta — auditoria da primeira transmissão produtiva

Além das trilhas existentes (`GNRE004` recepção, `GNRE005` consulta/fila e `GNRE008` PDF), registrar a decisão administrativa da empresa e, na primeira transmissão, o identificador de empresa e usuário, instante, ação confirmada, ambiente do snapshot, UF, receita, valor, identificador/hash do snapshot, identidade não sensível do certificado (serial/validade mascarados ou hash permitido pela política), versão/hash do contrato produtivo aprovado, resultado e recibo quando houver. Não registrar senha, PFX, chave privada ou SOAP sensível em logs de interface. O critério de “primeira” deve ser calculado de uma tentativa produtiva persistida/auditável, nunca de uma flag de tela.

## 5. Plano incremental recomendado

| Ordem | Task pequena e independente | Dependências | Riscos principais | Testes necessários |
| --- | --- | --- | --- | --- |
| 41.1 | Persistir autorização produtiva por empresa, revogação, permissão e auditoria; manter bloqueio operacional. | Task 40. | Confundir preferência com autorização; vazamento entre empresas; ausência de trilha. | Migration isolada, padrão negado, isolamento C004, CSRF/permissão, concorrência, habilitar/suspender/revogar, prova de que `ambiente=1` ainda bloqueia I/O. |
| 41.2 | Validador GNRE de certificado local e adaptador comum de mTLS, sem usar NF-e. | 41.1 somente para exibir prontidão; pode validar A1 independentemente. | Expor segredo; aceitar certificado vencido/sem chave; alegar suporte A3. | PFX ausente/senha errada/par inválido/vencido/não vigente/OID CNPJ ausente, limpeza de temporário, logs sem segredo, nenhuma chamada externa em falha. |
| 41.3 | Evidenciar contrato produtivo e permitir apenas configuração UF/snapshot em Produção para empresa autorizada e certificado apto. | 41.1, 41.2 e autorização explícita para validação técnica real. | WSDL/host divergente; gravar snapshot de retorno incompatível; consulta virar transmissão. | Hash/versionamento de evidência, allowlist, TLS/mTLS, resposta ambiente `1`, empresa/UF/hash, bloqueios negativos; sem criar guia nem transmitir. |
| 41.4 | Permitir transmissão produtiva explicitamente confirmada, por empresa liberada. | 41.1--41.3. | Efeito fiscal/financeiro real, duplicidade, confirmação só no cliente. | Revalidação server-side de todos os gates, modal/confirmação, idempotência, recibo, falha inconclusiva sem reenvio, isolamento, auditoria da primeira transmissão; execução real somente em roteiro aprovado. |
| 41.5 | Permitir consulta produtiva e PDF produtivo para guias produtivas já recebidas/aprovadas. | 41.4; PDF exige resultado individual `situacaoGuia=0`. | Consultar ambiente errado, PDF de lote associado incorretamente, interpretar fila como emissão. | Ambiente imutável ponta a ponta, bloqueio por suspensão, `situacaoGuia=0`, PDF base64/tamanho/hash/associação, reimpressão local sem novo Portal, nenhum fallback. |

Cada task deve manter Produção bloqueada até sua própria condição de liberação estar aprovada; não há autorização para iniciar qualquer uma delas nesta Task 41.

## Handoff e pendências externas

Antes da primeira operação produtiva, validar com cliente/Portal: CNPJ transmissor e empresa responsáveis, certificados por empresa e eventual A3, credenciamento/habilitação no Portal, escopo das UFs/receitas, WSDLs/contratos produtivos vigentes, TLS/mTLS e cadeia atuais, limites operacionais e aprovação formal fiscal/administrativa. Não se presume resposta, autorização ou requisito por UF sem fonte oficial atual.

Validação executada nesta task: somente `git diff --check`.
