# Tarefa 04 — Fluxo individual

**Situação:** Concluída e revisada em 15/10/2026. Substituída/estendida pela
Tarefa 09: a matrícula continua individual, mas pode entrar em um cabeçalho
compartilhado compatível.

## Objetivo

Permitir iniciar, pausar, retomar e finalizar uma participação individual por
matrícula, OP e processo habilitado.

## Regras obrigatórias

- Matrícula busca `PCP006` ativo vinculado a `D109` ativo, sem exigir vínculo de empresa.
- OP é validada em `T255` para a empresa da sessão, sem exigir `T262`.
- Colaborador deve estar ativo em `PCP006`; processo vem de `PCP004` ativo
  ligado a esse `PCP006` e de `SMT006`.
- O início cria somente `PCP001`, `PCP002` e `PCP003/I`.
- Pausa, retorno e finalização registram `PCP003`; a pausa pode usar
  `SMT012`. O tempo líquido desconta intervalos `P → R`.
- Nenhuma ação cria, altera ou consulta `T262`, `T262A` ou `SMT009`.
- `T255`, `T257` e quantidades da OP não são alteradas.

## Critérios de aceite da revisão

- Uma OP ativa sem engenharia inicia e finaliza normalmente.
- Nenhuma escrita é realizada em `T262`, `T262A` ou `SMT009`.
- Duas tentativas simultâneas para o mesmo colaborador resultam em uma única
  participação.
- Os testes e a documentação não possuem dependência operacional dessas três
  tabelas legadas.
