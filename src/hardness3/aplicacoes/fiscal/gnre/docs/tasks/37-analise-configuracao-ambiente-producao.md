# Task 37 — Análise de configuração de ambiente GNRE: homologação e produção

## Escopo e conclusão

Esta task é exclusivamente analítica. A decisão proposta é tornar o ambiente uma
configuração administrativa **por empresa GNRE** (`C004`), com homologação (`2`)
como padrão e produção (`1`) bloqueada até habilitação explícita, auditável e
operacionalmente comprovada. Não houve migration, alteração de tabela, código
executável, interface, Portal, transmissão, consulta, PDF, XML persistido,
snapshot, guia, certificado ou cadastro ERP alterado.

> **Nota de superação — estado posterior às Tasks 40 e 42.** A proposta de
> bloqueio acima registra a análise desta task e não descreve o comportamento
> atual. Produção não é bloqueada artificialmente pelo módulo. `GNRE009`
> escolhe o ambiente apenas para novas guias, que exigem `GNRE007` compatível
> com empresa, UF e ambiente. Transmissão, consulta de resultado, fila e PDF
> usam exclusivamente o ambiente imutável do snapshot vinculado, sem fallback
> entre Produção e Homologação. Certificado válido da empresa e habilitação
> prévia do Web Service no Portal GNRE continuam sendo pré-requisitos externos
> para operação real em Produção.
> Todas as demais referências a bloqueio nesta análise pertencem à proposta
> histórica superada.

O ambiente de uma guia será um fato derivado exclusivamente do snapshot
`GNRE007` já vinculado a ela. A configuração da empresa escolhe o ambiente a
ser usado para buscar/selecionar o próximo snapshot; ela não pode sobrescrever
nem fabricar `GNRE007_Ambiente`.

## Diagnóstico do estado à época

O módulo atual é deliberadamente somente de homologação. Os locais abaixo são
pontos obrigatórios da futura centralização; não devem receber substituições
pontuais independentes.

| Arquivo/fluxo | Estado atual e responsabilidade | Risco/impacto da futura mudança |
| --- | --- | --- |
| `src/ConfigUfClient.php` | Endpoint `www.testegnre.../GnreConfigUF`; monta `TConsultaConfigUf` com `<ambiente>2</ambiente>`. Lê o A1 da empresa em `C004`. | Consulta deve receber ambiente resolvido e usar allowlist; a resposta continua a ser a única fonte de `GNRE007_Ambiente`. |
| `src/ReceptionClient.php` | Endpoint de teste `GnreLoteRecepcao`; envia XML de lote produzido anteriormente. | A transmissão deve selecionar endpoint pelo ambiente do snapshot da guia, nunca por parâmetro do navegador. É o maior risco fiscal. |
| `src/ResultClient.php` | Endpoint de teste `GnreResultadoLote`; monta `TConsLote_GNRE` com `<ambiente>2</ambiente>`, inclusive na obtenção explícita do PDF. | Consulta/PDF devem usar o mesmo ambiente da tentativa/guia; não pode haver queda de produção para teste. |
| `src/ConfigUfSnapshot.php` | `AMBIENTE = '2'`; valida resposta, grava `GNRE007_Ambiente`, busca snapshots por empresa + ambiente + UF + hash e exige `2` em `forGuide`. | Remover a constante como regra única e validar contra o ambiente solicitado/retornado. Preservar imutabilidade e a chave de isolamento já existente. |
| `src/GuideDraftController.php` e `src/GuidedDraftController.php` | Selecionam snapshots locais e inserem `GNRE001_GNRE007_Id`; a sessão guarda somente a escolha. | A busca deve ser filtrada pelo ambiente efetivo da empresa. A criação deve verificar vínculo, empresa, UF e ambiente, gravando apenas o ID do snapshot. |
| `src/GuideCenter.php`, `public/task28-guide-center.php` | Central lista e filtra fatos de guias por empresa. | Exibir ambiente derivado do snapshot, distinguindo legado sem snapshot; não inferir nem alterar guias antigas. |
| `public/task10-presentation.php`, `public/task34-transmission-modal.js` | Cabeçalho mostra literalmente “Homologação”; confirmação e ação usam `transmit_homologation`/“Transmitir em homologação”. | O texto, modal, confirmação reforçada e rótulos precisam derivar do snapshot. Produção deve ter aviso inequívoco e não reaproveitar texto de homologação. |
| `src/QueueWorker.php`, `src/ManualConsultFeedback.php` e rotas POST em `public/index.php` | Processam a fila `GNRE005`, consultam resultado e preservam o fluxo assistido; não retransmitem após resultado inconclusivo. | A fila deve levar o ambiente imutável da guia/tentativa até `ResultClient`; preservar bloqueio, idempotência e nenhuma retransmissão automática. |
| `src/OfficialPdfService.php` | Após resultado individual `situacaoGuia=0`, solicita PDF e armazena em `GNRE008`. | A consulta do PDF deve permanecer no ambiente da guia aprovada; PDF não é autorização e não troca ambiente. |
| `src/TlsPolicy.php` | Exige TLS 1.2, cadeia e hostname verificados. | Produção exige nova evidência de WSDL, cadeia, hostname e política TLS antes de entrar na allowlist. |
| `docs/referencias/README.md` e `docs/referencias/wsdl/` | Referências locais v2.17 e WSDLs capturados somente para homologação. O README lista o caminho de produção, mas não contém WSDL/validação produtivos. | São ponto de partida, não autorização para produção. Há nota histórica de pin no README, mas a Task 17.1 registra que o pin foi removido; a fonte vigente de política é `TlsPolicy.php` + Task 17.1. |

Fluxo atual: consulta de configuração -> validação/snapshot `GNRE007` ->
vínculo em `GNRE001` -> XML local -> `GNRE004` (recepção) -> `GNRE005`
(consulta) -> resultado individual -> `GNRE008` (PDF, quando aplicável).
Hoje todos esses caminhos pressupõem homologação, mesmo quando a transmissão
recebe XML cujo ambiente foi gerado em outra camada.

## Contrato arquitetural proposto

1. A empresa GNRE possui uma configuração administrativa de ambiente. O valor
   inicial e fallback para empresas sem configuração é homologação (`2`).
2. Produção (`1`) só pode ser selecionada se a configuração da mesma empresa
   estiver explicitamente habilitada e em estado administrativo pronto. A troca
   exige permissão administrativa e auditoria de antes/depois, autor e data.
3. Um resolvedor único no código recebe a empresa e devolve somente `1` ou `2`,
   depois de aplicar essas regras. Clientes SOAP, criação, seleção de snapshot,
   fila e interface usam esse resolvedor/contrato, não constantes próprias.
4. A consulta oficial envia o ambiente resolvido; `GNRE007_Ambiente` é gravado
   somente se o XML retornado pelo Portal informar e validar o mesmo ambiente.
   Não se aceita cópia cega da configuração administrativa.
5. Uma nova guia só pode ser criada com snapshot da empresa, UF e ambiente
   resolvidos. Ela herda o ambiente pelo `GNRE001_GNRE007_Id`; não é necessária
   nem recomendada uma cópia mutável de ambiente em `GNRE001`.
6. Depois de criada, a guia não troca de ambiente. Correção/reabertura conserva
   o snapshot e as auditorias `GNRE004`, `GNRE005` e `GNRE008`; uma nova guia
   em outro ambiente é outro ato explícito.
7. Guia legada com `GNRE001_GNRE007_Id` nulo, snapshot ausente ou ambiente
   inválido permanece sem ambiente conhecido, bloqueada para ações dependentes
   de ambiente. Não haverá inferência por data, XML, empresa ou status.
8. `GNRE007` continua imutável e isolada por `(empresa, ambiente, UF, hash do
   XML)`. O hash e o XML oficial preservado distinguem versões de configuração
   do mesmo ambiente/UF.

## Persistência proposta para task futura

Uma nova tabela de configuração por empresa é preferível a colunas em `C004`:
isola a política específica da GNRE, evita dispersar campos fiscais e permite
auditoria própria. Nome indicativo: `GNRE009` (a numeração deve ser confirmada
na task de migration, sem reservar schema agora).

| Campo candidato | Finalidade |
| --- | --- |
| chave e `C004_Id` único | Uma política GNRE por empresa. |
| `Ambiente_Padrao` (`2` inicial; `1` permitido sob regras) | Ambiente de novas consultas/novas guias. |
| `Producao_Habilitada` | Bloqueio explícito de produção, independente do valor padrão. |
| `Prontidao_Producao`/estado administrativo | Ex.: `NAO_AVALIADA`, `PENDENTE`, `APROVADA`, `SUSPENSA`; evita habilitação implícita. |
| usuário/data da criação e última alteração | Responsabilização administrativa. |
| usuário/data/motivo da habilitação, suspensão e mudança de ambiente | Trilha de auditoria com antes/depois; preferir tabela de eventos se o padrão ERP exigir histórico imutável. |
| versão/revisão otimista | Evita sobrescrever uma decisão administrativa concorrente. |

Não persistir nessa configuração: URL livre, endpoint editável, WSDL/SOAP,
PFX, senha, chave privada, nem dados fiscais por UF. Certificado continua no
cadastro protegido `C004`; a configuração apenas pode informar estado
administrativo, nunca duplicar segredos.

Alternativas descartadas:

- **Flag global do ERP:** permitiria que uma empresa mudasse o risco de todas
  as outras; incompatível com certificados e habilitações por CNPJ.
- **Campo de ambiente diretamente na guia editável:** quebraria a prova do
  contexto que originou XML, tentativa e PDF.
- **Copiar ambiente configurado para `GNRE007`:** transformaria configuração
  local em suposto retorno do Portal e violaria o fato imutável.
- **Endpoint armazenado por empresa:** permitiria SSRF, desvio do Portal e
  auditoria insuficiente; endpoints devem ser allowlist versionada no código.

## Endpoints, WSDL, TLS e certificado

As referências locais v2.17 registram os caminhos abaixo. Produção está apenas
documentada como mesmo caminho sob outro host; ainda não há captura ou
validação produtiva local.

| Serviço | Homologação (`2`) | Produção (`1`, pendente de revisão) |
| --- | --- | --- |
| Configuração UF | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreConfigUF` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreConfigUF` |
| Recepção lote | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcao` |
| Resultado lote | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLote` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLote` |
| Recepção consulta | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreLoteRecepcaoConsulta` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreLoteRecepcaoConsulta` |
| Resultado consulta | `https://www.testegnre.pe.gov.br/gnreWS/services/GnreResultadoLoteConsulta` | `https://www.gnre.pe.gov.br/gnreWS/services/GnreResultadoLoteConsulta` |

A task de centralização deve codificar uma allowlist fechada por
`ambiente + serviço`, inclusive SOAP action, versões e limites; nenhum valor
vem do banco ou POST. Antes de habilitar produção, a task específica deve,
com autorização explícita, capturar e versionar cada WSDL aplicável, SHA-256,
data, cadeia TLS, hostname apresentado, exigência de mTLS e compatibilidade de
SOAP/action. Deve revalidar a política de TLS 1.2, CAs do sistema e qualquer
necessidade de pin; não deve copiar a exceção/histórico de homologação sem
evidência atual de produção.

O certificado é obtido do cadastro seguro de `C004`, como já fazem os três
clientes. Operacionalmente, cada CNPJ deve ter certificado ICP-Brasil aplicável
(A1 ou A3, conforme aceito e suportado), vigente, com uso de autenticação de
cliente, e estar habilitado/autorizado no Portal para o ambiente escolhido. A
evidência de habilitação em homologação não prova habilitação em produção.

## Segurança operacional da futura implementação

- Alterar ambiente ou habilitar produção requer permissão administrativa
  específica por empresa, CSRF, reautenticação/confirmação reforçada e auditoria.
- Produção fica bloqueada enquanto `Producao_Habilitada` e prontidão aprovada
  não forem verdadeiras; suspensão deve bloquear novas ações produtivas.
- Antes de transmitir produção, a tela/modal deve exibir “PRODUÇÃO”, empresa,
  CNPJ, UF, receita, valor e snapshot; exigir confirmação explícita separada.
- O servidor revalida tudo: permissões, configuração, snapshot e ambiente da
  guia. Nenhum rótulo ou campo oculto do navegador autoriza produção.
- Produção nunca cai automaticamente para homologação, e nenhum timeout,
  erro técnico ou resultado inconclusivo causa reenvio automático em qualquer
  ambiente. `GNRE004` mantém a evidência e a próxima ação permanece explícita.
- `situacaoGuia=0` persistida e interpretada individualmente continua sendo a
  única aprovação; estado técnico final da fila não é autorização por si só.
- Idempotência e trilhas de `GNRE004` (recepção), `GNRE005` (consultas/fila) e
  `GNRE008` (PDF) devem ser preservadas e sempre associadas ao mesmo snapshot.

## Conteúdo da futura tela/tutorial administrativo

A tela deve ser informativa e administrativa, sem testar conexão nem chamar o
Portal automaticamente. Deve explicar a diferença: homologação é teste e não
gera obrigação de pagamento; produção gera efeitos fiscais/financeiros reais.
Deve conter links oficiais por ambiente, indicação de que o endpoint é gerido
pelo sistema, instruções para cadastrar o certificado no ERP sem exibir PFX,
senha ou chave, e um estado seguro de disponibilidade (por exemplo, “cadastro
de certificado presente/ausente”, sem revelar segredo).

O checklist pré-primeira transmissão produtiva deve cobrir: CNPJ correto e
habilitado no Portal de produção; certificado válido e autorizado; WSDL/TLS de
produção revisados; responsável fiscal aprovando empresa/UF/receita; ambiente
visível no snapshot; e usuários autorizados definidos. A liberação deve caber
somente a responsável fiscal/administrador GNRE formalmente designado, com
segregação de quem opera transmissões quando a política da empresa exigir.

## Plano incremental e critérios de liberação

1. **Migration/configuração por empresa e autorização administrativa.** Criar
   a persistência mínima, permissão e auditoria; padrão `2`; produção bloqueada.
   Liberar após testes de isolamento entre empresas, auditoria e negação de
   produção sem habilitação.
2. **Centralização de ambiente/endpoints no código.** Resolver ambiente e
   allowlist única para configuração, recepção, resultado e PDF. Liberar após
   revisão estática provar ausência de endpoint/`ambiente=2` avulsos e testes
   para ambos os ambientes sem chamada externa.
3. **Seleção de ambiente e snapshots na criação da guia.** Filtrar/criar por
   ambiente, validar resposta Portal versus solicitado, tornar ambiente de guia
   imutável e bloquear legado indeterminado. Liberar após testes de empresa,
   UF, hash, legado e tentativa de troca pós-criação.
4. **Captura/revisão de WSDL e TLS de produção.** Mediante autorização,
   versionar evidências oficiais de produção e revisar mTLS/cadeia/hostname.
   Liberar apenas com hashes, parecer técnico e confirmação administrativa;
   não transmite guia nesta fase.
5. **Interface/tutorial/checklist.** Implementar configuração, indicadores de
   ambiente, confirmação reforçada e material informativo. Liberar após testes
   de permissão, acessibilidade e confirmação servidor/cliente.
6. **Homologação produtiva controlada.** Somente com autorização explícita da
   empresa e responsável fiscal, roteiro manual e observação de uma operação
   real. Liberar progressivamente por empresa; falha inconclusiva interrompe o
   avanço e não dispara reenvio.

## Validação desta análise

- Revisão estática dos clientes, snapshots, criação, central, apresentação,
  fila, PDF e referências locais descritos neste documento.
- `git diff --check` deve passar.
- O diff deve conter somente este documento e `docs/PROJECT_STATUS.md`; não há
  migration, código executável, banco, Portal, XML persistido, transmissão,
  consulta, PDF, snapshot ou guia real alterados.
