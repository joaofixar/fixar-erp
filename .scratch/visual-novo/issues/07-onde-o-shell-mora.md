Type: grilling
Status: resolved
Orbit: ERPFX-35
Blocked by: None

# Onde o shell mora

## Question

O PHP ofuscado chama o literal `index.tpl` (`index-framework.php`, linha 273). O PHP faz `fetch('menu.tpl')` e entrega o HTML em `{$menu}`; `{$menuArray}` também chega no template. `principal.css` também estiliza grade e formulário.

`bin/publicacao trazer --sobrescrever` junta três árvores. No arquivo em que os dois lados mudaram, fica a publicação (`ficar_com_a_publicacao`). Em seguida `bin/adaptar-hardness.py` reaplica os ajustes de PHP 8 por troca de trecho. Se o trecho-âncora não está no arquivo, o trazer para e não avança o ponto. O adaptador não conhece shell, CSS nem template. Arquivo que só a Fixar mudou, e que a publicação não tocou, o merge de três vias preserva.

Os fatos do chrome estão em [publicacao-e-o-shell.md](../research/publicacao-e-o-shell.md).

O shell novo mora em arquivos que a publicação não envia, com o gancho em `index.tpl` reaplicado pelo adaptador depois do trazer? Ou a spec edita `index.tpl`, `menu.tpl` e `principal.css` no lugar, sabendo que um conflito devolve a versão da publicação?

## Answer

O shell mora em arquivos que a publicação não envia. `index.tpl` só ganha o gancho. O `adaptar-hardness.py` reaplica esse gancho depois do `trazer --sobrescrever`. `index.tpl`, `menu.tpl` e `principal.css` não guardam a cara nova. O registro está no [ADR 0006](../../../docs/adr/0006-shell-fora-dos-arquivos-da-publicacao.md).
