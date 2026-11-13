# Task 30 — Acesso GNRE na tela inicial por perfil

## Entrega

- `prog/etc/etc011/outros.php` exibe o card **GNRE / Guias estaduais** no mesmo
  agrupamento flutuante e no mesmo padrão visual dos atalhos de aplicações
  isoladas. O card só é renderizado quando o ERP já carregou `C029_Id=310` em
  `$g['c029Ids']`.
- O destino é `aplicacoes/fiscal/gnre/public/?view=list` em nova aba, abrindo
  diretamente a Central de Guias. A empresa ativa do ERP é enviada apenas como
  sugestão `gnre_company` para que uma sessão GNRE anterior não prevaleça
  sobre a empresa da tela inicial.
- `bootstrap/app.php` aceita essa sugestão somente depois de reler, para o
  usuário da sessão ERP, as associações ativas `C019` e a liberação `C030` da
  permissão 310. Uma empresa ausente nessa lista recebe HTTP 403. Sem o
  parâmetro, o comportamento anterior de contexto GNRE é preservado.

## Permissão reutilizada

Não foi criada tabela, campo, sessão de autorização nem permissão paralela.
O mecanismo é a opção ERP existente `C029_Id=310`, **Acessar GNRE**, do grupo
Fiscal. A aplicação já a consulta em todas as requisições por meio de
`gnre_authorized_companies()`; portanto esconder o card não concede nem remove
o bloqueio do acesso direto.

Para liberar, no cadastro de perfil do ERP, habilite **Fiscal → Acessar GNRE**
para o perfil e a empresa desejados (registro correspondente em `C030`). O
usuário também precisa ter vínculo ativo na empresa em `C019`. Ao retirar a
liberação, o card some na próxima montagem da tela inicial e a URL direta passa
a responder 403.

## Contexto e segurança

O parâmetro de URL não é uma autorização: ele só seleciona uma empresa que já
foi revalidada no servidor contra `C019`/`C030`. A GNRE continua com contexto
próprio e não modifica `Empresa_Atual` do ERP. Assim, uma tentativa de abrir
uma empresa sem acesso não troca contexto nem expõe dados.

## Validação

- `php -l prog/etc/etc011/outros.php`
- `php -l aplicacoes/fiscal/gnre/bootstrap/app.php`
- `php aplicacoes/fiscal/gnre/tests/task30-home-access-synthetic.php`
- `git diff --check`

Roteiro manual: com a permissão 310 liberada, abrir a tela inicial, confirmar o
card e verificar que abre a GNRE na empresa atual. Remover a permissão, recarregar
a tela e confirmar que o card não aparece e que a URL direta responde 403.

Não houve alteração do fluxo GNRE, regras fiscais, XML, transmissão, banco,
snapshots ou permissões de Dashboard, MCP e demais módulos.
