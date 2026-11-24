# Fixar

ERP interno da Fixar Industrial. O ponto de partida é o Hardness, sem personalização. A operação da empresa se descreve por cima disso.

## Language

**Empresa**:
Um CNPJ que opera comercialmente neste ERP. A Fixar tem mais de uma.
_Avoid_: filial, cliente, schema

**Empresa de validação**:
A empresa em que a base abre. Não tem cliente, produto, orçamento, pedido, nota nem título.
_Avoid_: produção, cliente

**Aviso**:
Texto publicado na tela inicial. Grava mesmo sem empresa; a empresa só entra quando a tela de origem indica uma.
_Avoid_: perfil, notificação, alerta, nota

**Nota**:
Texto da Fixar sobre uma mudança do FX ERP, com data, título e texto, publicado na área de trabalho.
_Avoid_: aviso, patch note, atualização

**Alerta**:
Item não lido que o perfil pode ver no sino.
_Avoid_: aviso, notificação, mensagem

**Cliente**:
Quem compra da Fixar.
_Avoid_: empresa, fornecedor

**Fornecedor**:
Quem vende para a Fixar.
_Avoid_: cliente, empresa

**FX ERP**:
O sistema que a operação vê. Não é uma empresa do grupo.
_Avoid_: Fixar, Fixar ERP, Hardness

**Hardness**:
O produto comprado, que a Fixar altera. A base abre no comportamento dele.
_Avoid_: personalização, FX ERP

**Publicação**:
O Hardness como o vendedor o publica, na linha master do produto. Não é a base e não é a personalização.
_Avoid_: base, repositório oficial

**Base**:
O Hardness em execução na máquina da Fixar.
_Avoid_: repositório, código

**Personalização**:
Regra da Fixar gravada à parte do Hardness. Cobre orçamento, pedido, boleto, histórico de produto e a totalização que os acompanha.
_Avoid_: Hardness, empresa, cliente, app

**App**:
Pacote publicado pela Hardness.
_Avoid_: personalização, módulo, tela

**Loja de apps**:
O catálogo de apps que a Hardness publica.
_Avoid_: Loja Apps, modo IDE

**Usuário**:
Pessoa que entra no FX ERP, identificada pelo login.
_Avoid_: perfil, empresa

**Perfil**:
Conjunto de permissões de um usuário dentro de uma empresa. O perfil local abre todas as telas do Hardness.
_Avoid_: usuário, empresa

**Shell**:
O desktop logado em volta da área de trabalho. Reúne a marca, a empresa, o menu, o usuário e as abas.
_Avoid_: menu, paleta, tema

**Aba**:
Tela de módulo aberta na área de trabalho. Várias ficam abertas; uma fica visível. O que essa tela desenha por dentro faz parte dela.
_Avoid_: janela, tela

**Janela**:
O que flutua sobre a aba visível e fecha com ela, ou, sem aba, sobre a área de trabalho até uma aba ocupar essa área.
_Avoid_: aba, tela

**Menu**:
Os grupos e os itens de aba ou janela que um perfil pode abrir.
_Avoid_: paleta, atalho

**Paleta**:
A escolha, no centro da área de trabalho, de uma aba ou janela que o perfil já pode abrir.
_Avoid_: menu, atalho, busca de cliente, recente, ação da aba

**Miolo**:
O que a aba ou a janela desenha por dentro.
_Avoid_: shell, tema

**Tema**:
O conjunto único de tokens do desktop.
_Avoid_: tema-claro, tema-escuro, tema-2021, shell

**Token**:
Valor nomeado de cor, tipo ou densidade compartilhado pelo shell e pelo miolo.
_Avoid_: tema

**Densidade**:
O passo de tipo e espaçamento do miolo. O usuário escolhe o passo.
_Avoid_: zoom, fonte, tema
