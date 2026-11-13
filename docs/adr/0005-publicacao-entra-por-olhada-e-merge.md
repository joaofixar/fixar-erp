---
status: accepted
---

# A publicação entra por olhada e por merge, não por cópia

O Hardness em `src/` saiu da cópia em `sistema-legado/`. A publicação, no clone em `/root/sistema_hardness/hardness3`, ficou sem `pull` desde então. O pull seguinte trouxe mais de 600 commits no `master`. Copiar essa pasta por cima, ou dar pull dentro de `src/hardness3`, apagaria o que a Fixar já tirou e o que já ajustou.

A olhada, acionada por quem desenvolve, busca `origin/master` e lista os commits depois do SHA gravado neste repositório. Não altera `src/`, a base nem o banco. A primeira execução procura o commit da publicação que corresponde à cópia. Se nenhum fecha com a árvore atual, mostra os mais próximos e espera o SHA. Não copia.

O trazer, também acionado, faz um merge de três vias: a publicação no SHA gravado, o `src/hardness3` atual, a publicação na ponta pedida. O resultado nasce numa branch e não troca o checkout. Conflito para, fica visível e não avança o SHA. Merge limpo avança o SHA até a ponta que entrou. A base só serve esse código quando a branch é integrada.

## Considered Options

- Copiar a pasta do clone, ou puxar o `master` deles, todo dia, por cima de `src/hardness3`. O vão de mais de 600 commits entraria de uma vez e levaria junto o que este repositório já excluiu.
- Atualizar a base e o banco no mesmo passo. Coluna, índice e configuração voltariam, e a base em execução mudaria antes de alguém ver o código.

## Consequences

- Personalização, segredo e arquivo de outro cliente continuam de fora. O que só a Fixar alterou permanece, salvo conflito.
- Coluna, índice e configuração não entram. O ADR 0004 permanece. SQL de rotina pode vir na branch e só passa a valer na base depois da integração, na subida do contêiner.
- Nada dispara sozinho. A olhada é o comando que se pode rodar todo dia. O primeiro relatório é o vão desde a cópia do legado, não um fluxo curto.
- O clone fica em `master`. A comparação lê `origin/master` depois do `fetch`.
