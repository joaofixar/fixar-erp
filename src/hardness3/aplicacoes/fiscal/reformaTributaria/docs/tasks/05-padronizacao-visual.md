# Task 5 — Padronização visual

## Entrega

- O painel e a consulta de configurações fiscais adotam a linguagem de componentes da GNRE: superfícies claras, bordas discretas, azul principal, sombras leves, foco visível e tipografia consistente.
- O cabeçalho inicial usa o azul `#184784` e a marca oficial do Hardness, como a identidade da GNRE, sem reproduzir sua navegação lateral.
- O modal fiscal mantém um único cabeçalho externo com título, subtítulo e fechamento. A consulta interna fixa resumos, alternâncias e filtros; somente os resultados rolam.
- A consulta apresenta resumos CFOP/NCM, controles segmentados, busca com ícone, exportação CSV e tabela com situação, configuração identificada por CST e classificação tributária, pendência e detalhes em modal próprio.
- Nos detalhes de CFOP, o modal também apresenta CST/classificação regular, alíquotas, reduções e diferimentos derivados da classificação tributária oficial, além dos indicadores de crédito e das deduções de base configuradas no cadastro.
- Com o modal fiscal aberto, a página inicial não rola. O detalhe mantém seu próprio conteúdo rolável e o Id é exibido como primeira coluna na listagem de CFOP.
- Os filtros e resumos usam “Não configurados” em vez de “Pendentes”, evitando interpretar como pendência uma configuração que pode ser atendida pelo outro cadastro fiscal.
- O comportamento de consulta, filtros, busca, paginação, CSV, permissões e empresa atual não foi alterado.

## Validação local

- Aprovado: `php -l aplicacoes/fiscal/reformaTributaria/public/index.php`.
- Aprovado: `php -l aplicacoes/fiscal/reformaTributaria/public/configuracoes-fiscais-view.php`.
- Aprovado: `php -l aplicacoes/fiscal/reformaTributaria/public/configuracoes-fiscais.php`.
- Aprovado: `git diff --check`; como o módulo ainda está não rastreado neste worktree, os arquivos entregues também foram verificados com `git diff --no-index --check`.
