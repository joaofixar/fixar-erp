# Visual novo do desktop

Orbit: ERPFX-28

## Destination

A spec saiu deste mapa: [O desktop logado usa o visual novo](spec.md). A construção fica na branch `visual-novo`, nascida do master, para a base local validar antes do merge.

## Notes

Desktop logado. A aba é a tela de módulo: várias abertas, uma visível. A janela flutua sobre a aba visível, ou, sem aba, sobre a área de trabalho.

A marca no desktop é **FX ERP**. "Fixar" sozinho colide com empresas do grupo que já se chamam Fixar. "Fixar ERP" era o outro candidato. FX ERP cabe na sidebar e não repete o nome de uma empresa.

Ctrl+K e a lupa do topo abrem a mesma paleta no centro. Saem os atalhos Alt+letra, setas, Enter e Esc do menu.

O miolo (grades, formulários, jQuery UI) muda por um tema com os mesmos tokens do shell. Cada tela não se reescreve. O tema é um só.

O shell segue a referência: sidebar à esquerda com os grupos do menu, barra no topo com avatar, sino e lupa, área de trabalho no restante. A área de trabalho continua sendo o `#desktop`.

Toda sessão consulta grilling e domain-modeling. Research e prototype seguem o `Type:` do ticket.

O PHP do núcleo em `src/hardness3` está ofuscado. A leitura fica em `referencia/`. `sistema-legado/` é só leitura. O shell novo mora em arquivos que a publicação não envia. `index.tpl` só ganha o gancho, e o `adaptar-hardness.py` reaplica esse gancho depois do `trazer --sobrescrever`.

## Decisions so far

- [Publicação e o shell](.scratch/visual-novo/issues/05-publicacao-e-o-shell.md): o desktop chama o literal `index.tpl`. O `trazer --sobrescrever` fica com a publicação no arquivo em conflito e, em seguida, reaplica só os ajustes de PHP 8 do `adaptar-hardness.py`. Notas em [publicacao-e-o-shell.md](.scratch/visual-novo/research/publicacao-e-o-shell.md).
- [Tailwind no shell Smarty](.scratch/visual-novo/issues/04-tailwind-no-shell.md): o reset sai omitindo o Preflight. O prefixo `tw:` só renomeia classes. O CSS sem layer do legado ganha das utilities. A imagem de produção não gera esse CSS. Notas em [tailwind-no-shell.md](.scratch/visual-novo/research/tailwind-no-shell.md).
- [O que ainda flutua](.scratch/visual-novo/issues/01-o-que-ainda-flutua.md): a aba é a tela de módulo, uma visível. O formulário de inclusão continua janela sobre a aba de onde saiu.
- [Onde o shell mora](.scratch/visual-novo/issues/07-onde-o-shell-mora.md): o shell mora em arquivos que a publicação não envia. `index.tpl` só ganha o gancho, reaplicado pelo `adaptar-hardness.py` depois do trazer. Ver [ADR 0006](../../docs/adr/0006-shell-fora-dos-arquivos-da-publicacao.md).
- [O que a paleta pesquisa](.scratch/visual-novo/issues/02-o-que-a-paleta-pesquisa.md): a paleta lista o menu do perfil, na ordem do menu. Digitar filtra por item ou grupo. Escolher abre a mesma aba ou janela e fecha a paleta.
- [Empresa e usuário no shell](.scratch/visual-novo/issues/03-empresa-e-usuario-no-shell.md): a empresa fica sob a marca, com a lista pelo nome completo. O avatar abre login, perfil, troca de usuário e sair. O shell não mostra o banco nem o HTML livre da empresa.
- [Shell para reagir](.scratch/visual-novo/issues/06-shell-para-reagir.md): a barra lateral, com o topo (lupa, sino, avatar) e a faixa de abas, é o shell do dia inteiro. O trilho e a paleta primeiro ficam de fora. Protótipo em [shell.html](.scratch/visual-novo/prototype/shell.html).
- [O shell usa Tailwind](.scratch/visual-novo/issues/08-o-shell-usa-tailwind.md): Tailwind v4, Preflight omitido, prefixo `tw:`, CSS estático gerado fora da imagem PHP. A base em utility cobre a barra, o topo, a faixa e a paleta. O `#desktop` fica de fora. Ver [ADR 0008](../../docs/adr/0008-shell-usa-tailwind-sem-preflight.md).
- [A aba além da visível](.scratch/visual-novo/issues/09-a-aba-alem-da-visivel.md): a faixa fecha, reordena na sessão e começa vazia no login. O miolo permanece na aba. A janela sozinha fecha quando uma aba ocupa a área.
- [Onde nascem os tokens](.scratch/visual-novo/issues/10-onde-nascem-os-tokens.md): cor, tipo e densidade nascem no `@theme` (`--tw-…`). Uma folha fora da publicação, por último e sem layer, repinta o miolo. O tema é um só. O passo 12, 13 ou 14 entra no `body`. O sprite permanece e a cor do ícone é token. Ver [ADR 0009](../../docs/adr/0009-tokens-nascem-no-theme.md).
- [Quais valores o tema carrega](.scratch/visual-novo/issues/11-quais-valores-o-tema-carrega.md): o vocabulário é o Papéis, com a face Segoe UI. O passo padrão é 12, e Pequeno, Médio e Grande ficam no menu do avatar. Ver [ADR 0010](../../docs/adr/0010-tema-carrega-o-vocabulario-papeis.md). Mapa em [tema.html](.scratch/visual-novo/prototype/tema.html).
- [O que o sino lista](.scratch/visual-novo/issues/12-o-que-o-sino-lista.md): o pop pendurado no sino lista o Alerta não lido, com o comportamento do Hardness. O Aviso fica na área de trabalho. A refatoração do sino fica fora.
- [Loja de apps, modo IDE e o app em edição](.scratch/visual-novo/issues/13-loja-ide-e-app-em-edicao.md): a loja de apps fica em Configuração, só para o root, e lista o catálogo que a Hardness publica. O item de criar app sai do menu. Modo IDE e Editando App ficam fora da spec.
- [A paleta além da pesquisa](.scratch/visual-novo/issues/14-a-paleta-alem-da-pesquisa.md): a paleta continua só o menu do perfil. Sem recentes e sem ações da aba.
- [A área de trabalho no login](.scratch/visual-novo/issues/15-a-area-de-trabalho-no-login.md): com a faixa vazia, a área mostra o certificado quando existe, o Aviso e as Notas. Uma aba visível tira os três. A cobrança da Hardness e as últimas atualizações saem. As oito portas novas ficam no menu Apps.
- [O desktop logado usa o visual novo](spec.md): a spec de construção, pronta para um agente. A branch `visual-novo` nasce do master. O percurso de prova é o desktop no navegador, na base dessa branch. O merge espera essa validação. Orbit ERPFX-44.

- [A sidebar recolhe e a aba ganha desenho](.scratch/visual-novo/issues/28-a-sidebar-recolhe-e-a-aba-ganha-desenho.md): seções e grupos em sanfona com ícone, a sidebar recolhida só com ícones, a marca com a troca de empresa, o caminho no topo e a aba em pílula. Revisa a história 18 da spec e traz o trilho como modo da mesma sidebar. Orbit ERPFX-57.

## Not yet specified

## Out of scope

- Uma rota por vez. Reescrever cada fluxo como página única é outro esforço. A spec parte de várias coisas abertas ao mesmo tempo.
- Mobile, B2B e o modo compacto (`indexERP.tpl`). A cara do dia a dia é o desktop logado.
- Reescrever grade e formulário tela por tela. O miolo muda pelo tema.
- Atalhos Alt+letra, setas, Enter e Esc do menu. A navegação rápida passa a ser a paleta.
- Chamar o sistema só de Fixar, ou manter a marca Hardness no desktop (título, logo, rodapé, sobre).
- Trocar entre `tema-claro.css`, `tema-escuro.css` e `tema-2021.css`. Este esforço tem um tema só.
- Um conjunto novo de ícones. O desenho continua o sprite do ThemeRoller.
- Refatorar o que gera o Alerta, a leitura e o suporte. [O que o sino lista](.scratch/visual-novo/issues/12-o-que-o-sino-lista.md) só recoloca o comportamento do Hardness no pop. A refatoração é outro esforço.
- Remover o modo IDE (atalho, sessão e a pasta `ide/`) e a janela de criar app. [Loja de apps, modo IDE e o app em edição](.scratch/visual-novo/issues/13-loja-ide-e-app-em-edicao.md) tira os dois do desktop. Apagar o comportamento no Hardness é outro esforço.
- A tela de publicar a Nota. [A área de trabalho no login](.scratch/visual-novo/issues/15-a-area-de-trabalho-no-login.md) só desenha a Nota na área de trabalho. Publicar é outro esforço.
