Status: ready-for-agent
Orbit: ERPFX-54

# 25: A densidade muda o passo

**What to build:** Quem não escolheu abre no passo Pequeno (12). Quem já está em Médio ou Grande continua. Pequeno, Médio e Grande ficam no avatar, entre o perfil e Sair, com o passo atual marcado. A escolha grava o mesmo campo de hoje, inclusive pela tela de configuração da interface, e muda tipo e espaçamento do miolo. O padrão da coluna passa de 13 para 12.

O passo, vindo da spec:

| Passo | Nome | Fonte | Cabeçalho | Célula | Aba | Campo | Título | Entrelinha |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 12 | Pequeno | 12px | 4px 8px | 2px 8px | 22px | 24px | 4px 8px | 1.3 |
| 13 | Médio | 13px | 5px 8px | 4px 8px | 24px | 26px | 5px 8px | 1.3 |
| 14 | Grande | 14px | 6px 10px | 5px 10px | 28px | 30px | 6px 10px | 1.35 |

**Blocked by:** 21 — A empresa e o usuário ficam no shell. 24 — O miolo pinta pelo tema.

**Status:** ready-for-agent

- [ ] Quem ainda não escolheu densidade abre no passo 12, Pequeno
- [ ] Quem já gravou 13 ou 14 continua nesse passo
- [ ] Pequeno, Médio e Grande ficam no menu do avatar, entre o perfil e Sair, com o passo atual marcado
- [ ] Dá para subir e voltar de passo
- [ ] A escolha fica gravada e o próximo login abre no passo deixado
- [ ] O passo muda tipo e espaçamento do miolo: grade, campo e aba
- [ ] A tela de configuração da interface grava o mesmo passo
- [ ] O padrão da coluna, para quem não escolheu, é 12
