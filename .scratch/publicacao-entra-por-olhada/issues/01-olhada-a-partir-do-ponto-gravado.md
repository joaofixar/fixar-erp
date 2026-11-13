Status: ready-for-agent
Orbit: ERPFX-23

# 01: Olhada a partir do ponto gravado

**What to build:** Quem desenvolve informa um SHA da publicação e o comando grava esse ponto, sem copiar arquivo. A olhada lista os commits da publicação depois desse SHA, do mais antigo ao mais novo, cada um com identificador e assunto. Se a publicação não andou, a lista sai vazia e o comando termina bem. Se a busca da publicação falha, o comando termina em erro e não apresenta isso como ausência de atualização. O Hardness versionado, o checkout, a base e o banco ficam como estavam. O clone da publicação permanece em master, e a busca não apaga commit local nesse master.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [ ] Informar um SHA grava o ponto e não copia arquivo do Hardness
- [ ] A olhada lista os commits posteriores, do mais antigo ao mais novo, com identificador e assunto
- [ ] Sem commit novo, a olhada termina bem e a lista sai vazia
- [ ] Busca que falha termina em erro, sem lista vazia de sucesso
- [ ] A olhada não altera o Hardness versionado, o checkout, a base nem o banco
- [ ] O clone da publicação permanece em master e a busca não apaga commit local

## Comments
