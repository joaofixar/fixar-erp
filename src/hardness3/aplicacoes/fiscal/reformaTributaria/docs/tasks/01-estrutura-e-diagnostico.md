# Task 1 — Estrutura e diagnóstico técnico da base

## Status

Implementada. A validação funcional em uma base de teste e a revisão pela task gerenciadora ainda são necessárias.

## Escopo entregue

- Aplicação livre em `aplicacoes/fiscal/reformaTributaria/`.
- Abertura pelo menu inicial do Hardness.
- Acesso para todo usuário autenticado no Hardness, sem dependência de C029.
- Abertura sem empresa selecionada, sem dependência de `Empresa_Atual` e sem alterar o contexto de empresa do ERP.
- Visão geral de diagnóstico técnico somente leitura.
- Inventário de tabelas estendidas, campos essenciais, functions e procedures esperadas para a Reforma Tributária.
- Resultados separados em **Presente**, **Ausente** e **Não foi possível verificar**.

## Arquivos alterados pela entrega

- `aplicacoes/fiscal/reformaTributaria/config/module.php`
- `aplicacoes/fiscal/reformaTributaria/bootstrap/app.php`
- `aplicacoes/fiscal/reformaTributaria/src/TechnicalDiagnostic.php`
- `aplicacoes/fiscal/reformaTributaria/public/index.php`
- `prog/etc/etc011/outros.php`

## Acesso

O painel está disponível para qualquer usuário autenticado e ativo no Hardness. A abertura valida a sessão e o usuário em `C007`, mas não consulta C029, C019 ou C030. Não utiliza `Empresa_Atual` como pré-requisito, sugestão ou filtro.

O atalho do menu inicial é apresentado a todo usuário autenticado, sem dependência de C029.

## Catálogo de checks

O diagnóstico consulta apenas `information_schema` e não executa nenhuma rotina de negócio.

| Grupo | Cobertura |
| --- | --- |
| Tabelas estendidas | T001A, T003A/T004A, T005A/T006A, T007A/T008A, T013A/T014A, T218A/T219A e T224A/T225A |
| Campos de configuração | Flag de preenchimento automático em C004; CST, classificação e flags de crédito CBS/IBS em D006 |
| Campos de documentos | CST, classificação, base e valores CBS/IBS dos itens de saída, entrada, orçamento, pedido, cotação e OC; valores CBS/IBS do histórico |
| Procedures | Totalizações de orçamento, pedido, NF-e, cotação, OC e CT-e previstas pelos scripts existentes |
| Functions | 72 functions de cálculo para T004A, T006A, T008A, T084A, T219A e T225A |

O catálogo contém 13 tabelas e 83 routines esperadas.

## Presença da rotina x versão

O status **Presente** para uma function ou procedure confirma somente que uma routine com o nome e tipo esperados está publicada em `information_schema.ROUTINES`.

Esta task não compara o corpo da rotina implantada com os arquivos SQL do repositório. Portanto, uma rotina presente é exibida como **Versão não verificada**. Essa distinção evita afirmar que a base possui a revisão atual apenas porque a routine existe. Uma task futura poderá definir um mecanismo seguro e compatível para a confirmação de versão.

## Testes executados

Foram executados no repositório:

- `php -l` nos quatro arquivos PHP novos do painel, em `prog/etc/etc011/outros.php` e em `scripts/atualizacaoBase.php`;
- teste do catálogo estático: 13 tabelas e 83 routines esperadas;
- varredura do código novo do painel para confirmar que não contém comandos SQL de escrita ou chamada de routines;
- `git diff --check`.

Não foi executado diagnóstico conectado a banco, atualização de base, cadastro de permissão ou teste pelo navegador. Esses passos exigem uma base de teste autorizada.

## Roteiro pendente para base de teste

1. Entrar no Hardness sem depender de empresa ativa e confirmar que o atalho **Reforma Tributária — Diagnóstico técnico** é exibido.
2. Abrir `/hardness3/aplicacoes/fiscal/reformaTributaria/public/` e confirmar que a página carrega sem seleção de empresa.
3. Conferir a presença/ausência de estruturas contra a base de teste conhecida.
4. Validar que uma sessão ausente ou inválida recebe resposta 403 ao acessar a URL diretamente.
5. Confirmar, por auditoria do banco ou log de consultas, que a abertura não cria, altera ou executa procedures.

## Fora desta task

- Diagnóstico por empresa.
- Consulta de configurações de CFOP e NCM.
- Evidências de documentos por empresa.
- Simulador/calculadora.
- Criação de tabelas próprias, persistência de resultados, atualização automática ou alteração de regras fiscais.

## Continuidade

O diagnóstico técnico foi preservado como fonte interna e área secundária pela Task 2. A validação autenticada acima continua pendente para uma base de teste autorizada; a visão inicial do módulo passou a apresentar os recursos disponíveis e as frentes em desenvolvimento, sem seleção de empresa.
