# Tarefa 09 — Apontamento compartilhado por matrícula

**Situação:** Concluída em 15/10/2026.

O apontamento começa sempre por uma matrícula. O primeiro participante cria o
`PCP001`; entradas posteriores na mesma empresa/OP/processo/retrabalho/motivo
criam somente `PCP002` e `PCP003/I`, preservando horários independentes.

`T255` é bloqueada dentro da transação antes da busca/criação do cabeçalho.
Assim, sem criar um índice SQL de empresa em `PCP001`, a combinação é
serializada pela OP já vinculada à empresa. A matrícula é bloqueada por `D109`,
e a finalização bloqueia o cabeçalho antes de verificar se restam participantes.

A pausa, o retorno e a finalização são sempre individuais. O cabeçalho termina
somente após o último `PCP002` em `A` ou `P`. Registros históricos duplicados
não são removidos ou mesclados: a seleção usa o mais antigo aberto e a regra
vale para novos inícios.

A rota legada `pagina=grupo`/`modo=grupo` redireciona ao fluxo individual antes
de ler qualquer ação POST; não executa mutações. Não houve alteração no grid
legado, em `SMT009`, `T262`, `T262A`, `T257`, OPs ou quantidades.

**Validações:** `tests/task09-integration.php` é descartável e remove seu
schema no `finally`; cobre criação/entrada, horários, bloqueio por matrícula,
pausa individual, encerramento do último, concorrência, empresa, retrabalho e
incompatibilidade controlada da rota legada.
