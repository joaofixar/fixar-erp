# GNRE — Guia Nacional de Recolhimento de Tributos Estaduais

## Task 76 — piloto de lote físico GNRE pela Fila

Implementada localmente em 06/11/2026, aguardando revisão técnica e validação
manual explícita em Homologação. A seleção da Fila agora processa, em uma única
ação, grupos físicos novos: prepara e transmite; com recibo, persiste a primeira
consulta para 30 segundos depois e o modal a aciona automaticamente. Cada grupo
continua isolado dos demais. Grupos antigos já preparados
permanecem preservados e não são enviados automaticamente. Os grupos físicos
novos respeitam 30 guias, 600 KB e eventual limite oficial menor, sem alterar o
processamento sequencial individual. A divisão por UF,
receita ou snapshot foi removida: o contrato nacional permite várias UFs no
mesmo XML, preservando o snapshot próprio de cada guia. XML de lote, hash,
idempotência e participantes são persistidos antes da chamada; XML/XSD e
limites oficiais são validados por grupo. `0` somente autoriza com correlação
inequívoca, `1`/`2` são rejeições reenviáveis e `4` permanece pendente para
consulta; timeout e retorno ambíguo continuam bloqueando reenvio. Produção,
PDF de lote e alteração de `GNRE004`/`005`/`008` seguem fora de escopo.
Após correção, o bloqueio da Fila cobre todos os estados ativos de lote real e
o modal da Fila acompanha grupos recebidos/pendentes com contador persistido;
segue pendente de revisão técnica e validação manual, sem Portal.

O limite oficial `itensPorGuia` é bloqueado por guia durante a preparação, sem
afetar as demais selecionadas. A navegação não expõe mais a tela legada de lote
homogêneo; URLs antigas redirecionam para a Fila de transmissão.
O XML físico importa o `TDadosGNRE` congelado com o namespace oficial preservado
e normaliza somente fragmentos antigos que o herdavam do XML individual.

## Task 75 — piloto de lote físico real, isolado do MVP

Implementado localmente em 06/11/2026 e pendente de revisão técnica. Esta foi
a base técnica inicialmente acompanhada em uma tela separada; a Task 76 passou
a iniciá-la pela seleção da Fila, preservando esta tela apenas como apoio
técnico. Ela usa `GNRE010`/`GNRE011`, persiste XML/hash,
idempotência e lock antes de uma única recepção, conserva timeout/erro/SOAP
ambíguo como inconclusão e não cria registros individuais nem associa PDF.
Consulta pelo recibo é explícita; autorização individual continua dependente
da correlação segura da Task 65 e código individual `0`. Nenhuma chamada ao
Portal, migration ou alteração de guia real foi executada durante a implementação.

## Consolidação do schema mínimo para lote GNRE real

Concluída localmente em 05/11/2026: `GNRE010` passa a ser o cabeçalho futuro
do lote físico (idempotência, XML/hash, recepção, recibo, situação e agenda/
lease/evidência de consulta) e `GNRE011` passa a guardar a participação
imutável, ordinal apenas auditável, estado/resultado individual e decisão. A
instalação nova recebe o `CREATE TABLE` consolidado; o SQL manual idempotente
para bases que já possuam coortes está na Task 74. Coortes antigas permanecem
`PREPARADO`/`CANCELADO` com os campos novos nulos e não exigem ajuste manual.
O bloqueio transacional agora cobre todo lote ativo, sem criar vínculo mutável
em `GNRE001`. Não há PDF de lote, XML operacional, worker, Portal, transmissão,
consulta, banco real, migration automática ou mudança na Fila do MVP.

## Task 74 — tratamento conservador de resultado pendente do Portal

Corrigida em 05/11/2026 após regressão de retorno `401` sem guia individual.
Pendência oficial do resultado, incluindo processos `400`/`401`/`403` sem guia
e `situacaoGuia=4`, mantém a guia em `P` e a única `GNRE005` em
`PENDENTE`, sem retransmissão ou nova `GNRE004`. A próxima consulta usa a
estimativa persistida da recepção quando válida; sem ela, retoma em 3 segundos.
A tela individual e a Fila exibem orientação pública, descrição
sanitizada do Portal e horário brasileiro persistido; a Fila continua para as
guias não iniciadas e, enquanto o modal estiver aberto, retoma automaticamente
as pendentes no menor horário persistido. Falhas técnicas e as regras
de autorização, rejeição, PDF, lock/lease/token, empresa e ambiente permanecem
inalteradas. Somente uma guia individual única `0`/`1`/`2` finaliza; há exemplo
manual de reparo não executado, protegido por seleção e transação, na
[Task 74](tasks/74-tratamento-conservador-pendencia-portal.md).

## Task 73 — reanálise da transmissão GNRE em lote real com modelo mínimo

Concluída documentalmente em 05/11/2026. A revisão independente do Manual
local v2.17, XSDs e Tasks 63–68 confirma uma chamada física com várias guias,
recibo e consulta únicos, resultados `situacaoGuia` individuais e `pdfGuias`
no nível do lote. O contrato local não entrega chave segura do ERP por resultado
individual, nem associação individual de PDF; posição nunca pode ser usada.
`GNRE001`/`GNRE004`/`GNRE005`/`GNRE008` não bastam sem alterar sua semântica de
tentativa individual e criar ambiguidades de XML, recibo, fila e auditoria.

A recomendação mínima é cabeçalho físico de lote + participantes imutáveis,
com uma fila/lease por recibo de lote e decisão de correlação por participante.
Isso é conceitualmente a opção de duas estruturas, mas não torna
`GNRE010`/`GNRE011` atuais — coortes locais ainda sem envio — uma decisão
arquitetural definitiva. Não se recomenda uma tabela de eventos adicional antes
de justificativa de auditoria; a futura task de schema deve decidir se evolui ou
substitui as estruturas experimentais. Timeout, resposta parcial ou ambígua,
falha HTTP/TLS/SOAP e PDF sem relação inequívoca bloqueiam retransmissão e
atribuição individual. Empresa e ambiente nunca se misturam; UF, receita,
snapshot e layout permanecem homogêneos até prova contratual. Sem código,
schema, migration, banco, Portal, transmissão, consulta, PDF ou guia alterados.

## Task 70 — validação final de liberação do MVP GNRE

Revisão técnica concluída em 05/11/2026, sem chamadas ao Portal ou alteração
de banco. Lint do módulo e testes sintéticos pertinentes no `hardness-php7`
passaram, incluindo fila sequencial, reutilização do fluxo individual,
reabertura, PDF, geração por NF, bloqueio de coorte preparada e interface.
Não foi encontrado bloqueador nem correção pequena pré-liberação. O MVP está
apto para liberação controlada em Homologação, condicionado ao roteiro manual
de interface e interação autenticada registrado em
[Task 70](tasks/70-validacao-final-mvp.md). A Fila continua sequencial e não
é lote GNRE real; lote real/Portal único, financeiro automático e a redução
dos nomes técnicos `taskNN` seguem como itens pós-MVP documentados.

## Task 69 — liberação do MVP com Fila de transmissão sequencial

Implementada em 05/11/2026, aguardando validação manual. A Task 63 é o fluxo
operacional do MVP: as guias selecionadas são processadas sequencialmente e
individualmente, cada uma com XML, GNRE004, recibo, GNRE005, resultado e PDF
próprios. A interface não expõe preparação, consulta ou termos de lote real.
As Tasks 64 a 68 permanecem preservadas como infraestrutura e evolução futura,
mas não fazem parte da interface do MVP. A proteção técnica para participante
de `PREPARADO` no fluxo individual continua ativa. O lote real somente será
retomado por task própria após esta liberação.

## Task 67 — gestão local da coorte do lote GNRE real

Implementada localmente em 05/11/2026: a coorte `PREPARADO` pode ser cancelada
transacionalmente, por empresa GNRE ativa, com CSRF, `FOR UPDATE` e preservação
de `GNRE010`/`GNRE011`. A operação é idempotente e recusa qualquer evidência
individual (`GNRE004`, `GNRE005` ou `GNRE008`). Participantes de coorte
preparada ficam fora da Fila de transmissão e da revalidação individual; após
`CANCELADO`, voltam às regras normais. A tela local lista somente coortes da
empresa ativa e só permite cancelar as preparadas. Não houve schema/migration,
XML operacional, Portal, transmissão real, consulta, PDF ou alteração de guia.

## Task 66 — persistência segura da coorte do lote GNRE real

Implementada localmente em 05/11/2026: `GNRE010`/`GNRE011` e o serviço
transacional sem rota `GnreRealBatchCohort` preparam uma coorte homogênea de
rascunhos, congelando por participante o fragmento, SHA-256, fingerprint e a
versão `GNRE-BATCH-CORRELATION-v1` da Task 65. Empresa ativa, UF, ambiente do
`GNRE007` imutável, snapshot/layout, histórico individual e coorte preparada
concorrente são bloqueados sob transação/`FOR UPDATE`; repetição da mesma
seleção reutiliza a coorte `PREPARADO` e falhas fazem rollback integral.
Decisão/candidatos de correlação começam nulos. Não há XML, Portal,
transmissão, recibo, fila, consulta, PDF, `GNRE012` ou mudança de `GNRE001`;
Task 63 permanece processamento individual independente. Homologação e
Produção usam a mesma estrutura, sem liberação de transmissão real.

Correção: o congelamento agora parte de `GnreTdadosProjection`, validada sem
gerar XML pelos mesmos critérios do `XmlGenerator`. Isso evita inventar
emitente ou total, inclui somente nós que seriam transmitidos e bloqueia antes
da escrita uma guia cuja projeção não tenha as âncoras mínimas da Task 65.

## Task 65 — correlação segura para lote GNRE real

Implementada localmente em 05/11/2026 e corrigida na mesma data: contrato puro
v1 cria fragmento canônico auditável de `TDadosGNRE`, SHA-256 e impressão de
negócio para correlacionar retorno de lote sem usar posição, recibo, controle,
PDF, estado técnico ou NF-e. `documentoOrigem` (tipo e valor), `camposExtras` e
`identificadorGuia` permanecem apenas no fragmento auditável; não participam da
impressão de negócio, pois podem carregar NF-e ou não possuem semântica oficial
local suficiente. Assim, participantes que só diferem por NF-e ficam
`AMBIGUOUS`, nunca escolhidas por atalho. Só uma
participante compatível recebe `MATCHED`; nenhuma, mais de uma, ausência ou
parcialidade de dados bloqueiam como `UNMATCHED`, `AMBIGUOUS` ou
`INSUFFICIENT_EVIDENCE`. A autorização permanece exclusivamente em
`situacaoGuia=0` individual. Homologação e Produção compartilham o mesmo
contrato; o futuro lote obtém ambiente apenas de `GNRE007` imutável e não admite
mistura de empresa, ambiente, UF ou regras incompatíveis. O protocolo de ensaio
em Homologação e a evidência para a Task 66 estão documentados. Esta task não
habilita transmissão GNRE em lote real em nenhum ambiente e não alterou banco,
XML, Portal, fila, consulta ou PDF.

## Task 62 — preenchimento assistido por NF

Implementada em 05/11/2026, aguardando validação manual: a geração por NF
relê exclusivamente a NF autorizada `T007` e o snapshot imutável `GNRE007` da
guia. A projeção é gravada na mesma transação de ST, DIFAL, FCP isolado e RJ
DIFAL+FCP; no RJ ela é chamada explicitamente após os dois vínculos e antes do
item/valor FCP. Erro de persistência reverte guia, vínculos, item e valores. Quando
a configuração oficial exige e a compatibilidade é inequívoca, preenche
pagamento, documento NF-e/chave, mês/ano de emissão e destinatário CNPJ/CPF.
Inscrição estadual, convênio, detalhamento, adicionais e qualquer campo sem
opção oficial compatível continuam para revisão. O lote fixa Guia simples e
não inicia XML, Portal, PDF ou financeiro.

Correção de lote: a linha final agora preserva a mensagem efetiva retornada
pela criação, com status público. A identificação documental também reconhece
`CHAVE DA NFe`, `CHAVE DO DFe`, `Nota Fiscal Eletrônica` e `Nota Fiscal` exclusivamente entre as opções do
snapshot vinculado, sem fixar códigos.

Correção de data adicional: quando a receita tiver uma única definição oficial
de campo adicional intitulada “Data de Emissão”, inclusive declarada como
texto, o rascunho recebe a emissão persistida da NF; pagamento e vencimento
continuam na data de criação.

FCP-ST: a guia ST inclui `T007_Valor_FCP_ST`, o total autorizado persistido no
cabeçalho da NF, como valor tipo 12 quando permitido pelo snapshot da receita.
`T008_Valor_FCP_ST`/D006 permanecem somente na elegibilidade por CFOP e não
são somados nem usados para valor monetário da GNRE. FCP-partilha
(`T007_Valor_Total_FCP_Partilha`) não é reutilizado nessa composição.

No RJ, FCP-partilha sem DIFAL não tenta guia própria 100129; com DIFAL, ambos
compõem a única guia 100102. FCP-ST continua na guia ST.

Correção urgente de persistência: o bind do item assistido `GNRE003` foi
alinhado aos 15 parâmetros reais (`iissssssssssssi`), eliminando a falha de
criação/rollback causada pela assinatura com um tipo excedente. ST, DIFAL,
FCP e RJ usam esse mesmo contrato.

## Task 60 — inclusão assistida de GNRE a partir de NF (Escopos 1, 2 e 3)

Escopo 1 implementado em 04/11/2026, validado manualmente e aprovado pelo
usuário: D006 ganhou
os marcadores de elegibilidade `D006_Flag_Gera_GNRE_ST`,
`D006_Flag_Gera_GNRE_DIFAL` e `D006_Flag_Gera_GNRE_FCP`, todos `S`/`N` com
default seguro `N`, apresentados no cadastro de CFOP como “Gera GNRE de ST”,
“Gera GNRE de DIFAL” e “Gera GNRE de FCP”. Eles não alteram cálculos, NF,
estoque, financeiro, XML, Portal ou a guia; a documentação contém o SQL manual
não executado.

Escopo 2 implementado em 04/11/2026, validado manualmente e aprovado pelo
usuário: a rota
**Gerar guia por notas** exige período de emissão e consulta somente fatos
persistidos de `T007`/`T008`/`D006` da empresa GNRE ativa. A autorização usa
`T007_Numero_Protocolo_Nfe` preenchido; NF cancelada, inutilizada, denegada,
com protocolo de inutilização ou sem CFOP marcada é excluída. A tela apresenta
identificação, destinatário, UF, chave de acesso e valores ST/DIFAL/FCP apenas
para conferência. Escopo 3 implementado em 05/11/2026 e aguardando validação
manual explícita: o lote com CSRF seleciona, confere e cria rascunhos por
combinação NF + tributo (`ST`, `DIFAL` ou `FCP`), com vínculo único composto
`GNRE002_T007_Id` + `GNRE002_Tributo_Origem`; exige tipo permitido pelo snapshot local e não
preenche impostos, itens, valores, receita, vencimento ou dados fiscais. Sem
XML, transmissão, consulta, PDF, Portal, financeiro ou configuração automática.

Correção do Escopo 3: o schema explicita o estado de rascunho incompleto
(receita vazia, valor zero e vencimento nulo), e snapshot ausente ou ambíguo
bloqueia a combinação sem escolher versão automaticamente. ST, DIFAL e FCP no
grid só exibem valor quando seu respectivo flag de CFOP está ativo e o total
final de `T007` é positivo; continuam apenas como referência.

Apresentação do Escopo 3: a conferência do lote abre em modal interno
**Conferir rascunhos**, mantendo a lista de NFs ao fundo. O modal só relaciona
combinações com flag de CFOP ativo e total final positivo; componente zero/nulo
não é falha nem bloqueia os demais. X, Cancelar, recarregar ou iniciar nova
busca descartam apenas o plano transitório, sem criar registros. O fallback sem
JavaScript continua disponível na própria página. A criação permanece no POST
já protegido por CSRF, token transitório, releitura e idempotência NF +
tributo. Escopo 3 continua aguardando validação manual explícita.

## Task 59 — análise do MVP de geração manual de GNRE por NF

Concluída analiticamente em 04/11/2026: foi definido o MVP de busca de NFs
autorizadas e inclusão manual de rascunho GNRE por contrato de dados
persistidos, sem classes de NF-e no módulo GNRE. `T008`/CFOP fica restrita à
elegibilidade; os componentes vêm dos totais finais separados de `T007` (ST,
DIFAL destino, FCP, FCP-partilha e FCP-ST). A proposta vincula uma NF por
`GNRE002_T007_Id` com unicidade, prevê configuração empresa × UF subordinada a
`GNRE007` e revisão para cancelamento/alteração, sem automação de Portal,
financeiro ou emissão. Não houve tela, DDL, migration, banco, NF, guia, XML,
transmissão, consulta, PDF ou Portal.

## Task 57 — unificação do fluxo visível de transmissão GNRE

Implementada em 04/11/2026: **Transmitir GNRE** passou a ser o único fluxo
visível para operações com o Portal. **Processar** ficou restrita à validação
local e conferência do XML, apresentação curta do estado e acesso ao Histórico;
transmissão, consulta e PDF manuais não são mais exibidos nessa aba. Pendências
mostram o próximo horário permitido e orientam retomar o acompanhamento pelo
fluxo guiado. A autorização segue exigindo somente `situacaoGuia=0` individual
persistido; PDF pendente é iniciado pelo mesmo caminho guiado. Endpoints,
idempotência, bloqueios, fallback operacional, dados persistidos e integração
foram preservados, sem chamadas ao Portal ou mudanças de banco/schema.

## Backlog pós-MVP

As pendências deliberadamente adiadas — consolidação dos nomes `taskNN`,
configuração operacional de ambiente/certificado e descoberta da automação a
partir de NF-e — estão registradas em [POST_MVP_BACKLOG.md](POST_MVP_BACKLOG.md).
Elas não iniciam implementação e devem continuar sendo tratadas uma por vez.

## Task 58 — inventário de nomes técnicos `taskNN` ativos

Concluída analiticamente em 04/11/2026: o inventário separa shell, Central,
modal guiado e painel ainda carregados sob nomes `taskNN`; compatibilidades
servidor/sessão `task15_*` e `task18_*`; flashes `task23`/`task25`; o fluxo
manual legado `task10-manual-flow.php` e suas actions sem prefixo ainda com
handlers a caracterizar; e suíte histórica de testes/documentação. A UI atual usa o fluxo canônico
`GuideDraft`/`guide-*`; Task 15 não é uma segunda UI ativa, mas seus POSTs
ainda são aceitos/controlados. Nenhum nome foi renomeado. O documento define
pequenas tasks futuras e bloqueia remoção por mera ausência visual. Sem PHP,
JS, CSS, rotas, POST, sessão, banco, Portal, transmissão, consulta, fila, PDF
ou guia real alterados.

## Task 56 — Histórico: resultados de todas as tentativas

Implementada em 04/11/2026: Histórico deixou de projetar somente a GNRE005
mais recente. Cada cartão GNRE004 usa sua própria GNRE005 finalizada, por
`GNRE005_GNRE004_Id`, e interpreta somente o SOAP persistido daquela fila.
Assim, tentativas anteriores continuam mostrando seu resultado individual e
motivos, mesmo que compartilhem recibo com outra tentativa. SOAP ausente ou
inválido não gera resultado inventado. Sem Portal, banco, schema, fila,
transmissão, consulta, PDF ou guia real.

Complemento: autorização no Histórico é exibida apenas para `situacaoGuia=0`
individual persistida, como “Processada com sucesso” e “Guia
autorizada/emitida pelo Portal”; o retorno de lote `402` sozinho não autoriza.
Em Processar, a mesma confirmação elimina controles redundantes e mantém apenas
a mensagem de autorização e o fluxo coerente de PDF.

## Correção — rejeição individual: Processar e Histórico

Implementada em 04/11/2026: uma rejeição individual final já elegível à
reabertura recebe, em **Processar**, um bloco curto com não autorização,
motivos persistidos e a ação **Corrigir e reenviar**, no lugar das orientações
e controles redundantes de resultado final. Em **Histórico**, cada motivo
(código, descrição e campo) aparece somente no cartão da tentativa vinculada
ao resultado persistido, separado de “Recebida pelo Portal”. Pendência,
inconclusivo, rejeição de recepção e autorização não foram alterados. Sem
Portal, transmissão, consulta, PDF, banco, schema ou guia real.

## Correção urgente — reabertura para correção de guia rejeitada

Implementada em 04/11/2026: a aba **Processar** voltou a apresentar
**Corrigir e reenviar** ao lado do resultado individual rejeitado elegível. A
ação reabre a mesma `GNRE001` como rascunho, invalida somente o XML local e
preserva GNRE004/GNRE005/GNRE008, recibos, SOAP, tentativas e Histórico. A
elegibilidade agora confere a tentativa mais recente completa: um único recibo
recebido, uma única fila `FINALIZADA`, SOAP válido e um único
`situacaoGuia=1` ou `2`; `0`, resultados múltiplos, pendência, processamento,
SOAP inválido, ausência de fila e retorno inconclusivo bloqueiam. Reabertura
repetida em rascunho é idempotente e o reenvio normal continua criando a
próxima GNRE004 apenas após novo XML local válido. Não houve migration, alteração
de schema, Portal, transmissão, consulta, PDF ou guia real.

## Task 55 — revisão de textos Processar e Histórico

Implementada em 04/11/2026: **Processar guia** agora explica que Transmitir
GNRE é o fluxo guiado recomendado e que seus controles são avulsos. `F` usa
texto neutro, sem confundir finalização técnica com autorização; a confirmação
permanece vinculada ao resultado individual persistido `situacaoGuia=0`. A
consulta em andamento orienta atualização manual, sem prometer atualização
automática. **Histórico** apresenta cartões auditáveis por tentativa, com fatos
persistidos já carregados (situação, recibo, retorno, data/hora e resultado
individual quando associado), sem SOAP/XML/credenciais. Não houve banco,
Portal, transmissão, consulta, fila, PDF ou alteração de guia real.

## Task 54 — remoção do Controle da Central de Guias

Implementada em 04/11/2026: `?view=list` não mostra mais a coluna Controle,
seu cabeçalho, célula ou travessão associado; a tabela foi ajustada para nove
colunas. No XSD do resultado GNRE, `numeroControle` é opcional. O retorno pode
conter apenas `nossoNumero`, identificador interno da UF que não substitui o
controle, e o PDF pode exibir controle ausente do XML estruturado. Assim, a
Central não trata Controle como informação operacional. A projeção normalizada
da Task 53 continua preservada e só é preenchida se o Portal retornar
`numeroControle` estruturado e válido; ela não confirma emissão. Não houve
alteração de banco, GNRE004/GNRE005/GNRE008, XML, transmissão, consulta, PDF,
fila, snapshots, ambientes, regras fiscais, Portal ou guias reais.

## Task 53 — número de controle normalizado da guia autorizada

Implementada em 04/11/2026: `GNRE001_Numero_Controle` é uma projeção nullable
e indexada por empresa do `numeroControle` oficial, somente quando o SOAP do
resultado individual recém-persistido é válido, contém exatamente uma guia com
`situacaoGuia=0` e controle numérico válido. GNRE005 continua sendo a evidência
histórica; recusas, pendências, timeout, SOAP inválido, resultado inconclusivo e
controle ausente não preenchem a coluna, e valor existente não é sobrescrito.
`numeroControle` é opcional no XSD; o resultado pode trazer somente
`nossoNumero`, identificador interno da UF que não o substitui, e o PDF pode
exibir controle sem retorno estruturado no XML. Portanto a Central não exibe
Controle como informação operacional. A persistência normalizada continua
disponível somente quando o Portal fornecer `numeroControle` estruturado e
válido, sem confirmar emissão por si só. O SQL manual está em
[Task 53](tasks/53-numero-controle-normalizado-guia.md), não foi executado, e
não houve backfill nem nova consulta ao Portal.

## Task 50 — correção visual da ação Adicionar em guias com múltiplos itens

Implementada em 04/11/2026: as ações `Adicionar documento/item` (tipo `1`) e
`Adicionar receita` (tipo `2`) agora têm estrutura explícita de ação e
espaçamento abaixo da lista, mantendo o botão primário do módulo. O link de
modal e o fallback normal pelo `href` foram preservados; a ação da guia simples
(tipo `0`) não foi alterada. Não houve mudança em POST, URL, JavaScript, regras
GNRE, itens, valores, vencimentos, XML, transmissão, consulta, PDF, ambiente,
banco ou guias reais.

## Task 45 — exclusão segura e arquivamento

Implementada: exclusão física é restrita a rascunho sem histórico; rejeição técnica final de recepção pode ser arquivada, mas nunca excluída. O arquivamento é reversível e preserva auditoria. O SQL manual para `GNRE001_Arquivada` e seu índice está em `tasks/45-exclusao-arquivamento-seguro.md`; não foi executado.

## Task 47 — compatibilidade antes do SQL manual de arquivamento

Implementada: o SQL manual da Task 45 permanece necessário para arquivar e desarquivar. Antes dele, a Central continua acessível e não consulta nem exibe `GNRE001_Arquivada`; a exclusão segura de rascunho segue disponível somente com status `R` e sem GNRE004, GNRE005 ou GNRE008. Nenhum banco, migration, guia real, Portal, ambiente, snapshot, XML, transmissão, consulta ou PDF foi alterado.

## Task 48 — orientações de ambiente, Portal e certificado

Implementada: Configuração exibe em painel independente a orientação “Automação via Web Service”: transmissão e consulta GNRE exigem certificado digital e-CNPJ válido e habilitação solicitada diretamente no Portal GNRE antes do primeiro uso. Os links externos visíveis para [Produção](https://www.gnre.pe.gov.br:444/gnre/portal/automacao.jsp) e [Homologação](https://www.testegnre.pe.gov.br:444/gnre/portal/automacao.jsp) abrem em nova aba, em linhas separadas e com largura uniforme, e permanecem separados de Salvar. A tela é apenas orientativa: não integra, cadastra, armazena ou ativa certificados; essa integração segura continua fora do escopo e exige tarefa própria. A seleção segue influenciando somente novas guias; as existentes usam exclusivamente o ambiente imutável do snapshot `GNRE007`. Não houve alteração de `GNRE009`, banco, endpoints, Portal, transmissão, consulta, fila, PDF, XML ou regras fiscais.

## Task 49 — revisão de textos da tela Nova guia

Implementada: a rota Nova guia agora orienta a seleção de UF, a busca explícita de configurações e a escolha do tipo de guia com textos voltados à ação. O reinício informa o descarte apenas do estado transitório, e indisponibilidade de certificado é avisada dentro do bloco de configurações, sem modal e sem criar guia. Actions, CSRF, ambiente, persistência, Portal, transmissão, consulta, fila e PDF foram preservados.

Complemento implementado: a consulta manual de configurações categoriza certificado indisponível, recusa explícita de acesso ao Web Service em falha SOAP válida e retorno inconclusivo. Apenas a recusa declarada pelo Portal é apresentada como não autorizada; HTTP, TLS, timeout, SOAP inválido e demais erros permanecem neutros. Flashes de item seguem restritos à guia/modal e não são exibidos em Nova guia. Nenhuma categoria cria guia, transmissão, consulta de resultado ou PDF.

Correção complementar: a inspeção de Fault SOAP válido também ocorre quando a resposta HTTP não é 2xx. O código HTTP isolado não classifica acesso; HTML, corpo vazio, XML inválido, Fault não conclusivo, timeout, TLS e corpo acima do limite permanecem inconclusivos.

## Correção — troca de empresa abre a Central de Guias

Implementada em 04/11/2026: somente o POST `select_gnre_company` agora responde com PRG/303 para `?view=list`. A revalidação de sessão, permissão, empresa autorizada e CSRF foi preservada; o destino fixo não propaga `edit`, aba, `gnre34`, filtros, modal ou parâmetros da empresa anterior. A rota de Visão geral continua disponível, mas não é usada após a troca. Não houve mudança em banco, schema, snapshots, XML, guias, transmissão, consulta, fila, PDF, Portal ou regras fiscais.

## Task 44 — consistência visual de itens, vencimentos e emitente

Implementada em 03/11/2026: a aba Dados e itens mostra vencimento e valor de
itens múltiplos apenas em formato brasileiro, e o tipo único vazio passou a
oferecer inclusão explícita em modal com fallback HTML para o formulário normal.
O emitente ganhou grade legível de duas colunas no modal e no fallback. A task é
exclusivamente de apresentação e preserva todos os dados e contratos existentes:
persistência, `GNRE001`–`GNRE009`, `GNRE007`, XML, transação, transmissão,
consulta, PDF e ambientes não foram alterados.

## Task 41 — concluída analiticamente; conclusão histórica superada

A análise de [pré-requisitos e liberação controlada](tasks/41-analise-liberacao-controlada-producao.md) distingue fatos confirmados no Manual GNRE v2.17 e no módulo, pendências de validação real e decisões propostas. Ela separou a preferência administrativa `GNRE009` da então proposta autorização produtiva por empresa, propôs validação local de certificado GNRE sem NF-e, preservou ambiente imutável por `GNRE007`, ausência total de fallback para Homologação e emissão somente por resultado individual persistido `situacaoGuia=0`. O plano foi decomposto em autorização, certificado, configuração/snapshot, transmissão e consulta/PDF. Não houve chamada ao Portal, alteração de banco/schema, snapshot, guia, XML, transmissão, consulta ou PDF naquela análise.

> **Superada pelas Tasks 40 e 42:** Produção não é bloqueada artificialmente
> pelo módulo. `GNRE009` define somente a preferência para novas guias; uma
> nova guia exige `GNRE007` compatível com empresa, UF e ambiente, e a guia
> criada usa apenas o ambiente imutável do snapshot em transmissão, consulta de
> resultado, fila e PDF. Não há fallback entre ambientes, e alterar `GNRE009`
> não muda guias existentes. Certificado válido da empresa e habilitação prévia
> do Web Service no Portal GNRE continuam necessários para a operação real em
> Produção, como pré-requisitos externos.

## Task 26.5 — reaberta; aguardando validação visual

Página da guia organizada em abas URL-preserváveis: resumo, dados e itens, validação/XML, transmissão, documento e histórico. O shell validado na Task 26.2 foi restaurado para todas as rotas; falta a validação visual no navegador antes do encerramento. A entrega é exclusivamente de apresentação; regras fiscais, snapshots e integrações continuam inalterados.

## Task 27 — concluída documentalmente

A especificação de [Visão geral e listagem operacional](tasks/27-visao-geral-listagem-operacional.md) separa o painel de pendências da central completa de Guias, mapeia fatos disponíveis em `GNRE001`–`GNRE008`, limita emissão a `situacaoGuia=0` persistida e lista as decisões que ainda exigem persistência. Não houve implementação funcional, alteração de tela, CRUD, transmissão, XML, PDF, banco, regras fiscais, rotas, shell, sidebar ou Task 26.5; aguarda validação antes de iniciar qualquer Task 27.x.

## Task 37 — concluída analiticamente; conclusão histórica superada

A análise de [configuração de ambiente por empresa](tasks/37-analise-configuracao-ambiente-producao.md) define homologação (`2`) como padrão e produção (`1`) como habilitação administrativa explícita, auditável e isolada por empresa. O ambiente da guia deve permanecer derivado exclusivamente do snapshot imutável `GNRE007`, validado contra o retorno do Portal; não há inferência para legado nem troca após criação. A produção continua bloqueada até tasks separadas de persistência/permissão, centralização de endpoints, snapshots, revisão WSDL/TLS, interface e operação controlada. Nenhuma migration, código executável, interface, banco, Portal, transmissão, consulta, PDF, XML persistido, snapshot ou guia real foi alterado.

> **Superada pelas Tasks 40 e 42:** a conclusão de bloqueio acima é histórica.
> Produção não é bloqueada artificialmente pelo módulo. `GNRE009` escolhe o
> ambiente somente para novas guias, que exigem `GNRE007` compatível com
> empresa, UF e ambiente. Transmissão, consulta de resultado, fila e PDF usam
> exclusivamente o ambiente imutável do snapshot vinculado, sem fallback entre
> Produção e Homologação. Certificado válido da empresa e habilitação prévia do
> Web Service no Portal GNRE permanecem pré-requisitos externos da operação
> real em Produção.

> **Fonte de verdade do módulo.** Antes de iniciar uma tarefa, leia este
> arquivo e o arquivo da tarefa. Ao concluí-la, registre o handoff nela e
> atualize a situação abaixo. Esta documentação deve permitir a continuidade
> sem depender de conversas anteriores.

## Objetivo do MVP manual

Permitir ao usuário cadastrado no ERP criar uma GNRE manualmente, vincular zero,
um ou mais documentos de origem, transmitir diretamente ao Portal Nacional GNRE
e acompanhar o resultado. O MVP atende somente UFs que participem daquele
Portal; SP e ES não fazem parte desta entrega.

Uma guia pode ser individual ou de apuração. A diferença é operacional: ambas
são um cabeçalho de guia e podem possuir documentos vinculados. O MVP não
calcula imposto, não seleciona NF-e automaticamente, não fecha competências e
não integra pagamento. A automação futura da NF-e deverá criar os mesmos
registros, sem alterar o fluxo de transmissão.

## Decisões de arquitetura

- O módulo será uma aplicação independente em `aplicacoes/fiscal/gnre`, com interface
  própria, sessão e infraestrutura comum do ERP. O padrão de referência é
  `aplicacoes/pcp/apontamento`, não a estrutura de NF-e.
- A GNRE **não pode incluir classes, chamar métodos ou depender de qualquer
  implementação da NF-e**. Código de NF-e pode ser estudado somente como
  referência. Por exemplo, a leitura de PFX será reimplementada no namespace
  GNRE se a lógica for aproveitável; `carregaCertP12()` nunca será chamado.
- Conexão, sessão ERP, empresa ativa, permissões e CSRF são infraestrutura
  compartilhada permitida. Certificado, SOAP, XML/XSD, persistência, logs,
  estados e worker serão implementados pelo módulo GNRE.
- O envio de lote é assíncrono no protocolo oficial. O usuário aciona a
  transmissão, que registra o recibo; `GNRE005` preserva o estado técnico de
  consultas e tentativas. Não haverá cron, `.sh`, daemon ou dispatcher
  multi-banco. A exceção limitada da Task 34 é a consulta assistida pelo
  navegador: somente após o usuário iniciar e confirmar **Transmitir GNRE**,
  com o modal aberto e apenas no horário persistido em `GNRE005`. Não existe
  atividade após fechar a página/modal, nem retransmissão automática. O worker
  permanece recurso administrativo/manual. A fila não bloqueia faturamento e
  não reaproveita `scripts/queueNF/queueNF.php`.
- **Task 52:** o botão principal **Transmitir GNRE** abre o fluxo guiado e
  inicia automaticamente a geração e validação local do XML. Erros locais
  públicos encerram o ciclo sem criar tentativa ou chamar o Portal, com retorno
  para **Dados e itens**; XML válido mantém a prévia e exige confirmação
  explícita antes da transmissão. O envio, a fila de consulta e o PDF seguem
  os mesmos POSTs e serviços já existentes.
  A correção posterior preserva a aba de origem durante os redirects e guarda
  contexto transitório isolado por empresa e guia para reconstruir o modal.
- O banco será incremental: nenhuma tabela ou coluna será criada para reservar
  espaço para função futura. Cada migration nasce na tarefa que usa a estrutura.
- PDF, linha digitável, código de barras e PIX retornados pelo Portal serão
  dados de emissão, não confirmação de pagamento. O pagamento fica fora do MVP.

## Referências confirmadas no repositório

- `aplicacoes/pcp/apontamento/bootstrap/app.php`: modelo atual de aplicação
  isolada, sessão, CSRF e contexto de empresa.
- `scripts/atualizacaoBase.php`: ponto único para migrations distribuídas.
- `scripts/queueNF/etapa3-enviarXml-sincrona.php` e
  `hardness/class/nfe4_ext.class.php`: somente referências para identificar
  localização de certificado e comportamento de leitura de PFX; não serão
  importados pelo módulo.
- `hardness/carta_correcao`: referência apenas de experiência de usuário
  (cadastro seguido de transmissão). Sua transmissão síncrona e seu código
  legado não serão reutilizados.

## Tarefas e situação

| Ordem | Tarefa | Situação |
| --- | --- | --- |
| 00 | [Fundação documental e contrato oficial](tasks/00-fundacao-documental.md) | Concluída em 28/10/2026 |
| 01 | [Fundação da aplicação e acesso ERP](tasks/01-fundacao-aplicacao.md) | Concluída em 28/10/2026 |
| 02 | [Modelo mínimo e cadastro manual](tasks/02-modelo-cadastro-manual.md) | Concluída e validada em 28/10/2026 |
| 03 | Consulta de configuração da UF | Concluída e validada em 28/10/2026 |
| 04 | Geração e validação local do XML | Concluída e validada em 28/10/2026 |
| 05 | Transmissão e persistência do recibo | Concluída e validada em 28/10/2026 |
| 06 | Fila de consultas e recuperação | Concluída e validada em 28/10/2026 |
| 07 | Consulta manual do resultado pela interface | Concluída e validada em homologação em 28/10/2026: guia #4, R$ 1.000,00, HTTP 200/código 100/recibo 26000000042859; consulta manual sem retransmissão retornou 403 e levou `P -> F` e `PENDENTE -> FINALIZADA`, preservando GNRE004 `RECEBIDA`. `F` é resultado técnico final, não autorização/guia emitida; os motivos individuais ainda não são exibidos. |
| 08 | Interpretação e exibição do resultado individual | Concluída em 29/10/2026 — a tela interpreta somente o SOAP já persistido em `GNRE005`, exibe lote, situação individual e motivos de rejeição; `F` permanece resultado técnico final, não autorização. |
| 09 | Configuração oficial UF/receita e emitente | Concluída e validada manualmente em homologação em 29/10/2026: consulta explícita BA retornou código 450 (“Consulta da Configuração efetuada com sucesso”) e 13 receitas; o XML confirmou regras por receita para emitente, detalhamento, produto, período, documento, destinatário, vencimento, pagamento, convênio, campos adicionais, tipos GNRE e PIX. Nenhuma guia foi criada, transmitida ou consultada. A task aplica listas/requisitos e interface progressiva com limpeza de campos ocultados, revalida antes de GNRE004 e gera/antecipa emitente a partir do cadastro ERP, sem NF-e, cache de banco ou alteração da guia #4. |
| 10 | [Interface moderna e navegação por empresa](tasks/10-interface-moderna-navegacao-empresa.md) | Corrigida em 29/10/2026; fluxo manual de documentos, itens e validação XML restaurado, aguarda validação manual no navegador com múltiplas empresas. Contexto GNRE independente de `Empresa_Atual`; configuração em sessão por empresa/UF, sem transmissão/consulta e sem alteração da guia #4. |
| 11 | [Fluxo guiado para criação manual](tasks/11-fluxo-guiado-criacao-manual.md) | Concluída em 29/10/2026 — nova guia em duas etapas, com consulta de configuração apenas em “Continuar”, reutilização segura da sessão por empresa/UF e erros de cadastro exibidos no layout GNRE. Sem Portal, migration ou alteração de transmissão/fila. |
| 12 | [Análise e modelagem por tipo oficial de GNRE](tasks/12-modelagem-por-tipo-oficial-gnre.md) | Concluída analiticamente em 29/10/2026. Documenta os tipos oficiais `0` (Simples), `1` (Múltiplos Documentos de Origem) e `2` (Múltiplas Receitas), a interseção de tipos da UF/receitas, hierarquia XML e lacunas do modelo atual. Nenhuma implementação funcional foi realizada: sem alteração de interface, PHP, banco, XML, tabelas, migrations, guias, transmissão, consulta ou fila. Aguarda validação da modelagem antes de qualquer próxima task. |
| 13 | [Modelo de dados por tipo oficial](tasks/13-modelo-dados-por-tipo-oficial.md) | Concluída em 29/10/2026. Atualiza exclusivamente os `CREATE TABLE` completos: tipo oficial e pagamento global em `GNRE001`, vencimento/convênio por item em `GNRE003` e valores tributários/FCP filhos em `GNRE006`. Mantém campos legados; não há `ALTER TABLE`, backfill, execução de banco, interface, XML, transmissão, consulta, fila ou Portal. A recriação local manual e a validação estática estão documentadas. |
| 14 | [Gerador e validador por tipo oficial](tasks/14-gerador-validador-por-tipo-oficial.md) | Concluída e corrigida em 29/10/2026. XML local exige `GNRE001_Tipo_GNRE` explícito, valida interseção UF/receitas e regras oficiais por tipo, aplica `valorExigido` (omissão só para guia integralmente `N`), bloqueia mistura cálculo UF+valores, preserva a checagem de compatibilidade com `GNRE001_Valor`, N/S/O excepcional de convênio/FCP, destinatário no item e valores `GNRE006`; valida XSD local. Guias legadas sem tipo são bloqueadas sem migration ou inferência. Sem interface, transmissão, consulta, fila, certificado ou Portal; Task 15 não iniciada. |
| 15 | [Interface por tipo oficial](tasks/15-interface-por-tipo-oficial.md) | Reaberta para corrigir continuidade sem cache da UF: dados persistidos permanecem visíveis em consulta; XML e edição dependente ficam bloqueados até recarga manual. Regressões disponíveis até Task 16 passaram no hardness-php7. |
| 16 | [Motor de formulário por configuração oficial](tasks/16-motor-formulario-configuracao-oficial.md) | Em correção/validação. O contrato ganhou grupos com filhos explícitos e semântica centralizada de convênio/FCP, sem alterar Task 15, persistência, XML ou Portal. A Task 17 permanece bloqueada até aprovação. |
| 17 | [Persistência e versionamento TConfigUf](tasks/17-persistencia-versionamento-tconfiguf.md) | Implementada e aplicada localmente em 30/10/2026: GNRE007 mantém snapshots imutáveis por empresa/ambiente/UF/hash e GNRE001 tem vínculo nullable. O DDL atingiu somente GNRE001/GNRE007; `SHOW CREATE`/coluna/índice foram confirmados e a guia legada #7 permaneceu intacta, com vínculo NULL. |
| 18 | [Interface progressiva por snapshot oficial](tasks/18-interface-progressiva-snapshot.md) | Concluída em homologação em 02/11/2026. Guias #8 e #9 foram recebidas, mas tiveram rejeições cadastrais individuais registradas e encaminhadas à Task 19; não foram alteradas. |
| 19 | [Pré-validação cadastral](tasks/19-prevalidacao-cadastral.md) | Corrigida pela Task 21 em 02/11/2026: a falha era a redução precoce do IBGE e a hipótese/filtro incorreto sobre `D020_UF`; seleção e reabertura agora usam D020 → D018. Sem alteração/retransmissão de #8/#9/#13. |
| 20 | [Consolidação da fonte oficial de municípios](tasks/20-fonte-oficial-municipios.md) | Conclusão corrigida em 02/11/2026: D020 não estava incompleta. Ela já contém as cidades e IBGE-7; a consulta da Task 19 usava/supunha incorretamente `D020_UF`. O plano de catálogo/tabelas GNRE próprio foi substituído pela Task 21. |
| 21 | [Correção da seleção de municípios via D018/D020](tasks/21-correcao-municipios-d018-d020.md) | Concluída em 02/11/2026: UF via `D018_UF`, identidade IBGE-7 preservada sem migration e XML pelos últimos cinco dígitos. Legado sem IBGE-7 ou sem D020/D018 é bloqueado explicitamente. Sem Portal, schema, GNRE007, guias #8/#9/#13 ou estados. |
| 22 | [Seleção e validação de valores GNRE por item](tasks/22-valores-por-item.md) | Concluída em 02/11/2026: `valorExigido=A` agora representa Principal **ou** Total, com escolha única derivada do snapshot `GNRE007`; servidor/XML bloqueiam 11+21, FCP segue apenas o par aplicável, `N` não aceita valores e legado incompatível é avisado e bloqueado até correção. Sem Portal, transmissão, GNRE004/GNRE005 ou alteração das guias #8/#9/#13. |
| 23 | [Corrigir e reenviar guia rejeitada](tasks/23-corrigir-e-reenviar-guia-rejeitada.md) | Semântica corrigida em 03/11/2026, aguardando validação manual: somente um resultado individual inequivocamente `1` ou `2` reabre a própria `GNRE001` como `R`; `0` e qualquer conjunto múltiplo bloqueiam conservadoramente. A reabertura não cria/copia guia e preserva integralmente GNRE004/GNRE005, recibos, SOAP, XML enviado, resultado e fila. XML local é invalidado e retransmissão continua explícita, criando a próxima tentativa técnica no mesmo registro. O campo legado de origem permanece sem uso. Nenhuma guia real foi tocada. |
| 24 | [Análise de documento oficial e PDF da GNRE](tasks/24-analise-documento-pdf-gnre.md) | Concluída analiticamente em 02/11/2026: Manual v2.17/XSD confirmam `GnreResultadoLote` com `incluirPDFGuias=S` e retorno base64 `pdfGuias`; a consulta atual usa `N`, explicando a ausência no SOAP #19. Recomendada obtenção e preservação do PDF oficial do Portal após `situacaoGuia=0`; representação ERP é somente fallback explicitamente não oficial. Sem chamada ao Portal, PDF, migration, alteração de #19, GNRE004/GNRE005 ou início de task seguinte. |
| 25 | [Obtenção e armazenamento do PDF oficial GNRE](tasks/25-pdf-oficial-gnre.md) | Concluída e validada em homologação em 02/11/2026: a guia #19 aprovada (recibo `26000000045967`, controle `202690001313411`) obteve o PDF oficial pela interface e o abriu com sucesso. O Portal persistiu o documento original em `GNRE008 #1` (empresa 3, guia 19, tentativa 7, `PDF_OFICIAL`/`PORTAL_GNRE`, `application/pdf`, 10.333 bytes, SHA-256 `2a183e7943c442091d334c00998d5904e61eda4efa466ccd72bbe5b41b6a6b40`, em `2026-11-02 15:01:49`). As marcas de teste/inválida para pagamento e o aviso de QR Code são retornos esperados do Portal de homologação, não falha do ERP. |
| 26 | [Análise de experiência, navegação e especificação da interface GNRE](tasks/26-analise-ux-interface-gnre.md) | Concluída analiticamente em 02/11/2026: especifica navegação, página de guia por abas, experiência reativa derivada de `GNRE007`, matriz dos tipos 0/1/2, bugs UX e plano incremental. Sem alteração de código/CSS/JS, banco, schema, guias, XML, transmissão, consulta, PDF, filas ou snapshots; sem Portal e sem iniciar task seguinte. |
| 26.1 | [Política de estados, abas e ações da guia](tasks/26.1-politica-estados-abas-acoes.md) | Implementada em 02/11/2026, aguardando validação: política pura e reutilizável deriva estado de UX, abas, ação principal, bloqueios e contratos conceituais de rota de fatos já persistidos/autorizados. Não altera layout, URLs, banco, XML, transmissão, consulta, fila, PDF ou guias; `F` isoladamente nunca aprova. |
| 26.2 | [Shell visual, empresa, sidebar e breadcrumb](tasks/26.2-shell-sidebar-navegacao.md) | Corrigida visualmente em 06/11/2026, aguardando validação visual: reutiliza a logo local oficial do dashboard em faixa azul, com corpo branco, empresa em card claro, ícones SVG e menu ativo destacado; Gerar guia por notas usa documento fiscal e Configuração usa engrenagem, ambos identificados na hidratação para não afetar Guias, Nova guia ou Fila de transmissão. A barra fixa compacta mostra somente o título atual e preserva à direita o seletor existente. Uma única variável de altura governa barra e reserva do conteúdo em desktop/mobile; os contadores ganham hierarquia visual sem mudar seus filtros. Sem rota nova, migration, mudança fiscal, alteração de guia, XML, PDF, transmissão, consulta, fila ou Portal; Task 26.3 não iniciada. |
| 26.3 | [Formulário guiado e validações orientadas](tasks/26.3-formulario-guiado-validacoes.md) | Concluída e corrigida em 02/11/2026: o formulário progressivo organiza apenas os campos permitidos pelo snapshot GNRE007 em blocos guiados, identifica estados por texto, bloqueia lista oficial obrigatória vazia e preserva POST/valores incompatíveis para revisão explícita. A grade comum agora se adapta a três, duas ou uma coluna e contém selects/textos longos sem sobreposição horizontal. Flash e POST pendente são isolados por empresa, rota, guia e item; cada flash registra sua ação e Nova guia aceita apenas ações de criação/navegação, descartando com segurança o legado de edição. Falhas de persistência não expõem detalhe técnico ao usuário. Sem banco, snapshot, XML, transmissão, fila, PDF, Portal, alteração de guias ou remodelagem dos tipos 1 e 2. |
| 26.4 | [Experiência por tipo oficial de GNRE](tasks/26.4-experiencia-por-tipo-oficial.md) | Concluída em 02/11/2026: tipo 0 abre em formulário apenas sem item e, após persistência, em resumo com edição explícita; tipos 1 e 2 abrem em lista com inclusão e edição explícitas. POST pendente permanece isolado por item e XML/transmissão continuam após os itens, sem mudança de regras GNRE007, banco, XML, Portal ou guias. A estrutura de abas permanece para a Task 26.5. |
| 27 | [Visão geral e listagem operacional de GNRE](tasks/27-visao-geral-listagem-operacional.md) | Concluída documentalmente em 02/11/2026: define separação painel/listagem, indicadores, fontes e limites de GNRE001–GNRE008, filtros/colunas, mapeamento técnico-negócio, ações por estado, decisões futuras e testes manuais. Sem implementação funcional ou alteração de Task 26.5. |
| 28 | [Central de Guias: filtros e ações contextuais](tasks/28-central-guias-filtros-acoes.md) | Concluída em 02/11/2026, aguardando validação visual: `?view=list` é uma central somente leitura por empresa GNRE, com filtros GET, chips, classificação baseada em resultado individual persistido e ações já existentes. `F` isolado não emite; número de controle continua indisponível por não haver campo normalizado. Sem Portal, banco, XML, transmissão, PDF novo, alteração de guia, Task 26.5 ou início da Task 29. |
| 29 | Correção do catálogo de UF favorecida | Concluída em 02/11/2026: Nova guia usa um catálogo local, alfabético e independente de `GNRE007` com as 25 UFs atendidas pelo Portal Nacional. Após **Continuar**, um único snapshot compatível da empresa/ambiente/UF é selecionado e informado automaticamente, inclusive em uma seleção já aberta; a tela passa direto a “PR está pronta para emissão” e ao tipo GNRE, com dados técnicos e atualização do Portal recolhidos. Sem snapshot, a tela apresenta a consulta oficial como próximo passo normal, em uma área única sem alertas de erro; a chamada só acontece no clique e não cria a guia. Múltiplas versões continuam exigindo escolha. Sem banco, snapshots, guias existentes, XML, transmissão ou regras fiscais. `task29-favored-state-catalog-synthetic.php`, lint PHP/JS e `git diff --check` passaram; a suíte histórica ainda tem falhas estáticas não relacionadas nas Tasks 07, 08, 10, 11, 15, 22 e 26.3. |

| 30 | [Acesso GNRE na tela inicial por perfil](tasks/30-acesso-tela-inicial-por-perfil.md) | Concluída em 02/11/2026: card GNRE aparece somente com `C029_Id=310`, abre a aplicação isolada e sugere a empresa ativa do ERP. A sugestão é revalidada em `C019`/`C030` para o usuário da sessão; URL direta e empresa não autorizada continuam bloqueadas com 403. Sem alteração de banco, fluxo fiscal, XML, transmissão, snapshots ou permissões dos demais cards. Lint, `task30-home-access-synthetic.php`, regressão Task 29 e `git diff --check` passaram; a regressão estática preexistente da Task 10 continua falhando por procurar elementos removidos/realocados da apresentação. |
| 31 | [Consolidação de nomenclatura e compatibilidade interna](tasks/31-consolidacao-nomenclatura-compatibilidade.md) | Concluída em 03/11/2026: fluxo ativo usa contratos `GuideDraft`/`guide_draft_*` e arquivos `guide-*`. O adaptador `LegacyTask18Compatibility` é transitório e converte apenas POSTs/sessão cacheados `task18_*`; Task 15 segue congelada como legado. Lint PHP/JS, Task 31, Tasks 18/22, escopo de flash 26.3 e Tasks 26.5 passaram no `hardness-php7`; a asserção funcional preexistente de lista oficial vazia da Task 26.3 permanece falhando e não foi mascarada. `git diff --check` passou. Sem banco, Portal, snapshots, XML ou regra fiscal. |
| 32 | [Persistência da referência por item](tasks/32-referencia-item-persistencia.md) | Concluída em 03/11/2026: `reference_month`/`reference_year`, definidos pelo snapshot GNRE007, persistem em `GNRE003_Referencia_Mes`/`GNRE003_Referencia_Ano` no INSERT e UPDATE, reabrem no formulário e alimentam `<referencia><mes>`/`<ano>` no XML. A auditoria dos campos visíveis do `FormDefinitionBuilder` encontrou somente essa lacuna de mapeamento persistível. Sem migration, regra por UF/receita, Portal, transmissão, consulta, PDF, snapshots ou guias reais. |
| 36 | [Receita projetada por item na Central de Guias](tasks/36-receita-central-por-item.md) | Concluída em 03/11/2026: a coluna usa `GNRE001_Codigo_Receita` quando preenchido e, quando vazio, projeta o primeiro `GNRE003_Codigo_Receita` por `GNRE003_Id ASC`, sinalizando receitas distintas adicionais. Os filtros de receita e vencimento localizam fatos no cabeçalho ou em qualquer item com `EXISTS`, sem duplicar guias e mantendo o escopo da empresa GNRE ativa e os demais filtros; datas zeradas são ignoradas. Teste sintético cobre projeção, filtros, isolamento e ausência de persistência/migration. Não houve alteração persistente, schema, snapshot, XML, Portal, transmissão, consulta, fila, PDF ou guia real. |
| 37 | [Análise de configuração de ambiente: homologação e produção](tasks/37-analise-configuracao-ambiente-producao.md) | Concluída analiticamente em 03/11/2026: propõe configuração por empresa, homologação padrão, produção habilitada/auditável e ambiente imutável derivado do snapshot `GNRE007`; mapeia clientes, XML/SOAP, criação, Central, interface, fila, PDF, endpoints, TLS e certificado. Nenhuma implementação, migration, banco, Portal, XML persistido, transmissão, consulta, PDF, snapshot ou guia real foi alterado. |
| 38 | [Configuração mínima de ambiente por empresa](tasks/38-configuracao-minima-ambiente.md) | Implementada em 03/11/2026: `GNRE009` persiste somente a preferência `1`/`2` por empresa, com fallback seguro para homologação e tela protegida por permissão GNRE, empresa ativa e CSRF. A correção visual da rota `?view=config` a mantém no shell GNRE padrão, com sidebar, cabeçalho, área central e retorno para Guias. Produção permanece apenas preparatória: nenhum cliente, snapshot, XML, guia, transmissão, consulta, fila ou PDF consome a preferência; toda operação continua em homologação. Próxima task: centralizar ambiente e endpoints sem chamar produção. |
| 39 | [Centralização de ambiente e endpoints](tasks/39-centralizacao-ambiente-endpoints.md) | Implementada em 03/11/2026: `GnreEnvironmentResolver` concentra allowlist local de endpoints/contratos GNRE para `1` Produção e `2` Homologação; Configuração UF, recepção e resultado/PDF obtêm o endpoint por ele. A validação do ambiente não aceita valores externos e a operação usa somente a allowlist; nenhuma chamada ao Portal foi feita. |
| 40 | [Ambiente imutável em novos snapshots e guias](tasks/40-ambiente-imutavel-snapshot-guia.md) | Implementada sem Portal: `GNRE009` é lida apenas ao iniciar uma nova guia e seleciona candidatos `GNRE007` da mesma empresa, UF e ambiente. A guia criada permanece vinculada ao snapshot; transmissão, fila de resultado e PDF usam esse ambiente imutável, não a preferência atual, sem fallback. |
| 42 | [Ambiente operacional por empresa](tasks/42-ambiente-operacional-por-empresa.md) | Implementada e corrigida em 03/11/2026 sem Portal: a seleção em `GNRE009` define o ambiente somente das novas guias e direciona a busca/consulta para o `GNRE007` da mesma empresa, UF e ambiente. `gnre_rules`, persistência e cliente de configuração exigem ambiente explícito. `start_new_guide` e consulta administrativa usam `GNRE009`; recarga de guia usa o snapshot vinculado; vínculos, criação e ações Task 15 legadas sem contexto inequívoco foram bloqueados. Transmissão, consulta manual, fila e PDF continuam usando exclusivamente o snapshot, sem fallback. Nenhum Portal, guia real, snapshot real, transmissão, consulta ou PDF real foi utilizado. |
| 48 | [Orientações de ambiente, Portal e certificado](tasks/48-orientacoes-ambiente-portal-certificado.md) | Implementada em 04/11/2026: a Configuração ganhou orientação visual e links externos para os Portais de Produção e Homologação, sem integração ou armazenamento de certificado. `GNRE009` continua influenciando apenas novas guias; o ambiente de guias existentes permanece imutável no snapshot `GNRE007`. |
| Revisão técnica | [Consolidação do módulo](tasks/30-revisao-tecnica-consolidacao.md) | Concluída em 03/11/2026: análise sem implementação identificou nomenclatura temporária, a compatibilidade ativa da antiga Task 15, sobreposição de estado/apresentação e acoplamentos manuais de PHP, URLs, JavaScript e testes. Define sequência de refatoração em risco crescente, sem alterar banco, snapshots, XML, transmissão, consulta, PDF, Portal ou interface. |

## Planejamento incremental

1. **Task 01:** criar a fundação executável, acesso pela tela inicial e
   autorização. Não cria tabela GNRE, pois ainda não há dado GNRE a persistir.
2. **Task 02:** criar somente `GNRE001` (guia) e `GNRE002` (documento de
   origem), pois o cadastro manual passa a necessitar delas. Entregar CRUD de
   rascunhos individual/apuração, sem transmissão.
3. **Task 03:** implementar `GnreConfigUF` em homologação e usar sua resposta
   em tempo de requisição. Não haverá tabela de configuração ou cache até haver
   requisito concreto de persistência.
4. **Task 04:** gerar XML de lote GNRE v2.00 e validá-lo contra os XSDs locais.
   Cria `GNRE003` como itens genéricos de recolhimento, porque esses são os
   dados variáveis indispensáveis ao XML; não armazena XML gerado.
5. **Task 05:** criar `GNRE004` somente porque haverá tentativa real a auditar;
   ela preservará XML enviado, recibo, resposta e falhas. Entregar a recepção
   de lote, sem reenvio automático após timeout.
6. **Task 06:** criar `GNRE005` somente porque haverá recibos pendentes a
   processar. Ela é a persistência técnica de consultas/tentativas, com
   bloqueio e próxima tentativa; seu worker é somente administrativo/manual no
   MVP. Não representa configuração futura.
7. **Task 07:** consulta de resultado acionada pelo usuário na interface GNRE,
   com atualização de estados e histórico técnico visível. PDF, dados de
   emissão e pagamento continuam fora deste escopo.

### Correção Task 07 — feedback da consulta manual (03/11/2026)

Implementado PRG com flash de uso único isolado por empresa e guia. Após a
consulta, a aba Processar relê os estados persistidos de GNRE001/GNRE004/GNRE005,
exibe próxima tentativa e resultado individual já persistido, sem qualquer
consulta durante a renderização. A interface mantém uma única ação coerente e
trata pendente, final, retry, processamento, reagendamento, sem recibo,
bloqueio, inexistência e exceção pública segura. Não houve alteração de banco,
migration, Portal, guias reais, snapshots ou retransmissão.
8. **Task 08:** interpretar o retorno individual persistido e exibi-lo sem
   consulta externa. Não cria persistência nem altera a auditoria de recepção.

## Handoff obrigatório

| 43 | [Validação de pagamento e vencimentos por item](tasks/43-validacao-pagamento-vencimentos-itens.md) | Implementada em 03/11/2026: a política genérica compara o pagamento único da guia ao menor vencimento dos itens, sem regra por UF, receita ou tipo. Múltiplos vencimentos por item continuam permitidos; só o pagamento global posterior ao menor é bloqueado. A verificação ocorre na transação do item e novamente antes de XML/transmissão, sem migration, banco, guia real ou Portal alterado. |

Correção urgente Tasks 42/43 (03/11/2026): a criação de rascunho inicializa e valida o ambiente transitório antes de consultar o snapshot e antes do `INSERT` da `GNRE001`; ambiente ausente, UF/snapshot divergente ou ambiente divergente bloqueiam sem guia parcial. A mensagem pública de falha de criação foi separada da mensagem de item. Não houve Portal, migration, banco ou guia real alterados.

Correção Task 43 (03/11/2026): o diálogo de vencimento só abre para a mensagem exata da política de domínio; demais erros permanecem no modal sem instrução indevida. Ajustes visuais temporários por regex/`ob_start` foram removidos e separados da regra fiscal.

### Task 33 — correção de apresentação/feedback

Correção da aba Processar: feedback de XML e transmissão voltou a ser renderizado
sem expor SOAP; motivos persistidos de rejeição individual voltaram a aparecer;
a confirmação de transmissão foi preservada e a consulta manual passou a
selecionar a fila GNRE005 mais recente. A validação genérica de referência
obrigatória agora respeita o flag já registrado no snapshot oficial da UF.
Sem migration, banco, schema, snapshot ou XML persistido alterado pelo código.
Não houve chamada automática pelo código; a homologação da guia #26 foi teste
manual realizado pelo usuário.

Cada tarefa deve registrar: arquivos alterados; decisões e razões; migrations
e forma de aplicá-las; testes e resultados; roteiro de teste manual; pendências
e condição objetiva para liberar a próxima tarefa. Não avance para a próxima
tarefa sem validação do usuário.

# Task 34 — transmissão guiada no modal

O ciclo assistido depende da página/modal aberto; não existe cron, worker contínuo ou execução após o navegador fechar. A autorização transitória fica somente no navegador e é removida no fechamento, PDF ou estado terminal. A leitura de estado é local e as chamadas ao Portal permanecem exclusivamente nos POSTs autorizados. Depois de confirmação explícita em **Transmitir GNRE**, um retorno pendente é reagendado para cinco segundos; o modal consulta somente quando `GNRE005_Proxima_Tentativa` permitir e nunca retransmite automaticamente.

Correção Task 34: o fechamento também remove `gnre34=1` da URL; redirect sem ciclo ativo não reabre o modal.

Task 34 inclui prévia local e somente leitura antes da confirmação de transmissão; ela usa XML temporário validado e não chama o Portal. A aba manual Processar expõe apenas a próxima ação pública aplicável e deixa estado técnico, fila e SOAP no Histórico.

## Task 35 — estabilidade visual da guia

O cabeçalho operacional e as abas Dados e itens, Processar e Histórico foram mantidos no mesmo fluxo visual abaixo da barra global fixa. Não há comportamento sticky independente nem espaço adicional por aba; somente o conteúdo ativo varia. Sem banco, Portal, transmissão, consulta, PDF, snapshot ou guia real alterados.

O alinhamento é garantido por margem vertical zero na área de trabalho do grid; antes, `margin:auto` centralizava cada guia conforme a altura variável da aba. A abertura do modal de item não é afetada.

# Task 35 — modal de item e emitente

O item de guia abre em modal progressivo, preservando links e formulário normal como fallback sem JavaScript. A persistência, regras e hidratação continuam no POST existente e no snapshot GNRE007; não houve alteração de banco, Portal, transmissão ou guias reais. O emitente mostra CNPJ, razão social, UF e cidade, sem expor código XML/D020. Roteiro manual sem transmitir: abrir novo item, conferir desktop/mobile, cancelar preenchimento parcial, salvar e reabrir item, provocar erro de validação e conferir a manutenção dos valores e a apresentação simplificada do emitente.
# Task 61 — preenchimento assistido por NF

Implementado e aguardando validação manual explícita: o lote por NF + tributo prepara receita validada no snapshot, item GNRE003, valor GNRE006 e vencimento de São Paulo de forma atômica. ST usa 100099/11, DIFAL 100102/11 e FCP fora do RJ 100129/11. No RJ, DIFAL + FCP cria uma única 100102 com valores 11 + 12 e ambos os vínculos de NF. O assistente aceita somente Guia simples/tipo 0. Permanecem excluídos Portal, XML, transmissão, consulta, PDF, financeiro, SQL e alteração de guia real.

Correção posterior: vínculo RJ é existente somente quando DIFAL e FCP apontam para a mesma guia; vínculo parcial ou divergente fica bloqueado para revisão manual, sem escrita. Preenchimentos de pagamento, documento/chave, referência e emissão continuam fora deste escopo.

A criação RJ agora bloqueia a NF antes da releitura e, em duplicidade concorrente de vínculo, faz rollback e confirma os dois vínculos antes de retornar a guia existente.

# Task 63 — Fila de transmissão

- Incluída tela de seleção e acompanhamento de guias GNRE, sem alteração de schema. A Fila de transmissão é processamento sequencial individual, auditável e guiado pelas estruturas GNRE004/GNRE005/GNRE008; não representa lote GNRE real.

## Task 71 — processamento sequencial assistido na Fila de transmissão

A Fila passou a acompanhar automaticamente, no navegador aberto, cada guia
selecionada: validação, transmissão, espera da consulta persistida, consulta,
resultado e PDF idempotente quando autorizada. Enquanto uma guia aguarda, as
demais ainda não iniciadas seguem em ordem. Isto é processamento individual
sequencial assistido, não lote GNRE real; fechar/interromper apenas encerra o
ciclo visual e preserva todas as operações já persistidas.

Correção do resultado final: a classificação pública da Fila lê exclusivamente
o resultado individual persistido da tentativa/recibo mais recente. `situacaoGuia=0`
é autorizada; `1` e `2` são rejeitadas; ausência, invalidez, multiplicidade ou
código desconhecido em um resultado final é inconclusivo. O PDF só aparece
como obtido quando a própria etapa idempotente da Fila retorna sucesso.
- Task 72: ao encerrar, o modal substitui a cronologia intermediária por cards
  consolidados por situação final (incluindo pendentes e não iniciadas após
  interrupção). A impressão só é oferecida para PDFs oficiais cujo identificador
  foi retornado pela etapa da própria Fila; usa a rota persistida existente com
  `print=1`, sem nova consulta ou obtenção no Portal.
- Complemento Task 72: os PDFs persistidos no ciclo agora são reunidos em uma
  única impressão das guias autorizadas, sem ações individuais no resumo. A
  composição reutiliza o motor FPDI da concatenação de DANFEs da VEN003 e falha
  integralmente se algum documento não puder ser relido. Só no modal da Fila,
  em Homologação e Produção, há uma tentativa visual após 2 s; uma recusa por
  horário retorna ao `GNRE005_Proxima_Tentativa` persistido. O fluxo individual
  segue os 5 s atuais.
- Correção visual Task 72: o resumo não exibe “Abrir guia” nem impressão por
  linha; a ação disponível é somente a impressão consolidada quando os PDFs
  foram persistidos no ciclo. A página individual preserva suas ações próprias.
- Correção: a listagem de candidatos não depende mais da projeção geral nem da validade local do XML; rascunhos completos ou incompletos aparecem como disponíveis para validação posterior. A Task 64 permanece referência para futuro lote GNRE real.
- Falhas inesperadas da Fila retornam mensagem pública neutra; dados técnicos de transporte e infraestrutura não são expostos ao modal.
- O vencimento exibido na Fila prioriza o cabeçalho e, quando ausente, é projetado a partir dos itens em consulta única compatível com o MySQL legado.

## Task 75 — desfechos de transmissão visíveis e seguros

Corrigida localmente em 06/11/2026: modal individual e Fila compartilham a classificação pública da tentativa de recepção. Rejeição preserva código e descrição públicos; tentativa inconclusiva/criada sem recibo mostra mensagem segura, terminal e recarregável, sem SOAP ou diagnóstico de transporte e sem retornar silenciosamente à confirmação. A Fila mantém cada guia em um único grupo final e agora registra rejeição como rejeitada, não bloqueada. Exceção antes de recibo é persistida como inconclusiva quando possível; se a tentativa não puder ser finalizada, o marcador de sessão por empresa+guia atravessa PRG. Não houve chamada a Portal, consulta, PDF, SQL ou banco real.

# Task 64 — análise de transmissão GNRE em lote real

- Concluída documentalmente em 05/11/2026. Manual GNRE v2.17 e XSDs locais confirmam um `TLote_GNRE` com várias `TDadosGNRE`, um recibo compartilhado e `situacaoGuia` individual no retorno. O limite aplicável vem de `qtdMaximas/guiasPorLote` do snapshot da UF, não do `maxOccurs=200` do retorno. Como o contrato não traz id do ERP para correlacionar cada resultado, o lote real exigirá coorte persistida, correlação inequívoca e bloqueio conservador se houver ambiguidade. `pdfGuias` é documento de lote até prova de associação individual em Homologação. A Task 63 permanece processamento sequencial; recomenda-se renomeá-la para não sugerir lote real. Sem código operacional, schema, banco, Portal, transmissão, consulta, PDF ou guia alterados.
## Task 68 — preparação automática de coortes reais pela Fila de transmissão

Implementada localmente em 05/11/2026: a Fila de transmissão permite selecionar
guias e preparar coortes locais automaticamente, separando-as pelo contrato
imutável de empresa, UF, ambiente, snapshot e layout. A ação é POST com CSRF e
PRG, revalida e bloqueia cada guia na transação, informa bloqueios públicos por
guia e preserva a idempotência/isolamento da Task 66 e o cancelamento/bloqueio
individual da Task 67. Cada grupo compatível é atômico por savepoint: falha na
coorte faz rollback isolado, sem persistência parcial e sem impedir os demais
grupos válidos. Esta task prepara somente localmente: não gera XML
operacional, não envia ao Portal, não cria GNRE004/GNRE005, não consulta
resultado e não baixa PDF. Aguardando validação manual.

## Task 76 — identificador interno auditável no lote real

Preparação local concluída para o próximo teste controlado em Homologação: cada
novo participante de lote físico recebe `identificadorGuia` igual ao seu
`GNRE001_Id`, validado como decimal positivo de no máximo dez dígitos, antes do
congelamento em `GNRE011`. O retorno usa esse campo como correlação direta
somente se ele apontar para uma única participante do próprio lote; ausência
mantém a correlação conservadora, e valores inválidos, desconhecidos ou
colididos bloqueiam a decisão. O lote #7 e suas guias não foram alterados,
retransmitidos ou consultados por esta correção. A confirmação depende do novo
envio manual em Homologação para verificar se o Portal ecoa o campo.

Correção local subsequente: a chave de idempotência do envio físico passou a
conter também `GNRE010_Id`. Isto distingue duas tentativas físicas autorizadas
com participantes e XML congelado idênticos, sem enfraquecer a trava de
concorrência do mesmo lote. Os lotes #7, #8 e #9 não foram alterados,
retransmitidos ou consultados por essa correção.
