Status: ready-for-agent
Orbit: ERPFX-9

# A base abre como o Hardness

## Problem Statement

Quem desenvolve quer ajustar o Hardness a partir do vazio, até o modelo que a Fixar precisa. A base que sobe hoje ainda não é esse ponto de partida: ela liga a personalização de orçamento, pedido, boleto e histórico de produto, mostra orçamento, pedido e boleto no menu, e esconde itens que o Hardness entrega visíveis.

## Solution

A mesma base local passa a abrir como o Hardness. O menu é o do produto. A personalização não executa. O perfil local abre todas as telas do produto. A empresa de validação copiada da produção continua sendo onde se entra, com o usuário e a senha definidos aqui. Não há linhas de negócio nem retrato da produção. A base continua sem emitir nota, sem registrar boleto, sem enviar e-mail e sem falar com o fornecedor.

## User Stories

1. Como desenvolvedor da Fixar, quero subir a base com o mesmo comando de hoje, para ver o Hardness sem personalização.
2. Como desenvolvedor da Fixar, quero abrir a tela de login, para saber que a base está no ar.
3. Como desenvolvedor da Fixar, quero ver na tela de login a empresa de validação, para entrar na empresa que concentra orçamento, pedido e nota fiscal de saída.
4. Como desenvolvedor da Fixar, quero entrar com o usuário local e a senha definidos aqui, para não usar senha de produção.
5. Como desenvolvedor da Fixar, quero cair dentro dessa empresa ao entrar, para o trabalho local acontecer nela.
6. Como desenvolvedor da Fixar, quero uma mensagem clara quando a senha está errada, para saber que continuei deslogado.
7. Como desenvolvedor da Fixar, quero que um atalho de URL não me entre como administrador sem senha, para a base não herdar essa porta.
8. Como desenvolvedor da Fixar, quero uma falha explícita quando o banco local não está pronto, para não confundir isso com uma tela em branco.
9. Como desenvolvedor da Fixar, quero que o banco local continue depois de reiniciar o contêiner, para não recriar a estrutura a cada subida.
10. Como desenvolvedor da Fixar, quero a estrutura das tabelas igual à da produção de hoje, para a tela não quebrar numa coluna que só existe lá.
11. Como desenvolvedor da Fixar, quero essa estrutura sem cliente, fornecedor, produto, orçamento, pedido, nota e título, para o trabalho não depender de movimento real.
12. Como desenvolvedor da Fixar, quero que a subida não copie linhas de negócio da produção, para não existir retrato.
13. Como desenvolvedor da Fixar, quero que uma nova subida também não traga linhas de negócio, para o vazio permanecer.
14. Como desenvolvedor da Fixar, quero que a leitura da estrutura, quando precisar da produção, continue somente leitura e sem trava, para não parar quem está operando.
15. Como desenvolvedor da Fixar, quero o menu do Hardness, para ver o produto como ele é entregue.
16. Como desenvolvedor da Fixar, quero orçamento fora do menu, para o item comentado no Hardness continuar comentado.
17. Como desenvolvedor da Fixar, quero pedido fora do menu, para o item comentado no Hardness continuar comentado.
18. Como desenvolvedor da Fixar, quero boleto fora do menu, para o item comentado no Hardness continuar comentado.
19. Como desenvolvedor da Fixar, quero no menu o cadastro em que cliente e fornecedor convivem, para abrir a lista mesmo vazia.
20. Como desenvolvedor da Fixar, quero produtos, notas fiscais, estoque, expedição e notas fiscais recebidas no menu, para abrir o que o Hardness já mostra.
21. Como desenvolvedor da Fixar, quero contas a receber, contas a pagar e caixa e bancos no menu, para abrir o financeiro que o Hardness já mostra.
22. Como desenvolvedor da Fixar, quero CFOP, NCM e ICMS no menu, para abrir o cadastro fiscal que o Hardness já mostra.
23. Como desenvolvedor da Fixar, quero usuários e empresa no menu, para ver o usuário local e a empresa de validação.
24. Como desenvolvedor da Fixar, quero Banco Dados Clientes, Contadores Clientes, Usuarios Clientes e Opções no menu, para voltar o que a base escondia.
25. Como desenvolvedor da Fixar, quero Conferência Cega e Créditos Troca no menu, para voltar o que a base escondia.
26. Como desenvolvedor da Fixar, quero PDV, nota de loja, CRM, e-mail, PCP, SRM, comex, Serasa, SPED, módulo contábil e BI no menu quando o Hardness os mostra, para o produto inteiro aparecer.
27. Como desenvolvedor da Fixar, quero coletor e os demais itens que o Hardness entrega comentados fora do menu, para a base não inventar módulo.
28. Como desenvolvedor da Fixar, quero abrir cada item visível do menu, para o perfil local não bloquear o que o produto mostra.
29. Como desenvolvedor da Fixar, quero grade vazia ao abrir uma tela sem movimento, para o vazio ser sucesso.
30. Como desenvolvedor da Fixar, quero ver o usuário local na tela de usuários, para reconhecer com quem entrei.
31. Como desenvolvedor da Fixar, quero ver a empresa de validação na tela de empresa, para reconhecer onde entrei.
32. Como desenvolvedor da Fixar, quero abrir orçamento pelo endereço do produto, para o perfil local alcançar o módulo mesmo fora do menu.
33. Como desenvolvedor da Fixar, quero abrir pedido pelo endereço do produto, para o perfil local alcançar o módulo mesmo fora do menu.
34. Como desenvolvedor da Fixar, quero abrir boleto pelo endereço do produto, para o perfil local alcançar o módulo mesmo fora do menu.
35. Como desenvolvedor da Fixar, quero que o orçamento que abre seja o do Hardness, para a personalização não alterar a tela.
36. Como desenvolvedor da Fixar, quero que o pedido que abre seja o do Hardness, para a personalização não alterar a tela.
37. Como desenvolvedor da Fixar, quero que o boleto que abre seja o do Hardness, para a personalização não substituir a grade.
38. Como desenvolvedor da Fixar, quero que o histórico de produto que abre seja o do Hardness, para a personalização não substituir a grade.
39. Como desenvolvedor da Fixar, quero que a totalização e o reprocesso de estoque da Fixar não executem, para a personalização gravada à parte ficar inerte.
40. Como desenvolvedor da Fixar, quero que personalização de outro cliente não apareça, para a base ser o Hardness da Fixar e não um acervo de clientes.
41. Como desenvolvedor da Fixar, quero abrir uma grade vazia no PHP atual da base, para a página não cair numa conta ou num aviso.
42. Como desenvolvedor da Fixar, quero sair da sessão e voltar ao login, para encerrar o acesso local.
43. Como desenvolvedor da Fixar, quero que login e abertura de tela não abram conexão com a receita, com banco, com e-mail nem com o fornecedor do sistema, para a base continuar isolada.
44. Como desenvolvedor da Fixar, quero que um clique de emitir nota não chegue à receita, para uma exploração não gerar nota real.
45. Como desenvolvedor da Fixar, quero que um clique de registrar boleto não chegue ao banco, para uma exploração não registrar cobrança.
46. Como desenvolvedor da Fixar, quero que um clique de enviar e-mail não saia da máquina, para a base não escrever para cliente nem fornecedor.
47. Como desenvolvedor da Fixar, quero que a subida não consulte boleto nem telemetria do fornecedor do sistema, para o login não depender dele.
48. Como segundo desenvolvedor da Fixar, quero repetir a subida na minha máquina com os mesmos passos, para ver o mesmo Hardness sem personalização.

## Implementation Decisions

- A base em execução deixa de aplicar a personalização e deixa de reescrever o menu. O menu permanece o que o Hardness entrega: orçamento, pedido e boleto comentados; Banco Dados Clientes, Contadores Clientes, Usuarios Clientes, Opções, Conferência Cega e Créditos Troca visíveis; visível também o que o produto já mostra, inclusive PDV, nota de loja, CRM, e-mail, PCP, SRM, comex, Serasa, SPED, módulo contábil e BI.
- O perfil local libera todas as telas que o produto conhece. Orçamento, pedido e boleto seguem fora do menu e abrem pelo endereço do produto.
- A personalização de orçamento, pedido, boleto, histórico de produto, totalização e reprocesso de estoque não é instalada na base que roda. A referência original permanece intocada.
- A empresa semeada continua a que concentra orçamento, pedido e nota fiscal de saída, com os parâmetros copiados da produção. O usuário e a senha continuam os definidos aqui. Outras empresas da Fixar não entram.
- A estrutura continua a da produção, sem linhas de negócio. Não há retrato nem comando que copie cliente, produto, orçamento, pedido, nota ou título.
- O que só existe para o Hardness abrir no PHP 8.3 permanece. O isolamento permanece: a base não emite nota, não registra boleto, não envia e-mail e não fala com o fornecedor.
- Credencial fica fora do git. Cada desenvolvedor tem o próprio banco local, e ele sobrevive a um reinício do contêiner.

## Testing Decisions

- Um bom teste observa comportamento de fora, pelo HTTP do sistema no ar. É o mesmo ponto já usado para entrar, ler o menu e abrir tela. Não confere arquivo da personalização, SQL montado, template nem função interna.
- O percurso é um só: entrar com o usuário local na empresa de validação, ler o menu, abrir cada item visível, abrir orçamento, pedido e boleto pelo endereço do produto, e sair. Grade vazia passa. Item comentado no Hardness não pode aparecer no menu. Item que a base escondia tem de aparecer.
- A personalização de cálculo não tem linha de negócio para divergir. O teste não cria cliente nem produto para forçar essa diferença. O que se observa é a tela do produto respondendo, e a grade de boleto e a de histórico de produto serem as do Hardness.
- Qualquer conexão para a receita, banco, e-mail ou fornecedor do sistema durante o percurso reprova. O clique de emitir nota, registrar boleto e enviar e-mail também.
- Senha errada continua deslogado. Banco ausente falha de forma explícita. Atalho de administrador sem senha não entra.

## Out of Scope

- Desenhar o modelo ideal da Fixar, reescrever tela ou trocar a linguagem do legado.
- Um banco de fábrica diferente da estrutura atual da produção, ou uma empresa em branco no lugar da empresa de validação.
- Retrato da produção, cópia de linhas de negócio e inutilização de segredo além do que a base isolada já garante.
- Emitir nota de verdade, registrar boleto de verdade e enviar e-mail de verdade.
- Acompanhar release do fornecedor, revender o sistema ou atender outro cliente.
- Escolher outro CNPJ que não seja o da empresa que concentra orçamento, pedido e nota fiscal de saída.

## Further Notes

- Vocabulário: empresa é o CNPJ que opera no ERP; cliente é quem compra da Fixar; fornecedor é quem vende para a Fixar; Hardness é o produto comprado; personalização é a regra da Fixar gravada à parte; perfil é o conjunto de permissões de um usuário dentro de uma empresa. Cliente e fornecedor convivem no mesmo cadastro.
- O ADR 0001 continua na origem do sistema, no isolamento e na empresa de validação. O ADR 0002 é a decisão desta spec.
- A spec da base local permanece como histórico. Onde discorda deste arquivo, vale o ADR 0002.
