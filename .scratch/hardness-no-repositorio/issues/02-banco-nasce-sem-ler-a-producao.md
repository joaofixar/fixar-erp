Status: ready-for-agent
Orbit: ERPFX-17

# 02: O banco nasce sem ler a produção

**What to build:** Com o volume vazio e sem credencial de produção, a base abre na empresa de validação, com o usuário local e o perfil que alcança orçamento, pedido e nota no C.R.M. A subida não conecta na produção. Um volume que já tem a base não é apagado. A importação dos dados de produção não entra neste corte.

**Blocked by:** None (can start immediately).

**Status:** ready-for-agent

- [x] Com o volume vazio e sem credencial de produção, o login abre na empresa de validação
- [x] A tela de usuários mostra o usuário local
- [x] A tela de empresa mostra a empresa de validação
- [x] O C.R.M. mostra orçamento, pedido e nota
- [x] A subida não conecta na produção
- [x] Subir de novo com o volume já preenchido não apaga a base

## Comments

Concluído em d41015e. A fotografia local entra em a42b9c9 e a subida deixa de ler a produção. O percurso do volume vazio foi observado de novo em d41015e.
