Type: grilling
Status: resolved
Orbit: ERPFX-31
Blocked by: None

# Empresa e usuário no shell

## Question

O rodapé do desktop hoje mostra a empresa atual (com troca, quando há mais de uma), o usuário (com troca, quando o perfil permite), o perfil e a base. Sair está no menu.

O shell novo tem sidebar, e no topo avatar, sino e lupa. A marca no topo da sidebar é FX ERP.

Onde ficam a empresa atual e a troca de empresa, o usuário, o perfil, a base e o sair?

## Answer

A empresa atual fica na sidebar, logo abaixo de FX ERP, sempre visível. O controle fechado mostra o nome abreviado; o nome completo fica no título. Com uma empresa só, é rótulo. Com mais de uma, o mesmo controle abre a lista, e cada item mostra o nome completo. A troca recarrega o menu, fecha as janelas e atualiza a área de trabalho.

O avatar do topo abre o usuário. O menu mostra o login e, abaixo, o nome do perfil. O nome da pessoa fica fora do shell. A troca de usuário, só quando o perfil permite, fica nesse menu e faz o mesmo que a troca de empresa. Sem permissão, a lista não aparece. Sair é o último item e vai para `/logout`.

O shell não mostra o nome do banco. Há um banco só. Também não desenha o HTML livre da empresa.
