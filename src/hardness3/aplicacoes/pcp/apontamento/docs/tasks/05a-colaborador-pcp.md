# Tarefa 05A — Colaborador do Apontamento

**Situação:** concluída e revisada em 15/10/2026.

## Objetivo

Separar quem participa do Apontamento PCP do cadastro geral de funcionários.

## Modelo entregue

```
D109 → PCP006 → PCP004 → SMT006
```

- `PCP006` é o cadastro do colaborador do módulo: tem um único `D109`,
  matrícula própria e status `A/I`.
- A matrícula é única em `PCP006`; o módulo não lê nem cria `D109A`.
- `PCP004` associa um colaborador PCP aos processos permitidos de
  `SMT006`.
- `PCP002_D109_Id` continua guardando o colaborador que participou, para que
  o histórico não mude se o cadastro PCP for inativado.

## Regras operacionais

- Individual e grupo localizam apenas `PCP006` ativo com `D109` ativo.
- Os processos disponíveis dependem de `PCP006 → PCP004 → SMT006` ativos.
- Um `D109` ativo sem `PCP006` não pode iniciar apontamento.
- Fechamento de participação já aberta continua possível, mesmo após
  inativação posterior do PCP006.

## Ambiente e atualização-base

O `e032` de desenvolvimento teve seus dois colaboradores existentes
convertidos diretamente para a matrícula no `PCP006`; a coluna e o índice
legados de `PCP004_D109_Id` foram removidos nele. Não houve exclusão de
histórico.

A atualização-base contém somente o modelo novo. Não há migração automática
nem dependência de `D109A`.

## Validação

`tests/task05a-integration.php` cria um schema temporário e cobre:

- funcionário ativo sem PCP006;
- PCP006 inativo;
- fluxo individual;
- grupo com processos comuns;
- limpeza do schema no `finally`.
