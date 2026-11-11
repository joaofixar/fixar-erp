# Fixar

ERP interno da Fixar Industrial. O ponto de partida é o Hardness, sem personalização. A operação da empresa se descreve por cima disso.

## Language

**Empresa**:
Um CNPJ que opera comercialmente neste ERP. A Fixar tem mais de uma.
_Avoid_: filial, cliente, schema

**Cliente**:
Quem compra da Fixar.
_Avoid_: empresa, fornecedor

**Fornecedor**:
Quem vende para a Fixar.
_Avoid_: cliente, empresa

**Hardness**:
O produto comprado. A base de trabalho abre no comportamento dele.
_Avoid_: personalização, ERP da Fixar

**Personalização**:
Regra da Fixar gravada à parte do Hardness. Cobre orçamento, pedido, boleto, histórico de produto e a totalização que os acompanha.
_Avoid_: Hardness, empresa, cliente

**Perfil**:
Conjunto de permissões de um usuário dentro de uma empresa. O perfil local abre todas as telas do Hardness.
_Avoid_: usuário, empresa
