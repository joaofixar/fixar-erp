# Task 6 — Descoberta e arquitetura da Calculadora da Reforma Tributária

## Status e decisão

**Descoberta concluída. Recomendação: viável com dependências.**

O Hardness não deve criar ou manter fórmulas próprias para CBS, IBS ou Imposto Seletivo. A Calculadora de Tributos da Receita Federal é o motor oficial a ser avaliado para integração local. Há distribuição oficial offline, em código aberto, executável na infraestrutura do integrador e consumível por API REST local. A modalidade online é de consulta/simulação e não deve ser tratada como API de produção do Hardness.

Antes de iniciar uma implementação, é preciso obter o artefato oficial vigente e sua documentação de instalação, confirmar a licença no próprio repositório/pacote, validar o contrato OpenAPI da versão escolhida, homologar cenários fiscais com a área fiscal e definir a operação segura do serviço local. Esta task não implementou cálculo, simulação, integração externa, alteração de documento ou alteração cadastral.

Consulta realizada em **10/11/2026**.

## Matriz de fontes e conclusões

| Fonte, órgão/fornecedor e data de consulta | Conclusão usada nesta descoberta |
| --- | --- |
| [FAQ — Fundamentos da Calculadora de Tributos](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/apresentacoes/reforma-tributaria-do-consumo/faq-calculadora-v1-4.pdf), Receita Federal, 10/11/2026 | Define a Calculadora como motor com conteúdo normativo, memória de cálculo, fundamento legal e grupos XML. Declara código-fonte aberto, banco embarcado, versões controladas e integração local por API REST; a versão online não é serviço de cálculo para alto volume. Também registra que a validação do IBS não está uniforme para todos os modelos de DF-e. |
| [Portal Nacional — Calculadora](https://consumo.tributos.gov.br/servico/calcular-tributos-consumo), Receita Federal/RTC, 10/11/2026 | Expõe modalidades de regime geral, Simples, pedágio, base de cálculo, documentação de API e a Calculadora Offline. O portal mostra que a versão do produto é identificada; a versão usada em cada execução precisa ser capturada pelo Hardness. |
| [Documentação da API da Calculadora](https://piloto-cbs.tributos.gov.br/servico/calculadora-consumo/api/swagger-ui/index.html), Receita Federal/RTC, 10/11/2026 | Publica Swagger para cálculo, base de cálculo, validação e geração de XML. O contrato exato de entrada/saída deve ser congelado por versão e testado contra o componente local, não inferido da interface web. |
| [Manual de Serviços da RTC](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/manuais/reforma-tributaria-do-consumo/manual-servicos-rtc.pdf), Receita Federal, 10/11/2026 | Aponta download e instalação local da Calculadora Offline, guia de integração e exemplos para ERP. Confirma que a implantação é responsabilidade operacional do integrador. |
| [IT 2025.002 — RTC NF-e/NFC-e](https://www.nfe.fazenda.gov.br/portal/exibirArquivo.aspx?AspxAutoDetectCookieSupport=1&conteudo=jxTMMQeEVM8%3D), ENCAT/Portal NF-e, 10/11/2026 | Sustenta CST, cClassTrib, grupos IBS/CBS, base, parcelas estadual/municipal e CBS no leiaute; as tabelas e as regras do DF-e possuem versão própria e evoluem. |
| [Documentação RTC da NFS-e](https://www.gov.br/nfse/pt-br/biblioteca/documentacao-tecnica/rtc/rtc), Portal Nacional NFS-e/CGNFS-e, 10/11/2026 | Confirma que leiautes e correlações NBS/cClassTrib/cIndOp continuam versionados; alguns anexos são trabalho em desenvolvimento e explicitamente não têm regra de negócio implantada. Não podem virar regra inventada no Hardness. |
| [Protheus — adequação IBS/CBS](https://centraldeatendimento.totvs.com/hc/pt-br/articles/42351363577623-CROSS-Segmentos-TOTVS-Backoffice-Linha-Protheus-FIS-Adequa%C3%A7%C3%A3o-%C3%A0-obrigatoriedade-do-IBS-e-CBS-a-partir-de-03-de-agosto-de-2026), TOTVS, 10/11/2026 | Demonstra princípios aplicáveis: configuração fiscal centralizada, importação de tabela oficial, persistência separada dos valores/detalhes calculados, geração de XML e tratamento de rejeições. Não foi copiada interface, regra ou código. |
| [Oracle Fusion — atributos fiscais adicionais](https://docs.oracle.com/en/cloud/saas/readiness/scm/25c/inv25c/25C-inventory-wn-f38941.htm), Oracle, 10/11/2026 | Demonstra separação entre captura de atributos do XML e cálculo tributário: a documentação informa que os atributos importados não participam de cálculo nesse recurso. Aplicação ao Hardness: não confundir “campo existente” com dado apto e homologado para cálculo. |

### Base legal e técnica adotada

- A Calculadora oficial declara restringir suas regras à **Lei Complementar nº 214/2025** e às notas técnicas consensadas entre Receita Federal e a governança do IBS.
- Leiautes e validações de DF-e são fonte de contrato de documento; não substituem o motor oficial para derivar valores.
- A matriz deve ser revista na abertura da Task 7, pois tabelas, anexos e APIs são versionados.

## 1. Motor oficial: resposta arquitetural

| Questão | Resposta documentada | Consequência para o Hardness |
| --- | --- | --- |
| Há componente local? | Sim. A fonte oficial divulga Calculadora Offline, em código aberto, com o mesmo componente das modalidades online/offline. | Integrar somente uma instância local controlada, nunca automatizar a interface web. |
| Forma de integração? | API REST local do módulo offline; há Swagger, guia e scripts de exemplo. | Criar um adaptador interno, com timeout, validação de contrato e sem acoplar telas ao endpoint. |
| Atualização/versionamento? | Versões numeradas, histórico de alterações e banco embarcado atualizável; mudanças normativas, alíquotas e correções geram atualização. | Cada execução deve registrar versão do motor, pacote/checksum, versão do OpenAPI e versão das tabelas recebidas. Atualização deve ser homologada em ambiente controlado antes de produção. |
| Pode operar sem simulador online? | Sim. A integração de ERP prevista pela Receita é o consumo do módulo offline local; a versão online não é ofertada como serviço para alto volume. | Sim, desde que o pacote oficial esteja instalado e operante na infraestrutura aprovada. |
| Licença e redistribuição? | A FAQ declara código-fonte aberto, mas esta descoberta não confirmou o arquivo de licença do artefato vigente. | Bloqueador de engenharia: conferir licença, avisos, condições de redistribuição/modificação e cadeia de suprimento no release que será usado. |
| Infraestrutura e segurança? | O componente roda sob responsabilidade do integrador; a documentação pública consultada não substitui o manual de instalação da versão. | Isolar em serviço interno/rede privada, sem exposição pública; acesso autenticado/permitido apenas ao backend, logs sem dados sensíveis, segredo fora do código, limites de requisição, health check e plano de indisponibilidade. |

### Confirmações obrigatórias antes da integração real

1. URL de download oficial, versão, checksum/assinatura e licença do pacote selecionado.
2. Pré-requisitos do runtime, banco embarcado, portas, persistência, atualização e compatibilidade com a infraestrutura Hardness.
3. OpenAPI local da mesma versão e campos obrigatórios para cada tipo de operação que será habilitado.
4. Limites de uso, autenticação/segurança local, observabilidade e conteúdo de logs.
5. Escopo homologado por modelo de DF-e, regime e tipo de operação — sobretudo onde a própria FAQ aponta validação de IBS ainda não concluída para certos modelos.
6. Casos de teste oficiais e validação conjunta com fiscal antes de qualquer vínculo com documento fiscal.

## 2. Padrões observados em ERPs e princípios aplicáveis

| Aspecto | TOTVS Protheus | Oracle Fusion | Princípio para o Hardness |
| --- | --- | --- | --- |
| Configuração | Centraliza cálculo em configurador e importa tabela oficial de classificação. | Captura atributos novos de XML sem assumir que eles calculam tributo. | Cadastro fiscal é origem assistida; campo preenchido não equivale a dado válido/homologado. |
| Cálculo por item e totais | Valores e detalhes são persistidos após o cálculo para compor documento/XML. | A fonte consultada trata importação, não cálculo. | Enviar e receber em granularidade por item; totais devem ser devolvidos/validados pelo motor, sem totalização alternativa própria. |
| Memória e auditoria | Detalhes de tributos ficam separados dos relacionamentos/valores da operação. | Mantém atributos do documento de origem. | Guardar snapshot imutável de entrada, resposta integral, versão e status; apresentar memória do motor, não uma explicação reconstruída. |
| Alertas | Há orientação para rejeições e inconsistências de XML. | Limita explicitamente o que o recurso não calcula. | Diferenciar erro de preenchimento, alerta de fonte, indisponibilidade do motor e resultado não suportado. |
| Documento fiscal | Resultado calculado é usado para XML e sujeito a rejeições. | Dados importados podem existir sem afetar cálculo. | A geração/validação de DF-e é fase posterior, condicionada ao contrato oficial vigente. |
| Atualizações | Atualiza classificação e configurações por mecanismo central. | Entrega evoluções por release de produto. | Governança de versão, importação/atualização controlada e trilha de auditoria são requisitos, não conveniências. |

## 3. Mapa de dados: origem oficial × disponibilidade no Hardness

Legenda: **Disponível** = há indício técnico identificado; **Confirmar** = existe dado relacionado, mas é necessária seleção/qualidade/semântica; **Não disponível** = não identificado nesta descoberta; **Futuro** = depende de regra, cadastro, integração ou contrato oficial. A Task 1 inventariou estruturas esperadas; ela não confirmou presença e qualidade por base, portanto nenhum item é automaticamente apto para cálculo.

| Dado necessário | Origem/uso oficial | Situação no Hardness | Evidência e ação para Task 7 |
| --- | --- | --- | --- |
| Tipo/modelo/direção da operação | Contrato da API e leiaute DF-e. | **Confirmar.** Há documentos de orçamento, pedido, NF-e saída/entrada, cotação e OC inventariados. | Mapear cada modelo e o evento de negócio ao tipo aceito pela versão do motor. |
| Estabelecimento, origem e destino (UF/município) | Determinam regras e parcelas do IBS conforme operação. | **Confirmar.** Empresas e documentos existem, mas não houve auditoria de endereço/IBGE/qualidade. | Definir fonte canônica e obrigatoriedade; não usar texto de endereço como substituto de código válido. |
| Data do fato gerador/emissão | Contrato do cálculo e vigência de regra/tabela. | **Confirmar.** Há cabeçalhos documentais, sem mapeamento de semântica nesta task. | Selecionar data juridicamente aplicável por operação com fiscal. |
| NCM | Item/DF-e e classificação oficial; NBS para serviços. | **Disponível para NCM, confirmar qualidade.** `D005.D005_Classificacao_Fiscal` é tratado como NCM no módulo. **NBS: Não disponível.** | Validar tamanho/vigência do NCM; criar fonte para NBS somente quando o cenário oficial requerer. |
| CFOP | Campo do DF-e quando aplicável; não substitui CST/cClassTrib. | **Disponível, confirmar adequação.** `D006.D006_Codigo_CFOP`; a configuração global é preferencial no módulo atual. | Selecionar CFOP da operação e validar precedência com a regra homologada. |
| CST e cClassTrib | Tabelas oficiais e grupos `IBSCBS` do DF-e. | **Disponível, confirmar qualidade.** Campos CBS/IBS em `D005` e `D006`; campos previstos em itens de documentos. | Conferir versão, vigência, preenchimento e precedência; não inferir por NCM/CFOP. |
| Produto/serviço, quantidade, unidade, preço e valor do item | Entrada por item da operação. | **Confirmar.** Há itens nos documentos inventariados, porém não foi auditado o mapeamento de cada campo. | Construir adaptador por modelo e teste de arredondamento/unidade da API. |
| Frete, desconto, seguro, outras despesas, juros/multas e componentes de base | Leiautes oficiais expõem componentes e regras de base. | **Confirmar.** Há valores em documentos, mas não foi comprovada completude por modelo. | Mapear campo a campo; nunca completar ausente com zero sem regra oficial/decisão explícita do usuário. |
| Regime tributário e opção aplicável | Seleciona o fluxo/regime da Calculadora. | **Futuro.** A flag de preenchimento automático em `C004` não identifica regime. | Definir cadastro/integração e confirmação pelo usuário; separar regime do modo de preenchimento. |
| Redução, diferimento, benefício, crédito presumido e situação especial | A Calculadora pode produzir efeitos conforme regra/classificação. | **Futuro.** Há flags de débito/crédito e deduções em `D006`, mas não se comprovou equivalência ao contrato oficial. | Somente enviar dado cuja origem e semântica forem homologadas; modelar referências e documentos comprobatórios quando exigidos. |
| Alíquotas, tabelas, fundamento e versão | Conteúdo embarcado e atualizado pelo componente oficial. | **Futuro — motor oficial.** Há funções internas e exibição de alíquotas no código atual, mas isto não comprova homologação. | O adaptador lê o resultado oficial; não usa funções do Hardness como fonte concorrente. |

### O que já pode ser reaproveitado, com validação

- Catálogos globais de NCM (`D005`) e CFOP (`D006`), inclusive CST/classificação CBS/IBS, quando presentes e conferidos.
- Flag por empresa `C004_Flag_Tipo_Preenchimento_Reforma_Tributaria`, exclusivamente como preferência operacional de preenchimento; **não** é parâmetro fiscal do motor.
- Estruturas de itens e cabeçalhos já inventariadas para orçamento, pedido, NF-e, compra e cotação, nas fases posteriores.

## 4. Arquitetura proposta

```
Tela somente leitura / formulário manual
            │
            ▼
Validador de presença e qualidade (não calcula imposto)
            │  ├─ alertas de dado ausente / ambíguo
            │  └─ bloqueia cenário fora do escopo homologado
            ▼
Adaptador RTC do Hardness
            │  ├─ contrato versionado (DTO)
            │  ├─ timeout, correlação e mascaramento de logs
            │  └─ sem fallback de fórmula própria
            ▼
Calculadora Oficial Offline (rede privada)
            │
            ▼
Normalizador de resposta e evidência imutável
            │  ├─ valores por item e totais devolvidos
            │  ├─ memória/fundamento devolvidos
            │  ├─ versão do motor, regra e tabelas
            │  └─ entrada, resposta, alertas e hash/correlação
            ▼
Resultado fiscal explicável
```

### Contrato de apresentação

- **Calculado por fonte oficial integrada:** resposta válida da instância local, acompanhada de versão, memória e fundamento devolvidos.
- **Simulação com premissas informadas:** somente se o próprio motor oficial aceita o cenário e a tela marca claramente os campos informados manualmente; não significa documento pronto para emissão.
- **Não calculável com segurança:** motor indisponível, entrada incompleta, cenário fora de escopo/sem homologação ou retorno sem suporte. Exibir pendências e não mostrar valor estimado próprio.

O resultado poderá apresentar, quando devolvido/suportado pelo motor: base, CBS, IBS-UF, IBS-município, IS, reduções, diferimentos, créditos, total, alertas, versão e memória. O Hardness não deduz valores ausentes nem converte aviso em aprovação fiscal.

## 5. Entrega incremental

| Fase | Objetivo | Dados necessários | Dependências | Risco | Critério para avançar |
| --- | --- | --- | --- | --- | --- |
| 1 — Simulador manual | Formulário somente leitura que recebe dados explícitos, valida presença e, se o motor local estiver homologado, consulta-o. Não grava documento. | Identificação da operação, local, data, itens, NCM/NBS, CFOP aplicável, CST/cClassTrib, valores e regime. | Pacote local oficial, OpenAPI da versão, decisão fiscal de cenários suportados. | Campo manual incorreto e falsa percepção de cálculo definitivo. | Casos oficiais homologados; evidência completa/versionada; mensagens corretas para erro e indisponibilidade. |
| 2 — Preenchimento assistido | Sugerir dados de cadastros/operações existentes, com confirmação do usuário e sem alterar origem. | Fontes canônicas e qualidade de empresa, parceiro, produto, CFOP/NCM, CST/cClassTrib e valores. | Mapa de dados validado por modelo; regras de precedência homologadas. | Reaproveitar dado incompleto ou semanticamente diferente. | Cobertura de mapeamento por campo, taxa de pendências conhecida e testes contra entradas manuais equivalentes. |
| 3 — Integração documental | Usar dados de orçamento, pedido, NF-e, compras ou outros documentos como rascunho de simulação, preservando vínculo e snapshot. | Identificador e versão do documento, itens, componentes de base e contexto tributário. | Viabilidade técnica/legal por modelo; contrato oficial compatível; auditoria e permissões. | Documento muda após cálculo; modelo sem suporte oficial. | Reconciliação item/total, invalidação ao alterar origem e homologação fiscal por fluxo. |
| 4 — Validação/geração fiscal | Gerar ou validar dados de DF-e somente quando o componente, leiaute e regra estiverem homologados para o modelo. | Resposta oficial, grupos XML, versões de nota técnica/tabela e dados de emissão. | Homologação do modelo no ecossistema oficial, autorizador e governança de publicação. | Rejeição, divergência de leiaute e uso de regra não homologada. | Testes em ambiente autorizado, aceite fiscal e rastreabilidade integral por documento. |

## 6. Wireframe conceitual

```
┌ Reforma Tributária / Calculadora                                      ┐
│ Apoio operacional; não substitui orientação fiscal.                  │
│ Motor: não conectado / RTC Calc <versão> · Regra/tabelas <versão>    │
├──────────────────────────────────────────────────────────────────────┤
│ 1. Dados da operação                                                  │
│ [tipo/modelo*] [data do fato gerador*] [regime*]                      │
│ [origem: UF/município*] [destino: UF/município*] [CFOP quando couber] │
│                                                                       │
│ 2. Itens e valores                                                    │
│ Item | produto/serviço | NCM/NBS* | CST* | cClassTrib* | valor*       │
│      | frete | desconto | seguro | outros componentes                 │
│ [+ adicionar item]                                                    │
│                                                                       │
│ 3. Situações e benefícios                                              │
│ [redução/diferimento/crédito/situação especial — conforme contrato]   │
│                                                                       │
│ [Validar dados] [Calcular no motor oficial]                           │
├──────────────────────────────────────────────────────────────────────┤
│ Validações e alertas                                                  │
│ ! Obrigatório: município de destino                                   │
│ ! CST/cClassTrib ainda não confirmado para o item 2                   │
├──────────────────────────────────────────────────────────────────────┤
│ Resultado tributário                                                  │
│ Base | CBS | IBS UF | IBS município | IS | reduções | créditos | total│
│ Status: Calculado por fonte oficial / Premissas informadas / Bloqueado│
│ [▸ Memória de cálculo e fundamento aplicado]                          │
│ [▸ Dados enviados e retorno técnico mascarado]                        │
└──────────────────────────────────────────────────────────────────────┘
```

Regras de experiência: validar no campo e também no resumo; não esconder pendências em acordeões; desabilitar o cálculo somente com motivo visível; manter o resultado anterior identificado como histórico, nunca como resultado atual após alteração de entrada; e deixar a memória expansível, legível e exportável apenas com as permissões adequadas.

## Próxima task recomendada

Abrir a **Task 7 — Spike de integração local da Calculadora RTC** com escopo restrito: instalar em ambiente isolado o release oficial aprovado, registrar licença/versão/checksum, validar health check e OpenAPI, executar casos de teste oficiais sem documentos do Hardness e produzir matriz de compatibilidade por modelo/regime. Não incluir fórmula alternativa, chamada ao simulador online, emissão de DF-e ou escrita em documentos.
