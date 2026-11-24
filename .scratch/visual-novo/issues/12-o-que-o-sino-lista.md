Type: grilling
Status: resolved
Orbit: ERPFX-40
Blocked by: None

# O que o sino lista

## Question

O topo do shell tem o sino, como ficou em [Shell para reagir](06-shell-para-reagir.md). No desktop de hoje o sino é o `#messageAlert`: a contagem e, ao abrir, o painel intitulado "Notificações".

Esse painel lista mensagem e alerta não lidos que o perfil pode ver (usuário, alçada, tela e empresa). Dá para ocultar um item, atualizar a lista e enviar mensagem a outro usuário. Com permissão, entra também o suporte. Vazio, o painel diz "Você não possui nenhuma mensagem ou alerta."

O Aviso é outra coisa. É o texto publicado na tela inicial, desenhado na área de trabalho. O glossário evita chamar o Aviso de notificação.

O sino abre essa mesma lista, com as mesmas ações, e o Aviso continua na área de trabalho?

## Answer

O sino abre um pop pendurado no ícone, intitulado Alertas. Um pop por vez: abrir a empresa, o avatar ou a paleta fecha esse pop. O sino de novo, Esc ou um clique fora também fecha. Fechar descarta o rascunho de enviar mensagem. Abrir de novo começa vazio.

O item é um Alerta. Aviso continua na área de trabalho. Enviar mensagem continua a ação de escrever a outro usuário.

O comportamento é o do Hardness. A lista traz o Alerta não lido que o perfil pode ver, agrupado pelo nome abreviado da empresa, com a contagem do que não está oculto. Ocultar, atualizar e enviar ficam no pop. O suporte entra só com permissão. Clicar num Alerta da empresa atual executa a ação de hoje, tira o item da lista e da contagem e fecha o pop. Alerta de outra empresa não abre.

A refatoração do que gera o Alerta, da leitura e do suporte fica fora deste mapa.
