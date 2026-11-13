Status: ready-for-agent
Orbit: ERPFX-24

# 02: Primeira olhada mostra os candidatos

**What to build:** Sem SHA gravado, a olhada mostra os commits da publicação mais próximos da árvore atual do Hardness e não copia arquivo. Informar um desses SHAs grava o ponto, também sem copiar. A olhada seguinte parte desse ponto e lista só o que veio depois.

**Blocked by:** 01 — Olhada a partir do ponto gravado

**Status:** ready-for-agent

- [x] Sem SHA gravado, a olhada mostra candidatos e não copia arquivo
- [x] Informar um SHA candidato grava o ponto e não copia arquivo
- [x] A olhada seguinte parte do SHA gravado
- [x] O Hardness versionado, o checkout, a base e o banco ficam como estavam

## Comments

Concluído em 76685a6. Sem ponto gravado, a olhada mostra os commits mais próximos e não copia.
