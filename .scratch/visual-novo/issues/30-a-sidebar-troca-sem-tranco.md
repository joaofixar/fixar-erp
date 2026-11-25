Status: ready-for-human
Orbit: ERPFX-59

# 30: A sidebar troca sem tranco

**What to build:** Recolher e expandir a sidebar é imediato. A coluna não anima, e o miolo se reposiciona no mesmo quadro. Antes, a coluna animava em 0,18s, e o miolo, preso em pixels, só pulava para o lugar 200ms depois.

Ajusta o [ticket 28](28-a-sidebar-recolhe-e-a-aba-ganha-desenho.md).

**Blocked by:** nenhum

**Status:** ready-for-human

- [x] Recolher e expandir não anima a coluna
- [x] O miolo acompanha a sidebar no mesmo quadro
- [x] O percurso confere a borda do miolo logo depois de recolher

## Comments

Em `src/fx/entrada.css` (sai a `transition` da coluna) e `src/fx/shell.js` (`alternarRecolhida` dispara o `resize` na hora). As janelas de módulo têm `left` e `width` em pixels, gravados pelo `resize` do `sistema.js`; uma transição de CSS na coluna não as leva junto.
