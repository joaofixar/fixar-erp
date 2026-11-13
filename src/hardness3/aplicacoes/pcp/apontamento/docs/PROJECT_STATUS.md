# PCP — Apontamento de Produção

> **Fonte de verdade do módulo.** Leia este arquivo e a tarefa correspondente
> antes de trabalhar. Ao concluir uma tarefa, registre arquivos, decisões,
> validações e pendências no seu handoff.

## Objetivo do MVP

Oferecer uma interface própria, em uma estação de produção já autenticada no
Hardness, para registrar o tempo de colaboradores por OP e processo. A
matrícula apenas identifica o colaborador (`D109`); não é login nem altera a
sessão `C007` da estação.

A entrada pode ser feita pelo atalho **Apontamento PCP** na tela inicial do
ERP, em nova janela, pela rota pública `/apontamento/`.
Ele é exibido para perfis com C029 203 e 308. A rota curta usa rewrite e o
módulo possui login próprio de terminal nesta etapa.

## Decisões de modelo

- O módulo não cria, altera ou consulta `T262`, `T262A` ou `SMT009`.
- A OP vem de `T255` e o processo vem de `SMT006`; portanto uma OP pode ser
  apontada mesmo sem roteiro/processo de engenharia.
- `PCP001` registra obrigatoriamente `T255` e `SMT006`.
- `PCP002` registra a participação de um `D109`; `PCP003` registra os eventos
  `I`, `P`, `R` e `F`.
- `PCP006` define quais colaboradores (`D109`) participam do apontamento;
  `PCP004` define a habilitação muitos-para-muitos entre `PCP006` e processo.
  `PCP005` contém motivos de retrabalho. Pausas usam `SMT012`.
- A OP não é encerrada, não recebe quantidade/refugo e `T257` não é alterada.
- Um colaborador só possui uma participação ativa ou pausada no MVP. Para novos
  inícios existe no máximo um cabeçalho aberto por empresa/OP/processo/
  retrabalho/motivo; ele pode ter vários participantes com horários próprios.

## Estruturas necessárias

O bloco do projeto em `scripts/atualizacaoBase.php` cria somente:

- `SMT006`: catálogo global de processos;
- `SMT012`: motivos de parada;
- `PCP001` a `PCP006`: dados próprios do módulo.

`SMT009` e `T262A` não são requisitos do módulo e não devem ser adicionadas
pelo projeto à atualização-base. Clientes IDE podem mantê-las sem impacto.

**Referência de desenvolvimento:** estruturas e dados devem ser levantados em
`e032`, pelo container `hardness-php7`. Use somente leitura durante análises.

## Regras de operação

1. A matrícula localiza um `PCP006` ativo, vinculado a um `D109` ativo,
   sem filtro de empresa.
2. A sessão e a OP são validadas pela empresa ativa.
3. Sem participação aberta, a interface apresenta OPs ativas e os processos
   ativos em `PCP004` para o colaborador.
4. Pausa, retorno e finalização são individuais; não há operação de grupo.
5. Retrabalho exige um motivo ativo de `PCP005`.
6. O grid legado de apontamentos baseado em `SMT009` não exibirá o módulo.
   A gestão deve ter tela/grid próprio, alimentado por `PCP001`–`PCP003`.
7. Retrabalho permanece suportado pelo modelo e backend, mas sua opção está
   oculta na interface do piloto (`show_rework=false`) até existir cadastro e
   manutenção de motivos em `PCP005`.

## Tarefas e situação

| Ordem | Tarefa | Situação |
| --- | --- | --- |
| 00 | [Fundação documental](tasks/00-fundacao-documental.md) | Concluída |
| 01 | [Inventário e migração IDE/base](tasks/01-migracao-ide-base.md) | Concluída, revisada em 14/10/2026 |
| 02 | [Modelo novo PCP e integridade](tasks/02-modelo-pcp.md) | Concluída, revisada em 14/10/2026 |
| 03 | [Fundação da aplicação](tasks/03-fundacao-aplicacao.md) | Concluída |
| 04 | [Fluxo individual](tasks/04-fluxo-individual.md) | Concluída e revisada em 15/10/2026 |
| 05 | [Fluxo de grupo e pausas](tasks/05-fluxo-grupo.md) | Substituída pela Tarefa 09; código legado removido |
| 05A | [Colaborador do Apontamento](tasks/05a-colaborador-pcp.md) | Concluída em 15/10/2026 |
| 06 | [Cadastros e operação](tasks/06-cadastros-operacao.md) | Concluída para o MVP; retrabalho oculto no piloto |
| 07 | [Gestão, testes e implantação](tasks/07-grid-testes-implantacao.md) | Concluída em 15/10/2026 |
| 08 | [Visão geral operacional](tasks/08-visao-geral-operacional.md) | Concluída em 15/10/2026 |
| 09 | [Apontamento compartilhado por matrícula](tasks/09-apontamento-compartilhado.md) | Concluída em 15/10/2026 |
| 10 | [Login de terminal e seleção de empresa](tasks/10-login-terminal.md) | Concluída em 16/10/2026 |
| 11 | [Flag de disponibilidade de processo](tasks/11-flag-processo-apontamento.md) | Concluída em 16/10/2026 |

## Handoff obrigatório

Cada tarefa deve registrar situação/data, arquivos alterados, decisões,
validações e pendências. Atualize esta tabela ao encerrá-la. Divergência entre
código e documentação deve ser registrada e resolvida antes da próxima tarefa.

## Registro de revisão arquitetural — 14/10/2026

A versão inicial conectava o módulo à engenharia por `T262/T262A` e gerava um
`SMT009` por participação. Essa opção foi descartada: uma OP sem roteiro não
poderia ser apontada, e criar operações apenas para registrar tempo afetaria
indevidamente a engenharia, máquina, operador e custos. O módulo passa a ser
uma camada própria, ligada diretamente à OP e ao catálogo de processos.

O `e032` já possuía as tabelas IDE originais; elas não serão removidas. As
colunas experimentais de vínculo legado em `PCP001` e `PCP002` serão retiradas
somente do schema do módulo, preservando os demais registros PCP existentes.

## Handoff Tarefa 05 — 15/10/2026

Foram adicionados o fluxo de apontamento múltiplo, a lista de grupos abertos e
o teste de integração descartável. A criação e o encerramento de grupo são
transacionais e usam o `C007` da estação somente para auditoria. O individual
permanece responsável por pausa, retorno e finalização por matrícula. A
validação operacional foi concluída no container `hardness-php7`: lint PHP,
`git diff --check` e os testes das tarefas 04 e 05 passaram.

## Handoff Tarefa 05A — 15/10/2026

O `PCP006` é o cadastro explícito do colaborador no Apontamento PCP: guarda
o vínculo único com `D109`, sua matrícula própria e o status. `D109`
continua sendo a fonte do nome e do status funcional; `PCP002_D109_Id`
preserva o histórico. `PCP004` referencia somente
`PCP004_PCP006_Id`. A atualização-base contém apenas o modelo novo; a
conversão experimental do ambiente `e032` foi feita diretamente nele.
O fluxo individual exige PCP006 ativo para localizar e iniciar. O fluxo de
grupo foi substituído posteriormente pelo apontamento compartilhado por
matrícula da Tarefa 09.

## Handoff Tarefa 06 — colaboradores e processos, 15/10/2026

O módulo ganhou área inicial com sidebar e permissões separadas de operação e
gestão, mantendo PCP/Estoque 203 como pré-requisito. A gestão de colaboradores
lista apenas PCP006, usa matrícula própria e consulta D109 apenas para nome e
situação. Inclusão, ativação/inativação de PCP006 e vínculos PCP004 são
protegidos por CSRF e validação backend; funcionário D109 inativo não pode ser
ativado operacionalmente. O grid continua explicitamente reservado à Tarefa 07.
Os colaboradores são exibidos em tabela e as informações/ações ficam no modal
de alteração de cada registro, incluindo a mudança validada da matrícula PCP e
os vínculos de processo organizados por linha.

Atualização: a seção Processos foi iniciada com listagem e manutenção do
catálogo global SMT006 (ID, descrição, valor, pintura e etapa), preservando a
independência do módulo e sem criar vínculo por empresa.

Atualização: Motivos de pausa agora possui catálogo próprio de `SMT012`, com
filtro por descrição/ID, inclusão e alteração em modais e campos descrição e
cor. A exigência de quantidade não é configurada nesta etapa e valores legados
permanecem preservados nas edições. O teste da Tarefa 06 cobre as gravações
desse cadastro; Motivos de retrabalho continua pendente.

## Handoff Tarefa 07 — gestão de apontamentos, 15/10/2026

A tela `Gerenciar apontamentos` agora é uma consulta operacional própria,
somente leitura e protegida pelo acesso operacional C029 308, além dos
pré-requisitos já existentes de PCP/Estoque. Ela permanece disponível para
quem pode operar; somente Configurações exige a permissão 120. Todas as
consultas, inclusive detalhe, revalidam `PCP001 → T255` pela
empresa ativa. O grid usa PCP001–PCP003, T255, SMT006, D109, PCP005 e SMT012;
não usa SMT009, T262/T262A ou T257 e não altera OPs. Todos os filtros são
opcionais e combináveis; o limite de 100 registros evita carregar o histórico
inteiro. O tempo
líquido está centralizado em `bootstrap/consultation.php`: desconta P→R por
participante e mantém o grupo parado quando todos os participantes estão em
pausa. `tests/task07-integration.php` cobre dados entre empresas, filtros,
estados, tempos e detalhe.

### Correção complementar — fluxo de grupo

O fluxo de inclusão, remoção e retorno após erro no apontamento em grupo foi
normalizado para `pagina=grupo`. Matrículas válidas já selecionadas permanecem
na tela quando uma nova matrícula é inválida, inativa, sem PCP006 ou duplicada;
o aviso é exibido na própria tela de grupo. O teste 05 passou a cobrir rota de
inclusão/remoção e rejeição de matrícula inválida e duplicada.

`Grupos abertos` agora exibe somente apontamentos com dois ou mais
participantes; apontamentos individuais permanecem no terminal da matrícula e
na consulta gerencial.

Os grupos abertos passaram a usar cards e permitem pausa/retomada coletiva:
pausar exige motivo e registra eventos do grupo e dos participantes ativos.
A retomada alcança somente as participações pausadas por essa ação coletiva,
preservando pausas individuais anteriores. O teste 05 cobre pausa e retomada.
O acompanhamento de grupos também aceita filtros opcionais de OP e matrícula.

## Handoff Tarefa 08 — visão geral operacional, 15/10/2026

`pagina=visao-geral` agora é a página inicial operacional: preserva o cabeçalho
de empresa/sessão existente, oferece o atalho de apontamento e mostra
atalho de gerenciamento para C029 308 e Configurações somente para C029 120.
`bootstrap/overview.php` concentra
as consultas somente leitura: empresa é sempre filtrada por `PCP001 → T255 →
T255_C004_Id`; os totais usam o último evento aplicável de cada participante e
os grupos iniciados hoje usam `CURDATE()` do servidor. A lista curta traz no
máximo 10 grupos abertos, seu tempo líquido pela função central da Task 07 e
linka para o detalhe de gerenciamento. Não foram alteradas tabelas, OPs,
quantidades, status, `SMT009`, `T262`, `T262A`, `T257` ou
`atualizacaoBase.php`.

Arquivos: `bootstrap/overview.php`, `public/index.php`,
`public/assets/css/app.css`, `tests/task08-integration.php` e
`docs/tasks/08-visao-geral-operacional.md`. A validação descartável da Task 08
cobre isolamento entre empresas, ativos/pausados, iniciados hoje, limite e
atalhos por permissão. Não há pendências funcionais desta tarefa.

Atualização visual: a lista passou a chamar os registros de apontamentos e
identifica cada um como `Individual` ou `Em grupo` conforme sua quantidade de
participantes. A contagem de pausados aceita tanto o último evento `P` no
participante ativo quanto o status `PCP002_Status='P'` persistido pelo fluxo
atual, para refletir corretamente os apontamentos já pausados.

Correção de acompanhamento: cartões inteiramente pausados não recebem mais o
timer do navegador, preservando o tempo líquido congelado. O destaque visual e
o motivo da pausa atual são obtidos por `PCP003 → SMT012_Cor`.

## Handoff Tarefa 09 — apontamento compartilhado por matrícula, 15/10/2026

`bootstrap/individual.php` reutiliza o `PCP001` aberto da combinação
empresa/OP/processo/retrabalho/motivo e cria uma participação/evento inicial
por matrícula. A transação bloqueia a OP (`T255`) antes da busca/criação do
cabeçalho, bloqueia a matrícula por `D109` e bloqueia o cabeçalho na
finalização; não foi criado índice de empresa nem tabela nova. Pausa, retorno e
finalização permanecem individuais; somente o último participante fecha o
cabeçalho.

Foram removidos o menu e os atalhos de apontamento em grupo. A URL antiga
`pagina=grupo` ou `modo=grupo` redireciona ao apontamento individual antes de
processar POST, portanto não executa mutação. Dados históricos, grid legado,
`SMT009`, `T262`, `T262A`, `T257`, OPs, quantidades e status não foram
alterados. Duplicidades históricas de cabeçalho não são corrigidas
automaticamente; novos inícios selecionam o mais antigo aberto compatível.

Arquivos principais: `bootstrap/individual.php`, `bootstrap/app.php`,
`bootstrap/overview.php`, `public/index.php`,
`tests/task09-integration.php` e a documentação das tarefas 04, 05, 07 e 08.
Validações: lint de todos os PHP do módulo, `git diff --check` e os testes
04, 06, 07, 08 e 09 no `hardness-php7`, todos aprovados. Sem pendências
funcionais conhecidas.

### Revisão pré-piloto — 16/10/2026

O código executável do antigo fluxo de grupo foi removido:
`bootstrap/group.php`, seus testes exclusivos e o bloco legado do controlador
não fazem mais parte do módulo. A rota legada somente redireciona para o
apontamento por matrícula, antes de processar qualquer `POST`.

Retrabalho permanece preservado no modelo e no backend. No piloto, a opção de
início fica oculta por `show_rework=false`, pois ainda não existem motivos em
`PCP005`. Para reativá-la futuramente, basta habilitar essa configuração após
cadastrar e disponibilizar os motivos.

## Handoff Tarefa 10 — 16/10/2026

O Apontamento passou a manter autenticação própria em
`$_SESSION['pcp_apontamento']`, usando `C007` e
`SegurancaSenha::validarSenhaArmazenada`, sem criar tabela ou credencial nova e
sem alterar a sessão global do ERP. A empresa é obrigatória, revalidada em
`C019/C030/C004` e exige C029 203 e 308; C029 120 continua exclusiva de
Configurações. O cabeçalho informa usuário/empresa do terminal e “Sair do
terminal” fecha apenas esse contexto. O card ERP continua na mesma URL e pode
criar o contexto local do seu usuário autorizado; após logout local, exige
novo login do terminal. Foram acrescentados os endpoints de login, seleção e
logout, o teste descartável `tests/task10-integration.php` e a documentação da
tarefa. Não foram alteradas tabelas de negócio ou atualização-base.

O contexto do terminal expira após 8 horas sem atividade. O controle é próprio
do módulo e não altera a expiração ou o logout da sessão global do ERP.
