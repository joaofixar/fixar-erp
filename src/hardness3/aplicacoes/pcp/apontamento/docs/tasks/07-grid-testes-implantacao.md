# Tarefa 07 — Gestão, testes e implantação

> Nota Tarefa 09: ocorrências de “grupo” neste documento descrevem o
> cabeçalho histórico `PCP001`; a operação coletiva foi substituída pelo
> apontamento compartilhado por matrícula.

**Situação:** Concluída em 15/10/2026.

## Escopo

- Criar uma tela/grid próprio de gerenciamento, alimentado por
  `PCP001`, `PCP002` e `PCP003`: OP, processo, colaborador, início, fim,
  pausas, tempo líquido, retrabalho e status.
- Não alterar o grid legado baseado em `SMT009`; ele não representa este
  módulo.
- Usar o item gerencial já existente no menu lateral, visível somente com C029
  308, para abrir a consulta própria. A permissão 120 é exigida apenas nas
  configurações do módulo.
- Homologar fluxo individual, grupo, pausa, retrabalho, concorrência, cliente
  IDE e cliente sem IDE.
- Documentar implantação, rollback e limitações conhecidas.

## Critérios de aceite

- Os apontamentos são consultáveis na tela própria, sem `SMT009`.
- Nenhum fluxo do MVP altera OP, engenharia ou `T257`.
- Checklist de homologação e implantação preenchido.

## Implementação e validação

- `bootstrap/consultation.php` concentra a consulta, a validação dos filtros e
  o cálculo reutilizável do tempo líquido. O relógio de participante desconta
  cada intervalo `P → R`; o relógio do grupo só avança enquanto houver ao
  menos um participante ativo.
- `public/index.php?pagina=gerenciar` oferece período de início, OP,
  matrícula, processo, colaborador, status e retrabalho. Todos são opcionais
  e podem ser combinados; o limite de 100 itens,
  estado vazio e página de detalhe revalidada por empresa.
- A consulta sempre inicia em `PCP001 → T255` com `T255_C004_Id` da sessão.
  Não consulta nem altera `SMT009`, `T262`, `T262A`, `T257`, status ou
  quantidades da OP.
- O teste descartável `tests/task07-integration.php` cobre isolamento por
  empresa, filtros, estados aberto/pausado/finalizado, tempos, participantes
  e detalhe. Antes da implantação, executar no `hardness-php7`: lint PHP,
  `git diff --check` e testes 04, 05, 05A, 06 e 07.
