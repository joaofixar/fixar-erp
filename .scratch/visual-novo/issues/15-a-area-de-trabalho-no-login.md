Type: grilling
Status: resolved
Orbit: ERPFX-43
Blocked by: None

# A área de trabalho no login

## Question

[A aba além da visível](09-a-aba-alem-da-visivel.md) abre a faixa vazia no login. [O que o sino lista](12-o-que-o-sino-lista.md) deixa o Aviso na área de trabalho.

O `#desktop` de hoje, sem janela única, imprime cinco buracos: certificado, senha, versão, boleto e avisos. No PHP atual:

- O certificado vira um cartão no `#desktop` quando a NF-e A1 está perto de vencer ou vencida, para alçada 4 ou as permissões 20 e 36. Vencido, hoje, amanhã ou dentro do prazo mais curto, o mesmo cartão abre também um diálogo.
- A senha não entra nesse buraco: o PHP manda string vazia. Quem avisa é uma janela aberta pelo `sistema.js`.
- A versão lê `version.txt` e o HTML atribuído é vazio.
- Boleto e avisos não são atribuídos ao template. O texto do Aviso e os cartões de boleto saem na aba Início do B.I. (`etc011Avisos`). Boleto vencido além do prazo troca a página inteira por um endereço de suspensão.

O que desses cinco ocupa a área de trabalho quando a faixa está vazia?

## Answer

Com a faixa vazia, a área de trabalho mostra, nesta ordem, o cartão do certificado quando ele existe, os Avisos que passam no filtro de hoje e as Notas, da mais nova à mais antiga. Uma aba visível tira os três. A faixa vazia os traz de volta. Sem Nota publicada, a área não reserva um bloco vazio.

O cartão do certificado continua nas condições de hoje: NF-e A1 perto de vencer ou vencida, para alçada 4 ou as permissões 20 e 36. Vencido, hoje, amanhã ou dentro do prazo mais curto, o diálogo abre por cima. A senha continua janela. A versão fica de fora da área de trabalho e do shell.

A cobrança da Hardness sai por inteiro: cartões, boleto de parceiro e o redirecionamento que troca a página. Não há cobrança nova. A lista "Últimas atualizações" sai. As 41 notificações de vídeo da Hardness não viram Nota. O logo de fundo sai junto.

O Aviso mora só na área de trabalho. A aba Início do B.I. deixa de ser a tela inicial. O cockpit abre direto nos dashboards.

A Nota é data, título e texto, sem vídeo. Todo usuário logado a vê. O Aviso continua filtrado por perfil, empresa e usuário. A tela de publicar a Nota fica fora deste mapa.

Os oito atalhos que estavam na aba Início passam para um grupo novo do menu, chamado Apps, depois de Gerencial e antes de Outros. A ordem é Integrações, Conexões MCP, Webhooks, Apontamento PCP, GNRE, Reforma Tributária, Dashboard e Painel TV. Cada um abre o mesmo endereço de hoje, numa aba nova do navegador, com a mesma permissão. Não vira aba do desktop. Reforma Tributária aparece para todo usuário logado, então o grupo Apps também. A paleta os alcança porque lista o menu.

App no glossário continua o pacote que a Hardness publica. A Loja de apps continua em Configuração.
