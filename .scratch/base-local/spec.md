Status: ready-for-agent

# Base local do ERP

## Problem Statement

A Fixar precisa ver o próprio ERP abrir na máquina de quem desenvolve. Hoje o código comprado não sobe: falta a configuração local, a estrutura das tabelas que a empresa usa agora e os registros mínimos de uma empresa e de um usuário. Sem isso, ninguém confere uma tela, e qualquer evolução parte de um sistema que não roda.

## Solution

Uma base local, no contêiner do repositório, com a estrutura atual das tabelas da produção e sem linhas de negócio. Entra uma empresa — a que concentra orçamento, pedido e nota fiscal de saída — e um usuário criado aqui. A pessoa entra pela tela de login e abre as telas do ciclo comercial. Grades vazias são sucesso. O processo não fala com a receita, com banco, com e-mail nem com o fornecedor do sistema.

## User Stories

1. Como desenvolvedor da Fixar, quero subir a base com um comando, para não instalar o runtime do legado na minha máquina.
2. Como desenvolvedor da Fixar, quero que o mesmo comando rode no Windows e na máquina do outro dev, para os dois verem o mesmo sistema.
3. Como desenvolvedor da Fixar, quero que a base suba sem internet, para um bloqueio do fornecedor do sistema não me impedir de trabalhar.
4. Como desenvolvedor da Fixar, quero abrir a tela de login no navegador, para saber que o sistema está no ar.
5. Como desenvolvedor da Fixar, quero entrar com o usuário local e a senha definida aqui, para não usar senha de ninguém da produção.
6. Como desenvolvedor da Fixar, quero cair dentro da empresa de validação ao entrar, para olhar o ciclo como essa empresa opera.
7. Como desenvolvedor da Fixar, quero uma mensagem clara quando a senha está errada, para saber que continuei deslogado.
8. Como desenvolvedor da Fixar, quero uma falha explícita quando o banco local não está pronto, para não confundir isso com uma tela em branco.
9. Como desenvolvedor da Fixar, quero que o banco local continue depois de eu reiniciar o contêiner, para não recriar a estrutura a cada subida.
10. Como desenvolvedor da Fixar, quero a estrutura das tabelas igual à da produção de hoje, para a tela não quebrar numa coluna que só existe lá.
11. Como desenvolvedor da Fixar, quero essa estrutura sem clientes, produtos, orçamentos, pedidos, notas e títulos, para o primeiro marco não depender de dados reais.
12. Como desenvolvedor da Fixar, quero que a leitura da estrutura na produção não trave quem está operando, para a cópia não parar o expediente.
13. Como desenvolvedor da Fixar, quero que essa leitura não grave nada na produção, para a base local não ser um risco operacional.
14. Como desenvolvedor da Fixar, quero a credencial dessa leitura fora do git, para a senha não viajar no repositório.
15. Como desenvolvedor da Fixar, quero um clone novo sem mídia pesada, cache, arquivo gerado pelo uso e pastas de outros clientes, para o repositório caber e não carregar código alheio.
16. Como desenvolvedor da Fixar, quero ver no menu o cadastro em que cliente e fornecedor convivem, para abrir a lista mesmo vazia.
17. Como desenvolvedor da Fixar, quero abrir produtos, para ver a grade vazia sem erro.
18. Como desenvolvedor da Fixar, quero abrir orçamentos, para a tela que a Fixar usa estar no menu local.
19. Como desenvolvedor da Fixar, quero abrir pedidos, para a tela que a Fixar usa estar no menu local.
20. Como desenvolvedor da Fixar, quero abrir a nota fiscal de saída, para chegar na listagem sem emitir nota.
21. Como desenvolvedor da Fixar, quero abrir o estoque, para ver a posição vazia sem erro.
22. Como desenvolvedor da Fixar, quero abrir a nota fiscal de entrada, para chegar na listagem sem importar XML.
23. Como desenvolvedor da Fixar, quero abrir a expedição, para ver a tela do ciclo sem movimento.
24. Como desenvolvedor da Fixar, quero abrir contas a receber, para ver a grade vazia.
25. Como desenvolvedor da Fixar, quero abrir contas a pagar, para ver a grade vazia.
26. Como desenvolvedor da Fixar, quero abrir caixa e bancos, para ver a tela sem movimento.
27. Como desenvolvedor da Fixar, quero abrir boletos, para a tela que a Fixar personalizou estar no menu local.
28. Como desenvolvedor da Fixar, quero abrir os cadastros fiscais usados na nota (CFOP, NCM e ICMS), para a tela existir sem transmitir nada à receita.
29. Como desenvolvedor da Fixar, quero abrir usuários, para ver o usuário local com que entrei.
30. Como desenvolvedor da Fixar, quero abrir a empresa, para ver a empresa de validação com que entrei.
31. Como desenvolvedor da Fixar, quero um perfil que abra todas essas telas, para não esbarrar em permissão no primeiro marco.
32. Como desenvolvedor da Fixar, quero o menu sem PDV, nota de loja, CRM, e-mail integrado, PCP, SRM, comex, Serasa, SPED, módulo contábil, BI, coletor, loja virtual e B2B, para o que ficou de fora do ciclo não parecer parte do trabalho.
33. Como desenvolvedor da Fixar, quero sair da sessão e voltar ao login, para encerrar o acesso local.
34. Como desenvolvedor da Fixar, quero que um atalho de URL não me entre como administrador sem senha, para a base local não herdar essa porta.
35. Como desenvolvedor da Fixar, quero que a subida não consulte boleto nem telemetria do fornecedor do sistema, para o login não depender dele.
36. Como desenvolvedor da Fixar, quero que, ao percorrer login e telas do ciclo, nada saia da máquina em direção à receita, a um banco, a um servidor de e-mail ou ao fornecedor do sistema, para a base ser isolada de verdade.
37. Como desenvolvedor da Fixar, quero que um clique de emitir nota na base local não chegue à receita, para uma exploração da tela não gerar nota real.
38. Como desenvolvedor da Fixar, quero que um clique de registrar boleto não chegue ao banco, para uma exploração da tela não registrar cobrança.
39. Como desenvolvedor da Fixar, quero que um clique de enviar e-mail não saia da máquina, para a base não escrever para cliente nem fornecedor.
40. Como desenvolvedor da Fixar, quero as regras próprias já existentes de orçamento, pedido, boleto e histórico de produto na cópia que roda, para a base refletir a Fixar e não só o produto genérico.
41. Como segundo desenvolvedor da Fixar, quero repetir a subida na minha máquina com os mesmos passos, para cada um ter o próprio banco local.

## Implementation Decisions

- A base executável é uma cópia mantida do sistema comprado. A referência original permanece intocada e fora do git. Reescrever um módulo não faz parte deste marco.
- A aplicação deste marco é a do sistema comprado. O interpretador é PHP 8.3, no contêiner padrão do repositório. Quem desenvolve não depende do PHP instalado na máquina. Chamadas que o PHP 8 removeu e que o legado ainda faz ficam atrás de uma camada fina de compatibilidade. A sintaxe que o PHP 8 recusa carregar é corrigida na cópia que roda. O legado já silencia aviso de função antiga; isso permanece, para depreciação não derrubar a página.
- O banco do primeiro login recebe a estrutura atual da produção, lida com usuário somente leitura e sem trava que pare a escrita de quem opera. Um dump antigo de outro cliente não serve de estrutura.
- Junto da estrutura entram só os registros para login e para abrir o ciclo: uma empresa, um usuário com senha definida aqui, e o vínculo de permissão desse usuário. A empresa semeada é a que concentra orçamento, pedido e nota fiscal de saída. A Fixar tem outras empresas; elas não entram neste marco.
- O menu local mostra o ciclo: cadastro de cliente e fornecedor, produtos, orçamento, pedido, nota fiscal de saída, estoque, nota fiscal de entrada, expedição, contas a receber, contas a pagar, caixa e bancos, boleto, CFOP, NCM, ICMS, usuários e empresa. Orçamento, pedido e boleto aparecem mesmo que o menu genérico do produto os esconda.
- Código compartilhado das telas que ficaram de fora do ciclo permanece, porque as telas se chamam. O que sai é o item de menu. Personalização de outro cliente não entra na cópia.
- A subida não consulta o fornecedor do sistema e não oferece entrada de administrador sem senha. Durante o uso local o processo não abre conexão com a receita, com banco, com e-mail nem com esse fornecedor.
- Credencial fica fora do git. Também ficam de fora cache, arquivo gerado pelo uso, mídia pesada, pastas de outros clientes e o script de backup que carrega senha. Entram no git o código que faz a base subir, a definição do contêiner e a documentação.
- O banco local sobrevive a um reinício do contêiner. Cada desenvolvedor tem o seu.

## Testing Decisions

- Um bom teste observa comportamento de fora, pelo HTTP do sistema no ar. Confere login, tela aberta, grade vazia e ausência de conexão de saída. Não confere SQL montado, template nem função interna.
- O módulo sob teste é a base em execução. Não há suíte anterior neste repositório para servir de modelo. A referência intocada do legado não é o objeto do teste.
- O percurso coberto é: subir o contêiner, entrar com o usuário local na empresa de validação, abrir cada tela do ciclo e sair. Grade vazia passa. Qualquer conexão para a receita, banco, e-mail ou fornecedor do sistema durante esse percurso reprova.
- Senha errada continua deslogado. Banco ausente falha de forma explícita.

## Out of Scope

- O importador de dados de negócio: cópia de clientes, títulos, notas e demais linhas, inutilização de segredo, retrato que substitui a base e nova execução. A decisão continua valendo; a spec dele vem depois que esta base entrar.
- Emitir nota de verdade, registrar boleto de verdade e enviar e-mail de verdade. Esta spec só garante que a base local não faça isso.
- PDV, nota de loja, CRM, e-mail integrado, PCP, SRM, comex, Serasa, SPED, módulo contábil, BI, coletor, lojas virtuais, B2B e personalização de outro cliente.
- Acompanhar release do fornecedor, revender o sistema ou atender outro cliente.
- Reescrever telas ou trocar a linguagem do legado.
- Popular o banco local com movimento da produção.
- Escolher, entre as empresas da Fixar, algum CNPJ que não seja o da empresa que concentra orçamento, pedido e nota fiscal de saída.

## Further Notes

- Vocabulário: empresa é o CNPJ que opera no ERP; cliente é quem compra da Fixar; fornecedor é quem vende para a Fixar. Cliente e fornecedor convivem no mesmo cadastro.
- A decisão de nascer do legado, validar com uma empresa e deixar o importador para depois está no ADR do sistema novo. Esta spec cobre só o primeiro marco, a base local no ar.
- Trazer a estrutura das tabelas é parte desta base. Trazer as linhas de negócio não é.
