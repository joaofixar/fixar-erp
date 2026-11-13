# Task 02 — Modelo mínimo e cadastro manual

**Situação:** concluída e validada em 28/10/2026.

## Escopo realizado

- Criadas exclusivamente as tabelas `GNRE001` (guia) e `GNRE002` (documento de origem).
- A tela pública do módulo lista somente as guias da empresa ativa, cria e edita somente rascunhos e permite cancelá-los logicamente.
- O formulário aceita as modalidades `INDIVIDUAL` e `APURACAO`, e zero, um ou vários documentos de origem informados livremente. Não há busca automática de NF-e ou de qualquer outro documento.
- Todas as rotas passam novamente pelo bootstrap da Task 01: sessão ERP, empresa ativa e a permissão `C029_Id=310`.

## Modelo e motivo das colunas

`GNRE001` contém a empresa, modalidade, situação (`R` rascunho ou `C` cancelado), UF favorecida, código/detalhamento de receita, vencimento, valor, referência e observação: são os dados concretos que identificam uma guia manual. Também contém criação, alteração e cancelamento com usuário e data para operar o rascunho com rastreabilidade.

`GNRE002` contém apenas a guia relacionada, identificação e descrição livres do documento, além da data de inclusão. Isso permite o vínculo manual sem supor que o documento seja NF-e ou consultar qualquer fonte externa.

Os índices são os usados pela tela: `GNRE001` por empresa/situação e empresa/vencimento, e `GNRE002` por guia. Não há FK física, seguindo o padrão do ERP. Não foram criados campos ou tabelas de XML, transmissão, recibo, fila, PDF, configuração de UF ou integração NF-e.

## Arquivos alterados

- `scripts/atualizacaoBase.php`
- `aplicacoes/fiscal/gnre/public/index.php`
- `aplicacoes/fiscal/gnre/docs/PROJECT_STATUS.md`
- `aplicacoes/fiscal/gnre/docs/tasks/02-modelo-cadastro-manual.md`

## Aplicação da atualização-base

Execute o procedimento usual do ERP para `scripts/atualizacaoBase.php`, fora do horário comercial conforme o próprio script exige. Ele cria idempotentemente `GNRE001` e `GNRE002`. A permissão 310 já é criada pela Task 01 e deve estar liberada em `C030` para o perfil/empresa de teste.

## Testes realizados

- Lint PHP da tela GNRE.
- Checagem de sintaxe e espaço em branco do diff.
- Busca no módulo para confirmar ausência de includes/chamadas NF-e e de código de consulta de UF, XML, transmissão, PDF ou fila.
- Teste manual validado: guia `GNRE001_Id=1` gravada e dois documentos de origem livres persistidos em `GNRE002` (`GNRE002_Id=4` e `5`, identificações `556` e `557`).

## Roteiro de teste manual

1. Aplique a atualização-base e dê a permissão 310 a um perfil para a empresa de teste.
2. Acesse `/hardness3/aplicacoes/fiscal/gnre/public/index.php` com sessão ERP e empresa ativa.
3. Crie uma guia `INDIVIDUAL` sem documentos e confirme que aparece como rascunho na lista.
4. Edite-a, altere para `APURACAO`, inclua dois documentos livres e confirme a persistência ao reabrir.
5. Cancele o rascunho e confirme que ele continua listado como cancelado e não oferece edição.
6. Troque a empresa ativa ou retire a permissão 310: a guia da outra empresa não deve aparecer e o acesso sem permissão deve retornar 403.

## Pendências e condição para a Task 03

Esta task não consulta configurações de UF, calcula tributos, gera XML, transmite, cria PDF ou fila. A validação do cadastro manual e da migration em ambiente de teste foi concluída; a Task 03 está liberada mediante solicitação do usuário.
