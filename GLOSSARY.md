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
_Avoid_: perfil, notificação

**Cliente**:
Quem compra da Fixar.
_Avoid_: empresa, fornecedor

**Fornecedor**:
Quem vende para a Fixar.
_Avoid_: cliente, empresa

**Hardness**:
O produto comprado, que a Fixar altera. A base abre no comportamento dele.
_Avoid_: personalização, ERP da Fixar

**Publicação**:
O Hardness como o vendedor o publica, na linha master do produto. Não é a base e não é a personalização.
_Avoid_: base, repositório oficial

**Base**:
O Hardness em execução na máquina da Fixar.
_Avoid_: repositório, código

**Personalização**:
Regra da Fixar gravada à parte do Hardness. Cobre orçamento, pedido, boleto, histórico de produto e a totalização que os acompanha.
_Avoid_: Hardness, empresa, cliente

**Perfil**:
Conjunto de permissões de um usuário dentro de uma empresa. O perfil local abre todas as telas do Hardness.
_Avoid_: usuário, empresa
