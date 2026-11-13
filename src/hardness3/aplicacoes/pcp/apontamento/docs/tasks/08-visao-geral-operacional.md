# Tarefa 08 — Visão geral operacional

> Nota Tarefa 09: a visão geral continua listando cabeçalhos e participantes,
> mas não oferece mais atalho ou modalidade de apontamento em grupo.

**Situação:** Concluída em 15/10/2026.

## Entrega

- A rota padrão `pagina=visao-geral` passou a ser a entrada operacional do
  módulo, sem gráficos, histórico, custos ou produtividade.
- Exibe ações de apontamento individual e em grupo. `Gerenciar apontamentos`
  e `Configurações` dependem também da C029 120.
- Os cards exibem apontamentos abertos, participantes ativos, participantes pausados
  e grupos iniciados na data atual do servidor de banco.
- A lista tem no máximo 10 apontamentos `PCP001_Status='A'`, sempre isolados por
  `PCP001 → T255 → T255_C004_Id`, e direciona ao detalhe gerencial existente.
- Tempo líquido é obtido pela função central da Task 07. A tela não grava,
  pausa, retoma nem finaliza apontamentos e não consulta/escreve estruturas
  legadas de engenharia, quantidades ou OP.
- Quando todos os participantes estão pausados, o relógio do cartão permanece
  congelado; o destaque visual e o motivo usam `PCP003 → SMT012_Cor`.

## Validação

`tests/task08-integration.php` cria um banco descartável e cobre isolamento
entre empresas, evento aplicável para ativo/pausado (inclusive o status `P`
gravado pelo fluxo vigente), início no dia corrente, limite de 10, modalidade
individual/em grupo, tempo líquido e atalhos condicionados à permissão.
