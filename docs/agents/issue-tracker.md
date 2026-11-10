# Rastreador de issues: markdown local

Issues e specs deste repositório vivem como markdown em `.scratch/`.

A prosa é em português. Ficam em inglês os tokens que os skills gravam e leem: `Status:`, `Orbit:`, `Type:`, `Blocked by:`, os rótulos de `triage-labels.md`, os títulos `## Comments` e `## Answer`, e o título da seção `## Wayfinding operations` abaixo.

## Convenções

- Uma feature por diretório: `.scratch/<feature-slug>/`
- A spec é `.scratch/<feature-slug>/spec.md`
- Cada ticket de implementação é um arquivo em `.scratch/<feature-slug>/issues/<NN>-<slug>.md`, numerado a partir de `01`. Nunca um arquivo único juntando os tickets
- O estado de triagem é uma linha `Status:` perto do topo de cada issue (as strings dos papéis estão em `triage-labels.md`)
- Comentários e o histórico da conversa entram no final do arquivo, sob o título `## Comments`

## When a skill says "publish to the issue tracker"

Criar um arquivo novo sob `.scratch/<feature-slug>/` (e o diretório, se ainda não existir).

## When a skill says "fetch the relevant ticket"

Ler o arquivo no caminho indicado. A pessoa normalmente passa o caminho ou o número do issue direto.

O próximo ticket de implementação é o de menor número com algum critério ainda aberto, e com cada ticket em `Blocked by:` já com todos os critérios marcados.

## Espelho no Orbit

O markdown continua sendo o que o skill lê e grava. O quadro do Orbit só mostra o mesmo estado. O servidor MCP do projeto é o `orbit-mcp` de `.cursor/mcp.json`. O projeto é o **ERP Fixar**.

Ao publicar um ticket, criar a tarefa no mesmo passo em que o arquivo nasce. Ao concluir a implementação, marcar no arquivo cada critério que o trabalho cumpriu e atualizar a tarefa já ligada. Uma linha `Orbit:` logo depois da primeira linha `Status:` guarda o identificador (`ERPFX-12`). Tarefa com essa linha se atualiza. Sem a linha, uma tarefa nova duplicaria a que já existe.

O título da tarefa é o título do ticket. A descrição leva o que o ticket entrega, a linha `Blocked by:` e a lista de critérios. A spec da feature é a tarefa pai. Cada ticket dela nasce como subtarefa, com `parent_task_id` no identificador da spec.

O status do quadro sai do arquivo.

| Arquivo | Quadro |
| --- | --- |
| `needs-triage` | Backlog |
| `needs-info` | Em Análise |
| `ready-for-agent` | Pendente |
| `ready-for-human` | Em Revisão |
| `wontfix` | Cancelado |
| todos os critérios marcados | Concluído |

Os nomes da coluna da direita são os status do projeto. O id de cada um vem de `list_task_statuses`. Atualizar uma tarefa pede a `version` que `get_task` devolve, no campo `expected_version`. A descrição da tarefa concluída cita o hash do commit.

## Wayfinding operations

Usado pelo `/wayfinder`. O **mapa** é um arquivo com um arquivo **filho** por ticket.

- **Mapa**: `.scratch/<effort>/map.md` (o corpo de Notes / Decisions-so-far / Fog).
- **Ticket filho**: `.scratch/<effort>/issues/NN-<slug>.md`, numerado a partir de `01`, com a pergunta no corpo. Uma linha `Type:` registra o tipo (`research` / `prototype` / `grilling` / `task`); uma linha `Status:` registra `claimed` / `resolved`.
- **Bloqueio**: uma linha `Blocked by: NN, NN` perto do topo. O ticket desbloqueia quando cada arquivo listado está `resolved`.
- **Fronteira**: varrer `.scratch/<effort>/issues/` pelos arquivos abertos, desbloqueados e sem claim; o menor número ganha.
- **Claim**: gravar `Status: claimed` e salvar antes de qualquer trabalho.
- **Resolve**: acrescentar a resposta sob o título `## Answer`, gravar `Status: resolved`, e então acrescentar um ponteiro de contexto (resumo + link) em Decisions-so-far no `map.md`.
