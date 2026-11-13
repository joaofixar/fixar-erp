# Tarefa 03 — Fundação da aplicação

**Situação:** Concluída em 14/10/2026.

## Objetivo

Criar a estrutura executável de `aplicacoes/pcp/apontamento`, inspirada no
módulo Balanço, sem importar suas regras de inventário.

## Escopo

- `bootstrap/app.php`, `config/module.php`, `public/` e assets;
- sessão Hardness, contexto de banco/empresa e permissão de acesso;
- URL pública direta documentada;
- proteção CSRF para ações de escrita;
- helpers de resposta, flash e validação;
- layout próprio, responsivo e adequado ao terminal.

## Critérios de aceite

- O módulo não expõe `bootstrap`, `config` ou `docs` publicamente.
- A aplicação usa sessão ERP existente; matrícula não cria login ou sessão.
- Empresa é validada no backend em todas as consultas operacionais.

## Handoff da implementação

### Acesso direto

- O menu do ERP abre diretamente
  `/hardness3/aplicacoes/pcp/apontamento/public/index.php`.
- Não há rota curta, rewrite, redirecionamento ou configuração de VirtualHost
  para este módulo no MVP. Somente o diretório `public/` é acessado pelo menu.
- `public/.htaccess` contém exclusivamente `Options -Indexes -MultiViews` e
  `DirectoryIndex index.php`. A entrada atual é intencionalmente uma tela de
  prontidão: os fluxos de matrícula/OP/processo pertencem à Tarefa 04.

### Permissão e sessão

- Permissão escolhida: `C029_Id=203`, descrição existente `PCP`, grupo
  `Estoque`. Não foi criada permissão, menu, dado IDE ou migration nesta tarefa.
- A estação deve estar autenticada no ERP como uma conta técnica `C007` ativa
  (por exemplo, `TERMINAL PCP`) que possua, em `C019`/`C030`, a permissão 203
  liberada para a empresa selecionada. A aplicação não possui login próprio,
  não encerra a sessão ERP e não aceita matrícula como autenticação.
- Em toda requisição o backend recarrega `C019`/`C030`, exige empresa ativa e
  salva somente um contexto local validado. As próximas consultas operacionais
  devem obter a empresa por `pcp_apontamento_require_company()` e usá-la no
  predicado SQL.

### Arquivos criados

- `bootstrap/app.php`: bootstrap da sessão Hardness, autorização, contexto de
  empresa, CSRF, resposta JSON/erro, flash, validadores e layout.
- `config/module.php`: metadados, URL pública direta e permissão.
- `public/index.php`, `public/assets/css/app.css` e `public/assets/js/app.js`:
  tela própria, responsiva, sem componentes visuais do FW.
- `public/.htaccess`: desabilita listagem e MultiViews e define `index.php`
  como índice, sem regras de rewrite.

### Validações executadas

- `php -l aplicacoes/pcp/apontamento/bootstrap/app.php`: sem erros de sintaxe.
- `php -l aplicacoes/pcp/apontamento/public/index.php`: sem erros de sintaxe.
- `php -l aplicacoes/pcp/apontamento/config/module.php`: sem erros de sintaxe.
- `git diff --check`: sem erros de whitespace.
- `public/.htaccess`: conferido com exatamente duas diretivas, sem
  `RewriteCond` ou `RewriteRule`; confirmado que não existe `.htaccess` na
  raiz do módulo.
- Conferida a composição de URLs pelo `base_path` direto: CSS e JavaScript são
  referenciados como
  `/hardness3/aplicacoes/pcp/apontamento/public/assets/{css,js}/...`, e os
  dois arquivos existem sob `public/assets/`.
- Confirmada compatibilidade de assinatura com o PHP 7.4 local. Não foi feito
  teste HTTP autenticado, pois depende de uma sessão ERP real.

### Decisões, pendências e liberação

- Reutilizada a permissão PCP já existente para não alterar IDE/base fora do
  escopo. Não foram modificados `scripts/atualizacaoBase.php`, tabelas IDE,
  OP, `T262`, `T255`, `T257` nem dados produtivos.
- Pendente de implantação: cadastrar no menu o acesso direto a
  `aplicacoes/pcp/apontamento/public/index.php` e conceder a C007 técnica a
  permissão 203 na empresa da estação. Isso não invalida a fundação de código.
- **Próxima tarefa liberada:** Tarefa 04 pode implementar o fluxo individual
  usando `pcp_apontamento_require_access()`, `pcp_apontamento_require_company()`
  e `pcp_apontamento_require_csrf()` em toda escrita.
