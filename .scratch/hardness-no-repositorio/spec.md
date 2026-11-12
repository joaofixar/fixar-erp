Status: ready-for-agent
Orbit: ERPFX-15

# O Hardness editável fica no repositório

## Problem Statement

Quem desenvolve quer alterar o Hardness no projeto. O código que sobe hoje é uma cópia gerada, fora do git, recriada a partir da referência só de leitura. Um ajuste feito nela não fica no histórico e a preparação seguinte apaga. O banco de uma máquina nova ainda nasce de uma leitura da produção, antes de existir melhoria que peça esses dados.

## Solution

O Hardness que já abre entra no repositório, e o contêiner serve esse código. A preparação deixa de recriar o produto. O banco nasce de uma fotografia do MariaDB local: estrutura, empresa de validação, usuário local e permissões, sem linhas de negócio. A subida restaura essa fotografia só quando o volume está vazio e não conecta na produção. O endereço continua `/login` e `/hardness3/`. O produto continua se chamando Hardness.

## User Stories

1. Como desenvolvedor da Fixar, quero ver o Hardness no repositório, para alterá-lo no projeto.
2. Como desenvolvedor da Fixar, quero que um ajuste no Hardness permaneça depois de subir a base de novo, para a preparação não apagar o que fiz.
3. Como desenvolvedor da Fixar, quero subir a base com o mesmo comando de hoje, para não instalar o runtime na minha máquina.
4. Como desenvolvedor da Fixar, quero abrir a tela de login, para saber que a base está no ar.
5. Como desenvolvedor da Fixar, quero ver na tela de login a empresa de validação, para entrar na empresa em que a base abre.
6. Como desenvolvedor da Fixar, quero entrar com o usuário local e a senha definidos aqui, para não usar senha de produção.
7. Como desenvolvedor da Fixar, quero cair dentro da empresa de validação ao entrar, para o trabalho local acontecer nela.
8. Como desenvolvedor da Fixar, quero que uma máquina com o volume do banco vazio suba a empresa de validação, o usuário local e as permissões, para o login não depender de uma leitura da produção.
9. Como desenvolvedor da Fixar, quero subir a base sem credencial de produção, para a subida não pedir senha do servidor de produção.
10. Como desenvolvedor da Fixar, quero que a subida não conecte na produção, para o trabalho local não depender de quem está operando lá.
11. Como desenvolvedor da Fixar, quero ver o usuário local na tela de usuários, para reconhecer com quem entrei.
12. Como desenvolvedor da Fixar, quero ver a empresa de validação na tela de empresa, para reconhecer onde entrei.
13. Como desenvolvedor da Fixar, quero orçamento, pedido e nota no C.R.M., para o perfil local alcançar esses módulos.
14. Como desenvolvedor da Fixar, quero o perfil local abrindo as telas do Hardness, para a permissão não bloquear o produto.
15. Como desenvolvedor da Fixar, quero o menu do Hardness, para ver o produto como ele é entregue.
16. Como desenvolvedor da Fixar, quero orçamento, pedido e boleto fora do menu, para o item comentado no Hardness continuar comentado.
17. Como desenvolvedor da Fixar, quero abrir orçamento, pedido e boleto pelo endereço do produto, para o perfil local alcançar o módulo mesmo fora do menu.
18. Como desenvolvedor da Fixar, quero que a personalização de orçamento, pedido, boleto e histórico de produto não execute, para a tela ser a do Hardness.
19. Como desenvolvedor da Fixar, quero que a totalização e o reprocesso de estoque da Fixar não executem, para a personalização gravada à parte ficar inerte.
20. Como desenvolvedor da Fixar, quero que personalização de outro cliente não apareça, para a base ser o Hardness da Fixar.
21. Como desenvolvedor da Fixar, quero grade vazia ao abrir uma tela sem movimento, para o vazio ser sucesso.
22. Como desenvolvedor da Fixar, quero a base sem cliente, produto, orçamento, pedido, nota e título, para o trabalho não depender de movimento real.
23. Como desenvolvedor da Fixar, quero que as rotinas que as telas chamam existam no banco que nasce, para a grade não quebrar numa função ausente.
24. Como desenvolvedor da Fixar, quero que login e abertura de tela não abram conexão com a receita, com banco, com e-mail nem com o fornecedor, para a base continuar isolada.
25. Como desenvolvedor da Fixar, quero que a subida não consulte boleto nem telemetria do fornecedor, para o login não depender dele.
26. Como desenvolvedor da Fixar, quero que um clique de emitir nota não chegue à receita, para uma exploração não gerar nota real.
27. Como desenvolvedor da Fixar, quero que um clique de registrar boleto não chegue ao banco, para uma exploração não registrar cobrança.
28. Como desenvolvedor da Fixar, quero que um clique de enviar e-mail não saia da máquina, para a base não escrever para cliente nem fornecedor.
29. Como desenvolvedor da Fixar, quero entrar por `/login`, para o endereço de entrada continuar o de hoje.
30. Como desenvolvedor da Fixar, quero o Hardness sob `/hardness3/`, para o prefixo do produto continuar o que as telas já usam.
31. Como desenvolvedor da Fixar, quero que um volume que já tem a base não seja apagado ao subir de novo, para o ensaio local permanecer.
32. Como desenvolvedor da Fixar, quero que o banco local continue depois de reiniciar o contêiner, para não recriar a fotografia quando o volume já tem a base.
33. Como desenvolvedor da Fixar, quero uma falha explícita quando o banco local não está pronto, para não confundir isso com uma tela em branco.
34. Como desenvolvedor da Fixar, quero uma mensagem clara quando a senha está errada, para saber que continuei deslogado.
35. Como desenvolvedor da Fixar, quero que um atalho de URL não me entre como administrador sem senha, para a base não herdar essa porta.
36. Como desenvolvedor da Fixar, quero sair da sessão e voltar ao login, para encerrar o acesso local.
37. Como desenvolvedor da Fixar, quero que senha, certificado e token da empresa não sejam utilizáveis, para a fotografia local não carregar credencial.
38. Como desenvolvedor da Fixar, quero que documento de cliente e de funcionário fique fora do que versionamos, para dado de pessoa não entrar no repositório.
39. Como desenvolvedor da Fixar, quero que pasta de outro cliente fique fora do Hardness que editamos, para o repositório ser só da Fixar.
40. Como desenvolvedor da Fixar, quero cache e arquivo de uso fora do que versionamos, para a subida de cada máquina não brigar com a da outra.
41. Como desenvolvedor da Fixar, quero a configuração de conexão local fora do que versionamos, para cada máquina apontar o banco local sem gravar isso no histórico.
42. Como desenvolvedor da Fixar, quero a referência do legado intacta e só de leitura, para consultar o comportamento de origem sem alterá-lo.
43. Como segundo desenvolvedor da Fixar, quero repetir a subida na minha máquina sem credencial de produção, para ver a mesma base.
44. Como desenvolvedor da Fixar, quero que a importação dos dados de produção não aconteça nesta subida, para a base continuar limpa até a melhoria que a pedir.

## Implementation Decisions

- O contêiner serve o Hardness versionado. A cópia gerada deixa de ser uma segunda árvore do produto. O que está commitado é o Hardness que já sobe: PHP 8.3, isolamento, menu do produto e personalização fora.
- A referência só de leitura continua fora do git e intocada. Quem herda um comportamento cita o arquivo de origem.
- Ficam fora do git segredo, cache, arquivo de uso, mídia pesada, pasta de outro cliente, documento de cliente e de funcionário, e a configuração local de conexão.
- A preparação deixa de recriar o produto. O que resta é gravar a configuração local de conexão e os diretórios de cache.
- O banco inicial é uma fotografia do MariaDB local desta máquina. Traz a estrutura, inclusive as rotinas que as telas chamam, a empresa de validação, o usuário local e as permissões. Não traz cliente, produto, orçamento, pedido, nota nem título. Senha, certificado e token não ficam utilizáveis.
- A subida restaura a fotografia só quando o volume está vazio. Um volume que já tem a base permanece. A subida não abre conexão com a produção. O importador que lia a produção sai do repositório.
- Os endereços continuam `/login` e `/hardness3/`. O produto continua se chamando Hardness.
- O comando que sobe a base e os testes HTTP que já existem continuam. O ADR 0002 permanece: sem personalização e sem retrato. O isolamento do ADR 0001 permanece.

## Testing Decisions

- Um bom teste observa comportamento de fora, pelo HTTP da base no ar. É o mesmo ponto já usado para entrar, ler o menu e abrir tela. Não confere caminho de arquivo, SQL da fotografia, lista do git, template nem função interna.
- O percurso novo é um só: subir com o volume do banco vazio e sem credencial de produção, entrar com o usuário local na empresa de validação, ver esse usuário, essa empresa e, no C.R.M., orçamento, pedido e nota, e sair. Durante o percurso, qualquer conexão com a produção, a receita, o banco, o e-mail ou o fornecedor reprova.
- A suíte que já cobre menu, personalização fora, isolamento e o percurso das telas continua passando no mesmo ponto HTTP.
- O precedente são os testes de entrar na empresa, de abrir o que a base escondia e de a base continuar sem falar com o mundo.

## Out of Scope

- Trocar o prefixo `/hardness3/` por `/erp/` ou por outro endereço público. Matar esse prefixo é uma melhoria seguinte.
- Renomear o produto. Hardness continua o nome.
- Construir a ferramenta que importa os dados de produção.
- Desenhar o modelo ideal da Fixar, reescrever tela ou trocar a linguagem do legado.
- Trazer cliente, produto, orçamento, pedido, nota ou título.
- Emitir nota de verdade, registrar boleto de verdade e enviar e-mail de verdade.
- Acompanhar release do fornecedor, revender o sistema ou atender outro cliente.

## Further Notes

- Vocabulário: empresa é o CNPJ que opera no ERP; empresa de validação é a empresa em que a base abre, sem cliente, produto, orçamento, pedido, nota nem título; cliente é quem compra da Fixar; fornecedor é quem vende para a Fixar; Hardness é o produto comprado, que a Fixar altera; base é o Hardness em execução na máquina da Fixar; personalização é a regra da Fixar gravada à parte; perfil é o conjunto de permissões de um usuário dentro de uma empresa.
- O ADR 0003 é a decisão desta spec. Ele substitui, no ADR 0001, o que entra no git e a leitura da produção ao nascer o banco. O ADR 0002 permanece.
- Onde a spec da base sem personalização discorda deste arquivo, vale o ADR 0003.
