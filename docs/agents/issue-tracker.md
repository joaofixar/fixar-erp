# Rastreador de issues: markdown local

Issues e specs deste repositório vivem como markdown em `.scratch/`.

A prosa é em português. Ficam em inglês os tokens que os skills gravam e leem: `Status:`, `Type:`, `Blocked by:`, os rótulos de `triage-labels.md`, os títulos `## Comments` e `## Answer`, e o título da seção `## Wayfinding operations` abaixo.

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

## Wayfinding operations

Usado pelo `/wayfinder`. O **mapa** é um arquivo com um arquivo **filho** por ticket.

- **Mapa**: `.scratch/<effort>/map.md` (o corpo de Notes / Decisions-so-far / Fog).
- **Ticket filho**: `.scratch/<effort>/issues/NN-<slug>.md`, numerado a partir de `01`, com a pergunta no corpo. Uma linha `Type:` registra o tipo (`research` / `prototype` / `grilling` / `task`); uma linha `Status:` registra `claimed` / `resolved`.
- **Bloqueio**: uma linha `Blocked by: NN, NN` perto do topo. O ticket desbloqueia quando cada arquivo listado está `resolved`.
- **Fronteira**: varrer `.scratch/<effort>/issues/` pelos arquivos abertos, desbloqueados e sem claim; o menor número ganha.
- **Claim**: gravar `Status: claimed` e salvar antes de qualquer trabalho.
- **Resolve**: acrescentar a resposta sob o título `## Answer`, gravar `Status: resolved`, e então acrescentar um ponteiro de contexto (resumo + link) em Decisions-so-far no `map.md`.
