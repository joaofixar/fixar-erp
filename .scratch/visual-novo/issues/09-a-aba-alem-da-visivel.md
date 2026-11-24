Type: grilling
Orbit: ERPFX-37
Status: resolved
Blocked by: None

# A aba além da visível

## Question

A aba é a tela de módulo, várias abertas, uma visível. [Shell para reagir](.scratch/visual-novo/issues/06-shell-para-reagir.md) pôs essas abas numa faixa sobre a área de trabalho.

Além de trocar a aba visível, dá para fechar, reordenar e lembrar as abas abertas no próximo login?

O botão de fechar no protótipo [shell.html](.scratch/visual-novo/prototype/shell.html) existiu só para operar. Não é decisão.

## Answer

A faixa lista as telas `topleft`. Várias ficam abertas, uma visível. O miolo de cada uma continua o content de hoje. No C.R.M., a faixa CRM / clientes / + e, por baixo, Orçamentos, Pedidos e o resto seguem no `#desktop`. A faixa do shell fica fora do `#desktop` e não usa a classe `.tabs`, porque o `tabCRM.tpl` chama `$('.tabs')` na página inteira. Trocar de aba esconde o content. Voltar mostra o mesmo, nas mesmas tabs internas.

Cada aba fecha, sem pergunta, mesmo com inclusão preenchida. A pergunta de algumas janelas no Esc continua sendo o Esc da própria janela. Fechar a visível mostra a da direita, ou a da esquerda se ela era a última. A faixa pode ficar vazia. Fechar uma aba que não está visível deixa a visível onde está.

Arrastar muda a ordem na sessão. Uma aba nova entra no fim. Abrir de novo um item que já está aberto mostra o que já está aberto, no lugar em que está.

Login novo ou recarregar a página abre a faixa vazia. Trocar de empresa ou de usuário fecha as abas e as janelas, inclusive a janela que estava sozinha.

Sobre a aba visível cabem várias janelas, uma por cima da outra. Trocar de aba esconde a pilha. Voltar mostra a mesma pilha, na mesma ordem. Fechar a aba fecha a pilha junto.

Cliente/Fornecedor com a faixa vazia é janela, não aba. Ocupa a área de trabalho e o fechar dela vale. Abrir o C.R.M., o Estoque ou outra tela `topleft` fecha essa janela e o que estiver empilhado nela. A aba fica visível. A janela não volta.

Quando as abas não cabem, a faixa rola na horizontal e a aba visível permanece à vista.
