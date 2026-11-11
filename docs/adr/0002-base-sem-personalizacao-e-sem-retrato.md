---
status: accepted
---

# A base de trabalho é o Hardness sem personalização e sem retrato

A evolução parte da base já no ar: a estrutura das tabelas da produção, sem linhas de negócio. A personalização de orçamento, pedido, boleto e histórico de produto não entra em execução. Não há retrato da produção. A produção continua onde está.

## Considered Options

- Carregar um retrato da produção para ensaiar mudanças sobre o movimento real. O movimento real atrapalha quem está desenhando o modelo a partir do vazio, e cada execução apagaria o ensaio local.
- Manter a personalização ativa porque a Fixar já opera com ela. O objetivo agora é chegar ao modelo que a Fixar precisa partindo do Hardness, não preservar o comportamento atual.

## Consequences

- O menu fica o do Hardness. Orçamento, pedido e boleto seguem comentados, como o menu genérico entrega. Os itens que a base escondia voltam a aparecer.
- A empresa de validação copiada da produção continua sendo onde se entra, com os parâmetros dela. O usuário e a senha continuam os definidos aqui.
- O que só existe para o Hardness abrir no PHP 8.3 permanece. Não é personalização.
- O isolamento do ADR 0001 permanece. A base não emite nota, não registra boleto, não envia e-mail e não fala com o fornecedor.
- O perfil local abre todas as telas do Hardness. Orçamento, pedido e boleto continuam fora do menu e seguem alcançáveis quando alguém decidir abrir o módulo.
- Nenhuma personalização entra em execução. O conjunto gravado à parte inclui, além das quatro regras que a base hoje liga, a totalização e o reprocesso de estoque.
- A spec da base local vale só onde concorda com este ADR. Onde discorda, vale este ADR.
