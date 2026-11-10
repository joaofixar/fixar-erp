# Fixar

ERP interno da Fixar Industrial. Descreve a operação da empresa, não o produto de prateleira de onde o código partiu.

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
