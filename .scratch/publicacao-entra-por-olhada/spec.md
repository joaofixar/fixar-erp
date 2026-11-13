Status: ready-for-agent
Orbit: ERPFX-22

# A publicação entra por olhada e por merge

## Problem Statement

O Hardness versionado saiu da cópia em sistema-legado. A publicação ficou sem busca desde então. O pull seguinte trouxe mais de 600 commits no master. Quem desenvolve não tem um jeito de ver esse vão, nem de trazer o que a publicação mudou, sem copiar a pasta por cima e sem apagar o que a Fixar já tirou ou já ajustou.

## Solution

Quem desenvolve aciona duas ações, e nada dispara sozinho. A olhada busca a publicação e lista os commits depois do SHA gravado. Não altera o Hardness versionado, o checkout, a base nem o banco. O trazer faz um merge de três vias numa branch nova, sem trocar o checkout. Conflito para e não avança o SHA. Merge limpo avança o SHA. Personalização, segredo e arquivo de outro cliente ficam de fora. Coluna, índice e configuração não entram. A prova é um único script em volta do comando.

## User Stories

1. Como desenvolvedor da Fixar, quero acionar uma olhada, para ver se a publicação andou desde o ponto de parada.
2. Como desenvolvedor da Fixar, quero a lista dos commits da publicação depois do SHA gravado, para saber o que chegou.
3. Como desenvolvedor da Fixar, quero essa lista do mais antigo ao mais novo, para ler o vão na ordem em que a publicação o publicou.
4. Como desenvolvedor da Fixar, quero cada commit com o identificador e o assunto, para eu reconhecer a atualização sem abrir o diff inteiro.
5. Como desenvolvedor da Fixar, quero rodar a olhada de novo sem efeito, para o uso diário ser seguro.
6. Como desenvolvedor da Fixar, quero que a olhada não altere o Hardness versionado, para a base continuar no código do checkout atual.
7. Como desenvolvedor da Fixar, quero que a olhada não troque o checkout, para a base montada nesse código não mudar.
8. Como desenvolvedor da Fixar, quero que a olhada não fale com o banco, para coluna e rotina não mudarem num simples olhar.
9. Como desenvolvedor da Fixar, quero que a olhada busque a publicação remota, para a lista não ficar num master local atrasado.
10. Como desenvolvedor da Fixar, quero que essa busca não apague commit local no master do clone, para um trabalho não enviado continuar lá.
11. Como desenvolvedor da Fixar, quero que o clone permaneça em master, para o checkout da publicação não mude de branch.
12. Como desenvolvedor da Fixar, quero a olhada falhar de forma visível se a publicação não responder, para eu não concluir que não houve atualização.
13. Como desenvolvedor da Fixar, quero uma olhada vazia e bem-sucedida quando a publicação não andou, para eu saber que já estou no ponto gravado.
14. Como desenvolvedor da Fixar, quero a primeira olhada, sem SHA gravado, mostrando candidatos, para eu reconhecer o ponto da cópia do legado.
15. Como desenvolvedor da Fixar, quero que essa primeira olhada não copie arquivo, para os mais de 600 commits não entrarem enquanto eu escolho o SHA.
16. Como desenvolvedor da Fixar, quero informar o SHA escolhido e vê-lo gravado, para a próxima olhada partir dali.
17. Como desenvolvedor da Fixar, quero que gravar o SHA não copie arquivo, para marcar o ponto de parada sem trazer a publicação.
18. Como desenvolvedor da Fixar, quero o SHA no repositório da Fixar, para outra máquina ver o mesmo ponto de parada.
19. Como desenvolvedor da Fixar, quero acionar o trazer, para a publicação desde o SHA entrar no Hardness versionado.
20. Como desenvolvedor da Fixar, quero esse vão inteiro num único merge, para não aplicar commit por commit.
21. Como desenvolvedor da Fixar, quero o trazer numa branch nova, para revisar antes da base servir o código.
22. Como desenvolvedor da Fixar, quero que o trazer não troque o checkout, para a base em execução permanecer no código atual.
23. Como desenvolvedor da Fixar, quero que o que só a publicação mudou entre no resultado, para eu receber a atualização.
24. Como desenvolvedor da Fixar, quero que o que só a Fixar mudou permaneça, para o ajuste local não ser apagado.
25. Como desenvolvedor da Fixar, quero que um arquivo mexido dos dois lados pare o trazer, para eu decidir o conflito.
26. Como desenvolvedor da Fixar, quero ver quais arquivos conflitam, para saber onde olhar.
27. Como desenvolvedor da Fixar, quero que o conflito não avance o SHA, para a próxima olhada ainda mostrar o mesmo vão.
28. Como desenvolvedor da Fixar, quero que o conflito não deixe a árvore de trabalho no meio do merge, para a base não servir arquivo pela metade.
29. Como desenvolvedor da Fixar, quero que um trazer limpo avance o SHA até a ponta que entrou, para a olhada seguinte não repetir o que já entrou.
30. Como desenvolvedor da Fixar, quero a personalização de fora do resultado, para a base continuar sem personalização.
31. Como desenvolvedor da Fixar, quero segredo e arquivo de outro cliente de fora, para a publicação não reintroduzir o que o repositório já exclui.
32. Como desenvolvedor da Fixar, quero que o trazer não rode o atualizador de tabelas, para coluna, índice e configuração não mudarem.
33. Como desenvolvedor da Fixar, quero que o texto de rotina possa entrar na branch, para a lista do Hardness acompanhar a publicação.
34. Como desenvolvedor da Fixar, quero que esse texto não seja aplicado no banco no trazer, para a base só ganhar rotina depois que eu integrar e o contêiner subir.
35. Como desenvolvedor da Fixar, quero que nada disso rode sozinho, para eu escolher o dia de olhar e o dia de trazer.
36. Como desenvolvedor da Fixar, quero a primeira olhada, depois do SHA da cópia do legado, listando o vão de mais de 600 commits, para eu ver o tamanho antes de trazer.

## Implementation Decisions

- Há duas ações no mesmo comando: olhar e trazer. Nada é agendado.
- A olhada faz a busca no clone da publicação, que permanece em master, e lê a ponta remota. Não reinicia o master local e não troca de branch.
- O ponto de parada é um SHA da publicação gravado neste repositório.
- Sem SHA, a olhada compara a árvore do Hardness versionado com a publicação, ignorando o que a Fixar já exclui e o que só ela alterou. Mostra os commits mais próximos e para. Não copia.
- Informar um SHA grava esse ponto e não copia.
- O trazer é um merge de três vias. A base da comparação é a publicação no SHA gravado. Um lado é o Hardness versionado atual. O outro é a publicação na ponta pedida, que por padrão é a ponta remota.
- O resultado é um commit numa branch nova. O checkout atual não muda. A base em execução não passa a servir esse código até a branch ser integrada.
- Onde só a publicação mudou, entra a mudança. Onde só a Fixar mudou, fica o que já está. Onde os dois mudaram, o comando lista os arquivos, não deixa a árvore de trabalho no meio do merge e não avança o SHA.
- Merge limpo, com as exclusões reaplicadas, avança o SHA até a ponta que entrou.
- Ficam de fora a personalização, segredo, arquivo de outro cliente e o que o repositório já exclui do Hardness. O atualizador de tabelas não roda. Coluna, índice e configuração não mudam. Texto de rotina pode entrar na branch e não é aplicado no banco por este comando.
- A falha de busca da publicação faz o comando sair com erro. Não imprime uma lista vazia de sucesso.

## Testing Decisions

- Um bom teste olha o que quem desenvolve observa ao acionar o comando: a lista de commits, o SHA gravado, a branch nova, os arquivos em conflito, e o que não mudou. Não afirma o algoritmo interno do merge.
- Uma costura só. É um script de percurso no formato dos que já existem, em volta do mesmo comando. A publicação do teste é um repositório descartável, sem rede e sem o clone real. A base não sobe. O banco não é consultado.
- A olhada com SHA lista os commits posteriores e deixa o Hardness versionado e o checkout como estavam.
- Sem SHA, o script vê candidatos e confere que nenhum arquivo do Hardness versionado mudou.
- Gravar um SHA não copia arquivo.
- O trazer limpo deixa uma branch nova, não troca o checkout e avança o SHA. Uma olhada seguinte, com a publicação parada, sai vazia e bem-sucedida.
- O trazer com um arquivo mexido dos dois lados lista o conflito, não avança o SHA e não deixa a árvore de trabalho no meio do merge.
- Um arquivo de personalização, segredo ou outro cliente que a publicação tenha alterado não aparece no resultado.
- Busca que falha faz o script reprovar, e não conta como “não houve atualização”.

## Out of Scope

- Agendar a olhada ou o trazer.
- Rodar o atualizador de tabelas, com coluna, índice e configuração.
- Aplicar rotina no banco durante o trazer.
- Trocar o checkout ou reiniciar a base.
- Resolver conflito sozinho.
- Importar dados de produção.
- Trazer branch de cliente da publicação.

## Further Notes

- A decisão está no ADR da publicação. O glossário chama de publicação o Hardness na linha master do vendedor.
- O clone real fica no WSL, em master. O comando usa esse clone quando ninguém indica outro. O teste usa um repositório descartável.
- O primeiro vão real, no dia desta spec, é o de mais de 600 commits desde a cópia que virou o legado local. A spec não traz esse vão. Ela deixa o comando capaz de listá-lo e, quando acionado, de mergeá-lo.
