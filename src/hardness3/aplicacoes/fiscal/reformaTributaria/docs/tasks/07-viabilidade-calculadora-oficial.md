# Task 7 — Avaliação de viabilidade da Calculadora Oficial RTC

## Decisão

**Recomendação: viável com dependências externas ou internas.**

A integração futura tem caminho técnico documentado: a Receita Federal prevê que o componente offline seja executado na infraestrutura do integrador e consumido por API REST local; não há API pública hospedada para cálculo remoto. O Hardness já contém padrões úteis de serviço isolado, segredos fora do código, limitação, timeout, operação e logs sanitizados. Contudo, este repositório não comprova um ambiente atual de contêiner/rede privada pronto para receber o motor, e a documentação pública consultada não confirma todos os parâmetros da distribuição oficial vigente. Assim, a recomendação **não autoriza instalação**: ela autoriza somente planejar uma Task 8 de spike isolado depois de satisfeitas as condições deste documento.

Consulta documental e inspeção read-only realizadas em **10/11/2026**. Nenhum pacote, imagem, código, dependência ou componente oficial foi baixado, instalado, executado ou chamado. Nenhuma porta, rede, serviço, contêiner, configuração, dado ou regra fiscal foi alterado.

## Como ler esta avaliação

- **Fato documentado**: afirmação encontrada em fonte oficial pública, citada abaixo.
- **Evidência interna**: achado de leitura do repositório Hardness; não é garantia sobre um servidor em produção.
- **Inferência técnica**: recomendação de arquitetura derivada dos dois itens anteriores; exige aprovação antes de implementação.
- **Não confirmado**: depende da documentação e do pacote oficiais da versão que venha a ser aprovada. Não foi presumido como requisito.

## 1. Evidências sobre o componente oficial

| Tema | Situação | Evidência e consequência |
| --- | --- | --- |
| Distribuição | **Fato documentado.** Há componente offline e código-fonte aberto; o portal descreve alternativas de imagem Docker e componente Java (JAR). | O [Manual de Serviços RTC](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/manuais/reforma-tributaria-do-consumo/manual-servicos-rtc.pdf) indica download do componente offline, JAR com Java 21 e imagem Docker. A escolha do formato só poderá ocorrer depois de validar o release oficial selecionado. |
| Integração | **Fato documentado.** A integração de ERP é local, máquina a máquina, por API REST do módulo offline. | A [FAQ da Calculadora](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/apresentacoes/reforma-tributaria-do-consumo/faq-calculadora-v1-4.pdf) afirma que não há API pública de cálculo online e que ERPs consomem a API REST local. O adaptador Hardness não deve consumir o simulador web. |
| Contrato | **Fato documentado, mas versionável.** Há Swagger/OpenAPI e endpoints para cálculo, dados abertos e observabilidade. | O [Swagger oficial](https://consumo.tributos.gov.br/servico/calcular-tributos-consumo/api/swagger-ui/index.html) publica, entre outros, `POST /calculadora/regime-geral` e `GET /calculadora/dados-abertos/versao`. O contrato efetivo deve ser capturado do componente local da mesma versão; não deve ser inferido da UI pública. |
| Banco e persistência | **Fato documentado em nível geral.** O motor possui banco embarcado com tabelas normativas atualizáveis. | A FAQ informa banco embarcado com CST × cClassTrib, alíquotas, reduções, benefícios e demais parâmetros. **Não confirmado:** tecnologia do banco, diretórios, volume necessário, estratégia de backup/restauração e se a persistência contém algo além de dados normativos. |
| Runtime e SO | **Parcialmente documentado.** Java 21 é indicado para o JAR; Docker é alternativa de distribuição; WSL é citado para Windows. | **Não confirmado:** versões de Docker/Java aceitas no release, arquitetura de CPU, distribuição Linux suportada, CPU, memória, disco e dependências do sistema. Não se deve dimensionar nem provisionar com base em estimativas. |
| Portas | **Parcialmente documentado.** O manual descreve API local em `localhost:8080/api` e interface web local em `localhost`. | **Não confirmado:** se a porta/rota são imutáveis no release atual, como se configuram bind, TLS, CORS, interface web, health endpoint e acesso por rede de contêiner. A futura implantação deve descobrir isso no manual/pacote homologado, sem expor porta pública. |
| Atualização/versionamento | **Fato documentado.** Há versões numeradas, histórico de alterações e atualizações por norma, alíquota ou correção. | A [FAQ](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/apresentacoes/reforma-tributaria-do-consumo/faq-calculadora-v1-4.pdf) prevê liberação controlada, comunicação e rastreabilidade; a [notícia de maio de 2026](https://www.gov.br/fazenda/pt-br/assuntos/noticias/2026/maio/receita-federal-lanca-nova-versao-do-portal-nacional-de-tributacao-sobre-o-consumo-da-reforma-tributaria) registra o Release 12 e alterações de API. O Hardness deve tratar atualização como mudança de produção, nunca automática. |
| Dados enviados à Administração Tributária | **Fato documentado.** A execução local é descrita como sem coleta, telemetria ou transmissão automática das operações. | O [Manual da Plataforma CBS](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/manuais/reforma-tributaria-do-consumo/manual-plataforma-cbs-21-maio-2026-07h40.pdf) descreve processamento restrito ao ambiente do usuário. Isto não substitui a revisão de tráfego, configuração e dependências da versão instalada. |
| Licença, redistribuição e alteração | **Não confirmado.** A documentação diz que o código é aberto e pode ser utilizado/adaptado. | A FAQ sustenta uso/adaptação, mas esta análise não inspecionou arquivo `LICENSE`, avisos de terceiros nem termos do pacote oficial vigente. Redistribuir artefato/imagem, alterar código ou publicar uma imagem derivada fica bloqueado até parecer jurídico e leitura da licença do release. |
| Segurança e operação oficiais | **Parcialmente documentado.** Há guia, Swagger, scripts de exemplo e observabilidade funcional. | O Manual RTC aponta [guia de integração](https://consumo.tributos.gov.br/servico/calcular-tributos-consumo/calculadora/documentacao/guia-integracao), Swagger e instruções do offline. **Não confirmado:** autenticação local, autorização, TLS entre processos, política de logs, CVEs/SBOM, hardening e SLA/suporte oficial. |

### Matriz — requisito oficial × capacidade atual do Hardness × evidência × pendência

| Requisito/capacidade para a integração | Capacidade atual do Hardness | Evidência | Pendência antes de instalar |
| --- | --- | --- | --- |
| Motor local acessível por API REST | **Parcial.** Há módulos HTTP e separação de responsabilidades; não há adaptador RTC. | Fato oficial: API REST local. Evidência interna: `webhooks/` usa transporte HTTP controlado; `rest_api/` e `mcp/` têm contratos próprios. | Definir adaptador exclusivamente backend, DTO por versão e testes contra o OpenAPI local; não criar tela nesta fase. |
| Processo/serviço isolado | **Parcial.** Há padrão systemd com usuário dedicado, `EnvironmentFile`, hardening básico e reinício. | Evidência interna: `webhooks/systemd/*.service` usa `User`, `EnvironmentFile`, `NoNewPrivileges`, `PrivateTmp`, `ProtectHome` e `ProtectSystem`. | Escolher JAR ou contêiner conforme artefato; validar usuário, filesystem gravável, restart, limites e compatibilidade no servidor alvo. |
| Contêiner e rede privada | **Não comprovado.** O repositório possui diretório `docker/`, mas não há Compose/manifesto que demonstre uma rede privada atual para este motor. | Evidência interna: `docker/start_container.sh` é legado e apenas inicia Apache/MySQL; não é prova de orquestração ou de rede disponível. | Infraestrutura deve aprovar host/orquestrador, rede interna, DNS/alias, firewall e proibição de publicação de porta. |
| Porta/API local | **Parcial.** O backend PHP pode originar chamadas internas, mas não há configuração RTC. | Fato oficial: manual menciona API local/`localhost:8080/api`. | Confirmar bind e configuração da versão; restringir origem ao backend Hardness e impedir acesso de navegador, internet e outras redes. |
| Segredos | **Parcial.** Padrões de segredo fora de Git e rotação existem. | Evidência interna: `rest_api/README_INTERNO_OPERACAO.md` usa configuração local não versionada; `webhooks/docs/SECURITY.md` prevê chave injetada por ambiente. | Confirmar se o motor suporta autenticação. Se suportar, armazenar credencial no cofre/arquivo de ambiente protegido, rotacionar e nunca registrar. |
| Timeout, limite e indisponibilidade | **Parcial.** Há exemplo operacional maduro, mas não para RTC. | Evidência interna: webhooks usa connect timeout, timeout total, payload limitado, retry e estados terminais. | Definir orçamento de timeout, concorrência/fila, rate limit por tenant/usuário/operação e política de falha sem fallback fiscal próprio. |
| Logs e observabilidade | **Parcial.** Há sanitização e observabilidade, mas não métrica RTC. | Evidência interna: `observability/` mascara tokens/senhas; webhooks registra metadados seguros, não payload/URL/segredo. | Criar somente em task futura métricas e logs estruturados sem XML, documento, CNPJ/CPF, valores ou memória fiscal completos; definir retenção e acesso. |
| Health check | **Não confirmado para o motor; inexistente para integração.** | O Swagger tem área de observabilidade, mas a documentação consultada não confirma endpoint/liveness/readiness para o offline. | Identificar endpoint seguro no release ou usar verificação de processo/API sem efetuar cálculo; definir alarme e comportamento do Hardness. |
| Atualização e rollback | **Parcial.** Há disciplina de runbook e rollback de serviço em Webhooks. | Evidência interna: `webhooks/docs/PRODUCTION_DEPLOYMENT.md` exige validação prévia, service control e preservação de dados. | Processo RTC precisa de checksum/assinatura, SBOM se disponível, homologação fiscal, janela, backup/rollback do artefato e registro de versões. |
| Auditoria do cálculo | **Não implementada.** | Fato oficial: motor retorna memória de cálculo e base legal; Task 6 recomendou snapshot e versão. | Definir modelo, retenção/LGPD, permissão de acesso e imutabilidade antes de persistir entrada, resposta, memória ou versão. |

## 2. Compatibilidade e impactos para o Hardness

### Leitura da arquitetura atual

O Hardness é predominantemente PHP/Apache com MySQL e possui serviços auxiliares isolados, não uma plataforma de microsserviços já padronizada. A referência mais próxima é o worker de Webhooks: serviço `systemd` sob usuário dedicado, configuração por `EnvironmentFile`, limites de HTTP, retry e logs sem payload/segredo. MCP possui autenticação, escopos e rate limit com falha fechada. A observabilidade sanitiza campos que parecem senha, token, segredo ou chave.

Isto demonstra que há convenções reutilizáveis para uma futura borda Hardness → RTC, mas não prova que a infraestrutura de cada cliente tenha Docker, Java 21, capacidade livre, rede de contêiner, monitoramento, cofre de segredos ou operação 24×7. O local apropriado, em tese, é um **processo/contêiner separado do Apache e do MySQL do ERP**, na mesma zona privada do backend; essa é uma inferência técnica e depende de aprovação de infraestrutura.

### Impactos previsíveis

| Área | Impacto/riscos | Tratamento obrigatório antes de produção |
| --- | --- | --- |
| Backup | Banco embarcado e eventuais dados de runtime podem precisar de backup consistente; não há prova de seu conteúdo. | Descobrir diretórios/semântica oficial; definir se é dado regenerável por release ou dado a proteger, além de testar restauração. |
| Observabilidade | Logs podem expor itens, valores, identificadores e memória fiscal. | Métricas agregadas e correlação opaca; redaction antes de persistir; acesso operacional mínimo. |
| Suporte | O motor está em evolução Beta e a integração tem duas fronteiras de suporte: Hardness e componente oficial. | Runbook de triagem que separe erro de dado, erro do adaptador e erro do motor; canais oficiais e responsável fiscal definidos. |
| Atualização | Versão de regras pode mudar resultados e contratos. | Aprovação fiscal/técnica, ambiente isolado de homologação, testes de regressão, janela, rollback e trilha de versão. |
| Disponibilidade | Indisponibilidade não pode provocar cálculo alternativo pelo Hardness. | Falhar de forma explícita; não emitir/alterar documento; permitir retentativa controlada ou fluxo manual aprovado. |
| Privacidade | Entrada e resposta podem conter dados de operação e parceiros. | Minimização, mascaramento, retenção, controle de acesso e revisão LGPD/segurança. |

## 3. Condições mínimas de segurança e governança

Estas são **condições de projeto propostas**, não capacidades oficiais presumidas:

1. Executar o componente somente em rede privada, sem publicação de porta, proxy público, NAT de entrada ou acesso direto por navegador.
2. Permitir requisições apenas do backend Hardness para o serviço; nenhuma tela deve chamar a Calculadora diretamente.
3. Se o release suportar autenticação/autorização, habilitá-la com identidade de serviço e privilégio mínimo. Se não suportar, compensar com isolamento de rede, allowlist de origem e processo/usuário dedicado; registrar a limitação como risco aceito.
4. Nunca registrar em log payload integral, XML, memória detalhada, CNPJ/CPF, preços, bases, resultados ou segredos. Usar ID de correlação aleatório, versão e classes de erro; proteger os dados que uma auditoria aprovada vier a exigir.
5. Fixar timeout de conexão e total, tamanho máximo de requisição/resposta, concorrência e rate limit. Os valores só devem ser escolhidos após medição no spike, não nesta análise.
6. Ter health check que não execute cálculo fiscal, alerta de indisponibilidade e procedimento de degradação: bloquear o resultado como “não calculável com segurança”, sem fórmula/fallback local.
7. Atualizar somente por processo aprovado: origem oficial, versão, integridade/checksum ou assinatura se fornecida, análise de licença/terceiros, homologação técnica e fiscal, casos de regressão, janela, rollback e registro de aprovação.
8. Para cada cálculo que venha a ser persistido, registrar de forma auditável: versão do motor, versão do banco/tabelas normativas, versão OpenAPI/contrato, timestamp, identificador de correlação e hashes/snapshots autorizados de entrada e saída. O mecanismo de armazenamento ainda não foi projetado nesta task.

## 4. Dados e escopo futuro — confirmação a partir da Task 6

A Task 6 identificou dados potencialmente reaproveitáveis, mas explicitamente não atestou qualidade, vigência ou semântica fiscal. Esta task não fez cálculo, nem completou cadastro, nem converteu campos existentes em dados aptos.

| Dado potencial | Situação indicada pela Task 6 | Ainda depende de validação fiscal ou novo cadastro |
| --- | --- | --- |
| NCM | Potencialmente disponível em `D005.D005_Classificacao_Fiscal`. | Formato, vigência, qualidade e aplicabilidade por item. |
| CFOP | Potencialmente disponível em `D006.D006_Codigo_CFOP`. | Fonte canônica por operação e precedência; CFOP não substitui CST/cClassTrib. |
| CST e cClassTrib CBS/IBS | Há campos associados a produto/CFOP e previsão em itens documentais. | Preenchimento, vigência, versão de tabela, semântica e precedência por modelo. |
| Empresa, origem/destino, data e itens | Existem estruturas de empresa e documentos de venda, compra, orçamento, pedido, NF-e, cotação e OC. | Endereço/IBGE válido, data juridicamente aplicável, direção/modelo da operação, itens e componentes de base por fluxo. |
| Quantidade, unidade, preço, frete, desconto e outras parcelas | Há indícios nas estruturas documentais. | Mapeamento campo a campo ao contrato da versão da API; completude e regra para ausência. |
| NBS | Não identificado como disponível. | Cadastro/fonte oficial, vigência e uso em operações de serviço. |
| Regime, benefício, redução, diferimento, crédito presumido e situações especiais | Não confirmados como parâmetros fiscalmente aptos; a flag `C004_Flag_Tipo_Preenchimento_Reforma_Tributaria` é operacional, não regime. | Decisão fiscal, cadastro/fonte, evidência documental e contrato oficial aplicável. |
| Alíquotas, fundamentos e tabelas | Devem vir do motor oficial, não das rotinas SQL atuais do Hardness. | Versão do componente/tabelas e homologação do cenário. |

## 5. Dependências, riscos e aprovações

### Dependências bloqueantes

- Release oficial identificado, com origem, versão, checksum/assinatura quando disponível, manual de instalação e OpenAPI da mesma versão.
- Licença, avisos de terceiros, permissão de alteração e redistribuição analisados por jurídico/compliance.
- Confirmação de runtime, SO, porta/bind, persistência, uso de memória/disco, endpoint de saúde e política de atualização da versão escolhida.
- Ambiente isolado aprovado por infraestrutura e segurança, sem exposição pública, com rede privada e acesso restrito ao backend.
- Escopo fiscal homologado por modelo de documento, regime e operação; campos obrigatórios e casos oficiais definidos.
- Dono operacional, suporte, monitoramento, retenção e política de indisponibilidade aprovados.

### Riscos relevantes

- Contrato, leiautes e conteúdo normativo continuam evoluindo no produto Beta; integração sem congelar versão pode divergir de resultados e quebrar compatibilidade.
- Campo já existente no ERP pode ser incompleto, desatualizado ou ter significado diverso do exigido pela API.
- Sem autenticação local confirmada, o isolamento de rede torna-se controle essencial e deve ser revisado por segurança.
- Atualização sem regressão fiscal e rollback pode alterar resultados sem rastreabilidade suficiente.
- Logs e evidências podem ampliar indevidamente a exposição de dados fiscais e pessoais.
- A inexistência de prova de orquestração/rede privada no repositório impede afirmar prontidão operacional do parque instalado.

### Aprovações necessárias

- Fiscal/tributária: escopo inicial, dados, cenários, aceitação dos testes e interpretação de alertas.
- Segurança/LGPD: isolamento, identidade de serviço, logs, retenção e resposta a incidente.
- Infraestrutura/operações: host, runtime/contêiner, rede, monitoramento, backup, capacidade e janela.
- Engenharia Hardness: adaptador, versionamento, indisponibilidade, auditoria e plano de rollback.
- Jurídico/compliance: licença, redistribuição, alteração e dependências de terceiros.

## 6. Checklist da futura Task 8 — somente se aprovada para spike isolado

1. Registrar as aprovações acima, o objetivo limitado e a proibição de tráfego/documentos de produção no spike.
2. Obter manual e artefato diretamente da origem oficial aprovada; registrar URL, data, versão, hash/assinatura e licença antes de qualquer execução.
3. Confirmar, no material da mesma versão, runtime, SO, CPU/RAM/disco, porta, bind, persistência, atualização, health check, autenticação, logs e suporte.
4. Preparar ambiente descartável ou reversível, isolado em rede privada, com conta de serviço sem privilégio administrativo e sem porta pública.
5. Validar integridade/licença antes de instalar; inventariar componentes de terceiros/SBOM se fornecido.
6. Subir somente o componente oficial no ambiente isolado, aplicar hardening compatível e confirmar que não há telemetria/tráfego não previsto conforme documentação e observação autorizada.
7. Capturar o OpenAPI local e a versão do motor/tabelas; comparar com a documentação oficial, sem supor estabilidade entre releases.
8. Executar apenas casos sintéticos ou oficialmente autorizados, sem documento real, cálculo produtivo, cadastro ou alteração no Hardness; homologar entrada/saída com fiscal.
9. Medir saúde, consumo, limites, timeout, concorrência e falhas; testar parada/reinício, indisponibilidade e rollback do ambiente isolado.
10. Produzir relatório de aceite/reprovação com evidências, riscos residuais, runbook, plano de atualização/rollback e decisão explícita sobre a próxima fase. Não integrar ao Hardness sem uma autorização distinta.

## Fontes públicas oficiais consultadas

| Fonte | Data da consulta | Uso nesta análise |
| --- | --- | --- |
| [FAQ — Calculadora de Tributos v1.4, Receita Federal](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/apresentacoes/reforma-tributaria-do-consumo/faq-calculadora-v1-4.pdf) | 10/11/2026 | Modelo offline, API REST local, banco embarcado, versões e código aberto. |
| [Manual de Serviços RTC, Receita Federal](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/manuais/reforma-tributaria-do-consumo/manual-servicos-rtc.pdf) | 10/11/2026 | Guia, Swagger, download, JAR/Java 21, Docker e acesso local descritos. |
| [Swagger da Calculadora, Portal Nacional da RTC](https://consumo.tributos.gov.br/servico/calcular-tributos-consumo/api/swagger-ui/index.html) | 10/11/2026 | Existência e superfície pública do contrato OpenAPI; não substitui o contrato da versão local. |
| [Manual da Plataforma CBS, Receita Federal](https://www.gov.br/receitafederal/pt-br/centrais-de-conteudo/publicacoes/manuais/reforma-tributaria-do-consumo/manual-plataforma-cbs-21-maio-2026-07h40.pdf) | 10/11/2026 | Execução local, banco embarcado, versionamento e ausência declarada de telemetria/transmissão automática. |
| [Portal RTC — instruções da Calculadora Offline](https://consumo.tributos.gov.br/servico/calcular-tributos-consumo/calculadora/calculadora-offline/calculadora-offline-instrucoes) | 10/11/2026 | Referência oficial de instalação a validar no release aprovado; não foi usado para instalar ou baixar. |
| [Notícia — nova versão do Portal RTC, Ministério da Fazenda](https://www.gov.br/fazenda/pt-br/assuntos/noticias/2026/maio/receita-federal-lanca-nova-versao-do-portal-nacional-de-tributacao-sobre-o-consumo-da-reforma-tributaria) | 10/11/2026 | Evidência de evolução de releases, endpoints e comportamento Beta. |

Fontes internas lidas sem alteração: Task 6 e README do módulo de Reforma Tributária; documentação e unidades `systemd` de Webhooks; README de Observability; documentação operacional da REST API e código de configuração Docker existente. Essas leituras sustentam apenas as capacidades indicadas como evidência interna.
