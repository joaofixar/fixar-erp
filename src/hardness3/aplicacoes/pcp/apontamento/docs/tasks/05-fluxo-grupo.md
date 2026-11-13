# Tarefa 05 — Fluxo de grupo e pausas

**Situação:** Substituída pela Tarefa 09 em 15/10/2026. A interface e as
mutações coletivas foram desativadas; este documento registra apenas o
comportamento histórico da Tarefa 05.

## Escopo

- Selecionar vários colaboradores ativos em `PCP006`, OP e processo habilitado.
- Criar um `PCP001` e um `PCP002` por participante, com evento inicial
  `PCP003/I`.
- Validar todos antes da transação e rejeitar grupo parcial ou colaborador
  com participação ativa/pausada.
- Permitir pausa/retorno individual com `SMT012`, finalização individual e
  finalização do grupo para participantes ainda ativos.
- Manter eventos de grupo possíveis para evolução futura, sem integrar
  `SMT009`, `T262` ou `T262A`.

## Implementação e handoff — 15/10/2026

- A tela múltipla fica em `public/index.php?modo=grupo`, com retorno direto ao
  fluxo individual existente. Ela mantém as matrículas na própria requisição,
  permite remover participantes antes do início e lista os grupos abertos da
  empresa ativa.
- `bootstrap/group.php` reconsulta cada matrícula, bloqueia `D109` em ordem
  crescente de ID, revalida OP, processo `SMT006`, habilitação ativa em
  `PCP004`, motivo de retrabalho e participação aberta antes de criar dados.
  A criação inteira ocorre em uma transação.
- A finalização do grupo bloqueia o cabeçalho da empresa ativa e conclui apenas
  participantes `A`/`P`; participantes já `F` não são modificados. Pausa e
  retorno seguem exclusivamente no fluxo individual.
- O teste descartável `tests/task05-integration.php` cria e descarta seu schema
  no `finally`, cobrindo criação para três participantes, processo comum,
  rejeição atômica, finalização individual e finalização dos restantes.
- Não há consulta ou escrita nas tabelas legadas; o teste cria somente as
  estruturas permitidas, de forma que um acesso legado falharia.

**Validações:** em 15/10/2026, `git diff --check`, lint PHP e os testes
`task04-integration.php` e `task05-integration.php` executaram com sucesso no
container `hardness-php7`.

**Pendências:** nenhuma funcional conhecida.

## Critérios de aceite

- Vários grupos podem coexistir para a mesma OP/processo.
- O encerramento de uma pessoa não encerra os demais participantes.
- O módulo não altera OP, engenharia ou produção.
