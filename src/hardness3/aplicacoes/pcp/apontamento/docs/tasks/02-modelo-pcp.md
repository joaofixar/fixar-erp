# Tarefa 02 — Modelo novo PCP e integridade

**Situação:** Concluída e revisada em 14/10/2026.

## Modelo final do MVP

| Tabela | Responsabilidade |
| --- | --- |
| `PCP001` | Cabeçalho do grupo: `T255` (OP), `SMT006` (processo), status, início/fim, retrabalho, motivo e `C007`. |
| `PCP002` | Participação individual: grupo, `D109`, status e início/fim. |
| `PCP003` | Eventos `I/P/R/F`, por grupo ou participante, com `SMT012` opcional na pausa. |
| `PCP006` | Cadastro único do colaborador do apontamento: vínculo com `D109`, matrícula própria e status. |
| `PCP004` | Habilitação única `PCP006 + SMT006`, com status. |
| `PCP005` | Motivos de retrabalho, com status. |

Não há vínculo com `T262`, `T262A` ou `SMT009`. A aplicação impede
transacionalmente duas participações ativas/pausadas do mesmo colaborador;
não há restrição de banco que bloqueie evoluções futuras.

## Migration

`PCP001` a `PCP006` são criadas por `CREATE TABLE IF NOT EXISTS` com todos os
campos e índices da primeira versão. Campos novos introduzidos em versões
futuras usam `Criar_Campo`/`Criar_Indice`. O módulo não altera OP, `T257` ou
dados produtivos.
