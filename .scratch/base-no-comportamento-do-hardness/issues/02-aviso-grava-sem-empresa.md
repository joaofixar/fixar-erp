Status: ready-for-agent
Orbit: ERPFX-20

# 02: Aviso grava sem empresa

**What to build:** No mesmo percurso, gravar um aviso a partir da lista de empresas cria a linha sem empresa. A empresa da sessão não entra. Editar sem identificador não apaga uma empresa que já estava gravada.

**Blocked by:** 01 — Rotinas ausentes no percurso da base

**Status:** ready-for-agent

- [x] Gravar um aviso a partir da lista de empresas cria a linha
- [x] A empresa do aviso fica vazia
- [x] A empresa da sessão não é gravada
- [x] Editar sem identificador não apaga uma empresa já gravada
- [x] O texto digitado é o texto gravado

## Comments

Concluído em eae5ae8d. O aviso grava a empresa só quando a requisição indica. Está no master desde bb35ff6d.
