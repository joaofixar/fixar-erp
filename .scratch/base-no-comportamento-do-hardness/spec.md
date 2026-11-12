Status: ready-for-agent
Orbit: ERPFX-18

# Base local no comportamento do Hardness

## Problem Statement

Na base local, três comportamentos do Hardness não acontecem. Gravar um aviso a partir da lista de empresas falha: a empresa vai vazia e o banco recusa a linha. Abrir o orçamento, e outras telas, falha quando a consulta chama uma função que a base não tem. Um parâmetro que já está na requisição não segue para a aba seguinte. Em produção da empresa esses caminhos seguem. Quem desenvolve não consegue conferir a tela.

## Solution

A base local passa a se comportar como o Hardness que as telas já chamam. O aviso grava mesmo sem empresa, e a empresa só entra quando a tela de origem indica uma. Um passo único cria as rotinas da lista do Hardness que ainda não existem, sem recriar as que a fotografia já trouxe e sem atualizar tabelas. O parâmetro que já está na requisição segue para a aba seguinte, com as mesmas exclusões do Hardness. A prova é um único script de percurso.

## User Stories

1. Como usuário da base, quero gravar um aviso a partir da lista de empresas sem escolher empresa, para o texto ficar registrado.
2. Como usuário da base, quero que esse gravar termine sem erro de banco, para eu ver o aviso salvo em vez de uma falha.
3. Como usuário da base, quero o aviso gravado sem empresa, para ele não ficar preso à empresa em que eu entrei.
4. Como usuário da base, quero que a empresa da sessão não seja carimbada nesse aviso, para a empresa de validação não virar dona de um aviso que a lista não indicou.
5. Como usuário da base, quero que o texto que digitei seja o texto gravado, para a tela inicial poder mostrá-lo.
6. Como usuário da base, quero o aviso ativo quando deixo o padrão, para ele poder aparecer.
7. Como usuário da base, quero gravar mesmo com a descrição em branco, para um texto sem descrição não ser recusado.
8. Como usuário da base, quero o mesmo gravar que já funciona na produção da empresa, para a base local não inventar outra regra.
9. Como desenvolvedor da Fixar, quero a empresa ausente gravada como ausência, para o banco não recusar a string vazia numa coluna inteira.
10. Como desenvolvedor da Fixar, quero que uma tela que indica a empresa grave essa empresa no aviso, para a origem ser respeitada.
11. Como desenvolvedor da Fixar, quero que editar um aviso que já tem empresa, a partir de uma tela que não indica empresa, deixe a empresa como está, para uma edição posterior não apague a origem.
12. Como desenvolvedor da Fixar, quero abrir o grid de orçamentos e ver o status resolvido, para a consulta não responder que a função não existe.
13. Como desenvolvedor da Fixar, quero o status pendente traduzido para o rótulo pendente, para saber que a rotina rodou e não só que ela existe.
14. Como desenvolvedor da Fixar, quero que qualquer tela que chame uma rotina do Hardness a encontre, para não corrigir uma tela de cada vez.
15. Como desenvolvedor da Fixar, quero um passo único para todas as rotinas ausentes, para função, procedure e trigger chegarem juntos.
16. Como desenvolvedor da Fixar, quero que as rotinas que a fotografia já trouxe permaneçam como estão, para o passo não trocar uma definição que já funciona.
17. Como desenvolvedor da Fixar, quero rodar o passo de novo sem efeito nas rotinas que já existem, para a repetição ser segura.
18. Como desenvolvedor da Fixar, quero o nome da rotina cujo arquivo falhou, para eu saber qual SQL olhar.
19. Como desenvolvedor da Fixar, quero que uma falha não interrompa o resto, para as outras rotinas ainda serem criadas.
20. Como desenvolvedor da Fixar, quero uma segunda tentativa das que falharam, para uma rotina que dependia de outra ainda ser criada.
21. Como desenvolvedor da Fixar, quero o passo no volume que já está no ar, para não apagar o banco só para ganhar as rotinas.
22. Como desenvolvedor da Fixar, quero o mesmo passo quando um volume nasce vazio, para a próxima máquina não nascer sem as rotinas.
23. Como desenvolvedor da Fixar, quero a fotografia continuando como estrutura e dados de validação, para o texto das rotinas não ser copiado para dentro dela.
24. Como desenvolvedor da Fixar, quero que a atualização de tabelas do Hardness não rode junto, para coluna, índice e configuração não mudarem com as rotinas.
25. Como desenvolvedor da Fixar, quero de fora os SQL que a lista do Hardness não chama, para o passo não instalar rotina que o próprio atualizador não aplica.
26. Como desenvolvedor da Fixar, quero as rotinas do Hardness, sem personalização no lugar, para a base continuar sem personalização.
27. Como desenvolvedor da Fixar, quero o gatilho da lista que a base ainda não tem, para a gravação que depende dele acompanhar o Hardness.
28. Como desenvolvedor da Fixar, quero a criação aceita mesmo quando a rotina não declara se é determinística, para o log binário do banco não barrar o passo.
29. Como desenvolvedor da Fixar, quero um identificador de empresa que já está na requisição na aba seguinte, para ele não sumir entre telas.
30. Como desenvolvedor da Fixar, quero ação, tabela e aba fora desse repasse numa aba normal, para a exclusão do Hardness permanecer.
31. Como desenvolvedor da Fixar, quero ação, tabela e aba incluídas quando a aba pede o repasse completo, para o caminho de personalização de aba continuar igual ao Hardness.
32. Como desenvolvedor da Fixar, quero identificadores de divisão, ajax e janela fora da query repassada, para eles não serem duplicados na aba seguinte.
33. Como desenvolvedor da Fixar, quero que um parâmetro que a própria aba já coloca não seja repetido, para o valor da origem prevalecer.
34. Como desenvolvedor da Fixar, quero que o corpo do formulário, quando chega como lista, não entre na query, para o PHP 8 não abortar o pedido.
35. Como desenvolvedor da Fixar, quero a biblioteca legível com esse repasse, para ler o código não apagar o comportamento do Hardness.
36. Como desenvolvedor da Fixar, quero o outro ajudante de parâmetros ainda ignorando o corpo do formulário e mantendo a ação, para o envio de formulário que já foi corrigido continuar de pé.
37. Como desenvolvedor da Fixar, quero uma única prova em script de percurso, no formato do que já mostra a base nascendo sem ler a produção, para não existir um segundo estilo de teste.
38. Como desenvolvedor da Fixar, quero essa prova na empresa de validação, para ela não depender de cliente, produto, orçamento, pedido, nota nem título.
39. Como desenvolvedor da Fixar, quero a prova vermelha se o status do orçamento não resolve, se uma rotina da fotografia foi recriada, se o aviso ganhou a empresa da sessão, ou se um parâmetro da requisição sumiu na aba seguinte, para a regressão aparecer.

## Implementation Decisions

- A empresa do aviso sai da requisição. Sem identificador, o campo não entra na gravação e a coluna fica nula. Com identificador, o campo entra com esse valor. A empresa da sessão não é consultada.
- Editar um aviso sem identificador na requisição não altera a empresa já gravada.
- O passo lê a lista de rotinas do atualizador do Hardness e o SQL que cada nome dessa lista aponta. Cria função, procedure e trigger que a base não tem. Não remove e não recria as que já existem.
- O passo não executa a parte do atualizador que cria coluna, índice e configuração.
- SQL presente na pasta e ausente da lista fica de fora.
- Não há personalização de rotina nesta base. A fonte é o SQL do Hardness.
- Um arquivo que falha é registrado com o nome e o passo continua. Ao fim, as que falharam são tentadas de novo, na ordem da lista, para cobrir dependência entre rotinas. O que continuar falhando permanece na lista.
- Rodar o passo duas vezes não muda a rotina que já estava.
- O passo entra no nascimento do banco, depois da fotografia, e também é executado no volume que já tem dados. A fotografia não é reexecutada nesse volume.
- A criação liga a permissão do banco para rotina sem declaração de determinismo. Isso só importa na hora de criar.
- O repasse da requisição tem dois modos. No modo normal ficam de fora ação, tabela, aba, e também os identificadores de divisão, ajax e janela. No modo completo, ação, tabela e aba entram; os identificadores de divisão, ajax e janela continuam de fora.
- Valor que não é escalar não entra na query. Valor que a query da aba já contém não é repetido. Cada parâmetro novo é prefixado.
- A biblioteca geral permanece legível. O ramo do repasse que a reconstrução tinha esvaziado volta com o comportamento acima.
- O ajudante que monta a string de parâmetros do pedido continua ignorando o corpo do formulário e preservando a ação.
- A decisão de onde as rotinas nascem já está no ADR das rotinas ausentes. Esta spec não reabre essa escolha. Ela também não reabre o ADR da base sem personalização.

## Testing Decisions

- Um bom teste olha o que a pessoa e a tela observam: a rotina responde, a linha do aviso fica sem empresa, o parâmetro chega na aba seguinte. Não afirma detalhe interno de como o SQL foi montado.
- Uma costura só. É um script de percurso, no formato do que já sobe um projeto descartável, entra na empresa de validação e consulta o banco. Não há teste de função isolada nem teste dentro do PHP do Hardness.
- O percurso roda o passo das rotinas nessa base. O status pendente do orçamento volta como pendente. As rotinas que a fotografia já trouxe continuam com a mesma definição de antes do passo.
- O percurso grava um aviso a partir da lista de empresas. A linha existe e a empresa dela está vazia.
- O percurso coloca um parâmetro na requisição, diferente de ação, tabela e aba, e vê esse parâmetro na aba seguinte. Ação não aparece nesse repasse normal.
- O percurso inclui um arquivo de rotina que falha. O nome aparece na lista e as outras rotinas ainda são criadas.
- A prova usa a empresa de validação e não precisa de cliente, produto, orçamento, pedido, nota nem título.

## Out of Scope

- Rodar o atualizador completo do Hardness, com coluna, índice e configuração.
- Recriar as rotinas que a fotografia já trouxe.
- Instalar os SQL que a lista do Hardness não nomeia.
- Carimbar a empresa da sessão no aviso.
- Mudar quem vê o aviso na tela inicial.
- Trazer personalização de orçamento, pedido, boleto, histórico de produto ou totalização para a execução.
- Reescrever outras funções da biblioteca legível que ainda tenham ramo vazio, entre elas relacionamento de tabela, junção de códigos, troca de empresa e visibilidade de aba personalizada.
- Importar dados de produção.

## Further Notes

- O ADR das rotinas ausentes corrige o trecho do ADR da fotografia que dizia que ela traz as rotinas que as telas chamam. A fotografia segue como estrutura e dados de validação.
- Parte do comportamento já está na árvore de trabalho: o aviso sem empresa, o repasse da requisição e o passo das rotinas, com o gancho para o volume novo. O volume que já está no ar ainda não recebeu as rotinas, porque o motor de contêiner não respondeu na hora de executá-lo. A prova em script de percurso ainda não existe. O próximo trabalho fecha essa prova e executa o passo no volume atual, sem reabrir as decisões desta spec.
