Status: ready-for-agent
Orbit: ERPFX-25

# 03: Trazer numa branch sem trocar o checkout

**What to build:** Com o SHA gravado, o trazer faz um merge de três vias: a publicação nesse SHA, o Hardness versionado atual e a publicação na ponta. O resultado nasce numa branch nova e o checkout atual não muda. O que só a publicação mudou entra. O que só a Fixar mudou permanece. Personalização, segredo e arquivo de outro cliente ficam de fora. Onde os dois lados mudaram o mesmo arquivo, o comando lista esses arquivos, não avança o SHA e não deixa a árvore de trabalho no meio do merge. Merge limpo avança o SHA até a ponta que entrou, e a olhada seguinte, com a publicação parada, sai vazia. Texto de rotina pode entrar na branch e não é aplicado no banco. Coluna, índice e configuração não mudam. Nada é agendado.

**Blocked by:** 01 — Olhada a partir do ponto gravado

**Status:** ready-for-agent

- [x] O trazer limpo deixa uma branch nova e não troca o checkout
- [x] O que só a publicação mudou entra no resultado
- [x] O que só a Fixar mudou permanece
- [x] Personalização, segredo e arquivo de outro cliente não entram
- [x] Coluna, índice e configuração não mudam, e texto de rotina não é aplicado no banco
- [x] Conflito lista os arquivos, não avança o SHA e não deixa a árvore no meio do merge
- [x] Merge limpo avança o SHA, e a olhada seguinte sai vazia se a publicação não andou

## Comments

Concluído em b921203. O trazer limpo deixa a branch nova sem trocar o checkout. Conflito lista o arquivo e não avança o ponto. A exclusão que o gitignore já fazia entrou em b921203.
