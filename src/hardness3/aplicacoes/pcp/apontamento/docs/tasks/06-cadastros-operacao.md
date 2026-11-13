# Tarefa 06 — Cadastros e operação

**Situação:** Em andamento. Colaboradores e processos concluído no modelo
PCP006 em 15/10/2026.

## Objetivo

Disponibilizar as telas mínimas de manutenção para operar o apontamento.

## Escopo

- Habilitação colaborador × processo (`PCP004`), incluindo ativação/inativação.
- Motivos de retrabalho (`PCP005`).
- Manutenção/uso de processos (`SMT006`) e motivos de parada (`SMT012`) de
  acordo com as permissões definidas.
- Área de grupos/apontamentos ativos para operação livre do terminal.

## Critérios de aceite

- Um processo inativo/não habilitado não aparece no início do apontamento.
- Retrabalho não pode ser iniciado sem motivo ativo.
- Alterações de cadastro são auditáveis conforme padrão adotado pelo módulo.

## Handoff da implementação

Registrar permissões, telas, regras de inativação e testes realizados.

## Handoff da implementação — 15/10/2026

- A entrada passou a ter sidebar própria, sem dependência do dashboard de referência. `?modo=grupo` permanece mapeado para o fluxo de grupo.
- `Acessar Apontamento PCP` (C029 308) é mantida para liberar a operação e o futuro item de menu em Estoque. A configuração e gestão usam a permissão existente `P.C.P.` (C029 120), além do pré-requisito PCP/Estoque 203. Não existe uma permissão 309 do módulo.
- A seção Colaboradores e processos lista exclusivamente `PCP006`, com status
  do PCP e de `D109`, matrícula PCP e processos. A inclusão localiza apenas
  `D109` sem `PCP006`, exige matrícula única e não altera o cadastro mestre.
  Funcionário mestre inativo não pode ser incluído nem reativado no PCP.
- A listagem de colaboradores usa a mesma tabela dos catálogos; a manutenção
  de status e processos ocorre no modal aberto por **Alterar**, preservando a
  tela principal objetiva. A matrícula PCP pode ser alterada no modal, com
  validação de unicidade; os processos vinculados são exibidos em linhas com
  status e ação própria.
- Os vínculos são exclusivamente `PCP004_PCP006_Id × SMT006`; são ativados ou
  inativados sem exclusão. Retrabalho permanece em preparação nesta etapa.
- A seção Processos passou a listar o catálogo global `SMT006` com o ID da
  grade legada de referência, descrição, valor, pintura e etapa; permite
  incluir e alterar somente esses campos suportados, sem vínculo por empresa.
- A seção Motivos de pausa passou a listar o catálogo global `SMT012` com ID,
  descrição e cor. Inclusão e alteração ocorrem em modais próprios, com CSRF
  e validação backend; a exigência de quantidade ficou fora desta etapa e
  valores legados são preservados ao editar.
- `tests/task06-integration.php` usa schema descartável e cobre autorização,
  inclusão, duplicidade e alteração de matrícula, status de PCP006 e PCP004, e
  inclusão/alteração de SMT012.
  Pendência: os cadastros próprios e o grid continuam reservados às próximas etapas.
