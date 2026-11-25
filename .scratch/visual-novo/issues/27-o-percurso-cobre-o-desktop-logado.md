Status: ready-for-agent
Orbit: ERPFX-56

# 27: O percurso cobre o desktop logado

**What to build:** Um percurso no navegador, contra a base já no ar na branch `visual-novo`, cobre marca, empresa, menu, paleta, faixa, janela de inclusão, Cliente/Fornecedor com a faixa vazia, área vazia, sino, avatar (densidade e sair) e o grupo Apps. Grade vazia passa. Certificado, Aviso, Nota e Alerta ausentes são o vazio da empresa de validação. O script não troca o checkout, não sobe a base e não roda o trazer. O master não é o checkout sob teste.

**Blocked by:** 22 — O sino lista o alerta. 23 — A faixa vazia mostra a área de trabalho. 25 — A densidade muda o passo. 26 — O menu ganha a loja e o grupo Apps.

**Status:** ready-for-agent

- [x] Um percurso no navegador entra com o usuário local na empresa de validação, na base já no ar da branch `visual-novo`
- [x] O script não troca o checkout, não sobe a base e não roda o trazer
- [x] O percurso cobre a marca, a empresa sob a marca, o menu, a paleta (filtrar, abrir, desistir, busca sem resultado), a faixa (abrir, trocar, fechar, reordenar na sessão, recarregar vazia), a janela de inclusão sobre a aba, Cliente/Fornecedor como janela com a faixa vazia, a área vazia, o sino, o avatar (densidade e sair) e o grupo Apps
- [x] Grade vazia passa
- [x] Certificado, Aviso, Nota e Alerta ausentes são o estado vazio: sem cartão, sem bloco vazio de Nota, pop do sino no vazio do Hardness
- [x] O percurso não copia linha de produção para forçar certificado, segunda empresa, alerta de outra empresa nem usuário root
- [x] Com uma empresa, o controle é rótulo
- [x] A lista de usuários só aparece se o perfil permitir
- [x] A Loja de apps só aparece se o usuário local for root no critério de hoje; a ausência, quando não é, passa
- [x] O master não é o checkout sob teste
- [x] O percurso observa o que a pessoa vê e o que a ação faz; cor entra na sidebar, no fundo da área, na linha sob o mouse e no tamanho do tipo depois da troca de passo

## Comments

Implementado em `11284976`. O percurso entra com o usuário local na base já no ar de `visual-novo` e cobre marca, empresa, menu, paleta, faixa, inclusão, Cliente/Fornecedor, área vazia, sino, avatar e Apps. A Loja de apps só é exigida para o usuário 63 ou o flag de suporte. A grade comum não recebia a classe hover, então a linha sob o mouse passa a seguir o amarelo de hoje pelo ponteiro.
