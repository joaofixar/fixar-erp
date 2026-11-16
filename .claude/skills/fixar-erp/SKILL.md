---
name: fixar-erp
description: Mapa e regras do repositório fixar-erp (Hardness sem personalização, base local em PHP 8.3 + MariaDB, publicação do vendedor, tickets em .scratch). Ler antes de mexer em src/hardness3, banco/, bin/publicacao, testes/ ou nos tickets deste repositório.
---

# fixar-erp

ERP interno da Fixar Industrial. O ponto de partida é o **Hardness** (ERP em PHP comprado com o código), rodando **sem personalização**. Duas pessoas mantêm. A produção fica onde está: nada aqui lê ou escreve nela.

Antes de decidir algo, ler `GLOSSARY.md` e o ADR que toca a área (`docs/adr/0001` a `0005`). Usar os termos do glossário: Base, Publicação, Personalização, Empresa de validação, Aviso, Perfil. Se a mudança contradiz um ADR, dizer isso explicitamente.

## Mapa

| Caminho | O que é |
|---|---|
| `src/hardness3/` | O Hardness versionado (~14 mil arquivos). É o que o contêiner serve em `/hardness3/`. |
| `src/index.php`, `src/.htaccess` | Entrada `/login`. |
| `docker/php/` | Imagem PHP 8.3 + Apache. `compatibilidade.php` repõe o que o PHP 8 removeu (`each`, mysqli sem exceção). `saidas.php` lista as conexões que a base tentou abrir (vazio = isolada). |
| `docker-compose.yml` | `web` (porta 8080, rede `interna` sem saída) e `db` (MariaDB 11, `127.0.0.1:3306`, usuário/senha `fixar`). |
| `banco/fotografia.sql` | Estrutura da produção, sem linhas de negócio, mais empresa de validação, usuário local e permissões. Só entra com volume vazio. |
| `banco/aplicar-rotinas-ausentes.sh`, `banco/subir-com-rotinas.sh` | Cria as rotinas da lista `src/hardness3/scripts/atualizacaoBase-rotinas-lista.php` que ainda não existem (ADR 0004). Roda no volume novo e na subida do volume existente. Não roda o resto do atualizador. |
| `bin/preparar-base.py` | Grava a configuração local de conexão e os diretórios de cache. |
| `bin/publicacao` | Olhada e merge de três vias com a linha master do vendedor (ADR 0005). |
| `publicacao/ponto` | SHA da publicação que corresponde ao `src/hardness3` atual. |
| `testes/*.sh` | Scripts de percurso em bash (HTTP + consulta ao banco). Único estilo de teste do repositório. |
| `.scratch/<feature>/` | Specs e tickets (`spec.md`, `issues/NN-slug.md`). |
| `sistema-legado/` | Referência local, **só leitura**, fora do git. |
| `referencia/` | Framework do legado não ofuscado, **só leitura**. Útil para ler o que em `src/` está ofuscado. |
| `.agents/skills/` | Skills do fluxo de trabalho (to-spec, to-tickets, implement, wayfinder, tdd...). |

## Comandos

```bash
./run.sh                                  # máquina nova: confere pré-requisitos, instala o vendor/ do Composer e chama o ./subir
powershell -ExecutionPolicy Bypass -File run.ps1   # o mesmo no Windows, sem Git Bash
./subir                                   # prepara, constrói a imagem (--load) e sobe com --wait
bash testes/tela-de-login.sh              # exemplo de percurso; BASE_URL padrão http://127.0.0.1:8080
bash bin/publicacao                       # mostra o passo a passo
bash bin/publicacao olhar                 # lista commits da publicação depois de publicacao/ponto
bash bin/publicacao trazer                # merge numa branch publicacao/<sha>, sem trocar o checkout
```

No Windows o engine é o **Kiri** (`DOCKER_HOST=npipe:////./pipe/kiri`). Os comandos são `docker` e `docker compose`. Nunca introduzir Docker Desktop. Endereço pelo Kiri: `http://web.fixar-erp.kiri.localhost/login`.

## Regras do repositório

- Commits: Conventional Commits com descrição **em português**, no imperativo e sem ponto final. Tipos: `feat fix docs style refactor perf test build ci chore revert`. Escopo opcional.
- Comentários, docs, specs e tickets em português. Ficam em inglês os tokens que os skills leem: `Status:`, `Orbit:`, `Type:`, `Blocked by:`, `## Comments`, `## Answer` e os rótulos de triagem.
- Nunca gravar, renomear ou formatar nada em `sistema-legado/` ou `referencia/`. Comportamento herdado se reimplementa fora e cita o arquivo de origem.
- Nada de segredo no git: `confUsuario.php`, `sysConfig.php`, `.env`, `.p12`, `.keystore`, `scripts/backup/`, cache e `dados_usuarios/`. O `.gitignore` lista um a um; `testes/tela-de-login.sh` confere.
- A base **não** emite nota, não registra boleto, não envia e-mail e não fala com o fornecedor (ADR 0001/0002). Não quebrar o isolamento da rede `interna`.
- Personalização (orçamento, pedido, boleto, histórico de produto, totalização, reprocesso de estoque) não entra em execução (ADR 0002).
- Ajuste só o necessário para o Hardness abrir no PHP 8.3. Isso não conta como personalização.
- O prefixo `/hardness3/` fica até uma melhoria decidir o endereço público (ADR 0003).

## Tickets

- O próximo ticket é o de **menor número com critério `- [ ]` em aberto** e com tudo em `Blocked by:` já marcado. A linha `Status:` dos concluídos costuma continuar `ready-for-agent`; o que vale são os checkboxes.
- Ao publicar ou concluir um ticket, espelhar no Orbit (projeto **ERP Fixar**, MCP `orbit-mcp` em `.cursor/mcp.json`). Uma linha `Orbit: ERPFX-NN` logo depois de `Status:` liga o arquivo à tarefa; com a linha, atualizar em vez de criar. Detalhes em `docs/agents/issue-tracker.md`.
- A prova de cada spec é um script de percurso em `testes/`, na empresa de validação, sem depender de cliente, produto, orçamento, pedido, nota nem título.

## Armadilhas conhecidas

- **Arquivos ofuscados**: muita coisa em `src/hardness3` vem ofuscada (`goto` com rótulos aleatórios). Para ler, usar `referencia/` ou `sistema-legado/`. Antes de editar, ver se a Fixar já tem uma versão legível no histórico (`git log -- <arquivo>`).
- **Publicação com `--sobrescrever`**: no conflito, a versão do vendedor vence. Isso apaga alterações da Fixar no mesmo arquivo e contraria o texto do ADR 0005 ("conflito para"). Antes de trazer, listar os arquivos que a Fixar alterou desde o import (`450612f`) e conferir cada um depois do merge.
- Estado em 16/11/2026: o merge `70e44da` (com `--sobrescrever`) desfez três correções da Fixar: `bibliotecas/funcoesGerais.php` voltou ofuscado (perdeu a versão legível, o fim do laço do login `dc9288f` e o repasse de parâmetro), `prog/cad/cad031/form.php` perdeu o aviso sem empresa (`eae5ae8`) e `prog/est/est015/grid.php` voltou a ter `CONVERT(... USING utf8)`. As versões boas estão em `e96ee26`. O usuário pediu para deixar isso de lado por enquanto; não corrigir sem pedido.
- `bin/publicacao` aponta por padrão para `//wsl.localhost/Ubuntu-20.04/root/sistema_hardness/hardness3`. Em outra máquina, passar `--publicacao DIR`.
- Rotina nova vinda da publicação só entra no banco na próxima subida do contêiner.
- O healthcheck do `db` espera `/tmp/rotinas-aplicadas`; a primeira subida pode levar minutos (`start_period: 900s`).
- Comandos `du`/`find` sobre `src/hardness3` são lentos no Windows. Preferir `git ls-files`.
