Status: ready-for-agent
Orbit: ERPFX-14

# 05: A base continua sem falar com o mundo

**What to build:** No percurso de entrar, abrir cada item visível, abrir orçamento, pedido e boleto pelo endereço e sair, nada conecta com a receita, com banco, com e-mail ou com o fornecedor. O clique de emitir nota, o de registrar boleto e o de enviar e-mail também não saem da máquina. A subida não consulta boleto nem telemetria do fornecedor.

**Blocked by:** 02 — O perfil abre o que a base escondia; 03 — O perfil abre os módulos que o Hardness mostra; 04 — A personalização não executa

**Status:** ready-for-agent

- [x] Entrar, abrir cada item visível, abrir orçamento, pedido e boleto pelo endereço e sair não abre conexão com a receita, com banco, com e-mail nem com o fornecedor
- [x] O clique de emitir nota não chega à receita
- [x] O clique de registrar boleto não chega ao banco
- [x] O clique de enviar e-mail não sai da máquina
- [x] A subida não consulta boleto nem telemetria do fornecedor

## Comments

Concluído em 6d0c979. O catálogo de permissões veio da produção e ficou atrelado ao perfil local, para o C.R.M. abrir orçamento, pedido e nota.
