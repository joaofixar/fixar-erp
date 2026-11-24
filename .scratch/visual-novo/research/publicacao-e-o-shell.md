# Publicação e o shell

Fatos lidos no código, no histórico git deste repositório e no clone que já está na máquina. Nenhum arquivo de template, PHP ou CSS de produção foi editado. Nenhum `fetch`, `pull`, `checkout`, `commit` ou clone novo foi feito.

## O nome do template do desktop está fixo

Sim. Em `src/hardness3/sistema/index-framework.php`, linha 273, o nome é o literal `'index.tpl'`. Não há variável com o nome do template. O trecho, ofuscado com `goto`, é:

```php
if (isset($g['clienteAtual'])) { goto Xp9wH; }
if ($compacto) { goto Yv5RA; }
if ($_SESSION['device'] == 'desktop') { goto BIviX; }
$g['smarty']->display('index-mobile.tpl'); goto fOGvf;
BIviX: $g['smarty']->display('index.tpl'); fOGvf: goto NAOpA;
Yv5RA: $g['smarty']->display('indexERP.tpl'); NAOpA: goto k9oBt;
Xp9wH: $g['smarty']->display('indexB2B.tpl');
```

Há uma ocorrência de `display('index.tpl')`. Ela roda quando não há `$g['clienteAtual']`, `$compacto` é falso e `$_SESSION['device']` é `'desktop'`. Os outros três nomes também são literais: `index-mobile.tpl`, `indexERP.tpl`, `indexB2B.tpl`.

`$compacto` começa na linha 2 do mesmo arquivo. Fica verdadeiro para os bancos `e238`, `dev_e238`, `e239` e `dev_e239`. Na mesma linha há um segundo `$compacto = true`, quando o texto decifrado (cookie `dbAtual`, ou o parâmetro `r` que grava esse cookie) começa com `C`. Esse segundo trecho só executa se `dirname(__FILE__)` é `naousar/var/www/hardness3`; no caminho contrário o `goto CoLyy` pula o bloco. Com `$compacto`, a linha 273 chama `indexERP.tpl`.

`$_SESSION['device']` é definido na linha 107: navegador móvel vira `'mobile'`, senão `'desktop'`, com um arquivo `mobile.flag` por usuário podendo forçar um ou outro. Fora de `'desktop'`, a linha 273 chama `index-mobile.tpl`.

No commit que trouxe o arquivo, `450612f836d6df9f3c0783f3d05a102d6499c6a5` (2026-11-12), `git grep -n "display('index.tpl')"` acha o mesmo literal na linha 1501, já indentado. Depois de `250c769b01633b81ef773f2762654abc9eb9f3e3` (2026-11-17, `feat(publicacao): alinha o src à ponta da publicação`) o `git grep -c` ainda conta 1. O commit local seguinte, `81dc54830b289c12d2f970645a316428ab6a28af`, altera uma linha (o hunk começa na linha 23, numstat `1 1`) e o diff desse commit não contém `index.tpl` nem `display`.

## O que cada arquivo do chrome desenha

### `src/hardness3/smarty/templates/index.tpl`

209 linhas (`wc -l`). É o documento do desktop. Desenha:

- `<head>` com `{$titulo}`, o `require` do Monaco em `/hardness3/static/js/monaco-editor/0.25.2/min/vs`, o placeholder `{$cssJs}`, `{$cssExtra}` e a fonte Roboto.
- `#messageAlert`: painel de notificações, botão Suporte (se `$permissaoSuporte`), atualizar, enviar mensagem. Some quando `$oneWindow`.
- `#desktop`: área das janelas. Recebe `{$certificado}`, `{$senhaDesatualizada}`, `{$versao}`, `{$boleto}` e `{$avisos}`.
- `#footer`: empresa (`$selectEmpresa` / `$empresaAtualNome`), usuário, perfil, nome do banco, e os selos de modo IDE. Some quando `$oneWindow`.
- `#topo`: `#topoMenu` imprime `{$menu}`; ao lado, loja de apps, botão de zoom e `#logo` (`{$logoMenu}`), que abre a janela Sobre.
- Um script no fim declara `device = 'desktop'` e o objeto global `g` (empresa, usuário, personalização, tamanhos de janela). Clique em `#campoEmpresa` e `#campoUsuario` troca o texto pelo `<select>`.

`{$cssJs}` é montado por `processaCssJs()` na linha 107 e a função está na linha 293. Ela emite `<script src>` e `<link rel=stylesheet>` a partir de `$g['conteudo']`.

### `src/hardness3/smarty/templates/menu.tpl`

47 linhas. Não é uma página. É a barra:

- `<ul id="barra" class="menubar ...">`.
- Item Início, link `/erp/`.
- Um `<li class="menuvertical">` por chave de `$menuArray`, com atalho sublinhado (C, V, E, F, L, X, B, G, T, O). Cada filho é um `<a onclick="...">` com o JavaScript que veio do PHP. Se o item é array, mostra o atalho de teclado em `$menuA2[0]` e o `onclick` em `$menuA2[1]`.
- Item Sair, `window.location = '/logout'`.
- Fundo da `<ul>` vermelho quando `$modoDesenvolvedor`.

Quem injeta isso no desktop é a linha 172 de `index-framework.php`. O arquivo faz `require_once 'bibliotecas/menu.php'` (ou `menu_nfe.php` se a rota é `nfe`), percorre `$menu` e, com empresa atual, faz:

```php
$g['smarty']->assign('menuArray', $menuNormal);
$g['smarty']->assign('menu', $g['smarty']->fetch('menu.tpl'));
```

Isso quando o cookie `editarMenu` não é `hardness` e `$compacto` é falso. `$compacto` troca o fetch para `menuERP.tpl`. O cookie `editarMenu=hardness` usa `menuEditor.tpl`. Sem empresa atual, o mesmo arquivo atribui `menuArray` a `array('Sistema de Gestão Empresarial' => false)` e ainda faz `fetch('menu.tpl')` (de novo `menuERP.tpl` se `$compacto`). Contato B2B (`$g['clienteAtualContato'] > 0`) usa `menuB2B.tpl`. O `index.tpl` só imprime o HTML já renderizado em `{$menu}`.

### `src/hardness3/static/css/principal.css`

2003 linhas. A linha 66 de `index-framework.php` lê o arquivo inteiro (`static/css/principal.css`), troca os placeholders `[[fonte...]]` e o resultado entra no CSS da página. Não é só o chrome: o mesmo arquivo estiliza grid, formulário, aba, BI, TinyMCE e barra de rolagem (comentários nas linhas 635, 915, 1627, 1722, 1744, 1902).

O chrome está nestes seletores:

- `#topo`, `#topo #logo`, `#topo .zoomButton`, `#topo .lojaApp`, `#topo .notificacoesSuporte`, `#topoMenu` (por volta da linha 189).
- `#footer` e os botões/selects do rodapé (linha 233). Texto branco, altura 18px, fundo transparente.
- `ul.menubar` (linha 275): faixa de 49px. `ul.menubar .menuvertical`, `ul.menubar ul.menu` e `ul.menu li.submenu ul` desenham o dropdown. O submenu começa `display: none` (linha 423).
- `#desktop` (linha 748): `position: fixed; top: 51px; width/height 100%; overflow: hidden`. `#toolbar` está `display: none`.
- `#messageAlert` (linha 1340): painel fixo, ícone em `/hardness3/static/img/icone_notificacao.png`.

O `body` (linha 71) pinta o degradê cinza de fundo.

### `src/hardness3/bibliotecas/menu.php`

`wc -l` conta 268. Namespace `hardness`. O comentário da linha 4 é "Menu do sistema". O arquivo só monta o array `$menu`: grupos Cadastros, Vendas, Estoque, Financeiro, Fiscal, Comex, Contábil, Gerencial, Outros e Configuração. Cada item guarda uma string JavaScript `abrirJanela(...)` (ou um par atalho + `abrirJanela`). Não chama Smarty e não emite HTML. Itens de usuário `63` (IDE, atualização de base, personalização) e, se `gVerificaRoot()`, Loja Apps. O bloco comentado começa na linha 162 (Fast Crud e entradas antigas de menu). O `index-framework.php` é quem filtra esse array em `$menuNormal` e manda para o `menu.tpl`.

### `src/hardness3/static/js/sistema.js` e a versão legível

O arquivo servido é ofuscado, uma linha, 422480 bytes, modo `100644`, blob `ddc5f0579b8a396c0a8f636d32217a84b843632c` (`git ls-files -s` e `git cat-file -s HEAD:...`). A linha 66 de `index-framework.php` o coloca na página quando não é contato B2B:

```php
$g['conteudo']['jsArquivo'][] = $g['pathWeb'] . mudarCaminhoSeFramework('static/js/sistema.js') . '?load=' . mt_rand();
```

Contato B2B carrega `static/js/sistemaB2B_3.js` no lugar.

A versão legível está em `referencia/static/js/sistema.js` (12699 linhas). O cabeçalho, linhas 1–8, diz:

```javascript
/**
 * sistema.js
 *
 * Versão legível de sistema-legado/static/js/sistema.js.
 * ...
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */
```

O único commit desse arquivo é `9b9a2c0bed4465b82c0a03e89cca44b925835dd8` (2026-11-12, `feat(referencia): add script to deobfuscate JavaScript code and generate readable version of sistema.js`). Os commits que trocaram o blob de `src/hardness3/static/js/sistema.js` não incluem `referencia/static/js/sistema.js`. O cabeçalho aponta para `sistema-legado/`, não para o blob que está em `src/` hoje.

Pelo que essa versão legível faz no chrome, no `$(document).ready` (linha 11):

- `device == "mobile" ? carregaMenuMobile() : carregaMenu()`.
- `fixDesktopSize()` agora e no `resize` da janela.

`carregaMenu` (linha 8890) liga `mouseover`/`mouseout` em `.menuvertical a`: abre e fecha `ul.menu` dentro de `#topoMenu #barra`. `carregaMenuMobile` (linha 8945) faz o mesmo no clique e reposiciona o submenu. `fixDesktopSize` (linha 1224) mede `#desktop` e desconta `desktopUp`/`desktopDown` quando não é `oneWindow`. `acoesNotificacoes` (linha 1239) abre o painel `#messageAlert`. `opcoesZoom` (linha 1892) mexe em `#topo .zoomButton`.

O mesmo arquivo segue com `abrirJanela` (linha 2003), o widget `$.widget("ui.form"` (linha 8992) e o restante de grid e formulário. É o runtime do cliente, não só a barra.

O delta do blob ofuscado entre a cópia e o que está no `HEAD` não foi desofuscado aqui. Os dois lados são uma linha; a primeira diferença aparece no byte 9 (`a0_0x1016e2` virou `a0_0x302842`) e o sufixo comum tem 9 bytes. É outra ofuscação do arquivo inteiro, 422913 bytes para 422480.

## O que mudou neste repositório desde a cópia

O primeiro commit que toca `src/hardness3` é:

```text
450612f836d6df9f3c0783f3d05a102d6499c6a5 2026-11-12 feat(base): serve o Hardness versionado
```

Comando: `git log --reverse --format='%H %ad %s' --date=short -- src/hardness3`. Nesse commit, `git log --name-status` marca os cinco arquivos como `A`.

Árvore de trabalho limpa nesses caminhos: `git status --porcelain --` os cinco arquivos não imprime nada.

| Arquivo | Commits depois da entrada | O que o diff mostra |
| --- | --- | --- |
| `smarty/templates/index.tpl` | nenhum | `git log 450612f..HEAD --` esse caminho é vazio |
| `smarty/templates/menu.tpl` | nenhum | idem |
| `static/css/principal.css` | nenhum | idem |
| `bibliotecas/menu.php` | um | `250c769` (2026-11-17), numstat `1 1` |
| `static/js/sistema.js` | dois | conteúdo em `70e44da`; só o modo em `4874be5` |

`menu.php`, commit `250c769b01633b81ef773f2762654abc9eb9f3e3`, assunto `feat(publicacao): alinha o src à ponta da publicação`. A única linha trocada é o item Outros → Log erros PHP: deixa de ser `abrirJanela` para `/outros/apacheErros/apacheErros/content/` e passa a ser `window.open('/outros/apacheErros/apacheErros/standalone/?ajax=true', ...)`.

`sistema.js`:

- `70e44da3bde23101de1029532973980afbdec275` (2026-11-13), `feat(publicacao): traz a publicação até 9453d52241c28868c52e1fadb4f7ee62447189c6`. `git diff --raw` contra `450612f`: modo `100644` → `100755`, blob `bccd9398` → `ddc5f057`. Numstat `1 1` porque o arquivo é uma linha. Tamanho 422913 → 422480.
- `4874be56a0edb1e788307cac53603424f81869b2` (2026-11-16), `feat(publicacao): traz a publicação até 786d42eb62cd30f9a80affbd338335a168ee6971`. `git diff --raw` contra o pai: `:100755 100644 ddc5f057 ddc5f057`. O blob não muda. O bit executável volta a `100644`. Numstat `0 0`.

O `HEAD` continua no blob `ddc5f057`, modo `100644`.

O escolhedor de template, que não é um dos cinco arquivos acima, também andou. `250c769` reescreve `src/hardness3/sistema/index-framework.php` (numstat `68 1501`) e o literal `display('index.tpl')` continua, uma vez. `81dc548` (2026-11-17, `feat(adaptar-hardness): implementa adaptações para cadeia de telas no sistema/index-framework.php`) muda uma outra linha; o diff não contém `index.tpl`.

## O clone da publicação nesta máquina

Existe. É o caminho do ADR 0005, dentro do WSL.

`wsl.exe -l -v` lista `Ubuntu-20.04` como distro padrão (parada no momento da consulta, versão 2), mais `kiri` e `docker-desktop`. Não há distro chamada só `Ubuntu`.

Dentro de Ubuntu-20.04:

```text
DIR_YES
/root/sistema_hardness          (diretório, 9 out 2025)
/root/sistema_hardness/hardness3 (diretório, 13 nov 13:43)
git rev-parse --is-inside-work-tree → true
origin  git@github.com:hardnesserp/hardness3.git (fetch e push)
branch  master
status  ## master...origin/master [behind 22]
HEAD    a69ffe42f129e217f0b94f5e3878ecc52167625f
        2026-11-13 Merge pull request #4056 from hardnesserp/feature/duplicar-ordem-producao
```

Comando: `wsl.exe -d Ubuntu-20.04 -e bash -lc '...'` com `test -d`, `git remote -v`, `git status -sb`, `git log -1`. A mesma pasta, vista do Windows, é `//wsl.localhost/Ubuntu-20.04/root/sistema_hardness/hardness3` (`ls` mostrou `aplicacoes/`, `bibliotecas/`, `prog/`, `index.php` e o restante).

`bin/publicacao`, linhas 80–81, usa esse UNC quando `--publicacao` vem vazio:

```bash
publicacao="//wsl.localhost/Ubuntu-20.04/root/sistema_hardness/hardness3"
```

Sem fetch nesta pesquisa, o `origin/master` já gravado no clone é `02426d888e720c96c45d60fde2ddf545dfa818af` (2026-11-17, `Merge pull request #4069 from hardnesserp/feat/nota-espelho-observacao-destinatario`). `git rev-list --count HEAD..origin/master` imprime `22`. `origin/master..HEAD` imprime `0`. `git status --porcelain` no clone não imprime arquivo sujo.

`wsl.exe -d kiri -e bash` falha com `execvpe(bash) failed: No such file or directory`. Não há outro clone óbvio em `/root/sistema_hardness/hardness3` fora desse Ubuntu, nem em `C:\root\sistema_hardness\hardness3`, na pasta do usuário, nem em `C:\Projetos Fixar\sistema_hardness\hardness3` (`test -d` deu MISSING).

O ADR 0005 separa esta pasta da cópia em `sistema-legado/`. Esta nota não abriu `sistema-legado/` como se fosse o clone da publicação.

## O trazer não copia a pasta por cima

Lido em `bin/publicacao` e `bin/adaptar-hardness.py` depois do restante desta nota. O ADR 0005 descreve o merge de três vias e a parada no conflito. O comando de hoje tem um passo a mais.

`trazer` lê o ponto em `bin/ponto`, busca a ponta e faz `read-tree -m` com três árvores: a publicação no ponto, o `src/hardness3` atual, a publicação na ponta (`bin/publicacao`, função `trazer`). Com `--sobrescrever`, `ficar_com_a_publicacao` percorre os caminhos em conflito e grava o estágio 3, o lado da publicação. O texto do próprio comando diz: "Onde os dois lados mudaram o mesmo arquivo, fica a publicação."

Se ainda resta caminho em conflito, o comando para e não avança o ponto. Sem conflito, `aplicar_adaptacoes` extrai a árvore e roda `bin/adaptar-hardness.py`. Esse script reescreve PHP 8 por troca de trecho: `mysqli_connect_error` sem argumento, chaves de string, âncoras em `bibliotecas/funcoesGerais.php`, `sistema/interfaceUsuario.php` e `sistema/index-framework.php` (`CADEIA` / `PAI`). Se a âncora sumiu e a forma segura ainda não está no arquivo, o script termina em erro e o trazer não avança o ponto.

O adaptador não menciona `index.tpl`, `menu.tpl`, `principal.css` nem `sistema.js`. Alteração de shell feita nesses arquivos não volta depois de um conflito. Arquivo que só a Fixar alterou, e que a publicação não tocou entre o ponto e a ponta, o merge de três vias preserva.
