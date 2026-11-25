Status: ready-for-agent
Orbit: ERPFX-19

# 01: Rotinas ausentes no percurso da base

**What to build:** Numa base descartável, o passo cria as rotinas da lista do Hardness que ainda não existem. O status pendente do orçamento volta como pendente. As rotinas da fotografia continuam iguais. Um arquivo que falha aparece na lista e o resto ainda é criado. O mesmo passo roda num volume novo, depois da fotografia.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [x] O percurso sobe uma base descartável e roda o passo das rotinas
- [x] O status pendente do orçamento volta como pendente
- [x] As rotinas que a fotografia já trouxe continuam com a mesma definição
- [x] Um arquivo que falha fica nomeado e as outras rotinas ainda são criadas
- [x] Um volume novo roda o mesmo passo depois da fotografia
- [x] Rodar o passo de novo não recria rotina que já existe

## Comments

Concluído em e95842d4. O passo no volume que já tem dados entra em 1623788e. Os dois estão no master desde bb35ff6d.
