Type: research
Status: resolved
Orbit: ERPFX-33
Blocked by: None

# Publicação e o shell

## Question

O shell de hoje mora em `src/hardness3/smarty/templates/index.tpl` e `menu.tpl`. O PHP ofuscado em `src/hardness3/sistema/index-framework.php` escolhe o template. A publicação do Hardness entra por merge (ADR 0005), e um merge reescreve o que os dois lados mudaram no mesmo arquivo.

Quais arquivos do chrome a publicação altera desde a cópia que está neste repositório, e o nome do template do desktop está fixo no PHP?

Fatos, com fonte: o histórico destes arquivos, o ponto em que `display('index.tpl')` é chamado, e o clone da publicação se ele existir nesta máquina (o ADR 0005 cita `/root/sistema_hardness/hardness3`). Não escolher onde o shell novo mora. Não editar esses arquivos.

O arquivo da resposta é `.scratch/visual-novo/research/publicacao-e-o-shell.md`.

## Answer

O desktop chama o literal `index.tpl` em `src/hardness3/sistema/index-framework.php`, linha 273, quando não há cliente B2B, `$compacto` é falso e `device` é `desktop`. Os outros três nomes (`index-mobile.tpl`, `indexERP.tpl`, `indexB2B.tpl`) também são literais.

`index.tpl`, `menu.tpl` e `principal.css` só aparecem no commit que trouxe `src/hardness3` (`450612f`, 2026-11-12). A publicação, desde então, trocou uma linha de `menu.php` (Log erros PHP) e reescreveu a linha ofuscada de `sistema.js` (`70e44da`); um commit seguinte só devolveu o modo do JS para `100644`.

O clone existe em `/root/sistema_hardness/hardness3` no WSL Ubuntu-20.04 (`origin` `hardnesserp/hardness3`, branch `master`, 22 commits atrás do `origin/master` já gravado, sem fetch nesta pesquisa).

Notas: [publicacao-e-o-shell.md](../research/publicacao-e-o-shell.md).

O caminho de atualização, lido depois desta resposta em `bin/publicacao` e `bin/adaptar-hardness.py`: `trazer --sobrescrever` fica com a publicação no arquivo em conflito e reaplica só os ajustes de PHP 8. O adaptador não devolve alteração de template, CSS ou shell.
