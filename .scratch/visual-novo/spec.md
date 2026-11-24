Status: ready-for-agent
Orbit: ERPFX-44

# O desktop logado usa o visual novo

## Problem Statement

Quem opera o FX ERP passa o dia no desktop do Hardness: o menu no alto, os atalhos Alt+letra, o rodapé com empresa, usuário, banco e o HTML livre da empresa, a marca Hardness, três temas e a área inicial repartida entre certificado, cobrança e a aba Início do B.I.

Quem desenvolve quer a cara nova na base local, numa branch separada do master, para validar antes de integrar. O master segue com o desktop atual até essa validação.

## Solution

O desktop logado passa a ser o shell e o miolo no tema único. A sidebar leva a marca FX ERP, a empresa e o menu do perfil. O topo leva a lupa, o sino e o avatar. A faixa de abas fica sobre a área de trabalho, uma aba visível. Ctrl+K e a lupa abrem a paleta no centro, e ela pesquisa o menu. Com a faixa vazia, a área mostra o certificado quando existe, o Aviso e a Nota. O miolo muda pelo tema, sem reescrever cada tela.

A construção fica na branch `visual-novo`, nascida do master. A base local sobe nessa branch. O percurso no navegador valida o desktop aí. O merge no master espera essa validação.

## User Stories

1. Como usuário, quero entrar no FX ERP e ver a marca FX ERP no alto da sidebar, para reconhecer o sistema sem confundir com uma empresa do grupo.
2. Como usuário, quero a sidebar à esquerda com os grupos do menu, o topo com lupa, sino e avatar, e a faixa de abas sobre a área de trabalho, para operar o dia inteiro nesse desktop.
3. Como usuário, quero a área de trabalho no restante da tela, para o miolo ocupar o lugar de sempre.
4. Como usuário, quero a empresa atual logo abaixo da marca, sempre visível, para saber onde estou.
5. Como usuário, quero o controle fechado com o nome abreviado da empresa e o nome completo no título, para a sidebar caber e o nome inteiro continuar à mão.
6. Como usuário com uma empresa só, quero a empresa como rótulo, para não abrir uma lista de um item.
7. Como usuário com mais de uma empresa, quero o mesmo controle abrir a lista, cada item com o nome completo, para escolher a empresa certa.
8. Como usuário, quero que a troca de empresa recarregue o menu, feche as janelas e atualize a área de trabalho, para o desktop passar a ser o da empresa escolhida.
9. Como usuário, quero o avatar do topo abrir o meu menu, para achar login, perfil, densidade e sair no mesmo lugar.
10. Como usuário, quero ver o login e, abaixo, o nome do perfil, para reconhecer com qual acesso entrei.
11. Como usuário, quero o nome da pessoa fora do shell, para o desktop mostrar o acesso e não a pessoa.
12. Como usuário cujo perfil permite trocar de usuário, quero essa troca no menu do avatar, com o mesmo efeito da troca de empresa, para mudar de acesso sem sair do desktop.
13. Como usuário cujo perfil não permite trocar de usuário, quero a lista de usuários ausente, para o menu não oferecer o que não posso.
14. Como usuário, quero Sair como último item do avatar, indo para a saída da sessão, para encerrar o acesso.
15. Como usuário, quero o shell sem o nome do banco, para a tela não mostrar a base.
16. Como usuário, quero o shell sem o HTML livre da empresa, para a sidebar não desenhar conteúdo solto.
17. Como usuário, quero o menu do perfil na sidebar, na ordem do menu, para abrir o que já posso abrir.
18. Como usuário, quero o nome do grupo só como título, para o grupo organizar e o item abrir.
19. Como usuário, quero Ctrl+K e a lupa abrirem a mesma paleta no centro da área de trabalho, para achar um item sem percorrer a sidebar.
20. Como usuário, quero a paleta abrir com o campo vazio, o cursor no campo e o destaque no primeiro item, para eu digitar ou confirmar na hora.
21. Como usuário, quero a paleta listar os mesmos grupos e itens da sidebar, na ordem do menu, para a busca e o menu serem a mesma lista.
22. Como usuário, quero que digitar deixe os itens em que o nome do item ou o grupo contém o texto, sem diferenciar maiúsculas, acentos ou pontos, para achar do jeito que eu lembro.
23. Como usuário, quero a linha "Não há item" quando nada casa, sem destaque, e que Enter não abra nada, para eu saber que a busca falhou.
24. Como usuário, quero as setas movendo o destaque de item em item, para escolher sem o mouse.
25. Como usuário, quero Enter ou um clique abrindo a mesma aba ou janela que o menu abre, e fechando a paleta, para a escolha me levar direto à tela.
26. Como usuário, quero Esc, Ctrl+K de novo ou um clique fora fechando a paleta sem abrir nada, para desistir da busca.
27. Como usuário, quero cliente, produto, nota e pedido continuarem sendo achados dentro das telas deles, para a paleta não virar busca de registro.
28. Como usuário, quero a paleta sem bloco de recentes quando o campo está vazio, para a lista ser o menu.
29. Como usuário, quero a paleta sem ações da aba aberta, para fechar e reordenar ficarem na faixa e o miolo ficar na tela.
30. Como usuário, quero os atalhos Alt+letra, setas, Enter e Esc do menu fora do desktop, para a navegação rápida ser a paleta.
31. Como usuário, quero a tela de módulo (C.R.M., estoque, nota) como aba, para várias ficarem abertas e uma ficar visível.
32. Como usuário, quero o formulário de inclusão como janela sobre a aba de onde saiu, para preencher sem perder a lista.
33. Como usuário, quero a faixa listando as telas que ocupam a área, para ver o que está aberto.
34. Como usuário, quero o miolo de cada aba igual ao conteúdo de hoje, para a tela de módulo continuar a mesma por dentro.
35. Como usuário, quero as abas internas do C.R.M. dentro da área de trabalho, para Orçamentos, Pedidos e o restante seguirem na tela.
36. Como usuário, quero a faixa do shell fora da área de trabalho, para ela não ser a aba interna do módulo.
37. Como usuário, quero que trocar de aba esconda o miolo e que voltar mostre o mesmo, nas mesmas abas internas, para o trabalho de cada tela permanecer.
38. Como usuário, quero fechar cada aba sem pergunta, mesmo com inclusão preenchida, para tirar a tela na hora.
39. Como usuário, quero a pergunta de algumas janelas no Esc continuando a ser o Esc da própria janela, para o fechar da aba não tomar o lugar dessa pergunta.
40. Como usuário, quero que fechar a aba visível mostre a da direita, ou a da esquerda se ela era a última, para eu cair numa tela que ainda está aberta.
41. Como usuário, quero a faixa poder ficar vazia, para eu voltar à área de trabalho.
42. Como usuário, quero que fechar uma aba que não está visível deixe a visível onde está, para o fechar não trocar a tela em que estou.
43. Como usuário, quero arrastar uma aba e mudar a ordem na sessão, para deixar lado a lado o que estou usando.
44. Como usuário, quero uma aba nova no fim da faixa, para o que acabei de abrir ser o último.
45. Como usuário, quero que abrir de novo um item já aberto mostre o que já está aberto, no lugar em que está, para não duplicar a tela.
46. Como usuário, quero login novo ou recarregar a página com a faixa vazia, para a sessão não guardar aba.
47. Como usuário, quero que trocar de empresa ou de usuário feche as abas e as janelas, inclusive a janela que estava sozinha, para o acesso novo começar limpo.
48. Como usuário, quero várias janelas empilhadas sobre a aba visível, para abrir mais de um formulário.
49. Como usuário, quero que trocar de aba esconda a pilha e que voltar mostre a mesma pilha, na mesma ordem, para os formulários acompanharem a aba.
50. Como usuário, quero que fechar a aba feche a pilha junto, para o formulário não ficar solto.
51. Como usuário, quero Cliente/Fornecedor, com a faixa vazia, como janela e não como aba, para o cadastro flutuar sobre a área.
52. Como usuário, quero o fechar dessa janela sozinha valendo, para eu dispensá-la.
53. Como usuário, quero que abrir o C.R.M., o Estoque ou outra tela que ocupa a área feche essa janela e o que estiver empilhado nela, para a aba ficar visível e a janela não voltar.
54. Como usuário, quero a faixa rolando na horizontal quando as abas não cabem, com a aba visível à vista, para alcançar qualquer aba aberta.
55. Como usuário, quero a faixa vazia no login mostrando, nesta ordem, o cartão do certificado quando ele existe, os Avisos e as Notas da mais nova à mais antiga, para ver o que importa antes de abrir um módulo.
56. Como usuário, quero uma aba visível tirando certificado, Aviso e Nota, para o módulo ocupar a área.
57. Como usuário, quero a faixa vazia trazendo os três de volta, para fechar a última aba devolver a área de trabalho.
58. Como usuário, quero a área sem um bloco vazio de Nota quando não há Nota publicada, para o vazio não reservar espaço.
59. Como usuário com alçada 4 ou com as permissões 20 e 36, quero o cartão do certificado quando a NF-e A1 está perto de vencer ou vencida, para agir antes de a nota parar.
60. Como usuário, quero o diálogo do certificado por cima quando ele está vencido, vence hoje, vence amanhã ou está dentro do prazo mais curto, para o aviso não depender de eu olhar o cartão.
61. Como usuário, quero a senha continuando como janela, para o aviso de senha não virar cartão da área.
62. Como usuário, quero a versão fora da área de trabalho e fora do shell, para o desktop não anunciar o arquivo de versão.
63. Como usuário, quero a cobrança da Hardness fora do desktop, cartões, boleto de parceiro e o redirecionamento que troca a página, para a área não ser uma tela de suspensão.
64. Como usuário, quero a lista "Últimas atualizações" fora da área, para o lugar ficar com Aviso e Nota.
65. Como usuário, quero as notificações de vídeo da Hardness sem virar Nota, para a Nota ser texto da Fixar.
66. Como usuário, quero o logo de fundo fora da área, para o fundo ser chapado.
67. Como usuário, quero o Aviso só na área de trabalho, filtrado por perfil, empresa e usuário, para ler o texto que me cabe.
68. Como usuário, quero a aba Início do B.I. deixando de ser a tela inicial, para o cockpit abrir direto nos dashboards.
69. Como usuário, quero cada Nota com data, título e texto, sem vídeo, da mais nova à mais antiga, para ler a mudança do FX ERP.
70. Como usuário logado, quero ver toda Nota publicada, para a Nota não depender do perfil.
71. Como usuário, quero o sino abrir um pop pendurado no ícone, intitulado Alertas, para ler o que não li.
72. Como usuário, quero um pop por vez, para abrir a empresa, o avatar ou a paleta fechar o pop do sino.
73. Como usuário, quero o sino de novo, Esc ou um clique fora fechando o pop, para dispensá-lo.
74. Como usuário, quero que fechar o pop descarte o rascunho de enviar mensagem, e que abrir de novo comece vazio, para o texto não voltar pela metade.
75. Como usuário, quero cada item do sino como Alerta, para o Aviso continuar na área de trabalho.
76. Como usuário, quero enviar mensagem como a ação de escrever a outro usuário, para o pop continuar servindo a isso.
77. Como usuário, quero a lista com o Alerta não lido que o perfil pode ver, agrupado pelo nome abreviado da empresa, com a contagem do que não está oculto, para ver o que é meu.
78. Como usuário, quero ocultar, atualizar e enviar no pop, para tratar a lista sem sair do sino.
79. Como usuário com permissão de suporte, quero o suporte no pop, para alcançar o que o perfil já alcança.
80. Como usuário sem permissão de suporte, quero o suporte fora do pop, para o sino não oferecer o que não posso.
81. Como usuário, quero o pop vazio dizendo o que o Hardness já diz quando não há alerta, para eu reconhecer que não há nada a ler.
82. Como usuário, quero que clicar num Alerta da empresa atual execute a ação de hoje, tire o item da lista e da contagem e feche o pop, para o alerta me levar à tela e sair da fila.
83. Como usuário, quero que um Alerta de outra empresa não abra, para eu não cair numa empresa que não é a atual.
84. Como usuário root, no critério de hoje (usuário 63 ou usuário de suporte), quero a Loja de apps no grupo Configuração, para abrir o catálogo que a Hardness publica.
85. Como usuário com perfil Diretor, quero a Loja de apps fora do menu, para o catálogo continuar só com o root.
86. Como usuário root, quero a janela da loja com os apps de versão de produção e as ações Obter, Instalar e Atualizar, para instalar o que a Hardness publica.
87. Como usuário, quero a nuvem fora do topo, para a loja ser alcançada pela sidebar e pela paleta.
88. Como usuário, quero o item de criar app fora do menu do shell, para o menu não oferecer essa criação.
89. Como usuário, quero o desktop sem o selo Modo IDE e sem o selo Editando App, para a sessão de edição não aparecer no shell.
90. Como usuário, quero um grupo Apps no menu, depois de Gerencial e antes de Outros, para achar os atalhos que estavam na aba Início.
91. Como usuário, quero esse grupo na ordem Integrações, Conexões MCP, Webhooks, Apontamento PCP, GNRE, Reforma Tributária, Dashboard e Painel TV, para achar cada um no lugar combinado.
92. Como usuário, quero cada um desses itens abrindo o mesmo endereço de hoje, numa aba nova do navegador, com a mesma permissão, para o atalho continuar o mesmo destino.
93. Como usuário, quero esses itens sem virar aba do desktop, para a faixa não misturar página externa com tela de módulo.
94. Como usuário logado, quero ver Reforma Tributária e, por isso, o grupo Apps, para o grupo existir mesmo quando os outros itens não são meus.
95. Como usuário, quero a paleta alcançando o grupo Apps e a Loja de apps, porque os dois estão no menu, para a busca achar o que a sidebar mostra.
96. Como usuário, quero um tema só no desktop, para a cara não depender de trocar tema claro, tema escuro ou tema 2021.
97. Como usuário, quero o shell e o miolo com as mesmas cores, para a sidebar e a grade serem o mesmo desktop.
98. Como usuário, quero o cromado quieto e a cor no status, no erro, na seleção e no acento, para a tela não pintar tudo.
99. Como usuário, quero o hover da linha da grade no amarelo de hoje, para reconhecer a linha sob o mouse.
100. Como usuário, quero o fundo da área chapado, sem o gradiente cinza e sem a imagem de fundo, para a área descansar o olho.
101. Como usuário, quero a face Segoe UI, para o tipo do shell e do miolo ser o mesmo.
102. Como usuário, quero a sidebar e o título da janela na mesma cor escura, com o texto claro, para a janela parecer do mesmo desktop.
103. Como usuário, quero o erro do formulário e o erro do miolo na mesma cor, para o erro ser um só.
104. Como usuário, quero a célula de status e o Aviso nas cores de hoje, para o verde, o amarelo, o vermelho e o azul continuarem dizendo o que já dizem.
105. Como usuário, quero o desenho do ícone no sprite de hoje e a cor do ícone vinda do tema, para o ícone mudar de cor sem trocar de desenho.
106. Como usuário que ainda não escolheu densidade, quero o passo Pequeno, para o desktop abrir em 12.
107. Como usuário que já está em Médio ou Grande, quero continuar nesse passo, para a minha escolha valer.
108. Como usuário, quero Pequeno, Médio e Grande no menu do avatar, entre o perfil e Sair, para mudar o passo sem abrir outra tela.
109. Como usuário, quero subir e voltar de passo, com o passo atual marcado, para experimentar e desfazer.
110. Como usuário, quero a escolha gravada, para o próximo login abrir no passo que deixei.
111. Como usuário, quero o passo mudando tipo e espaçamento do miolo, para a grade, o campo e a aba acompanharem a escolha.
112. Como usuário, quero a tela de configuração da interface continuando a gravar o mesmo passo, para os dois lugares não divergirem.
113. Como desenvolvedor da Fixar, quero o visual novo na branch `visual-novo`, nascida do master, para a base local servir essa branch.
114. Como desenvolvedor da Fixar, quero o master com o desktop atual até o merge, para quem está na master não cair no visual ainda em prova.
115. Como desenvolvedor da Fixar, quero validar o desktop no navegador, nessa base, antes do merge, para integrar o que já vi funcionando.
116. Como desenvolvedor da Fixar, quero o shell em arquivos que a publicação não envia, para o próximo trazer não apagar a cara nova.
117. Como desenvolvedor da Fixar, quero o template do desktop só com o gancho, reaplicado depois do trazer com sobrescrita, para a publicação poder reenviar o template e o desktop continuar o novo.
118. Como desenvolvedor da Fixar, quero o trazer parando quando a âncora do gancho sumiu, para a publicação não entrar pela metade.
119. Como usuário, quero a grade e o formulário mudando pelo tema, para cada tela não ser reescrita.

## Implementation Decisions

- A construção acontece na branch `visual-novo`, criada a partir do master. A base local que valida é o checkout dessa branch. O master permanece no desktop atual. O merge espera o percurso no navegador passar nessa base e fica fora da construção.
- O shell é a barra lateral, o topo (lupa, sino e avatar) e a faixa de abas. O trilho e a paleta no lugar da sidebar ficam de fora. A marca é FX ERP. A área de trabalho continua o lugar do miolo.
- A aba é a tela de módulo que ocupa a área. Várias ficam abertas, uma visível. O formulário de inclusão continua janela sobre a aba de onde saiu. Cliente/Fornecedor com a faixa vazia é janela. Abrir uma tela que ocupa a área fecha essa janela e a pilha dela.
- A faixa fica fora da área de trabalho e não é o widget de abas que o módulo já consulta na página inteira. Trocar de aba esconde o miolo e a pilha de janelas. Voltar restaura os dois. Fechar não pergunta. A ordem arrastada dura a sessão. Login novo e recarregar abrem a faixa vazia. Troca de empresa ou de usuário fecha abas e janelas.
- A empresa fica sob a marca. Uma empresa é rótulo. Mais de uma abre a lista pelo nome completo. A troca recarrega o menu, fecha as janelas e atualiza a área. O avatar mostra login e perfil, a troca de usuário só com permissão, a densidade e Sair. O shell não mostra o banco nem o HTML livre da empresa.
- A paleta e a sidebar listam o menu do perfil, na ordem do menu. Digitar filtra por item ou grupo, sem diferenciar maiúsculas, acentos ou pontos. Escolher abre a mesma aba ou janela e fecha a paleta. Sem recentes e sem ações da aba. Os atalhos Alt+letra, setas, Enter e Esc do menu saem.
- O sino abre o pop Alertas, um pop por vez, com o comportamento do Hardness: Alerta não lido que o perfil pode ver, agrupado pela empresa, ocultar, atualizar, enviar e, com permissão, o suporte. Alerta de outra empresa não abre. O Aviso fica na área de trabalho.
- Com a faixa vazia, a área mostra o cartão do certificado nas condições de hoje, os Avisos no filtro de hoje e as Notas da mais nova à mais antiga. Uma aba visível tira os três. Sem Nota, não há bloco vazio. A senha continua janela. A versão fica de fora. A cobrança da Hardness sai por inteiro. A aba Início do B.I. deixa de ser a tela inicial.
- A Loja de apps é item de Configuração, só para o root no critério de hoje, e abre o catálogo que a Hardness publica. O item de criar app sai do menu do shell. Modo IDE e Editando App não são desenhados. O grupo Apps entra depois de Gerencial e antes de Outros, na ordem Integrações, Conexões MCP, Webhooks, Apontamento PCP, GNRE, Reforma Tributária, Dashboard e Painel TV. Cada um abre o endereço de hoje numa aba nova do navegador, com a mesma permissão, e não vira aba do desktop. App continua o pacote publicado pela Hardness.
- O shell mora em arquivos que a publicação não envia. O template do desktop só ganha o gancho, e o adaptador recoloca esse gancho depois do trazer com sobrescrita. Se a âncora sumiu, o trazer para, no mesmo critério dos ajustes de PHP 8. O CSS do miolo que a publicação envia continua no lugar. O CSS do shell e a folha do tema são arquivos que a publicação não envia. O percurso vê o efeito porque a página servida já é o shell novo.
- O shell usa Tailwind v4. O Preflight fica omitido. O prefixo é `tw:`. O CSS é arquivo estático, gerado fora da imagem PHP e ligado pelo gancho. A utility cede na propriedade que o CSS sem layer do legado já declara no mesmo elemento. A sidebar, o topo, a faixa e a paleta, e os descendentes, levam a base de caixa, fonte herdada e sem borda nem fundo do navegador. A paleta não nasce dentro da área de trabalho. A área de trabalho e o diálogo, que vai para o body, ficam fora dessa base.
- Cor, tipo e densidade nascem uma vez no tema desse Tailwind. O tema é um só. Uma folha fora da publicação entra pelo gancho, depois do cache e sem layer, e o miolo lê os tokens por ela. O sprite permanece. A cor do ícone é token. O passo entra no body. O cache que grava o pixel perde na propriedade que a folha repinta.
- O vocabulário é o Papéis, com a face `"Segoe UI", system-ui, sans-serif`. O cromado é quieto. A cor fica no status, no erro, na seleção e no acento. O hover da grade é o amarelo. O fundo é chapado: as três paradas do gradiente do body leem a mesma cor, e a imagem de fundo da área sai. Célula e aviso continuam no hex de hoje. Ficam sem token as chaves de menu do tema antigo. A textura do ThemeRoller sai. Os valores e o que cada token preenche vieram do protótipo do tema.

| Token | Valor | O que pinta |
| --- | --- | --- |
| `side` | `#1b2430` | sidebar, título da janela, cabeçalho do miolo |
| `side-ink` | `#e8edf2` | texto da sidebar e do título, ícone sobre eles |
| `side-muted` | `#9aa6b4` | empresa sob a marca |
| `accent` | `#1d4f8a` | avatar, título de seção, estado ativo, borda da seleção marcada |
| `canvas` | `#e7eaee` | fundo da área, faixa de abas, trilha da rolagem, as três paradas do body |
| `paper` | `#ffffff` | grade, campo, aba ativa, conteúdo do miolo |
| `ink` | `#1a1d21` | texto e ícone |
| `muted` | `#5c6570` | texto secundário, texto do cabeçalho da grade, texto da área vazia |
| `line` | `#d5d9e0` | bordas |
| `accent-soft` | `#e6eef8` | linha selecionada, realce, hover de aba |
| `hover` | `#FFE98C` | linha sob o mouse |
| `header` | `#f4f6f8` | cabeçalho da grade, zebra, filtro, aba em repouso |
| `selected-cell` | `#d3e2f5` | célula selecionada |
| `readonly` | `#f3f4f6` | campo somente leitura |
| `error` | `#ffaaaa` | erro do formulário e erro do miolo |
| `shadow` | `#141c2833` | sombra |
| `scroll-handle` | `#b7c0cb` | alça da rolagem |

Célula e aviso, no hex de hoje: verde `#B6F9B1`, amarelo `#FCF6A4`, amarelo-claro `#F9F7BD`, vermelho `#FCDAC2`, azul `#B3D8F9`, avisos `#063fdb`, `#E82728`, `#6fc293`, `#F1D126`. O ícone do erro usa o aviso vermelho.

O passo que abre, para quem não escolheu, é 12. Quem já gravou 13 ou 14 mantém. O padrão da coluna passa de 13 para 12. A escolha grava o mesmo campo de hoje. Pequeno, Médio e Grande ficam no menu do avatar.

| Passo | Nome | Fonte | Cabeçalho | Célula | Aba | Campo | Título | Entrelinha |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 12 | Pequeno | 12px | 4px 8px | 2px 8px | 22px | 24px | 4px 8px | 1.3 |
| 13 | Médio | 13px | 5px 8px | 4px 8px | 24px | 26px | 5px 8px | 1.3 |
| 14 | Grande | 14px | 6px 10px | 5px 10px | 28px | 30px | 6px 10px | 1.35 |

- O registro das decisões de lugar, de Tailwind e de tema está nos ADR 0006, ADR 0008, ADR 0009 e ADR 0010.

## Testing Decisions

- Um bom teste observa o que a pessoa vê e o que a ação faz no desktop logado. Não confere classe de utility, template, função PHP nem variável interna. Cor entra só no que o olho encontra: a sidebar, o fundo da área, a linha sob o mouse e o tamanho do tipo depois da troca de passo.
- Uma costura só. É um percurso no navegador contra a base já no ar, na branch `visual-novo`. A entrada é a mesma dos percursos que já entram na empresa de validação com o usuário local. O script não troca o checkout, não sobe a base e não roda o trazer. O gancho aparece porque a página servida já é o shell novo.
- O percurso cobre a marca, a empresa sob a marca, o menu, a paleta (filtrar, abrir, desistir, busca sem resultado), a faixa (abrir, trocar, fechar, reordenar na sessão, recarregar vazia), a janela de inclusão sobre a aba, Cliente/Fornecedor como janela com a faixa vazia, a área vazia, o sino, o avatar (densidade e sair) e o grupo Apps.
- Grade vazia passa. Certificado, Aviso, Nota e Alerta ausentes são o estado vazio da empresa de validação: sem cartão, sem bloco vazio de Nota, pop do sino no vazio do Hardness. O percurso não copia linha de produção para forçar certificado, segunda empresa, alerta de outra empresa nem usuário root. Com uma empresa, o controle é rótulo. A lista de usuários só aparece se o perfil permitir. A Loja de apps só aparece se o usuário local for root no critério de hoje; a ausência, quando não é, passa.
- O master não é o checkout sob teste.

## Out of Scope

- Integrar a branch no master. O merge espera a validação local.
- Uma rota por vez, ou reescrever cada fluxo como página única.
- Mobile, B2B e o modo compacto. A cara do dia a dia é o desktop logado.
- Reescrever grade e formulário tela por tela. O miolo muda pelo tema.
- O trilho e a paleta no lugar da sidebar.
- Recentes e ações da aba na paleta. Busca de cliente, produto, nota ou pedido na paleta.
- Atalhos Alt+letra, setas, Enter e Esc do menu.
- Chamar o sistema só de Fixar, ou manter a marca Hardness no desktop (título, logo, rodapé, sobre).
- Trocar entre tema claro, tema escuro e tema 2021.
- Um conjunto novo de ícones. O desenho continua o sprite.
- Refatorar o que gera o Alerta, a leitura e o suporte.
- Remover o modo IDE (atalho, sessão e a pasta de edição) e a janela de criar app. Os dois saem do desenho do desktop. Apagar o comportamento no Hardness é outro esforço.
- A tela de publicar a Nota.
- Mostrar o banco ou o HTML livre da empresa.
- Cobrança da Hardness e a lista de últimas atualizações.
- Transformar notificação de vídeo da Hardness em Nota.

## Further Notes

- Vocabulário do glossário: shell, aba, janela, menu, paleta, miolo, tema, token, densidade, empresa, usuário, perfil, aviso, nota, alerta, app, loja de apps, FX ERP, Hardness, publicação. O grupo Apps do menu não é o App. App continua o pacote que a Hardness publica. A Loja de apps continua em Configuração.
- Os ADR 0006, 0008, 0009 e 0010 são a decisão de lugar, de Tailwind e de tema. O passo padrão é 12. Isso fecha o padrão 14 que uma resposta anterior do mapa tinha deixado; os ADR já dizem 12.
- Os tickets do mapa continuam o registro de como cada decisão foi fechada. A spec é o plano de construção.
- A empresa de validação não tem cliente, produto, orçamento, pedido, nota nem título. O percurso trata o vazio como sucesso.
