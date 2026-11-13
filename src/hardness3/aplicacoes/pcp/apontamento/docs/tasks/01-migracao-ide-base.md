# Tarefa 01 — Inventário e migração IDE/base

**Situação:** Concluída e revisada em 14/10/2026.

## Resultado

- A referência foi levantada em leitura no banco `e032`, pelo container
  `hardness-php7`.
- O bloco do projeto fica diretamente em `scripts/atualizacaoBase.php`; não
  existe script auxiliar.
- Foram mantidas somente `SMT006` (processos) e `SMT012` (motivos de
  parada). A matrícula pertence ao cadastro próprio `PCP006`.
- `SMT009` e `T262A` foram retiradas do bloco do projeto. Elas pertencem à
  integração IDE/engenharia e não são usadas pelo MVP.
- Não foram incluídos campos Infori em `SMT006`, nem criado vínculo de empresa
  nas tabelas SMT.

## Continuidade

As tabelas IDE já existentes no `e032` não foram removidas ou alteradas. A
próxima evolução de tabela já distribuída deve usar a atualização-base, sem
script paralelo.
