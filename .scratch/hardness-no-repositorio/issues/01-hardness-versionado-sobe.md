Status: ready-for-agent
Orbit: ERPFX-16

# 01: O Hardness versionado sobe

**What to build:** A base sobe servindo o Hardness que está no repositório. Login, menu e telas continuam os de hoje, em `/login` e `/hardness3/`. Subir de novo não troca esse Hardness. Um ajuste num grid aparece ao atualizar esse grid. Segredo, cache, arquivo de uso, pasta de outro cliente, documento e a configuração de conexão ficam fora do que se versiona. A referência do legado continua só de leitura.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [x] A base sobe e o login abre em `/login`
- [x] O menu do Hardness abre e as telas visíveis respondem em `/hardness3/`
- [x] Subir a base de novo não troca o Hardness que respondeu
- [x] Um ajuste num grid do Hardness versionado aparece ao atualizar esse grid
- [x] Personalização de outro cliente não aparece
- [x] A referência do legado permanece só de leitura

## Comments

Concluído em d41015e. O contêiner serve o Hardness de `src/` desde 450612f. Chave, keystore e arquivo de outro cliente saíram do que o git rastreia em a3a3004.
