# Padrões do repositório

Prosa de pessoas em português. Identificadores que os skills casam ficam em inglês: caminhos (`GLOSSARY.md`, `docs/adr/`, `.scratch/`), rótulos de triagem, as linhas `Status:`, `Type:` e `Blocked by:`, e o título `## Wayfinding operations` em `docs/agents/issue-tracker.md`.

## Commits

[Conventional Commits](https://www.conventionalcommits.org/pt-br/): um assunto que diz o efeito, um corpo só quando o porquê não cabe no assunto.

```
<tipo>(<escopo>): <descrição no imperativo, em português>

<corpo opcional, em português: por que esta mudança existe>

<rodapé opcional>
```

O escopo é opcional. Sem ponto final no assunto. Tipos usados aqui: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `build`, `ci`, `chore`, `revert`. Mudança incompatível: `feat!:` ou o rodapé `BREAKING CHANGE:`.

```
feat(estoque): registra a entrada de mercadoria

O saldo precisa nascer de um movimento, senão a primeira versão não fecha inventário.
```

```
fix(nfe): recalcula o total quando o desconto muda
docs(agents): traduz o rastreador local para português
```

## Idioma

Comentários no código, docs, specs, tickets, glossário e ADRs em português.

O corpo do comentário ou do ticket é português. O que permanece em inglês é token de máquina ou nome já estabelecido no skill: `Status: ready-for-agent`, `Type: research`, os títulos `## Comments` e `## Answer` dentro do ticket, e as seções do mapa do `/wayfinder` (`Destination`, `Notes`, `Decisions so far`, `Not yet specified`, `Out of scope`).

## sistema-legado/

`sistema-legado/` é referência local, igual na máquina de cada dev, e fica de fora do git de propósito. Ler um arquivo de lá para entender o comportamento atual.

Gravar, renomear, apagar ou formatar qualquer arquivo sob `sistema-legado/` está fora do trabalho deste repositório. Comportamento que o sistema novo precise herdar se reimplementa fora dessa pasta; o ticket ou o comentário cita o arquivo de origem.

## Contêineres

O runtime de contêiner deste projeto é o [Kiri](https://docs.runkiri.com/how-it-works/). É um engine em Rust que fala a Docker Engine API: `docker`, `docker compose` e `buildx` seguem iguais. Imagens, Dockerfiles e Compose não mudam de formato.

Não introduzir Docker Desktop como engine do projeto. Os comandos do dia a dia continuam os do Docker, apontados para o Kiri.

Onde o engine mora:

- **Windows** (beta): dentro do WSL2. O CLI fala com `npipe:////./pipe/kiri`.
- **macOS**: um contexto `kiri` e o socket `~/.kiri/run/docker.sock`. Nomes locais no formato `web.kiri.localhost`.
- **Linux**: sem VM; o agent roda no kernel do host.

```
docker run -d --name web -p 8080:80 nginx
docker compose up
```
